<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed');
/*
Original Plugin Name: OptionTree
Original Plugin URI: http://wp.envato.com
Original Author: Derek Herman
Original Author URI: http://valendesigns.com
*/

/**
 * Functions Load
 *
 */
 /* whitelist options, you can add more register_settings changing the second parameter */
 
 function wppb_register_settings() {
	$premiumPresent = WPPB_PLUGIN_DIR . '/premium/premium.php';
	$addonPresent = WPPB_PLUGIN_DIR . '/premium/addon/addon.php';
	
	register_setting( 'wppb_option_group', 'wppb_default_settings' );
	register_setting( 'wppb_general_settings', 'wppb_general_settings' );
	register_setting( 'wppb_display_admin_settings', 'wppb_display_admin_settings' );
	if (file_exists($premiumPresent)){
		register_setting( 'wppb_profile_builder_pro_serial', 'wppb_profile_builder_pro_serial' );
	}
	if (file_exists($addonPresent)){
		register_setting( 'wppb_addon_settings', 'wppb_addon_settings' );
		register_setting( 'customRedirectSettings', 'customRedirectSettings' );
		register_setting( 'customUserListingSettings', 'customUserListingSettings' );
		register_setting( 'reCaptchaSettings', 'reCaptchaSettings' );
	}
	
	
}

$wppb_premiumAdmin = WPPB_PLUGIN_DIR . '/premium/functions/';	
if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' ))
	include_once($wppb_premiumAdmin.'premium.functions.load.php');    

function wppb_add_plugin_stylesheet() {
		$wppb_generalSettings = get_option('wppb_general_settings');
		
        $styleUrl_default = WPPB_PLUGIN_URL . '/assets/css/front.end.css';
        $styleUrl_white = WPPB_PLUGIN_URL . '/premium/assets/css/front.end.white.css';
        $styleUrl_black = WPPB_PLUGIN_URL . '/premium/assets/css/front.end.black.css';
        $styleFile_default = WPPB_PLUGIN_DIR . '/assets/css/front.end.css';
        $styleFile_white = WPPB_PLUGIN_DIR . '/premium/assets/css/front.end.white.css';
        $styleFile_black = WPPB_PLUGIN_DIR . '/premium/assets/css/front.end.black.css';
        if ( (file_exists($styleFile_default)) && ($wppb_generalSettings['extraFieldsLayout'] == 'yes') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_default);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_white)) && ($wppb_generalSettings['extraFieldsLayout'] == 'white') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_white);
            wp_enqueue_style( 'wppb_stylesheet');
        }elseif ( (file_exists($styleFile_black)) && ($wppb_generalSettings['extraFieldsLayout'] == 'black') ) {
            wp_register_style('wppb_stylesheet', $styleUrl_black);
            wp_enqueue_style( 'wppb_stylesheet');
        }
}


function wppb_show_admin_bar($content){
	global $current_user;
	global $wpdb;
	
	$userRole = '';
	$admintSettingsPresent = get_option('wppb_display_admin_settings','not_found');
	
	if ($admintSettingsPresent != 'not_found'){
		if ($current_user->ID != 0){
				
			$userRole = apply_filters ( 'wppb_user_role_value', $current_user->roles[0], $current_user->ID);
			
			if ($userRole != NULL){
				$getSettings = $admintSettingsPresent[$userRole];
				if ($getSettings == 'show')
					return true;
					
				elseif ($getSettings == 'hide')
					return false;
			
			}else
				return true;
		}
		
	}else
		return true;
		
}

if(!function_exists('wppb_curpageurl')){
    function wppb_curpageurl() {
     $pageURL = 'http';
     if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")) {
		$pageURL .= "s";
	 }
     $pageURL .= "://";
     if ($_SERVER["SERVER_PORT"] != "80") {
      $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
     } else {
      $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
     }
     return $pageURL;
    }
}



if ( is_admin() ){
	/* include the css for the datepicker */
	$wppb_premiumDatepicker = WPPB_PLUGIN_DIR . '/premium/assets/css/';
	if (file_exists ( $wppb_premiumDatepicker.'datepicker.style.css' )){
		add_action('admin_enqueue_scripts', 'wppb_add_datepicker_style');
		function wppb_add_datepicker_style(){
			wp_enqueue_style( 'profile-builder-admin-datepicker-style', WPPB_PLUGIN_URL.'/premium/assets/css/datepicker.style.css', false, PROFILE_BUILDER_VERSION);
		}
	}



	/* register the settings for the menu only display sidebar menu for a user with a certain capability, in this case only the "admin" */
	add_action('admin_init', 'wppb_register_settings');
  

	/* display the same extra profile fields in the admin panel also */
	$wppb_premium = WPPB_PLUGIN_DIR . '/premium/functions/';
	if (file_exists ( $wppb_premium.'extra.fields.php' )){
		include( $wppb_premium.'extra.fields.php' );
		add_action( 'show_user_profile', 'display_profile_extra_fields_in_admin', 10 );
		add_action( 'edit_user_profile', 'display_profile_extra_fields_in_admin', 10 );
		add_action( 'personal_options_update', 'save_profile_extra_fields_in_admin', 10 );
		add_action( 'edit_user_profile_update', 'save_profile_extra_fields_in_admin', 10 );
	}

}else if ( !is_admin() ){
	/* include the stylesheet */
	add_action('wp_print_styles', 'wppb_add_plugin_stylesheet');		

	$wppb_plugin = WPPB_PLUGIN_DIR . '/';

	/* include the menu file for the profile informations */
	include_once($wppb_plugin.'front-end/wppb.edit.profile.php');        		 
	add_shortcode('wppb-edit-profile', 'wppb_front_end_profile_info');

	/*include the menu file for the login screen */
	include_once($wppb_plugin.'front-end/wppb.login.php');       
	add_shortcode('wppb-login', 'wppb_front_end_login');

	/* include the menu file for the register screen */
	include_once($wppb_plugin.'front-end/wppb.register.php');        		
	add_shortcode('wppb-register', 'wppb_front_end_register_handler');	
	
	/* include the menu file for the recover password screen */
	include_once($wppb_plugin.'front-end/wppb.recover.password.php');        		
	add_shortcode('wppb-recover-password', 'wppb_front_end_password_recovery');

	/* set the front-end admin bar to show/hide */
	add_filter( 'show_admin_bar' , 'wppb_show_admin_bar');

	/* Shortcodes used for the widget area. */
	add_filter('widget_text', 'do_shortcode', 11);

	/* check to see if the premium functions are present */
	$wppb_premiumAdmin = WPPB_PLUGIN_DIR . '/premium/functions/';	
	if (file_exists ( $wppb_premiumAdmin.'premium.functions.load.php' )){

		include_once($wppb_premiumAdmin.'premium.functions.load.php');    

		/* filter to set current users custom avatar */
		add_filter('get_avatar', 'wppb_changeDefaultAvatar', 21, 5);
	}

	$wppb_premiumAddon = WPPB_PLUGIN_DIR . '/premium/addon/';	
	if (file_exists ( $wppb_premiumAddon.'addon.functions.php' )){
		//include the file containing the addon functions
		include_once($wppb_premiumAddon.'addon.functions.php');    

		$wppb_addonOptions = get_option('wppb_addon_settings');
		if ($wppb_addonOptions['wppb_userListing'] == 'show'){
		  //add shortcode for the user-listing functionality
		  add_shortcode('wppb-list-users', 'wppb_list_all_users');
		}else
			add_shortcode('wppb-list-users', 'wppb_list_all_users_display_error');
	}
}