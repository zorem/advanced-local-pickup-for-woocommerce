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

		add_action( 'admin_notices', array( $this, 'admin_notice_after_update' ) );		
		add_action('admin_init', array( $this, 'wplp_plugin_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'alp_return_for_woocommerce_notice' ) );
		add_action( 'admin_init', array( $this, 'alp_return_for_woocommerce_notice_ignore' ) );

		add_action( 'admin_notices', array( $this, 'alp_black_friday_admin_notice' ) );
		add_action( 'admin_init', array( $this, 'alp_black_friday_notice_ignore' ) );
	}

	public function admin_notices_for_alp_pro() {
		if ( isset( $_GET['alp-pro-settings-ignore-notice'] ) ) {
			if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'alp_pro_dismiss_notice')) {
				set_transient( 'alp_settings_admin_notice_ignore', 'yes', 2592000 );
			}
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

	/*
	* Display admin notice on plugin install or update
	*/
	public function admin_notice_after_update() { 		
		
		if ( get_option('wplp_review_notice_ignore') ) {
			return;
		}
		
		// Add nonce to the dismissable URL
		$nonce = wp_create_nonce('wplp_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['wplp-review-ignore-notice' => 'true', 'nonce' => $nonce]));
		
		?>
		<style>		
		.wp-core-ui .notice.wplp-dismissable-notice {
			position: relative;
			padding-right: 38px;
		}
		.wp-core-ui .notice.wplp-dismissable-notice a.notice-dismiss {
			padding: 9px;
			text-decoration: none;
		} 
		.wp-core-ui .button-primary.btn_review_notice {
			background: transparent;
			color: #f1a451;
			border-color: #f1a451;
			text-transform: uppercase;
			padding: 0 11px;
			font-size: 12px;
			height: 30px;
			line-height: 28px;
			margin: 5px 0 15px;
		}
		</style>	
		<div class="notice updated notice-success wplp-dismissable-notice">
			<a href="<?php echo esc_url($dismissable_url); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>
			<p>Hey, I noticed you are using the Zorem Local Pickup Plugin - that’s awesome!</br>Could you please do me a BIG favor and give it a 5-star rating on WordPress to help us spread the word and boost our motivation?</p>
			<p>Eran Shor</br>Founder of zorem</p>
			<a class="button-primary btn_review_notice" target="blank" href="https://wordpress.org/support/plugin/advanced-local-pickup-for-woocommerce/reviews/#new-post">Ok, you deserve it</a>
			<a class="button-primary btn_review_notice" href="<?php echo esc_url($dismissable_url); ?>">Nope, maybe later</a>
			<a class="button-primary btn_review_notice" href="<?php echo esc_url($dismissable_url); ?>">I already did</a>
		</div>
	<?php 		
	}	


	/*
	* Hide admin notice on dismiss of ignore-notice
	*/
	public function wplp_plugin_notice_ignore() {
		if (isset($_GET['wplp-review-ignore-notice'])) {
			if (isset($_GET['nonce']) && wp_verify_nonce($_GET['nonce'], 'wplp_dismiss_notice')) {
				update_option( 'wplp_review_notice_ignore', 'true' );
			}
		}
	}

	/*
	* Dismiss admin notice for return
	*/
	public function alp_return_for_woocommerce_notice_ignore() {
		if ( isset( $_GET['alp-return-for-woocommerce-notice'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'alp_return_for_woocommerce_dismiss_notice')) {
					update_option('alp_return_for_woocommerce_notice_ignore', 'true');
				}
			}
			
		}
	}

	/*
	* Display admin notice on plugin install or update
	*/
	public function alp_return_for_woocommerce_notice() { 		
		
		$return_installed = ( function_exists( 'zorem_returns_exchanges' ) ) ? true : false;
		if ( $return_installed ) {
			return;
		}
		
		if ( get_option('alp_return_for_woocommerce_notice_ignore') ) {
			return;
		}	
		
		$nonce = wp_create_nonce('alp_return_for_woocommerce_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['alp-return-for-woocommerce-notice' => 'true', 'nonce' => $nonce]));

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
			margin: 5px 0 15px;
		}
		.alp-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success alp-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<h3>Launching Zorem Returns!</h3>
			<p>We’re thrilled to announce the launch of our new <a href="<?php echo esc_url( 'https://www.zorem.com/product/zorem-returns/' ); ?>"><strong>Zorem Returns Plugin!</strong></a> This powerful tool is designed to streamline and automate your returns and exchanges management process, freeing up your time to focus on what truly matters—growing your business.</p>

			<p><strong>Act fast!</strong> For a limited time, you can enjoy an exclusive <strong>40% discount</strong> on Zorem Returns Plugin with the coupon code <strong>RETURNS40</strong>. Don’t miss out—the offer expires 2 weeks after installing this plugin or update.</p>
						
			<a class="button-primary alp_notice_btn" target="blank" href="<?php echo esc_url( 'https://www.zorem.com/product/zorem-returns/' ); ?>">Unlock 40% Off</a>
			<a class="button-primary alp_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>			
		</div>	
		<?php 				
	}

	/*
	* Dismiss admin notice for black friday
	*/
	public function alp_black_friday_notice_ignore() {
		if ( isset( $_GET['alp-black-friday-notice'] ) ) {
			
			if (isset($_GET['nonce'])) {
				$nonce = sanitize_text_field($_GET['nonce']);
				if (wp_verify_nonce($nonce, 'alp_black_friday_dismiss_notice')) {
					update_option('alp_black_friday_notice_ignore', 'true');
				}
			}
			
		}
	}

	/*
	* Display admin notice for black friday on plugin install or update
	*/
	public function alp_black_friday_admin_notice() { 		
		
		$return_installed = ( function_exists( 'Zorem_Local_Pickup_Pro' ) ) ? true : false;
		if ( $return_installed ) {
			return;
		}

		if ( get_option('alp_black_friday_notice_ignore') || strtotime( current_time('Y-m-d') ) > strtotime('2024-12-03') ) {
			return;
		}	
		
		$nonce = wp_create_nonce('alp_black_friday_dismiss_notice');
		$dismissable_url = esc_url(add_query_arg(['alp-black-friday-notice' => 'true', 'nonce' => $nonce]));

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
			margin: 5px 0 15px;
		}
		.alp-dismissable-notice strong{
			font-weight: bold;
		}
		</style>
		<div class="notice updated notice-success alp-dismissable-notice">			
			<a href="<?php esc_html_e( $dismissable_url ); ?>" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></a>			
			<h3>Black Friday & Cyber Monday: 40% Off All Plugins!</h3>
			<p>Get 40% off all plugins from November 27th to December 2nd on the Zorem website. Don’t miss our biggest sale of the year to boost your store’s performance!</p>	
			<a class="button-primary alp_notice_btn" target="blank" href="<?php echo esc_url( 'https://www.zorem.com/products/' ); ?>">Shop Now</a>
			<a class="button-primary alp_notice_btn" href="<?php esc_html_e( $dismissable_url ); ?>">Dismiss</a>			
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
