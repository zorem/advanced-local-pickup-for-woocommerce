<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ALP_CUSTOMIZER_ADMIN {

	// WooCommerce email classes.
	public static $email_types_class_names  = array(
		'new_order'                         => 'WC_Email_New_Order',
		'cancelled_order'                   => 'WC_Email_Cancelled_Order',
		'customer_processing_order'         => 'WC_Email_Customer_Processing_Order',
		'customer_completed_order'          => 'WC_Email_Customer_Completed_Order',
		'customer_refunded_order'           => 'WC_Email_Customer_Refunded_Order',
		'customer_on_hold_order'            => 'WC_Email_Customer_On_Hold_Order',
		'customer_invoice'                  => 'WC_Email_Customer_Invoice',
		'failed_order'                      => 'WC_Email_Failed_Order',
		'customer_new_account'              => 'WC_Email_Customer_New_Account',
		'customer_note'                     => 'WC_Email_Customer_Note',
		'customer_reset_password'           => 'WC_Email_Customer_Reset_Password',
		
		//ALP custom status
		'ready_pickup'						=> 'WC_Email_Customer_Ready_Pickup_Order',
		'pickup'							=> 'WC_Email_Customer_Pickup_Order',
	);
	
	public static $email_types_order_status = array(
		'new_order'                         => 'processing',
		'cancelled_order'                   => 'cancelled',
		'customer_processing_order'         => 'processing',
		'customer_completed_order'          => 'completed',
		'customer_refunded_order'           => 'refunded',
		'customer_on_hold_order'            => 'on-hold',
		'customer_invoice'                  => 'processing',
		'failed_order'                      => 'failed',
		'customer_new_account'              => null,
		'customer_note'                     => 'processing',
		'customer_reset_password'           => null,
		
		//ALP custom status
		'ready_pickup'						=> 'ready-pickup',
		'pickup'							=> 'pickup',
	);
	
	/**
	 * Get the class instance
	 *
	 * @since  1.0
	 * @return ALP_CUSTOMIZER_ADMIN
	*/
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Instance of this class.
	 *
	 * @var object Class Instance
	*/
	private static $instance;
	
	/**
	 * Initialize the main plugin function
	 * 
	 * @since  1.0
	*/
	public function __construct() {
		$this->init();
	}
	
	/*
	 * init function
	 *
	 * @since  1.0
	*/
	public function init() {

		//adding hooks
		add_action( 'admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );
		
		//save of settings hook
		add_action( 'wp_ajax_alp_save_email_settings', array( $this, 'customizer_save_email_settings' ) );
		
		add_action( 'wp_ajax_email_preview', array( $this, 'get_preview_email' ) );
		
		//load javascript in admin
		add_action('admin_enqueue_scripts', array( $this, 'customizer_enqueue_scripts' ) );
		
		//load CSS/javascript in admin
		add_action('admin_footer',  array( $this, 'admin_footer_enqueue_scripts' ) );
		
	}
	
	/*
	 * Admin Menu add function
	 *
	 * @since  2.4
	 * WC sub menu 
	*/
	public function register_woocommerce_menu() {
		add_menu_page( __( 'ALP Customizer', 'advanced-local-pickup-for-woocommerce' ), __( 'ALP Customizer', 'advanced-local-pickup-for-woocommerce' ), 'manage_options', 'alp_customizer', array( $this, 'settingsPage' ) );
	}
	
	/*
	 * Add admin javascript
	 *
	 * @since  2.4
	 * WC sub menu 
	*/
	public function admin_footer_enqueue_scripts() {
		?>
		<style type="text/css">
			#toplevel_page_alp_customizer { display: none !important; }
		</style>
		<?php
	}
	
	/*
	 * callback for settingsPage
	 *
	 * @since  2.4
	*/
	public function settingsPage() {

		$page = isset( $_GET["page"] ) ? $_GET["page"] : "" ;
		
		// Add condition for css & js include for admin page  
		if ( $page != 'alp_customizer' ) {
			return;
		}
	
		$email_type = !empty( $_GET['email_type'] ) ? sanitize_text_field($_GET['email_type']) : get_option( 'orderStatus', 'ready_pickup' );
		
		$iframe_url = $this->get_email_preview_url( $email_type );

		// When load this page will not show adminbar
		?>
		<section class="zoremmail-layout zoremmail-layout-has-sider">
			<form method="post" id="zoremmail_email_options" class="zoremmail_email_options" style="display: contents;">
				<section class="zoremmail-layout zoremmail-layout-has-content zoremmail-layout-sider">
					<aside class="zoremmail-layout-slider-header">
						<button type="button" class="wordpress-to-back" tabindex="0">
							<a class="zoremmail-back-wordpress-link" href="#"><span class="zoremmail-back-wordpress-title dashicons dashicons-no-alt"></span></a>
						</button>
						<button type="button" class="wordpress-to-back" tabindex="0">
							<?php $back_link =  admin_url().'admin.php?page=local_pickup'; ?>
							<a class="zoremmail-back-wordpress-link" href="<?php echo esc_html( $back_link ); ?>"><span class="zoremmail-back-wordpress-title dashicons dashicons-no-alt"></span></a>
						</button>
						<span class="efc-save-content" style="float: right;">
							<button name="save" class="efc-btn efc-save button-primary woocommerce-save-button" type="submit" value="Save changes" disabled>Saved</button>
							<?php wp_nonce_field( 'customizer_email_options_actions', 'customizer_email_options_nonce_field' ); ?>
							<input type="hidden" name="action" value="alp_save_email_settings">
						</span>
					</aside>
					<aside class="zoremmail-layout-slider-content">
						<div class="zoremmail-layout-sider-container">
							<?php $this->get_html( $this->customize_setting_options_func() ); ?>
						</div>
					</aside>
					<aside class="zoremmail-layout-content-collapse">
						<div class="zoremmail-layout-content-media" style="float: right;">
							<a data-width="600px" data-iframe-width="100%" class="active"><span class="dashicons dashicons-desktop"></span></a>
							<a data-width="600px" data-iframe-width="610px"><span class="dashicons dashicons-tablet"></span></a>
							<a data-width="400px" data-iframe-width="410px"><span class="dashicons dashicons-smartphone"></span></a>
						</div>
					</aside>
					<button type="button" class="wordpress-to-back" tabindex="0">
						<?php $back_link =  admin_url().'admin.php?page=local_pickup'; ?>
						<a class="zoremmail-back-wordpress-link" href="<?php echo esc_html( $back_link ); ?>"><span class="zoremmail-back-wordpress-title dashicons dashicons-no-alt"></span></a>
					</button>
				</section>
				<section class="zoremmail-layout zoremmail-layout-has-content">
					<div class="zoremmail-layout-content-container">
						<section class="zoremmail-layout-content-preview customize-preview">
							<div id="overlay"></div>
							<iframe id="email_preview" src="<?php esc_attr_e($iframe_url); ?>" style="width: 100%;height: 100%;display: block;margin: 0 auto;"></iframe>
						</section>
					</div>
				</section>
			</form>
		</section>
		<?php
	}
	
	/*
	* Add admin javascript
	*
	* @since 1.0
	*/	
	public function customizer_enqueue_scripts() {
		
		$page = isset( $_GET["page"] ) ? $_GET["page"] : "" ;
		
		// Add condition for css & js include for admin page  
		if ( $page != 'alp_customizer' ) {
			return;
		}
		
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_register_style( 'woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION );
		wp_enqueue_style( 'woocommerce_admin_styles');
		wp_register_script( 'select2', WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js', array( 'jquery' ), '4.0.3' );
		wp_enqueue_script( 'select2');
		
		// Add tiptip js and css file		
		wp_enqueue_style( 'wclp-customizer', plugin_dir_url(__FILE__) . 'assets/customizer.css', array(), wc_local_pickup()->version );
		wp_enqueue_script( 'wclp-customizer', plugin_dir_url(__FILE__) . 'assets/customizer.js', array( 'jquery', 'wp-util', 'wp-color-picker','jquery-tiptip' ), wc_local_pickup()->version, true );
		
		wp_localize_script('wclp-customizer', 'zorem_customizer', array(
			'site_title'			=> get_bloginfo( 'name' ),
			'order_number'			=> 1,
			'customer_first_name'	=> 'Sherlock',
			'customer_last_name'	=> 'Holmes',
			'customer_company_name' => 'Detectives Ltd.',
			'customer_username'		=> 'sher_lock',
			'customer_email'		=> 'sherlock@holmes.co.uk',
			'est_delivery_date'		=> '2021-07-30 15:28:02',
			'email_iframe_url'		=> add_query_arg( array( 'action'	=> 'email_preview' ), admin_url( 'admin-ajax.php' ) ),
		));
		
	}
	
	/*
	* save settings function
	*/
	public function customizer_save_email_settings() {			
		
		if ( !current_user_can( 'manage_options' ) ) {
			echo json_encode( array('permission' => 'false') );
			die();
		}

		if ( ! empty( $_POST ) && check_admin_referer( 'customizer_email_options_actions', 'customizer_email_options_nonce_field' ) ) {

			//data to be saved
			
			$settings = $this->customize_setting_options_func();
			/*foreach ( $settings as $key=>$val ) {
				if ( $val['type'] != 'section' && $val['type'] != 'panel' ) {
					if ( 'array' == $val['option_type'] ) {
						$option_data = get_option( $val['option_name'], array() );
						if ( $val['type'] == 'text' || $val['type'] == 'textarea' ) {
							$option_data[$key] = !empty($_POST[$key]) ? $_POST[$key] : $val['placeholder'];
						} else {
							$option_data[$key] = $_POST[$key];
						}
						update_option( $val['option_name'], $option_data );
					} else {
						update_option( $val[$key], $_POST[$key] );
					}					
				}
			}*/
			
			foreach ( $settings as $key => $val ) {
				if ( isset( $val['type'] ) && 'textarea' == $val['type'] && !isset( $val['option_key'] ) ) {
					$option_data = get_option( $val['option_name'], array() );
					$option_data[$key] = htmlentities( wp_unslash( $_POST[$key] ) );
					update_option( $val['option_name'], $option_data );
				} elseif ( isset( $val['option_type'] ) && 'key' == $val['option_type'] ) {
					update_option( $key, wc_clean( $_POST[$key] ) );
				} elseif ( isset( $val['option_type'] ) && 'array' == $val['option_type'] ) {
					if ( isset( $val['option_key'] ) ) {
						$option_data = get_option( $val['option_name'], array() );
						$option_data[$val['option_key']] = wc_clean( wp_unslash( $_POST[$key] ) );
						update_option( $val['option_name'], $option_data );
					} else {
						$option_data = get_option( $val['option_name'], array() );
						$option_data[$key] = wc_clean( wp_unslash( $_POST[$key] ) );
						update_option( $val['option_name'], $option_data );
					}
				} else {
					
				}
			}
			
			
				
			echo json_encode( array('success' => 'true') );
			die();
	
		}
	}
	
	/*
	* save settings function
	*/
	public function customizer_header_section_settings() {			
		
		if ( !current_user_can( 'manage_options' ) ) {
			echo json_encode( array('permission' => 'false') );
			die();
		}
		
		
		
		if ( ! empty( $_POST ) ) {
			
			//data to be saved
			$email_type = isset( $_GET['email_type'] ) ? sanitize_text_field($_GET['email_type']) : 'ready_pickup';
			$orderStatus = isset($_POST['orderStatus']) && !empty($_POST['orderStatus']) ? $_POST['orderStatus'] : $email_type;
			$selected_order_id = isset($_POST['selected_order_id']) && !empty($_POST['selected_order_id']) ? $_POST['selected_order_id'] : 'mockup';
			
			update_option( 'orderStatus', $orderStatus );
			update_option( 'selected_order_id', $selected_order_id );

			echo json_encode( array('success' => 'true') );
			die();
	
		}
	}
	
	public function customize_setting_options_func() {
		
		$email_type = isset( $_GET['email_type'] ) ? sanitize_text_field($_GET['email_type']) : get_option( 'orderStatus', 'ready_pickup' );
		
		$defualt_array = array(
			'ready_pickup_subject' => "Your {site_title} order is now Ready for pickup",
			'ready_pickup_heading' => "Your Order is Ready for pickup",
			'ready_pickup_additional_content' => "Hi there. we thought you'd like to know that your recent order from {site_title} has been ready for pickup.",
			'pickup_subject' => "Your order from {site_title} was picked up",
			'pickup_heading' => "You've Got it!",
			'pickup_additional_content' => "Hi {customer_first_name}. Thank you for picking up your {site_title} order #{order_number}. We hope you enjoyed your shopping experience.",
		);
		
		$email_settings = get_option('woocommerce_customer_'.$email_type.'_order_settings', array());
		$pickup_instruction = get_option('pickup_instruction_customize_settings', array());
		
		$email_iframe_url = $this->get_email_preview_url( $email_type );
		
		$settings = array(
			
			//panels
			'email_content'	=> array(
				'id'	=> 'email_content',
				'class' => 'options_panel',
				'label'	=> esc_html__( 'Email Notifications', 'advanced-local-pickup-pro' ),
				'title'	=> esc_html__( 'Email Content', 'advanced-local-pickup-pro' ),
				'type'	=> 'panel',
				'iframe_url' => '',
				'show'	=> true,
			),
			'email_design'	=> array(
				'id'	=> 'email_design',
				'class' => 'options_panel',
				'label' => esc_html__( 'Email Style', 'advanced-local-pickup-pro' ),
				'title'	=> esc_html__( 'Email Design', 'advanced-local-pickup-pro' ),
				'type'	=> 'panel',
				'iframe_url' => '',
				'show'	=> true,
			),
			
			//sub-panels
			'header' => array(
				'id'     => 'email_content',
				'title'       => esc_html__( 'Email Content', 'advanced-local-pickup-pro' ),
				'type'     => 'sub-panel-heading',
				'parent'	=> 'email_content',
				'show'     => true,
				'class' => 'sub_options_panel',
			),
			'email_settings' => array(
				'id'	=> 'email_settings',
				'title'	=> esc_html__( 'Email Settings', 'trackship-for-woocommerce' ),
				'type'	=> 'sub-panel',
				'parent'=> 'email_content',
				'show'	=> true,
				'class' => 'sub_options_panel',
			),
			'header2' => array(
				'id'     => 'email_design',
				'title'       => esc_html__( 'Email Design', 'advanced-local-pickup-pro' ),
				'type'     => 'sub-panel-heading',
				'parent'	=> 'email_design',
				'show'     => true,
				'class' => 'sub_options_panel',
			),
			'widget_style' => array(
				'id'     => 'widget_style',
				'title'       => esc_html__( 'Widget Style', 'advanced-local-pickup-pro' ),
				'type'     => 'sub-panel',
				'parent'	=> 'email_design',
				'show'     => true,
				'class' => 'sub_options_panel',
			),
			'widget_header' => array(
				'id'     => 'widget_header',
				'title'       => esc_html__( 'Widget Header', 'advanced-local-pickup-pro' ),
				'type'     => 'sub-panel',
				'parent'	=> 'email_design',
				'show'     => true,
				'class' => 'sub_options_panel',
			),
			'pickup_location_info' => array(
				'id'     => 'pickup_location_info',
				'title'       => esc_html__( 'Pickup Location info', 'advanced-local-pickup-pro' ),
				'type'     => 'sub-panel',
				'parent'	=> 'email_design',
				'show'     => true,
				'class' => 'sub_options_panel',
			),
			
			//section
			'heading2' => array(
				'id'     => 'widget_style',
				'title'       => esc_html__( 'Widget Style', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'section',
				'parent'	=> 'email_design',
				'show'     => true,
				'class' => 'email_design_first_section ',
			),
			'background_color' => array(
				'title'    => esc_html__( 'Background Color', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'color',
				'default'  => !empty($pickup_instruction['background_color']) ? $pickup_instruction['background_color'] : '#f5f5f5',
				'show'     => true,
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'border_color' => array(
				'title'    => esc_html__( 'Border Color', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'color',
				'default'  => !empty($pickup_instruction['border_color']) ? $pickup_instruction['border_color'] : '#e0e0e0',
				'show'     => true,
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'padding' => array(
				'title'    => esc_html__( 'Padding', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'select',
				'default'  => !empty($pickup_instruction['padding']) ? $pickup_instruction['padding'] : '15px',
				'show'     => true,
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
				'options'  => array(
					'0px' => '0px',
					'5px' => '5px',
					'10px' => '10px',
					'15px' => '15px',
					'20px' => '20px',
					'25px' => '25px',
					'30px' => '30px',
				)
			),
			'heading3' => array(
				'id'     => 'widget_header',
				'title'       => esc_html__( 'Widget Header', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'section',
				'parent'	=> 'email_design',
				'show'     => true,
				
			),
			'hide_widget_header' => array(
				'title'    => esc_html__( 'Hide Widget Header', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => isset($pickup_instruction['hide_widget_header']) ? $pickup_instruction['hide_widget_header'] : '0',
				'type'     => 'checkbox',
				'show'     => true,
				'class'	   => 'hide_widget_header',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'widget_header_text' => array(
				'title'    => esc_html__( 'Widget Header Text', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($pickup_instruction['widget_header_text']) ? $pickup_instruction['widget_header_text'] : esc_html__( 'Pick up information', 'advanced-local-pickup-for-woocommerce' ),
				'placeholder' => esc_html__( 'Pick up information', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'text',
				'show'     => true,
				'class'	   => 'widget_header_text',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'heading4' => array(
				'id'     => 'pickup_location_info',
				'title'       => esc_html__( 'Pickup Location info', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'section',
				'parent'	=> 'email_design',
				'show'     => true,
			),
			'hide_addres_header' => array(
				'title'    => esc_html__( 'Hide Pickup Address Header', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => isset($pickup_instruction['hide_addres_header']) ? $pickup_instruction['hide_addres_header'] : '0',
				'type'     => 'checkbox',
				'show'     => true,
				'class'	   => 'hide_addres_header',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'addres_header_text' => array(
				'title'    => esc_html__( 'Pickup Address Header Text', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($pickup_instruction['addres_header_text']) ? $pickup_instruction['addres_header_text'] : esc_html__( 'Pickup Address', 'advanced-local-pickup-for-woocommerce' ),
				'placeholder' => esc_html__( 'Pickup Address', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'text',
				'show'     => true,
				'class'	   => 'addres_header_text',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'hide_hours_header' => array(
				'title'    => esc_html__( 'Hide Office Hours Header', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => isset($pickup_instruction['hide_hours_header']) ? $pickup_instruction['hide_hours_header'] : '0',
				'type'     => 'checkbox',
				'show'     => true,
				'class'	   => 'hide_hours_header',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
			'header_hours_text' => array(
				'title'    => esc_html__( 'Office Hours Header Text', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($pickup_instruction['header_hours_text']) ? $pickup_instruction['header_hours_text'] : esc_html__( 'Pickup Hours', 'advanced-local-pickup-for-woocommerce' ),
				'placeholder' => esc_html__( 'Pickup Hours', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'text',
				'show'     => true,
				'class'	   => 'header_hours_text',
				'option_name' => 'pickup_instruction_customize_settings',
				'option_type' => 'array',
			),
		);
		
		//sections			
		$all_statuses = array(
			'ready_pickup'		=> esc_html__( 'Ready for Pickup', 'advanced-local-pickup-for-woocommerce' ),
			'pickup'			=> esc_html__( 'Picked Up', 'advanced-local-pickup-for-woocommerce' ),
		);
		
		$settings[ 'heading1' ] = array(
			'id'	=> 'email_settings',
			'class' => 'email_content_first_section ',
			'title'	=> esc_html__( 'Email Settings', 'trackship-for-woocommerce' ),
			'type'	=> 'section',
			'parent'=> 'email_settings',
			'show'	=> true,
		);

		$settings[ 'orderStatus' ] = array(
			'title'    => esc_html__( 'Email type', 'advanced-local-pickup-pro' ),
			'type'     => 'select',
			'default'  => !empty($email_type) ? $email_type : 'ready_pickup',
			'show'     => true,
			'options'  => $all_statuses,
		);
		
		
		foreach ( $all_statuses as $key => $value ) {
						
			$email_settings = get_option('woocommerce_customer_'.$key.'_order_settings', array());
			$settings[ $key . '_enabled' ] = array(
				'title'    => esc_html__( 'Enable email', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($email_settings['enabled']) ? $email_settings['enabled'] : 'yes',
				'type'     => 'tgl-btn',
				'show'     => true,
				'option_name'=> 'woocommerce_customer_'.$key.'_order_settings',
				'option_key'=> 'enabled',
				'option_type'=> 'array',
				'class'		=> $key . '_sub_menu all_status_submenu enabled',
			);
			$settings[ $key . '_recipient' ] = array(
				'title'    => esc_html__( 'Recipients', 'advanced-local-pickup-for-woocommerce' ),
				'desc'  => esc_html__( 'add comma-seperated emails, defaults to placeholder {customer_email} ', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($email_settings['recipient']) ? $email_settings['recipient'] : '{customer_email}',
				'placeholder' => esc_html__( 'add comma-seperated emails, defaults to placeholder {customer_email}', 'advanced-local-pickup-for-woocommerce' ),
				'type'     => 'text',
				'show'     => true,
				'option_name' => 'woocommerce_customer_'.$key.'_order_settings',
				'option_key'=> 'recipient',
				'option_type' => 'array',
				'class'		=> $key . '_sub_menu all_status_submenu recipient',
			);
			$settings[ $key . '_subject' ] = array(
				'title'    => esc_html__( 'Email Subject', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($email_settings['subject']) ? stripslashes($email_settings['subject']) : $defualt_array[$key.'_subject'],
				'placeholder' => $defualt_array[$key.'_subject'],
				'type'     => 'text',
				'show'     => true,
				'option_name' => 'woocommerce_customer_'.$key.'_order_settings',
				'option_key'=> 'subject',
				'option_type' => 'array',
				'class'		=> $key . '_sub_menu all_status_submenu subject',
			);
			$settings[ $key . '_heading' ] = array(
				'title'    => esc_html__( 'Email heading', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($email_settings['heading']) ? stripslashes($email_settings['heading']) : $defualt_array[$key.'_heading'],
				'placeholder' => $defualt_array[$key.'_heading'],
				'type'     => 'text',
				'show'     => true,
				'class'	=> 'heading',
				'option_name' => 'woocommerce_customer_'.$key.'_order_settings',
				'option_key'=> 'heading',
				'option_type' => 'array',
				'class'		=> $key . '_sub_menu all_status_submenu heading',
			);
			$settings[ $key . '_additional_content' ] = array(
				'title'    => esc_html__( 'Email content', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => !empty($email_settings['additional_content']) ? stripslashes($email_settings['additional_content']) : $defualt_array[$key.'_additional_content'],
				'placeholder' => $defualt_array[$key.'_additional_content'],
				'type'     => 'textarea',
				'show'     => true,
				'class'	=> 'additional_content',
				'option_key'=> 'additional_content',
				'option_name' => 'woocommerce_customer_'.$key.'_order_settings',
				'option_type' => 'array',
				'class'		=> $key . '_sub_menu all_status_submenu additional_content',
			);
			$settings[ $key . '_codeinfoblock' ] = array(
				'title'    => esc_html__( 'Available Placeholders:', 'advanced-local-pickup-for-woocommerce' ),
				'default'  => '<code>{customer_first_name}<br>{customer_last_name}<br>{site_title}<br>{order_number}</code>',
				'type'     => 'codeinfo',
				'show'     => true,
				'class'		=> $key . '_sub_menu all_status_submenu',
			);
		};
		
		$settings = apply_filters( 'customizer_email_options_array' , $settings );
		
		return $settings; 

	}
	
	/*
	* Get html of fields
	*/
	public function get_html( $arrays ) {
		
		echo '<ul class="zoremmail-panels">';
		?>
		<div class="customize-section-title">
			<h3>
				<span class="customize-action">
					<?php esc_html_e( 'Local Pickup', 'advanced-local-pickup-pro' ); ?>
				</span>
				<?php esc_html_e( 'Email Customizer', 'advanced-local-pickup-pro' ); ?>
			</h3>
		</div>
		<?php
		foreach ( (array) $arrays as $id => $array ) {
			if ( isset($array['show']) && true != $array['show'] ) {
				continue; 
			}

			if ( isset($array['type']) && 'panel' == $array['type'] ) {
				?>
				<li id="<?php isset($array['id']) ? esc_attr_e($array['id']) : ''; ?>" data-label="<?php isset($array['title']) ? esc_html_e($array['title']) : ''; ?>" data-iframe_url="<?php isset($array['iframe_url']) ? esc_attr_e($array['iframe_url']) : ''; ?>" class="zoremmail-panel-title <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
					<span><?php isset($array['title']) ? esc_html_e($array['title']) : ''; ?></span>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
				</li>
				<?php
			}

		}
		echo '</ul>';
		
		echo '<ul class="zoremmail-sub-panels" style="display:none;">';
		foreach ( (array) $arrays as $id => $array ) {
			
			if ( isset($array['show']) && true != $array['show'] ) {
				continue; 
			}
			
			if ( isset($array['type']) && 'sub-panel-heading' == $array['type'] ) {
				?>
				<li data-id="<?php isset($array['parent']) ? esc_attr_e($array['parent']) : ''; ?>" class="zoremmail-sub-panel-heading <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?> <?php isset($array['parent']) ? esc_attr_e($array['parent']) : ''; ?>">
					<?php /*<button type="button" class="customize-section-back" tabindex="0">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<span><?php esc_html_e( $array['title'] ); ?></span>*/ ?>
					<div class="customize-section-title">
						<button type="button" class="customize-section-back" tabindex="0">
							<span class="screen-reader-text">Back</span>
						</button>
						<h3>
							<span class="customize-action">
								<?php esc_html_e( 'You are customizing', 'advanced-local-pickup-pro' ); ?>
							</span>
							<?php esc_html_e( $array['title'] ); ?>
						</h3>
					</div>
				</li>
				<?php
			}

			if ( isset($array['type']) && 'sub-panel' == $array['type'] ) {
				?>
				<li id="<?php isset($array['id']) ? esc_attr_e($array['id']) : ''; ?>"  data-type="<?php isset($array['parent']) ? esc_html_e($array['parent']) : ''; ?>" data-label="<?php isset($array['title']) ? esc_html_e($array['title']) : ''; ?>" class="zoremmail-sub-panel-title <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?> <?php isset($array['parent']) ? esc_attr_e($array['parent']) : ''; ?>">
					<span><?php isset($array['title']) ? esc_html_e($array['title']) : ''; ?></span>
					<span class="dashicons dashicons-arrow-right-alt2"></span>
				</li>
				<?php
			}
		}
		echo '</ul>';
		foreach ( (array) $arrays as $id => $array ) {

			if ( isset($array['show']) && true != $array['show'] ) {
				continue; 
			}

			if ( isset($array['type']) && 'panel' == $array['type'] ) {
				continue; 
			}

			if ( isset($array['type']) && 'sub-panel-heading' == $array['type'] ) {
				continue; 
			}
			
			if ( isset($array['type']) && 'sub-panel' == $array['type'] ) {
				continue; 
			}

			if ( isset($array['type']) && ( 'section' == $array['type'] ) ) {
				echo $id != 'heading' ? '</div>' : '';
				?>
				<div data-id="<?php isset($array['id']) ? esc_attr_e($array['id']) : ''; ?>" class="zoremmail-menu-submenu-title <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
					<?php /*<button type="button" class="customize-section-back" tabindex="0">
						<span class="dashicons dashicons-arrow-left-alt2"></span>
					</button>
					<span><?php esc_html_e( $array['title'] ); ?></span>*/ ?>
					<div class="customize-section-title">
						<button type="button" class="customize-section-back" tabindex="0">
							<span class="screen-reader-text">Back</span>
						</button>
						<h3>
							<span class="customize-action">
								<?php esc_html_e( 'Customizing', 'advanced-local-pickup-pro' ); ?>
							</span>
							<?php esc_html_e( $array['title'] ); ?>
						</h3>
					</div>
				</div>
				<div class="zoremmail-menu-contain" data-parent="<?php isset($array['parent']) ? esc_attr_e($array['parent']) : ''; ?>">
				<?php
			} else {
				$array_default = isset( $array['default'] ) ? $array['default'] : '';
				?>
				<div class="zoremmail-menu zoremmail-menu-inline zoremmail-menu-sub <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
					<div class="zoremmail-menu-item">
						<div class="<?php esc_attr_e( $id ); ?> <?php esc_attr_e( $array['type'] ); ?>">
							<?php if ( isset($array['title']) && 'checkbox' != $array['type'] ) { ?>
								<div class="menu-sub-title"><?php esc_html_e( $array['title'] ); ?></div>
							<?php } ?>
							<?php if ( isset($array['type']) && 'text' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<input type="text" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>" value="<?php echo esc_html( $array_default ); ?>" class="zoremmail-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
									<br>
									<span class="menu-sub-tooltip"><?php isset($array['desc']) ? esc_html_e($array['desc']) : ''; ?></span>
								</div>
							<?php } else if ( isset($array['type']) && 'textarea' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<textarea id="<?php esc_attr_e( $id ); ?>" rows="4" name="<?php esc_attr_e( $id ); ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>" class="zoremmail-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>"><?php echo esc_html( $array_default ); ?></textarea>
									<br>
									<span class="menu-sub-tooltip"><?php isset($array['desc']) ? esc_html_e($array['desc']) : ''; ?></span>
								</div>
							<?php } else if ( isset($array['type']) && 'codeinfo' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<span class="menu-sub-codeinfo <?php esc_html_e($array['type']); ?>"><?php echo isset($array['default']) ? wp_kses_post($array['default']) : ''; ?></span>
								</div>
							<?php } else if ( isset($array['type']) && 'select' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<select name="<?php esc_attr_e( $id ); ?>" id="<?php esc_attr_e( $id ); ?>" class="zoremmail-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>">
										<?php foreach ( (array) $array['options'] as $key => $val ) { ?>
											<option value="<?php echo esc_html($key); ?>" <?php echo $array_default == $key ? 'selected' : ''; ?>><?php echo esc_html($val); ?></option>
										<?php } ?>
									</select>
									<br>
									<span class="menu-sub-tooltip"><?php isset($array['desc']) ? esc_html_e($array['desc']) : ''; ?></span>
								</div>
							<?php } else if ( isset($array['type']) && 'color' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<input type="text" name="<?php esc_attr_e( $id ); ?>" id="<?php esc_attr_e( $id ); ?>" class="input-text regular-input zoremmail-input <?php esc_html_e($array['type']); ?> <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>" value="<?php echo esc_html( $array_default ); ?>" placeholder="<?php isset($array['placeholder']) ? esc_attr_e($array['placeholder']) : ''; ?>">
									<br>
									<span class="menu-sub-tooltip"><?php isset($array['desc']) ? esc_html_e($array['desc']) : ''; ?></span>
								</div>
							<?php } else if ( isset($array['type']) && 'checkbox' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<label class="menu-sub-title">
										<input type="hidden" name="<?php esc_attr_e( $id ); ?>" value="0"/>
										<input type="checkbox" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" class="zoremmail-checkbox <?php isset($array['class']) ? esc_attr_e($array['class']) : ''; ?>" value="1" <?php echo $array_default ? 'checked' : ''; ?>/>
										<?php esc_html_e( $array['title'] ); ?>
										<?php if ( isset($array['tip-tip'] ) ) { ?>
											<span class="woocommerce-help-tip tipTip" title="<?php echo esc_html( $array['tip-tip'] ); ?>"></span>
										<?php } ?>
									</label>
								</div>
							<?php } else if ( isset($array['type']) && 'radio_butoon' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<label class="menu-sub-title">
										<?php foreach ( $array['choices'] as $key => $value ) { ?>
											<label class="radio-button-label">
												<input type="radio" name="<?php echo esc_attr( $id ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php echo $array_default == $key ? 'checked' : ''; ?>/>
												<span><?php echo esc_html( $value ); ?></span>
											</label>
										<?php } ?>
									</label>
								</div>
							<?php } else if ( isset($array['type']) && 'tgl-btn' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<?php //echo $array_default; ?>
									<label class="menu-sub-title">
										<span class="tgl-btn-parent">
											<input type="hidden" name="<?php esc_attr_e( $id ); ?>" value="no">
											<input type="checkbox" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" class="tgl tgl-flat" <?php echo $array_default ? 'checked' : ''; ?> value="yes">
											<label class="tgl-btn" for="<?php esc_attr_e( $id ); ?>"></label>
										</span>
										<label for="<?php esc_attr_e( $id ); ?>"><?php isset($array['label']) ? esc_attr_e($array['label']) : ''; ?></label>
									</label>
								</div>
							<?php } else if ( isset($array['type']) && 'media' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<fieldset>
										<div class="media-botton">
											<input id="asre_upload_image_button" type="button" class="button" value="<?php esc_html_e( 'Select image' , 'default'); ?>" />
											<input type="hidden" name="<?php echo esc_html($id); ?>" class='<?php echo esc_html($id); ?> textfield-media' placeholder='Upload Image' value='
											<?php 
											if (!empty($array_default)) {
												echo esc_html($array_default);
											} 
											?>
											' id="<?php echo esc_html($id); ?>"/>
											<input type='hidden' name='asre_image_id' class='asre_image_id' placeholder="Image" value='' id='asre_image_id' style=""/>
										</div>
										<?php if ( !empty($array_default) ) { ?>
											<div class="asre-image-placeholder" style="display:none;">No File Selected</div>
											<div class="thumbnail asre-thumbnail-image">				
												<img src="<?php echo esc_url($array_default); ?>" id="asre_thumbnail" draggable="false" alt="">
												<span id="remove_btn" class="dashicons dashicons-dismiss"></span>
											</div>
										<?php } else { ?>
										<div class="asre-image-placeholder" style="display:block;">No File Selected</div>
											<div class="thumbnail asre-thumbnail-image" style="display:none;">			
												<img src="" draggable="false" id="asre_thumbnail" alt=""/>
												<span id="remove_btn" class="dashicons dashicons-dismiss"></span>
											</div>
										<?php } ?>
									</fieldset>
									<?php if ( isset($array['tooltip']) ) { ?>
										<span class="media-desc" style=""><?php echo esc_html($array['tooltip']); ?></span>
									<?php } ?>
								</div>
							<?php } else if ( isset($array['type']) && 'range' == $array['type'] ) { ?>
								<div class="menu-sub-field">
									<label class="menu-sub-title">
										<input type="range" class="zoremmail-range" id="<?php esc_attr_e( $id ); ?>" name="<?php esc_attr_e( $id ); ?>" value="<?php echo esc_html( $array_default ); ?>" min="<?php esc_html_e( $array['min'] ); ?>" max="<?php esc_html_e( $array['max'] ); ?>" oninput="this.nextElementSibling.value = this.value">
										<input style="width:50px;" class="slider__value" type="number" min="<?php esc_attr_e( $array['min'] ); ?>" max="<?php esc_attr_e( $array['max'] ); ?>" value="<?php echo esc_html( $array_default ); ?>">
									</label>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
				<?php
			}
		}
	}
	
	/**
	 * Get the email order status
	 *
	 * @param string $email_template the template string name.
	 */
	public function get_email_order_status( $email_template ) {
		
		$order_status = apply_filters( 'customizer_email_type_order_status_array', self::$email_types_order_status );
		
		$order_status = self::$email_types_order_status;
		
		if ( isset( $order_status[ $email_template ] ) ) {
			return $order_status[ $email_template ];
		} else {
			return 'processing';
		}
	}
	
	/**
	 * Get the email class name
	 *
	 * @param string $email_template the email template slug.
	 */
	public function get_email_class_name( $email_template ) {
		
		$class_names = apply_filters( 'customizer_email_type_class_name_array', self::$email_types_class_names );

		$class_names = self::$email_types_class_names;
		if ( isset( $class_names[ $email_template ] ) ) {
			return $class_names[ $email_template ];
		} else {
			return false;
		}
	}
	
	/**
	 * Get the email content
	 *
	 */
	public function get_preview_email( $send_email = false, $email_addresses = null ) { 
		
		// Load WooCommerce emails.
		$wc_emails      = WC_Emails::instance();
		$emails         = $wc_emails->get_emails();		
		
		$email_template = isset( $_GET['email_type'] ) ? sanitize_text_field($_GET['email_type']) : get_option( 'orderStatus', 'ready_pickup' );
		$preview_id = 'mockup';

		$email_type = self::get_email_class_name( $email_template );

		if ( false === $email_type ) {
			return false;
		}		 				
		
		// Reference email.
		if ( isset( $emails[ $email_type ] ) && is_object( $emails[ $email_type ] ) ) {
			$email = $emails[ $email_type ];
		
		}
		$order_status = self::get_email_order_status( $email_template );
		
		// Get an order
		$order = self::get_wc_order_for_preview( $order_status, $preview_id );

		if ( is_object( $order ) ) {
			// Get user ID from order, if guest get current user ID.
			if ( 0 === ( $user_id = (int) $order->get_meta( '_customer_user', true ) ) ) {
				$user_id = get_current_user_id();
			}
		} else {
			$user_id = get_current_user_id();
		}
		// Get user object
		$user = get_user_by( 'id', $user_id );
		
		if ( isset( $email ) ) {
			// Make sure gateways are running in case the email needs to input content from them.
			WC()->payment_gateways();
			// Make sure shipping is running in case the email needs to input content from it.
			WC()->shipping();
			switch ( $email_template ) {
				/**
				 * WooCommerce (default transactional mails).
				 */
				case 'customer_invoice':
					$email->object = $order;
					if ( is_object( $order ) ) {
						$email->invoice = ( function_exists( 'wc_gzdp_get_order_last_invoice' ) ? wc_gzdp_get_order_last_invoice( $order ) : null );
						$email->find['order-date']   = '{order_date}';
						$email->find['order-number'] = '{order_number}';
						$email->replace['order-date']   = wc_format_datetime( $email->object->get_date_created() );
						$email->replace['order-number'] = $email->object->get_order_number();
						// Other properties
						$email->recipient = $email->object->get_billing_email();
					}
					break;
				case 'customer_refunded_order':
					$email->object               = $order;
					$email->partial_refund       = $partial_status;
					if ( is_object( $order ) ) {
						$email->find['order-date']   = '{order_date}';
						$email->find['order-number'] = '{order_number}';
						$email->replace['order-date']   = wc_format_datetime( $email->object->get_date_created() );
						$email->replace['order-number'] = $email->object->get_order_number();
						// Other properties
						$email->recipient = $email->object->get_billing_email();
					}
					break;
				case 'customer_new_account':
					$email->object             = $user;
					$email->user_pass          = '{user_pass}';
					$email->user_login         = stripslashes( $email->object->user_login );
					$email->user_email         = stripslashes( $email->object->user_email );
					$email->recipient          = $email->user_email;
					$email->password_generated = true;
					break;
				case 'customer_note':
					$email->object                  = $order;
					$email->customer_note           = __( 'Hello! This is an example note', 'advanced-email-customizer' );
					if ( is_object( $order ) ) {
						$email->find['order-date']      = '{order_date}';
						$email->find['order-number']    = '{order_number}';
						$email->replace['order-date']   = wc_format_datetime( $email->object->get_date_created() );
						$email->replace['order-number'] = $email->object->get_order_number();
						// Other properties
						$email->recipient = $email->object->get_billing_email();
					}
					break;
				case 'customer_reset_password':
					$email->object     = $user;
					$email->user_id    = $user_id;
					$email->user_login = $user->user_login;
					$email->user_email = stripslashes( $email->object->user_email );
					$email->reset_key  = '{{reset-key}}';
					$email->recipient  = stripslashes( $email->object->user_email );
					break;
				
				/**
				 * Everything else.
				 */
				default:
					$email->object               = $order;
					$user_id = $order ? $order->get_meta( '_customer_user', true ) : '';
					if ( is_object( $order ) ) {
						$email->find['order-date']   = '{order_date}';
						$email->find['order-number'] = '{order_number}';
						$email->find['customer-first-name'] = '{customer_first_name}';
						$email->find['customer-last-name'] = '{customer_last_name}';
						$email->replace['order-date']   = wc_format_datetime( $email->object->get_date_created() );
						$email->replace['order-number'] = $email->object->get_order_number();
						$email->replace['customer-first-name'] = $email->object->get_billing_first_name();
						$email->replace['customer-last-name'] = $email->object->get_billing_last_name();
						// Other properties
						$email->recipient = $email->object->get_billing_email();
					}
					break;
			}

			if ( ! empty( $email ) ) {

				$content = $email->get_content();		
				$content = $email->style_inline( $content );
				$content = apply_filters( 'woocommerce_mail_content', $content );	
				
			} else {
				if ( false == $email->object ) {
					$content = '<div style="padding: 35px 40px; background-color: white;">' . __( 'This email type can not be previewed please try a different order or email type.', 'advanced-email-customizer' ) . '</div>';
				}
			}
		} else {
			$content = false;
		}
		
		echo $content;
		die();
	}
	
	/**
	 * Get WooCommerce order for preview
	 *
	 * @param string $order_status
	 * @return object
	 */
	public static function get_wc_order_for_preview( $order_status = null, $order_id = null ) {
		if ( ! empty( $order_id ) && 'mockup' != $order_id ) { 
			return wc_get_order( $order_id );
		} else {
			// Use mockup order
	
			// Instantiate order object
			$order = new WC_Order();

			// Other order properties
			$order->set_props( array(
				'id'                 => 1,
				'status'             => ( null === $order_status ? 'processing' : $order_status ),
				'billing_first_name' => 'Sherlock',
				'billing_last_name'  => 'Holmes',
				'billing_company'    => 'Detectives Ltd.',
				'billing_address_1'  => '221B Baker Street',
				'billing_city'       => 'London',
				'billing_postcode'   => 'NW1 6XE',
				'billing_country'    => 'GB',
				'billing_email'      => 'sherlock@holmes.co.uk',
				'billing_phone'      => '02079304832',
				'date_created'       => gmdate( 'Y-m-d H:i:s' ),
				'total'              => 24.90,
				'method_title'		=> 'Local Pickup',
				'method_id'			=> 'local_pickup'
			) );

			// Item #1
			$order_item = new WC_Order_Item_Product();
			$order_item->set_props( array(
				'name'     => 'A Study in Scarlet',
				'subtotal' => '9.95',
			) );
			$order->add_item( $order_item );

			// Item #2
			$order_item = new WC_Order_Item_Product();
			$order_item->set_props( array(
				'name'     => 'The Hound of the Baskervilles',
				'subtotal' => '14.95',
			) );
			$order->add_item( $order_item );
			
			$item = new WC_Order_Item_Shipping();
			$item->set_props( array(
				'method_title' => 'Local Pickup',
				'method_id' => 'local_pickup'
			) );
			$order->add_item($item);

			// Return mockup order
			return $order;
		}

	}
	
	/**
	 * Get Order Ids
	 *
	 * @return array
	 */
	public static function get_order_ids() {		
		$order_array = array();
		$order_array['mockup'] = esc_html( 'Mockup Order', 'advanced-local-pickup-for-woocommerce' );

		$orders = new WP_Query(
			array(
				'post_type'      => 'shop_order',
				'post_status'    => array( 'wc-ready-pickup', 'wc-pickup'	),
				'posts_per_page' => 20,
			)
		);
		
		if ( $orders->posts ) {
			foreach ( $orders->posts as $order ) {
				// Get order object.
				$order_object = new WC_Order( $order->ID );
				$order_array[ $order_object->get_id() ] = $order_object->get_id() . ' - ' . $order_object->get_billing_first_name() . ' ' . $order_object->get_billing_last_name();
			}
		}

		return $order_array;
	}
	
	/**
	 * Get preview URL(admin load url)
	 *
	 */
	public function get_email_preview_url( $status ) {
		return add_query_arg( array(
			'action'	=> 'email_preview',
			'email_type'	=> $status
		), admin_url( 'admin-ajax.php' ) );
	}
}
