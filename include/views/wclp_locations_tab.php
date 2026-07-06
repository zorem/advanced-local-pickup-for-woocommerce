<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound,WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound,WordPress.NamingConventions.PrefixAllGlobals.DynamicHooknameFound -- View template file; variable and hook names are established by the surrounding controller and are part of the plugin API.
/**
 * Pickup Location tab — ZUI redesign for ALP Free.
 *
 * Free supports a single pickup location. This file is a thin wrapper that
 * either:
 *   - Loads `wclp-edit-location-form.php` for the (only) existing location, OR
 *   - Renders an "Add your pickup location" empty state if no row exists yet.
 *
 * The edit form handles its own save flow (`wclp_location_edit_form_update`).
 *
 * @package WooAdvancedLocalPickup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$alp_locations_data = wc_local_pickup()->admin->get_data();
$alp_has_location   = ! empty( $alp_locations_data ) && is_array( $alp_locations_data );

if ( $alp_has_location ) {
	// Free tier: there is always exactly one location.  The edit form reads
	// $_REQUEST['id'] — surface the row's id so the form can hydrate.
	$alp_first_location = reset( $alp_locations_data );
	if ( is_object( $alp_first_location ) && isset( $alp_first_location->id ) ) {
		if ( ! isset( $_REQUEST['id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$_REQUEST['id'] = (string) $alp_first_location->id; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		}
	}
}
?>
<?php if ( $alp_has_location ) : ?>
	<?php require_once __DIR__ . '/wclp-edit-location-form.php'; ?>
<?php else : ?>
	<div class="zui-layout zui-layout--full alp-edit-layout">
		<main class="zui-content zui-content--full">
			<div class="zui-card alp-locations-empty">
				<span class="alp-locations-empty__icon" aria-hidden="true">
					<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
				</span>
				<h2 class="alp-locations-empty__title"><?php esc_html_e( 'Add your pickup location', 'advanced-local-pickup-for-woocommerce' ); ?></h2>
				<p class="alp-locations-empty__desc"><?php esc_html_e( 'Set up where customers will come to collect their orders. You can configure address, business hours, and special pickup instructions.', 'advanced-local-pickup-for-woocommerce' ); ?></p>
				<form method="post" class="alp-locations-empty__form">
					<input type="hidden" name="action" value="wclp_location_edit_form_update">
					<input type="hidden" name="id" value="0">
					<?php wp_nonce_field( 'wclp_location_edit_form_action', 'wclp_location_edit_form_nonce_field' ); ?>
					<button type="submit" class="zui-btn-primary alp-btn-add-location">
						<svg class="zui-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
						<span><?php esc_html_e( 'Add Pickup Location', 'advanced-local-pickup-for-woocommerce' ); ?></span>
					</button>
				</form>
			</div>
		</main>
	</div>
<?php endif; ?>
