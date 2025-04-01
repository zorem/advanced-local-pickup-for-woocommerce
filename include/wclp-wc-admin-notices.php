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
			<h2><?php esc_html_e('🚀 Supercharge Your Local Pickup with Zorem Local Pickup PRO!', 'zorem-local-pickup'); ?></h2>
			<p>Enhance your local pickup workflow with <a href="https://www.zorem.com/product/advanced-local-pickup-pro/">Zorem Local Pickup PRO!</a> Set up multiple pickup locations, offer pickup appointments, customize instructions, and manage orders efficiently with a fulfillment dashboard.</p>
			<p><strong>🎉 Get 20% Off your first order!</strong> Use code <strong>ALPPRO20</strong> at checkout.</p>
			<a href="https://www.zorem.com/product/advanced-local-pickup-pro/" class="button-primary btn_pro_notice" target="_blank"><?php esc_html_e('UPGRADE NOW', 'zorem-local-pickup'); ?></a>
			<a href="<?php echo $dismissable_url; ?>" class="button-primary btn_pro_notice" target="_blank"><?php esc_html_e('Dismiss', 'zorem-local-pickup'); ?></a>
			<p><strong>★</strong> for new customers only</p>
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
