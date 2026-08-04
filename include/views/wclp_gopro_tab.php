<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- View template file; variable and hook names are established by the surrounding controller and are part of the plugin API.
/**
 * Go Pro tab — ZUI redesign for ALP Free.
 *
 * Mirrors CBR Free's go-pro template structure (hero + feature comparison
 * table + CTA) using the `alp-go-pro-v2` namespace instead of `cbr-`.
 *
 * @package WooAdvancedLocalPickup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Per-slug emblem icons (lucide path data) come from the library's icon
// registry — assets/zui/icons.php is included so we can call \Zorem\UI\get_icon()
// inside the Powerful Add-ons grid below. Without this, every `'img'`-less
// addon card would render an empty logo tile.
$alp_zui_dir = wc_local_pickup()->get_plugin_path() . '/assets/zui';
if ( ! function_exists( 'Zorem\UI\get_icon' ) && file_exists( $alp_zui_dir . '/icons.php' ) ) {
	require_once $alp_zui_dir . '/icons.php';
}

$alp_gopro_url = 'https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPFREE&utm_campaign=GoPro';

$alp_comp_features = array(
	array(
		'title'      => esc_html__( 'Pickup Locations', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Manage where your customers pick up their orders.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'limited',
		'free_label' => esc_html__( '1 Location', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Unlimited Locations', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Pickup Appointments', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Let customers reserve a specific pickup time slot at checkout.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Reservable Slots', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Fulfillment Dashboard', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Monitor live pickup orders by status from a dedicated dashboard.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Full Dashboard', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Cart & Checkout Customization', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Customize how pickup options appear on the cart and checkout pages.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Inline & Popup', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Pickup Discounts & Fees', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Apply a percentage / flat discount or a pickup fee per location.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Flexible Pricing', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Mixed Orders (Pickup + Shipping)', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Let customers ship part of the order and pick up the rest.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Full Support', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Curbside Pickup', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Let customers notify the store when they have arrived at the curb.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Curbside Flow', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Automations & Reminders', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Automatic pickup reminders, status changes, and follow-ups.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Scheduled Workflows', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Order Statuses', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Custom WooCommerce order statuses tailored to local pickup.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'limited',
		'free_label' => esc_html__( 'Ready / Picked Up', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( '+ Processing LP', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Customer Pickup Changes', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Let customers update their pickup location or time after checkout.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'cross',
		'free_label' => esc_html__( 'Not Available', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Self-Serve Changes', 'advanced-local-pickup-for-woocommerce' ),
	),
	array(
		'title'      => esc_html__( 'Premium Support', 'advanced-local-pickup-for-woocommerce' ),
		'desc'       => esc_html__( 'Priority ticket handling and dedicated help center access.', 'advanced-local-pickup-for-woocommerce' ),
		'free'       => 'limited',
		'free_label' => esc_html__( 'Forum Support', 'advanced-local-pickup-for-woocommerce' ),
		'pro'        => esc_html__( 'Priority Support', 'advanced-local-pickup-for-woocommerce' ),
	),
);
?>
<div class="zui-layout zui-layout--full" id="wclp_content_gopro">
<main class="zui-content zui-content--full">
	<section id="alp-section-go-pro" class="zui-section is-active" data-section="go-pro">

		<div class="alp-go-pro-v2">

			<!-- Hero -->
			<div class="gopro-hero">
				<h1><?php esc_html_e( 'Unlock the full power of Local Pickup', 'advanced-local-pickup-for-woocommerce' ); ?></h1>
				<p>
					<?php
					echo wp_kses_post(
						sprintf(
							/* translators: %s: link to ALP Pro */
							__( 'Stop limiting your store with a single pickup location and basic statuses. Upgrade to <a href="%s" target="_blank">ALP PRO</a> for multiple locations, appointments, a fulfillment dashboard, and more.', 'advanced-local-pickup-for-woocommerce' ),
							esc_url( $alp_gopro_url )
						)
					);
					?>
				</p>
			</div>

			<!-- Feature comparison -->
			<div class="gopro-comparison">
				<div class="gopro-comp-header">
					<div class="gopro-comp-header-label"><?php esc_html_e( 'Feature Comparison', 'advanced-local-pickup-for-woocommerce' ); ?></div>
					<div class="gopro-comp-header-col">
						<span class="comp-header-badge badge-current"><?php esc_html_e( 'Current', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						<span class="comp-header-title"><?php esc_html_e( 'ALP FREE', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					</div>
					<div class="gopro-comp-header-col is-pro">
						<span class="comp-header-badge badge-recommended"><?php esc_html_e( 'Recommended', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						<span class="comp-header-title"><?php esc_html_e( 'ALP PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					</div>
				</div>

				<?php foreach ( $alp_comp_features as $feat ) : ?>
					<div class="gopro-comp-row">
						<div class="gopro-comp-feature">
							<strong><?php echo esc_html( $feat['title'] ); ?></strong>
							<span><?php echo esc_html( $feat['desc'] ); ?></span>
						</div>
						<div class="gopro-comp-cell">
							<?php if ( 'check' === $feat['free'] ) : ?>
								<span class="comp-icon icon-check">
									<svg fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
								</span>
							<?php else : ?>
								<span class="comp-icon icon-x">
									<svg fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5"><line x1="16" y1="8" x2="8" y2="16"/><line x1="8" y1="8" x2="16" y2="16"/></svg>
								</span>
							<?php endif; ?>
							<?php if ( ! empty( $feat['free_label'] ) ) : ?>
								<span class="comp-status"><?php echo esc_html( $feat['free_label'] ); ?></span>
							<?php endif; ?>
						</div>
						<div class="gopro-comp-cell is-pro">
							<span class="comp-icon icon-check">
								<svg fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
							</span>
							<span class="comp-status"><?php echo esc_html( $feat['pro'] ); ?></span>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<!-- CTA -->
			<div class="gopro-cta">
				<a href="<?php echo esc_url( $alp_gopro_url ); ?>" class="gopro-cta-btn" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'GET STARTED WITH PRO', 'advanced-local-pickup-for-woocommerce' ); ?></a>
				<p class="gopro-cta-sub"><?php esc_html_e( 'Join store owners running smarter local pickup workflows', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			</div>

		</div>

		<?php
		/* ---- Telemetry & Communication Preferences card ---- */
		$alp_tracker_slug   = 'advanced_local_pickup_for_woocommerce';
		$alp_optin_email    = (int) get_option( $alp_tracker_slug . '_optin_email_notification', 0 );
		$alp_enable_usage   = (int) get_option( $alp_tracker_slug . '_enable_usage_data', 0 );
		$alp_opt_email_key  = $alp_tracker_slug . '_optin_email_notification';
		$alp_opt_env_key    = $alp_tracker_slug . '_enable_usage_data';
		$alp_tracking_nonce = $alp_tracker_slug . '_usage_data_form_nonce';
		$alp_tracking_act   = 'alp_free_telemetry_save';
		?>
		<div class="zui-card zui-lic-card alp-lic-telemetry-card">
			<div class="zui-lic-card__head zui-lic-card__head--row">
				<span class="zui-lic-card__title">
					<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
					<?php esc_html_e( 'Telemetry & Communication Preferences', 'advanced-local-pickup-for-woocommerce' ); ?>
				</span>
				<div class="zui-lic-save">
					<span class="zui-lic-save__msg" id="alp-usage-msg" hidden><?php esc_html_e( 'Saved', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<button type="button" class="zui-lic-savebtn" data-target="#alp-usage-tracking-form"><?php esc_html_e( 'Save', 'advanced-local-pickup-for-woocommerce' ); ?></button>
				</div>
			</div>
			<form method="post" id="alp-usage-tracking-form" class="zui-lic-telemetry-form">
				<div class="zui-lic-pref">
					<label class="zui-toggle">
						<input type="hidden" name="<?php echo esc_attr( $alp_opt_email_key ); ?>" value="0">
						<input type="checkbox" name="<?php echo esc_attr( $alp_opt_email_key ); ?>" value="1" class="zui-toggle__input" <?php checked( 1, $alp_optin_email ); ?>>
						<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
					</label>
					<div>
						<span class="zui-lic-pref__title"><?php esc_html_e( 'Security bulletins and core asset deployment digests', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						<p class="zui-lic-pref__sub"><?php esc_html_e( 'Subscribes your registered domain contact to high priority bulletins, local carrier format enhancements, and core diagnostic security updates.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
					</div>
				</div>
				<div class="zui-lic-pref">
					<label class="zui-toggle">
						<input type="hidden" name="<?php echo esc_attr( $alp_opt_env_key ); ?>" value="0">
						<input type="checkbox" name="<?php echo esc_attr( $alp_opt_env_key ); ?>" value="1" class="zui-toggle__input" <?php checked( 1, $alp_enable_usage ); ?>>
						<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
					</label>
					<div>
						<span class="zui-lic-pref__title"><?php esc_html_e( 'Opt-in to share WooCommerce environment specifications', 'advanced-local-pickup-for-woocommerce' ); ?></span>
						<p class="zui-lic-pref__sub"><?php esc_html_e( 'Shares secure versioning data (PHP environment base, active plugins lists, localized theme template names) to let our priority support team proactively identify store errors.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
					</div>
				</div>
				<?php wp_nonce_field( $alp_tracker_slug . '_usage_data_form', $alp_tracking_nonce ); ?>
				<input type="hidden" name="action" value="<?php echo esc_attr( $alp_tracking_act ); ?>">
			</form>
		</div>

		<!-- Powerful Add-ons (ZUI License Ecosystem) -->
		<?php
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$alp_more_plugins = array(
			array(
				'title'       => 'Advanced Shipment Tracking',
				'description' => __( 'AST Pro provides powerful features to easily add tracking information to WooCommerce orders, automate fulfillment workflows, and keep your customers happy and informed.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://www.zorem.com/product/woocommerce-advanced-shipment-tracking/?utm_source=wp-admin&utm_medium=AST&utm_campaign=ALPFREE-add-ons',
				'icon'        => 'package',
				'tint'        => '#DBEAFE',
				'accent'      => '#2563EB',
				'file'        => 'ast-pro/ast-pro.php',
			),
			array(
				'title'       => 'TrackShip for WooCommerce',
				'description' => __( 'Take control of your post-shipping workflows, reduce time spent on customer service and provide a superior post-purchase experience to your customers. Beyond automatic shipment tracking, TrackShip brings a branded tracking experience into your store.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://wordpress.org/plugins/trackship-for-woocommerce/?utm_source=wp-admin&utm_medium=TS4WC&utm_campaign=ALPFREE-add-ons',
				'img'         => wc_local_pickup()->plugin_dir_url() . 'assets/images/ts-45.png',
				'icon'        => 'truck',
				'tint'        => '#CCFBF1',
				'accent'      => '#0D9488',
				'file'        => 'trackship-for-woocommerce/trackship-for-woocommerce.php',
			),
			array(
				'title'       => 'Country Based Restrictions',
				'description' => __( 'Restrict where your products are visible and purchasable by country. Hide categories, block payments or shipping methods per country, and surface a clean restriction message at checkout.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://wordpress.org/plugins/woo-product-country-base-restrictions/?utm_source=wp-admin&utm_medium=CBR&utm_campaign=ALPFREE-add-ons',
				'icon'        => 'globe',
				'tint'        => '#FEF3C7',
				'accent'      => '#D97706',
				'file'        => 'woo-product-country-base-restrictions/woocommerce-product-country-base-restrictions.php',
			),
			array(
				'title'       => 'Customer Email Verification',
				'description' => __( 'Reduce registration spam and fraudulent orders by requiring customers to verify their email address when registering an account or before placing an order.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://www.zorem.com/product/customer-email-verification/?utm_source=wp-admin&utm_medium=CEV&utm_campaign=ALPFREE-add-ons',
				'icon'        => 'shield-check',
				'tint'        => '#FEE2E2',
				'accent'      => '#DC2626',
				'file'        => 'customer-email-verification/customer-email-verification.php',
			),
			array(
				'title'       => 'SMS for WooCommerce',
				'description' => __( 'Keep customers informed with automated SMS text messages — order updates, ready-for-pickup alerts, and delivery notifications, all triggered from WooCommerce.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://www.zorem.com/product/sms-for-woocommerce/?utm_source=wp-admin&utm_medium=SMSWOO&utm_campaign=ALPFREE-add-ons',
				'icon'        => 'phone',
				'tint'        => '#DBEAFE',
				'accent'      => '#2563EB',
				'file'        => 'sms-for-woocommerce/sms-for-woocommerce.php',
			),
			array(
				'title'       => 'Email Reports for WooCommerce',
				'description' => __( 'Sales Report Email Pro helps you understand how well your store is performing and how your products are selling by sending daily, weekly, or monthly sales reports directly to your email.', 'advanced-local-pickup-for-woocommerce' ),
				'url'         => 'https://www.zorem.com/product/email-reports-for-woocommerce/?utm_source=wp-admin&utm_medium=SRE&utm_campaign=ALPFREE-add-ons',
				'icon'        => 'mail',
				'tint'        => '#F3E8FF',
				'accent'      => '#9333EA',
				'file'        => 'sales-report-email-pro/sales-report-email-pro.php',
			),
		);
		?>
		<div class="alp-go-pro-v2">
			<div class="zui-lic-eco">
				<div class="zui-lic-eco__head">
					<div class="zui-lic-eco__heading">
						<span class="zui-lic-eco__icon">
							<svg class="zui-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/><path d="M5 3v4"/><path d="M19 17v4"/><path d="M3 5h4"/><path d="M17 19h4"/></svg>
						</span>
						<div>
							<h3><?php esc_html_e( 'Powerful Add-ons', 'advanced-local-pickup-for-woocommerce' ); ?></h3>
							<p><?php esc_html_e( 'Extend your store\'s capabilities with our ecosystem.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
						</div>
					</div>
					<div class="zui-lic-eco__filters">
						<button type="button" class="zui-lic-eco__filter is-active" data-filter="all"><?php esc_html_e( 'All', 'advanced-local-pickup-for-woocommerce' ); ?></button>
						<button type="button" class="zui-lic-eco__filter" data-filter="active"><?php esc_html_e( 'Active', 'advanced-local-pickup-for-woocommerce' ); ?></button>
						<button type="button" class="zui-lic-eco__filter" data-filter="addons"><?php esc_html_e( 'Add-ons', 'advanced-local-pickup-for-woocommerce' ); ?></button>
					</div>
				</div>
				<div class="zui-lic-eco__search">
					<span class="zui-lic-eco__search-icon">
						<svg class="zui-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
					</span>
					<input type="text" class="zui-input" id="alp-lic-search" placeholder="<?php esc_attr_e( 'Search&hellip;', 'advanced-local-pickup-for-woocommerce' ); ?>">
				</div>
				<div class="zui-lic-eco__grid" id="alp-lic-grid">
					<?php
					foreach ( $alp_more_plugins as $alp_index => $addon ) :
						$is_active = is_plugin_active( $addon['file'] );
						?>
						<div class="zui-card zui-lic-plugin" data-name="<?php echo esc_attr( strtolower( $addon['title'] ) ); ?>" data-active="<?php echo $is_active ? '1' : '0'; ?>">
							<div class="zui-lic-plugin__head">
								<span class="zui-lic-plugin__logo" style="background: <?php echo esc_attr( $addon['tint'] ); ?>; color: <?php echo esc_attr( $addon['accent'] ); ?>;">
									<?php
									if ( ! empty( $addon['img'] ) ) {
										?>
										<img src="<?php echo esc_url( $addon['img'] ); ?>" alt="<?php echo esc_attr( $addon['title'] ); ?>" loading="lazy">
										<?php
									} elseif ( function_exists( 'Zorem\UI\get_icon' ) ) {
										echo \Zorem\UI\get_icon( $addon['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted static SVG.
									}
									?>
								</span>
								<div class="zui-lic-plugin__id">
									<h4 class="zui-lic-plugin__name"><?php echo esc_html( $addon['title'] ); ?></h4>
									<span class="zui-lic-plugin__type"><?php esc_html_e( 'WOO EXTENSION', 'advanced-local-pickup-for-woocommerce' ); ?></span>
								</div>
								<?php if ( ! $is_active && 0 === $alp_index ) : ?>
									<span class="zui-lic-plugin__badge"><?php esc_html_e( 'Recommended', 'advanced-local-pickup-for-woocommerce' ); ?></span>
								<?php endif; ?>
							</div>
							<div class="zui-lic-plugin__body">
								<p class="zui-lic-plugin__desc"><?php echo esc_html( $addon['description'] ); ?></p>
								<div class="zui-lic-plugin__foot">
									<?php if ( $is_active ) : ?>
										<span class="zui-lic-plugin__active">
											<span class="zui-lic-plugin__dot"></span>
											<?php esc_html_e( 'Active', 'advanced-local-pickup-for-woocommerce' ); ?>
										</span>
										<span class="zui-lic-plugin__stat"><?php esc_html_e( 'Active in this store', 'advanced-local-pickup-for-woocommerce' ); ?></span>
									<?php else : ?>
										<a class="zui-lic-plugin__get" href="<?php echo esc_url( $addon['url'] ); ?>" target="_blank" rel="noreferrer noopener">
											<span><?php esc_html_e( 'Get Extension', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<svg class="zui-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
										</a>
										<span class="zui-lic-plugin__stat"><?php esc_html_e( 'By zorem.com', 'advanced-local-pickup-for-woocommerce' ); ?></span>
									<?php endif; ?>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
					<div class="zui-lic-eco__empty" hidden><?php esc_html_e( 'No plugins matched.', 'advanced-local-pickup-for-woocommerce' ); ?></div>
				</div>
			</div>
		</div>

	</section>
</main>
</div>
