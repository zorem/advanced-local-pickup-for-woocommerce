<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- View template file; variable and hook names are established by the surrounding controller and are part of the plugin API.
/**
 * Edit Pickup Location form — ZUI redesign for ALP Free.
 *
 * Mirrors ALP Pro's accordion structure but only with the sections supported
 * by the free tier:
 *   - Name & Special Instructions
 *   - Address
 *   - Business Hours
 * The remaining PRO sections render the accordion header + a locked-overlay
 * panel inviting users to upgrade.
 *
 * Preserves the existing field names so the legacy
 * `wclp_location_edit_form_update_callback` save handler keeps working.
 *
 * @package WooAdvancedLocalPickup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$location_id = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ) : '0'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$location    = wc_local_pickup()->admin->get_data_byid( $location_id );

// New location flow (id=0): build an empty stdClass so isset() checks below don't blow up.
if ( ! is_object( $location ) ) {
	$location = new stdClass();
}

$cancel_url = admin_url( 'admin.php?page=local_pickup&tab=locations' );

$store_name = isset( $location->store_name ) ? stripslashes( $location->store_name ) : '';

// Address subtitle.
$address_subtitle = '';
$country_setting  = isset( $location->store_country ) ? $location->store_country : get_option( 'woocommerce_default_country' );
if ( strstr( $country_setting, ':' ) ) {
	$country_setting = explode( ':', $country_setting );
	$country         = current( $country_setting );
	$state           = end( $country_setting );
} else {
	$country = $country_setting;
	$state   = '';
}
if ( ! empty( $location->store_address ) ) {
	$address_subtitle .= esc_html( $location->store_address );
}
if ( ! empty( $location->store_address_2 ) ) {
	$address_subtitle .= ', ' . esc_html( $location->store_address_2 );
}
if ( ! empty( $location->store_city ) ) {
	$address_subtitle .= ', ' . esc_html( $location->store_city );
}
if ( ! empty( $location->store_postcode ) ) {
	$address_subtitle .= ' - ' . esc_html( $location->store_postcode );
}
if ( $country && ! empty( $location->store_name ) && isset( WC()->countries->countries[ $country ] ) ) {
	$address_subtitle .= ', ' . esc_html( WC()->countries->countries[ $country ] ) . '.';
}

// PRO-locked sections to render below the editable ones.
$alp_upgrade_url = 'https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPFREE&utm_campaign=location-lock';

$alp_pro_sections = array(
	array(
		'title' => esc_html__( 'Pickup Appointments', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Configure reservable customer booking time slots.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Let customers book a specific pickup time slot when placing the order. Configure days, intervals, and capacity per slot.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Products', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Restrict this pickup location to specific products or categories.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Make this location available only for selected products, categories, or exclude specific items entirely.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Shipping Method', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Excluded shipping methods for this location.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Configure which WooCommerce shipping methods should be hidden when this pickup location is selected.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Payment Method', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Excluded payment gateways at checkout.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Restrict which payment gateways are available when customers choose this pickup location.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Price Adjustments', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'No markup or discounts on free tier.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Apply a percentage or flat discount, or a pickup fee, when this location is chosen.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Notifications', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Notify additional recipients about this location.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Send pickup notifications to additional store staff or location managers in addition to the customer.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Automations', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Status-change reminders and follow-ups.', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Schedule automatic reminders before pickup, mark orders Picked Up after X days, and similar workflows.', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title' => esc_html__( 'Curbside Pickup', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Status: Disabled', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Let customers notify the store when they have arrived for curbside delivery. Configure prompts, vehicle details, and arrival flow.', 'advanced-local-pickup-for-woocommerce' ),
	),
);
?>
<div class="zui-layout zui-layout--full alp-edit-layout">
<main class="zui-content zui-content--full">

	<form method="post" id="wclp_location_tab_form" class="alp-edit-form">

		<!-- TOP HEADER CARD: page title + Cancel + Save & close -->
		<div class="alp-edit-header">
			<div class="alp-edit-header__main">
				<span class="alp-edit-header__icon">
					<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
				</span>
				<div class="alp-edit-header__text">
					<h2 class="alp-edit-header__title">
						<?php echo ( $location_id && intval( $location_id ) > 0 ) ? esc_html__( 'Edit Pickup Location', 'advanced-local-pickup-for-woocommerce' ) : esc_html__( 'Add Pickup Location', 'advanced-local-pickup-for-woocommerce' ); ?>
					</h2>
					<p class="alp-edit-header__sub"><?php esc_html_e( 'Configure your pickup location name, address, and business hours.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				</div>
			</div>
			<div class="alp-edit-header__actions">
				<a class="alp-btn-link" href="<?php echo esc_url( $cancel_url ); ?>"><?php esc_html_e( 'Cancel', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				<button name="save" type="submit" class="zui-btn-primary zui-savebtn wclp-save btn_location_submit alp-btn-save-close" value="Save Changes">
					<span class="zui-savebtn__spinner" aria-hidden="true"></span>
					<span class="zui-savebtn__label"><?php esc_html_e( 'Save Changes', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				</button>
				<span class="alp_error_msg"></span>
				<?php wp_nonce_field( 'wclp_location_edit_form_action', 'wclp_location_edit_form_nonce_field' ); ?>
				<input type="hidden" name="action" value="wclp_location_edit_form_update">
				<input type="hidden" id="location_id" name="id" value="<?php echo esc_attr( $location_id ); ?>">
			</div>
		</div>

		<!-- NAME & SPECIAL INSTRUCTIONS -->
		<div class="accordion heading address-special alp-edit-acc">
			<label>
				<span class="alp-edit-acc__title"><?php esc_html_e( 'Name & Type', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				<span class="alp-edit-acc__chevron dashicons dashicons-arrow-down-alt2"></span>
				<br>
				<span class="heading-subtitle"><?php echo esc_html( $store_name ); ?></span>
			</label>
		</div>
		<div class="panel options address-special alp-edit-panel">

			<div class="zui-row alp-edit-row">
				<div class="zui-row__head">
					<span class="zui-row__label">
						<?php esc_html_e( 'Location Name', 'advanced-local-pickup-for-woocommerce' ); ?>
						<span class="alp-required" aria-hidden="true">*</span>
						<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The location name for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?>">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
							<span class="zui-tooltip__bubble"><?php esc_html_e( 'The location name for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
						</span>
					</span>
				</div>
				<div class="zui-row__control">
					<input type="text" class="zui-input regular-input" name="wclp_store_name" id="wclp_store_name" value="<?php echo isset( $location->store_name ) ? esc_attr( stripslashes( $location->store_name ) ) : ''; ?>" placeholder="<?php esc_attr_e( 'Demo Store', 'advanced-local-pickup-for-woocommerce' ); ?>">
					<span class="alp_error_msg" style="display:none;"><?php esc_html_e( 'You must add a location name and save to proceed.', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				</div>
			</div>

			<div class="zui-row alp-edit-row">
				<div class="zui-row__head">
					<span class="zui-row__label">
						<?php esc_html_e( 'Special Pickup instruction to your customers', 'advanced-local-pickup-for-woocommerce' ); ?>
						<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The special instruction for your store.', 'advanced-local-pickup-for-woocommerce' ); ?>">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
							<span class="zui-tooltip__bubble"><?php esc_html_e( 'The special instruction for your store.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
						</span>
					</span>
				</div>
				<div class="zui-row__control">
					<textarea rows="3" class="zui-input regular-input input-text" name="wclp_store_instruction" id="wclp_store_instruction" placeholder="<?php esc_attr_e( 'Special Pickup instruction to your customers', 'advanced-local-pickup-for-woocommerce' ); ?>"><?php echo isset( $location->store_instruction ) ? esc_textarea( stripslashes( $location->store_instruction ) ) : ''; ?></textarea>
				</div>
			</div>

		</div>

		<!-- ADDRESS -->
		<?php
		$country_value = isset( $location->store_country ) ? $location->store_country : get_option( 'woocommerce_default_country' );
		if ( strstr( $country_value, ':' ) ) {
			$country_value = explode( ':', $country_value );
			$country_sel   = current( $country_value );
			$state_sel     = end( $country_value );
		} else {
			$country_sel = $country_value;
			$state_sel   = '*';
		}
		?>
		<div class="accordion heading address alp-edit-acc">
			<label>
				<span class="alp-edit-acc__title"><?php esc_html_e( 'Address', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				<span class="alp-edit-acc__chevron dashicons dashicons-arrow-down-alt2"></span>
				<br>
				<span class="heading-subtitle"><?php echo esc_html( $address_subtitle ); ?></span>
			</label>
		</div>
		<div class="panel options address alp-edit-panel">
			<div class="alp-edit-fieldgrid">

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'Address line 1', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The street address for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_html_e( 'The street address for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<input type="text" class="zui-input regular-input" name="wclp_store_address" id="wclp_store_address" value="<?php echo isset( $location->store_address ) ? esc_attr( $location->store_address ) : ''; ?>" placeholder="<?php esc_attr_e( '123 Main Street', 'advanced-local-pickup-for-woocommerce' ); ?>">
					</div>
				</div>

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'Address line 2', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'An additional, optional address line for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_attr_e( 'An additional, optional address line for your business location.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<input type="text" class="zui-input regular-input" name="wclp_store_address_2" id="wclp_store_address_2" value="<?php echo isset( $location->store_address_2 ) ? esc_attr( $location->store_address_2 ) : ''; ?>" placeholder="<?php esc_attr_e( 'Suite A', 'advanced-local-pickup-for-woocommerce' ); ?>">
					</div>
				</div>

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'City', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The city in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_html_e( 'The city in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<input type="text" class="zui-input regular-input" name="wclp_store_city" id="wclp_store_city" value="<?php echo isset( $location->store_city ) ? esc_attr( $location->store_city ) : ''; ?>" placeholder="<?php esc_attr_e( 'New York', 'advanced-local-pickup-for-woocommerce' ); ?>">
					</div>
				</div>

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'Country / State', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The country and state or province, if any, in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_html_e( 'The country and state or province, if any, in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<div class="zui-select-wrap">
							<select name="wclp_default_country" id="wclp_default_country" class="zui-select select wc-enhanced-select" data-placeholder="<?php esc_attr_e( 'Choose a country / region&hellip;', 'advanced-local-pickup-for-woocommerce' ); ?>" aria-label="<?php esc_attr_e( 'Country / Region', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<?php WC()->countries->country_dropdown_options( $country_sel, $state_sel ); ?>
							</select>
							<span class="zui-select-chevron">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
							</span>
						</div>
					</div>
				</div>

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'Postcode / ZIP', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The postal code, if any, in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_html_e( 'The postal code, if any, in which your business is located.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<input type="text" class="zui-input regular-input" name="wclp_store_postcode" id="wclp_store_postcode" value="<?php echo isset( $location->store_postcode ) ? esc_attr( $location->store_postcode ) : ''; ?>" placeholder="<?php esc_attr_e( '10001', 'advanced-local-pickup-for-woocommerce' ); ?>">
					</div>
				</div>

				<div class="zui-row alp-edit-row">
					<div class="zui-row__head">
						<span class="zui-row__label">
							<?php esc_html_e( 'Phone number', 'advanced-local-pickup-for-woocommerce' ); ?>
							<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'The phone number for your business information.', 'advanced-local-pickup-for-woocommerce' ); ?>">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
								<span class="zui-tooltip__bubble"><?php esc_html_e( 'The phone number for your business information.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
							</span>
						</span>
					</div>
					<div class="zui-row__control">
						<input type="text" class="zui-input regular-input" name="wclp_store_phone" id="wclp_store_phone" value="<?php echo isset( $location->store_phone ) ? esc_attr( $location->store_phone ) : ''; ?>" placeholder="<?php esc_attr_e( '555-0199', 'advanced-local-pickup-for-woocommerce' ); ?>">
					</div>
				</div>

			</div>
		</div>

		<!-- BUSINESS HOURS -->
		<?php
		// Hours-subtitle computation — kept from the legacy free file so the
		// summary text under the Business Hours accordion stays accurate.
		$hours_subtitle = '';
		$store_days_serialized = isset( $location->store_days ) ? $location->store_days : '';
		$store_days_val        = $store_days_serialized ? @unserialize( $store_days_serialized ) : get_option( 'wclp_store_days' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize
		if ( ! empty( $store_days_val ) && ! empty( $location->store_name ) ) {
			$all_days_subtitle = array(
				'sunday'    => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
				'monday'    => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce' ),
				'tuesday'   => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
				'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
				'thursday'  => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
				'friday'    => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
				'saturday'  => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
			);
			$w_day_subtitle = array_slice( $all_days_subtitle, (int) get_option( 'start_of_week' ) );
			foreach ( $all_days_subtitle as $k => $v ) {
				$w_day_subtitle[ $k ] = $v;
			}
			foreach ( $store_days_val as $k => $v ) {
				if ( isset( $w_day_subtitle[ $k ] ) ) {
					$w_day_subtitle[ $k ] = $v;
				}
			}
			foreach ( $w_day_subtitle as $key => $val ) {
				if ( is_array( $val ) && isset( $val['checked'] ) && 1 === intval( $val['checked'] ) && ! empty( $val['wclp_store_hour'] ) && ! empty( $val['wclp_store_hour_end'] ) ) {
					$hours_subtitle .= substr( ucfirst( $key ), 0, 3 ) . ': ' . esc_html( $val['wclp_store_hour'] ) . '-' . esc_html( $val['wclp_store_hour_end'] ) . ' ';
				}
			}
		}
		?>
		<div class="accordion heading business-hours alp-edit-acc">
			<label>
				<span class="alp-edit-acc__title"><?php esc_html_e( 'Business Hours', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				<span class="alp-edit-acc__chevron dashicons dashicons-arrow-down-alt2"></span>
				<br>
				<span class="heading-subtitle"><?php echo esc_html( $hours_subtitle ); ?></span>
			</label>
		</div>
		<?php
		$current_time_fmt = isset( $location->store_time_format ) ? $location->store_time_format : '24';
		$store_days_data  = isset( $location->store_days ) ? @unserialize( $location->store_days ) : get_option( 'wclp_store_days' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize

		$all_days_bh = array(
			'sunday'    => esc_html__( 'Sunday', 'advanced-local-pickup-for-woocommerce' ),
			'monday'    => esc_html__( 'Monday', 'advanced-local-pickup-for-woocommerce' ),
			'tuesday'   => esc_html__( 'Tuesday', 'advanced-local-pickup-for-woocommerce' ),
			'wednesday' => esc_html__( 'Wednesday', 'advanced-local-pickup-for-woocommerce' ),
			'thursday'  => esc_html__( 'Thursday', 'advanced-local-pickup-for-woocommerce' ),
			'friday'    => esc_html__( 'Friday', 'advanced-local-pickup-for-woocommerce' ),
			'saturday'  => esc_html__( 'Saturday', 'advanced-local-pickup-for-woocommerce' ),
		);
		$days_bh = array_slice( $all_days_bh, (int) get_option( 'start_of_week' ) );
		foreach ( $all_days_bh as $k => $v ) {
			$days_bh[ $k ] = $v;
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
			for ( $min = 0; $min < 60; $min = $min + apply_filters( 'alp_work_hours_slots', '30' ) ) {
				$tt = gmdate( 'H:i', strtotime( "$hour:$min" ) );
				$send_time_array[ $tt ] = $tt;
			}
		}

		$fmt_time = function ( $hhmm ) use ( $current_time_fmt ) {
			if ( '12' === (string) $current_time_fmt ) {
				return gmdate( 'g:ia', strtotime( $hhmm ) );
			}
			return $hhmm;
		};
		?>
		<div class="panel options business-hours alp-edit-panel">

			<div class="zui-row alp-edit-row">
				<div class="zui-row__head">
					<span class="zui-row__label">
						<?php esc_html_e( 'Display time format', 'advanced-local-pickup-for-woocommerce' ); ?>
						<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'Select time format which you want to display in business hours for customers.', 'advanced-local-pickup-for-woocommerce' ); ?>">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
							<span class="zui-tooltip__bubble"><?php esc_html_e( 'Select time format which you want to display in business hours for customers.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
						</span>
					</span>
				</div>
				<div class="zui-row__control">
					<div class="zui-select-wrap zui-select-wrap--sm">
						<select class="zui-select select" id="wclp_default_time_format" name="wclp_default_time_format">
							<option value="12" <?php selected( $current_time_fmt, '12' ); ?>><?php esc_html_e( '12 hour', 'advanced-local-pickup-for-woocommerce' ); ?></option>
							<option value="24" <?php selected( $current_time_fmt, '24' ); ?>><?php esc_html_e( '24 hour', 'advanced-local-pickup-for-woocommerce' ); ?></option>
						</select>
						<span class="zui-select-chevron">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
						</span>
					</div>
				</div>
			</div>

			<div class="zui-row alp-edit-row">
				<div class="zui-row__head">
					<span class="zui-row__label">
						<?php esc_html_e( 'Work hours', 'advanced-local-pickup-for-woocommerce' ); ?>
						<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'Select working days of your store.', 'advanced-local-pickup-for-woocommerce' ); ?>">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
							<span class="zui-tooltip__bubble"><?php esc_html_e( 'Select working days of your store.', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
						</span>
					</span>
					<p class="zui-row__desc"><?php esc_html_e( 'Tick each day you accept pickups and click the time range to set hours.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				</div>
				<div class="zui-row__control">
					<div class="pickup_hours_div business alp-workhours-grid alp-workhours-grid--cards">
						<?php foreach ( (array) $days_bh as $key => $val ) :
							$bh_checked  = ( isset( $store_days_data[ $key ]['checked'] ) && 1 == $store_days_data[ $key ]['checked'] ); // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
							$pill_class  = 'hours-time';
							$hour1_start = isset( $store_days_data[ $key ]['wclp_store_hour'] )     ? $store_days_data[ $key ]['wclp_store_hour']     : '';
							$hour1_end   = isset( $store_days_data[ $key ]['wclp_store_hour_end'] ) ? $store_days_data[ $key ]['wclp_store_hour_end'] : '';
							$has_hour1   = ( '' !== $hour1_start && '' !== $hour1_end );
							$day_label   = isset( $day_short[ $key ] ) ? $day_short[ $key ] : ucfirst( $key );
							$date_long   = isset( $days_bh[ $key ] ) ? $days_bh[ $key ] : ucfirst( $key );
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
									<span class="hours <?php echo esc_attr( $pill_class ); ?> alp-workhours-card__pill<?php echo $has_hour1 ? '' : ' alp-workhours-card__pill--empty'; ?>">
										<?php if ( $has_hour1 ) : ?>
											<?php echo esc_html( $fmt_time( $hour1_start ) ); ?> &ndash; <?php echo esc_html( $fmt_time( $hour1_end ) ); ?>
										<?php else : ?>
											<span class="dashicons dashicons-plus"></span>
										<?php endif; ?>
									</span>
									<?php do_action( 'wclp_split_hours_hook', $key, $current_time_fmt, $location, $pill_class ); ?>

									<div id="bh_hours_popup_html_<?php echo esc_attr( $key ); ?>" class="popupwrapper alp-hours-popup alp-hours-popup--bh" style="display:none;">
										<div class="popuprow alp-hours-popup__card">

											<div class="alp-hours-popup__head">
												<div class="alp-hours-popup__heading">
													<h4 class="alp-hours-popup__title"><?php esc_html_e( 'Business Hours', 'advanced-local-pickup-for-woocommerce' ); ?></h4>
													<p class="alp-hours-popup__sub"><?php /* translators: %s: day name */ printf( esc_html__( 'Configure intervals for %s', 'advanced-local-pickup-for-woocommerce' ), esc_html( $date_long ) ); ?></p>
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
												<?php do_action( 'wclp_multi_hours_hook', $key, $current_time_fmt, $location, $send_time_array ); ?>
											</div>

											<?php do_action( 'wclp_apply_mltiple_popup_hook', $days_bh, $key ); ?>

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
				</div>
			</div>

			<?php do_action( 'wclp_add_business_setting_html_hook', $location ); ?>
		</div>

		<?php /* ---- PRO-locked sections ---- */ ?>
		<?php foreach ( $alp_pro_sections as $pro_section ) : ?>
			<div class="accordion heading premium alp-edit-acc alp-pro-locked-section">
				<label>
					<span class="alp-edit-acc__title"><?php echo esc_html( $pro_section['title'] ); ?></span>
					<span class="zui-pro-feature">
						<span class="zui-pro-feature__badge"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						<span class="zui-pro-feature__lock" aria-hidden="true">
							<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
						</span>
					</span>
					<span class="alp-edit-acc__chevron dashicons dashicons-arrow-down-alt2"></span>
					<br>
					<span class="heading-subtitle"><?php echo esc_html( $pro_section['sub'] ); ?></span>
				</label>
			</div>
			<div class="panel options alp-edit-panel alp-pro-locked-section">
				<div class="zui-lock-section">
					<div class="zui-lock-section__icon">
						<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
					</div>
					<p class="zui-lock-section__desc"><?php echo esc_html( $pro_section['desc'] ); ?></p>
					<a href="<?php echo esc_url( $alp_upgrade_url ); ?>" class="zui-btn-primary zui-lock-section__cta" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Upgrade to PRO', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				</div>
			</div>
		<?php endforeach; ?>

		<?php do_action( 'wclp_add_setting_html_hook', $location ); ?>

	</form>

</main>
</div>
