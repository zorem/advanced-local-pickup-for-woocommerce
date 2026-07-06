<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.DirectDatabaseQuery.SchemaChange -- Queries operate on the plugin's own {$wpdb->prefix}alp_pickup_location table; caching is not applicable to a settings-editor UI where writes must be reflected immediately.
// phpcs:disable WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder,WordPress.DB.PreparedSQLPlaceholders.ReplacementsWrongNumber -- %1s / %2s used for identifiers (table/column names) derived from $wpdb->prefix — safe internal values.
// phpcs:disable WordPress.Security.NonceVerification.Recommended -- Read-only admin display screens read GET vars after capability check; write endpoints verify via check_admin_referer() explicitly.
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- Fires third-party integration hooks (WPML `wpml_register_single_string`) whose names are established by external plugins.

class WC_Local_Pickup_Admin {

	/**
	 * Get the class instance
	 *
	 * @since  1.0
	 * @return WC_Local_pickup_Admin
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
	public $table;
	public $data;
	
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
		
		add_action('admin_menu', array( $this, 'register_woocommerce_menu' ), 99 );
		
		//ajax save admin api settings
		add_action( 'wp_ajax_wclp_setting_form_update', array( $this, 'wclp_setting_form_update_callback' ) );
		add_action( 'wp_ajax_wclp_location_edit_form_update', array( $this, 'wclp_location_edit_form_update_callback' ) );
		
		// Register new status
		add_action( 'init', array( $this, 'register_pickup_order_status' ) );
		
		// Add to list of WC Order statuses
		add_filter( 'wc_order_statuses', array( $this, 'add_pickup_to_order_statuses' ) );
		add_filter( 'bulk_actions-edit-shop_order', array( $this, 'add_bulk_actions_change_order_status' ), 50, 1 );
		add_filter( 'bulk_actions-woocommerce_page_wc-orders', array( $this, 'add_bulk_actions_change_order_status'), 50, 1 );			
		
		// Add to custom email for WC Order statuses
		add_filter( 'woocommerce_email_before_order_table', array( $this, 'add_location_address_detail_emails' ), 2, 4 );
		
		// Add Addition content for processing email
		add_filter( 'woocommerce_email_before_order_table', array( $this, 'add_addional_content_on_processing_email' ), 1, 4 );
		add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_local_pickup_order_status_actions_button' ), 100, 2 );
		
		add_action( 'woocommerce_view_order', array( $this, 'add_location_address_detail_order' ), 10, 2 );
		
		add_action( 'woocommerce_order_details_before_order_table', array( $this, 'add_location_address_detail_order_received' ), 10, 2 );
		
		add_action( 'admin_footer', array( $this, 'footer_function' ), 1 );
		
		add_action( 'wp_ajax_wclp_update_state_dropdown', array( $this, 'wclp_update_state_dropdown_fun') );
		add_action( 'wp_ajax_wclp_update_work_hours_list', array( $this, 'wclp_update_work_hours_list_fun' ) );
		add_action( 'wp_ajax_wclp_update_edit_location_form', array( $this, 'wclp_update_edit_location_form_fun') );
		add_action( 'wp_ajax_wclp_apply_work_hours', array( $this, 'wclp_apply_work_hours_fun' ) );

		// Telemetry preferences save — dedicated handler so the response is a
		// clean JSON envelope. Bypasses the shared WC_Trackers handler which
		// bare-die()s when both toggles are 0 and leaves the AJAX request
		// without a body.
		add_action( 'wp_ajax_alp_free_telemetry_save', array( $this, 'telemetry_save' ) );
				
		add_filter( 'woocommerce_valid_order_statuses_for_order_again', array( $this, 'add_reorder_button_pickup' ), 50, 1 );
		add_filter( 'admin_body_class', array( $this, 'wp_body_classes' ) );
		
	}
	
	public function wp_body_classes( $classes ) {
		$page = isset($_GET['page']) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( isset($page) && 'local_pickup' == sanitize_text_field($page)) {
			$classes .= ' woocommerce_page_local_pickup';
		}
		return $classes;
	}

	
	/*
	* Admin Menu add function
	* WC sub menu 
	*/
	public function register_woocommerce_menu() {
		
		if ( class_exists( 'Zorem_Local_Pickup_Pro' ) ) {
			$menu_label = 'Local Pickup <strong style="color:#009933;">Pro</strong>';	
		} else {
			$menu_label = 'Local Pickup';	
		}
		
		add_submenu_page( 'woocommerce', 'Local Pickup', $menu_label, 'manage_options', 'local_pickup', array( $this, 'woocommerce_local_pickup_page_callback' ) ); //woocommerce_local_pickup_page_callback
	}
	
	/*
	* Callback for Zorem Local Pickup page
	*/
	public function woocommerce_local_pickup_page_callback() {
		$tab        = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
		$active_tab = ! empty( $tab ) ? $tab : 'settings';
		$tab_base   = '?page=local_pickup';
		?>
		<div class="alp-settings-app zui-scope" id="alp-settings-app">
			<header class="zui-header" id="alp-set-header">
				<div class="zui-header__bar">
					<div class="zui-header__lead">
						<button type="button" class="zui-header__menu-toggle" data-ast-drawer-toggle aria-label="<?php esc_attr_e( 'Open settings menu', 'advanced-local-pickup-for-woocommerce' ); ?>">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
						</button>
						<div class="zui-brand">
							<span class="zui-brand__emblem" aria-hidden="true">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/><path d="M22 7v3a2 2 0 0 1-4 0V7"/><path d="M18 10a2 2 0 0 1-4 0V7"/><path d="M14 10a2 2 0 0 1-4 0V7"/><path d="M10 10a2 2 0 0 1-4 0V7"/><path d="M6 10a2 2 0 0 1-4 0V7"/></svg>
							</span>
							<span class="zui-brand__name">ALP</span>
							<span class="zui-brand__badge zui-brand__badge--free"><?php esc_html_e( 'FREE', 'advanced-local-pickup-for-woocommerce' ); ?></span>
							<span class="zui-brand__tagline"><?php esc_html_e( 'Zorem Local Pickup', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						</div>
					</div>
					<div class="zui-header-actions">
						<a class="zui-header-action zui-header-action--with-label" href="https://docs.zorem.com/docs/zorem-local-pickup/?utm_source=wp-admin&utm_medium=ALP&utm_campaign=Docs" target="_blank" rel="noreferrer noopener">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
							<span class="zui-header-action__label"><?php esc_html_e( 'Docs Portal', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						</a>
					</div>
				</div>
				<nav class="zui-tabs" aria-label="<?php esc_attr_e( 'ALP modules', 'advanced-local-pickup-for-woocommerce' ); ?>">
					<a class="zui-tabs__item<?php echo ( 'settings' === $active_tab ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . '&tab=settings' ); ?>" data-tab="settings" data-label="<?php esc_attr_e( 'Settings', 'advanced-local-pickup-for-woocommerce' ); ?>"<?php echo ( 'settings' === $active_tab ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Settings', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-tabs__item<?php echo ( 'locations' === $active_tab ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . '&tab=locations' ); ?>" data-tab="locations" data-label="<?php esc_attr_e( 'Pickup Location', 'advanced-local-pickup-for-woocommerce' ); ?>"<?php echo ( 'locations' === $active_tab ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Pickup Location', 'advanced-local-pickup-for-woocommerce' ); ?></a>
					<a class="zui-tabs__item zui-tabs__item--gopro<?php echo ( 'go-pro' === $active_tab ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $tab_base . '&tab=go-pro' ); ?>" data-tab="go-pro" data-label="<?php esc_attr_e( 'Go Pro', 'advanced-local-pickup-for-woocommerce' ); ?>"<?php echo ( 'go-pro' === $active_tab ) ? ' aria-current="page"' : ''; ?>><?php esc_html_e( 'Go Pro', 'advanced-local-pickup-for-woocommerce' ); ?> <span class="zui-tabs__sparkle">&#10024;</span></a>
				</nav>
			</header>

			<div class="woocommerce wclp_admin_layout">
				<div class="wclp_admin_content">
					<?php
					echo '<div class="zui-tab-panel" data-tab="settings"' . ( 'settings' === $active_tab ? '' : ' hidden' ) . '>';
					require 'views/wclp_setting_tab.php';
					echo '</div>';

					echo '<div class="zui-tab-panel" data-tab="locations"' . ( 'locations' === $active_tab ? '' : ' hidden' ) . '>';
					require 'views/wclp_locations_tab.php';
					echo '</div>';

					echo '<div class="zui-tab-panel" data-tab="go-pro"' . ( 'go-pro' === $active_tab ? '' : ' hidden' ) . '>';
					require 'views/wclp_gopro_tab.php';
					echo '</div>';
					?>
				</div>
			</div>
		</div>
		<?php
	}
	
	
	/*
	* Settings form save for Setting tab
	*/
	public function wclp_setting_form_update_callback() {			
		
		if ( !current_user_can( 'manage_options' ) ) {
			echo json_encode( array('permission' => 'false') );
			die();
		}
		
		if ( ! empty( $_POST ) && check_admin_referer( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ) ) {
			
			$wclp_processing_additional_content = isset($_POST['wclp_processing_additional_content']) ? wp_kses_post( wp_unslash( $_POST['wclp_processing_additional_content'] ) ) : '';
			if (isset($wclp_processing_additional_content)) {
				update_option( 'wclp_processing_additional_content', $wclp_processing_additional_content );
			}
			
			$display_in_processing_email = isset($_POST['wclp_show_pickup_instruction']['display_in_processing_email']) ? sanitize_text_field( wp_unslash( $_POST['wclp_show_pickup_instruction']['display_in_processing_email'] ) ) : '';
			$display_in_order_received_page = isset($_POST['wclp_show_pickup_instruction']['display_in_order_received_page']) ? sanitize_text_field( wp_unslash( $_POST['wclp_show_pickup_instruction']['display_in_order_received_page'] ) ) : '';
			$display_in_order_details_page = isset($_POST['wclp_show_pickup_instruction']['display_in_order_details_page']) ? sanitize_text_field( wp_unslash( $_POST['wclp_show_pickup_instruction']['display_in_order_details_page'] ) ) : '';
			$wclp_show_pickup_instruction_opt = array(
				'display_in_processing_email' => $display_in_processing_email,
				'display_in_order_received_page' => $display_in_order_received_page,
				'display_in_order_details_page' => $display_in_order_details_page,
			);			
			update_option( 'wclp_show_pickup_instruction', $wclp_show_pickup_instruction_opt);
			
	
			// local pickup setting html hook
			do_action('wclp_general_setting_save_hook');
			
			$wclp_status_ready_pickup = isset($_POST['wclp_status_ready_pickup']) ? sanitize_text_field( wp_unslash( $_POST['wclp_status_ready_pickup'] ) ) : '';
			update_option( 'wclp_status_ready_pickup', $wclp_status_ready_pickup );
			$wclp_ready_pickup_status_label_color = isset($_POST['wclp_ready_pickup_status_label_color']) ? sanitize_text_field( wp_unslash( $_POST['wclp_ready_pickup_status_label_color'] ) ) : '';
			update_option( 'wclp_ready_pickup_status_label_color', $wclp_ready_pickup_status_label_color );
			$wclp_ready_pickup_status_label_font_color = isset($_POST['wclp_ready_pickup_status_label_font_color']) ? sanitize_text_field( wp_unslash( $_POST['wclp_ready_pickup_status_label_font_color'] ) ) : '';
			update_option( 'wclp_ready_pickup_status_label_font_color', $wclp_ready_pickup_status_label_font_color );
			$wclp_status_picked_up = isset($_POST['wclp_status_picked_up']) ? sanitize_text_field( wp_unslash( $_POST['wclp_status_picked_up'] ) ) : '';
			update_option( 'wclp_status_picked_up', $wclp_status_picked_up );
			$wclp_pickup_status_label_color = isset($_POST['wclp_pickup_status_label_color']) ? sanitize_text_field( wp_unslash( $_POST['wclp_pickup_status_label_color'] ) ) : '';
			update_option( 'wclp_pickup_status_label_color', $wclp_pickup_status_label_color );
			$wclp_pickup_status_label_font_color = isset($_POST['wclp_pickup_status_label_font_color']) ? sanitize_text_field( wp_unslash( $_POST['wclp_pickup_status_label_font_color'] ) ) : '';
			update_option( 'wclp_pickup_status_label_font_color', $wclp_pickup_status_label_font_color );					
			
			$pickup_email = get_option('woocommerce_customer_pickup_order_settings', array());									
			$wclp_enable_pickup_email = isset($_POST['wclp_enable_pickup_email']) ? sanitize_text_field( wp_unslash( $_POST['wclp_enable_pickup_email'] ) ) : '';
			if (1 == $wclp_enable_pickup_email) {
				update_option( 'customizer_pickup_order_settings_enabled', $wclp_enable_pickup_email );
				$enabled = 'yes';
			} else {
				update_option( 'customizer_pickup_order_settings_enabled', sanitize_text_field( '' ));	
				$enabled = 'no';
			}
			
			$opt = array(
				'enabled' => $enabled,
				'subject' => isset($pickup_email['subject']) ? $pickup_email['subject'] : '',
				'heading' => isset($pickup_email['heading']) ? $pickup_email['heading'] : '',
				'additional_content' => isset($pickup_email['additional_content']) ? $pickup_email['additional_content'] : '',
				'recipient' => isset($pickup_email['recipient']) ? $pickup_email['recipient'] : '',
				'email_type' => isset($pickup_email['email_type']) ? $pickup_email['email_type'] : 'html',
			);
			update_option( 'woocommerce_customer_pickup_order_settings', wc_clean( $opt ) );
			
			$ready_pickup_email = get_option('woocommerce_customer_ready_pickup_order_settings');
			$wclp_enable_ready_pickup_email = isset($_POST['wclp_enable_ready_pickup_email']) ? sanitize_text_field( wp_unslash( $_POST['wclp_enable_ready_pickup_email'] ) ) : '';
			if (1 == $wclp_enable_ready_pickup_email) {
				update_option( 'customizer_ready_pickup_order_settings_enabled', $wclp_enable_ready_pickup_email );
				$enabled = 'yes';
			} else {
				update_option( 'customizer_ready_pickup_order_settings_enabled', sanitize_text_field( '' ));	
				$enabled = 'no';
			}
			
			$opt = array(
				'enabled' => $enabled,
				'subject' => isset($ready_pickup_email['subject']) ? $ready_pickup_email['subject'] : '',
				'heading' => isset($ready_pickup_email['heading']) ? $ready_pickup_email['heading'] : '',
				'additional_content' => isset($ready_pickup_email['additional_content']) ? $ready_pickup_email['additional_content'] : '',
				'recipient' => isset($ready_pickup_email['recipient']) ? $ready_pickup_email['recipient'] : '',
				'email_type' => isset($ready_pickup_email['email_type']) ? $ready_pickup_email['email_type'] : 'html',
			);
			update_option( 'woocommerce_customer_ready_pickup_order_settings', wc_clean( $opt ) );
			echo json_encode( array('success' => 'true') );
			die();
	
		}
	}
	
	/*
	* Get all data 
	*/
	public function get_data() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'alp_pickup_location';
		// Avoid database table not found errors when plugin is first installed
		// by checking if the plugin option exists
		if ( empty( $this->data ) ) {
			$this->data = array();

			$wpdb->hide_errors();
			
			$results = $wpdb->get_results( $wpdb->prepare('SELECT * FROM %1s ORDER BY position ASC', $this->table) ); //ORDER BY name ASC			
						
			$this->data = $results;
		}
		return $this->data;
	}
	
	/*
	* Settings form save for Setting tab
	*/
	public function wclp_location_edit_form_update_callback() {

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json( array( 'permission' => 'false' ) );
		}

		// Validate nonce ourselves so a failure returns JSON instead of the
		// default `-1` text response (which then breaks dataType:"json" parsing).
		$posted_nonce = isset( $_POST['wclp_location_edit_form_nonce_field'] ) ? sanitize_text_field( wp_unslash( $_POST['wclp_location_edit_form_nonce_field'] ) ) : '';
		if ( ! wp_verify_nonce( $posted_nonce, 'wclp_location_edit_form_action' ) ) {
			wp_send_json( array( 'success' => 'fail', 'msg' => 'Nonce verification failed. Please reload the page and try again.' ) );
		}

		global $wpdb;

		$id           = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$existing_rows = $this->get_data();

		// Insert flow for a brand-new location.
		if ( '0' === (string) $id || '' === $id ) {
			if ( ! class_exists( 'Zorem_Local_Pickup_Pro' ) && count( (array) $existing_rows ) >= 1 ) {
				wp_send_json( array(
					'success' => 'fail',
					'msg'     => 'The free version supports only one pickup location. Upgrade to PRO for multiple.',
				) );
			}
			$wpdb->insert( $this->table, array(
				'store_name' => isset( $_POST['wclp_store_name'] ) ? sanitize_text_field( wp_unslash( $_POST['wclp_store_name'] ) ) : '',
			) );
			$id = (int) $wpdb->insert_id;
			if ( ! $id ) {
				wp_send_json( array( 'success' => 'fail', 'msg' => 'Could not create a new location row.' ) );
			}
		}

		$wclp_store_name          = isset( $_POST['wclp_store_name'] )          ? sanitize_text_field( wp_unslash( $_POST['wclp_store_name'] ) )          : '';
		$wclp_store_address       = isset( $_POST['wclp_store_address'] )       ? sanitize_text_field( wp_unslash( $_POST['wclp_store_address'] ) )       : '';
		$wclp_store_address_2     = isset( $_POST['wclp_store_address_2'] )     ? sanitize_text_field( wp_unslash( $_POST['wclp_store_address_2'] ) )     : '';
		$wclp_store_city          = isset( $_POST['wclp_store_city'] )          ? sanitize_text_field( wp_unslash( $_POST['wclp_store_city'] ) )          : '';
		$wclp_default_country     = isset( $_POST['wclp_default_country'] )     ? sanitize_text_field( wp_unslash( $_POST['wclp_default_country'] ) )     : '';
		$wclp_store_postcode      = isset( $_POST['wclp_store_postcode'] )      ? sanitize_text_field( wp_unslash( $_POST['wclp_store_postcode'] ) )      : '';
		$wclp_store_phone         = isset( $_POST['wclp_store_phone'] )         ? sanitize_text_field( wp_unslash( $_POST['wclp_store_phone'] ) )         : '';
		$wclp_default_time_format = isset( $_POST['wclp_default_time_format'] ) ? sanitize_text_field( wp_unslash( $_POST['wclp_default_time_format'] ) ) : '';
		$wclp_store_instruction   = isset( $_POST['wclp_store_instruction'] )   ? wp_kses_post( wp_unslash( $_POST['wclp_store_instruction'] ) )          : '';

		if ( isset( $_POST['wclp_store_days'] ) && is_array( $_POST['wclp_store_days'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wc_clean() sanitizes each element of the array; Plugin Check does not recognize it.
			$wclp_store_days = serialize( wc_clean( wp_unslash( $_POST['wclp_store_days'] ) ) );
		} else {
			// Preserve existing value on the row (don't fall back to a global option
			// because that'd wipe the location's hours whenever the popup HTML wasn't
			// in the POST for some reason).
			$current = $this->get_data_byid( $id );
			$wclp_store_days = isset( $current->store_days ) ? $current->store_days : serialize( array() );
		}

		$data = array(
			'store_name'        => $wclp_store_name,
			'store_address'     => $wclp_store_address,
			'store_address_2'   => $wclp_store_address_2,
			'store_city'        => $wclp_store_city,
			'store_country'     => $wclp_default_country,
			'store_postcode'    => $wclp_store_postcode,
			'store_phone'       => $wclp_store_phone,
			'store_time_format' => $wclp_default_time_format,
			'store_days'        => $wclp_store_days,
			'store_instruction' => $wclp_store_instruction,
		);

		$data = apply_filters( 'wclp_location_edit_form_save_hook', $data );

		// Make sure every column we're about to write actually exists on the table
		// (older installs missed a couple of columns from a partial schema).
		$tabledata = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s LIMIT 1', $this->table ) );
		foreach ( (array) $data as $col => $val ) {
			if ( 'store_name' === $col ) { continue; }
			if ( $tabledata && ! isset( $tabledata->$col ) ) {
				$wpdb->query( $wpdb->prepare( 'ALTER TABLE %1s ADD %2s text NOT NULL', $this->table, $col ) );
			}
		}

		$result = $wpdb->update( $this->table, $data, array( 'id' => $id ) );

		if ( false === $result ) {
			wp_send_json( array(
				'success' => 'fail',
				'msg'     => 'Database update failed: ' . esc_html( $wpdb->last_error ),
			) );
		}

		do_action( 'wpml_register_single_string', 'zorem-local-pickup-pro', $wclp_store_name,        $wclp_store_name );
		do_action( 'wpml_register_single_string', 'zorem-local-pickup-pro', $wclp_store_instruction, $wclp_store_instruction );
		do_action( 'wpml_register_single_string', 'zorem-local-pickup-pro', $wclp_store_address,     $wclp_store_address );
		do_action( 'wpml_register_single_string', 'zorem-local-pickup-pro', $wclp_store_address_2,   $wclp_store_address_2 );
		do_action( 'wpml_register_single_string', 'zorem-local-pickup-pro', $wclp_store_city,        $wclp_store_city );

		wp_send_json( array( 'success' => 'true', 'id' => $id ) );
	}
	
	/*
	* Get data by id
	*/
	public function get_data_byid( $id ) {
		global $wpdb;

		$this->table = $wpdb->prefix . 'alp_pickup_location';
		
		$results = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM %1s WHERE id = %2d', $this->table, $id ) );
		
		$results = $this->get_slaches_data_byid($results);
		
		return $results;
	}
	
	/**
	 * Remove slashes from strings, arrays and objects
	 * 
	 * @param    mixed   input data
	 * @return   mixed   cleaned input data
	 */
	public function get_slaches_data_byid( $results ) {
		if (is_array($results)) {
			$results = array_map('get_slaches_data_byid', $results);
		} elseif (is_object($results)) {
			$vars = get_object_vars($results);
			foreach ($vars as $k=>$v) {
				$results->$k = stripslashes($v);
			}
		} else {
			$results = stripslashes($results);
		}
		return $results;
	}
	
	// Register new status
	public function register_pickup_order_status() {
		
		$ready_for_pickup = get_option( 'wclp_status_ready_pickup', 0);
		if (true == $ready_for_pickup) {
			register_post_status( 'wc-ready-pickup', array(
				'label'                     => esc_html__( 'Ready for Pickup', 'advanced-local-pickup-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Ready for Pickup (%s)', 'Ready for Pickup (%s)', 'advanced-local-pickup-for-woocommerce' )
			) );
		}

		$picked = get_option( 'wclp_status_picked_up', 0);
		if (true == $picked) {
			register_post_status( 'wc-pickup', array(
				'label'                     => esc_html__( 'Picked up', 'advanced-local-pickup-for-woocommerce' ),
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				/* translators: %s: number of orders */
				'label_count'               => _n_noop( 'Picked up (%s)', 'Picked up (%s)', 'advanced-local-pickup-for-woocommerce' )
			) );
		}
		
	}
	
	// Add to list of WC Order statuses
	public function add_pickup_to_order_statuses( $order_statuses ) {
	 
		$new_order_statuses = array();
	 
		// add new order status after processing
		foreach ( $order_statuses as $key => $status ) {
	 
			$new_order_statuses[ $key ] = $status;
			
			$ready_for_pickup = get_option( 'wclp_status_ready_pickup', 0);
			if (true == $ready_for_pickup) {
				if ( 'wc-processing' === $key ) {
					$new_order_statuses['wc-ready-pickup'] = esc_html__( 'Ready for Pickup', 'advanced-local-pickup-for-woocommerce' );
				}
			}
			
			$picked = get_option( 'wclp_status_picked_up', 0);
			if (true == $picked) {
				if ( 'wc-processing' === $key ) {
					$new_order_statuses['wc-pickup'] = esc_html__( 'Picked up', 'advanced-local-pickup-for-woocommerce' );
				}
			}
		}
	 
		return $new_order_statuses;
	}
	
	// Add bulk action change status to custom order status
	public function add_bulk_actions_change_order_status( $bulk_actions ) {
		$ready_for_pickup = get_option( 'wclp_status_ready_pickup', 0);
		if (true == $ready_for_pickup) {
			$bulk_actions['mark_ready-pickup'] = esc_html__( 'Change status to ready for pickup', 'advanced-local-pickup-for-woocommerce' );
		}
		$picked = get_option( 'wclp_status_picked_up', 0);
		if (true == $picked) {
			$bulk_actions['mark_pickup'] = esc_html__( 'Change status to picked up', 'advanced-local-pickup-for-woocommerce' );
		}
		return $bulk_actions;		
	}
	
	public function add_location_address_detail_order_received( $order_id ) {		
		
		$wclp_show_pickup_instruction = get_option('wclp_show_pickup_instruction');
		
		////IF display location details not enabel then @return;
		if (!is_order_received_page()) {
			return;
		}

		if (!isset($wclp_show_pickup_instruction['display_in_order_received_page'])) {
			return;
		}
		if ( isset($wclp_show_pickup_instruction['display_in_order_received_page']) && '1' != $wclp_show_pickup_instruction['display_in_order_received_page'] ) {
			return;		
		}
		
		$order = wc_get_order($order_id);
		
		// Iterating through order shipping items
		foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
		
		//IF  dshipping method is not local pickup then @return;
		if ( !isset($shipping_method ) ) {
			return;
		}
		if ( isset($shipping_method ) && 'local_pickup' != $shipping_method ) {
			return;
		}
		
		$local_template	= get_stylesheet_directory() . '/woocommerce/emails/pickup-instruction.php';
		
		$data = $this->get_data();
		$location_id = get_option('location_defualt', min($data)->id);
		
		$location = $this->get_data_byid($location_id);
		
		$country_code = isset($location) ? $location->store_country : get_option('woocommerce_default_country');
		
		$split_country = explode( ':', $country_code );
		$store_country = isset($split_country[0]) ? $split_country[0] : '';
		$store_state   = isset($split_country[1]) ? $split_country[1] : '';
				
		$store_days = isset($location->store_days) ? unserialize($location->store_days) : array();
		$all_days = array(
			'sunday' => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday' => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday' => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday' => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday' => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday' => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$w_day = array_slice($all_days, get_option('start_of_week'));
		foreach ($all_days as $key=>$val) {
			$w_day[$key] = $val;
		}
		foreach ($store_days as $key => $val) {
			if ($w_day[$key]) {
				$w_day[$key] = $val;
			}
		}
				
		$wclp_default_time_format = isset($location) ? $location->store_time_format : '24';
		if ('12' == $wclp_default_time_format) {
			foreach ($w_day as $key=>$val) {	
				if (isset($val['wclp_store_hour'])) {
					$last_digit = explode(':', $val['wclp_store_hour']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					} else {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					}
				}
				if (isset($val['wclp_store_hour_end'])) {
					$last_digit = explode(':', $val['wclp_store_hour_end']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					} else {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					}
				}
				$w_day[$key] = $val;				
			}	
		}
		
		if ( file_exists( $local_template ) && wp_is_writable( $local_template )) {	
			wc_get_template( 'myaccount/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', get_stylesheet_directory() . '/woocommerce/' );
		} else {
			wc_get_template( 'myaccount/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', wc_local_pickup()->get_plugin_path() . '/templates/' );	
		}			
		
	}					
	
	public function add_location_address_detail_order( $order_id ) {		
		
		$wclp_show_pickup_instruction = get_option('wclp_show_pickup_instruction');
		////IF display location details not enabel then @return;
		
		if (!is_view_order_page()) {
			return;
		}

		if (!isset($wclp_show_pickup_instruction['display_in_order_details_page'])) {
			return;
		}
		if ( isset($wclp_show_pickup_instruction['display_in_order_details_page']) && '1' != $wclp_show_pickup_instruction['display_in_order_details_page'] ) {
			return; 
		}
		
		$order = wc_get_order($order_id);
		
		// Iterating through order shipping items
		foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
		
		//IF  dshipping method is not local pickup then @return;
		if ( !isset($shipping_method ) ) {
			return;
		}
		if ( isset($shipping_method ) && 'local_pickup' != $shipping_method ) {
			return;		
		}
		
		$local_template	= get_stylesheet_directory() . '/woocommerce/emails/pickup-instruction.php';
		
		$data = $this->get_data();
		$location_id = get_option('location_defualt', min($data)->id);
		
		$location = $this->get_data_byid($location_id);
		
		$country_code = isset($location) ? $location->store_country : get_option('woocommerce_default_country');
		
		$split_country = explode( ':', $country_code );
		$store_country = isset($split_country[0]) ? $split_country[0] : '';
		$store_state   = isset($split_country[1]) ? $split_country[1] : '';
				
		$store_days = isset($location->store_days) ? unserialize($location->store_days) : array();
		$all_days = array(
			'sunday' => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday' => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday' => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday' => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday' => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday' => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$w_day = array_slice($all_days, get_option('start_of_week'));
		foreach ($all_days as $key=>$val) {
			$w_day[$key] = $val;
		}
		foreach ($store_days as $key => $val) {
			if ($w_day[$key]) {
				$w_day[$key] = $val;
			}
		}
				
		$wclp_default_time_format = isset($location) ? $location->store_time_format : '24';
		if ('12' == $wclp_default_time_format) {
			foreach ($w_day as $key=>$val) {	
				if (isset($val['wclp_store_hour'])) {
					$last_digit = explode(':', $val['wclp_store_hour']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					} else {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					}
				}
				if (isset($val['wclp_store_hour_end'])) {
					$last_digit = explode(':', $val['wclp_store_hour_end']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					} else {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					}
				}
				$w_day[$key] = $val;				
			}	
		}
		
		if ( file_exists( $local_template ) && wp_is_writable( $local_template )) {	
			wc_get_template( 'myaccount/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', get_stylesheet_directory() . '/woocommerce/' );
		} else {
			wc_get_template( 'myaccount/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', wc_local_pickup()->get_plugin_path() . '/templates/' );	
		}			
		
	}	
	
	public function add_location_address_detail_emails( $order, $sent_to_admin, $plain_text, $email ) {		
		
		//IF display location details not enabel then @return;
		$wclp_show_pickup_instruction = get_option('wclp_show_pickup_instruction');
		
		// Iterating through order shipping items
		foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
		
		//IF  dshipping method is not local pickup then @return;
		if ( !isset($shipping_method ) ) {
			return;
		}
		if ( isset($shipping_method ) && 'local_pickup' != $shipping_method ) {
			return;		
		}
		
		$local_template	= get_stylesheet_directory() . '/woocommerce/emails/pickup-instruction.php';
		
		$data = $this->get_data();
		$location_id = get_option('location_defualt', min($data)->id);
		
		$location = $this->get_data_byid($location_id);
		
		$country_code = isset($location) ? $location->store_country : get_option('woocommerce_default_country');
		
		$split_country = explode( ':', $country_code );
		$store_country = isset($split_country[0]) ? $split_country[0] : '';
		$store_state   = isset($split_country[1]) ? $split_country[1] : '';
				
		$store_days = isset($location->store_days) ? unserialize($location->store_days) : array();
		$all_days = array(
			'sunday' => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday' => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday' => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday' => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday' => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday' => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$w_day = array_slice($all_days, get_option('start_of_week'));
		foreach ($all_days as $key=>$val) {
			$w_day[$key] = $val;
		}
		if ( !empty($store_days) ) {
			foreach ($store_days as $key => $val) {
				if ($w_day[$key]) {
					$w_day[$key] = $val;
				}
			}
		}
				
		$wclp_default_time_format = isset($location) ? $location->store_time_format : '24';
		if ('12' == $wclp_default_time_format) {
			foreach ($w_day as $key=>$val) {	
				if (isset($val['wclp_store_hour'])) {
					$last_digit = explode(':', $val['wclp_store_hour']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					} else {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					}
				}
				if (isset($val['wclp_store_hour_end'])) {
					$last_digit = explode(':', $val['wclp_store_hour_end']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					} else {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					}
				}
				$w_day[$key] = $val;				
			}	
		}
		
		if ( 'customer_ready_pickup_order' == $email->id || ( isset($wclp_show_pickup_instruction['display_in_processing_email']) && '1' == $wclp_show_pickup_instruction['display_in_processing_email'] && 'customer_processing_order' == $email->id ) ) { 

			if ( file_exists( $local_template ) && wp_is_writable( $local_template )) {	
				wc_get_template( 'emails/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', get_stylesheet_directory() . '/woocommerce/' );
			} else {
				wc_get_template( 'emails/pickup-instruction.php', array( 'w_day' => $w_day, 'location' => $location, 'store_country' => $store_country, 'store_state' => $store_state ), 'advanced-local-pickup-for-woocommerce/', wc_local_pickup()->get_plugin_path() . '/templates/' );	
			}	

		}
		
	}
	
	public function add_addional_content_on_processing_email( $order, $sent_to_admin, $plain_text, $email ) {

		if ( is_object($email) && isset($email->id) && 'customer_processing_order' != $email->id ) {
			return;
		}

		// Iterating through order shipping items
		foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
				
		//IF  dshipping method is not local pickup then @return;
		if ( !isset($shipping_method ) ) {
			return;
		}
		if ( isset($shipping_method ) && 'local_pickup' != $shipping_method ) {
			return;
		}
		
		$settings = $this->wclp_general_setting_fields_func();		
		$addional_content = get_option('wclp_processing_additional_content', $settings['wclp_processing_additional_content']['default']);
		echo '<p>' . wp_kses_post(stripslashes($addional_content), 'advanced-local-pickup-for-woocommerce') . '</p>';
	}
	
	/**
	 *
	 * Get times as option-list.
	 *
	 * @return string List of times
	 */
	public function get_times( $default = '19:00', $interval = '+30 minutes' ) {

		$output[] = array();
		unset($output[0]);
		$current = strtotime( '00:00' );
		$end = strtotime( '23:59' );

		while ( $current <= $end ) {
			$time = gmdate( 'H:i', $current );
			$sel = ( $time == $default ) ? ' selected' : '';
		
			$output[gmdate( 'h:i A', $current )] .= gmdate( 'h:i A', $current );
			$current = strtotime( $interval, $current );
		}

		return $output;
	}
	
	public function wclp_update_state_dropdown_fun() {
		
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'alp-ajax-nonce' ) ) {
			die();
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wc_clean() sanitizes the value; Plugin Check does not recognize it as a sanitizer.
		$country = isset($_POST['country']) ? wc_clean( wp_unslash( $_POST['country'] ) ) : '';
		$countries_obj   = new WC_Countries();
		$default_county_states = $countries_obj->get_states( $country );
		if (empty($default_county_states)) {
			echo json_encode( array('state' => 'empty') );
			die();
		} else {
			ob_start();
			?>
			<option value="<?php echo esc_html($key); ?>"><?php esc_html_e('Select', 'advanced-local-pickup-for-woocommerce'); ?></option>
			<?php 
			foreach ( (array) $default_county_states as $key => $val ) { 
				
				?>
				<option value="<?php echo esc_html($key); ?>"><?php echo esc_html($val); ?></option>
				<?php 
			}
			$html = ob_get_clean();			
			echo json_encode( array('state' => $html) );
			die();
		}	
		echo json_encode( array('state' => 'empty') );
		die();		
	}
	
	public function wclp_update_work_hours_list_fun() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'alp-ajax-nonce' ) ) {
			die();
		}

		$location_id = isset($_POST['id']) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$location    = $this->get_data_byid($location_id);

		// Honour the chosen time format (12/24h) when the user toggles the dropdown.
		$posted_fmt = isset($_POST['hour_format']) ? sanitize_text_field( wp_unslash( $_POST['hour_format'] ) ) : '';
		if ( '12' === (string) $posted_fmt || '24' === (string) $posted_fmt ) {
			$wclp_store_time_format = $posted_fmt;
		} else {
			$wclp_store_time_format = isset($location->store_time_format) ? $location->store_time_format : '24';
		}

		$all_days = array(
			'sunday'    => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday'    => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday'   => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday'  => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday'    => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday'  => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$days = array_slice($all_days, (int) get_option('start_of_week'));
		foreach ($all_days as $k => $v) {
			$days[$k] = $v;
		}

		$day_short = array(
			'sunday'    => esc_html__( 'Sun', 'advanced-local-pickup-for-woocommerce' ),
			'monday'    => esc_html__( 'Mon', 'advanced-local-pickup-for-woocommerce' ),
			'tuesday'   => esc_html__( 'Tue', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wed', 'advanced-local-pickup-for-woocommerce' ),
			'thursday'  => esc_html__( 'Thu', 'advanced-local-pickup-for-woocommerce' ),
			'friday'    => esc_html__( 'Fri', 'advanced-local-pickup-for-woocommerce' ),
			'saturday'  => esc_html__( 'Sat', 'advanced-local-pickup-for-woocommerce' ),
		);

		$send_time_array = array();
		for ( $hour = 0; $hour < 24; $hour++ ) {
			for ( $min = 0; $min < 60; $min += 30 ) {
				$tt = gmdate( 'H:i', strtotime( "$hour:$min" ) );
				$send_time_array[ $tt ] = $tt;
			}
		}

		$fmt_time = function ( $hhmm ) use ( $wclp_store_time_format ) {
			if ( '12' === (string) $wclp_store_time_format ) {
				return gmdate( 'g:ia', strtotime( $hhmm ) );
			}
			return $hhmm;
		};

		$multi_checkbox_data = isset( $location->store_days ) ? @unserialize( $location->store_days ) : array();

		ob_start();
		?>
		<div class="pickup_hours_div business alp-workhours-grid alp-workhours-grid--cards">
			<?php foreach ( (array) $days as $key => $val ) :
				$bh_checked  = ( isset( $multi_checkbox_data[ $key ]['checked'] ) && 1 == $multi_checkbox_data[ $key ]['checked'] );
				$hour1_start = isset( $multi_checkbox_data[ $key ]['wclp_store_hour'] )     ? $multi_checkbox_data[ $key ]['wclp_store_hour']     : '';
				$hour1_end   = isset( $multi_checkbox_data[ $key ]['wclp_store_hour_end'] ) ? $multi_checkbox_data[ $key ]['wclp_store_hour_end'] : '';
				$has_hour1   = ( '' !== $hour1_start && '' !== $hour1_end );
				$day_label   = isset( $day_short[ $key ] ) ? $day_short[ $key ] : ucfirst( $key );
				$date_long   = isset( $days[ $key ] ) ? $days[ $key ] : ucfirst( $key );
				?>
				<div class="wplp_pickup_duration alp-workhours-card<?php echo $bh_checked ? ' is-on' : ''; ?>">
					<div class="alp-workhours-card__head">
						<span class="alp-workhours-card__day"><?php echo esc_html( $day_label ); ?></span>
						<label class="zui-toggle alp-workhours-card__toggle" for="<?php echo esc_attr( $key ); ?>">
							<input type="hidden" name="wclp_store_days[<?php echo esc_attr( $key ); ?>][checked]" value="0">
							<input type="checkbox" id="<?php echo esc_attr( $key ); ?>" name="wclp_store_days[<?php echo esc_attr( $key ); ?>][checked]" class="zui-toggle__input pickup_days_checkbox" <?php checked( $bh_checked ); ?> value="1">
							<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
						</label>
					</div>
					<fieldset class="wclp_pickup_time_fieldset alp-workhours-card__hours">
						<span class="hours hours-time alp-workhours-card__pill<?php echo $has_hour1 ? '' : ' alp-workhours-card__pill--empty'; ?>">
							<?php if ( $has_hour1 ) : ?>
								<?php echo esc_html( $fmt_time( $hour1_start ) ); ?> &ndash; <?php echo esc_html( $fmt_time( $hour1_end ) ); ?>
							<?php else : ?>
								<span class="dashicons dashicons-plus"></span>
							<?php endif; ?>
						</span>
						<?php do_action( 'wclp_split_hours_hook', $key, $wclp_store_time_format, $location, 'hours-time' ); ?>

						<div id="bh_hours_popup_html_<?php echo esc_attr( $key ); ?>" class="popupwrapper alp-hours-popup alp-hours-popup--bh" style="display:none;">
							<div class="popuprow alp-hours-popup__card">
								<div class="alp-hours-popup__head">
									<div class="alp-hours-popup__heading">
										<h4 class="alp-hours-popup__title"><?php esc_html_e( 'Business Hours', 'advanced-local-pickup-for-woocommerce' ); ?></h4>
										<?php /* translators: %s: day name (e.g. Monday) */ ?>
										<p class="alp-hours-popup__sub"><?php printf( esc_html__( 'Configure intervals for %s', 'advanced-local-pickup-for-woocommerce' ), esc_html( $date_long ) ); ?></p>
									</div>
									<button type="button" class="alp-hours-popup__close popup_close_icon dashicons dashicons-no-alt" aria-label="<?php esc_attr_e( 'Close', 'advanced-local-pickup-for-woocommerce' ); ?>"></button>
								</div>
								<div class="alp-hours-popup__col-labels">
									<span class="alp-hours-popup__col-label"><?php esc_html_e( 'FROM', 'advanced-local-pickup-for-woocommerce' ); ?></span>
									<span></span>
									<span class="alp-hours-popup__col-label"><?php esc_html_e( 'TO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
									<span></span>
								</div>
								<div class="alp-hours-popup__body">
									<span class="morning-time alp-hours-popup__row">
										<span class="zui-select-wrap alp-hours-popup__field">
											<select class="zui-select select <?php echo esc_attr( $key ); ?> wclp_pickup_time_select start" name="wclp_store_days[<?php echo esc_attr( $key ); ?>][wclp_store_hour]">
												<option value=""><?php esc_html_e( 'Select', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												<?php foreach ( $send_time_array as $opt_key => $opt_val ) : ?>
													<option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $hour1_start, $opt_key ); ?>><?php echo esc_html( $fmt_time( $opt_val ) ); ?></option>
												<?php endforeach; ?>
											</select>
											<span class="zui-select-chevron"><svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg></span>
										</span>
										<span class="alp-hours-popup__sep"><?php esc_html_e( 'to', 'advanced-local-pickup-for-woocommerce' ); ?></span>
										<span class="zui-select-wrap alp-hours-popup__field">
											<select class="zui-select select <?php echo esc_attr( $key ); ?> wclp_pickup_time_select end" name="wclp_store_days[<?php echo esc_attr( $key ); ?>][wclp_store_hour_end]">
												<option value=""><?php esc_html_e( 'Select', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												<?php foreach ( $send_time_array as $opt_key => $opt_val ) : ?>
													<option value="<?php echo esc_attr( $opt_key ); ?>" <?php selected( $hour1_end, $opt_key ); ?>><?php echo esc_html( $fmt_time( $opt_val ) ); ?></option>
												<?php endforeach; ?>
											</select>
											<span class="zui-select-chevron"><svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg></span>
										</span>
										<span class="dashicons dashicons-trash alp-hours-popup__trash"></span>
									</span>
									<?php do_action( 'wclp_multi_hours_hook', $key, $wclp_store_time_format, $location, $send_time_array ); ?>
								</div>
								<?php do_action( 'wclp_apply_mltiple_popup_hook', $days, $key ); ?>
								<div class="alp-hours-popup__actions">
									<button type="button" class="wclp-apply alp-hours-popup__btn" value="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Apply & close', 'advanced-local-pickup-for-woocommerce' ); ?></button>
									<?php do_action( 'wclp_apply_mltiple_on_days_hook' ); ?>
								</div>
							</div>
							<div class="popupclose"></div>
						</div>
					</fieldset>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		$html = ob_get_clean();
		echo json_encode( array('pickup_hours_div' => $html) );
		die();
	}
	
	public function wclp_update_edit_location_form_fun() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'alp-ajax-nonce' ) ) {
			die();
		}

		$location_id = isset($_POST['id']) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$location = $this->get_data_byid($location_id);
		
		ob_start();
		include('views/wclp-edit-location-form.php');
		$html = ob_get_clean();			
		echo json_encode( array('edit_location_form' => $html) );
		die();
	}
	
	public function wclp_apply_work_hours_fun() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'alp-ajax-nonce' ) ) {
			wp_send_json( array( 'success' => 'fail', 'msg' => 'Nonce verification failed.' ) );
		}

		global $wpdb;
		$location_id = isset( $_POST['id'] )   ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wc_clean() sanitizes each element of the array; Plugin Check does not recognize it.
		$days        = isset( $_POST['days'] ) ? wc_clean( wp_unslash( $_POST['days'] ) )         : array();

		// Read posted hour values up-front and trim. Empty values must NOT be
		// written back to the row — that was wiping saved hours when the JS
		// failed to read the popup selects.
		$wclp_store_hour      = isset( $_POST['wclp_store_hour'] )      ? sanitize_text_field( wp_unslash( $_POST['wclp_store_hour'] ) )      : '';
		$wclp_store_hour_end  = isset( $_POST['wclp_store_hour_end'] )  ? sanitize_text_field( wp_unslash( $_POST['wclp_store_hour_end'] ) )  : '';
		$wclp_store_hour2     = isset( $_POST['wclp_store_hour2'] )     ? sanitize_text_field( wp_unslash( $_POST['wclp_store_hour2'] ) )     : '';
		$wclp_store_hour_end2 = isset( $_POST['wclp_store_hour_end_2'] ) ? sanitize_text_field( wp_unslash( $_POST['wclp_store_hour_end_2'] ) ) : ( isset( $_POST['wclp_store_hour_end2'] ) ? sanitize_text_field( wp_unslash( $_POST['wclp_store_hour_end2'] ) ) : '' );

		if ( '' === $wclp_store_hour || '' === $wclp_store_hour_end || empty( $days ) ) {
			wp_send_json( array( 'success' => 'fail', 'msg' => 'Missing From / To time — nothing was saved.' ) );
		}

		$location = $this->get_data_byid( $location_id );
		if ( ! $location ) {
			wp_send_json( array( 'success' => 'fail', 'msg' => 'Location not found.' ) );
		}

		// Defensive: if the existing store_days couldn't be unserialized (older
		// rows can have a partial / corrupted blob), start from an empty array
		// rather than from `false` — which would silently turn into a fresh
		// array on first key write and wipe every other day.
		$store_days = isset( $location->store_days ) ? @unserialize( $location->store_days ) : array();
		if ( ! is_array( $store_days ) ) { $store_days = array(); }

		foreach ( (array) $days as $key ) {
			$key = sanitize_key( $key );
			if ( ! $key ) { continue; }
			if ( ! isset( $store_days[ $key ] ) || ! is_array( $store_days[ $key ] ) ) {
				$store_days[ $key ] = array();
			}
			$store_days[ $key ]['checked']             = '1';
			$store_days[ $key ]['wclp_store_hour']     = $wclp_store_hour;
			$store_days[ $key ]['wclp_store_hour_end'] = $wclp_store_hour_end;
			// Only touch the second interval keys when the second interval is
			// actually present — otherwise we'd clobber a previously-saved
			// second interval on a row that wasn't being edited.
			if ( '' !== $wclp_store_hour2 && '' !== $wclp_store_hour_end2 ) {
				$store_days[ $key ]['wclp_store_hour2']     = $wclp_store_hour2;
				$store_days[ $key ]['wclp_store_hour_end2'] = $wclp_store_hour_end2;
			}
		}

		$wpdb->update( $this->table, array( 'store_days' => serialize( $store_days ) ), array( 'id' => wc_clean( $location_id ) ) );

		// Render the grid HTML from the just-saved row and return it.
		$this->wclp_update_work_hours_list_fun();
	}
	
	public function wclp_general_setting_fields_func() {		
		$show_pickup_instraction_option = array( 
			'display_in_processing_email' => esc_html__( 'Processing order email', 'advanced-local-pickup-for-woocommerce' ),
			'display_in_order_received_page' => esc_html__( 'Order received page', 'advanced-local-pickup-for-woocommerce' ),
			'display_in_order_details_page' => esc_html__( 'My Account (orders history)', 'advanced-local-pickup-for-woocommerce' ),			
		);
		$settings = array(						
			'wclp_show_pickup_instruction' => array(
				'id'       => 'wclp_show_pickup_instruction',
				'css'      => 'min-width:50px;',
				'default'  => '',
				'show'	   => true,
				'type'     => 'multiple_checkbox',
				'options'  => $show_pickup_instraction_option,
				'class'	   => '',
				'desc_tip' => '',
			),
			'wclp_processing_additional_content' => array(
				'title'    => esc_html__( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ),
				'tooltip'  => esc_html__( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ),
				'id'       => 'wclp_processing_additional_content',
				'css'      => 'min-width:50px;',
				'default'  => esc_html__( 'You will receive an email when your order is ready for pickup.', 'advanced-local-pickup-for-woocommerce' ),
				'placeholder' => esc_html__( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ),
				'show'	   => true,
				'type'     => 'textarea',
				'class'	   => 'additional_textarea',
				'desc_tip' => '',
			),
		);
		$settings = apply_filters( 'alp_display_location_option_data_array', $settings );
		return $settings;
		
	}
	
	/*
	* Get html of fields
	*/
	public function get_html2( $arrays ) {
		$checked = '';
		foreach ( (array) $arrays as $id => $array ) {
			if ($array['show']) {	
				
				?>
				<?php if ('dropdown' == $array['type']) { ?>               	
					<tr valign="top" class="html2_title_row <?php echo esc_html($array['class']); ?>">
						<?php if ( !empty($array['title']) && isset($array['title'])) { ?>										
						<th><span class="row-label">
							<label for=""><?php echo esc_html($array['title']); ?><?php echo ( isset($array['title_link']) ) ? esc_html($array['title_link']) : ''; ?>
								<?php if ( isset($array['tooltip']) ) { ?>
									<span class="woocommerce-help-tip tipTip" title="<?php echo esc_html($array['tooltip']); ?>"></span>
								<?php } ?>
							</label>
							<?php if ( isset($array['desc_tip']) ) { ?>
									<p class="description"><?php echo esc_html($array['desc_tip']); ?></p>
								<?php } ?>
							<?php if ( isset( $array['type'] ) && 'dropdown' == $array['type'] ) { ?>
								<?php
								if ( isset($array['multiple']) ) {
									$multiple = 'multiple';
									$field_id = $array['multiple'];
								} else {
									$multiple = '';
									$field_id = $id;
								}
								?>
								<fieldset>
									<select class="select select2" id="<?php echo esc_html($field_id); ?>" name="<?php echo esc_html($id); ?>" <?php echo esc_html($multiple); ?> style="width:150px;"> 
									<?php 
									foreach ( (array) $array['options'] as $key => $val ) { 
										$selected = '';
										if ( isset($array['multiple']) ) {
											if (in_array($key, (array) $this->data->$field_id )) {
												$selected = 'selected';
											}
										} else {
											if ( get_option($array['id']) == (string) $key ) {
												$selected = 'selected';
											}
										}
										?>
											<option value="<?php echo esc_html($key); ?>" <?php echo esc_html($selected); ?> ><?php echo esc_html($val); ?></option>
										<?php } ?><p class="description"><?php echo ( isset($array['desc']) ) ? esc_html($array['desc']) : ''; ?></p>
									</select>
								</fieldset>
							<?php } ?></span>
						</th>
						<?php } ?>
					</tr>
					<?php 
				}
				if ( !empty($array['title']) && 'textarea' == $array['type'] ) { 
					?>
					<tr valign="top" class="html2_title_row <?php echo esc_html($array['class']); ?>">
						<th><span class="row-label">
							<label for=""><?php echo esc_html($array['title']); ?><?php echo ( isset($array['title_link']) ) ? esc_html($array['title_link']) : ''; ?>
								<?php if ( isset($array['tooltip']) ) { ?>
									<span class="woocommerce-help-tip tipTip" title="<?php echo esc_html($array['tooltip']); ?>"></span>
								<?php } ?>
							</label>
							<?php if ( isset($array['desc_tip']) ) { ?>
									<p class="description"><?php echo esc_html($array['desc_tip']); ?></p>
								<?php } ?>
							</span>
						</th>
					</tr>
					<tr valign="top" class="html2_title_row <?php echo esc_html($array['class']); ?>">
						<td class="forminp"  <?php echo ( 'desc' == $array['type'] ) ? 'colspan=2' : ''; ?>>
							<fieldset>
								<textarea rows="4" cols="20" class="input-text regular-input" type="textarea" name="<?php echo esc_html($id); ?>" id="<?php echo esc_html($id); ?>" style="" placeholder="<?php echo ( !empty($array['placeholder']) ) ? esc_html($array['placeholder']) : ''; ?>"><?php echo esc_html(stripslashes(get_option($array['id'], $array['default']))); ?></textarea>
							</fieldset>
						</td>
					</tr>
				<?php } ?>
				<?php if ('dropdown' != $array['type'] && 'textarea' != $array['type']) { ?>
					<tr class="<?php echo esc_html($array['class']); ?>">
						<td class="forminp" <?php echo ( 'desc' == $array['type'] ) ? 'colspan=2' : ''; ?>>
							<?php 
							if ( 'checkbox' == $array['type'] ) {								
								if (get_option($array['id'])) {
									$checked = 'checked';
								} else {
									$checked = '';
								}
								if (isset($array['disabled']) && true == $array['disabled']) {
									$disabled = 'disabled';
									$checked = '';
								} else {
									$disabled = '';
								}							
								?>
							<?php if ('toggle' == $array['class']) { ?>
							<span class="mdl-list__item-secondary-action">
								<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="<?php echo esc_html($id); ?>">
									<input type="hidden" name="<?php echo esc_html($id); ?>" value="0"/>
									<input type="checkbox" id="<?php echo esc_html($id); ?>" name="<?php echo esc_html($id); ?>" class="mdl-switch__input" <?php echo esc_html($checked); ?> value="1" <?php echo esc_html($disabled); ?>/>
								</label><p class="description"><?php echo ( isset($array['desc']) )? esc_html($array['desc']) : ''; ?></p>
							</span>
							<?php } else { ?>
								<span class="checkbox">
									<label class="checkbx-label" for="<?php echo esc_html($id); ?>">
										<input type="hidden" name="<?php echo esc_html($id); ?>" value="0"/>
										<input type="checkbox" id="<?php echo esc_html($id); ?>" name="<?php echo esc_html($id); ?>" class="checkbox-input" <?php echo esc_html($checked); ?> value="1" <?php echo esc_html($disabled); ?>/>
									</label><p class="description"><?php echo ( isset($array['desc']) ) ? esc_html($array['desc']) : ''; ?></p>
								</span>
							<?php } ?>
							<?php } elseif ( 'textarea' == $array['type'] ) { ?>
								<fieldset>
									<textarea rows="4" cols="20" class="input-text regular-input" type="textarea" name="<?php echo esc_html($id); ?>" id="<?php echo esc_html($id); ?>" style="" placeholder="<?php echo ( !empty($array['placeholder']) ) ? esc_html($array['placeholder']) : ''; ?>"><?php echo esc_html(stripslashes(get_option($array['id'], $array['default']))); ?></textarea>
								</fieldset>
							<?php } elseif ( 'multiple_checkbox' == $array['type'] ) { ?>
								<?php
								$op = 1;	
								foreach ( (array) $array['options'] as $key => $val ) {									
									$multi_checkbox_data = get_option($id);
									if (isset($multi_checkbox_data[$key]) && 1 == $multi_checkbox_data[$key]) {
										$checked = 'checked';
									} else {
										$checked = '';
									} 
									?>
									<div class="wplp_multiple <?php echo esc_html($array['class']); ?>">
										<span class="wplp_multiple_checkbox">
											<label class="" for="<?php echo esc_html($key); ?>">
												<input type="hidden" name="<?php echo esc_html($id); ?>[<?php echo esc_html($key); ?>]" value="0"/>
												<input type="checkbox" id="<?php echo esc_html($key); ?>" name="<?php echo esc_html($id); ?>[<?php echo esc_html($key); ?>]" class=""  <?php echo esc_html($checked); ?> value="1"/>
												<span class="multiple_label"><?php echo esc_html($val); ?></span>	
												</br>
											</label>																		
										</span>
									</div>								
								<?php 
								} 
							} else if ('text' == $array['type']) { 
								?>
								<fieldset>
									<input class="input-text regular-input " type="text" name="<?php echo esc_html($id); ?>" id="<?php echo esc_html($id); ?>" style="" value="<?php echo esc_html(get_option($array['id'], get_option($array['default']))); ?>" placeholder="<?php echo ( !empty($array['placeholder']) ) ? esc_html($array['placeholder']) : ''; ?>">
								</fieldset>
							<?php } ?>
							
						</td>
					</tr>
				<?php } ?>
			<?php 
			} 
		} 
	}
	
	/*
	* Change style of available for pickup and picked up order label
	*/	
	public function footer_function() {
		if ( !is_plugin_active( 'woocommerce-order-status-manager/woocommerce-order-status-manager.php' ) ) {
			$rfp_bg_color = get_option('wclp_ready_pickup_status_label_color', '#365EA6');
			$rfp_color = get_option('wclp_ready_pickup_status_label_font_color', '#fff');
			
			$pu_bg_color = get_option('wclp_pickup_status_label_color', '#f1a451');
			$pu_color = get_option('wclp_pickup_status_label_font_color', '#fff');						
			?>
			<style>
			.order-status.status-ready-pickup,.order-status-table .order-label.wc-ready-pickup{
				background: <?php echo esc_html($rfp_bg_color); ?>;
				color: <?php echo esc_html($rfp_color); ?>;
			}						
			.order-status.status-pickup,.order-status-table .order-label.wc-pickup{
				background: <?php echo esc_html($pu_bg_color); ?>;
				color: <?php echo esc_html($pu_color); ?>;
			}	
			</style>
			<?php
		}
	}
	
	/*
	* Add action button in order list to change order status from processing to ready for pickup and ready for pickup to Picked Up
	*/
	public function add_local_pickup_order_status_actions_button( $actions, $order ) {			
		
		// Iterating through order shipping items
		foreach ( $order->get_items( 'shipping' ) as $item_id => $shipping_item_obj ) {			
			$shipping_method = $shipping_item_obj->get_method_id();						
		}
				
		//IF  dshipping method is not local pickup then @return;
		if ( !isset($shipping_method ) ) {
			return $actions;
		}
		if ( isset($shipping_method ) && 'local_pickup' != $shipping_method ) {
			return $actions;
		}
		
		wp_enqueue_style( 'alp-order', wc_local_pickup()->plugin_dir_url(__FILE__) . 'assets/css/order.css', array(), wc_local_pickup()->version );
		
		$ready_for_pickup = get_option( 'wclp_status_ready_pickup', 0);
		if (true == $ready_for_pickup) {
			if ( $order->has_status( array( 'processing' ) ) ) {
				// Get Order ID (compatibility all WC versions)
				$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				// Set the action button
				$actions['ready_for_pickup'] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=ready-pickup&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
					'name'      => esc_html__( 'Mark order as ready for pickup', 'advanced-local-pickup-for-woocommerce' ),
					'action'    => 'ready_for_pickup_icon', // keep "view" class for a clean button CSS
				);
				unset($actions['complete']);
			}
		}
		$picked = get_option( 'wclp_status_picked_up', 0);
		if (true == $picked) {
			if ( $order->has_status( array( 'ready-pickup' ) ) ) {
				// Get Order ID (compatibility all WC versions)
				$order_id = method_exists( $order, 'get_id' ) ? $order->get_id() : $order->id;
				// Set the action button
				$actions['pickup'] = array(
					'url'       => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=pickup&order_id=' . $order_id ), 'woocommerce-mark-order-status' ),
					'name'      => esc_html__( 'Mark order as picked up', 'advanced-local-pickup-for-woocommerce' ),
					'action'    => 'picked_up_icon', // keep "view" class for a clean button CSS
				);
			}
		} else {
			if ( $order->has_status( array( 'ready-pickup' ) ) ) {
				$actions['complete'] = array(
					'url'    => wp_nonce_url( admin_url( 'admin-ajax.php?action=woocommerce_mark_order_status&status=completed&order_id=' . $order->get_id() ), 'woocommerce-mark-order-status' ),
					'name'   => esc_html__( 'Complete', 'advanced-local-pickup-for-woocommerce' ),
					'action' => 'complete',
				);
			}
		}			
				
		return $actions;
	}	
	
	/*
	* Add order again button for delivered order status	
	*/
	public function add_reorder_button_pickup( $statuses ) {
		$picked = get_option( 'wclp_status_picked_up', 0);
		if (true == $picked) {
			$statuses[] = 'pickup';
		}
		return $statuses;	
	}
	
	public function get_option_value_from_array( $array, $key, $default_value ) {		
		$array_data = get_option($array);	
		$value = '';
		
		if (isset($array_data[$key])) {
			$value = $array_data[$key];	
		}					
		
		if ('' == $value) {
			$value = $default_value;
		}
		return $value;
	}
	
	/*
	* Return pickup data by order id.
	*/
	public function get_pickup_data( $order_id ) {
		
		if (empty($order_id)) {
			return array();
		}
		
		$locations = $this->get_data();
		
		$location_id = get_option('location_defualt', min($locations)->id);
		
		$location = $this->get_data_byid($location_id);
		$store_address = $this->get_store_address_by_id($location_id);
		$store_hours = $this->get_store_hours_by_id($location_id);
					
		$location_data[$location_id] = array(
			'pickup_location_name' => isset($location->store_name) ? $location->store_name : '',
			'pickup_location_address' => $store_address,
			'pickup_location_hours' => $store_hours,
		);
		
		return $location_data;
	}
	
	/*
	* Return pickup store address by id.
	*/
	public function get_store_address_by_id( $location_id ) {
		
		if ( empty( $location_id ) ) {
			return '';
		}
		
		$location = $this->get_data_byid($location_id);
		$country_code = isset($location) ? $location->store_country : get_option('woocommerce_default_country');
		$split_country = explode( ':', $country_code );
		$store_country = isset($split_country[0]) ? $split_country[0] : '';
		$store_state   = isset($split_country[1]) ? $split_country[1] . ' ' : '';
		
		$store_address = isset($location->store_address) ? $location->store_address . ' ' : '';
		$store_address_2 = isset($location->store_address_2) ? $location->store_address_2 . ' ' : '';
		$store_city = isset($location->store_city) ? $location->store_city . ' ' : '';
		$store_postcode = isset($location->store_postcode) ? $location->store_postcode . ' ' : '';
		$store_country = isset( WC()->countries->countries[$store_country] ) ? WC()->countries->countries[$store_country] : '';
		
		$address = $store_address . $store_address_2 . $store_city . $store_state . $store_postcode . $store_country ;
		
		return $address;
	}
	
	/*
	* Return pickup store hours by id.
	*/
	public function get_store_hours_by_id( $location_id ) {
		
		if ( empty( $location_id ) ) {
			return '';
		}
		
		$location = $this->get_data_byid($location_id);
		$store_days = isset($location->store_days) ? unserialize($location->store_days) : array();
		$all_days = array(
			'sunday' => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday' => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce'),
			'tuesday' => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday' => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday' => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday' => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$w_day = array_slice($all_days, get_option('start_of_week'));
		foreach ($all_days as $key=>$val) {
			$w_day[$key] = $val;
		}
		foreach ($store_days as $key => $val) {
			if ($w_day[$key]) {
				$w_day[$key] = $val;
			}
		}
				
		$wclp_default_time_format = isset($location) ? $location->store_time_format : '24';
		if ('12' == $wclp_default_time_format) {
			foreach ($w_day as $key=>$val) {	
				if (isset($val['wclp_store_hour'])) {
					$last_digit = explode(':', $val['wclp_store_hour']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					} else {
						$val['wclp_store_hour'] = gmdate('g:ia', strtotime($val['wclp_store_hour']));
					}
				}
				if (isset($val['wclp_store_hour_end'])) {
					$last_digit = explode(':', $val['wclp_store_hour_end']);
					if ('00' == end($last_digit)) {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					} else {
						$val['wclp_store_hour_end'] = gmdate('g:ia', strtotime($val['wclp_store_hour_end']));
					}
				}
				$w_day[$key] = $val;				
			}	
		}
		
		if (!empty($w_day)) { 	
		$n = 0;
		$new_array = [];
		$previousValue = [];
		
			foreach ($w_day as $day=>$value) {				
				if (isset($value['checked']) && 1 == $value['checked']) {																	
					if ($value != $previousValue) {
						$n++;
					}
					$new_array[$n][$day] = $value;					
					$previousValue = $value;
				} else {
					$n++;
					$new_array[$n][$day] = '';	
					$previousValue = '';
				}							
			}
		}
		$pickup_hour = '';
		foreach ($new_array as $key => $data) {
			
			if (1 == count($data)) {	
									
				if ( isset(reset($data)['wclp_store_hour']) && '' != reset($data)['wclp_store_hour'] && isset(reset($data)['wclp_store_hour_end']) && '' != reset($data)['wclp_store_hour_end'] ) {
					reset($data);
					$pickup_hour .= esc_html(ucfirst(key($data)), 'advanced-local-pickup-for-woocommerce') . ' : ' . esc_html(reset($data)['wclp_store_hour']) . ' - ' . esc_html(reset($data)['wclp_store_hour_end']);
				}
			}
			if (2 == count($data)) {
				
				if ( isset(reset($data)['wclp_store_hour']) && '' != reset($data)['wclp_store_hour'] && isset(reset($data)['wclp_store_hour_end']) && '' != reset($data)['wclp_store_hour_end'] ) {
					reset($data);
					$array_key_first = key($data);
					end($data);
					$array_key_last = key($data);
					$pickup_hour .= esc_html(ucfirst($array_key_first), 'advanced-local-pickup-for-woocommerce') . ' - ' ; 
					$pickup_hour .= esc_html(ucfirst($array_key_last) . ' ', 'advanced-local-pickup-for-woocommerce') . ' : ' . esc_html(reset($data)['wclp_store_hour']) . ' - ' . esc_html(reset($data)['wclp_store_hour_end']);
				}
			
			}
			if (count($data) > 2) { 
				if ( isset(reset($data)['wclp_store_hour']) && '' != reset($data)['wclp_store_hour'] && isset(reset($data)['wclp_store_hour_end']) && '' != reset($data)['wclp_store_hour_end'] ) {
					reset($data);
					$array_key_first = key($data);
					end($data);
					$array_key_last = key($data);
					$pickup_hour .= esc_html(ucfirst($array_key_first), 'advanced-local-pickup-for-woocommerce') . ' ' . esc_html(' To', 'advanced-local-pickup-for-woocommerce') . ' ' . esc_html(ucfirst($array_key_last), 'advanced-local-pickup-for-woocommerce') . ' : ' . esc_html(reset($data)['wclp_store_hour']) . ' - ' . esc_html(reset($data)['wclp_store_hour_end']); 
						
				}		
			
			}	
		}
		
		return $pickup_hour;
	}

	/**
	 * AJAX: persist Telemetry & Communication Preferences toggles.
	 *
	 * Writes both options directly and returns a JSON success envelope so
	 * the frontend can show a snackbar regardless of whether zero, one,
	 * or two toggles are enabled. Bypasses the shared zorem-tracking
	 * handler's early `die()` on the all-zero branch.
	 *
	 * @since 1.8.0
	 */
	public function telemetry_save() {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_send_json_error( array( 'message' => __( 'Permission denied.', 'advanced-local-pickup-for-woocommerce' ) ), 403 );
		}

		check_ajax_referer( 'advanced_local_pickup_for_woocommerce_usage_data_form', 'advanced_local_pickup_for_woocommerce_usage_data_form_nonce' );

		$slug        = 'advanced_local_pickup_for_woocommerce';
		$optin_email = isset( $_POST[ $slug . '_optin_email_notification' ] ) ? absint( wp_unslash( $_POST[ $slug . '_optin_email_notification' ] ) ) : 0;
		$enable_env  = isset( $_POST[ $slug . '_enable_usage_data' ] )        ? absint( wp_unslash( $_POST[ $slug . '_enable_usage_data' ] ) )        : 0;

		update_option( $slug . '_optin_email_notification', $optin_email );
		update_option( $slug . '_enable_usage_data', $enable_env );
		update_option( $slug . '_usage_data_selector', true );

		// Keep the tracker cron in sync with the toggle state — mirrors
		// WC_Trackers::set_unset_usage_data_cron() so we don't send when
		// both are off.
		if ( 0 === $optin_email && 0 === $enable_env ) {
			wp_clear_scheduled_hook( 'zorem_usage_data_' . $slug );
		} elseif ( ! wp_next_scheduled( 'zorem_usage_data_' . $slug ) ) {
			wp_schedule_event( time() + 10, 'weekly', 'zorem_usage_data_' . $slug );
		}

		wp_send_json_success( array( 'message' => __( 'Data saved successfully', 'advanced-local-pickup-for-woocommerce' ) ) );
	}

}
