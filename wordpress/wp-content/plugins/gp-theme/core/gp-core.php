<?php 

$post_type_to_url_part = array("gp_news" => "news",
                               "gp_events" => "events",
                               "gp_advertorial" => "new-stuff",
                               "gp_competitions" => "competitions",
                               "gp_people" => "people",
                               "gp_ngocampaign" => "ngo-campaign");

function gp_core_create_tables() {
	global $wpdb, $gp;

	if ( !empty($wpdb->charset) ) {
		$charset_collate = " DEFAULT CHARACTER SET " . $wpdb->charset;
	}
		
	$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_drinquiries (
		ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		org_name VARCHAR(255) NOT NULL,  
		org_url VARCHAR(255) NOT NULL, 
		org_street VARCHAR(255) NOT NULL, 
		org_city VARCHAR(255) NOT NULL, 
		org_postcode VARCHAR(4) NOT NULL, 
		org_state VARCHAR(3) NOT NULL, 
		org_twitter VARCHAR(255) NOT NULL, 
		org_facebook VARCHAR(255) NOT NULL, 
		org_description LONGTEXT NOT NULL, 
		contact_firstname VARCHAR(255) NOT NULL, 
		contact_lastname VARCHAR(255) NOT NULL, 
		contact_email VARCHAR(255) NOT NULL, 
		contact_phone VARCHAR(255) NOT NULL, 
		org_storeurl VARCHAR(255) NOT NULL,
		dir_images VARCHAR(1000) NOT NULL,
		dsc_biodiversity LONGTEXT NOT NULL, 
		dsc_carbon LONGTEXT NOT NULL, 
		dsc_history LONGTEXT NOT NULL, 
		dsc_energy LONGTEXT NOT NULL,
		dsc_sustainability LONGTEXT NOT NULL, 
		dsc_envfacts LONGTEXT NOT NULL, 
		dsc_materials LONGTEXT NOT NULL,
		dsc_social LONGTEXT NOT NULL, 
		dsc_packaging LONGTEXT NOT NULL, 
		dsc_recyclability LONGTEXT NOT NULL,
		dsc_recycledcontent LONGTEXT NOT NULL, 
		dsc_water LONGTEXT NOT NULL,
		certs_list VARCHAR(255) NOT NULL,
		FORM_STATE VARCHAR(255) NOT NULL,
		UID BIGINT(20) NOT NULL,
		CREATED VARCHAR(255) NOT NULL,
		LAST_ACTIVITY VARCHAR(255) NOT NULL,
		SUBMITTED VARCHAR(255) NOT NULL,
		USER_AGENT VARCHAR(255) NOT NULL,
		REMOTE_ADDR VARCHAR(255) NOT NULL,
		HTTP_REFERER VARCHAR(255) NOT NULL,
		session_id VARCHAR(255) NOT NULL,
		SITE_ID BIGINT(20) NOT NULL
	)" . $charset_collate. ";";
	
	$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_adinquiries (
		ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		org_name VARCHAR(255) NOT NULL, 
		notes LONGTEXT NOT NULL, 
		contact_firstname VARCHAR(255) NOT NULL, 
		contact_lastname VARCHAR(255) NOT NULL, 
		contact_email VARCHAR(255) NOT NULL, 
		contact_phone VARCHAR(255) NOT NULL, 
		ad_opts VARCHAR(255) NOT NULL, 
		FORM_STATE VARCHAR(255) NOT NULL,
		UID BIGINT(20) NOT NULL,
		CREATED VARCHAR(255) NOT NULL,
		LAST_ACTIVITY VARCHAR(255) NOT NULL,
		SUBMITTED VARCHAR(255) NOT NULL,
		USER_AGENT VARCHAR(255) NOT NULL,
		REMOTE_ADDR VARCHAR(255) NOT NULL,
		HTTP_REFERER VARCHAR(255) NOT NULL,
		session_id VARCHAR(255) NOT NULL,
		SITE_ID BIGINT(20) NOT NULL
	)" . $charset_collate. ";";
	
	#$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_cpinquiries (ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, UID BIGINT(20) NOT NULL)" . $charset_collate. ";";
	
	$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_feedback (
		ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
		UID BIGINT(20) NOT NULL, 
		contact_email VARCHAR(255) NOT NULL, 
		feedback_content LONGTEXT NOT NULL, 
		FORM_STATE VARCHAR(255) NOT NULL,
		UID BIGINT(20) NOT NULL,
		CREATED VARCHAR(255) NOT NULL,
		LAST_ACTIVITY VARCHAR(255) NOT NULL,
		SUBMITTED VARCHAR(255) NOT NULL,
		USER_AGENT VARCHAR(255) NOT NULL,
		REMOTE_ADDR VARCHAR(255) NOT NULL,
		HTTP_REFERER VARCHAR(255) NOT NULL,
		session_id VARCHAR(255) NOT NULL,
		SITE_ID BIGINT(20) NOT NULL
	)" . $charset_collate. ";";

	$sql[] = "CREATE TABLE " . $wpdb->base_prefix . "gp_ecocerts (
		ID BIGINT(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  		org_name VARCHAR(255) NOT NULL,
  		org_abbr VARCHAR(255) NOT NULL,
  		org_logo_full VARCHAR(255) NOT NULL
  		org_logo_thumb VARCHAR(255) NOT NULL,
  		org_contact_email VARCHAR(255) NOT NULL,
  		org_siteurl VARCHAR(255) NOT NULL,
  		org_reach VARCHAR(255) NOT NULL,
		SITE_ID BIGINT(20) NOT NULL
	)" . $charset_collate . ";";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	
	add_option("GP_DB_VERSION", GP_DB_VERSION);
}

function gp_set_admin_menu() {
	#http://codex.wordpress.org/Administration_Menus
	add_menu_page( 'Theme Settings', 'Green Pages', 'manage_options', 'gp-settings', 'gp_load_settings', GP_PLUGIN_URL . '/gp-theme-icon.png', 100 );
  	add_submenu_page( 'gp-settings', 'Theme Settings', 'Theme Settings', 'manage_options', 'gp-settings', 'gp_load_settings' );
  	add_submenu_page( 'gp-settings', 'Post Settings', 'Post Settings', 'manage_options', 'gp-post', 'gp_load_post' );
  	add_submenu_page( 'gp-settings', 'Form Settings', 'Form Settings', 'manage_options', 'gp-forms', 'gp_load_forms' );
  	add_submenu_page( 'gp-settings', 'Sitemap Settings', 'Sitemap Settings', 'manage_options', 'gp-sitemaps', 'gp_load_sitemaps' );
  	add_submenu_page( 'gp-settings', 'Directory Inquiries', 'Directory Inquiries', 'manage_options', 'gp-directory-inquiries', 'gp_load_drinquiries' );
  	add_submenu_page( 'gp-settings', 'Advertiser Inquiries', 'Advertiser Inquiries', 'manage_options', 'gp-advertiser-inquiries', 'gp_load_adinquiries' );
  	add_submenu_page( 'gp-settings', 'Content Partner Inquiries', 'Content Partner Inquiries', 'manage_options', 'gp-content-partner-inquiries', 'gp_load_cpinquiries' );
  	add_submenu_page( 'gp-settings', 'Feedback', 'Feedback', 'manage_options', 'gp-feedback', 'gp_load_feedback' );
}  

function gp_load_settings() { 
    include( GP_PLUGIN_DIR . '/pages/gp-form-settings.php' ); 
} 

function gp_load_post() {  
    include( GP_PLUGIN_DIR . '/pages/gp-form-post.php' );  
}

function gp_load_forms() {  
    include( GP_PLUGIN_DIR . '/pages/gp-form-forms.php' );  
}

function gp_load_sitemaps() {  
    include( GP_PLUGIN_DIR . '/pages/gp-form-sitemaps.php' );  
}

function gp_load_drinquiries() {  
    include( GP_PLUGIN_DIR . '/pages/gp-list-drinquiries.php' );
}

function gp_load_adinquiries() {  
    include( GP_PLUGIN_DIR . '/pages/gp-list-adinquiries.php' );
}

function gp_load_cpinquiries() {  
    include( GP_PLUGIN_DIR . '/pages/gp-list-cpinquiries.php' );  
}

function gp_load_feedback() {  
    include( GP_PLUGIN_DIR . '/pages/gp-list-feedback.php' );  
}

function nl2br_limit($string, $num){
	$dirty = preg_replace('/\r/', '', $string);
	$clean = preg_replace('/\n{4,}/', str_repeat('<br />', $num), preg_replace('/\r/', '', $dirty));
	   
	return nl2br($clean);
}

function get_ID_by_slug($page_slug) {
    $page = get_page_by_path($page_slug);
    if ($page) {
        return $page->ID;
    } else {
        return false;
    }
}

function cleanstring($value) {
	$value = strip_tags($value);
	$value = trim($value);
	return $value;
}

function validate_empty($value) {
	if (empty($value) || $value == '') {
		return true;
	} else {
		return false;
	}
}

function validate_short($value, $length) {
	if (strlen($value) < $length) {
		return true;
	} else {
		return false;
	}
}

function validate_emailaddress($value) {
	if (is_email($value)) {
		return false;
	} else {
		return true;
	}
}

function validate_multivalue($value, $limit) {
	if (is_array($value) && count($value) > $limit) {
		return true;
	} else {
		return false;
	}
}

function check_slug_optionlist($option) {
	$array = explode(',', get_option($option));
	foreach ($array as $value) {
		if (get_ID_by_slug(trim($value))) { return true; }
	} 
	return false;
}

function array_insert($array,$pos,$val)
{
    $array2 = array_splice($array,$pos);
    $array[] = $val;
    $array = array_merge($array,$array2);

    return $array;
}
?>
