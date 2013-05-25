<?php
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
    global $post, $gp;
    
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();

    if ( !isset( $posttype ) ) {
        $posttype = $post->post_type;
    }
    
    foreach ( $edition_posttypes as $value ) {
        if ( $value['enabled'] === true && $value['id'] == $posttype ) {
            return $value['args']['rewrite']['slug'];
        }
    }
    
    return "";
}

function getPostTypeID_by_Slug( $posttypeslug ) {
    global $gp;
    
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
    foreach ( $edition_posttypes as $value ) {
        if ( $value['enabled'] === true && $value['args']['rewrite']['slug'] == $posttypeslug ) {
            return $value['id'];
        }
    }

    return false;
}

function checkPostTypeSlug( $posttypeslug ) {
    global $gp;
    
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
    foreach ( $edition_posttypes as $value ) {
        if ( $value['enabled'] === true && $value['args']['rewrite']['slug'] == $posttypeslug ) {
            return true;
        }
    }

    return false;
}

function getRealIPAddress( $ip=false ) {
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

function getLocationByIP($clientip = false) {
    global $wpdb;
    
    $clientip = sprintf( "%u", ip2long( getRealIPAddress( $clientip ) ) );

    $query = $wpdb->prepare(
            "SELECT
            " . $wpdb->prefix . "maxmind_geolitecitylocation.city,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.regionName as region,
            " . $wpdb->prefix . "geonames_countryinfo.name AS country,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.postalCode as postcode,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.latitude,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.longitude,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.country as country_iso2,
            " . $wpdb->prefix . "maxmind_geolitecitylocation.region as region_iso2
            FROM " . $wpdb->prefix . "maxmind_geolitecityblocks_ipv4
            INNER JOIN " . $wpdb->prefix . "maxmind_geolitecitylocation
            ON " . $wpdb->prefix . "maxmind_geolitecityblocks_ipv4.locId = " . $wpdb->prefix . "maxmind_geolitecitylocation.locId
            LEFT OUTER JOIN " . $wpdb->prefix . "geonames_countryinfo
            ON " . $wpdb->prefix . "maxmind_geolitecitylocation.country = " . $wpdb->prefix . "geonames_countryinfo.iso_alpha2
            WHERE
            " . $wpdb->prefix . "maxmind_geolitecityblocks_ipv4.startIpNum <= %s
            AND " . $wpdb->prefix . "maxmind_geolitecityblocks_ipv4.endIpNum >= %s
            LIMIT 1;",
            $clientip,
            $clientip
    );
     
    return $wpdb->get_row($query, ARRAY_A);
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

/* GET ABSOLUTE DATE */
function get_absolutedate( $start, $end, $dateformat = 'jS F Y', $timeformat = 'g:i a', $abreviate = true, $dropyear = true, $join = array(' to ', ' at ', ' - ') ) {
    # A completely incomplete function

    $datetime_format = array(
            'year' => array('LoYy'),
            'month' => array('FmMnt'),
            'week' => array('W'),
            'day' => array('dDjlNSwz'),
            'time' => array('aAbgGhHisu'),
            'timezone' => array('eIOPTZ'),
            'full' => array('crU')
    );

    if ( !is_numeric($start) && !is_numeric($end) ) {
        return false;
    }

    if ( !is_string($dateformat) ) {
        $dateformat = 'jS F Y g:i a';
    }

    if ( !is_string($timeformat) ) {
        $timeformat = 'g:i a';
    }

    if ( empty($timeformat) ) {
        $showtime = false;
    } else {
        $showtime = true;
    }

    if ( !is_bool($abreviate) ) {
        $abreviate = true;
    }

    if ( !is_bool($dropyear) ) {
        $dropyear = true;
    }

    $yearstart = date('Y', $start);
    $yearend = date('Y', $end);
    $yearnow = date('Y');

    $monthstart = date('n', $start);
    $monthend = date('n', $end);
    $monthnow = date('n');

    $daystart = date('j', $start);
    $dayend = date('j', $end);
    $daynow = date('j');

    $hourstart = (int)date('H', $start);
    $hourend = (int)date('H', $end);
    $hournow = (int)date('H');

    $minutestart = (int)date('i', $start);
    $minuteend = (int)date('i', $end);
    $minutenow = (int)date('i');

    if ($yearstart == $yearnow && $yearend == $yearnow) {
        if ($daystart != $dayend && $monthstart != $monthend) {
            $displaydate = date('jS F', $start) . $join[0];
        }
        if ($daystart != $dayend && $monthstart == $monthend) {
            $displaydate = date('jS', $start) . $join[0];
        }
        $displaydate = $displaydate . date('jS F', $end);
    } else {
        if ($daystart != $dayend && $monthstart != $monthend && $yearstart != $yearend) {
            $displaydate = date('jS F Y', $start) . $join[0];
        }
        if ($daystart != $dayend && $monthstart != $monthend && $yearstart == $yearend) {
            $displaydate = date('jS F', $start) . $join[0];
        }
        if ($daystart != $dayend && $monthstart == $monthend && $yearstart == $yearend) {
            $displaydate = date('jS', $start) . $join[0];
        }
        $displaydate = $displaydate . date('jS F Y', $end);
    }

    if ( ( ( ( $hourstart * 60 ) + $minutestart ) < ( ( $hourend * 60 ) + $minuteend ) ) && $showtime == true) {
        $displaydate = $displaydate . $join[1] . date($timeformat, $start) . $join[2] . date($timeformat, $end);
    }

    return $displaydate;

}

function get_country_map() {

    $countries_map = array( 'AF' => 'Afghanistan', 'AL' => 'Albania', 'DZ' => 'Algeria', 'AD' => 'Andorra', 'AO' => 'Angola', 
    						'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 'AU' => 'Australia', 
    						'AT' => 'Austria', 'AZ' => 'Azerbaijan', 'BS' => 'The Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 
    						'BB' => 'Barbados', 'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 
    						'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BR' => 'Brazil', 
    						'BN' => 'Brunei', 'BG' => 'Bulgaria', 'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'KH' => 'Cambodia', 
    						'CM' => 'Cameroon', 'CA' => 'Canada', 'CV' => 'Cape Verde', 'CF' => 'Central African Republic', 'TD' => 'Chad', 
    						'CL' => 'Chile', 'CN' => 'China', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo, Republic of the', 
    						'CD' => 'Congo, Democratic Republic of the', 'CR' => 'Costa Rica', 'CI' => 'Cote d\'Ivoire', 'HR' => 'Croatia', 
    						'CU' => 'Cuba', 'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 
    						'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'TL' => 'Timor-Leste', 'EC' => 'Ecuador', 'EG' => 'Egypt', 
    						'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 'ET' => 'Ethiopia', 
    						'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GA' => 'Gabon', 'GM' => 'Gambia', 'GE' => 'Georgia', 
    						'DE' => 'Germany', 'GH' => 'Ghana', 'GR' => 'Greece', 'GD' => 'Grenada', 'GT' => 'Guatemala', 'GN' => 'Guinea', 
    						'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HN' => 'Honduras', 'HU' => 'Hungary', 'IS' => 'Iceland', 
    						'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 'IQ' => 'Iraq', 'IE' => 'Ireland', 'IL' => 'Israel', 
    						'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 
    						'KI' => 'Kiribati', 'KP' => 'Korea, North', 'KR' => 'Korea, South', 'ZZ' => 'Kosovo', 'KW' => 'Kuwait', 
    						'KG' => 'Kyrgyzstan', 'LA' => 'Laos', 'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 
    						'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 'MK' => 'Macedonia', 
    						'MG' => 'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 
    						'MH' => 'Marshall Islands', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'MX' => 'Mexico', 
    						'FM' => 'Micronesia, Federated States of', 'MD' => 'Moldova', 'MC' => 'Monaco', 'MN' => 'Mongolia', 
    						'ME' => 'Montenegro', 'MA' => 'Morocco', 'MZ' => 'Mozambique', 'MM' => 'Myanmar (Burma)', 'NA' => 'Namibia', 
    						'NR' => 'Nauru', 'NP' => 'Nepal', 'NL' => 'Netherlands', 'NZ' => 'New Zealand', 'NI' => 'Nicaragua', 
    						'NE' => 'Niger', 'NG' => 'Nigeria', 'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 
    						'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 'PE' => 'Peru', 'PH' => 'Philippines', 
    						'PL' => 'Poland', 'PT' => 'Portugal', 'QA' => 'Qatar', 'RO' => 'Romania', 'RU' => 'Russia', 'RW' => 'Rwanda', 
    						'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 'VC' => 'Saint Vincent and the Grenadines', 'WS' => 'Samoa', 
    						'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 'SN' => 'Senegal', 'RS' => 'Serbia', 
    						'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 'SG' => 'Singapore', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 
    						'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 'SS' => 'South Sudan', 'ES' => 'Spain', 
    						'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 'SZ' => 'Swaziland', 'SE' => 'Sweden', 'CH' => 'Switzerland', 
    						'SY' => 'Syria', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TG' => 'Togo', 
    						'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 'TR' => 'Turkey', 'TM' => 'Turkmenistan', 
    						'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 'GB' => 'United Kingdom', 
    						'US' => 'United States of America', 'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VA' => 'Vatican City', 
    						'VE' => 'Venezuela', 'VN' => 'Vietnam', 'YE' => 'Yemen', 'ZM' => 'Zambia', 'ZW' => 'Zimbabwe');

	return $countries_map;

}

?>