<?php
add_action( 'add_meta_boxes', 'create_GPMeta' );
add_action( 'save_post', 'save_GPMeta' );
add_action( 'admin_head', 'js_GPMeta' );

/**
 * Builds inline Javascript or links to Javascript libraries for individual Meta Boxes inside the <head> tag.
 *
 */
function js_GPMeta () {
    global $gp;

    $thisposttype = get_post_type();
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();

    for($index = 0; $index < count($edition_posttypes); $index++) {
        if ( $edition_posttypes[$index]['enabled'] == true && $edition_posttypes[$index]['id'] == $thisposttype && is_array( $edition_posttypes[$index]['GPmeta'] ) ) {
            foreach ( $edition_posttypes[$index]['GPmeta'] as $metabox ) {
                if ( is_array( $metabox ) ) {
                    if ( isset( $metabox['id'] ) && isset( $metabox['title'] ) && function_exists( 'gp_js_' . $metabox['id'] . '_meta' ) ) {
                        call_user_func('gp_js_' . $metabox['id'] . '_meta');
                    }
                }
            }
        }
    }
}

function create_GPMeta () {
    // Create Meta Boxes Router
    global $gp;
    
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
    for($index = 0; $index < count($edition_posttypes); $index++) {
        if ( $edition_posttypes[$index]['enabled'] == true && is_array( $edition_posttypes[$index]['GPmeta'] ) ) {
            foreach ( $edition_posttypes[$index]['GPmeta'] as $metabox ) {
                if ( is_array( $metabox ) ) {
                    if ( isset( $metabox['id'] ) && isset( $metabox['title'] ) && function_exists( 'gp_create_' . $metabox['id'] . '_meta' ) ) {
                        add_meta_box( 'gp_create_' . $metabox['id'] . '_meta', $metabox['title'] , 'gp_create_' . $metabox['id'] . '_meta', $edition_posttypes[$index]['id'], 'normal', 'default', $metabox );
                    }
                }
            }
        }
    }
}

function save_GPMeta () {
    // Save Meta Boxes Router
    global $post, $gp;
    
    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();
    
    if ( isset( $post ) && !current_user_can( 'edit_post', $post->ID ) ) {
        return $post->ID;
    }

    for($index = 0; $index < count($edition_posttypes); $index++) {
        if ( $edition_posttypes[$index]['enabled'] == true && is_array( $edition_posttypes[$index]['GPmeta'] ) ) {
            foreach ( $edition_posttypes[$index]['GPmeta'] as $metabox ) {
                if ( is_array( $metabox ) ) {
                    if ( isset( $metabox['id'] ) && isset( $metabox['title'] ) && function_exists( 'gp_save_' . $metabox['id'] . '_meta' ) ) {
                        if ( isset( $_POST['gp_' . $metabox['id'] . '-nonce'] ) && wp_verify_nonce( $_POST['gp_' . $metabox['id'] . '-nonce'], 'gp_' . $metabox['id'] . '-nonce' ) ) {
                            call_user_func('gp_save_' . $metabox['id'] . '_meta');
                        }
                    }
                }
            }
        }
    }
}

function gp_create_postGeoLoc_meta($post, $metabox) {
    global $gp;
    
    $custom = get_post_custom($post->ID);

    $meta_geo_location = ( !isset($custom["gp_google_geo_location"][0]) || empty($custom["gp_google_geo_location"][0]) ) ? false : $custom["gp_google_geo_location"][0];
    $meta_geo_latitude = ( !isset($custom["gp_google_geo_latitude"][0]) || empty($custom["gp_google_geo_latitude"][0]) ) ? false : $custom["gp_google_geo_latitude"][0];
    $meta_geo_longitude = ( !isset($custom["gp_google_geo_longitude"][0]) || empty($custom["gp_google_geo_longitude"][0]) ) ? false : $custom["gp_google_geo_longitude"][0];
    $meta_geo_country = ( !isset($custom["gp_google_geo_country"][0]) || empty($custom["gp_google_geo_country"][0]) ) ? false : $custom["gp_google_geo_country"][0];
    $meta_geo_administrative_area_level_1 = ( !isset($custom["gp_google_geo_administrative_area_level_1"][0]) || empty($custom["gp_google_geo_administrative_area_level_1"][0]) ) ? false : $custom["gp_google_geo_administrative_area_level_1"][0];
    $meta_geo_administrative_area_level_2 = ( !isset($custom["gp_google_geo_administrative_area_level_2"][0]) || empty($custom["gp_google_geo_administrative_area_level_2"][0]) ) ? false : $custom["gp_google_geo_administrative_area_level_2"][0];
    $meta_geo_administrative_area_level_3 = ( !isset($custom["gp_google_geo_administrative_area_level_3"][0]) || empty($custom["gp_google_geo_administrative_area_level_3"][0]) ) ? false : $custom["gp_google_geo_administrative_area_level_3"][0];
    $meta_geo_locality = ( !isset($custom["gp_google_geo_locality"][0]) || empty($custom["gp_google_geo_locality"][0]) ) ? false : $custom["gp_google_geo_locality"][0];
    $meta_geo_locality_slug = ( !isset($custom["gp_google_geo_locality"][0]) || empty($custom["gp_google_geo_locality"][0]) ) ? false : sanitize_title($custom["gp_google_geo_locality"][0]);
    
    $geo_currentlocation = $gp->location;

    $meta_maxmind_geo_latitude = ( isset($geo_currentlocation['latitude']) ) ? $geo_currentlocation['latitude'] : false ;
    $meta_maxmind_geo_longitude = ( isset($geo_currentlocation['longitude']) ) ? $geo_currentlocation['longitude'] : false ;
    $meta_maxmind_geo_country = ( isset($geo_currentlocation['country']) ) ? $geo_currentlocation['country_iso2'] : false ;
    $meta_maxmind_geo_region = ( isset($geo_currentlocation['region']) ) ? $geo_currentlocation['region_iso2'] : false ;
    $meta_maxmind_geo_city = ( isset($geo_currentlocation['city']) ) ? $geo_currentlocation['city'] : false ;
    $meta_maxmind_geo_city_slug = ( isset($geo_currentlocation['city']) ) ? sanitize_title($geo_currentlocation['city']) : false ;
    
    $meta_maxmind_geo_latitude = ( !isset($custom["gp_maxmind_geo_latitude"][0]) || empty($custom["gp_maxmind_geo_latitude"][0]) ) ? $meta_maxmind_geo_latitude : $custom["gp_maxmind_geo_latitude"][0];
    $meta_maxmind_geo_longitude = ( !isset($custom["gp_maxmind_geo_longitude"][0]) || empty($custom["gp_maxmind_geo_longitude"][0]) ) ? $meta_maxmind_geo_longitude : $custom["gp_maxmind_geo_longitude"][0];
    $meta_maxmind_geo_country = ( !isset($custom["gp_maxmind_geo_country"][0]) || empty($custom["gp_maxmind_geo_country"][0]) ) ? $meta_maxmind_geo_country : $custom["gp_maxmind_geo_country"][0];
    $meta_maxmind_geo_region = ( !isset($custom["gp_maxmind_geo_region"][0]) || empty($custom["gp_maxmind_geo_region"][0]) ) ? $meta_maxmind_geo_region : $custom["gp_maxmind_geo_region"][0];
    $meta_maxmind_geo_city = ( !isset($custom["gp_maxmind_geo_city"][0]) || empty($custom["gp_maxmind_geo_city"][0]) ) ? $meta_maxmind_geo_city : $custom["gp_maxmind_geo_city"][0];
    $meta_maxmind_geo_city_slug = ( !isset($custom["gp_maxmind_geo_city"][0]) || empty($custom["gp_maxmind_geo_city"][0]) ) ? $meta_maxmind_geo_city_slug : sanitize_title($custom["gp_maxmind_geo_city"][0]);
    
    $meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
    $meta_nonce_value = wp_create_nonce( $meta_nonce_name );

    $inputhidden = ( get_user_role( array('administrator'), 0 ) ) ? 'type="text"' : 'type="hidden"';

    echo '
    <input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />

    <div class="gp-meta">
    <input name="gp_google_geo_location" id="gp_google_geo_location" type="text" style="width:99%;" value="' . $meta_geo_location . '" />
    <input name="gp_google_geo_latitude" id="gp_google_geo_latitude" ' . $inputhidden . ' value="' . $meta_geo_latitude . '" readonly="readonly" />
    <input name="gp_google_geo_longitude" id="gp_google_geo_longitude" ' . $inputhidden . ' value="' . $meta_geo_longitude . '" readonly="readonly" />
    <input name="gp_google_geo_country" id="gp_google_geo_country" ' . $inputhidden . ' value="' . $meta_geo_country . '" readonly="readonly" />
    <input name="gp_google_geo_administrative_area_level_1" id="gp_google_geo_administrative_area_level_1" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_1 . '" readonly="readonly" />
    <input name="gp_google_geo_administrative_area_level_2" id="gp_google_geo_administrative_area_level_2" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_2 . '" readonly="readonly" />
    <input name="gp_google_geo_administrative_area_level_3" id="gp_google_geo_administrative_area_level_3" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_3 . '" readonly="readonly" />
    <input name="gp_google_geo_locality" id="gp_google_geo_locality" ' . $inputhidden . ' value="' . $meta_geo_locality . '" readonly="readonly" />
    <input name="gp_google_geo_locality_slug" id="gp_google_geo_locality_slug" ' . $inputhidden . ' value="' . $meta_geo_locality_slug . '" readonly="readonly" />
    <div class="clear"></div>
    <input name="gp_maxmind_geo_latitude" id="gp_maxmind_geo_latitude" ' . $inputhidden . ' value="' . $meta_maxmind_geo_latitude . '" readonly="readonly" />
    <input name="gp_maxmind_geo_longitude" id="gp_maxmind_geo_longitude" ' . $inputhidden . ' value="' . $meta_maxmind_geo_longitude . '" readonly="readonly" />
    <input name="gp_maxmind_geo_country" id="gp_maxmind_geo_country" ' . $inputhidden . ' value="' . $meta_maxmind_geo_country . '" readonly="readonly" />
    <input name="gp_maxmind_geo_region" id="gp_maxmind_geo_region" ' . $inputhidden . ' value="' . $meta_maxmind_geo_region . '" readonly="readonly" />
    <input name="gp_maxmind_geo_city" id="gp_maxmind_geo_city" ' . $inputhidden . ' value="' . $meta_maxmind_geo_city . '" readonly="readonly" />
    <input name="gp_maxmind_geo_city_slug" id="gp_maxmind_geo_city_slug" ' . $inputhidden . ' value="' . $meta_maxmind_geo_city_slug . '" readonly="readonly" />
    <div id="map_canvas"></div>
    </div>
    ';
}

function gp_save_postGeoLoc_meta() {
    global $post;

    if(isset($_POST['gp_google_geo_location'])) {
        update_post_meta($post->ID, 'gp_google_geo_location', $_POST['gp_google_geo_location'] );
    }

    if(isset($_POST['gp_google_geo_latitude'])) {
        update_post_meta($post->ID, 'gp_google_geo_latitude', $_POST['gp_google_geo_latitude'] );
    }

    if(isset($_POST['gp_google_geo_longitude'])) {
        update_post_meta($post->ID, 'gp_google_geo_longitude', $_POST['gp_google_geo_longitude'] );
    }

    if(isset($_POST['gp_google_geo_country'])) {
        update_post_meta($post->ID, 'gp_google_geo_country', $_POST['gp_google_geo_country'] );
    }

    if(isset($_POST['gp_google_geo_administrative_area_level_1'])) {
        update_post_meta($post->ID, 'gp_google_geo_administrative_area_level_1', $_POST['gp_google_geo_administrative_area_level_1'] );
    }

    if(isset($_POST['gp_google_geo_administrative_area_level_2'])) {
        update_post_meta($post->ID, 'gp_google_geo_administrative_area_level_2', $_POST['gp_google_geo_administrative_area_level_2'] );
    }

    if(isset($_POST['gp_google_geo_administrative_area_level_3'])) {
        update_post_meta($post->ID, 'gp_google_geo_administrative_area_level_3', $_POST['gp_google_geo_administrative_area_level_3'] );
    }

    if(isset($_POST['gp_google_geo_locality'])) {
        update_post_meta($post->ID, 'gp_google_geo_locality', $_POST['gp_google_geo_locality'] );
    }
    
    if(isset($_POST['gp_google_geo_locality_slug'])) {
        update_post_meta($post->ID, 'gp_google_geo_locality_slug', sanitize_title($_POST['gp_google_geo_locality']) );
    }
    
    if(isset($_POST['gp_maxmin_geo_latitude'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_latitude', $_POST['gp_maxmind_geo_latitude'] );
    }

    if(isset($_POST['gp_maxmin_geo_longitude'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_longitude', $_POST['gp_maxmind_geo_longitude'] );
    }
    
    if(isset($_POST['gp_maxmin_geo_country'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_country', $_POST['gp_maxmind_geo_country'] );
    }
    
    if(isset($_POST['gp_maxmin_geo_region'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_region', $_POST['gp_maxmind_geo_region'] );
    }
    
    if(isset($_POST['gp_maxmin_geo_city'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_city', $_POST['gp_maxmind_geo_city'] );
    }
    
    if(isset($_POST['gp_maxmin_geo_city_slug'])) {
        update_post_meta($post->ID, 'gp_maxmind_geo_city_slug', $_POST['gp_maxmind_geo_city_slug'] );
    }
    
    return $post;
}

function gp_js_postGeoLoc_meta() {
    global $post, $wpdb, $gp;

    $custom = get_post_custom($post->ID);

    #echo '122.155.36.202' . "<br/>";
    #echo sprintf( "%u", ip2long( '122.155.36.202' ) );
    #echo ip2long( '122.155.36.202' );
    #echo "<br/>" . inet_ntop( '122.155.36.202' ) . "<br/>";
    #echo '2001:0db8:85a3:0000:0000:8a2e:0370:7334' . "<br/>";
    #echo inet_ntop( '2001:0db8:85a3:0000:0000:8a2e:0370:7334' ) . "<br/>";

    $meta_source = false;
    $meta_initzoom = 9;

    $meta_postlat = ( !isset($custom["gp_google_geo_latitude"][0]) || empty($custom["gp_google_geo_latitude"][0]) ) ? false : $custom["gp_google_geo_latitude"][0];
    $meta_postlng = ( !isset($custom["gp_google_geo_longitude"][0]) || empty($custom["gp_google_geo_longitude"][0]) ) ? false : $custom["gp_google_geo_longitude"][0];
    $meta_source = ( $meta_postlat && $meta_postlng ) ? 'db' : false;

    if ( !$meta_source ) {
        $geo_currentlocation = $gp->location;

        if ( $location ) {
            $meta_postlat = ( !empty($geo_currentlocation['latitude']) ) ? $geo_currentlocation['latitude'] : false;
            $meta_postlng = ( !empty($geo_currentlocation['longitude']) ) ? $geo_currentlocation['longitude'] : false;
            $meta_source = ( $meta_postlat && $meta_postlng ) ? 'maxmind' : false;
        }
    }

    if ( !$meta_source ) {
        $meta_source = 'default';
        $meta_postlat = "-33.8688";
        $meta_postlng = "151.2195";
        $meta_address = "";
    }
    
    echo '
    <script src="https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places" type="text/javascript"></script>
    <script type="text/javascript">
    function doMap(geoLat, geoLng, source) {
        var initZoom = ' . $meta_initzoom . ';
    ';

    if ( $meta_source == 'maxmind' ) {
        echo '
        if ( !jQuery(\'.maxmind_acknowledgment\').length ) {
            jQuery(\'#footer\').append(\'<div class="maxmind_acknowledgment">This product includes GeoLite data created by MaxMind, available from <a href="http://maxmind.com/" target="_blank">http://maxmind.com/</a></div>\');
        }
        ';
    }

        echo '
        var mapOptions = {
            center: new google.maps.LatLng(geoLat, geoLng),
            zoom: initZoom,
            disableDefaultUI: true,
            disableDoubleClickZoom: true,
            draggable: false,
            keyboardShortcuts: false,
            scrollwheel: false,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        
        var map = new google.maps.Map(document.getElementById(\'map_canvas\'), mapOptions);
        ';
    
    if ( $meta_source == 'db' || $meta_source == 'maxmind' ) {
        echo '
        var marker = new google.maps.Marker({
            position: map.getCenter(),
            map: map
        });
        ';
    }

        echo '
        var options = {
            types: [\'(cities)\']
        };
        
        var input = document.getElementById(\'gp_google_geo_location\');
        var autocomplete = new google.maps.places.Autocomplete(input, options);
        
        autocomplete.bindTo(\'bounds\', map);
        
        var marker = new google.maps.Marker({map: map});
        
        google.maps.event.addListener(autocomplete, \'place_changed\', function() {
            var place = autocomplete.getPlace();
        	
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
            }
        	
            map.setZoom(initZoom);
        
            var image = new google.maps.MarkerImage(
                place.icon,
                new google.maps.Size(71, 71),
                new google.maps.Point(0, 0),
                new google.maps.Point(17, 34),
                new google.maps.Size(35, 35)
            );
        	
            marker.setIcon(image);
            marker.setPosition(place.geometry.location);
            
            var country = \'\';
            var administrative_area_level_1 = \'\';
            var administrative_area_level_2 = \'\';
            var administrative_area_level_3 = \'\';
            var locality = \'\';
            	
            for (var i = 0; i < place.address_components.length; i++) {
                var addr = place.address_components[i];
                if (addr.types[0] == \'country\') {country = addr.short_name;}
                if (addr.types[0] == \'administrative_area_level_1\') {administrative_area_level_1 = addr.short_name;}
                if (addr.types[0] == \'administrative_area_level_2\') {administrative_area_level_2 = addr.short_name;}
                if (addr.types[0] == \'administrative_area_level_3\') {administrative_area_level_3 = addr.short_name;}
                if (addr.types[0] == \'locality\') {locality = addr.short_name;}
            }
        
            var location = map.getCenter();
            document.getElementById("gp_google_geo_latitude").value = place.geometry.location.lat();
            document.getElementById("gp_google_geo_longitude").value = place.geometry.location.lng();
            document.getElementById("gp_google_geo_country").value = country;
            document.getElementById("gp_google_geo_administrative_area_level_1").value = administrative_area_level_1;
            document.getElementById("gp_google_geo_administrative_area_level_2").value = administrative_area_level_2;
            document.getElementById("gp_google_geo_administrative_area_level_3").value = administrative_area_level_3;
            document.getElementById("gp_google_geo_locality").value = locality;
        });
    
        google.maps.event.addDomListener(input, \'keydown\', function(e) {
            if (e.keyCode == 13) {
                if (e.preventDefault) {
                    e.preventDefault();
                } else {
                    // Since the google event handler framework does not handle early IE versions, we have to do it by our self. :-(
                    e.cancelBubble = true;
                    e.returnValue = false;
                }
            }
        });
    }

    function initialize() {
        var geoLat = ' . $meta_postlat . ';
        var geoLng = ' . $meta_postlng . ';
        var source = \'' . $meta_source . '\'

        if ( source == \'db\' || source == \'maxmind\' ) {
            doMap( geoLat, geoLng, source );
            return;
        }

        if ( navigator && navigator.geolocation ) {
            var location_timeout = setTimeout( function () { doMap( geoLat, geoLng, source ) }, 10000 );
            navigator.geolocation.getCurrentPosition(
                function geo_success( position ) {
                    clearTimeout( location_timeout );
                    doMap( position.coords.latitude, position.coords.longitude, \'html5\' );
                },
                function geo_error( error ) {
                    clearTimeout( location_timeout );
                    doMap( geoLat, geoLng, source );
                },
                {maximumAge:0,timeout:10000,enableHighAccuracy:true}
            );
        } else {
            doMap( geoLat, geoLng, source );
        }
    }
    
    google.maps.event.addDomListener(window, \'load\', initialize);
    </script>
    ';
}

function gp_create_postEventDate_meta ($post, $metabox) {
    $custom = get_post_custom($post->ID);

    $meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
    $meta_nonce_value = wp_create_nonce( $meta_nonce_name );

    $meta_sd = isset($custom["gp_events_startdate"][0]) ? $custom["gp_events_startdate"][0] : "";
    $meta_ed = isset($custom["gp_events_enddate"][0]) ? $custom["gp_events_enddate"][0] : "";
    
    $meta_sd = (!is_numeric($meta_sd)) ? strtotime($meta_sd) : $meta_sd;
    $meta_ed = (!is_numeric($meta_ed)) ? strtotime($meta_ed) : $meta_ed;  
    
    $meta_st = isset($custom["gp_events_starttime"][0]) ? $custom["gp_events_starttime"][0] : "";
    $meta_et = isset($custom["gp_events_endtime"][0]) ? $custom["gp_events_endtime"][0] : "";

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    if ($meta_sd == null) {
        $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;
    }

    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);

    echo '<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />';
    ?>
    <div class="tf-meta">
    	<ul>
    		<li><label>Start Date</label><input name="gp_events_startdate"
    			type="text" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
    		<li><label>Start Time</label><input name="gp_events_starttime"
    			type="text" value="<?php echo $meta_st; ?>" /></li>
    		<li><label>End Date</label><input name="gp_events_enddate" type="text"
    			class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
    		<li><label>End Time</label><input name="gp_events_endtime" type="text"
    			value="<?php echo $meta_et; ?>" /></li>
    	</ul>
    </div>
<?php
}

function gp_save_postEventDate_meta() {
    global $post;
    $thisposttype = get_post_type();

    if(isset($_POST[$thisposttype . '_startdate'])) {
        $updatestartd = strtotime ( $_POST[$thisposttype . '_startdate'] );
        update_post_meta($post->ID, $thisposttype . '_startdate', $updatestartd );
    }

    if(isset($_POST[$thisposttype . '_enddate'])) {
        $updateendd = strtotime ( $_POST[$thisposttype . '_enddate'] );
        update_post_meta($post->ID, $thisposttype . '_enddate', $updateendd );
    }
    
    if(isset($_POST[$thisposttype . '_starttime'])) {
        $updatestartt = $_POST[$thisposttype . '_starttime'];
        update_post_meta($post->ID, $thisposttype . '_starttime', $updatestartt );
    }

    if(isset($_POST[$thisposttype . '_endtime'])) {
        $updateendt = $_POST[$thisposttype . '_endtime'];
        update_post_meta($post->ID, $thisposttype . '_endtime', $updateendt );
    }    

    return $post;
}

function gp_create_postCompetitionDate_meta ($post, $metabox) {
    $custom = get_post_custom($post->ID);

    $meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
    $meta_nonce_value = wp_create_nonce( $meta_nonce_name );

    $meta_sd = isset($custom["gp_competitions_startdate"][0]) ? $custom["gp_competitions_startdate"][0] : "";
    $meta_ed = isset($custom["gp_competitions_enddate"][0]) ? $custom["gp_competitions_enddate"][0] : "";
    $meta_dd = isset($custom["gp_competitions_drawdate"][0]) ? $custom["gp_competitions_drawdate"][0] : "";
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;
    $meta_dt = $meta_dd;

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    if ($meta_sd == null) {
        $meta_sd = time(); $meta_ed = $meta_sd; $meta_dd = $meta_sd; $meta_st = 0; $meta_et = 0; $meta_dt = 0;
    }

    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_dd = date("D, M d, Y", $meta_dd);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);
    $clean_dt = date($time_format, $meta_dt);

    echo '<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />';
    ?>
    <div class="tf-meta">
    	<ul>
    		<li><label>Start Date</label><input name="gp_competitions_startdate" type="text" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
    		<li><label>Start Time</label><input name="gp_competitions_starttime" type="text" value="<?php echo $clean_st; ?>" /></li>
    		<li><label>Close Date</label><input name="gp_competitions_enddate" type="text" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
    		<li><label>Close Time</label><input name="gp_competitions_endtime" type="text" value="<?php echo $clean_et; ?>" /></li>
    		<li><label>Draw Date</label><input name="gp_competitions_drawdate" type="text" class="tfdate" value="<?php echo $clean_dd; ?>" /></li>
    		<li><label>Draw Time</label><input name="gp_competitions_drawtime" type="text" value="<?php echo $clean_dt; ?>" /></li>
    	</ul>
    </div>
    <?php
}

function gp_save_postCompetitionDate_meta () {
    global $post;
    $thisposttype = get_post_type();

    if(isset($_POST[$thisposttype . '_startdate'])) {
        $updatestartd = strtotime ( $_POST[$thisposttype . '_startdate'] . $_POST[$thisposttype . '_starttime'] );
        update_post_meta($post->ID, $thisposttype . '_startdate', $updatestartd );
    }

    if(isset($_POST[$thisposttype . '_enddate'])) {
        $updateendd = strtotime ( $_POST[$thisposttype . '_enddate'] . $_POST[$thisposttype . '_endtime']);
        update_post_meta($post->ID, $thisposttype . '_enddate', $updateendd );
    }
     
    if(isset($_POST[$thisposttype . '_drawdate'])) {
        $updatedrawd = strtotime ( $_POST[$thisposttype . '_drawdate'] . $_POST[$thisposttype . '_drawtime']);
        update_post_meta($post->ID, $thisposttype . '_drawdate', $updatedrawd );
    }

    return $post;
}

function gp_create_postProductURL_meta ($post, $metabox) {
    $custom = get_post_custom($post->ID);

    $meta_product_url = isset($custom["gp_advertorial_product_url"][0]) ? $custom["gp_advertorial_product_url"][0] : "";
    $meta_product_call = isset($custom["gp_advertorial_call_to_action"][0]) ? $custom["gp_advertorial_call_to_action"][0] : "";
    
    $meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
    $meta_nonce_value = wp_create_nonce( $meta_nonce_name );

    echo '<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />';
    ?>
    <div class="gp-meta">
    	<label>Call to action:</label>
    	<input id="gp_advertorial_call_to_action" 
    	       type="text" 
    	       name="gp_advertorial_call_to_action" 
    	       value="<?php if ( !empty($meta_product_call) ) { echo $meta_product_call; } else { echo 'Buy It!'; } ?>"><br />
    	<label>Url: </label><input id="gp_advertorial_product_url" type="text" name="gp_advertorial_product_url" value="<?php if ( !empty($meta_product_url) ) { echo $meta_product_url; } else { echo 'http://'; } ?>">
    </div>
    <?php 	  
}

function gp_save_postProductURL_meta () {
	global $post;
	$thisposttype = get_post_type();
	
	if(isset($_POST[$thisposttype . '_call_to_action'])) {		# gp_advertorial meta - url for 'Buy It!' button
		update_post_meta($post->ID, $thisposttype . '_call_to_action', $_POST[$thisposttype . '_call_to_action'] );
	}
	
	if(isset($_POST[$thisposttype . '_product_url'])) {		# gp_advertorial meta - url for 'Buy It!' button
		update_post_meta($post->ID, $thisposttype . '_product_url', $_POST[$thisposttype . '_product_url'] );
	}
	
	return $post;
}
?>