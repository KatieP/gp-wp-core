<?php
#
add_action( 'add_meta_boxes', 'create_GPMeta' );

add_action( 'save_post', 'save_GPMeta' );

function create_GPMeta () {
	// Create Meta Boxes Router
	global $newposttypes;
	for($index = 0; $index < count($newposttypes); $index++) {
		if ( $newposttypes[$index]['enabled'] == true && is_array( $newposttypes[$index]['GPmeta'] ) ) {
			foreach ( $newposttypes[$index]['GPmeta'] as $metabox ) {
				if ( is_array( $metabox ) ) {
					if ( $metabox['id'] && $metabox['title'] && function_exists( 'gp_create_' . $metabox['id'] . '_meta' ) ) {
							add_meta_box( 'gp_create_' . $metabox['id'] . '_meta', $metabox['title'] , 'gp_create_' . $metabox['id'] . '_meta', $newposttypes[$index]['id'] );
					}
				}
			}
		}
	}
}

function save_GPMeta () {
	// Save Meta Boxes Router
	global $post, $newposttypes;
	
	$thisposttype = get_post_type();
	
	if ( isset( $_POST[$thisposttype . '-nonce'] ) && !wp_verify_nonce( $_POST[$thisposttype . '-nonce'], $thisposttype . '-nonce' ) ) {
		return $post->ID;
	}
    
	if ( isset( $post ) && !current_user_can( 'edit_post', $post->ID ) ) {
		return $post->ID;
	}
	
	for($index = 0; $index < count($newposttypes); $index++) {
		if ( $newposttypes[$index]['enabled'] == true && is_array( $newposttypes[$index]['GPmeta'] ) ) {
			foreach ( $newposttypes[$index]['GPmeta'] as $metabox ) {
				if ( is_array( $metabox ) ) {
					if ( $metabox['id'] && $metabox['title'] && function_exists( 'gp_save_' . $metabox['id'] . '_meta' ) ) {
						call_user_func('gp_save_' . $metabox['id'] . '_meta');	
					}
				}
			}
		}
	}
}

function gp_create_postGeoLoc_meta() {
	global $post, $newposttypes;
	$custom = get_post_custom($post->ID);
	$thisposttype = get_post_type();
	
	$meta_geoloc = (!isset($custom[$thisposttype . "_geoloc"][0])) ? "" : $custom[$thisposttype . "_geoloc"][0];
	$meta_nonce = wp_create_nonce( 'gp_postGeoLoc_meta-nonce' );
	
	echo '
		<input type="hidden" name="gp_postGeoLoc_meta-nonce" id="gp_postGeoLoc_meta-nonce" value="' . $meta_nonce . '" />
	
		<div class="gp-meta">
			<input name="' . $thisposttype . '_geoloc" type="text" style="width:99%;" value="' . $meta_geoloc . '" />
		</div>
	';
}

function gp_save_postGeoLoc_meta() {
	global $post, $newposttypes;
	$thisposttype = get_post_type();
    
    /* set your custom fields */
	if(isset($_POST[$thisposttype . '_geoloc'])) {
    	update_post_meta($post->ID, $thisposttype . '_geoloc', $_POST[$thisposttype . '_geoloc'] );
    }
    
    return $post;
}

function gp_create_postEventDate_meta () {
    global $post, $states_au;
    $custom = get_post_custom($post->ID);

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

    echo '<input type="hidden" name="gp_events-nonce" id="gp_events-nonce" value="' . wp_create_nonce( 'gp_events-nonce' ) . '" />';
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

function gp_create_postCompetitionDate_meta () {
    global $post;
    $custom = get_post_custom($post->ID);

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

    echo '<input type="hidden" name="gp_competitions-nonce" id="gp_competitions-nonce" value="' . wp_create_nonce( 'gp_competitions-nonce' ) . '" />';
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

function gp_create_postProductURL_meta () {
	 global $post;
	 $custom = get_post_custom($post->ID);
	 $meta_product_url = isset($custom["gp_advertorial_product_url"][0]) ? $custom["gp_advertorial_product_url"][0] : "";
	 
	 echo '<input type="hidden" name="gp_advertorial-nonce" id="gp_advertorial-nonce" value="' . wp_create_nonce( 'gp_advertorial-nonce' ) . '" />';
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