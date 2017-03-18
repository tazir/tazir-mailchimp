jQuery(document).ready(function(){
	jQuery('#cf-submitted').click(function() {
		if (tazirCheck( escape(jQuery('#iMail').val()) )) {
		jQuery.ajax({
			type: 'post',url: ajaxtazirajax.ajaxurl,data: { action: 'myMailchimp', lst: jQuery( '#lst' ).val(), email: jQuery( '#iMail' ).val(), grp: jQuery( '#grp' ).val(), _ajax_nonce: jQuery('#taz_nonce').val() },
			beforeSend: function() {console.log(jQuery('#taz_nonce').val()); jQuery("#mcresult").html('Loading...<img src="/wp-admin/images/wpspin_light.gif">').css('color', 'green');}, 
			success: function(result){ //so, if data is retrieved, store it in html
				jQuery('#mcresult').html(result).css('color', 'green');
				jQuery('#mcresult').focus();
			},
			error: function() {
				jQuery('#mcresult').html('מצטערים, יש בעיה בשליחת הטופס').css('color', 'red');
			}
		}); //close jQuery.ajax
		} //tazirCheck - true
		else {
			jQuery('#mcresult').html('כתובת אימייל שגוייה. נא לבדוק ולהכניס שוב').css('color', 'red');
			jQuery('#iMail').focus();
		}
		return false;
	}); //#cf-submitted.click
	jQuery('#cf-submitted-nd').click(function() {
		if (tazirCheck( escape(jQuery('#iMail_nd').val()) )) {
		jQuery.ajax({
			type: 'post',url: ajaxtazirajax.ajaxurl,data: { action: 'myMailchimp', lst: jQuery( '#lst_nd' ).val(), email: jQuery( '#iMail_nd' ).val(), grp: jQuery( '#grp_nd' ).val(), _ajax_nonce: jQuery('#taz_nonce').val() },
			beforeSend: function() {console.log(jQuery('#taz_nonce').val()); jQuery("#mcresult_nd").html('Loading...<img src="/wp-admin/images/wpspin_light.gif">').css('color', 'green');}, 
			success: function(result){ //so, if data is retrieved, store it in html
				jQuery('#mcresult_nd').html(result).css('color', 'green');
				jQuery('#mcresult_nd').focus();
			},
			error: function() {
				jQuery('#mcresult_nd').html('מצטערים, יש בעיה בשליחת הטופס').css('color', 'red');
			}
		}); //close jQuery.ajax
		} //tazirCheck - true
		else {
			jQuery('#mcresult_nd').html('כתובת אימייל שגוייה. נא לבדוק ולהכניס שוב').css('color', 'red');
			jQuery('#iMail_nd').focus();
		}
		return false;
	}); //#cf-submitted-nd.click
});

function tazirCheck(iMail) {
	var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
	return re.test(iMail);
}
