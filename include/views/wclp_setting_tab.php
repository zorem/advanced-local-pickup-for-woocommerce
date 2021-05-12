<section id="wclp_content1" class="wclp_tab_section">
    <div class="wclp_tab_inner_container">
		<div class="wclp_outer_form_table subtab_inner_container">
			<input id="tab_general_settings" type="radio" name="alp_general_settings_tabs" class="inner_tab_input" data-subtab="general-settings" checked>
			<label for="tab_general_settings" class="inner_tab_label"><?php _e( 'General Settings', 'woo-advanced-shipment-tracking' ); ?></label>				
			
			<input id="tab_order_status" type="radio" name="alp_general_settings_tabs" class="inner_tab_input" data-subtab="custom-order-status">
			<label for="tab_order_status" class="inner_tab_label"><?php _e( 'Custom Order Statuses', 'woo-advanced-shipment-tracking' ); ?></label>
			<div class="tabs_inner_section" id="content_general_settings">
				<form method="post" id="wclp_setting_tab_form">
					<table class="form-table html-layout-2">
						<tbody>
							<?php $this->get_html2( $this->wclp_general_setting_fields_func() ); ?>
						</tbody>
					</table>
					<?php 
						// local pickup setting html hook
						do_action('wclp_local_pickup_setting_html_hook');
					?>
					<?php 
						// local pickup setting html hook
						do_action('wclp_local_pickup_message_setting_html_hook');
					?>
					<div class="submit wclp-btn">
						<div class="spinner workflow_spinner" style="float:none"></div>
						<button name="save" class="wclp-save button-primary woocommerce-save-button" type="submit" value="Save changes"><?php _e( 'Save changes', 'woocommerce' ); ?></button>
						<?php wp_nonce_field( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ); ?>
						<input type="hidden" name="action" value="wclp_setting_form_update">
					</div>
				</form>
			</div>				
			<div class="tabs_inner_section" id="content_order_status">
				<form method="post" id="wclp_osm_tab_form">			
					<table class="form-table order-status-table">
						<tbody>
							<tr valign="top" class="ready_pickup_row <?php if(!get_option('wclp_status_ready_pickup')){echo 'disable_row'; } ?>">
								<td class="forminp">
									<span class="mdl-list__item-secondary-action">
										<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wclp_status_ready_pickup">
											<input type="hidden" name="wclp_status_ready_pickup" value="0"/>
											<input type="checkbox" id="wclp_status_ready_pickup" name="wclp_status_ready_pickup" class="mdl-switch__input order_status_toggle" <?php if(get_option('wclp_status_ready_pickup')){echo 'checked'; } ?> value="1"/>
										</label>
									</span>
								</td>
								<td class="forminp status-label-column" style="width: 125px;">
									<span class="order-label wc-ready-pickup" style="background:<?php echo get_option('wclp_ready_pickup_status_label_color', '#8bc34a');?>;color:<?php echo get_option('wclp_ready_pickup_status_label_font_color', '#fff');?>">
										<?php _e( 'Ready for pickup', 'advanced-local-pickup-for-woocommerce' ); ?>
									</span>
								</td>								
								<td class="forminp">							
									<?php
									$wclp_enable_ready_pickup_email = get_option('woocommerce_customer_ready_pickup_order_settings');
									if(isset($wclp_enable_ready_pickup_email) && !empty($wclp_enable_ready_pickup_email)){
										if($wclp_enable_ready_pickup_email['enabled'] == 'yes' || $wclp_enable_ready_pickup_email['enabled'] == 1){
											$ready_pickup_checked = 'checked';
										} else{
											$ready_pickup_checked = '';									
										}
									} else {
										$ready_pickup_checked = 'checked';
									}
									?>
									<fieldset>
										<input class="input-text regular-input " type="text" name="wclp_ready_pickup_status_label_color" id="wclp_ready_pickup_status_label_color" style="" value="<?php echo get_option('wclp_ready_pickup_status_label_color', '#8bc34a' )?>" placeholder="">
										<select class="select" id="wclp_ready_pickup_status_label_font_color" name="wclp_ready_pickup_status_label_font_color">	
											<option value="#fff" <?php if(get_option('wclp_ready_pickup_status_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
											<option value="#000" <?php if(get_option('wclp_ready_pickup_status_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
										</select>
										<label class="send_email_label">
											<input type="hidden" name="wclp_enable_ready_pickup_email" value="0"/>
											<input type="checkbox" name="wclp_enable_ready_pickup_email" id="wclp_enable_ready_pickup_email" <?php echo $ready_pickup_checked; ?> value="1"><?php _e( 'Send Email', 'advanced-local-pickup-for-woocommerce' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo wclp_ready_pickup_customizer_email::get_customizer_url('customer_email_notifications'); ?>"><?php _e( 'edit email', 'advanced-local-pickup-for-woocommerce' ) ?></a>
									</fieldset>
								</td>
							</tr>					
							<tr valign="top" class="picked_up_row <?php if(!get_option('wclp_status_picked_up')){echo 'disable_row'; } ?>">
								<td class="forminp">
									<span class="mdl-list__item-secondary-action">
										<label class="mdl-switch mdl-js-switch mdl-js-ripple-effect" for="wclp_status_picked_up">
											<input type="hidden" name="wclp_status_picked_up" value="0"/>
											<input type="checkbox" id="wclp_status_picked_up" name="wclp_status_picked_up" class="mdl-switch__input order_status_toggle" <?php if(get_option('wclp_status_picked_up')){echo 'checked'; } ?> value="1"/>
										</label>
									</span>
								</td>
								<td class="forminp status-label-column" style="width: 125px;">
									<span class="order-label wc-pickup" style="background:<?php echo get_option('wclp_pickup_status_label_color', '#2196f3');?>;color:<?php echo get_option('wclp_pickup_status_label_font_color', '#fff');?>">
										<?php _e( 'Picked up', 'advanced-local-pickup-for-woocommerce' ); ?>
									</span>
								</td>								
								<td class="forminp">							
									<?php
									$wclp_enable_pickup_email = get_option('woocommerce_customer_pickup_order_settings');
									if(isset($wclp_enable_pickup_email) && !empty($wclp_enable_pickup_email)){
										if($wclp_enable_pickup_email['enabled'] == 'yes' || $wclp_enable_pickup_email['enabled'] == 1){
											$pickup_checked = 'checked';
										} else{
											$pickup_checked = '';									
										}
									} else {
										$pickup_checked = 'checked';
									}
									?>
									<fieldset>
										<input class="input-text regular-input " type="text" name="wclp_pickup_status_label_color" id="wclp_pickup_status_label_color" style="" value="<?php echo get_option('wclp_pickup_status_label_color', '#2196f3')?>" placeholder="">
										<select class="select" id="wclp_pickup_status_label_font_color" name="wclp_pickup_status_label_font_color">	
											<option value="#fff" <?php if(get_option('wclp_pickup_status_label_font_color') == '#fff'){ echo 'selected'; }?>><?php _e( 'Light Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
											<option value="#000" <?php if(get_option('wclp_pickup_status_label_font_color') == '#000'){ echo 'selected'; }?>><?php _e( 'Dark Font', 'advanced-local-pickup-for-woocommerce' ); ?></option>
										</select>
										<label class="send_email_label">
											<input type="hidden" name="wclp_enable_pickup_email" value="0"/>
											<input type="checkbox" name="wclp_enable_pickup_email" id="wclp_enable_pickup_email" <?php echo $pickup_checked; ?> value="1"><?php _e( 'Send Email', 'advanced-local-pickup-for-woocommerce' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo wclp_pickup_customizer_email::get_customizer_url('customer_email_notifications'); ?>"><?php _e( 'edit email', 'advanced-local-pickup-for-woocommerce' ) ?></a>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
					<?php wp_nonce_field( 'wclp_osm_form_action', 'wclp_osm_form_nonce_field' ); ?>
					<input type="hidden" name="action" value="wclp_osm_form_update">
				</form>
			</div>
    	</div>
	</div>
</section>