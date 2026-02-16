<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$more_plugins = array(
	0 => array(
		'title' => 'Advanced Shipment Tracking',
		'description' => __( 'AST PRO provides powerful features to easily add tracking info to WooCommerce orders, automate the fulfillment workflows and keep your customers happy and informed. AST allows you to easily add tracking and fulfill your orders straight from the Orders page, while editing orders, and allows customers to view the tracking i from the View Order page.', 'zorem-local-pickup' ),
		'url' => 'https://www.zorem.com/product/woocommerce-advanced-shipment-tracking/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'ast-45.png',
		'height' => '45px',
		'file' => 'ast-pro/ast-pro.php'
	),
	1 => array(
		'title' => 'TrackShip for WooCommerce',
		'description' => __( 'Take control of your post-shipping workflows, reduce time spent on customer service and provide a superior post-purchase experience to your customers. Beyond automatic shipment tracking, TrackShip brings a branded tracking experience into your store.', 'zorem-local-pickup' ),
		'url' => 'https://wordpress.org/plugins/trackship-for-woocommerce/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'ts-45.png',
		'height' => '45px',
		'file' => 'trackship-for-woocommerce/trackship-for-woocommerce.php'
	),
	2 => array(
		'title' => 'Country Based Restrictions',
		'description' => __( 'The country-based restrictions plugin by zorem works by the WooCommerce Geolocation or the shipping country added by the customer and allows you to restrict products on your store to sell or not to sell to specific countries.', 'zorem-local-pickup' ),
		'url' => 'https://www.zorem.com/product/country-based-restriction-pro/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'cbr-icon.png',
		'height' => '45px',
		'file' => 'country-based-restriction-pro-addon/country-based-restriction-pro-addon.php'
	),
	3 => array(
		'title' => 'Customer Email Verification',
		'description' => __( 'Customer Email Verification helps WooCommerce store owners reduce registration spam and fraudulent orders by requiring customers to verify their email address when registering an account or before placing an order.', 'zorem-local-pickup' ),
		'url' => 'https://www.zorem.com/product/customer-email-verification/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'cev-45.png',
		'height' => '45px',
		'file' => 'customer-email-verification-pro/customer-email-verification-pro.php'
	),
	4 => array(
		'title' => 'SMS for WooCommerce',
		'description' => __( 'Keep your customers informed by sending them automated SMS text messages with order and delivery updates. You can send SMS notifications to customers when the order status is updated or when the shipment is out for delivery and more.', 'zorem-local-pickup' ),
		'url' => 'https://www.zorem.com/product/sms-for-woocommerce/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'sms-45.png',
		'height' => '45px',
		'file' => 'sms-for-woocommerce/sms-for-woocommerce.php'
	),
	5 => array(
		'title' => 'Email Reports for WooCommerce',
		'description' => __( 'Sales Report Email Pro helps you understand how well your store is performing and how your products are selling by sending daily, weekly, or monthly sales reports directly from your WooCommerce store to your email.', 'zorem-local-pickup' ),
		'url' => 'https://www.zorem.com/product/email-reports-for-woocommerce/?utm_source=wp-admin&utm_medium=alp-addons&utm_campaign=add-ons',
		'image' => 'sre-45.png',
		'height' => '45px',
		'file' => 'sales-report-email-pro/sales-report-email-pro.php'
	),
);

$plugin_url = wc_local_pickup()->plugin_dir_url();
?>
<section id="wclp_content4" class="wclp_tab_section">
	<div class="d_table addons_page_dtable" style="">
		<section id="content_tab_addons" class="<?php if ( class_exists( 'Zorem_Local_Pickup_Pro' ) ) { echo 'inner_tab_section'; } ?>">

		<!-- ALP Go Pro v2 layout -->
		<div class="alp-go-pro-v2">
			<!-- Hero -->
			<div class="gopro-hero">
				<h1><?php esc_html_e( 'Take Your Local Pickup to the Next Level', 'zorem-local-pickup' ); ?></h1>
				<p>
				<?php 
				echo wp_kses_post(
					__( 'Stop limiting your store with basic pickup. Switch from a <strong>basic setup</strong> to a <a href="https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPPRO&utm_campaign=go-pro" target="_blank">fully advanced pickup powerhouse</a>.', 'zorem-local-pickup' )
				);
				?>
				</p>
			</div>

			<!-- Feature comparison -->
			<div class="gopro-comparison">
				<div class="gopro-comp-header">
					<div class="gopro-comp-header-label"><?php esc_html_e( 'Feature Comparison', 'zorem-local-pickup' ); ?></div>
					<div class="gopro-comp-header-col">
						<span class="comp-header-badge badge-current"><?php esc_html_e( 'Current', 'zorem-local-pickup' ); ?></span>
						<span class="comp-header-title"><?php esc_html_e( 'ALP FREE', 'zorem-local-pickup' ); ?></span>
					</div>
					<div class="gopro-comp-header-col is-pro">
						<span class="comp-header-badge badge-recommended"><?php esc_html_e( 'Recommended', 'zorem-local-pickup' ); ?></span>
						<span class="comp-header-title"><?php esc_html_e( 'ALP PRO', 'zorem-local-pickup' ); ?></span>
					</div>
				</div>
				<?php
				$comp_features = array(
					array( 'title' => __( 'Pickup Location', 'zorem-local-pickup' ), 'desc' => __( 'Set up and manage pickup locations for your store.', 'zorem-local-pickup' ), 'free' => 'limited', 'free_label' => __( 'Single Location', 'zorem-local-pickup' ), 'pro' => __( 'Multiple Locations', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Custom Order Statuses', 'zorem-local-pickup' ), 'desc' => __( 'Add custom order statuses like Ready for Pickup and Picked Up.', 'zorem-local-pickup' ), 'free' => 'limited', 'free_label' => __( 'Basic', 'zorem-local-pickup' ), 'pro' => __( 'Full Workflow', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Processing LP Status', 'zorem-local-pickup' ), 'desc' => __( 'Separate local pickup processing from shipping orders.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Full Control', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Local Pickup Dashboard', 'zorem-local-pickup' ), 'desc' => __( 'Dedicated fulfillment dashboard for managing local pickup orders.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Full Dashboard', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Local Pickup Appointments', 'zorem-local-pickup' ), 'desc' => __( 'Let customers schedule pickup appointments with date and time slots.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Full Scheduling', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Cart & Checkout Options', 'zorem-local-pickup' ), 'desc' => __( 'Display pickup instructions, location layout, and sorting at checkout.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Full Customization', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Mixed Orders', 'zorem-local-pickup' ), 'desc' => __( 'Allow local pickup and other shipping methods on the same order.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Per-Item Pickup', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Pickup Discounts & Fees', 'zorem-local-pickup' ), 'desc' => __( 'Apply a discount or a fee for Local Pickup orders.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Full Control', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Product Page Messages', 'zorem-local-pickup' ), 'desc' => __( 'Display local pickup availability messages on product pages.', 'zorem-local-pickup' ), 'free' => 'cross', 'free_label' => __( 'Not Available', 'zorem-local-pickup' ), 'pro' => __( 'Custom Messages', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Email Notifications', 'zorem-local-pickup' ), 'desc' => __( 'Send customized email notifications for pickup order status changes.', 'zorem-local-pickup' ), 'free' => 'limited', 'free_label' => __( 'Basic', 'zorem-local-pickup' ), 'pro' => __( 'Full Customization', 'zorem-local-pickup' ) ),
					array( 'title' => __( 'Premium Support', 'zorem-local-pickup' ), 'desc' => __( 'Priority ticket handling and dedicated help center access.', 'zorem-local-pickup' ), 'free' => 'limited', 'free_label' => __( 'Standard Only', 'zorem-local-pickup' ), 'pro' => __( 'Priority Support', 'zorem-local-pickup' ) ),
				);
				foreach ( $comp_features as $feat ) :
					?>
				<div class="gopro-comp-row">
					<div class="gopro-comp-feature">
						<strong><?php echo esc_html( $feat['title'] ); ?></strong>
						<span><?php echo esc_html( $feat['desc'] ); ?></span>
					</div>
					<div class="gopro-comp-cell">
						<?php if ( 'check' === $feat['free'] ) : ?>
							<span class="comp-icon icon-check"><svg fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
						<?php else : ?>
							<span class="comp-icon icon-x"><svg fill="none" viewBox="0 0 24 24" stroke="#fff" stroke-width="2.5"><line x1="16" y1="8" x2="8" y2="16"/><line x1="8" y1="8" x2="16" y2="16"/></svg></span>
						<?php endif; ?>
						<?php if ( ! empty( $feat['free_label'] ) ) : ?>
							<span class="comp-status"><?php echo esc_html( $feat['free_label'] ); ?></span>
						<?php endif; ?>
					</div>
					<div class="gopro-comp-cell is-pro">
						<span class="comp-icon icon-check"><svg fill="none" viewBox="0 0 24 24" stroke="#16a34a" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg></span>
						<span class="comp-status"><?php echo esc_html( $feat['pro'] ); ?></span>
					</div>
				</div>
				<?php endforeach; ?>
			</div>

			<!-- CTA -->
			<div class="gopro-cta">
				<a href="https://www.zorem.com/product/zorem-local-pickup-pro/?utm_source=wp-admin&utm_medium=ALPPRO&utm_campaign=go-pro" class="gopro-cta-btn" target="_blank"><?php esc_html_e( 'GET STARTED WITH PRO', 'zorem-local-pickup' ); ?></a>
				<p class="gopro-cta-sub"><?php esc_html_e( 'Join store owners optimizing their local pickup workflow', 'zorem-local-pickup' ); ?></p>
			</div>
		</div>

		<!-- Powerful Add-ons -->
		<div class="alp-go-pro-v2">
			<div class="gopro-addons">
				<div class="gopro-addons-header">
					<div>
						<h2><?php esc_html_e( 'Powerful Add-ons', 'zorem-local-pickup' ); ?></h2>
						<p><?php esc_html_e( "Extend your store's capabilities with our ecosystem", 'zorem-local-pickup' ); ?></p>
					</div>
				</div>
				<div class="section-content wclp_tab_inner_container">
					<div class="plugins_section free_plugin_section">
						<?php
						$icon_colors = array( '#ecfdf5', '#ede9fe', '#eff6ff', '#fef3c7', '#fce7f3', '#e0f2fe' );
						foreach ( $more_plugins as $index => $addon ) : 
						$icon_bg = isset( $icon_colors[ $index ] ) ? $icon_colors[ $index ] : '#f3f4f6';
						?>
							<div class="single_plugin">
								<div class="free_plugin_inner">
									<div class="plugin_image">
										<div class="gopro-addon-card-icon" style="background:<?php echo esc_attr( $icon_bg ); ?>">
											<img src="<?php echo esc_url( $plugin_url . 'assets/images/' . $addon['image'] ); ?>" height="<?php echo esc_attr( $addon['height'] ); ?>">
										</div>
										<h3 class="plugin_title"><?php echo esc_html( $addon['title'] ); ?></h3>
									</div>
									<div class="plugin_description wclp-btn">
										<p><?php echo esc_html( $addon['description'] ); ?></p>
										<?php if ( is_plugin_active( $addon['file'] ) ) : ?>
											<a href="#" class="button alp-addon-learn-more-btn is-active" style="pointer-events:none;color:#16a34a;border-color:#16a34a;"><?php esc_html_e( 'Active', 'zorem-local-pickup' ); ?></a>
										<?php else : ?>
											<a href="<?php echo esc_url( $addon['url'] ); ?>" class="button alp-addon-learn-more-btn" target="_blank"><?php esc_html_e( 'Learn More', 'zorem-local-pickup' ); ?></a>
										<?php endif; ?>
									</div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>

		</section>
	</div>
</section>
