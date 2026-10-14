<?php
/**
 * Shared helpers for ALP Free — the in-plugin upgrade UTM convention.
 *
 * Mirrors the AST Free pattern (includes/class-wc-settings-helpers.php): every
 * outbound sales link in the plugin goes through one builder so placements
 * can't drift into new spellings again.
 *
 * @package WooAdvancedLocalPickup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add the in-plugin UTM convention to a zorem.com link.
 *
 * Source and medium are FIXED — GA4's "In-Plugin" channel group matches on
 * them — and the placement goes in utm_content, never in medium. (Custom
 * mediums like "ALPFREE" / "alp-addons" are exactly why older sessions landed
 * in GA4's "Unassigned" bucket.)
 *
 * @param string $url      Destination URL.
 * @param string $content  Placement slug, unique per link location (e.g. plugin-setting).
 * @param string $campaign Campaign slug.
 * @return string
 */
if ( ! function_exists( 'alp_utm_url' ) ) {
	function alp_utm_url( $url, $content, $campaign = 'upgrade-to-pro' ) {
		return add_query_arg(
			array(
				'utm_source'   => 'alp-free',
				'utm_medium'   => 'in-plugin',
				'utm_campaign' => $campaign,
				'utm_content'  => $content,
			),
			$url
		);
	}
}

/**
 * Advanced Local Pickup Pro product-page link for one upgrade placement.
 *
 * Points at the LIVE product URL (zorem-local-pickup-pro). The newer
 * advanced-local-pickup-pro alias 301-redirects, and that redirect STRIPS the
 * query string — which would drop our UTM params and send the session back to
 * GA4's "Unassigned" bucket (the very thing this convention fixes). The GA4
 * item name still comes from the product itself ("Advanced Local Pickup Pro"),
 * not the URL slug, so attribution stays on the current product.
 *
 * NOTE: once the website makes advanced-local-pickup-pro the canonical URL
 * (no redirect, or a redirect that preserves the query string — part of the
 * "Fix GA4 data quality" task), switch this back to that slug.
 *
 * @param string $content Placement slug for utm_content.
 * @return string
 */
if ( ! function_exists( 'alp_pro_upgrade_url' ) ) {
	function alp_pro_upgrade_url( $content ) {
		return alp_utm_url( 'https://www.zorem.com/product/zorem-local-pickup-pro/', $content );
	}
}
