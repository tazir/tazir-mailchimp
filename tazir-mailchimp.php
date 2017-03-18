<?php
/*
Plugin Name: Tazir - MailChimp form Includer
Description: Include MailChimp form code in widget using AJAX. Could add 2 widgets in same page
Version: 1.0
Author: Tazir
Author URI: http://wp.bdidut.info/
License: GNU GPLv3
Installation:
	1. Upload the files
	2. Put your api key (line 142) and your Mailchimp data center (line 169)
	3. Put your list ID (line 89)
	4. Now you can add the widget on your website
*/

// Enqueue JS file
define('TAZIRURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );

function ajaxtazir_enqueuescripts() {
	wp_enqueue_script('ajaxtazir', TAZIRURL.'/ajaxtazir.js', array('jquery'));
	wp_localize_script( 'ajaxtazir', 'ajaxtazirajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'ajaxtazir_enqueuescripts');

/* Based on http://www.wpexplorer.com/create-widget-plugin-wordpress */
class tazir_mc extends WP_Widget {

	// constructor
	function tazir_mc() {
		$widget_ops = array('classname' => 'tazir_mc_class', 'description' => __('Include MailChimp form code in widget using AJAX', 'tazir_mc'));
		$control_ops = array('width' => 400, 'height' => 300);
		parent::__construct(false, $name = __('MailChimp form - Tazir', 'tazir_mc'), $widget_ops, $control_ops );
	}

	// widget form creation
	function form($instance) {	
		// Check values
		if( $instance) {
			 $title = esc_attr($instance['title']);
			 $text = esc_attr($instance['text']);
			 $textarea = esc_textarea($instance['textarea']);
			 $checkbox = esc_attr( $instance['checkbox'] );
		} else {
			 $title = '';
			 $text = '';
			 $textarea = '';
			 $checkbox = '';
		} ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'tazir_mc'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Text:', 'tazir_mc'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" type="text" value="<?php echo $text; ?>" />
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('textarea'); ?>"><?php _e('Textarea:', 'tazir_mc'); ?></label>
			<textarea class="widefat" id="<?php echo $this->get_field_id('textarea'); ?>" name="<?php echo $this->get_field_name('textarea'); ?>"><?php echo $textarea; ?></textarea>
		</p>
		<p>
			<input id="<?php echo esc_attr( $this->get_field_id( 'checkbox' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'checkbox' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?> />
			<label for="<?php echo esc_attr( $this->get_field_id( 'checkbox' ) ); ?>"><?php _e( 'Second Mailchimp form on the page', 'tazir_mc' ); ?></label>
		</p>
		<?php
	}

	// widget update
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['textarea'] = strip_tags($new_instance['textarea']);
		$instance['checkbox'] = strip_tags($new_instance['checkbox']);
		return $instance;
	}

	// widget display
	function widget($args, $instance) {
		extract( $args );
		// these are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$text = $instance['text'];
		$textarea = $instance['textarea'];
		$checkbox = $instance['checkbox'];
		$nonce = wp_create_nonce( 'ajaxtazir' );
		$coffee_lst = 'YOUR LIST ID';
		
		echo $before_widget;
		// Openning tags + widgets fields
		echo '<!-- Begin MailChimp Signup Form -->';
		echo '<div class="widget-text tazir_mc_box"><form id="invite';
		if( $checkbox && $checkbox == '1' ) //Second tazir_mc widget on the page
			echo '_nd';
		echo '">';		
		if ( $title ) {
		  echo $before_title . $title . $after_title;
		}
		if( $text ) {
		  echo '<p class="tazir_mc_text">'.$text.'</p>';
		}
		if( $textarea ) {
		 echo '<p class="tazir_mc_textarea">'.$textarea.'</p>';
		}

		// MailChimp & hidden fields
		echo '<input type="hidden" name="lst" id="lst';
		if( $checkbox && $checkbox == '1' ) //Second tazir_mc widget on the page
			echo '_nd';
		echo '" value="'. $coffee_lst .'"/>';
		echo '<input type="text" placeholder="'. __('Email address', 'tazir_mc') .'" name="email" id="iMail';
		if( $checkbox && $checkbox == '1' ) //Second tazir_mc widget on the page
			echo '_nd';
		echo '" />';
		echo '<input type="hidden" name="taz_nonce" id="taz_nonce" value="'. $nonce .'"/>';
		echo '<input class="mc-new-submit" value="'. __('Submit', 'tazir_mc') .'" id="cf-submitted';
		if( $checkbox && $checkbox == '1' ) //Second tazir_mc widget on the page
			echo '_nd';
		echo '" />
			  </form>
			<span id="mcresult';
		if( $checkbox && $checkbox == '1' ) //Second tazir_mc widget on the page
			echo '_nd';
		echo '"></span>
			</div>
			<!--End mc_embed_signup-->';

		echo $after_widget;
	}
}

// register widget
add_action('widgets_init', create_function('', 'return register_widget("tazir_mc");'));


function send_my_mailchimp() {
	check_ajax_referer( "ajaxtazir" );
	$merge_vars = array(); $iname = $mygrp = '';

	$apiKey = 'PUT YOUR API KEY HERE';
	$listId = sanitize_key($_POST['lst']);
	$email = sanitize_email($_POST['email']);
	$iname = sanitize_text_field($_POST['iname']);

/*	if ($_POST['grp'] && $_POST['grp'] != '') // interest categories
		$mygrp = array(sanitize_key($_POST['grp']) => true);
	if($iname != '')
		$merge_vars = Array('FNAME' => $iname);
*/
	$data = array(
	    'email_address'=>$email,
	    'status' => 'subscribed', // "subscribed","unsubscribed","cleaned","pending"
	);
	if($mygrp != '')
		$data = array_merge( array('interests' => $mygrp), $data );
	if (count($merge_vars) >0)
		$data = array_merge( array('merge_vars' => $merge_vars), $data);

	$payload = json_encode($data);
	$memberId = md5(strtolower($email));
	/* You should change the next line according to your Mailchimp data center */
	$submit_url = "https://us15.api.mailchimp.com/3.0/lists/$listId/members/$memberId";
//	$submit_url = "https://us15.api.mailchimp.com/3.0/lists/$listId/interest-categories";
	$ch = curl_init();
	$options = array( CURLOPT_URL	=> $submit_url,
			CURLOPT_POST	=> true,
			CURLOPT_USERPWD	=> 'tazir:' . $apiKey,
			CURLOPT_TIMEOUT	=> 10,
			CURLOPT_HTTPHEADER	=> ['Content-Type: application/json'],
			CURLOPT_USERAGENT	=> 'PHP-MCAPI/2.0',
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_CUSTOMREQUEST	=> 'PUT', //'GET'
			CURLOPT_POSTFIELDS	=> $payload
	);

	curl_setopt_array($ch, $options);	 
	$result = curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close ($ch);
	$data = json_decode($result);
	if ($httpCode == '200'){
	    echo "הכתובת ". $_POST['email']. " נוספה לרשימה. תודה";
	} else {
		echo $data->detail;
	}
//	echo $httpCode;
//	var_dump($data);
	die();
}
add_action( 'wp_ajax_myMailchimp', 'send_my_mailchimp' );
add_action( 'wp_ajax_nopriv_myMailchimp', 'send_my_mailchimp' );

?>
