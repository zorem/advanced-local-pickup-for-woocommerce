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

		add_action('admin_notices', array( $this, 'alp_pro192' ) );
		add_action( 'admin_init', array( $this, 'alp_notice_dismiss192' ) );

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
	public function alp_notice_dismiss192() {
		if ( isset( $_GET['notice-dismiss-alp'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field( wp_unslash( $_GET['nonce'] ) );
				if (wp_verify_nonce($nonce, 'alp_notice_close')) {
					update_option('alp_notice_dismiss192', 'true');
				}
			}
			
		}
	}

	public function alp_pro192() {
		
		// Exclude notice from a specific page (replace 'alp_plugin_page' with your actual page slug)
		if (isset($_GET['page']) && $_GET['page'] === 'local_pickup') {
			return;
		}

		if ( get_option('alp_notice_dismiss192') ) {
			return;
		}	

		$nonce = wp_create_nonce('alp_notice_close');
		$dismissable_url = esc_url(add_query_arg(['notice-dismiss-alp' => 'true', 'nonce' => $nonce]));
	
		?>
		<style>		
		.wp-core-ui .notice.alp-dismissable-notice{
			position: relative;
			padding-right: 38px;
			border-left-color: #005B9A;
		}
		.wp-core-ui .notice.alp-dismissable-notice h3{
			margin-bottom: 5px;
		} 
		.wp-core-ui .notice.alp-dismissable-notice a.notice-dismiss{
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.alp_notice_btn {
			background: #005B9A;
			color: #fff;
			border-color: #005B9A;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 1em;
		}
		.alp-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success alp-dismissable-notice">
			<a href="<?php echo esc_url( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'advanced-local-pickup-for-woocommerce' ); ?></span></a>
			<h2><?php esc_html_e( '📦 Upgrade to Local Pickup PRO – Unlock Powerful Pickup Features!', 'advanced-local-pickup-for-woocommerce' ); ?></h2>
			<p><?php esc_html_e( 'Take your local pickup experience to the next level with Zorem Local Pickup PRO:', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '✅ Let customers schedule pickup appointments', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '✅ Set up multiple pickup locations', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '✅ Send pickup reminders and instructions', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '✅ Apply pickup-based discounts or fees', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '✅ Customize pickup availability and display options', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<p><?php esc_html_e( '🎁 Special Offer: Get 20% OFF with coupon code ALPPRO20 – limited time only!', 'advanced-local-pickup-for-woocommerce' ); ?></p>

			<a class="button-primary alp_notice_btn" target="blank" href="https://www.zorem.com/product/zorem-local-pickup-pro/"><?php esc_html_e( 'Upgrade to Local Pickup PRO', 'advanced-local-pickup-for-woocommerce' ); ?></a>
			<a class="button-primary alp_notice_btn" href="<?php echo esc_url( $dismissable_url ); ?>"><?php esc_html_e( 'Dismiss', 'advanced-local-pickup-for-woocommerce' ); ?></a>
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
