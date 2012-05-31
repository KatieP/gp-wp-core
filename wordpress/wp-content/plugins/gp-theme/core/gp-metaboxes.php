<?php
add_action( 'add_meta_boxes', 'create_GPMeta' );
add_action( 'save_post', 'save_GPMeta' );
add_action( 'admin_head', 'js_GPMeta' );

function js_GPMeta () {
	// Attach Javascript for Meta Boxes
	global $newposttypes;
	$thisposttype = get_post_type();
	
	for($index = 0; $index < count($newposttypes); $index++) {
		if ( $newposttypes[$index]['enabled'] == true && $newposttypes[$index]['id'] == $thisposttype && is_array( $newposttypes[$index]['GPmeta'] ) ) {
			foreach ( $newposttypes[$index]['GPmeta'] as $metabox ) {
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
	global $newposttypes;
	for($index = 0; $index < count($newposttypes); $index++) {
		if ( $newposttypes[$index]['enabled'] == true && is_array( $newposttypes[$index]['GPmeta'] ) ) {
			foreach ( $newposttypes[$index]['GPmeta'] as $metabox ) {
				if ( is_array( $metabox ) ) {
					if ( isset( $metabox['id'] ) && isset( $metabox['title'] ) && function_exists( 'gp_create_' . $metabox['id'] . '_meta' ) ) {
							add_meta_box( 'gp_create_' . $metabox['id'] . '_meta', $metabox['title'] , 'gp_create_' . $metabox['id'] . '_meta', $newposttypes[$index]['id'], 'normal', 'default', $metabox );
					}
				}
			}
		}
	}
}

function save_GPMeta () {
	// Save Meta Boxes Router
	global $post, $newposttypes;
	
	if ( isset( $post ) && !current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}
	
	for($index = 0; $index < count($newposttypes); $index++) {
		if ( $newposttypes[$index]['enabled'] == true && is_array( $newposttypes[$index]['GPmeta'] ) ) {
			foreach ( $newposttypes[$index]['GPmeta'] as $metabox ) {
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
	global $newposttypes;
	
	$custom = get_post_custom($post->ID);
	$thisposttype = get_post_type();
	
	$meta_geo_location = (!isset($custom[$thisposttype . "_google_geo_location"][0])) ? "" : $custom[$thisposttype . "_google_geo_location"][0];
	$meta_geo_latitude = (!isset($custom[$thisposttype . "_google_geo_latitude"][0])) ? "" : $custom[$thisposttype . "_google_geo_latitude"][0];
	$meta_geo_longitude = (!isset($custom[$thisposttype . "_google_geo_longitude"][0])) ? "" : $custom[$thisposttype . "_google_geo_longitude"][0];
	$meta_geo_country = (!isset($custom[$thisposttype . "_google_geo_country"][0])) ? "" : $custom[$thisposttype . "_google_geo_country"][0];
	$meta_geo_administrative_area_level_1 = (!isset($custom[$thisposttype . "_google_geo_administrative_area_level_1"][0])) ? "" : $custom[$thisposttype . "_google_geo_administrative_area_level_1"][0];
	$meta_geo_administrative_area_level_2 = (!isset($custom[$thisposttype . "_google_geo_administrative_area_level_2"][0])) ? "" : $custom[$thisposttype . "_google_geo_administrative_area_level_2"][0];
	$meta_geo_administrative_area_level_3 = (!isset($custom[$thisposttype . "_google_geo_administrative_area_level_3"][0])) ? "" : $custom[$thisposttype . "_google_geo_administrative_area_level_3"][0];
	$meta_geo_locality = (!isset($custom[$thisposttype . "_google_geo_locality"][0])) ? "" : $custom[$thisposttype . "_google_geo_locality"][0];
	
	$meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
	$meta_nonce_value = wp_create_nonce( $meta_nonce_name );

	$inputhidden = ( get_user_role( array('administrator'), 0 ) ) ? 'type="text"' : 'type="hidden"';
	
	echo '
		<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />
	
		<div class="gp-meta">
			<input name="' . $thisposttype . '_google_geo_location" id="gp_google_geo_location" type="text" style="width:99%;" value="' . $meta_geo_location . '" />
			<input name="' . $thisposttype . '_google_geo_latitude" id="gp_google_geo_latitude" ' . $inputhidden . ' value="' . $meta_geo_latitude . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_longitude" id="gp_google_geo_longitude" ' . $inputhidden . ' value="' . $meta_geo_longitude . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_country" id="gp_google_geo_country" ' . $inputhidden . ' value="' . $meta_geo_country . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_administrative_area_level_1" id="gp_google_geo_administrative_area_level_1" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_1 . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_administrative_area_level_2" id="gp_google_geo_administrative_area_level_2" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_2 . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_administrative_area_level_3" id="gp_google_geo_administrative_area_level_3" ' . $inputhidden . ' value="' . $meta_geo_administrative_area_level_3 . '" readonly="readonly" />
			<input name="' . $thisposttype . '_google_geo_locality" id="gp_google_geo_locality" ' . $inputhidden . ' value="' . $meta_geo_locality . '" readonly="readonly" />
			<div id="map_canvas"></div>
		</div>
	';
}

function gp_save_postGeoLoc_meta() {
	global $post, $newposttypes;
	$thisposttype = get_post_type();
    
	if(isset($_POST[$thisposttype . '_google_geo_location'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_location', $_POST[$thisposttype . '_google_geo_location'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_latitude'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_latitude', $_POST[$thisposttype . '_google_geo_latitude'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_longitude'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_longitude', $_POST[$thisposttype . '_google_geo_longitude'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_country'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_country', $_POST[$thisposttype . '_google_geo_country'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_administrative_area_level_1'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_administrative_area_level_1', $_POST[$thisposttype . '_google_geo_administrative_area_level_1'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_administrative_area_level_2'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_administrative_area_level_2', $_POST[$thisposttype . '_google_geo_administrative_area_level_2'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_administrative_area_level_3'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_administrative_area_level_3', $_POST[$thisposttype . '_google_geo_administrative_area_level_3'] );
    }
    
	if(isset($_POST[$thisposttype . '_google_geo_locality'])) {
    	update_post_meta($post->ID, $thisposttype . '_google_geo_locality', $_POST[$thisposttype . '_google_geo_locality'] );
    }
    
    return $post;
}

function gp_js_postGeoLoc_meta() {
	global $post;
	
	$custom = get_post_custom($post->ID);
	$thisposttype = get_post_type();
	
	$meta_geolat = (!isset($custom[$thisposttype . "_google_geo_latitude"][0])) ? "-33.8688" : $custom[$thisposttype . "_google_geo_latitude"][0];
	$meta_geolng = (!isset($custom[$thisposttype . "_google_geo_longitude"][0])) ? "151.2195" : $custom[$thisposttype . "_google_geo_longitude"][0];
	
	echo '
	<script src="https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places" type="text/javascript"></script>
	<script type="text/javascript">
	function initialize() {
		var options = {
			types: [\'(cities)\']
		};
      
		var mapOptions = {
			center: new google.maps.LatLng(' . $meta_geolat . ', ' . $meta_geolng . '),
			zoom: 5,
			disableDefaultUI: true,
			disableDoubleClickZoom: true,
			draggable: false,
			keyboardShortcuts: false,
			scrollwheel: false,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		};
        
		var map = new google.maps.Map(document.getElementById(\'map_canvas\'), mapOptions);

		var input = document.getElementById(\'gp_google_geo_location\');
		var autocomplete = new google.maps.places.Autocomplete(input, options);

		autocomplete.bindTo(\'bounds\', map);

		var marker = new google.maps.Marker({map: map});

		google.maps.event.addListener(autocomplete, \'place_changed\', function() {
			var place = autocomplete.getPlace();
			if (place.geometry.viewport) {
				map.fitBounds(place.geometry.viewport);
				map.setZoom(5);
			} else {
				map.setCenter(place.geometry.location);
				map.setZoom(5);
			}

			var image = new google.maps.MarkerImage(
			place.icon,
			new google.maps.Size(71, 71),
			new google.maps.Point(0, 0),
			new google.maps.Point(17, 34),
			new google.maps.Size(35, 35));
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
	google.maps.event.addDomListener(window, \'load\', initialize);
 	</script>
	';
}

function gp_create_postEventDate_meta ($post, $metabox) {
    global $states_au;
    $custom = get_post_custom($post->ID);

    $meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
	$meta_nonce_value = wp_create_nonce( $meta_nonce_name );
    
    $meta_sd = isset($custom["gp_events_startdate"][0]) ? $custom["gp_events_startdate"][0] : "";
    $meta_ed = isset($custom["gp_events_enddate"][0]) ? $custom["gp_events_enddate"][0] : "";
    $meta_st = $meta_sd;
    $meta_et = $meta_ed;
    
    $meta_loccountry = 'AU';
    $meta_locstate = isset($custom["gp_events_locstate"][0]) ? $custom["gp_events_locstate"][0] : "";
    $meta_locsuburb = isset($custom["gp_events_locsuburb"][0]) ? $custom["gp_events_locsuburb"][0] : "";

    $date_format = get_option('date_format');
    $time_format = get_option('time_format');

    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_st = 0; $meta_et = 0;}
    
    $clean_sd = date("D, M d, Y", $meta_sd);
    $clean_ed = date("D, M d, Y", $meta_ed);
    $clean_st = date($time_format, $meta_st);
    $clean_et = date($time_format, $meta_et);

    echo '<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />';
    ?>
    <div class="tf-meta">
        <ul>
            <li><label>Start Date</label><input name="gp_events_startdate" type="text" class="tfdate" value="<?php echo $clean_sd; ?>" /></li>
            <li><label>Start Time</label><input name="gp_events_starttime" type="text" value="<?php echo $clean_st; ?>" /></li>
            <li><label>End Date</label><input name="gp_events_enddate" type="text" class="tfdate" value="<?php echo $clean_ed; ?>" /></li>
            <li><label>End Time</label><input name="gp_events_endtime" type="text" value="<?php echo $clean_et; ?>" /></li>
        </ul>
    </div>

    <div class="gp-meta">
        <ul>
            <li>
            	<label>State</label>
            		<select name="gp_events_locstate">
						<?php
						
						foreach ($states_au as $state) {
							if ($state == $meta_locstate) {$state_selected = ' selected';} else {$state_selected = '';}
		  					echo '<option value="' . $state . '"' . $state_selected . '>' . $state . '</option>';
						}
						
		  				?> 									
					</select>
            
            </li>
            <li><label>Suburb</label><input name="gp_events_locsuburb" type="text" value="<?php echo $meta_locsuburb; ?>" /></li>
        </ul>
        <input type="hidden" name="gp_events_loccountry" value="<?php echo $meta_loccountry; ?>" />
    </div>

    <?php
}

function gp_save_postEventDate_meta() {
	global $post, $newposttypes;
	$thisposttype = get_post_type();
	
	if(isset($_POST[$thisposttype . '_startdate'])) {
    	$updatestartd = strtotime ( $_POST[$thisposttype . '_startdate'] . $_POST[$thisposttype . '_starttime'] );
    	update_post_meta($post->ID, $thisposttype . '_startdate', $updatestartd );
    }

    if(isset($_POST[$thisposttype . '_enddate'])) {
    	$updateendd = strtotime ( $_POST[$thisposttype . '_enddate'] . $_POST[$thisposttype . '_endtime']);
    	update_post_meta($post->ID, $thisposttype . '_enddate', $updateendd );
    }
    
    
	if(isset($_POST[$thisposttype . '_loccountry'])) {
    	update_post_meta($post->ID, $thisposttype . '_loccountry', $_POST[$thisposttype . '_loccountry'] );
    }
    
	if(isset($_POST[$thisposttype . '_locstate'])) {
    	update_post_meta($post->ID, $thisposttype . '_locstate', $_POST[$thisposttype . '_locstate'] );
    }
    
	if(isset($_POST[$thisposttype . '_locsuburb'])) {
    	update_post_meta($post->ID, $thisposttype . '_locsuburb', $_POST[$thisposttype . '_locsuburb'] );
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

    if ($meta_sd == null) { $meta_sd = time(); $meta_ed = $meta_sd; $meta_dd = $meta_sd; $meta_st = 0; $meta_et = 0; $meta_dt = 0;}
    
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
	global $post, $newposttypes;
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
	 
	$meta_nonce_name = 'gp_' . $metabox['args']['id'] . '-nonce';
	$meta_nonce_value = wp_create_nonce( $meta_nonce_name );
	 
	 echo '<input type="hidden" name="' . $meta_nonce_name . '" id="' . $meta_nonce_name . '" value="' . $meta_nonce_value . '" />';
	 ?>	 
	 <div class="gp-meta">
	 	<label>Enter the url your product can be purchased from:  </label><input id="gp_advertorial_product_url" type="text" name="gp_advertorial_product_url" value="<?php 
	 	if ( !empty($meta_product_url) ) {
	 		echo $meta_product_url; 
	 	} 
	 	else {
			echo 'http://';
	 	}	
	 ?>">
	 </div>
	 <?php 	  
}

function gp_save_postProductURL_meta () {
	global $post, $newposttypes;
	$thisposttype = get_post_type();
	
	if(isset($_POST[$thisposttype . '_product_url'])) {		# gp_advertorial meta - url for 'Buy It!' button
		update_post_meta($post->ID, $thisposttype . '_product_url', $_POST[$thisposttype . '_product_url'] );
	}
	
	return $post;
}
?>