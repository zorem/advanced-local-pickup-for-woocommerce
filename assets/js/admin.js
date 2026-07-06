/* alp_snackbar — delegate to the library's `ZUI.snackbar()` API.
 * The library handles the toast markup (`.zui-snackbar-stack`,
 * `.zui-snackbar`, icon, text, close, progress bar) and stacking — we just
 * tell it the message + variant. A simple in-house fallback covers the
 * edge case where window.ZUI hasn't loaded yet. */
(function( $ ){
	function alpToast(msg, variant){
		if ( typeof window.ZUI !== "undefined" && typeof window.ZUI.snackbar === "function" ) {
			window.ZUI.snackbar( msg, { type: variant } );
			return;
		}
		// Fallback if zui.js failed to load.
		if ( jQuery('.zui-snackbar-stack').length === 0 ) {
			$('body').append('<div class="zui-snackbar-stack"></div>');
		}
		$('.zui-snackbar-stack').empty().append(
			'<div class="zui-snackbar zui-snackbar--' + variant + '" style="--zui-snackbar-duration: 3000ms">' +
				'<span class="zui-snackbar__text"></span>' +
				'<button class="zui-snackbar__close" aria-label="Dismiss">&times;</button>' +
				'<span class="zui-snackbar__progress"></span>' +
			'</div>'
		);
		$('.zui-snackbar-stack .zui-snackbar__text').text( msg );
	}
	$.fn.alp_snackbar         = function(msg){ alpToast(msg, 'success'); return this; };
	$.fn.alp_snackbar_warning = function(msg){ alpToast(msg, 'error');   return this; };
})( jQuery );

/* ZUI top-tab switching — no page reload (mirrors ALP Pro pattern).
 * All tabs (Settings, Pickup Location, Go Pro) are rendered into separate
 * `.zui-tab-panel[data-tab]` divs; clicks just swap which one is visible. */
function alpActivateTab(tab) {
	"use strict";
	jQuery(".zui-tab-panel").prop("hidden", true);
	jQuery('.zui-tab-panel[data-tab="' + tab + '"]').prop("hidden", false);
	jQuery(".zui-tabs__item").removeClass("is-active").removeAttr("aria-current");
	jQuery('.zui-tabs__item[data-tab="' + tab + '"]')
		.addClass("is-active")
		.attr("aria-current", "page");
}

jQuery(document).on("click", ".zui-tabs__item[data-tab]", function(e){
	"use strict";
	if ( jQuery(this).attr("data-tab-external") ) {
		return;
	}
	e.preventDefault();
	var tab = jQuery(this).data("tab");
	if ( ! tab ) { return; }
	alpActivateTab(tab);
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=local_pickup&tab=" + tab;
	window.history.pushState({ path: url }, "", url);
	jQuery(window).trigger("resize");
});

/* .zui-color-input (library) — keep the swatch dot + workflow row's
 * soft-tint icon tile in sync as the user picks a color. */
function alpPaintColorTargets(hex, $wrap) {
	"use strict";
	$wrap.find(".zui-color-dot").css("--zui-c", hex);
	var $icon = $wrap.closest(".alp-workflow-row").find(".alp-workflow-row__icon");
	$icon.css({ background: hex + "1A", color: hex });
}
jQuery(document).on("input change", ".zui-color-input input[type='color']", function () {
	"use strict";
	var hex = this.value;
	if ( ! hex ) { return; }
	var $wrap = jQuery(this).closest(".zui-color-input");
	$wrap.find("input.zui-input").val(hex);
	alpPaintColorTargets(hex, $wrap);
});
jQuery(document).on("input", ".zui-color-input input.zui-input", function () {
	"use strict";
	var hex = this.value;
	if ( ! /^#[0-9a-f]{6}$/i.test(hex) ) { return; }
	var $wrap = jQuery(this).closest(".zui-color-input");
	$wrap.find("input[type='color']").val(hex);
	alpPaintColorTargets(hex, $wrap);
});

/* Settings sidebar nav — click a sidebar item to swap which section is
 * visible without a page reload. Scoped to the Settings tab panel so the
 * Go Pro / Pickup Location panels' own `<section data-section>` markup
 * isn't accidentally hidden when the user toggles a Settings sub-section. */
jQuery(document).on("click", ".zui-sidebar__item[data-section]", function(e){
	"use strict";
	e.preventDefault();
	var $btn = jQuery(this);
	var section = $btn.data("section");
	if ( ! section ) { return; }
	var $panel = $btn.closest(".zui-tab-panel");
	if ( ! $panel.length ) { $panel = jQuery('.zui-tab-panel[data-tab="settings"]'); }
	$panel.find(".zui-sidebar__item").removeClass("is-active").removeAttr("aria-current");
	$btn.addClass("is-active").attr("aria-current", "true");
	$panel.find(".zui-section[data-section]").each(function(){
		var $section = jQuery(this);
		if ( $section.data("section") === section ) {
			$section.addClass("is-active").prop("hidden", false);
		} else {
			$section.removeClass("is-active").prop("hidden", true);
		}
	});
	jQuery("#alp-settings-app").removeClass("zui-sidebar-open");
	if ( window.history && window.history.replaceState ) {
		var url = new URL( window.location.href );
		url.searchParams.set("section", section);
		window.history.replaceState({ path: url.toString() }, "", url.toString());
	}
});

/* Mobile drawer toggle (sidebar slide-in on small viewports). */
jQuery(document).on("click", "[data-ast-drawer-toggle]", function(e){
	"use strict";
	e.preventDefault();
	jQuery("#alp-settings-app").addClass("zui-sidebar-open");
});
jQuery(document).on("click", "[data-ast-drawer-close]", function(e){
	"use strict";
	e.preventDefault();
	jQuery("#alp-settings-app").removeClass("zui-sidebar-open");
});

/*header script*/
jQuery( document ).on( "click", "#activity-panel-tab-help", function(e) {
	e.preventDefault(); // stops link from making page jump to the top
	e.stopPropagation(); // when you click the button, it stops the page from seeing it as clicking the body too
	jQuery(this).addClass( 'is-active' );
	jQuery( '.woocommerce-layout__activity-panel-wrapper' ).addClass( 'is-open is-switching' );
});

jQuery( document ).on( "click", ".woocommerce-layout__activity-panel-wrapper", function(e) {	
	e.stopPropagation(); // when you click the button, it stops the page from seeing it as clicking the body too	
});

jQuery( document ).on( "click", "body", function() {	
	jQuery('#activity-panel-tab-help').removeClass( 'is-active' );
	jQuery( '.woocommerce-layout__activity-panel-wrapper' ).removeClass( 'is-open is-switching' );
});
/*header script end*/ 

jQuery(document).ready(function(){
	
	"use strict";
	
	// Country / State dropdowns get the wc-enhanced-select select2 treatment;
	// pickup time dropdowns now use the native .zui-select chrome (matching Pro)
	// so we deliberately do NOT include `.wclp_pickup_time_select` here.
	jQuery('#wclp_default_single_country, #wclp_default_single_state, #wclp_default_country, #wclp_display_pickup_instruction_statuses').select2();
	
	jQuery(".tipTip").tipTip();	
	
	// wpColorPicker removed — workflow rows now use the library .zui-color-input
	// (color dot + hex text) component. Live preview for the workflow row's
	// icon tile + the legacy sample label is wired up via the global handler
	// below (registered outside this ready() block so it survives re-renders).

	
	//jQuery('#wclp_setting_tab_form .accordion').trigger('click');
	if(jQuery('#wclp_store_name').val() === ''){
		jQuery(".address-special").addClass('active');
		jQuery(".address-special").next('.panel').addClass('active').hide().slideDown(1000);
		jQuery(".address-special").css('cursor', 'default');
		jQuery(".address-special").find('span.wclp-btn').show();
		jQuery(".address-special").find('span.dashicons').removeClass('dashicons-arrow-right-alt2');
		jQuery(".address-special").find('label').css('color','#212121');
	}
	
});

jQuery(document).on("click", ".accordion", function () {
	"use strict";

	var $this = jQuery(this);
	var $panel = $this.next('.panel');
	var location_name = jQuery('#wclp_store_name').val();

	if (location_name === '') {
		jQuery('#wclp_store_name').next(".alp_error_msg").show();
		jQuery('#wclp_store_name').css('border-color', 'red');
		return;
	}

	// Only animate the currently-open panel(s) so slideUp on hidden panels
	// (jQuery treats slideUp on display:none as a no-op) doesn't visually
	// snap the close. Chain close → open so both animations are fully visible.
	var $openPanels = jQuery('.accordion.active').next('.panel').filter(':visible');

	// If clicking already active accordion — close it and stop.
	if ($this.hasClass('active')) {
		$this.removeClass('active');
		$panel.removeClass('active').stop(true, true).css('display', 'block').slideUp(1000);

		$this.find('span.wclp-btn').hide();
		$this.find('span.dashicons').addClass('dashicons-arrow-right-alt2');
		$this.find('label').css('color', '');
		return;
	}

	// Reset chrome on ALL accordions (visual only, no animation).
	jQuery('.accordion').removeClass('active');
	jQuery('.accordion').find('span.wclp-btn').hide();
	jQuery('.accordion').find('span.dashicons').addClass('dashicons-arrow-right-alt2');
	jQuery('.accordion').find('label').css('color', '');

	// Activate the clicked accordion's chrome immediately.
	$this.addClass('active');
	$this.find('span.wclp-btn').show();
	$this.find('span.dashicons').removeClass('dashicons-arrow-right-alt2');
	$this.find('label').css('color', '#212121');

	// Close currently-open panel and open the new one AT THE SAME TIME.
	// css("display","block") guarantees the closing panel has a real height to
	// animate from so its slideUp actually plays for the full 1000ms.
	if ($openPanels.length) {
		$openPanels.removeClass('active').stop(true, true).css('display', 'block').slideUp(1000);
	}
	$panel.addClass('active').stop(true, true).hide().slideDown(1000);
});

(function( $ ){
	$.fn.isInViewport = function( element ) {
		var win = $(window);
		var viewport = {
			top : win.scrollTop()			
		};
		viewport.bottom = viewport.top + win.height();
		
		var bounds = this.offset();		
		bounds.bottom = bounds.top + this.outerHeight();

		if( bounds.top >= 0 && bounds.bottom <= window.innerHeight) {
			return true;
		} else {
			return false;	
		}		
	};
})( jQuery );

jQuery(document).on("change", "#wclp_pickup_status_label_font_color", function(){
	var font_color = jQuery(this).val();
	jQuery('.order-status-table .order-label.wc-pickup').css('color',font_color);
	save_custom_order_status();
});

jQuery(document).on("change", "#wclp_ready_pickup_status_label_font_color", function(){
	var font_color = jQuery(this).val();
	jQuery('.order-status-table .order-label.wc-ready-pickup').css('color',font_color);
	save_custom_order_status();
});

jQuery(document).on("click", "#wclp_status_pickup", function(){
	if(jQuery(this).prop("checked") == true){
        jQuery(this).closest('tr').removeClass('disable_row');				
    } else{
		jQuery(this).closest('tr').addClass('disable_row');
	}	
});


/* ZUI savebtn lifecycle — swap the .zui-savebtn__label to "Saving…" while the
 * AJAX is in flight, restore on completion. Matches the library convention
 * documented in zorem-settings-ui /css/components/savebtn.css. */
function alpSavebtnSaving($btn) {
	var $label = $btn.find(".zui-savebtn__label");
	if ( $label.length && ! $btn.data("alpSavebtnOriginalLabel") ) {
		$btn.data("alpSavebtnOriginalLabel", $label.html());
	}
	$label.html("Saving…");
	$btn.addClass("is-saving").prop("disabled", true);
}
function alpSavebtnReset($btn) {
	var original = $btn.data("alpSavebtnOriginalLabel");
	if ( original ) {
		$btn.find(".zui-savebtn__label").html(original);
	}
	$btn.removeClass("is-saving").prop("disabled", false);
}

/*ajex call for general tab form save*/
jQuery(document).on("click", "#wclp_setting_tab_form .wclp-save", function(){
	"use strict";
	var $btn = jQuery(this);
	alpSavebtnSaving($btn);
	var form = jQuery('#wclp_setting_tab_form');
	jQuery.ajax({
		url: ajaxurl,
		data: form.serialize() + '&nonce=' + alp_object.nonce,
		type: 'POST',
		dataType:"json",
		success: function(response) {
			alpSavebtnReset($btn);
			if ( response.success === "true" ) {
				jQuery(document).alp_snackbar( "Settings Successfully Saved." );
			} else if ( response.permission === "false" ) {
				jQuery(document).alp_snackbar_warning( "you don't have permission to save settings." );
			}
		},
		error: function(response) {
			alpSavebtnReset($btn);
			console.log(response);
		}
	});
	return false;
});

jQuery(document).on("click", "#wclp_status_ready_pickup", function(){
	if(jQuery(this).prop("checked") == true){
        jQuery(this).closest('tr').removeClass('disable_row');				
    } else{
		jQuery(this).closest('tr').addClass('disable_row');
	}	
});

jQuery(document).on("click", "#wclp_status_picked_up", function(){
	if(jQuery(this).prop("checked") == true){
        jQuery(this).closest('tr').removeClass('disable_row');				
    } else{
		jQuery(this).closest('tr').addClass('disable_row');
	}	
});

/*ajex call for general tab form save*/
jQuery(document).on("click", "#wclp_location_tab_form .btn_location_submit", function(){
	"use strict";

	var $btn = jQuery(this);
	jQuery(".alp_error_msg").hide();
	var validation = true;
	var days = [ 'saturday', 'friday', 'thursday', 'wednesday', 'tuesday', 'monday', 'sunday' ];
	// Helper: open the Business Hours accordion + scroll to it so the user
	// can see the highlighted day card when a time is missing.
	var openBusinessHours = function () {
		var $bh = jQuery('.accordion.heading.business-hours');
		if ( $bh.length && ! $bh.hasClass('active') ) {
			$bh.trigger('click');
		}
	};
	for ( var i = 0, l = days.length; i < l; i++ ) {
		jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','#ddd');
		jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','#ddd');
		var $dayCard = jQuery('#'+days[ i ]).closest('.wplp_pickup_duration');
		$dayCard.css('border-color','');

		if(jQuery('#'+days[ i ]).prop("checked") == true){
			var wclp_store_hour = jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"] option:selected').val();
			var wclp_store_hour_end = jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"] option:selected').val();

			if(wclp_store_hour == ''){
				$dayCard.css('border-color','red');
				openBusinessHours();
				jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','red');
				jQuery(document).alp_snackbar_warning( 'Pick a "from" time for ' + days[ i ].charAt(0).toUpperCase() + days[ i ].slice(1) );
				validation=false;
			}
			if(wclp_store_hour_end == ''){
				$dayCard.css('border-color','red');
				openBusinessHours();
				jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','red');
				jQuery(document).alp_snackbar_warning( 'Pick a "to" time for ' + days[ i ].charAt(0).toUpperCase() + days[ i ].slice(1) );
				validation=false;
			}
			if(wclp_store_hour != '' && wclp_store_hour_end != ''){
				var st = minFromMidnight(wclp_store_hour);
				var et = minFromMidnight(wclp_store_hour_end);
				if(st>=et){
					$dayCard.css('border-color','red');
					openBusinessHours();
					jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour]"]').css('border-color','red');
					jQuery('select[name="wclp_store_days['+days[ i ]+'][wclp_store_hour_end]"]').css('border-color','red');
					jQuery(document).alp_snackbar_warning( 'End time must be after the start time for ' + days[ i ].charAt(0).toUpperCase() + days[ i ].slice(1) );
					validation=false;
				}
			}
		}
	}
	 
	var location_name = jQuery('#wclp_store_name').val();
	if(location_name === ''){
		if(!jQuery('.address-special').hasClass('active')){
			jQuery('.address-special').trigger('click');
		}		
		jQuery('#wclp_store_name').css('border-color','red');
		jQuery('#wclp_store_name').css('display','block');
		jQuery('.alp_error_msg').show();
		validation=false;
	} else {
		jQuery('#wclp_store_name').css('border-color','');
	}

	if(validation === true){
		alpSavebtnSaving($btn);
		var $form = jQuery('#wclp_location_tab_form');
		// Force select2-wrapped <select> elements (Country/State) to flush their
		// value onto the native element before serialize, just in case any
		// older select2 build keeps a stale value out of the DOM.
		$form.find('select.wc-enhanced-select').each(function(){
			jQuery(this).trigger('change.select2');
		});
		jQuery.ajax({
			url: ajaxurl,
			data: $form.serialize() + '&nonce=' + alp_object.nonce,
			type: 'POST',
			dataType:"json",
			success: function(response) {
				alpSavebtnReset($btn);
				if ( ! response ) {
					jQuery(document).alp_snackbar_warning( "Save failed — no response from server." );
					return;
				}
				if( response.success === "fail" ){
					jQuery('#wclp_location_tab_form').prepend('<div class="alp_error_msg">'+(response.msg || 'Save failed.')+'</div>');
					jQuery('.alp_error_msg').show();
					return;
				}
				if( response.permission === "false" ){
					jQuery(document).alp_snackbar_warning( "You don't have permission to save settings." );
					return;
				}
				if( response.success === "true" ){
					jQuery('.alp_error_msg').remove();
					jQuery(document).alp_snackbar( "Settings Successfully Saved." );
					if ( response.id ) {
						window.history.pushState({}, '', "admin.php?page=local_pickup&tab=locations&section=edit&id="+response.id);
						jQuery("#location_id").val(response.id);
					}
				} else {
					jQuery(document).alp_snackbar_warning( "Save did not complete. Please try again." );
				}
			},
			error: function(xhr) {
				alpSavebtnReset($btn);
				jQuery(document).alp_snackbar_warning( "Server error while saving (" + xhr.status + ")." );
				console.log('ALP save error', xhr.status, xhr.responseText);
			}
		});
	}
	return false;
});

function minFromMidnight(tm){
	"use strict";
	if(tm){
		var ampm= tm.substr(-2);
		var clk = tm.substr(0, 5);
		var m  = parseInt(clk.match(/\d+$/)[0], 10);
		var h  = parseInt(clk.match(/^\d+/)[0], 10);
		h += (ampm.match(/pm/i))? 12: 0;
		return h*60+m;
	}
}

jQuery(document).on("click", ".wclp_tab_input", function(){
	"use strict";
	var tab = jQuery(this).data('tab');
	
	var label = jQuery(this).data('label');
	jQuery('.zorem-layout__header-breadcrumbs .header-breadcrumbs-last').text(label);
	var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page=local_pickup&tab="+tab;
	window.history.pushState({path:url},'',url);	
});

jQuery(document).on("click", ".inner_tab_input", function(){
	var tab = jQuery('input[name="tabs"]:checked').data('tab');
	var subtab = jQuery(this).data('subtab');
	if( subtab !== undefined){
		var url = window.location.protocol + "//" + window.location.host + window.location.pathname+"?page=local_pickup&tab="+tab+"&subtab="+subtab;
	}
	
	window.history.pushState({path:url},'',url);	
});

jQuery(document).on("change click", ".pickup_days_checkbox", function(){
	"use strict";
	var $card = jQuery(this).closest('.wplp_pickup_duration');
	if(jQuery(this).prop("checked") === true){
		$card.addClass('is-on');
		$card.find('.wclp_pickup_time_fieldset span.hours').addClass('hours-time');
		$card.find('.wclp_pickup_time_fieldset').prop('disabled', false);
	} else{
		$card.removeClass('is-on');
		$card.find('.wclp_pickup_time_fieldset span.hours').removeClass('hours-time');
		$card.find('.wclp_pickup_time_fieldset').prop('disabled', 'disabled');
	}
});
jQuery(document).ready(function(){
	"use strict";
	jQuery('.pickup_days_checkbox').each(function(){
		var $card = jQuery(this).closest('.wplp_pickup_duration');
		if(jQuery(this).prop("checked") === true){
			$card.addClass('is-on');
			$card.find('.wclp_pickup_time_fieldset span.hours').addClass('hours-time');
			$card.find('.wclp_pickup_time_fieldset').prop('disabled', false);
		} else{
			$card.removeClass('is-on');
			$card.find('.wclp_pickup_time_fieldset span.hours').removeClass('hours-time');
			$card.find('.wclp_pickup_time_fieldset').prop('disabled', 'disabled');
		}
	});
});


/*ajex call for general tab form save*/	
jQuery(document).on("change", "#wclp_default_single_country", function(){
	"use strict";
	
	var country = jQuery(this).val();
	var data = {
		action: 'wclp_update_state_dropdown',
		country: country,
		nonce: alp_object.nonce,
	};		
	
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if(response.state !== 'empty'){
				jQuery('#wclp_default_single_state').empty().append(response.state);				
				jQuery("#wclp_default_single_state").closest('tr').show();
			} else{
				jQuery('#wclp_default_single_state').empty();
				jQuery("#wclp_default_single_state").closest('tr').hide();
			}			
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

/*ajex call for general tab form save*/	
jQuery(document).on("change", "#wclp_default_time_format", function(){
	"use strict";
	jQuery(".location-setting .panel.business-hours").block({
		message: null,
		overlayCSS: {
			background: "#fff",
			opacity: 0.6
		}	
    });	
	var hour_format = jQuery(this).val();
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
	// Prefer the hidden location_id field over the URL query — the URL on
	// the "Pickup Location" tab can be `?page=local_pickup&tab=locations`
	// with no `id` segment, while the hidden field always carries the row id.
	var id = ( jQuery('#location_id').val() || getUrlParameter('id') || '' );
	var data = {
		action: 'wclp_update_work_hours_list',
		hour_format: hour_format,
		id: id,
		nonce: alp_object.nonce,
	};
	
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if(response.pickup_hours_div){
				jQuery(".pickup_hours_div").replaceWith(response.pickup_hours_div);
				jQuery(".wclp_pickup_time_select").select2();
				var pickup_days_checkbox = jQuery('.pickup_days_checkbox');
				jQuery(pickup_days_checkbox).each(function(){		
					if(jQuery(this).prop("checked") === true){
						jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').prop('disabled', false);
					} else{
						jQuery(this).closest('.wplp_pickup_duration').find('.wclp_pickup_time_fieldset').prop('disabled', 'disabled');
					}
				});
				jQuery(".location-setting .panel.business-hours").unblock();
			}
				
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
});

function wclp_update_edit_location_form(){
	"use strict";
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
	// Same fallback as the apply-hours handler — hidden field wins over URL
	// so the AJAX never fires with an empty id when the URL has no `&id=`.
	var id = ( jQuery('#location_id').val() || getUrlParameter('id') || '' );
	var data = {
		action: 'wclp_update_edit_location_form',
		id: id,
		nonce: alp_object.nonce,
	};
	
	jQuery.ajax({
		url: ajaxurl,
		data: data,
		type: 'POST',
		dataType:"json",	
		success: function(response) {
			if(response.edit_location_form){
				jQuery(".pickup-location-setting").replaceWith(response.edit_location_form);
				jQuery(".wclp_pickup_time_select").select2();
				jQuery("#wclp_selected_products, #wclp_excluded_products, #wclp_selected_categories, #wclp_excluded_categories").select2();
			}
				
		},
		error: function(response) {
			console.log(response);			
		}
	});
	return false;
}

jQuery(document).on("click", ".wclp-apply", function(){
	"use strict";
	jQuery(".alp_error_msg").remove();
	var hour_format = jQuery("#wclp_default_time_format").val();
	var getUrlParameter = function getUrlParameter(sParam) {
		var sPageURL = window.location.search.substring(1),
			sURLVariables = sPageURL.split('&'),
			sParameterName,
			i;

		for (i = 0; i < sURLVariables.length; i++) {
			sParameterName = sURLVariables[i].split('=');

			if (sParameterName[0] === sParam) {
				return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
			}
		}
	};
	
	var validation = true;
	var hasClassMorning = jQuery(this).closest('.popuprow').find(".morning-time").hasClass("hide-select-box");
	var hasClassAfternoon = jQuery(this).closest('.popuprow').find(".afternoon-time").hasClass("hide-select-box");
	// Scope the time-select lookup to the popup card. The Apply button's
	// .parent() is .alp-hours-popup__actions in the new layout — it does NOT
	// contain the .start/.end selects (those live in .alp-hours-popup__body
	// > .morning-time). Without this fix the AJAX would POST empty hour
	// values and wipe the row that was just being edited.
	var $popup = jQuery(this).closest('.popuprow');
	if ( ! $popup.length ) { $popup = jQuery(this).closest('.alp-hours-popup'); }
	var wclp_store_hour = $popup.find("select.start").first();
	var wclp_store_hour_end = $popup.find("select.end").first();
	var wclp_store_hour2 = $popup.find("select.start2").first();
	var wclp_store_hour_end2 = $popup.find("select.end2").first();

	// Fail-fast: never overwrite saved hours with empty values.
	if ( ! wclp_store_hour.length || ! wclp_store_hour_end.length ) {
		jQuery(document).alp_snackbar_warning( "Could not read the popup From/To selects. Please re-open and try again." );
		return false;
	}
	if ( ( wclp_store_hour.val() || '' ) === '' || ( wclp_store_hour_end.val() || '' ) === '' ) {
		jQuery(document).alp_snackbar_warning( 'Pick both "From" and "To" times before applying.' );
		return false;
	}
	
	var days = [];
	var day = jQuery(this).val();
	days.push(day);
	jQuery("input[name=weekday-"+jQuery(this).val()+"]:checked").each(function(){
		if(jQuery(this).val() !== day){
			days.push(jQuery(this).val());
		}
	});
	

	// Resolve the location id from the hidden form field first (always set
	// when the edit form renders), and only fall back to the URL parameter
	// if for some reason the hidden field is empty. Reading from the URL
	// alone was returning undefined whenever the user landed on
	// `?page=local_pickup&tab=locations` without the `&id=N` segment in
	// the URL, which made PHP fail with "Location not found.".
	var alpLocationId = ( jQuery('#location_id').val() || getUrlParameter('id') || '' );
	var data = {
		action: 'wclp_apply_work_hours',
		hour_format: hour_format,
		id: alpLocationId,
		days: days,
		wclp_store_hour:      wclp_store_hour.val(),
		wclp_store_hour_end:  wclp_store_hour_end.val(),
		wclp_store_hour2:     wclp_store_hour2.length     ? (wclp_store_hour2.val()     || '') : '',
		wclp_store_hour_end2: wclp_store_hour_end2.length ? (wclp_store_hour_end2.val() || '') : '',
		nonce: alp_object.nonce,
	};
	if (wclp_store_hour.val() !== '' && wclp_store_hour_end.val() !== '' && hasClassMorning === false) {
		var st1 = minFromMidnight(wclp_store_hour.val());
		var et1 = minFromMidnight(wclp_store_hour_end.val());
		if (st1 >= et1) {
			wclp_store_hour.css('border-color','red');
			wclp_store_hour_end.css('border-color','red');
			jQuery(this).after('<div class="alp_error_msg">End time must be greater than start time</div>');
			jQuery('.alp_error_msg').show();
			validation = false;
		}
	}

	if (wclp_store_hour_end.val() !== '' && wclp_store_hour2.length && wclp_store_hour2.val() !== '' && hasClassAfternoon === false) {
		var st = minFromMidnight(wclp_store_hour_end.val());
		var et = minFromMidnight(wclp_store_hour2.val());
		if (st >= et) {
			wclp_store_hour_end.css('border-color','red');
			wclp_store_hour2.css('border-color','red');
			jQuery(this).after('<div class="alp_error_msg">Start split time must be greater than end time</div>');
			jQuery('.alp_error_msg').show();
			validation = false;
		}
	}
	if (wclp_store_hour2.length && wclp_store_hour2.val() && wclp_store_hour_end2.length && wclp_store_hour_end2.val() && hasClassAfternoon === false) {
		var st2 = minFromMidnight(wclp_store_hour2.val());
		var et2 = minFromMidnight(wclp_store_hour_end2.val());
		if (st2 >= et2) {
			wclp_store_hour2.css('border-color','red');
			wclp_store_hour_end2.css('border-color','red');
			jQuery(this).after('<div class="alp_error_msg">End time must be greater than start interval time</div>');
			jQuery('.alp_error_msg').show();
			validation = false;
		}
	}
	
	if(validation === true){
		jQuery('.alp-hours-popup').hide();
		jQuery(".panel.business-hours").block({
			message: null,
			overlayCSS: {
				background: "#fff",
				opacity: 0.6
			}
		});
		jQuery.ajax({
			url: ajaxurl,
			data: data,
			type: 'POST',
			dataType:"json",
			success: function(response) {
				jQuery(".panel.business-hours").unblock();
				if ( ! response ) {
					jQuery(document).alp_snackbar_warning( "No response from server while saving hours." );
					return;
				}
				if ( response.success === 'fail' ) {
					jQuery(document).alp_snackbar_warning( response.msg || "Could not save hours." );
					return;
				}
				if ( response.pickup_hours_div ) {
					jQuery(".pickup_hours_div").replaceWith(response.pickup_hours_div);
					// Sync the .is-on state + fieldset enabled flag on each card
					// based on the rendered checkbox state.
					jQuery('.pickup_days_checkbox').each(function(){
						var $card = jQuery(this).closest('.wplp_pickup_duration');
						if ( jQuery(this).prop("checked") === true ) {
							$card.addClass('is-on');
							$card.find('.wclp_pickup_time_fieldset').prop('disabled', false);
						} else {
							$card.removeClass('is-on');
							$card.find('.wclp_pickup_time_fieldset').prop('disabled', 'disabled');
						}
					});
					jQuery(document).alp_snackbar( "Hours updated." );
				}
			},
			error: function(xhr) {
				jQuery(".panel.business-hours").unblock();
				jQuery(document).alp_snackbar_warning( "Server error while saving hours (" + xhr.status + ")." );
				console.log('ALP apply error', xhr.status, xhr.responseText);
			}
		});
	}
	return false;
});

jQuery(document).on("click", ".hours-time", function(){
	"use strict";
	jQuery(this).parent().find(".alp-hours-popup").show();
});
jQuery(document).on("click", ".alp-apply-multiple", function(){	
	"use strict";
	jQuery(this).parent().find(".hours-popup").hide();
	jQuery(this).hide();
	jQuery(this).parent().find(".alp-hours-popup").hide();
	jQuery(this).parent().find(".apply-days-popup").show();
});
jQuery(document).on("click", ".back-popup", function(){	
	"use strict";
	jQuery(this).parent().parent().find(".alp-hours-popup").show();
	jQuery(this).parent().parent().find(".apply-days-popup").hide();
	jQuery(this).parent().parent().find(".hours-popup").show();
	jQuery(".alp-apply-multiple").show();
});
jQuery(document).on("click", ".popupclose, .popup_close_icon", function(){
	"use strict";
	jQuery('.alp-hours-popup').hide();
});

jQuery(document).on("click", ".alp-hours-popup .dashicons-trash", function(){
	"use strict";
	jQuery(this).parent().find("select").val("");
	jQuery(this).parent().find("select").select2();
});

/* ALP PRO Sidebar - Show only on Settings tab */
jQuery(document).ready(function(){
	"use strict";
	var activeTab = jQuery('.wclp_tab_input:checked').data('tab');
	if (!activeTab || activeTab === 'settings') {
		jQuery('.alp-pro-sidebar').addClass('alp-sidebar-visible');
	}
});

jQuery(document).on("click", ".wclp_tab_input", function(){
	"use strict";
	var tab = jQuery(this).data('tab');
	if (tab === 'settings') {
		jQuery('.alp-pro-sidebar').addClass('alp-sidebar-visible');
	} else {
		jQuery('.alp-pro-sidebar').removeClass('alp-sidebar-visible');
	}
});
 
/* License Ecosystem grid on Go Pro tab — filter pills + search.
 * Mirrors CBR's behaviour against ALP's `#alp-lic-*` ids. */
jQuery(function($){
	"use strict";
	var $grid = $("#alp-lic-grid");
	if ( ! $grid.length ) { return; }
	var $search  = $("#alp-lic-search");
	var $empty   = $grid.find(".zui-lic-eco__empty");
	var $filters = $(".zui-lic-eco__filters .zui-lic-eco__filter");
	var current  = "all";

	function applyFilter() {
		var q = $.trim( ( $search.val() || "" ).toLowerCase() );
		var shown = 0;
		$grid.find(".zui-lic-plugin").each(function(){
			var $card = $(this);
			var name = ( $card.attr("data-name") || "" ).toLowerCase();
			var active = $card.attr("data-active") === "1";
			var matchSearch = ! q || name.indexOf(q) !== -1;
			var matchFilter = ( current === "all" )
				|| ( current === "active" && active )
				|| ( current === "addons" && ! active );
			var show = matchSearch && matchFilter;
			$card.toggle( show );
			if ( show ) { shown++; }
		});
		if ( shown === 0 ) { $empty.removeAttr("hidden"); } else { $empty.attr("hidden", ""); }
	}

	$search.on("input", applyFilter);
	$filters.on("click", function(){
		current = $(this).attr("data-filter");
		$filters.removeClass("is-active");
		$(this).addClass("is-active");
		applyFilter();
	});
});

/* Telemetry & Communication Preferences — persist toggles via the dedicated
 * `alp_free_telemetry_save` AJAX handler. Uses fetch + FormData (matches
 * AST's pattern) so `checkbox + hidden` co-existence is serialized exactly
 * as the browser would submit, avoiding jQuery quirks. */
function alpSaveUsageTrackingForm() {
	"use strict";
	var form = document.getElementById("alp-usage-tracking-form");
	if (!form) { return; }
	var card = form.closest(".alp-lic-telemetry-card");
	var btn  = card ? card.querySelector(".zui-lic-savebtn") : null;
	var msg  = card ? card.querySelector("#alp-usage-msg") : null;
	var origBtn = btn ? btn.textContent : "Save";
	if (btn) { btn.disabled = true; btn.textContent = "Saving…"; }

	var ajaxURL = (typeof ajaxurl !== "undefined")
		? ajaxurl
		: ((typeof alp_object !== "undefined" && alp_object.admin_url) ? alp_object.admin_url + "admin-ajax.php" : "/wp-admin/admin-ajax.php");

	var fd = new FormData(form);

	if (window.console && console.info) {
		console.info("[ALP telemetry] posting to", ajaxURL, "form data:", Array.from(fd.entries()));
	}

	fetch(ajaxURL, {
		method: "POST",
		credentials: "same-origin",
		body: fd
	})
		.then(function (r) { return r.text().then(function (t) { return { status: r.status, text: t }; }); })
		.then(function (o) {
			var res = null;
			try { res = JSON.parse(o.text); } catch (e) { /* not JSON */ }
			if (window.console && console.info) {
				console.info("[ALP telemetry] response status=" + o.status, res || o.text);
			}
			var ok = o.status >= 200 && o.status < 400 && res && res.success;
			var text = (res && res.data && res.data.message) ? res.data.message : (ok ? "Data saved successfully" : "Save failed");
			if (ok) {
				if (jQuery(document).alp_snackbar) { jQuery(document).alp_snackbar(text); }
				if (msg) { msg.removeAttribute("hidden"); msg.textContent = "Saved"; msg.style.background = "#10b981"; msg.style.display = "inline-block"; }
			} else {
				if (jQuery(document).alp_snackbar_warning) { jQuery(document).alp_snackbar_warning(text); }
				if (msg) { msg.removeAttribute("hidden"); msg.textContent = "Error"; msg.style.background = "#ef4444"; msg.style.display = "inline-block"; }
			}
		})
		.catch(function (err) {
			if (window.console && console.error) { console.error("[ALP telemetry] fetch error", err); }
			if (jQuery(document).alp_snackbar_warning) { jQuery(document).alp_snackbar_warning("Save failed"); }
			if (msg) { msg.removeAttribute("hidden"); msg.textContent = "Error"; msg.style.background = "#ef4444"; msg.style.display = "inline-block"; }
		})
		.then(function () {
			if (btn) { btn.disabled = false; btn.textContent = origBtn; }
			setTimeout(function () { if (msg) { msg.setAttribute("hidden", "hidden"); } }, 2400);
		});
}
jQuery(document).on("submit", "#alp-usage-tracking-form", function (e) {
	e.preventDefault();
	alpSaveUsageTrackingForm();
});
jQuery(document).on("click", ".alp-lic-telemetry-card .zui-lic-savebtn", function (e) {
	e.preventDefault();
	alpSaveUsageTrackingForm();
});
