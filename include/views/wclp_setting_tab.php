<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- View template file; variable and hook names are established by the surrounding controller and are part of the plugin API.
/**
 * Settings tab — ZUI redesign for ALP Free.
 *
 * Mirrors the structure of ALP Pro's wclp_setting_tab.php but with the free
 * tier feature set:
 *   - Display Options:     2 rows editable, 3 rows locked (PRO).
 *   - Local Pickup Workflow: 2 rows editable (Ready For Pickup, Picked Up),
 *                          1 row locked (Processing LP).
 *   - Local Pickup Dashboard / Cart & Checkout / Catalog:
 *                          entire sections locked behind a centered upgrade card.
 *
 * The save handler (`wclp_setting_form_update_callback` in
 * `include/wc-local-pickup-admin.php`) reads:
 *   - wclp_show_pickup_instruction[ display_in_order_details_page | display_in_order_received_page ]
 *   - wclp_processing_additional_content
 *   - wclp_status_ready_pickup / wclp_status_picked_up
 *   - wclp_ready_pickup_status_label_color / wclp_ready_pickup_status_label_font_color
 *   - wclp_pickup_status_label_color / wclp_pickup_status_label_font_color
 *   - wclp_enable_ready_pickup_email / wclp_enable_pickup_email
 *
 * @package WooAdvancedLocalPickup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Active in-tab sub-section. Switched without reload by admin.js sidebar handler.
$alp_section        = isset( $_GET['section'] ) ? sanitize_key( wp_unslash( $_GET['section'] ) ) : 'display-options'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$alp_valid_sections = array( 'display-options', 'local-pickup-workflow', 'local-pickup-dashboard', 'checkout-configuration', 'products-catalog-options' );
if ( ! in_array( $alp_section, $alp_valid_sections, true ) ) {
	$alp_section = 'display-options';
}

// Display Options values for ZUI rows.
$wclp_show_pickup    = (array) get_option( 'wclp_show_pickup_instruction' );
$wclp_emails_content = get_option( 'wclp_processing_additional_content', esc_html__( 'You will receive an email when your order is ready for pickup.', 'advanced-local-pickup-for-woocommerce' ) );

// Free editable rows.
$display_options_free_rows = array(
	'display_in_order_details_page' => array(
		'label' => esc_html__( 'My Account (orders history)', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Display pickup details in the My Account section.', 'advanced-local-pickup-for-woocommerce' ),
		'tip'   => '',
	),
	'display_in_order_received_page' => array(
		'label' => esc_html__( 'Order received page', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Display pickup details on the Order Received confirmation page.', 'advanced-local-pickup-for-woocommerce' ),
		'tip'   => '',
	),
);

// PRO-locked rows (rendered disabled — the data still posts so an upgrade keeps it).
$display_options_locked_rows = array(
	'display_pickup_items_in_pickup_info' => array(
		'label' => esc_html__( 'Show Pickup Items in Pickup Information', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'List specific pickup items within the pickup details section.', 'advanced-local-pickup-for-woocommerce' ),
		'tip'   => esc_html__( 'Enable this option to display a list of pickup items in the pickup information section. Disable it to hide item details from the pickup information.', 'advanced-local-pickup-for-woocommerce' ),
	),
	'enable_pickup_qr' => array(
		'label' => esc_html__( 'Show pickup confirmation QR code', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Place QR verification codes inside order confirmations and transactional templates.', 'advanced-local-pickup-for-woocommerce' ),
		'tip'   => esc_html__( 'Display a scannable QR code on the customer\'s order page and pickup emails. Staff scan it at the counter to confirm pickup.', 'advanced-local-pickup-for-woocommerce' ),
	),
	'allow_customer_change_pickup_info' => array(
		'label' => esc_html__( 'Allow customers to change pickup details after placing the order', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Enable customers to update their pickup preferences post-purchase.', 'advanced-local-pickup-for-woocommerce' ),
		'tip'   => esc_html__( 'Allows customers to update the pickup location or pickup time from their order details page after checkout.', 'advanced-local-pickup-for-woocommerce' ),
	),
);

// Section metadata for the sub-section nav + headers.
$alp_sections = array(
	'display-options' => array(
		'title' => esc_html__( 'Display Options', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Configure how pickup information is displayed to customers.', 'advanced-local-pickup-for-woocommerce' ),
		'icon'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>',
	),
	'local-pickup-workflow' => array(
		'title' => esc_html__( 'Order Statuses', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Manage how your customers see their orders and control when notifications are triggered.', 'advanced-local-pickup-for-woocommerce' ),
		'icon'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"></path><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"></path></svg>',
	),
	'local-pickup-dashboard' => array(
		'title' => esc_html__( 'Local Pickup Dashboard', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Configure and monitor live status for your local pickup orders.', 'advanced-local-pickup-for-woocommerce' ),
		'icon'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14a9 3 0 0 0 18 0V5"></path><path d="M3 12a9 3 0 0 0 18 0"></path></svg>',
	),
	'checkout-configuration' => array(
		'title' => esc_html__( 'Cart & Checkout Options', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Configure how local pickup displays on your checkout and cart pages.', 'advanced-local-pickup-for-woocommerce' ),
		'icon'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>',
	),
	'products-catalog-options' => array(
		'title' => esc_html__( 'Products Catalog Options', 'advanced-local-pickup-for-woocommerce' ),
		'sub'   => esc_html__( 'Configure how local pickup notices and notifications show on product catalog pages.', 'advanced-local-pickup-for-woocommerce' ),
		'icon'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
	),
);

// Map sub-section -> icon used in the sidebar nav button.
$alp_sidebar_icons = array(
	'display-options'           => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>',
	'local-pickup-workflow'     => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
	'local-pickup-dashboard'    => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M3 5v14a9 3 0 0 0 18 0V5"></path><path d="M3 12a9 3 0 0 0 18 0"></path></svg>',
	'checkout-configuration'    => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="5" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="10" x2="22" y2="10"></line></svg>',
	'products-catalog-options'  => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
);

// Upgrade URL (used by every locked section).
$alp_upgrade_url = 'https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPFREE&utm_campaign=settings-lock';

// Sections that have editable content (Save button rendered only on these).
$alp_editable_sections = array( 'display-options', 'local-pickup-workflow' );

// Lock overlay copy per section.
$alp_locked_copy = array(
	'local-pickup-dashboard'   => array(
		'title' => esc_html__( 'Unlock Local Pickup Dashboard', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Monitor live pickup orders, filter by status, and fulfill items from a single dashboard. Upgrade to ALP PRO to unlock the Local Pickup Dashboard.', 'advanced-local-pickup-for-woocommerce' ),
	),
	'checkout-configuration'   => array(
		'title' => esc_html__( 'Unlock Cart & Checkout Customization', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Customize how pickup locations and pickup options appear on the cart and checkout pages, sort by distance, allow mixed orders, and more. Upgrade to ALP PRO to unlock these options.', 'advanced-local-pickup-for-woocommerce' ),
	),
	'products-catalog-options' => array(
		'title' => esc_html__( 'Unlock Products Catalog Options', 'advanced-local-pickup-for-woocommerce' ),
		'desc'  => esc_html__( 'Show "Available for Local Pickup" notices on product pages and shop catalogs with customizable copy and placement. Upgrade to ALP PRO to unlock these options.', 'advanced-local-pickup-for-woocommerce' ),
	),
);
?>
<div class="zui-layout">

	<div class="zui-sidebar__overlay" data-ast-drawer-close></div>

	<aside class="zui-sidebar" id="ast-set-sidebar">

		<div class="zui-sidebar__mobile-head">
			<span class="zui-sidebar__mobile-title"><?php esc_html_e( 'ALP Settings', 'advanced-local-pickup-for-woocommerce' ); ?></span>
			<button type="button" class="zui-sidebar__close" data-ast-drawer-close aria-label="<?php esc_attr_e( 'Close menu', 'advanced-local-pickup-for-woocommerce' ); ?>">
				<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
			</button>
		</div>

		<nav class="zui-sidebar__nav" aria-label="<?php esc_attr_e( 'Settings sections', 'advanced-local-pickup-for-woocommerce' ); ?>">
			<?php
			foreach ( $alp_sections as $slug => $meta ) :
				$is_active     = ( $slug === $alp_section );
				$is_locked_nav = ! in_array( $slug, $alp_editable_sections, true );
				$sidebar_label = ( 'local-pickup-workflow' === $slug ) ? esc_html__( 'Local Pickup Workflow', 'advanced-local-pickup-for-woocommerce' ) : $meta['title'];
				$btn_classes   = 'zui-sidebar__item';
				if ( $is_active )     { $btn_classes .= ' is-active'; }
				if ( $is_locked_nav ) { $btn_classes .= ' is-locked'; }
				?>
				<button type="button" class="<?php echo esc_attr( $btn_classes ); ?>" data-section="<?php echo esc_attr( $slug ); ?>" aria-controls="alp-section-<?php echo esc_attr( $slug ); ?>"<?php echo $is_active ? ' aria-current="true"' : ''; ?>>
					<span class="zui-sidebar__icon"><?php echo $alp_sidebar_icons[ $slug ]; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static hardcoded SVG markup, no user input. ?></span>
					<span class="zui-sidebar__label"><?php echo esc_html( $sidebar_label ); ?></span>
					<?php if ( $is_locked_nav ) : ?>
						<span class="zui-sidebar__pro-tag" title="<?php esc_attr_e( 'Available in PRO', 'advanced-local-pickup-for-woocommerce' ); ?>"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<?php endif; ?>
				</button>
			<?php endforeach; ?>
		</nav>

		<div class="zui-quickhelp">
			<h4 class="zui-quickhelp__title"><?php esc_html_e( 'Quick Help', 'advanced-local-pickup-for-woocommerce' ); ?></h4>
			<p class="zui-quickhelp__text"><?php esc_html_e( 'Learn how to configure ALP for best results.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
			<div class="zui-quickhelp__links">
				<a class="zui-quickhelp__link" href="https://docs.zorem.com/docs/zorem-local-pickup/?utm_source=wp-admin&utm_medium=ALP&utm_campaign=QuickHelp" target="_blank" rel="noreferrer noopener">
					<span><?php esc_html_e( 'View Documentation', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
				<a class="zui-quickhelp__link zui-quickhelp__link--muted" href="https://wordpress.org/support/plugin/advanced-local-pickup-for-woocommerce/#new-topic-0" target="_blank" rel="noreferrer noopener">
					<span><?php esc_html_e( 'Get Support', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
				</a>
			</div>
			<div class="zui-quickhelp__art" aria-hidden="true">
				<span class="zui-quickhelp__glow"></span>
				<span class="zui-quickhelp__sphere"></span>
				<span class="zui-quickhelp__paper zui-quickhelp__paper--1">
					<span class="zui-quickhelp__line zui-quickhelp__line--brand"></span>
					<span class="zui-quickhelp__line zui-quickhelp__line--mid"></span>
					<span class="zui-quickhelp__line zui-quickhelp__line--short"></span>
				</span>
				<span class="zui-quickhelp__paper zui-quickhelp__paper--2">
					<span class="zui-quickhelp__line zui-quickhelp__line--accent"></span>
					<span class="zui-quickhelp__line zui-quickhelp__line--mid"></span>
					<span class="zui-quickhelp__line zui-quickhelp__line--mid"></span>
					<span class="zui-quickhelp__line zui-quickhelp__line--short"></span>
				</span>
				<span class="zui-quickhelp__check">&#10003;</span>
			</div>
		</div>

	</aside>

	<main class="zui-content" id="ast-set-content">
		<form id="wclp_setting_tab_form" class="wclp_setting_tab_form" method="post" action="" enctype="multipart/form-data" novalidate>

			<?php foreach ( $alp_sections as $slug => $meta ) :
				$is_active   = ( $slug === $alp_section );
				$is_editable = in_array( $slug, $alp_editable_sections, true );
				?>
				<section id="alp-section-<?php echo esc_attr( $slug ); ?>" class="zui-section<?php echo $is_active ? ' is-active' : ''; ?>" data-section="<?php echo esc_attr( $slug ); ?>"<?php echo $is_active ? '' : ' hidden'; ?>>

					<div class="zui-section-header">
						<div class="zui-section-header__main">
							<span class="zui-section-header__icon"><?php echo $meta['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div class="zui-section-header__text">
								<div class="zui-section-header__titlewrap">
									<h2 class="zui-section-header__title"><?php echo esc_html( $meta['title'] ); ?></h2>
								</div>
								<p class="zui-section-header__sub"><?php echo esc_html( $meta['sub'] ); ?></p>
							</div>
						</div>
						<div class="zui-section-header__actions">
							<?php if ( $is_editable ) : ?>
								<span class="zui-savebtn-wrap">
									<button name="save" type="submit" class="zui-savebtn wclp-save woocommerce-save-button" value="Save Changes">
										<span class="zui-savebtn__spinner" aria-hidden="true"></span>
										<span class="zui-savebtn__label"><?php esc_html_e( 'Save Changes', 'advanced-local-pickup-for-woocommerce' ); ?></span>
									</button>
								</span>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( 'display-options' === $slug ) : ?>

						<div class="zui-card">
							<?php
							/* ---- Free editable rows ---- */
							foreach ( $display_options_free_rows as $opt_key => $row ) :
								$checked = ( isset( $wclp_show_pickup[ $opt_key ] ) && intval( $wclp_show_pickup[ $opt_key ] ) === 1 ) ? 'checked' : '';
								?>
								<div class="zui-row zui-row--inline border_1">
									<div class="zui-row__head">
										<span class="zui-row__label">
											<?php echo esc_html( $row['label'] ); ?>
											<?php if ( ! empty( $row['tip'] ) ) : ?>
												<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php echo esc_attr( $row['tip'] ); ?>">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
													<span class="zui-tooltip__bubble"><?php echo esc_html( $row['tip'] ); ?><span class="zui-tooltip__arrow"></span></span>
												</span>
											<?php endif; ?>
										</span>
										<p class="zui-row__desc"><?php echo esc_html( $row['desc'] ); ?></p>
									</div>
									<div class="zui-row__control">
										<label class="zui-toggle">
											<input type="hidden" name="wclp_show_pickup_instruction[<?php echo esc_attr( $opt_key ); ?>]" value="0">
											<input type="checkbox" name="wclp_show_pickup_instruction[<?php echo esc_attr( $opt_key ); ?>]" value="1" class="zui-toggle__input" <?php echo esc_html( $checked ); ?>>
											<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
										</label>
									</div>
								</div>
							<?php endforeach; ?>

							<?php
							/* ---- PRO locked rows ---- */
							foreach ( $display_options_locked_rows as $opt_key => $row ) :
								$is_nested_parent = ( 'allow_customer_change_pickup_info' === $opt_key );
								?>
								<div class="zui-row zui-row--inline border_1 alp-pro-row-locked" data-pro-locked="1">
									<div class="zui-row__head">
										<span class="zui-row__label">
											<?php echo esc_html( $row['label'] ); ?>
											<?php if ( ! empty( $row['tip'] ) ) : ?>
												<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php echo esc_attr( $row['tip'] ); ?>">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
													<span class="zui-tooltip__bubble"><?php echo esc_html( $row['tip'] ); ?><span class="zui-tooltip__arrow"></span></span>
												</span>
											<?php endif; ?>
										</span>
										<p class="zui-row__desc"><?php echo esc_html( $row['desc'] ); ?></p>
									</div>
									<div class="zui-row__control">
										<label class="zui-toggle">
											<input type="hidden" name="wclp_show_pickup_instruction[<?php echo esc_attr( $opt_key ); ?>]" value="0">
											<input type="checkbox" name="wclp_show_pickup_instruction[<?php echo esc_attr( $opt_key ); ?>]" value="1" class="zui-toggle__input" disabled>
											<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
										</label>
										<span class="zui-pro-feature">
											<span class="zui-pro-feature__badge"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<span class="zui-pro-feature__lock" aria-hidden="true">
												<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
											</span>
										</span>
									</div>
								</div>

								<?php if ( $is_nested_parent ) : ?>
									<div class="zui-row__nested alp-pro-row-locked" data-pro-locked="1">
										<div class="zui-row zui-row--inline">
											<div class="zui-row__head">
												<span class="zui-row__label"><?php esc_html_e( 'Limit appointment changes before pickup', 'advanced-local-pickup-for-woocommerce' ); ?></span>
												<p class="zui-row__desc"><?php esc_html_e( 'Restrict appointment rescheduling within a threshold duration before the session.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
											</div>
											<div class="zui-row__control">
												<label class="zui-toggle">
													<input type="hidden" name="wclp_limit_pickup_change_before" value="0">
													<input type="checkbox" name="wclp_limit_pickup_change_before" value="1" class="zui-toggle__input" disabled>
													<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
												</label>
												<span class="zui-pro-feature">
													<span class="zui-pro-feature__badge"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
													<span class="zui-pro-feature__lock" aria-hidden="true">
														<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
													</span>
												</span>
											</div>
										</div>
										<div class="zui-row zui-row--inline zui-row--inline-fields">
											<div class="zui-row__head">
												<span class="zui-row__label"><?php esc_html_e( 'Allow appointment changes up to:', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											</div>
											<div class="zui-row__control">
												<input type="number" min="1" class="zui-input zui-input--xs" name="wclp_pickup_change_time_limit" value="1" disabled>
												<div class="zui-select-wrap">
													<select class="zui-select" name="wclp_pickup_change_time_unit" disabled>
														<option value="minutes"><?php esc_html_e( 'Minutes', 'advanced-local-pickup-for-woocommerce' ); ?></option>
														<option value="hours"><?php esc_html_e( 'Hours', 'advanced-local-pickup-for-woocommerce' ); ?></option>
														<option value="days" selected><?php esc_html_e( 'Days', 'advanced-local-pickup-for-woocommerce' ); ?></option>
													</select>
													<span class="zui-select-chevron">
														<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
													</span>
												</div>
												<span class="zui-row__hint"><?php esc_html_e( 'before the customer pickup.', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											</div>
										</div>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>

							<?php /* ---- Free: Additional content on processing email (the only free textarea) ---- */ ?>
							<div class="zui-row border_1">
								<div class="zui-row__head">
									<span class="zui-row__label">
										<?php esc_html_e( 'Additional content on processing email', 'advanced-local-pickup-for-woocommerce' ); ?>
										<span class="zui-tooltip" tabindex="0" role="img" aria-label="<?php esc_attr_e( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ); ?>">
											<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
											<span class="zui-tooltip__bubble"><?php esc_html_e( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ); ?><span class="zui-tooltip__arrow"></span></span>
										</span>
									</span>
									<p class="zui-row__desc"><?php esc_html_e( 'Customize the message shown on the processing order email when the order is a local pickup.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
								</div>
								<div class="zui-row__control">
									<textarea rows="3" class="zui-input regular-input input-text additional_textarea" name="wclp_processing_additional_content" id="wclp_processing_additional_content" placeholder="<?php esc_attr_e( 'Additional content on processing email in case of local pickup orders', 'advanced-local-pickup-for-woocommerce' ); ?>"><?php echo esc_textarea( stripslashes( $wclp_emails_content ) ); ?></textarea>
								</div>
							</div>
						</div>

					<?php elseif ( 'local-pickup-workflow' === $slug ) : ?>

						<?php
						// Free editable statuses + the locked Processing LP row above them.
						$workflow_statuses = array(
							array(
								'locked'         => true,
								'enabled_key'    => 'wclp_status_processing_lp',
								'color_key'      => 'wclp_processing_lp_status_label_color',
								'color_default'  => '#2563eb',
								'font_key'       => 'wclp_processing_lp_status_label_font_color',
								'font_default'   => '#fff',
								'email_key'      => 'wclp_enable_processing_lp_email',
								'label'          => esc_html__( 'Processing LP', 'advanced-local-pickup-for-woocommerce' ),
								'desc'           => esc_html__( 'Order being processed for local pickup', 'advanced-local-pickup-for-woocommerce' ),
								'icon'           => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 12 13 21 6"></polyline><path d="M3 6v12l9 5 9-5V6"></path><line x1="12" y1="13" x2="12" y2="22"></line></svg>',
							),
							array(
								'locked'         => false,
								'enabled_key'    => 'wclp_status_ready_pickup',
								'color_key'      => 'wclp_ready_pickup_status_label_color',
								'color_default'  => '#0ea5e9',
								'font_key'       => 'wclp_ready_pickup_status_label_font_color',
								'font_default'   => '#fff',
								'email_key'      => 'wclp_enable_ready_pickup_email',
								'email_option'   => 'woocommerce_customer_ready_pickup_order_settings',
								'customizer_slug' => 'ready_pickup',
								'label'          => esc_html__( 'Ready For Pickup', 'advanced-local-pickup-for-woocommerce' ),
								'desc'           => esc_html__( 'Order is ready to be collected', 'advanced-local-pickup-for-woocommerce' ),
								'icon'           => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>',
							),
							array(
								'locked'         => false,
								'enabled_key'    => 'wclp_status_picked_up',
								'color_key'      => 'wclp_pickup_status_label_color',
								'color_default'  => '#22c55e',
								'font_key'       => 'wclp_pickup_status_label_font_color',
								'font_default'   => '#fff',
								'email_key'      => 'wclp_enable_pickup_email',
								'email_option'   => 'woocommerce_customer_pickup_order_settings',
								'customizer_slug' => 'pickup',
								'label'          => esc_html__( 'Picked Up', 'advanced-local-pickup-for-woocommerce' ),
								'desc'           => esc_html__( 'Package has been collected by customer', 'advanced-local-pickup-for-woocommerce' ),
								'icon'           => '<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>',
							),
						);
						?>
						<div class="zui-card">
							<?php foreach ( $workflow_statuses as $st ) :
								$status_color = get_option( $st['color_key'], $st['color_default'] );
								$status_font  = get_option( $st['font_key'], $st['font_default'] );
								if ( ! empty( $st['locked'] ) ) {
									// Force defaults — settings are PRO.
									$status_color = $st['color_default'];
									$status_font  = $st['font_default'];
									$is_enabled   = false;
								} else {
									$is_enabled = (bool) get_option( $st['enabled_key'] );
								}
								// Email opt: defaults to 'checked' when never saved.
								$email_checked = 'checked';
								if ( ! empty( $st['email_option'] ) ) {
									$email_settings = get_option( $st['email_option'] );
									if ( isset( $email_settings['enabled'] ) && ( 'yes' === $email_settings['enabled'] || 1 == $email_settings['enabled'] ) ) { // phpcs:ignore Universal.Operators.StrictComparisons.LooseEqual
										$email_checked = 'checked';
									} elseif ( ! empty( $email_settings ) ) {
										$email_checked = '';
									}
								}
								$row_classes = 'alp-workflow-row';
								if ( ! empty( $st['locked'] ) ) {
									$row_classes .= ' alp-pro-row-locked';
								} elseif ( ! $is_enabled ) {
									$row_classes .= ' is-disabled';
								}
								?>
								<div class="<?php echo esc_attr( $row_classes ); ?>"<?php echo ! empty( $st['locked'] ) ? ' data-pro-locked="1"' : ''; ?>>
									<label class="zui-toggle alp-workflow-row__toggle">
										<?php if ( empty( $st['locked'] ) ) : ?>
											<input type="hidden" name="<?php echo esc_attr( $st['enabled_key'] ); ?>" value="0">
											<input type="checkbox" id="<?php echo esc_attr( $st['enabled_key'] ); ?>" name="<?php echo esc_attr( $st['enabled_key'] ); ?>" value="1" class="zui-toggle__input tgl tgl-flat-alp" <?php echo $is_enabled ? 'checked' : ''; ?>>
										<?php else : ?>
											<input type="checkbox" class="zui-toggle__input tgl tgl-flat-alp" disabled>
										<?php endif; ?>
										<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
									</label>

									<span class="alp-workflow-row__icon" style="background: <?php echo esc_attr( $status_color ); ?>1A; color: <?php echo esc_attr( $status_color ); ?>;">
										<?php echo $st['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static hardcoded SVG markup, no user input. ?>
									</span>

									<div class="alp-workflow-row__id">
										<span class="alp-workflow-row__name-row">
											<span class="alp-workflow-row__name"><?php echo esc_html( $st['label'] ); ?></span>
											<?php if ( ! empty( $st['locked'] ) ) : ?>
												<span class="zui-pro-feature alp-workflow-row__pro">
													<span class="zui-pro-feature__badge"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
													<span class="zui-pro-feature__lock" aria-hidden="true">
														<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
													</span>
												</span>
											<?php endif; ?>
										</span>
										<span class="alp-workflow-row__desc"><?php echo esc_html( $st['desc'] ); ?></span>
									</div>

									<div class="alp-workflow-row__field">
										<span class="alp-workflow-row__field-label"><?php esc_html_e( 'Color', 'advanced-local-pickup-for-woocommerce' ); ?></span>
										<div class="zui-color-input">
											<span class="zui-color-dot" style="--zui-c: <?php echo esc_attr( $status_color ); ?>;">
												<input type="color" value="<?php echo esc_attr( $status_color ); ?>" aria-label="<?php esc_attr_e( 'Choose color', 'advanced-local-pickup-for-woocommerce' ); ?>" tabindex="-1"<?php echo ! empty( $st['locked'] ) ? ' disabled' : ''; ?>>
											</span>
											<?php if ( empty( $st['locked'] ) ) : ?>
												<input type="text" class="zui-input" name="<?php echo esc_attr( $st['color_key'] ); ?>" id="<?php echo esc_attr( $st['color_key'] ); ?>" value="<?php echo esc_attr( $status_color ); ?>" maxlength="7">
											<?php else : ?>
												<input type="text" class="zui-input" value="<?php echo esc_attr( $status_color ); ?>" maxlength="7" disabled>
											<?php endif; ?>
										</div>
									</div>

									<div class="alp-workflow-row__field alp-workflow-row__field--theme">
										<span class="alp-workflow-row__field-label"><?php esc_html_e( 'Theme', 'advanced-local-pickup-for-woocommerce' ); ?></span>
										<div class="zui-select-wrap zui-select-wrap--sm">
											<?php if ( empty( $st['locked'] ) ) : ?>
												<select class="zui-select" id="<?php echo esc_attr( $st['font_key'] ); ?>" name="<?php echo esc_attr( $st['font_key'] ); ?>">
													<option value="#fff" <?php selected( $status_font, '#fff' ); ?>><?php esc_html_e( 'Light', 'advanced-local-pickup-for-woocommerce' ); ?></option>
													<option value="#000" <?php selected( $status_font, '#000' ); ?>><?php esc_html_e( 'Dark', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
											<?php else : ?>
												<select class="zui-select" disabled>
													<option value="#fff" <?php selected( $status_font, '#fff' ); ?>><?php esc_html_e( 'Light', 'advanced-local-pickup-for-woocommerce' ); ?></option>
													<option value="#000" <?php selected( $status_font, '#000' ); ?>><?php esc_html_e( 'Dark', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
											<?php endif; ?>
											<span class="zui-select-chevron">
												<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
											</span>
										</div>
									</div>

									<div class="alp-workflow-row__field alp-workflow-row__field--email">
										<span class="alp-workflow-row__field-label"><?php esc_html_e( 'Email Notification', 'advanced-local-pickup-for-woocommerce' ); ?></span>
										<label class="zui-checkbox send_email_label">
											<?php if ( empty( $st['locked'] ) ) : ?>
												<input type="hidden" name="<?php echo esc_attr( $st['email_key'] ); ?>" value="0">
												<input type="checkbox" class="zui-checkbox__input" name="<?php echo esc_attr( $st['email_key'] ); ?>" id="<?php echo esc_attr( $st['email_key'] ); ?>" value="1" <?php echo esc_html( $email_checked ); ?>>
											<?php else : ?>
												<input type="checkbox" class="zui-checkbox__input" disabled>
											<?php endif; ?>
												<span class="zui-checkbox__box"></span>
											<span><?php esc_html_e( 'Send Email', 'advanced-local-pickup-for-woocommerce' ); ?></span>
										</label>
									</div>

									<?php if ( empty( $st['locked'] ) ) : ?>
										<a class="alp-workflow-row__cog" href="<?php echo esc_url( admin_url() . 'admin.php?page=alp_customizer&email_type=' . $st['customizer_slug'] ); ?>" title="<?php esc_attr_e( 'Customize email template', 'advanced-local-pickup-for-woocommerce' ); ?>">
											<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 1 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 1 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 1 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 1 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
										</a>
									<?php else : ?>
										<span class="alp-workflow-row__cog-spacer" aria-hidden="true"></span>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						</div>

					<?php elseif ( isset( $alp_locked_copy[ $slug ] ) ) : ?>

						<?php $copy = $alp_locked_copy[ $slug ]; ?>
						<div class="zui-lock-section">
							<div class="zui-lock-section__icon">
								<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
							</div>
							<h3 class="zui-lock-section__title">
								<?php echo esc_html( $copy['title'] ); ?>
								<span class="zui-lock-section__badge"><?php esc_html_e( 'PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
							</h3>
							<p class="zui-lock-section__desc"><?php echo esc_html( $copy['desc'] ); ?></p>
							<a href="<?php echo esc_url( $alp_upgrade_url ); ?>" class="zui-btn-primary zui-lock-section__cta" target="_blank" rel="noreferrer noopener"><?php esc_html_e( 'Upgrade to PRO', 'advanced-local-pickup-for-woocommerce' ); ?></a>
						</div>

						<?php /* ---- Preview of PRO fields below the upgrade card ---- */ ?>
						<div class="zui-card zui-lock-section__preview" aria-hidden="true">
							<div class="alp-pro-preview__content">
								<?php if ( 'local-pickup-dashboard' === $slug ) : ?>

									<div class="zui-row">
										<div class="zui-row__head">
											<span class="zui-row__label">
												<?php esc_html_e( 'Order statuses to display', 'advanced-local-pickup-for-woocommerce' ); ?>
												<span class="zui-tooltip" tabindex="-1" role="img" aria-label="<?php esc_attr_e( 'You can choose multiple order statuses for display order in fulfillment dashboard.', 'advanced-local-pickup-for-woocommerce' ); ?>">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
												</span>
											</span>
											<p class="zui-row__desc"><?php esc_html_e( 'Select which order statuses should appear in your monitoring dashboard.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<div class="zui-select-wrap">
												<select class="zui-select" disabled>
													<option><?php esc_html_e( 'Select order statuses…', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
												<span class="zui-select-chevron">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
												</span>
											</div>
										</div>
									</div>

								<?php elseif ( 'checkout-configuration' === $slug ) :
									$preview_toggle_rows = array(
										array(
											'label' => esc_html__( 'Allow force date selection', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Enable customers to forcibly select a pickup date.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Enable map-based location picker', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Display an interactive map interface for picking locations.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Allow mixed orders - local pickup / shipping', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Decide whether customers can choose to ship part of the order and pick up the rest.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Allow pickup per item', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Specify whether a product can be picked up at various locations or if only one is allowed per order.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Display local pick address on cart/checkout', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Show the pickup address on both the cart and checkout pages.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Display location short description on cart/checkout', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Show the pickup location\'s short description directly on the cart and checkout pages.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Display location opening hours on cart/checkout', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Surface the location\'s opening hours so customers can plan their pickup time.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Display location pickup terms / notes on cart/checkout', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Show pickup terms or special notes from the location so customers know what to expect.', 'advanced-local-pickup-for-woocommerce' ),
										),
										array(
											'label' => esc_html__( 'Display location contact info (phone / email) on cart/checkout', 'advanced-local-pickup-for-woocommerce' ),
											'desc'  => esc_html__( 'Display the location\'s phone and email so customers can reach out before pickup.', 'advanced-local-pickup-for-woocommerce' ),
										),
									);
									?>
									<div class="zui-row">
										<div class="zui-row__head">
											<span class="zui-row__label"><?php esc_html_e( 'Select pickup location layout', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<p class="zui-row__desc"><?php esc_html_e( 'Choose the layout for displaying pickup location options on the cart/checkout page.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<div class="zui-select-wrap">
												<select class="zui-select" disabled>
													<option><?php esc_html_e( 'Inline', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
												<span class="zui-select-chevron">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
												</span>
											</div>
										</div>
									</div>
									<div class="zui-row border_1">
										<div class="zui-row__head">
											<span class="zui-row__label"><?php esc_html_e( 'Pickup locations sort by', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<p class="zui-row__desc"><?php esc_html_e( 'Customize the sorting order of pickup locations for the cart/checkout page.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<div class="zui-select-wrap">
												<select class="zui-select" disabled>
													<option><?php esc_html_e( 'Manually', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
												<span class="zui-select-chevron">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
												</span>
											</div>
										</div>
									</div>
									<?php foreach ( $preview_toggle_rows as $row ) : ?>
										<div class="zui-row zui-row--inline border_1">
											<div class="zui-row__head">
												<span class="zui-row__label"><?php echo esc_html( $row['label'] ); ?></span>
												<p class="zui-row__desc"><?php echo esc_html( $row['desc'] ); ?></p>
											</div>
											<div class="zui-row__control">
												<label class="zui-toggle">
													<input type="checkbox" class="zui-toggle__input" disabled>
													<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
												</label>
											</div>
										</div>
									<?php endforeach; ?>

								<?php elseif ( 'products-catalog-options' === $slug ) : ?>

									<div class="zui-row zui-row--inline">
										<div class="zui-row__head">
											<span class="zui-row__label"><?php esc_html_e( 'Display Message on the Product Page', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<p class="zui-row__desc"><?php esc_html_e( 'Showcase availability messages for Local Pickup directly on the product page.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<label class="zui-toggle">
												<input type="checkbox" class="zui-toggle__input" disabled>
												<span class="zui-toggle__track"><span class="zui-toggle__thumb"></span></span>
											</label>
										</div>
									</div>
									<div class="zui-row border_1">
										<div class="zui-row__head">
											<span class="zui-row__label"><?php esc_html_e( 'Product Page Message Location', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<p class="zui-row__desc"><?php esc_html_e( 'Select the placement of the Local Pickup message on the product page.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<div class="zui-select-wrap">
												<select class="zui-select" disabled>
													<option><?php esc_html_e( 'After Add to Cart button', 'advanced-local-pickup-for-woocommerce' ); ?></option>
												</select>
												<span class="zui-select-chevron">
													<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"></polyline></svg>
												</span>
											</div>
										</div>
									</div>
									<div class="zui-row border_1">
										<div class="zui-row__head">
											<span class="zui-row__label"><?php esc_html_e( 'Product Page Message', 'advanced-local-pickup-for-woocommerce' ); ?></span>
											<p class="zui-row__desc"><?php esc_html_e( 'Defaults to "This product is available for Local Pickup."', 'advanced-local-pickup-for-woocommerce' ); ?></p>
										</div>
										<div class="zui-row__control">
											<textarea rows="3" class="zui-input regular-input input-text" disabled placeholder="<?php esc_attr_e( 'This product is available for Local Pickup.', 'advanced-local-pickup-for-woocommerce' ); ?>"></textarea>
										</div>
									</div>

								<?php endif; ?>
							</div>
						</div>

					<?php endif; ?>

				</section>
			<?php endforeach; ?>

			<?php wp_nonce_field( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ); ?>
			<input type="hidden" name="action" value="wclp_setting_form_update">

		</form>

		<?php
		// ---- Library .zui-upsell panel — list mirrors the Go Pro comparison
		// rows in include/views/wclp_gopro_tab.php so the messaging is consistent.
		$alp_upsell_features = array(
			esc_html__( 'Unlimited pickup locations', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Pickup appointments with reservable time slots', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Fulfillment dashboard for staff', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Cart & checkout customization (Inline & Popup)', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Pickup discounts & fees per location', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Mixed orders — pickup + shipping in the same cart', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Curbside pickup flow', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Automations & customer pickup reminders', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Processing LP order status', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Self-serve customer pickup changes', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Per-product / per-category location rules', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Shipping & payment method filtering per location', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'WooCommerce Checkout Block (Gutenberg) support', 'advanced-local-pickup-for-woocommerce' ),
			esc_html__( 'Priority support', 'advanced-local-pickup-for-woocommerce' ),
		);
		$alp_upsell_url = 'https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPFREE&utm_campaign=UpsellPanel';
		?>
		<section class="zui-upsell" aria-labelledby="alp-upsell-title">

			<header class="zui-upsell__head">
				<span class="zui-upsell__emblem" aria-hidden="true">
					<svg class="zui-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 7 4.41-4.41A2 2 0 0 1 7.83 2h8.34a2 2 0 0 1 1.42.59L22 7"/><path d="M4 12v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8"/><path d="M15 22v-4a2 2 0 0 0-2-2h-2a2 2 0 0 0-2 2v4"/><path d="M2 7h20"/></svg>
				</span>
				<div class="zui-upsell__head-text">
					<span class="zui-upsell__eyebrow"><?php esc_html_e( 'ALP PRO', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<h3 class="zui-upsell__title" id="alp-upsell-title"><?php esc_html_e( 'Unlock Advanced Local Pickup with ALP PRO', 'advanced-local-pickup-for-woocommerce' ); ?></h3>
					<p class="zui-upsell__sub"><?php esc_html_e( 'Stop limiting your store with a single pickup location and basic statuses. Upgrade to ALP PRO for multiple locations, appointments, a fulfillment dashboard, mixed orders, curbside pickup, and more — beyond the basics included in the free plugin.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				</div>
			</header>

			<ul class="zui-upsell__features">
				<?php foreach ( $alp_upsell_features as $alp_feat ) : ?>
					<li class="zui-upsell__feature">
						<span class="zui-upsell__check" aria-hidden="true">
							<svg class="zui-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
						</span>
						<span class="zui-upsell__label"><?php echo esc_html( $alp_feat ); ?></span>
					</li>
				<?php endforeach; ?>
			</ul>

			<footer class="zui-upsell__foot">
				<div class="zui-upsell__offer">
					<span class="zui-upsell__offer-label"><?php esc_html_e( 'Launch offer', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					<span class="zui-upsell__offer-body">
						<?php esc_html_e( 'Get 20% off — use code', 'advanced-local-pickup-for-woocommerce' ); ?>
						<span class="zui-upsell__code">ALPPRO20</span>
						<?php esc_html_e( 'at checkout.', 'advanced-local-pickup-for-woocommerce' ); ?>
					</span>
					<span class="zui-upsell__offer-note">★ <?php esc_html_e( 'for new customers only', 'advanced-local-pickup-for-woocommerce' ); ?></span>
				</div>
				<a class="zui-upsell__cta" href="<?php echo esc_url( $alp_upsell_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php esc_html_e( 'Upgrade to ALP PRO', 'advanced-local-pickup-for-woocommerce' ); ?> <span aria-hidden="true">&rarr;</span>
				</a>
			</footer>

		</section>

	</main>

</div>
