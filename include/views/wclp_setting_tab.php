<section id="wclp_content1" class="wclp_tab_section">
	<div class="wclp_tab_inner_container">
		<div class="wclp_outer_form_table">
			<form method="post" id="wclp_setting_tab_form" class="wclp_setting_tab_form">
				<div class="accordion heading">
					<label>
						<?php esc_html_e( 'Display options', 'zorem-local-pickup' ); ?>
						<span class="submit wclp-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="wclp-save button-primary woocommerce-save-button" type="submit" value="Save & close"><?php esc_html_e( 'Save & close', 'zorem-local-pickup' ); ?></button>
							<?php wp_nonce_field( 'wclp_setting_form_action', 'wclp_setting_form_nonce_field' ); ?>
							<input type="hidden" name="action" value="wclp_setting_form_update">
						</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel">
					<table class="form-table html-layout-2">
						<tbody>
							<?php $this->get_html2( $this->wclp_general_setting_fields_func() ); ?>
						</tbody>
					</table>
					<!-- PRO Display Options fields (locked) -->
					<table class="form-table html-layout-2 alp-pro-locked-fields">
						<tbody>
							<tr valign="top" class="alp-pro-feature-row">
								<td colspan="2" style="padding: 12px 15px;">
									<label style="display:inline-flex;align-items:center;gap:6px;">
										<input type="checkbox" disabled class="pro_feature"/>
										<span class="pro_feature"><?php esc_html_e( 'Show Pickup Items in Pickup Information', 'zorem-local-pickup' ); ?></span>
										<span class="woocommerce-help-tip tipTip pro_feature" title="<?php esc_attr_e( 'Enable this option to display a list of pickup items in the pickup information section.', 'zorem-local-pickup' ); ?>"></span>
									</label>
									<span class="alp-pro-row-actions"><span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span><span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span></span>
								</td>
							</tr>
							<tr valign="top" class="alp-pro-feature-row">
								<td colspan="2" style="padding: 12px 15px;">
									<label style="display:inline-flex;align-items:center;gap:6px;">
										<input type="checkbox" disabled class="pro_feature"/>
										<span class="pro_feature"><?php esc_html_e( 'Allow customers to change pickup details after placing the order', 'zorem-local-pickup' ); ?></span>
										<span class="woocommerce-help-tip tipTip pro_feature" title="<?php esc_attr_e( 'Allows customers to update the pickup location or pickup time from their order details page after checkout.', 'zorem-local-pickup' ); ?>"></span>
									</label>
									<span class="alp-pro-row-actions"><span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span><span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span></span>
								</td>
							</tr>
							<tr valign="top" class="alp-pro-feature-row">
								<td colspan="2" style="padding: 8px 15px 8px 40px;">
									<label style="display:inline-flex;align-items:center;gap:6px;">
										<input type="checkbox" disabled class="pro_feature"/>
										<span class="pro_feature"><?php esc_html_e( 'Limit appointment changes before pickup', 'zorem-local-pickup' ); ?></span>
									</label>
									<span class="alp-pro-row-actions"><span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span><span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span></span>
								</td>
							</tr>
							<tr valign="top" class="alp-pro-feature-row">
								<td colspan="2" style="padding: 12px 15px;">
									<div style="display:flex;align-items:center;gap:10px;">
										<span class="pro_feature"><?php esc_html_e( 'Allow appointment changes up to', 'zorem-local-pickup' ); ?></span>
										<input type="number" value="2" disabled class="pro_feature" style="width:60px;">
										<select disabled class="pro_feature" style="width:120px;"><option><?php esc_html_e( 'Minutes', 'zorem-local-pickup' ); ?></option></select>
									</div>
									<span class="alp-pro-row-actions"><span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span><span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span></span>
								</td>
							</tr>
							<tr valign="top" class="alp-pro-feature-row">
								<td colspan="2" style="padding: 12px 15px;margin:0;">
									<div style="display:flex;align-items:flex-start;justify-content:space-between;width:100%;">
										<div>
											<label class="pro_feature" style="font-weight:600;display:flex;align-items:center;gap:4px;margin-bottom:8px;"><?php esc_html_e( 'Additional order emails display', 'zorem-local-pickup' ); ?>
												<span class="woocommerce-help-tip tipTip pro_feature" title="<?php esc_attr_e( 'You can choose multiple order statuses for display pickup instruction in emails.', 'zorem-local-pickup' ); ?>"></span>
											</label>
											<div class="alp-pro-tags-input pro_feature">
												<span class="alp-pro-tag">&times; <?php esc_html_e( 'Processing', 'zorem-local-pickup' ); ?></span>
												<span class="alp-pro-tag">&times; <?php esc_html_e( 'Processing LP', 'zorem-local-pickup' ); ?></span>
											</div>
										</div>
										<span class="alp-pro-row-actions"><span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span><span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span></span>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="accordion heading">
					<label>
						<?php esc_html_e( 'Local pickup workflow', 'zorem-local-pickup' ); ?>
						<span class="submit wclp-btn">
							<div class="spinner workflow_spinner" style="float:none"></div>
							<button name="save" class="wclp-save button-primary woocommerce-save-button" type="submit" value="Save changes"><?php esc_html_e( 'Save & close', 'zorem-local-pickup' ); ?></button>
							<input type="hidden" name="action" value="wclp_setting_form_update">
						</span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
				<div class="panel">
					<table class="form-table order-status-table">
						<tbody>
							<!-- PRO: Processing LP status (locked) -->
							<tr valign="top" class="processing_lp_row alp-pro-feature-row disable_row">
								<td class="">
									<span class="tgl-btn-parent" style="">
										<input type="checkbox" class="tgl tgl-flat-alp pro_feature" disabled/>
										<label class="tgl-btn pro_feature"></label>
									</span>
								</td>
								<td class="status-label-column" style="width: 130px;">
									<span class="order-label pro_feature" style="background:#ffc700;color:#fff">
										<?php esc_html_e( 'Processing LP', 'zorem-local-pickup' ); ?>
									</span>
								</td>
								<td class="">
									<fieldset>
										<input class="input-text regular-input pro_feature" type="text" value="#ffc700" disabled>
										<select class="select pro_feature" disabled><option><?php esc_html_e( 'Light Font', 'zorem-local-pickup' ); ?></option></select>
									</fieldset>
								</td>
								<td class="" style="text-align: right;">
									<span class="alp-pro-row-actions">
										<span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span>
										<span class="alp-pro-lock"><span class="dashicons dashicons-lock"></span></span>
									</span>
								</td>
							</tr>
							<tr valign="top" class="ready_pickup_row <?php echo ( !get_option('wclp_status_ready_pickup') ) ? 'disable_row' : ''; ?>">
								<td class="">
									<span class="tgl-btn-parent" style="">
										<input type="hidden" name="wclp_status_ready_pickup" value="0">
										<input type="checkbox" id="wclp_status_ready_pickup" name="wclp_status_ready_pickup" class="tgl tgl-flat-alp" <?php echo ( get_option('wclp_status_ready_pickup') ) ? 'checked' : ''; ?> value="1"/>
										<label class="tgl-btn" for="wclp_status_ready_pickup"></label>
									</span>
								</td>
								<td class=" status-label-column" style="width: 130px;">
									<span class="order-label wc-ready-pickup" style="background:<?php echo esc_html(get_option('wclp_ready_pickup_status_label_color', '#8bc34a')); ?>;color:<?php echo esc_html(get_option('wclp_ready_pickup_status_label_font_color', '#fff')); ?>">
										<?php esc_html_e( 'Ready for pickup', 'zorem-local-pickup' ); ?>
									</span>
								</td>
								<td class="">
									<fieldset>
										<input class="input-text regular-input " type="text" name="wclp_ready_pickup_status_label_color" id="wclp_ready_pickup_status_label_color" style="" value="<?php echo esc_html(get_option('wclp_ready_pickup_status_label_color', '#8bc34a' )); ?>" placeholder="">
										<select class="select" id="wclp_ready_pickup_status_label_font_color" name="wclp_ready_pickup_status_label_font_color">	
											<option value="#fff" <?php echo ( '#fff' == get_option('wclp_ready_pickup_status_label_font_color') ) ? 'selected' : ''; ?>><?php esc_html_e( 'Light Font', 'zorem-local-pickup' ); ?></option>
											<option value="#000" <?php echo ( '#000' == get_option('wclp_ready_pickup_status_label_font_color') ) ? 'selected' : ''; ?>><?php esc_html_e( 'Dark Font', 'zorem-local-pickup' ); ?></option>
										</select>
									</fieldset>
								</td>								
								<td class="" style="text-align: right;">							
									<?php
									$wclp_enable_ready_pickup_email = get_option('woocommerce_customer_ready_pickup_order_settings');
									if (isset($wclp_enable_ready_pickup_email) && !empty($wclp_enable_ready_pickup_email)) {
										if ('yes' == $wclp_enable_ready_pickup_email['enabled'] || 1 == $wclp_enable_ready_pickup_email['enabled']) {
											$ready_pickup_checked = 'checked';
										} else {
											$ready_pickup_checked = '';									
										}
									} else {
										$ready_pickup_checked = 'checked';
									}
									?>
									<fieldset>
										<label class="send_email_label">
											<input type="hidden" name="wclp_enable_ready_pickup_email" value="0"/>
											<input type="checkbox" name="wclp_enable_ready_pickup_email" id="wclp_enable_ready_pickup_email" <?php echo esc_html($ready_pickup_checked); ?> value="1"><?php esc_html_e( 'Send Email', 'zorem-local-pickup' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo esc_url(admin_url('admin.php?page=alp_customizer&email_type=ready_pickup')); ?>"><?php esc_html_e( 'Customize', 'zorem-local-pickup' ); ?></a>
									</fieldset>
								</td>
							</tr>					
							<tr valign="top" class="picked_up_row  
							<?php 
							if (!get_option('wclp_status_picked_up')) {
								echo 'disable_row';
							} 
							?>
							">
								<td class="">
									<span class="tgl-btn-parent" style="">
										<input type="hidden" name="wclp_status_picked_up" value="0">
										<input type="checkbox" id="wclp_status_picked_up" name="wclp_status_picked_up" class="tgl tgl-flat-alp" 
										<?php echo ( get_option('wclp_status_picked_up') ) ? 'checked' : ''; ?> value="1"/>
										<label class="tgl-btn" for="wclp_status_picked_up"></label>
									</span>
								</td>
								<td class=" status-label-column" style="width: 130px;">
									<span class="order-label wc-pickup" style="background:<?php echo esc_html(get_option('wclp_pickup_status_label_color', '#2196f3')); ?>;color:<?php echo esc_html(get_option('wclp_pickup_status_label_font_color', '#fff')); ?>">
										<?php esc_html_e( 'Picked up', 'zorem-local-pickup' ); ?>
									</span>
								</td>
								<td class="">
									<fieldset>
										<input class="input-text regular-input " type="text" name="wclp_pickup_status_label_color" id="wclp_pickup_status_label_color" style="" value="<?php echo esc_html(get_option('wclp_pickup_status_label_color', '#2196f3')); ?>" placeholder="">
										<select class="select" id="wclp_pickup_status_label_font_color" name="wclp_pickup_status_label_font_color">	
											<option value="#fff" <?php echo ( '#fff' == get_option('wclp_pickup_status_label_font_color') ) ? 'selected' : ''; ?>><?php esc_html_e( 'Light Font', 'zorem-local-pickup' ); ?></option>
											<option value="#000" <?php echo ( '#000' == get_option('wclp_pickup_status_label_font_color') ) ? 'selected' : ''; ?>><?php esc_html_e( 'Dark Font', 'zorem-local-pickup' ); ?></option>
										</select>
									</fieldset>
								</td>								
								<td class="" style="text-align: right;">							
									<?php
									$wclp_enable_pickup_email = get_option('woocommerce_customer_pickup_order_settings');
									if (isset($wclp_enable_pickup_email) && !empty($wclp_enable_pickup_email)) {
										if ('yes' == $wclp_enable_pickup_email['enabled'] || 1 == $wclp_enable_pickup_email['enabled']) {
											$pickup_checked = 'checked';
										} else {
											$pickup_checked = '';									
										}
									} else {
										$pickup_checked = 'checked';
									}
									?>
									<fieldset>
										<label class="send_email_label">
											<input type="hidden" name="wclp_enable_pickup_email" value="0"/>
											<input type="checkbox" name="wclp_enable_pickup_email" id="wclp_enable_pickup_email" <?php echo esc_html($pickup_checked); ?> value="1"><?php esc_html_e( 'Send Email', 'zorem-local-pickup' ); ?>
										</label>
										<a class='settings_edit' href="<?php echo esc_url(admin_url('admin.php?page=alp_customizer&email_type=pickup')); ?>"><?php esc_html_e( 'Customize', 'zorem-local-pickup' ); ?></a>
									</fieldset>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				
				<div class="accordion heading premium pro_section">
					<label>
						<?php esc_html_e( 'Local Pickup Dashboard', 'zorem-local-pickup' ); ?>
						<span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>

				<div class="accordion heading premium pro_section">
					<label>
						<?php esc_html_e( 'Cart & Checkout Options', 'zorem-local-pickup' ); ?>
						<span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>

				<div class="accordion heading premium pro_section">
					<label>
						<?php esc_html_e( 'Products Catalog Options', 'zorem-local-pickup' ); ?>
						<span class="alp-pro-badge"><?php esc_html_e( 'PRO', 'zorem-local-pickup' ); ?></span>
						<span class="dashicons dashicons-arrow-right-alt2"></span>
					</label>
				</div>
			</form>
		</div>
	</div>
</section>
