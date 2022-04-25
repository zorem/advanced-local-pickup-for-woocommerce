/* alp_snackbar jquery */
(function( $ ){
	$.fn.alp_snackbar = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var alp_snackbar = $("<article></article>").addClass('snackbar-log snackbar-log-success snackbar-log-show').text( msg );
		$(".snackbar-logs").append(alp_snackbar);
		setTimeout(function(){ alp_snackbar.remove(); }, 3000);
		return this;
	}; 
})( jQuery );

/* alp_snackbar_warning jquery */
(function( $ ){
	$.fn.alp_snackbar_warning = function(msg) {
		if ( jQuery('.snackbar-logs').length === 0 ){
			$("body").append("<section class=snackbar-logs></section>");
		}
		var alp_snackbar_warning = $("<article></article>").addClass( 'snackbar-log snackbar-log-error snackbar-log-show' ).html( msg );
		$(".snackbar-logs").append(alp_snackbar_warning);
		setTimeout(function(){ alp_snackbar_warning.remove(); }, 3000);
		return this;
	}; 
})( jQuery );
/*header script end*/ 

/*
on change alert box open
*/
jQuery(document).on("click", ".back_to_notice", function(){
	var r = confirm( 'The changes you made will be lost if you navigate away from this page.' );
	if (r === true ) {
	} else {	
		return false;
	}
});

function setting_change_trigger() {	
	jQuery('.woocommerce-save-button').removeAttr("disabled");
	jQuery('.zoremmail-layout-content-header .wclp-save').html('Save Changes');
	jQuery('.zoremmail-back-wordpress-link').addClass('back_to_notice');
}

function save_customizer_email_setting(){
	
	jQuery('.zoremmail-layout-content-preview').addClass('customizer-unloading');
	setting_change_trigger();
	
	var form = jQuery('#zoremmail_email_options');
	jQuery.ajax({
		url: ajaxurl,//csv_workflow_update,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",		
		success: function(response) {
			if( response.success === "true" ){
				jQuery('iframe').attr('src', jQuery('iframe').attr('src'));
				//jQuery('#email_preview').on('load', function(){
					//jQuery('.zoremmail-layout-content-preview').removeClass('customizer-unloading');
				//});
			} else {
				//
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
}


var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

jQuery(document).on("click", ".zoremmail-menu-submenu-title", function(){
	if (jQuery(this).next('.zoremmail-menu-contain').hasClass('active')) {
        jQuery(this).next('.zoremmail-menu-contain').removeClass('active');
		jQuery(this).find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
		jQuery(this).css('color', '#124fd6');
    } else {
		jQuery('.zoremmail-menu-submenu-title').find('.dashicons').removeClass('dashicons-arrow-down-alt2').addClass('dashicons-arrow-right-alt2');
		jQuery('.zoremmail-menu-contain').removeClass('active');
		jQuery(this).next('.zoremmail-menu-contain').addClass('active');
		jQuery(this).find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
		jQuery('.zoremmail-menu-submenu-title').css('color', '#124fd6');
		jQuery(this).css('color', '#212121');
	}
	change_submenu_item();
});

jQuery(document).on("click", ".zoremmail-panel-title", function(){
	jQuery('.header_orderStatus').show();
	jQuery('.zoremmail-layout-content-preview').addClass('customizer-unloading');
	jQuery( ".zoremmail-panel-title, .zoremmail-layout-sider-heading .main_logo" ).hide();
	jQuery( ".customize-section-back" ).show();
	jQuery('.tgl-btn-parent').show();
	var lable = jQuery(this).data('label');
	jQuery( '.customizer_Breadcrumb' ).html( lable );
	var id = jQuery(this).attr('id');
	var Status = jQuery('#orderStatus').val();
	jQuery('.zoremmail-menu-submenu-title, .customize-section-title').each(function(index, element) {
		if ( jQuery(this).data('id') ===  id ) {
			if ( jQuery( this ).hasClass( "zoremmail-menu-status" ) ) {
				if ( jQuery( this ).hasClass( Status ) ) {
					jQuery(this).addClass('open');
				} else {
					jQuery(this).removeClass('open');
				}
			} else {
				jQuery(this).addClass('open');
			}
		} else {
			jQuery(this).removeClass('open');
		}
	});
	
	jQuery( '.zoremmail-menu-submenu-title.'+id+'_first_section' ).trigger('click');
	
	//For click on fies section 
	/*if ( 'email_content' == id ) {
		jQuery( '.zoremmail-menu-submenu-title.email_content_first_section.'+status ).trigger('click');
	} else {
		jQuery( '.zoremmail-menu-submenu-title.'+id+'_first_section' ).trigger('click');
	}*/
	
	var sPageURL = window.location.href.split('&')[0];
	window.history.pushState("object or string", sPageURL, sPageURL+'&type='+id+'&email_type='+Status);
	
	var iframe_url = alp_customizer.email_iframe_url+'&type='+id+'&email_type='+Status;
	
	jQuery('.options_panel').attr('data-iframe_url',iframe_url);
	jQuery('iframe').attr('src', iframe_url);
	
	change_submenu_item();
	jQuery( ".tgl-btn-parent span" ).hide();
	jQuery( ".tgl-btn-parent .tgl_"+Status ).show();
	
	jQuery( '#orderStatus' ).select2({
		templateSelection: text_contain,
		minimumResultsForSearch: Infinity
	});
	
});

jQuery(document).on("click", ".customize-section-back", function(){
	jQuery('.header_orderStatus').hide();
	jQuery(".customize-section-title").removeClass('open');
	jQuery( ".zoremmail-panel-title" ).show();
	jQuery('.zoremmail-menu-contain').removeClass('active');
	jQuery('.zoremmail-menu-submenu-title').removeClass('open');
	jQuery('.zoremmail-menu-submenu-title').removeClass('active');
	jQuery('.zoremmail-menu-submenu-title').find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
});

jQuery(document).on("click", "#zoremmail_email_options .wclp-save", function(){
	"use strict";
	var form = jQuery('#zoremmail_email_options');
	var btn = jQuery('#zoremmail_email_options .wclp-save');
	jQuery.ajax({
		url: ajaxurl,//csv_workflow_update,		
		data: form.serialize(),
		type: 'POST',
		dataType:"json",
		beforeSend: function(){
			btn.prop('disabled', true).html('Please wait..');
		},		
		success: function(response) {
			if( response.success === "true" ){
				btn.prop('disabled', true).html('Saved');
				jQuery(document).alp_snackbar( "Settings Successfully Saved." );
			} else {
				if( response.permission === "false" ){
					btn.prop('disabled', false).html('Save Changes');
					jQuery(document).alp_snackbar_warning( "you don't have permission to save settings." );
				}
			}
		},
		error: function(response) {
			console.log(response);			
		}
	});
});

jQuery(document).on("change", "#orderStatus", function(){
	
	"use strict";
	jQuery('.zoremmail-layout-content-preview').addClass('customizer-unloading');
	var Status = jQuery('#orderStatus').val();
	if ( Status == 'pickup' ) {
		jQuery('.customize-section-back').trigger('click');
		jQuery('#email_content').trigger('click');		
	}
	var sPageURL = window.location.href.split('&')[0];
	var id = jQuery('.zoremmail-menu-submenu-title.open').data('id');
	window.history.pushState("object or string", sPageURL, sPageURL+'&type='+id+'&email_type='+Status);
	
	var iframe_url = alp_customizer.email_iframe_url+'&type='+id+'&email_type='+Status;
	
	jQuery('.options_panel').attr('data-iframe_url',iframe_url);
	jQuery('iframe').attr('src', iframe_url);
	
	change_submenu_item();
	jQuery( ".tgl-btn-parent span" ).hide();
	jQuery( ".tgl-btn-parent .tgl_"+Status ).show();

});

function change_submenu_item() {
	var Status = jQuery('#orderStatus').val();
	jQuery( '.all_status_submenu' ).hide();
	jQuery( '.all_status_submenu.' + Status + '_sub_menu' ).show();
}

function text_contain(state) {
	var type = getUrlParameter('type');
	if ( type == 'email_design' ) {
		return 'Preview: ' + state.text;	
	} else {
		return 'Editing: ' + state.text;
	}

};

jQuery('iframe').load(function(){
	jQuery('.zoremmail-layout-content-preview').removeClass('customizer-unloading');
	jQuery("#email_preview").contents().find( 'div#query-monitor-main' ).css( 'display', 'none');
	jQuery( '.zoremmail-layout-content-media .last-checked .dashicons' ).trigger('click');	
});

jQuery(document).on("click", ".customize-section-back", function(){
	jQuery('.tgl-btn-parent').hide();
	jQuery( '.customizer_Breadcrumb' ).html( 'Customizer' );
	jQuery( ".customize-section-back" ).hide();
	jQuery( ".zoremmail-panel-title, .zoremmail-layout-sider-heading .main_logo" ).show();
	jQuery('.zoremmail-menu-contain').removeClass('active');
	jQuery('.zoremmail-menu-submenu-title').removeClass('open');
	jQuery('.zoremmail-menu-submenu-title').removeClass('active');
	jQuery('.zoremmail-menu-submenu-title').find('.dashicons').removeClass('dashicons-arrow-right-alt2').addClass('dashicons-arrow-down-alt2');
	var Status = jQuery('#orderStatus').val();
	if ( Status == 'pickup' ) {
		jQuery('#email_design').hide();
	} else {
		jQuery('#email_design').show();
	}
});

jQuery(document).ready(function(){
	
	jQuery('.zoremmail-input.color').wpColorPicker();
	jQuery( '#orderStatus' ).select2({
		templateSelection: text_contain,
		minimumResultsForSearch: Infinity
	});
	
	var Status = jQuery('#orderStatus').val();
	if ( Status == 'pickup' ) {
		jQuery('#email_design').hide();
	} else {
		jQuery('#email_design').show();
	}
	
	jQuery('.zoremmail-input').on("keyup", function(){
		setting_change_trigger();
	});
	
	jQuery(document).on("change", ".tgl.tgl-flat, .zoremmail-checkbox, .zoremmail-input.color, .zoremmail-range, .zoremmail-input.select", function(){
		setting_change_trigger();
	});
	
	jQuery( ".zoremmail-input.heading" ).keyup( function( event ) {
		var str = event.target.value;
		var res = str.replace("{site_title}", alp_customizer.site_title);
		var res = res.replace("{order_number}", alp_customizer.order_number);
		var res = res.replace("{customer_first_name}", alp_customizer.customer_first_name);
		var res = res.replace("{customer_last_name}", alp_customizer.customer_last_name);
		var res = res.replace("{customer_company_name}", alp_customizer.customer_company_name);
		var res = res.replace("{customer_username}", alp_customizer.customer_username);
		var res = res.replace("{customer_email}", alp_customizer.customer_email);
		var res = res.replace("{est_delivery_date}", alp_customizer.est_delivery_date);
		if( str ){				
			jQuery("#email_preview").contents().find( '#header_wrapper h1' ).text(res);
		} else{
			jQuery("#email_preview").contents().find( '#header_wrapper h1' ).text(event.target.placeholder);
		}
	});
	
	jQuery( ".zoremmail-input.additional_content" ).keyup( function( event ) {
		var str = event.target.value;
		var res = str.replace("{site_title}", alp_customizer.site_title);
		var res = res.replace("{order_number}", alp_customizer.order_number);
		var res = res.replace("{customer_first_name}", alp_customizer.customer_first_name);
		var res = res.replace("{customer_last_name}", alp_customizer.customer_last_name);
		var res = res.replace("{customer_company_name}", alp_customizer.customer_company_name);
		var res = res.replace("{customer_username}", alp_customizer.customer_username);
		var res = res.replace("{customer_email}", alp_customizer.customer_email);
		var res = res.replace("{est_delivery_date}", alp_customizer.est_delivery_date);
		
		if( str ){				
			jQuery("#email_preview").contents().find( '.wclp_additional_content' ).text(res);
		} else{
			jQuery("#email_preview").contents().find( '.wclp_additional_content' ).text(event.target.placeholder);
		}
		setting_change_trigger();
	});
	
	jQuery( ".zoremmail-input.widget_header_text" ).keyup( function( event ) {
		var str = event.target.value;
		if( str ){				
			jQuery("#email_preview").contents().find( 'h2.local_pickup_email_title' ).text(str);
		} else{
			jQuery("#email_preview").contents().find( 'h2.local_pickup_email_title' ).text(event.target.placeholder);
		}
		setting_change_trigger();
	});
	
	jQuery( ".zoremmail-input.addres_header_text" ).keyup( function( event ) {
		var str = event.target.value;
		if( str ){				
			jQuery("#email_preview").contents().find( '.wclp_location_box1 .wclp_location_box_heading' ).text(str);
		} else{
			jQuery("#email_preview").contents().find( '.wclp_location_box1 .wclp_location_box_heading' ).text(event.target.placeholder);
		}
		setting_change_trigger();
	});
	
	jQuery( ".zoremmail-input.header_hours_text" ).keyup( function( event ) {
		var str = event.target.value;
		if( str ){				
			jQuery("#email_preview").contents().find( '.wclp_location_box2 .wclp_location_box_heading' ).text(str);
		} else{
			jQuery("#email_preview").contents().find( '.wclp_location_box2 .wclp_location_box_heading' ).text(event.target.placeholder);
		}
		setting_change_trigger();
	});
	
	jQuery( "#widget_layout" ).on( "change", function() {
		var value = jQuery(this).val();
		if ( value == '1colums' ) {
			jQuery("#email_preview").contents().find( '.wclp_mail_address' ).css( 'width', '50%' );
			jQuery("#email_preview").contents().find( '.wclp_location_box' ).css( 'width', '' );
			jQuery("#email_preview").contents().find( '.wclp_location_box2' ).css( 'margin-top', '5px' );
		} else {
			jQuery("#email_preview").contents().find( '.wclp_mail_address' ).css( 'width', '' );
			jQuery("#email_preview").contents().find( '.wclp_location_box' ).css( 'width', '49%' );
			jQuery("#email_preview").contents().find( '.wclp_location_box2' ).css( 'margin-top', '0' );
			jQuery("#email_preview").contents().find( '.wclp_location_box2' ).css( 'padding-top', '0' );
			
		}
		setting_change_trigger();
	});
	
	if ( jQuery.fn.wpColorPicker ) { 
		jQuery('#background_color').wpColorPicker({
			change: function(e, ui) {		
				var color = ui.color.toString();
				jQuery("#email_preview").contents().find( '.wclp_location_box_heading' ).css( 'background-color', color );
				jQuery("#email_preview").contents().find( '.wclp_location_box' ).css( 'background-color', color );
				setting_change_trigger();
			}, 	
		});
		jQuery('#border_color').wpColorPicker({
			change: function(e, ui) {		
				var color = ui.color.toString();
				jQuery("#email_preview").contents().find( '.wclp_location_box_heading' ).css( 'border-color', color );
				jQuery("#email_preview").contents().find( '.wclp_location_box' ).css( 'border-color', color );
				setting_change_trigger();
			}, 	
		});
	}	
	
	jQuery( "#padding" ).on( "change", function() {
		var value = jQuery(this).val();
		jQuery("#email_preview").contents().find( '.wclp_location_box_content' ).css( 'padding', value );
		jQuery("#email_preview").contents().find( '.wclp_location_box_heading' ).css( 'padding', value );
		setting_change_trigger();
	});

	jQuery('.hide_widget_header input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery('.hide_addres_header input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery('.hide_hours_header input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery( ".zoremmail-input.pickup_items_heading" ).keyup( function( event ) {
		var str = event.target.value;
		if( str ){				
			jQuery("#email_preview").contents().find( '.pickup_items_heading' ).text(str);
		} else{
			jQuery("#email_preview").contents().find( '.pickup_items_heading' ).text(event.target.placeholder);
		}
		setting_change_trigger();
	});
	
	jQuery('.display_product_images input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery('.display_product_prices input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery('.display_shipping_address input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery('.display_billing_address input').on("click", function(){
		save_customizer_email_setting();
	});
	
	jQuery( ".zoremmail-layout-content-media .dashicons" ).on( "click", function() {
		jQuery(this).parent().siblings().removeClass('last-checked');
		var width = jQuery(this).parent().data('width');
		var iframeWidth = jQuery(this).parent().data('iframe-width');
		jQuery('#template_container, #template_body').css('width', width);
		jQuery( ".zoremmail-layout-content-media .dashicons" ).css('color', '#fff');
		jQuery(this).parent().addClass('last-checked');
		jQuery(this).css('color', '#09d3ac');
		jQuery("#email_preview").css('width', iframeWidth);
		jQuery("#email_preview").contents().find('#template_container, #template_body, #template_footer').css('width', width);
	});
	
	//hide_widget_header
	if ( jQuery('.hide_widget_header input').is(':checked') === true ) {
		jQuery('.widget_header_text .zoremmail-menu-item').hide();
	} else {
		jQuery('.widget_header_text .zoremmail-menu-item').show();
	}
	jQuery('.hide_widget_header input').on("click", function(){
		if ( jQuery(this).is(':checked') === true ) {
			jQuery('.widget_header_text .zoremmail-menu-item').hide();
		} else {
			jQuery('.widget_header_text .zoremmail-menu-item').show();
		}
	});
	
	//hide_addres_header
	if ( jQuery('.hide_addres_header input').is(':checked') === true ) {
		jQuery('.addres_header_text .zoremmail-menu-item').hide();
	} else {
		jQuery('.addres_header_text .zoremmail-menu-item').show();
	}
	jQuery('.hide_addres_header input').on("click", function(){
		if ( jQuery(this).is(':checked') === true ) {
			jQuery('.addres_header_text .zoremmail-menu-item').hide();
		} else {
			jQuery('.addres_header_text .zoremmail-menu-item').show();
		}
	});
	
	//hide_hours_header
	if ( jQuery('.hide_hours_header input').is(':checked') === true ) {
		jQuery('.header_hours_text .zoremmail-menu-item').hide();
	} else {
		jQuery('.header_hours_text .zoremmail-menu-item').show();
	}
	jQuery('.hide_hours_header input').on("click", function(){
		if ( jQuery(this).is(':checked') === true ) {
			jQuery('.header_hours_text .zoremmail-menu-item').hide();
		} else {
			jQuery('.header_hours_text .zoremmail-menu-item').show();
		}
	});
});
