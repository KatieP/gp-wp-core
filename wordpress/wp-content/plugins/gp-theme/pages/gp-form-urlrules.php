<?php
global $wpdb;

$geo_rules = '
&lt;IfModule mod_rewrite.c&gt;
    RewriteEngine On
    RewriteBase /
';

$serviced_regions = Geo::getServicedRegions();
$current_location = Geo::getCurrentLocation();

#$current_states = Config::getStates();
#$post_types = Config::getPostTypes();


$query = "SELECT iso_alpha2 FROM " . $wpdb->base_prefix . "geonames_countryinfo;";
$countries = $wpdb->get_results( $query, ARRAY_A );

if ( $countries )  {
    foreach ( $countries as $country ) {
        if ( isset( $country['iso_alpha2'] ) ) {
            
            if ( in_array( $country['iso_alpha2'], $serviced_regions ) ) {
                require_once( GP_PLUGIN_DIR . '/configs/' . $country['iso_alpha2'] . '.php' );
                
                $className = $country['iso_alpha2'] . '\Config';
                $current_states = $className::getStates();
                $post_types = $className::getPostTypes();
                
                if ( isset( $post_types ) ) {
                    foreach ( $post_types as $value ) {
                        if ( isset( $value['id'] ) && isset( $value['args']['rewrite']['slug'] ) ) {
                            $posttype_id = $value['id'];
                            $posttype_slug = $value['args']['rewrite']['slug'];
                
                            if ( isset( $current_states ) ) {
                                foreach( $current_states as $state ) {
                                    if ( isset( $state['code'] ) && isset( $state['name'] ) ) {
                                        $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/' . sanitize_title($state['name']) . '/([a-z\-]+)/page/([0-9]{1,})/?$ index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&filterby_state=' . $state['code'] . '&filterby_city=$1&paged=$2 [L]';
                                        $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/' . sanitize_title($state['name']) . '/page/([0-9]{1,})/?$ index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&filterby_state=' . $state['code'] . '&paged=$1 [L]';
                                        $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/' . sanitize_title($state['name']) . '/([a-z\-]+)/?$ index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&filterby_state=' . $state['code'] . '&filterby_city=$1 [L]';
                                        $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/' . sanitize_title($state['name']) . '/?$ /index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&filterby_state=' . $state['code'] . ' [L]';
                                    }
                                }
                            }
                            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/page/([0-9]{1,})/?$ /index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&paged=$1 [L]';
                            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/?$  /index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . ' [L]';
                        }
                    }
                }
            } else {
                require_once( GP_PLUGIN_DIR . '/configs/_default.php' );
                
                $current_states = _default\Config::getStates();
                $post_types = _default\Config::getPostTypes();
                
                if ( isset( $post_types ) ) {
                    foreach ( $post_types as $value ) {
                        if ( isset( $value['id'] ) && isset( $value['args']['rewrite']['slug'] ) ) {
                            $posttype_id = $value['id'];
                            $posttype_slug = $value['args']['rewrite']['slug'];
                            
                            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/page/([0-9]{1,})/?$ /index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . '&paged=$1 [L]';
                            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/' . strtolower($country['iso_alpha2']) . '/?$  /index.php?post_type=' . $posttype_id . '&filterby_country=' . $country['iso_alpha2'] . ' [L]';
                        }
                    }
                }
            }
        }
    }
}

require_once( GP_PLUGIN_DIR . '/configs/_default.php' );

$current_states = _default\Config::getStates();
$post_types = _default\Config::getPostTypes();

if ( isset( $post_types ) ) {
    foreach ( $post_types as $value ) {
        if ( isset( $value['id'] ) && isset( $value['args']['rewrite']['slug'] ) ) {
            $posttype_id = $value['id'];
            $posttype_slug = $value['args']['rewrite']['slug'];

            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/page/([0-9]{1,})/?$ /index.php?post_type=' . $posttype_id . '&filterby_country=DEFAULT&paged=$1 [L]';
            $geo_rules .= '
    RewriteRule ^' . $posttype_slug . '/?$  /index.php?post_type=' . $posttype_id . '&filterby_country=DEFAULT [L]';
        }
    }
}

$geo_rules .= '
&lt;/IfModule&gt;';
?>

<div class="wrap">
    <div id="icon-options-general" class="icon32"><br /></div>  
    <h2>URL Rewrite Rules</h2>
    
	<table class="form-table">
		<tr>
		    <th>Geo Rules<br /><br /><span style="font-weight:bold;font-size:11px;">Add this to your <i>.htaccess</i> file <u>before</u> your Wordpress rules.</span></th>
		    <td><textarea class="large-text" style="height:400px;"><?php echo $geo_rules; ?></textarea></td>
		</tr>
	</table>
</div>