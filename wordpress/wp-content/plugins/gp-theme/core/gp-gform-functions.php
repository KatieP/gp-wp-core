<?php

/** 
 *  Gravity Forms extension functions
 * 
 *  Jobs like integrating google places autocomplete with Gravity Forms,
 *  updating post meta or updating custom columns in
 *  wp_posts table on post creation and post editing are done here.
 *  
 */

add_filter("gform_date_min_year", "set_min_year");
function set_min_year($min_year){
    /** 
     * Set Minimum year for Gravity form year selector drop down menus 
     */
    $current_year = date("Y");
    return $current_year;
}

function add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id) {
    /**
     *	Uses Gravity Form filter to assign appropriate id values to specific location 
     *  input fields of Gravity Forms. Location input fields are identified by a 
     *  css class name assigned to the field's wrapper during form creation.
     *  
     *  This enables Google Places autocomplete to work and forms to capture 
     *  location data i.e. lat, long etc for posts and member registration.
     *  
     *  Also captures and stores members tags /key words of interest in their profile. 
     *  http://www.gravityhelp.com/documentation/page/Gform_field_input
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es 
     *
     **/
    
    $field_css_class =  $field['cssClass'];
    $input_name_id =    $field['id'];
    $location = 		'gp_google_geo_location';
    $latitude = 		'gp_google_geo_latitude';
    $longitude = 		'gp_google_geo_longitude';
    $country = 			'gp_google_geo_country';
    $admin_lvl_one = 	'gp_google_geo_administrative_area_level_1';
    $admin_lvl_two = 	'gp_google_geo_administrative_area_level_2';
    $admin_lvl_three = 	'gp_google_geo_administrative_area_level_3';
    $locality = 		'gp_google_geo_locality';
    $locality_slug = 	'gp_google_geo_locality_slug';
    $user_tags =        'gp_user_tags';
    $notification_set = 'notification_setting';
    $weekly_email =     'weekly_email';
    $monthly_email =    'monthly_email';
    $type =             'type="hidden"';
    $read_only = 		'readonly="readonly"';    
    
    # Check css class name for match with location class names above and define id on match
    switch ($field_css_class) {
        case $location:
            $type = 'type="text"';
            $read_only = '';
            $input_id = $location;
            break;
        case $latitude:
            $input_id = $latitude;
            break;
        case $longitude:
            $input_id = $longitude;
            break;
        case $country:
            $input_id = $country;
            break;
        case $admin_lvl_one:
            $input_id = $admin_lvl_one;
            break;
        case $admin_lvl_two:
            $input_id = $admin_lvl_two;
            break;
        case $admin_lvl_three:
            $input_id = $admin_lvl_three;
            break;
        case $locality:
            $input_id = $locality;
            break;
        case $locality_slug:
            $input_id = $locality_slug;
            break;
        case $user_tags:
            $type = 'type="text"'; 
            $read_only = '';
            $input_id = $user_tags;
            break;
        case $notification_set:
            $type = 'type="radio"';
            $read_only = '';
            $input_id = $notification_set;
            break;
    }

    switch ($type) {
        case 'type="radio"':
            $input = (isset($input_id)) ? get_correct_radio_buttons($input_name_id, $input_id, $type, $read_only) : '';
            break;
        default:
            $input = (isset($input_id)) ? get_correct_input_field($input_name_id, $input_id, $type, $read_only) : '';
    }
           
    return $input;
}
add_filter("gform_field_input", "add_location_and_tag_fields", 10, 5);

function get_correct_input_field($input_name_id, $input_id, $type, $read_only) {
    /**
	 *  Returns location input field for Gravity Forms with appropriate id 
	 *  value to work with Google places autocomplete, location data 
	 *  and user tags / topics of interest. 
	 *  Called by add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id)
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     */
    
    global $current_user;
    
    $current_data = (isset($current_user->$input_id)) ? $current_user->$input_id : '';
    
    $correct_input = '<div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $input_id .'" '. $type .' 
                                 value="'. $current_data .'" '. $read_only .' class="medium" tabindex="5">
                      </div>';    

    return $correct_input;
}

function get_correct_radio_buttons($input_name_id, $input_id, $type, $read_only) {
    /**
	 *  Returns notification setting radio button for Gravity Forms with appropriate id  
	 *  Called by add_location_and_tag_fields($input, $field, $value, $lead_id, $form_id)
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     **/
    
    global $current_user;
    $notification_setting =  $current_user->notification_setting;
    $daily =                 'daily_email';
    $weekly =                'weekly_email';
    $monthly =               'monthly_email';
    $system =                'system_email';
    $daily_decription =      '<span class="slightly-larger-font">
                                  <strong>Daily: \'The Green Laser\'</strong> Get notified each day of news, events and projects happening near you
                              </span>';
    $weekly_decription =     '<span class="slightly-larger-font">
                                  <strong>Weekly: \'The Green Razor\'</strong> The best of your environmental movement in a weekly email
                              </span>';
    $monthly_decription =    '<span class="slightly-larger-font">
                                  <strong>Monthly: \'The Green Phaser\'</strong> The best of the Green Pages Community of the month
                              </span>';
    $system_decription =     '<span class="slightly-larger-font">
                                  <strong>Rare: \'System Messages Only\'</strong>
                              </span>';
    
    switch ($notification_setting) {
        case 'daily_email':
            $check_daily_email =    ' checked="checked"';
            $check_weekly_email =   '';
            $check_monthly_email =  '';
            $check_system_email =   '';
            break;
        case 'weekly_email':
            $check_daily_email =    '';
            $check_weekly_email =   ' checked="checked"';
            $check_monthly_email =  '';
            $check_system_email =   '';
            break;
        case 'monthly_email':
            $check_daily_email =    '';
            $check_weekly_email =   ''; 
            $check_monthly_email =  ' checked="checked"';
            $check_system_email =   '';
            break;
        case 'system_email':
            $check_daily_email =    '';
            $check_weekly_email =   ''; 
            $check_monthly_email =  '';
            $check_system_email =   ' checked="checked"';
            break;
    }
    
    // Currently only offering weekly and rare system only options
    $correct_input = '<div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $weekly .'" '. $type .' 
                                 value="'. $weekly .'" '. $check_weekly_email .' tabindex="5"> 
                          '. $weekly_decription .'  
                      </div>
                      <div class="ginput_container">
                          <input name="input_'. $input_name_id .'" id="'. $system .'" '. $type .' 
                                 value="'. $system .'" '. $check_system_email .' tabindex="5"> 
                          '. $system_decription .'
                      </div>';

    return $correct_input;
}

function set_event_dates_lat_and_long($entry, $form) {
    /**
     * Converts event date data from gravity form into timestamp
     * so that event can be sorted by start date in event_index()
     * 
     * Also sets post lat and long as decimal for events in post table
     * Triggered on gravity form submission
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     **/
    
    global $wpdb, $post;
    $post    = get_post($entry["post_id"]);
    setup_postdata( $post ); 
    $post_id = $post->ID;

	// Avoid an infinite loop by the following
	if ( !wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
        remove_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);
	    
        $start_key    = 'gp_events_startdate';
        $end_key      = 'gp_events_enddate';
        $start_date   = get_post_meta($post_id, $start_key, true);
        $end_date     = get_post_meta($post_id, $end_key, true);
        
        if ( !empty($start_date) && !empty($end_date) ) {

            $start_ts = strtotime($start_date);
            $end_ts   = strtotime($end_date);
            update_post_meta($post_id, $start_key, $start_ts);
            update_post_meta($post_id, $end_key, $end_ts);     
        
        }    

        if ($post->post_type == 'gp_news') {

            $location_meta_key = 	   'gp_google_geo_location';
            $lat_meta_key = 		   'gp_google_geo_latitude';
            $long_meta_key = 		   'gp_google_geo_longitude';
            $country_meta_key = 	   'gp_google_geo_country';
            $admin_lvl_one_key = 	   'gp_google_geo_administrative_area_level_1';
            $admin_lvl_two_key = 	   'gp_google_geo_administrative_area_level_2';
            $admin_lvl_three_key = 	   'gp_google_geo_administrative_area_level_3';
            $locality_key = 		   'gp_google_geo_locality';
            $locality_slug_key = 	   'gp_google_geo_locality_slug';
            
            $edit_news_uri =           '/forms/update-news/?gform_post_id='. $post->ID;
            
            $location_entry = 	       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '9'  : '8' ;
            $lat_entry = 		       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '10' : '9' ;
            $long_entry = 		       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '11' : '10';
            $country_entry = 	       ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '12' : '11';
            $admin_lvl_one_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '13' : '12';
            $admin_lvl_two_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '14' : '13';
            $admin_lvl_three_entry =   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '15' : '14';
            $locality_entry = 		   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '16' : '15';
            $locality_slug_entry = 	   ( strpos($_SERVER['REQUEST_URI'], $edit_news_uri ) === 0 ) ? '17' : '16';              
            
            $original_location =       $entry[$location_entry];
            $original_lat =            $entry[$lat_entry];
            $original_long =           $entry[$long_entry];
            $original_country =        $entry[$country_entry];
            $original_admin_1 =        $entry[$admin_lvl_one_entry];
            $original_admin_2 =        $entry[$admin_lvl_two_entry];
            $original_admin_3 =        $entry[$admin_lvl_three_entry];
            $original_locality =       $entry[$locality_entry];
            $original_locality_slug =  $entry[$locality_slug_entry];
        
            update_post_meta($post_id, $location_meta_key, $original_location); 
            update_post_meta($post_id, $lat_meta_key, $original_lat);
            update_post_meta($post_id, $long_meta_key, $original_long);
            update_post_meta($post_id, $country_meta_key, $original_country); 
            update_post_meta($post_id, $admin_lvl_one_key, $original_admin_1); 
            update_post_meta($post_id, $admin_lvl_two_key, $original_admin_2); 
            update_post_meta($post_id, $admin_lvl_three_key, $original_admin_3); 
            update_post_meta($post_id, $locality_key, $original_locality);
            update_post_meta($post_id, $locality_slug_key, $original_locality_slug);            
        }
            
        $post_lat  =       (float) get_post_meta($post_id, $lat_meta_key, true);
        $post_long =       (float) get_post_meta($post_id, $long_meta_key, true);
            
    	// update the post, with lat and long as decimal
		$table =           'wp_posts';
		$data =            array( 'post_latitude' => $post_lat, 'post_longitude' => $post_long );
		$where =           array( 'ID' => $post_id );
		$format =          array( '%s', '%s' );
   
        $wpdb->update($table, $data, $where, $format);     

        add_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);    
	}
}
add_action("gform_after_submission", "set_event_dates_lat_and_long", 10, 2);

?>
