<?php
	// Add nonce to the dismissable URL
	$nonce = wp_create_nonce('alp_pro_dismiss_notice');
	$dismissable_url = esc_url(add_query_arg(['alp-pro-settings-ignore-notice' => 'true', 'nonce' => $nonce]));
?>

<div class="admin-message-panel">
	<div class="admin-message-row is-dismissible">
		<h1 class="admin_message_header"><?php esc_html_e('🚀 Supercharge Your Local Pickup with Zorem Local Pickup PRO!', 'zorem-local-pickup'); ?></h1>
		<p>Enhance your local pickup workflow with <a href="https://www.zorem.com/product/advanced-local-pickup-pro/">Zorem Local Pickup PRO!</a> Set up multiple pickup locations, offer pickup appointments, customize instructions, and manage orders efficiently with a fulfillment dashboard.</p>
		<p><strong>🎉 Get 20% Off your first order!</strong> Use code <strong>ALPPRO20</strong> at checkout.</p>
		<a href="https://www.zorem.com/product/advanced-local-pickup-pro/" class="button-primary btn_pro_notice" target="_blank"><?php esc_html_e('UPGRADE NOW', 'zorem-local-pickup'); ?></a>
		<!-- <a href="<?php //esc_html_e( $dismissable_url ); ?>" class="button-secondary btn_pro_notice"><?php //esc_html_e('Dismiss', 'zorem-local-pickup'); ?></a> -->
		<p><strong>★</strong> for new customers only</p>
	</div>
</div>