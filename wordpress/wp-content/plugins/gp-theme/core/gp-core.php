<?php 

function gp_core_create_gp_tables() {
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
        if (get_ID_by_slug(trim($value))) {
            return true;
        }
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

function time_ago( $tm, $rcs = 0 ) {
    $cur_tm = time(); $dif = $cur_tm-$tm;
    $pds = array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' );
    $lngh = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );
    for ( $v = sizeof( $lngh )-1; ( $v >= 0 ) && ( ( $no = $dif/$lngh[$v] ) <= 1 ); $v-- ); if ( $v < 0 ) $v = 0; $_tm = $cur_tm-( $dif%$lngh[$v] );

    $no = floor( $no ); if( $no <> 1 ) $pds[$v] .='s'; $x=sprintf( "%d %s ", $no,$pds[$v] );
    #if(($rcs == 1)&&($v >= 1)&&(($cur_tm-$_tm) > 0)) $x .= time_ago($_tm);
    if( ($rcs > 0 ) && ( $v >= 1 ) && ( ( $cur_tm-$_tm ) > 0 ) ) $x .= time_ago( $_tm, --$rcs );
    return $x;
}

function abbr_number( $val )
{
    $scale = array( ''=>1, 'K'=>1000,'M'=>1000000,'B'=>1000000000 );
    foreach ( $scale as $p => $div ) {
        $t = round( $val/$div ) . $p;
        if ( strlen( $t ) < ( 3+strlen( $p ) ) ) {
            break;
        }
    }
    return trim( $t );
}

function get_profile_author() {
    return (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
}

function getPluralName($newposttype) {
    if ($newposttype['plural'] == true) {
        return $newposttype['name'] . 's';
    } else {
        return $newposttype['name'];
    }
}

function getPostTypeSlug( $posttype ) {
    global $post;
    $posttypes = Config::getPostTypes();

    if ( !isset( $posttype ) ) {
        $posttype = $post->post_type;
    }
    
    foreach ( $posttypes as $value ) {
        if ( $value['enabled'] === true && $value['id'] == $posttype ) {
            return $value['args']['rewrite']['slug'];
        }
    }
    
    return "";
}

function getPostTypeID_by_Slug( $posttypeslug ) {
    $posttypes = Config::getPostTypes();
    
    foreach ( $posttypes as $value ) {
        if ( $value['enabled'] === true && $value['args']['rewrite']['slug'] == $posttypeslug ) {
            return $value['id'];
        }
    }

    return false;
}

function checkPostTypeSlug( $posttypeslug ) {
    $posttypes = Config::getPostTypes();
    
    foreach ( $posttypes as $value ) {
        if ( $value['enabled'] === true && $value['args']['rewrite']['slug'] == $posttypeslug ) {
            return true;
        }
    }

    return false;
}

function getRealIPAddress( $ip ) {
    if ( filter_var(trim($ip), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ) {
        return $ip;
    }

    if ( !empty($_SERVER['HTTP_CLIENT_IP']) ) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if ( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    if ( !empty($_SERVER['REMOTE_ADDR']) ) {
        return $_SERVER['REMOTE_ADDR'];
    }

    return false;
}

function checkURIFileName($filetocheck) {
    $basename = basename($_SERVER['REQUEST_URI']);
    $request_file =  substr($basename, 0, strpos($basename, '?'));
    if ( $request_file == $filetocheck ) {
        return true;
    }

    return false;
}

function isCurlInstalled() {
    return function_exists( 'curl_init' );
    #return in_array( 'curl', get_loaded_extensions() );
}

function remoteCurlInfo($remote_file) {
    if ( !isCurlInstalled() ) {
        return false;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_file);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FILETIME, true);
    $header = curl_exec($ch);

    if ($header === false) {
        #die(curl_error($ch));
        curl_close($ch);
        return false;
    }

    #$modified_timestamp = curl_getinfo($ch, CURLINFO_FILETIME);
    #$filesize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

    $curlinfo = curl_getinfo($ch);
    curl_close($ch);

    return $curlinfo;

    #if ($modified_timestamp != -1) {
    #return $modified_timestamp;
    #}

    return false;
}

function uploadRemoteFile($source = false, $destination = false, $allowed_filetypes = false, $overwrite = false, $overwrite_older = false, $unpack_in_place = false, $attempts = 1, $allowed_urlschemes = false) {
    $supported_filetypes = array( "ZIP", "GZ", "TXT", "CSV" ); // make sure these are all uppercase
    $supported_urlschemes = array( "HTTP" );

    if ( !$source ) return false;
    if ( !filter_var( $source, FILTER_VALIDATE_URL ) ) return false;

    if ( !$destination ) return false;
    if ( !file_exists( $destination ) ) return false;
    if ( !is_writeable( $destination ) ) return false; // directory owner should be www-data:www-data and perms 744

    if ( !$allowed_filetypes ) $allowed_filetypes = $supported_filetypes;
    if ( !is_array( $allowed_filetypes ) && !is_string( $allowed_filetypes ) ) return false;
    if ( is_string( $allowed_filetypes ) ) $allowed_filetypes = array( $allowed_filetypes );
    $allowed_filetypes = array_map( 'strtoupper', $allowed_filetypes );
    foreach ( $allowed_filetypes as $allowed_filetype ) {
        if ( !in_array( $allowed_filetype, $supported_filetypes ) ) return false;
    }

    $filetype = strtoupper( pathinfo( parse_url( $source, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
    if ( !in_array($filetype, $allowed_filetypes) ) return false;

    if ( !is_bool( $overwrite ) ) return false;
    if ( !is_bool( $overwrite_older ) ) return false;
    if ( !is_bool( $unpack_in_place ) ) return false;
    if ( !is_int( $attempts ) ) return false;

    if ( !$allowed_urlschemes ) $allowed_urlschemes = $supported_urlschemes;
    if ( !is_array( $allowed_urlschemes ) && !is_string( $allowed_urlschemes ) ) return false;
    if ( is_string( $allowed_urlschemes ) ) $allowed_urlschemes = array( $allowed_urlschemes );
    $allowed_urlschemes = array_map( 'strtoupper', $allowed_urlschemes );
    foreach ( $allowed_urlschemes as $allowed_urlscheme ) {
        if ( !in_array( $allowed_urlscheme, $supported_urlschemes ) ) return false;
    }

    if ( !isCurlInstalled() ) return false;

    echo remoteCurlInfo( $source ) . "<br />";

    switch ($filetype) {
        case "ZIP":
            break;
        case "GZ":
            break;
        default:
            // error
            break;
    }

    return false;
}
?>
