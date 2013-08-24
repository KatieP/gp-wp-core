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
define( 'GP_DB_VERSION', '0.5' );
define( 'GP_GEONAMES_VERSION', '0.4' );
define( 'GP_MAXMIND_VERSION', '0.4' );
define( 'GP_DEBIAN_ISOCODES_VERSION', '0.4' );

define( 'GP_PLUGIN_DIR', WP_PLUGIN_DIR . '/gp-theme' );
define( 'GP_PLUGIN_URL', plugins_url( '/gp-theme' ) );
define( 'WP_ADMIN_DIR', ABSPATH . 'wp-admin' );

//spl_autoload_register(function($class) {
    require_once( GP_PLUGIN_DIR . '/core/gp-functions.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-billing-functions.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-db.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-wp-admin.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-geonames.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-maxmind.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-debian-isocodes.php' );
    require_once( GP_PLUGIN_DIR . '/config/_site.php' );
    require_once( GP_PLUGIN_DIR . '/config/_geo.php' );

    global $gp;
    $geo_currentlocation = Geo::getCurrentLocation();
    $gp->location = $geo_currentlocation;

    define( 'SELECTED_COUNTRY', $geo_currentlocation['country_iso2'] );
    
    require_once( GP_PLUGIN_DIR . '/editions/' . SELECTED_COUNTRY . '.php' );
//});

//spl_autoload_register(function($class) {
    require_once( GP_PLUGIN_DIR . '/core/gp-geo.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-email-notification.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-campaignmonitor.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-metaboxes.php' );
    require_once( GP_PLUGIN_DIR . '/pages/gp-shortcodes.php' );
    require_once( GP_PLUGIN_DIR . '/core/gp-legacy.php' );
//});

add_action('init', 'gp_set_core_globals');
add_action('admin_init', 'gp_run_updates');
add_action('admin_menu', 'gp_set_admin_menu');

# We're using admin_init instead of admin_head because it doesn't handle enqueue_script/style() but...
add_action('admin_init', 'gp_plugin_scripts');
add_action('init', 'gp_site_scripts');
# ...but we use login_head here instead because it doesn't recognize enqueue_script/style(). Duh! There's also a wp_admin_css(). Ummm, why?!
add_action('login_head', 'gp_login_scripts');

add_action('wp_login', 'gp_session_onlogin');
add_action('wp_logout', 'gp_session_onlogout');
add_action('init', 'gp_session_handler');

remove_action('wp_head', 'wp_generator');

function default_login_redirect( $redirect, $request_redirect )
{
    if ( $request_redirect === '' )
        $redirect = home_url();
    return $redirect;
}
add_filter( 'login_redirect', 'default_login_redirect', 10, 2 );

/* RECORD DATE/TIME OF LAST TIME USER LOGGED IN */
function user_last_login($login) {
    global $user_ID;
    $user = get_userdatabylogin($login);
    update_usermeta($user->ID, 'last_login', $epochtime = strtotime('now'));
}
add_action('wp_login','user_last_login');

/* REDIRECT USER AFTER LOGIN/LOGOUT */
function redirect_login() {
    wp_redirect($_SERVER['HTTP_REFERER']);
}
#add_action('wp_login','redirect_login');

function redirect_logout() {
    wp_redirect($_SERVER['HTTP_REFERER']);
}
#add_action('wp_logout ','redirect_logout');

function gp_set_core_globals() {
    global $gp;
    global $current_user;

    $current_user = wp_get_current_user();
    
    $geo = new GeoSession();
    
    $gp->plugin->prefix = 'gp_theme';
    $gp->loggedin_user->id = $current_user->ID;
    $gp->states = $geo->getStates();
    $gp->uri->country = strtolower($gp->location['country_iso2']) . "/";
    $gp->campaignmonitor = array(
        1 => array(
            'api' => 'fd592f119aba9e1a50c9c7f09119e0ff',
            'lists' => array(
                'subscription-greenrazor' => array('api' => '6f745fb4dad5ab592b5bac0f23d9e826', 'profile_text' => 'Weekly "Green Razor" newsletter', 'register_add' => true, 'register_text' => 'Subscribe to our weekly newsletter the "Green Razor"?'),
                'subscription-promotional' => array('api' => 'ab2c53e2475810da981d03ed5986a262', 'profile_text' => '3rd party marketing and promotional emails', 'register_add' => true, 'register_text' => 'Support us by receiving occasional Promotional emails?')
            )
        ),
        2 => array(
            'api' => 'fd592f119aba9e1a50c9c7f09119e0ff',
            'lists' => array(
                'subscription-greenrazor' => array('api' => '96446bb7331d8f3a2857f6d29a71c21c', 'profile_text' => 'Weekly "Green Razor" newsletter', 'register_add' => true, 'register_text' => 'Subscribe to our weekly newsletter the "Green Razor"?'),
                'subscription-promotional' => array('api' => '5889bd046d839212942eae902bff3a9d', 'profile_text' => '3rd party marketing and promotional emails', 'register_add' => true, 'register_text' => 'Support us by receiving occasional Promotional emails?')
            )
        )
    );
}

function gp_run_updates() {
    if ( !current_user_can('administrator') )
        return false;

    if (get_option('GP_DB_VERSION') != GP_DB_VERSION) {
        gp_core_create_gp_tables();
        update_option('GP_DB_VERSION', GP_DB_VERSION);
    }

    if (get_option('GP_GEONAMES_VERSION') != GP_GEONAMES_VERSION) {
        gp_core_create_geonames_tables();
        gp_core_import_geonames_citiesdata();
        update_option('GP_GEONAMES_VERSION', GP_GEONAMES_VERSION);
    }

    if (get_option('GP_DEBIAN_ISOCODES_VERSION') != GP_DEBIAN_ISOCODES_VERSION) {
        gp_core_create_debian_isocodes_tables();
        gp_core_import_debian_isocodes_data();
        update_option('GP_DEBIAN_ISOCODES_VERSION', GP_DEBIAN_ISOCODES_VERSION);
    }
    
    if (get_option('GP_MAXMIND_VERSION') != GP_MAXMIND_VERSION) {
        gp_core_create_maxmind_tables();
        gp_core_import_maxmind_citiesdata();
        update_option('GP_MAXMIND_VERSION', GP_MAXMIND_VERSION);
    }
}

function gp_plugin_scripts() {

    if ( parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/profile.php" || parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH) == "/wp-admin/user-edit.php" ) {
        wp_register_script('gp_textareacounter', GP_PLUGIN_URL . '/js/textareacounter.js', false, false, true);
        wp_enqueue_script('gp_textareacounter');
    }

    wp_register_style('gp-admin', GP_PLUGIN_URL . '/css/gp-admin.css');
    wp_enqueue_style('gp-admin');
    
    wp_register_style('jquery-ui', GP_PLUGIN_URL . '/css/jquery-ui-1.8.9.custom.css');
    wp_enqueue_style('jquery-ui');
 
    wp_deregister_script('jquery-ui-datepicker');
    wp_register_script('jquery-ui-datepicker', GP_PLUGIN_URL . '/js/jquery.ui.datepicker.min.js', false, false, true);
    wp_enqueue_script('jquery-ui-datepicker');

}

function gp_login_scripts() {
    echo '<link rel="stylesheet" href="' . GP_PLUGIN_URL . '/css/gp-login.css" type="text/css" media="all" />';
}

function gp_site_scripts() {
    global $current_user, $wpdb;

    if(!is_admin()){
        wp_register_style('normalize', GP_PLUGIN_URL . '/css/normalize.min.css');
        wp_enqueue_style('normalize');

        wp_register_style('generic', get_bloginfo('template_url') . '/template/css/generic.css');
        wp_enqueue_style('generic');
        
        wp_register_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css');
        wp_enqueue_style('jquery-ui');
        
        wp_register_style('jquery-ui-custom-css', get_bloginfo('template_url') . '/template/custom-theme/jquery-ui-1.8.22.custom.css');
        wp_enqueue_style('jquery-ui-custom-css');
        
        wp_register_style('gp_web_fonts', get_bloginfo('template_url') . '/template/css/fontfaces.css');
        wp_enqueue_style( 'gp_web_fonts');

        wp_deregister_script('modernizr');
        wp_register_script('modernizr', GP_PLUGIN_URL . '/js/modernizr-2.6.1.min.js');
        wp_enqueue_script('modernizr');
        
        wp_deregister_script('jquery');
        wp_register_script('jquery', GP_PLUGIN_URL . '/js/jquery-1.8.0.min.js');
        wp_enqueue_script('jquery');
        
        wp_deregister_script('jquery-ui-widget');
        wp_register_script('jquery-ui-widget', GP_PLUGIN_URL . '/js/jquery.ui.widget.min.js');
        wp_enqueue_script('jquery-ui-widget');
        
        wp_deregister_script('jquery-ui-datepicker');
        wp_register_script('jquery-ui-datepicker', GP_PLUGIN_URL . '/js/jquery.ui.datepicker.min.js');
        wp_enqueue_script('jquery-ui-datepicker');
        
        wp_deregister_script('jquery-ui-dialog');
        wp_register_script('jquery-ui-dialog', GP_PLUGIN_URL . '/js/jquery.ui.dialog.min.js');
        wp_enqueue_script('jquery-ui-dialog');
        
        wp_deregister_script('jquery-ui-position');
        wp_register_script('jquery-ui-position', GP_PLUGIN_URL . '/js/jquery.ui.position.min.js');
        wp_enqueue_script('jquery-ui-position');

        wp_register_script('gp', GP_PLUGIN_URL . '/js/gp.js', false, false, true);
        wp_enqueue_script('gp');

        wp_register_script('hashchange', GP_PLUGIN_URL . '/js/jquery.ba-hashchange.min.js', false, false, true);
        wp_enqueue_script('hashchange');
        
        // Required for File Upload. Must be at Footer.
        wp_deregister_script('jquery-ui-core');
        wp_register_script('jquery-ui-core', GP_PLUGIN_URL . '/js/jquery.ui.core.min.js', false, false, true);
        wp_enqueue_script('jquery-ui-core');
        
        #if (basename(get_permalink()) == 'list-your-business-4') {
        wp_register_style('fileupload-ui', GP_PLUGIN_URL . '/css/jquery.fileupload-ui.css');
        wp_enqueue_style('fileupload-ui');
        
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
        #}
    }
}

add_action('wpmu_activate_user', 'gp_wpmu_activate_user', 10, 3);
function gp_wpmu_activate_user($user_id, $password, $meta) {
    global $current_site, $gp, $wpdb;

    $subscription_post = array();
    $cm_lists = $gp->campaignmonitor[$current_site->id]['lists'];
    if ( is_array( $cm_lists ) ) {
        foreach ( $cm_lists as $key => $value ) {
            if ( !empty( $meta[ $key ] ) ) {
                if ( !cm_subscribe( $key, $meta[ $key ], $user_id ) ) {
                    $meta[ $key ] = false;
                }
                $subscription_post = $subscription_post + array( $key => $meta[ $key ] );
            }
        }
        update_usermeta($user_id, $wpdb->prefix . 'subscription', $subscription_post);
    }

    if ( !empty( $meta[ 'subscribe-advertiser' ] ) ) {
        if ( $meta[ 'subscribe-advertiser' ] == true ) {
            update_usermeta($user_id, 'reg_advertiser', true );
        } else {
            update_usermeta($user_id, 'reg_advertiser', false );
        }
    }
}

?>