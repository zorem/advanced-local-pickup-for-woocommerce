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
		add_action( 'admin_init', array( $this, 'admin_notices_for_alp_pro' ) );										
		add_action( 'alp_settings_admin_notice', array( $this, 'alp_settings_admin_notice' ) );
		add_action( 'alp_settings_admin_footer', array( $this, 'alp_settings_admin_footer' ) );
	}

	public function admin_notices_for_alp_pro() {
		if ( isset( $_GET['alp-pro-settings-ignore-notice'] ) ) {
			set_transient( 'alp_settings_admin_notice_ignore', 'yes', 2592000 );
		}
	}

	public function alp_settings_admin_notice() {
		$ignore = get_transient( 'alp_settings_admin_notice_ignore' );
		if ( 'yes' == $ignore ) {
			return;
		}
		include 'views/admin_message_panel.php';
	}

	public function alp_settings_admin_footer() {
		include 'views/admin_footer_panel.php';
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
