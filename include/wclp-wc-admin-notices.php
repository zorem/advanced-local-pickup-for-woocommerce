<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		add_action( 'alp_settings_admin_footer', array( $this, 'alp_settings_admin_footer' ) );

		add_action('admin_notices', array( $this, 'alp_admin_upgrade_notice' ) );
		add_action( 'admin_init', array( $this, 'alp_notice_ignore' ) );

		add_action('admin_notices', array( $this, 'alp_admin_review_notice' ) );
		add_action( 'admin_init', array( $this, 'alp_review_notice_ignore' ) );
	}

	public function alp_settings_admin_notice() {
		include 'views/admin_message_panel.php';
	}

	public function alp_settings_admin_footer() {
		include 'views/admin_footer_panel.php';
	}

	/*
	* Dismiss admin notice for alp
	*/
	public function alp_notice_ignore() {
		if ( isset( $_GET['alp-notice-dismiss'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'alp_dismiss_notice')) {
					update_option('alp_notice_ignore', 'true');
				}
			}
			
		}
	}

	/*
	* Dismiss admin notice for alp
	*/
	public function alp_review_notice_ignore() {
		if ( isset( $_GET['alp-review-notice-dismiss'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'alp_dismiss_review_notice')) {
					update_option('alp_review_notice_ignore', 'true');
				}
			}
			
		}
	}

	public function alp_admin_upgrade_notice() {
		
		// Exclude notice from a specific page (replace 'alp_plugin_page' with your actual page slug)
		if (isset($_GET['page']) && $_GET['page'] === 'local_pickup') {
			return;
		}

		if ( get_option('alp_notice_ignore') ) {
			return;
		}	

		$nonce = wp_create_nonce('alp_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['alp-notice-dismiss' => 'true', 'nonce' => $nonce]));
	
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
			margin: 5px 0 0;
		}
		.alp-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success alp-dismissable-notice">
			<a href="<?php echo $dismissable_url; ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			<h2><?php esc_html_e('🏪 Upgrade to Zorem Local Pickup PRO & Streamline Your Local Pickup Workflow!', 'zorem-local-pickup'); ?></h2>
			<p>Take control of your local pickups with <a href="https://www.zorem.com/product/advanced-local-pickup-pro/">Zorem Local Pickup PRO</a>. Set up multiple pickup locations, offer scheduled appointments, customize pickup instructions, and manage orders from a centralized fulfillment dashboard—all within WooCommerce.</p>
			<p><strong>🎉 Get 20% Off!</strong> for new customers only. Use code <strong>ALPPRO20</strong> at checkout.</p>
			<a href="https://www.zorem.com/product/advanced-local-pickup-pro/" class="button-primary alp_notice_btn" target="_blank"><?php esc_html_e('UPGRADE NOW', 'zorem-local-pickup'); ?></a>
			<a href="<?php echo $dismissable_url; ?>" class="button-primary alp_notice_btn"><?php esc_html_e('Dismiss', 'zorem-local-pickup'); ?></a>
			<p><strong>★</strong> for new customers only</p>
		</div>
		<?php
	}
	
	public function alp_admin_review_notice() {

		// Exclude notice from a specific page (replace 'alp_plugin_page' with your actual page slug)
		if (isset($_GET['page']) && $_GET['page'] === 'local_pickup') {
			return;
		}

		if ( get_option('alp_review_notice_ignore') ) {
			return;
		}	

		$nonce = wp_create_nonce('alp_dismiss_review_notice');
		$dismissable_url = esc_url(add_query_arg(['alp-review-notice-dismiss' => 'true', 'nonce' => $nonce]));
	
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
			margin: 5px 0 0;
		}
		.alp-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success alp-dismissable-notice">
			<a href="<?php echo $dismissable_url; ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			<h2><?php esc_html_e('⭐ Love Zorem Local Pickup? Share Your Feedback! ❤️', 'zorem-local-pickup'); ?></h2>
			<p>We hope <strong>Zorem Local Pickup</strong> is making your WooCommerce store’s local pickup process smoother and more efficient! Your feedback helps us improve and bring you even better features.</p>
			<p>If you’re enjoying the plugin, please take a moment to leave us a <strong>5-star review</strong>—it means a lot to us! ⭐</p>
			<p><a href="https://wordpress.org/support/plugin/advanced-local-pickup-for-woocommerce/reviews/#new-post" class="button-primary alp_notice_btn" target="_blank"><?php esc_html_e('Leave a Review', 'zorem-local-pickup'); ?></a>
			<a href="<?php echo $dismissable_url; ?>" class="button-primary alp_notice_btn"><?php esc_html_e('Dismiss', 'zorem-local-pickup'); ?></a></p>
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
