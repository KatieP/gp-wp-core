<?php
/*
Plugin Name: Green Pages: Theme Plugin
Plugin URI: http://www.thegreenpages.com.au/
Description: Extra settings, forms, etc for the Green Pages theme.
Version: 0.1
Author: Eddy Respondek
Author URI: 
License: 
*/

define( 'GP_VERSION', '0.1' );
define( 'GP_DB_VERSION', '0.4' );
define( 'GP_PLUGIN_DIR', WP_PLUGIN_DIR . '/gp-theme' );
define( 'GP_PLUGIN_URL', plugins_url( '/gp-theme' ) );

require_once( GP_PLUGIN_DIR . '/core/gp-core.php' );
require_once( GP_PLUGIN_DIR . '/core/gp-email-notification.php' );
require_once( GP_PLUGIN_DIR . '/pages/gp-shortcodes.php' );

add_action( 'init', 'gp_set_core_globals' );
add_action( 'admin_init', 'gp_run_updates' );
add_action( 'admin_menu', 'gp_set_admin_menu' );

# We're using admin_init instead of admin_head because it doesn't handle enqueue_script/style() but...
add_action('admin_init', 'gp_plugin_scripts');
add_action('init', 'gp_site_scripts');
# ...but we use login_head here instead because it doesn't recognize enqueue_script/style(). Duh! There's also a wp_admin_css(). Ummm, why?!
add_action('login_head', 'gp_login_scripts');

add_action('wp_login', 'gp_session_onlogin');
add_action('wp_logout', 'gp_session_onlogout');
add_action( 'init', 'gp_session_handler' );

remove_action('wp_head', 'wp_generator');

function default_login_redirect( $redirect, $request_redirect )
{
    if ( $request_redirect === '' )
        $redirect = home_url();
    return $redirect; 
}
add_filter( 'login_redirect', 'default_login_redirect', 10, 2 );

function sanitize_username( $username, $raw_username, $strict ) {
	$username = $raw_username;
	$username = wp_strip_all_tags( $username );
	$username = remove_accents( $username );
	// Kill octets
	$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

	// If strict, reduce to ASCII for max portability.
	if ( $strict )
		$username = preg_replace( '|[^a-zA-Z0-9 _.\-]|i', '', $username );

	$username = trim( $username );
	// Consolidate contiguous whitespace
	$username = preg_replace( '|\s+|', ' ', $username );

	return $username;
}
add_filter( 'sanitize_user', 'sanitize_username', 10, 3 );

add_filter('admin_title', 'gp_admin_title');
function gp_admin_title($admin_title) {
	global $title;
	return get_bloginfo('name') . " &rsaquo; " . wp_specialchars( strip_tags( $title ) );
}

function gp_set_core_globals() {
	global $gp;
	global $current_user;
	
	$current_user = wp_get_current_user();
	
	$gp->plugin->prefix = 'gp_theme';
	$gp->loggedin_user->id = $current_user->ID;
}

function gp_run_updates() {
	if ( !current_user_can('administrator') )
		return false;
	
	if (get_option('GP_DB_VERSION') != GP_DB_VERSION) {
		gp_core_create_tables();
		update_option('GP_DB_VERSION', GP_DB_VERSION);
	}	
}

function gp_plugin_scripts() {
	# Re-register to place BEFORE our custom style. We are adding to this stylesheet. (Isn't there a better way to order this?)
	wp_deregister_style('colors');
    wp_register_style('colors', WP_ADMIN_DIR . '/css/colors-fresh.css');
    wp_enqueue_style('colors');
    
    # Our custom admin style.
    wp_register_style('gp_admin', GP_PLUGIN_URL . '/css/gp-admin.css');
    wp_enqueue_style('gp_admin');
	
    # Add our own Jquery.
	wp_deregister_script('jquery');
	wp_register_script('jquery', GP_PLUGIN_URL . '/js/jquery-1.7.1.min.js');
    wp_enqueue_script('jquery');
    
    if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" || parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/user-edit.php" ) {
    	wp_register_script('gp_textareacounter', GP_PLUGIN_URL . '/js/textareacounter.js');
    	wp_enqueue_script('gp_textareacounter');
     }
    
    //if ($_REQUEST['post_type'] == 'gp_events' || $_REQUEST['post_type'] == 'gp_competitions') {
    	wp_register_style('jquery-ui', GP_PLUGIN_URL . '/css/jquery-ui-1.8.9.custom.css');
    	wp_enqueue_style('jquery-ui');
    	
    	wp_register_script('jquery-ui', GP_PLUGIN_URL . '/js/jquery-ui-1.8.9.custom.min.js');
    	wp_enqueue_script('jquery-ui');
    	
    	wp_register_script('jquery-ui-datepicker', GP_PLUGIN_URL . '/js/jquery.ui.datepicker.js');
    	wp_enqueue_script('jquery-ui-datepicker');

    	wp_register_script('pubforce-admin', GP_PLUGIN_URL . '/js/pubforce-admin.js');
    	wp_enqueue_script('pubforce-admin');
    //}
}

function gp_login_scripts() {
	echo '<link rel="stylesheet" href="' . GP_PLUGIN_URL . '/css/gp-login.css" type="text/css" media="all" />';
}

function gp_site_scripts() {
	global $current_user;

	if(!is_admin()){
		wp_register_style('reset', GP_PLUGIN_URL . '/css/reset.css');
    	wp_enqueue_style('reset');
		
		wp_register_style('generic', get_bloginfo('template_url') . '/template/generic.css');
    	wp_enqueue_style('generic');
		
		wp_deregister_script('jquery');
		wp_register_script('jquery', GP_PLUGIN_URL . '/js/jquery-1.7.1.min.js');
    	wp_enqueue_script('jquery');
		
    	// Required for both Pirobox and File Upload. Must be at Footer for File Upload.
		wp_register_script('jquery-ui', GP_PLUGIN_URL . '/js/jquery-ui-1.8.16.min.js', false, false, true);
	    wp_enqueue_script('jquery-ui');
	    
	    wp_register_script('pirobox-extended', GP_PLUGIN_URL . '/js/pirobox_extended/js/pirobox_extended.js');
	    wp_enqueue_script('pirobox-extended');
		
		if ($current_user->subscription["subscription-greenrazor"] != "true" || !is_user_logged_in()) {
			wp_register_script('boxy', GP_PLUGIN_URL . '/js/jquery.boxy.js');
	   		wp_enqueue_script('boxy');	
		}
		
		wp_register_script('gp', GP_PLUGIN_URL . '/js/gp.js');
	    wp_enqueue_script('gp');
		
		wp_register_script('hashchange', GP_PLUGIN_URL . '/js/jquery.ba-hashchange.min.js');
	    wp_enqueue_script('hashchange');
	    
	    /*
		global $post;

	    if (get_post_type($post->ID) != "page") {
		wp_register_script('gp_socialbar', GP_PLUGIN_URL . '/js/gp_socialbar.js');
		wp_enqueue_script('gp_socialbar');
	    }
	    */

	    #if (basename(get_permalink()) == 'list-your-business-4') {
		    wp_register_script('jquery-templates', 'http://ajax.aspnetcdn.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js', false, false, true);
		    wp_enqueue_script('jquery-templates');
		    
		    wp_register_script('jquery-iframe-transport', GP_PLUGIN_URL . '/js/jquery.iframe-transport.js', false, false, true);
		    wp_enqueue_script('jquery-iframe-transport');
		    
		    wp_register_script('jquery-fileupload', GP_PLUGIN_URL . '/js/jquery.fileupload.js', false, false, true);
		    wp_enqueue_script('jquery-fileupload');
		    
		    wp_register_script('jquery-fileupload-ui', GP_PLUGIN_URL . '/js/jquery.fileupload-ui.js', false, false, true);
		    wp_enqueue_script('jquery-fileupload-ui');
		    
		    wp_register_script('file-upload', GP_PLUGIN_URL . '/js/file-upload.js', false, false, true);
		    wp_enqueue_script('file-upload');
		    
		    wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css');
		    wp_enqueue_style('jquery-ui');
		    
		    wp_register_style('fileupload-ui', GP_PLUGIN_URL . '/css/jquery.fileupload-ui.css');
		    wp_enqueue_style('fileupload-ui');
	    #}
    	
		if ($current_user->subscription["subscription-greenrazor"] != "true" || !is_user_logged_in()) {
			wp_register_style('boxy', GP_PLUGIN_URL . '/js/boxy.css');
    		wp_enqueue_style('boxy');
		}
		
		wp_register_style('pirobox-style', GP_PLUGIN_URL . '/js/pirobox_extended/css_pirobox/style_2/style.css');
    	wp_enqueue_style('pirobox-style');
    	
    	wp_register_style('pirobox-css', GP_PLUGIN_URL . '/js/pirobox_extended/css/css.css');
    	wp_enqueue_style('pirobox-css');
    	
    	wp_register_style('pirobox-default', GP_PLUGIN_URL . '/js/pirobox_extended/content/css/default.css');
    	wp_enqueue_style('pirobox-default');
	}
}

function gp_session_onlogout() {
	session_destroy();
	session_unset();
}

function gp_session_onlogin() {
	// Force create new session id and clear existing session data.
	session_regenerate_id(true);
}

function gp_session_handler() {
	// ref: http://stackoverflow.com/questions/520237/how-do-i-expire-a-php-session-after-30-minutes
	// ref: http://stackoverflow.com/questions/5081025/php-session-fixation-hijacking
	
	/*
	 * Note about sessions on Linux:
	 * What most people also don't know, is that most Linux distributions (Debian and Ubuntu for me atleast) have a cronbjob that cleans up your session dir using the value set in the global /etc/php5/php.ini (which defaults to 24mins). So even if you set a value larger in your scripts, the cronbjob will still cleanup sessions using the global value.
	 * If you run into that situation, you can set the global value higher in /etc/php5/php.ini, disable the cronjob or even better, do your own session cleanup in a non-systemwide directory or a database.
	 * 
	 * Note about session on Wordpress:
	 * Wordpress is stateless which means it doesn't use sessions. It uses cookies for logins. We have to manually enable sessions and to do that have have to ensure we're not going to destroy everyone's session data everytime something gets changed. The only way to do this is to start new session in the "wp-config.php" file as it's the only file that doesn't get loaded everytime. 
	 */
	
	// Destroy session if older than 60mins
	if (isset($_SESSION['LAST_ACTIVITY']) && (strtotime('now') - $_SESSION['LAST_ACTIVITY'] > 3600)) {
    	session_destroy();
    	session_unset();
	}
	$_SESSION['LAST_ACTIVITY'] = strtotime('now');
	
	// Avoid "session hijacking", track HTTP_USER_AGENT strings between requests.
	if (isset($_SESSION['USER_AGENT']) && ($_SESSION['USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'])) {
    	session_destroy();
    	session_unset();
	} else if (!isset($_SESSION['USER_AGENT'])) {
		$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	}
	
	// Avoid "session fixation", periodically (every 60mins) regenerate the session id since it's creation. 
	if (!isset($_SESSION['CREATED'])) {
    	$_SESSION['CREATED'] = strtotime('now');
	} else if (strtotime('now') - $_SESSION['CREATED'] > 3600) {
    	session_regenerate_id(); // in the examples they call 'true' to destory all session data - what's the point of that?
    	$_SESSION['CREATED'] = strtotime('now');
	}
	
	return true;
}


/*
 * Notes for when we implement full DB sessions -
 * ref: http://www.djangobook.com/en/beta/chapter12/
 * For anonymous users use cookies to store hashed session ids
 */
function gp_db_session_handler($form_id, $form_name) {
	global $wpdb, $current_user;
	
	define(SECRET, "ge8upazuwabutuyuzukawrachurebaja");
	
	$qrystring = $wpdb->prepare("SELECT UID, HASH, LAST_ACTIVITY, USER_AGENT, CREATED FROM ". $form_name . " WHERE ID = " . $form_id);
	$qryresults = $wpdb->get_row($qrystring);
	
	if ( empty( $qryresults->UID ) ) {
		$success = false;
	} else {
		$UID = $qryresults->UID;
		$HASH = $qryresults->HASH;
		$LAST_ACTIVITY = $qryresults->LAST_ACTIVITY;
		$USER_AGENT = $qryresults->USER_AGENT;
		$CREATED = $qryresults->CREATED;
	}
	
	// Destroy session if older than 30mins
	if (strtotime('now') - $LAST_ACTIVITY > 1800) {
    	$success = false;
	}

	// Avoid "session hijacking", track HTTP_USER_AGENT strings between requests.
	if ($USER_AGENT != $_SERVER['HTTP_USER_AGENT']) {
		$success = false;
	}
	
	// If anonymous user then use cookies. Otherwise use Wordpress authentication.
	if (!is_user_logged_in()) {
		if (base64_decode($_COOKIE[$form_name]) !=  md5(SECRET . $HASH . $form_id) . "-" . $form_id) {
			$success = false;
		}
		if ($UID != 0) {
			$success = false;
		}
	} else {
		if ($UID != $current_user->ID) {
			$success = false;
		}
	}
	
	if ($success === false) {
		$UID = 0;
		$HASH = null;
		$LAST_ACTIVITY = strtotime('now');
		$USER_AGENT = $_SERVER['HTTP_USER_AGENT'];
		$CREATED = strtotime('now');
		
		if (!is_user_logged_in()) {
			// Delete cookie
			$HASH = md5(uniqid(rand(), true) . SECRET);
			$SIG = md5(SECRET . $HASH . $form_id);
			$cookie = base64_encode($SIG . "-" . $form_id);
			setcookie($form_name, $cookie, 0, '/', 'www.thegreenpages.com.au', isset($_SERVER["HTTPS"]), true);
			#session_set_cookie_params('o, /', 'www.thegreenpages.com.au', isset($_SERVER["HTTPS"]), true)
			// Create cookie
		} else {
			// Delete DB record
			$UID = $current_user->ID;
		}
		// Create DB record
		$wpdb->insert( $form_name, array('UID' => $UID, 'HASH' => $HASH, 'LAST_ACTIVITY' => $LAST_ACTIVITY, 'USER_AGENT' => $USER_AGENT, 'CREATED' => $CREATED), array('%d', '%s', '%s', '%s', '%s'));
		
		$update_result = $wpdb->update( $form_name, array('HASH' => $HASH, 'LAST_ACTIVITY' => $LAST_ACTIVITY, 'USER_AGENT' => $USER_AGENT, 'CREATED' => $CREATED), array('ID' => $form_id, 'UID' => $UID), array('%s', '%s', '%s', '%s'), array('%d', '%d'));
		
		return false;
	} else {
		$LAST_ACTIVITY = strtotime('now');
		
		if (!is_user_logged_in()) {
			// Avoid "session fixation", periodically (every 30mins) regenerate the session id since it's creation. 
			if (time() - $CREATED > 1800) {
				$HASH = md5(uniqid(rand(), true) . SECRET);
				$CREATED = strtotime('now');
				# update HASH, CREATED in db
				$SIG = md5(SECRET . $HASH . $form_id);
				$cookie = base64_encode($SIG . "-" . $form_id);
				# update or destory cookie??
				setcookie($form_name, $cookie, 0, '/', 'www.thegreenpages.com.au', isset($_SERVER["HTTPS"]), true);
			}
		}
		
		// Update DB record
		$update_result = $wpdb->update( $form_name, array('HASH' => $HASH, 'LAST_ACTIVITY' => $LAST_ACTIVITY), array('ID' => $form_id, 'UID' => $UID), array('%s', '%s'), array('%d', '%d'));
		
		return true;	
	}
}

add_action('wpmu_activate_user', 'gp_wpmu_activate_user');
function gp_wpmu_activate_user() {
	global $current_site;
	
	if ( !empty( $meta[ 'subscribe_greenrazor' ] ) ) {
		/*
		if (cm_subscribe($subscription_post['subscription-greenrazor'])) {
			update_usermeta($user_id, 'subscription', $subscription_post );
		} else {
			$subscription_post['subscription-greenrazor']='false';
			update_usermeta($user_id, 'subscription', $subscription_post );
		}
		*/
	}
	
	if ( !empty( $meta[ 'subscribe_advertiser' ] ) ) {
		if ( $meta[ 'subscribe_advertiser' ] == true ) {
			update_usermeta($user_id, 'advertiser', true );
		} else {
			update_usermeta($user_id, 'advertiser', false );
		}
	}
}
?>
