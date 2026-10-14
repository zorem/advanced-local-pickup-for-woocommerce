<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Dismiss link handler verifies nonce via wp_verify_nonce() explicitly; admin_notices display simply reads $_GET['page'] to conditionally suppress the notice.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- Function is the singleton accessor for the class of the same name; renaming would break backward compatibility with existing installs.

class WC_ALP_Admin_Notices_Under_WC_Admin {

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	 */
	private static $instance;

	/**
	 * Second upgrade trigger fires once the store has taken this many
	 * local-pickup orders — a moment of value, not during setup.
	 */
	const MILESTONE_ORDERS = 10;

	/** Dismissing the milestone card snoozes it for this long rather than hiding it forever. */
	const MILESTONE_SNOOZE = 60 * DAY_IN_SECONDS;

	/** Screens the milestone card is allowed to appear on (where pickup work happens). */
	const ORDER_SCREENS = array( 'edit-shop_order', 'shop_order', 'woocommerce_page_wc-orders' );

	/**
	 * Initialize the main plugin function
	*/
	public function __construct() {
		$this->init();
	}
	
	/**
	 * Get the class instance
	 *
	 * @return WC_ALP_Admin_Notices_Under_WC_Admin
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
	/*
	* init from parent mail class
	*/
	public function init() {
		add_action( 'alp_settings_admin_notice', array( $this, 'alp_settings_admin_notice' ) );

		add_action('admin_notices', array( $this, 'alp_pro194' ) );
		add_action( 'admin_init', array( $this, 'alp_notice_dismiss194' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'alp_pro194_notice_styles' ), 20 );

		// Second upgrade trigger at a moment of value: once the store has taken
		// MILESTONE_ORDERS local-pickup orders, on the WooCommerce order screens.
		add_action( 'woocommerce_order_status_changed', array( $this, 'alp_count_pickup_order' ), 20, 4 );
		add_action( 'admin_notices', array( $this, 'alp_pickup_milestone_notice' ) );
		add_action( 'admin_init', array( $this, 'alp_handle_milestone_dismiss' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'alp_milestone_notice_styles' ), 20 );

		// Review request notice (ZUI plugin-notice card)
		// add_action( 'admin_notices', array( $this, 'alp_free_review_notice' ) );
		// add_action( 'admin_init', array( $this, 'alp_free_review_notice_ignore' ) );
		// add_action( 'admin_enqueue_scripts', array( $this, 'alp_free_review_notice_styles' ) );
	}

	/*
	* Dismiss the review notice
	*/
	public function alp_free_review_notice_ignore() {
		if ( isset( $_GET['alp-free-review-ignore-notice'] ) && isset( $_GET['nonce'] ) ) {
			$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ) );
			if ( wp_verify_nonce( $nonce, 'alp_free_review_notice' ) ) {
				update_option( 'alp_free_review_notice_ignore', 'true' );
			}
		}
	}

	/*
	* ZUI plugin-notice styles — the review notice renders from `admin_notices`
	* on every admin page, outside `.zui-scope`, so the standalone component
	* stylesheet is enqueued here (it has no dependency on the zui.css bundle).
	*/
	public function alp_free_review_notice_styles() {

		if ( get_option( 'alp_free_review_notice_ignore' ) ) {
			return;
		}

		$zui_version_file = wc_local_pickup()->get_plugin_path() . '/assets/zui/VERSION';
		$zui_version      = is_readable( $zui_version_file ) ? trim( file_get_contents( $zui_version_file ) ) : wc_local_pickup()->version;

		wp_enqueue_style( 'alp-zui-pnotice', wc_local_pickup()->plugin_dir_url() . 'assets/zui/css/components/plugin-notice.css', array(), $zui_version );
	}

	/*
	* Display the review request notice (ZUI plugin-notice card)
	*/
	public function alp_free_review_notice() {

		if ( get_option( 'alp_free_review_notice_ignore' ) ) {
			return;
		}

		$nonce           = wp_create_nonce( 'alp_free_review_notice' );
		$dismissable_url = esc_url( add_query_arg( array( 'alp-free-review-ignore-notice' => 'true', 'nonce' => $nonce ) ) );
		?>
		<div class="zui-pnotice" role="status">
			<span class="zui-pnotice__avatar" aria-hidden="true">
				<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/></svg>
			</span>
			<div class="zui-pnotice__body">
				<strong class="zui-pnotice__title">⭐ <?php esc_html_e( 'Enjoying Zorem Local Pickup? Leave Us a Review!', 'advanced-local-pickup-for-woocommerce' ); ?></strong>
				<p class="zui-pnotice__text"><?php esc_html_e( 'We hope Zorem Local Pickup has made managing local pickup orders easy for your store and your customers! Your feedback helps us grow and continue improving the plugin.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( 'If you love using ALP, we\'d really appreciate it if you could take a moment to leave us a 5-star review. It helps us keep improving and providing the best experience for you!', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><strong>👍 <?php esc_html_e( 'Support ALP & Share Your Experience!', 'advanced-local-pickup-for-woocommerce' ); ?></strong></p>
				<div class="zui-pnotice__actions">
					<a class="zui-pnotice__btn" href="https://wordpress.org/support/plugin/advanced-local-pickup-for-woocommerce/reviews/" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Ok, you deserve it', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-pnotice__btn zui-pnotice__btn--ghost" href="<?php echo esc_url( $dismissable_url ); ?>"><?php esc_html_e( 'I already did', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-pnotice__link" href="<?php echo esc_url( $dismissable_url ); ?>"><?php esc_html_e( 'Nope, maybe later', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				</div>
			</div>
			<a class="zui-pnotice__close" href="<?php echo esc_url( $dismissable_url ); ?>" aria-label="<?php esc_attr_e( 'Dismiss', 'advanced-local-pickup-for-woocommerce' ); ?>">&times;</a>
		</div>
		<?php
	}

	public function alp_settings_admin_notice() {
		include 'views/admin_message_panel.php';
	}

	/*
	* Dismiss admin notice for alp
	*/
	public function alp_notice_dismiss194() {
		if ( isset( $_GET['notice-dismiss-alp'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ) );
				if (wp_verify_nonce($nonce, 'alp_notice_close')) {
					update_option('alp_notice_dismiss194', 'true');
				}
			}
			
		}
	}

	/*
	* ZUI plugin-notice styles for the PRO upgrade card. It renders from
	* `admin_notices` on every admin page (except the plugin settings page),
	* outside `.zui-scope`, so the standalone component stylesheet is enqueued
	* here — same handle as the other ALP notices (idempotent).
	*/
	public function alp_pro194_notice_styles() {

		if ( ( isset( $_GET['page'] ) && 'local_pickup' === $_GET['page'] ) || get_option( 'alp_notice_dismiss194' ) ) {
			return;
		}

		$zui_version_file = wc_local_pickup()->get_plugin_path() . '/assets/zui/VERSION';
		$zui_version      = is_readable( $zui_version_file ) ? trim( file_get_contents( $zui_version_file ) ) : wc_local_pickup()->version;

		wp_enqueue_style( 'alp-zui-pnotice', wc_local_pickup()->plugin_dir_url() . 'assets/zui/css/components/plugin-notice.css', array(), $zui_version );
	}

	public function alp_pro194() {

		// Exclude the notice from the plugin's own settings page.
		if ( isset( $_GET['page'] ) && 'local_pickup' === $_GET['page'] ) {
			return;
		}

		if ( get_option( 'alp_notice_dismiss194' ) ) {
			return;
		}

		$nonce           = wp_create_nonce( 'alp_notice_close' );
		$dismissable_url = esc_url( add_query_arg( array( 'notice-dismiss-alp' => 'true', 'nonce' => $nonce ) ) );
		?>
		<div class="zui-pnotice" id="alp-pro-upgrade-notice" role="status">
			<span class="zui-pnotice__avatar" aria-hidden="true">
				<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
			</span>
			<div class="zui-pnotice__body">
				<strong class="zui-pnotice__title"><?php esc_html_e( '📦 Upgrade to Local Pickup PRO – Unlock Powerful Pickup Features!', 'advanced-local-pickup-for-woocommerce' ); ?></strong>
				<p class="zui-pnotice__text"><?php esc_html_e( 'Take your local pickup experience to the next level with Advanced Local Pickup Pro:', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( '✅ Let customers schedule pickup appointments', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( '✅ Set up multiple pickup locations', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( '✅ Send pickup reminders and instructions', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( '✅ Apply pickup-based discounts or fees', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php esc_html_e( '✅ Customize pickup availability and display options', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<p class="zui-pnotice__text"><?php echo wp_kses_post( __( '🎁 <strong>20% OFF</strong> with coupon code <strong>ALPPRO20</strong> — limited time only!', 'advanced-local-pickup-for-woocommerce' ) ); ?></p>
				<div class="zui-pnotice__actions">
					<a class="zui-pnotice__btn" href="<?php echo esc_url( alp_pro_upgrade_url( 'admin-notice' ) ); ?>" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Upgrade to Advanced Local Pickup Pro', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-pnotice__link" href="<?php echo esc_url( $dismissable_url ); ?>"><?php esc_html_e( 'Dismiss', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				</div>
			</div>
			<a class="zui-pnotice__close" href="<?php echo esc_url( $dismissable_url ); ?>" aria-label="<?php esc_attr_e( 'Dismiss', 'advanced-local-pickup-for-woocommerce' ); ?>">&times;</a>
		</div>
		<?php
	}

	/**
	 * Count a completed local-pickup order exactly once. Mirrors AST's
	 * shipped-order counter; keyed on WooCommerce's local-pickup shipping
	 * method so it only ever counts real pickup orders. If the store never
	 * uses a pickup shipping method the count stays 0 and the milestone card
	 * simply never appears — no false prompts.
	 *
	 * @param int           $order_id    Order ID.
	 * @param string        $status_from Previous status.
	 * @param string        $status_to   New status.
	 * @param WC_Order|null $order       Order object (WC passes it on this hook).
	 */
	public function alp_count_pickup_order( $order_id, $status_from, $status_to, $order = null ) {
		if ( ! $order instanceof WC_Order ) {
			$order = wc_get_order( $order_id );
		}
		if ( ! $order || $order->get_meta( '_alp_pickup_counted' ) ) {
			return;
		}

		$is_pickup = false;
		foreach ( $order->get_shipping_methods() as $method ) {
			if ( false !== strpos( (string) $method->get_method_id(), 'pickup' ) ) {
				$is_pickup = true;
				break;
			}
		}
		if ( ! $is_pickup ) {
			return;
		}

		$order->update_meta_data( '_alp_pickup_counted', 1 );
		$order->save();
		update_option( 'alp_pickup_orders_count', (int) get_option( 'alp_pickup_orders_count', 0 ) + 1, false );
	}

	/**
	 * Milestone card shows only on the order screens, to shop managers, once
	 * the pickup-order count clears the threshold and the snooze has lapsed.
	 */
	private function alp_milestone_notice_visible() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		return $screen && in_array( $screen->id, self::ORDER_SCREENS, true )
			&& current_user_can( 'manage_woocommerce' )
			&& (int) get_option( 'alp_pickup_orders_count', 0 ) >= self::MILESTONE_ORDERS
			&& (int) get_option( 'alp_pickup_milestone_snoozed_until', 0 ) < time();
	}

	/**
	 * Dismissing snoozes for MILESTONE_SNOOZE rather than hiding forever; when
	 * it returns the count in the title has grown, so it isn't a repeat.
	 */
	public function alp_handle_milestone_dismiss() {
		if ( isset( $_GET['alp-milestone-snooze'], $_GET['nonce'] )
			&& wp_verify_nonce( sanitize_key( wp_unslash( $_GET['nonce'] ) ), 'alp_milestone_snooze' ) ) {
			update_option( 'alp_pickup_milestone_snoozed_until', time() + self::MILESTONE_SNOOZE, false );
		}
	}

	/**
	 * Load the .zui-pnotice component only where the milestone card renders
	 * (order screens, outside the settings zui.css bundle).
	 */
	public function alp_milestone_notice_styles() {
		if ( ! $this->alp_milestone_notice_visible() ) {
			return;
		}
		$zui_version_file = wc_local_pickup()->get_plugin_path() . '/assets/zui/VERSION';
		$zui_version      = is_readable( $zui_version_file ) ? trim( file_get_contents( $zui_version_file ) ) : wc_local_pickup()->version;
		wp_enqueue_style( 'alp-zui-pnotice', wc_local_pickup()->plugin_dir_url() . 'assets/zui/css/components/plugin-notice.css', array(), $zui_version );
	}

	/**
	 * The second upgrade trigger — shown at a moment of value (orders are
	 * coming through pickup) rather than during setup. Dismissible (snooze).
	 */
	public function alp_pickup_milestone_notice() {
		if ( ! $this->alp_milestone_notice_visible() ) {
			return;
		}

		$count       = (int) get_option( 'alp_pickup_orders_count', 0 );
		$upgrade_url = alp_pro_upgrade_url( 'pickup-orders-milestone' );
		$nonce       = wp_create_nonce( 'alp_milestone_snooze' );
		$dismiss_url = add_query_arg( array( 'alp-milestone-snooze' => 'true', 'nonce' => $nonce ) );
		?>
		<div class="zui-pnotice" id="alp-pickup-milestone-notice" role="status">
			<span class="zui-pnotice__avatar" aria-hidden="true">
				<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
			</span>
			<div class="zui-pnotice__body">
				<strong class="zui-pnotice__title">
					<?php
					/* translators: %s: number of orders fulfilled via local pickup */
					echo esc_html( sprintf( _n( '🎉 You\'ve fulfilled %s order with Local Pickup', '🎉 You\'ve fulfilled %s orders with Local Pickup', $count, 'advanced-local-pickup-for-woocommerce' ), number_format_i18n( $count ) ) );
					?>
				</strong>
				<p class="zui-pnotice__text"><?php esc_html_e( 'Advanced Local Pickup Pro takes it further: unlimited pickup locations, reservable appointment slots, per-item pickup, product availability per location, pickup discounts & fees, and a fulfillment dashboard.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<div class="zui-pnotice__actions">
					<a class="zui-pnotice__btn" href="<?php echo esc_url( $upgrade_url ); ?>" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'See what Pro unlocks', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-pnotice__link" href="<?php echo esc_url( $dismiss_url ); ?>"><?php esc_html_e( 'Not now', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				</div>
			</div>
			<a class="zui-pnotice__close" href="<?php echo esc_url( $dismiss_url ); ?>" aria-label="<?php esc_attr_e( 'Dismiss', 'advanced-local-pickup-for-woocommerce' ); ?>">&times;</a>
		</div>
		<?php
	}

}

/**
 * Returns an instance of WC_ALP_Admin_Notices_Under_WC_Admin.
 *
 * @since 1.6.5
 * @version 1.6.5
 *
 * @return WC_ALP_Admin_Notices_Under_WC_Admin
*/
function WC_ALP_Admin_Notices_Under_WC_Admin() {
	static $instance;

	if ( ! isset( $instance ) ) {		
		$instance = new WC_ALP_Admin_Notices_Under_WC_Admin();
	}

	return $instance;
}

/**
 * Register this class globally.
 *
 * Backward compatibility.
*/
WC_ALP_Admin_Notices_Under_WC_Admin();
