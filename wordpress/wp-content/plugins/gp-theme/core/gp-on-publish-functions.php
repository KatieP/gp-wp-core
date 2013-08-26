<?php

/**
 * Actions that occur when posts are published are defined here. 
 * Such as cleaning broken images in posts that come 
 * through rss feeds, setting posts to pending from new users etc. 
 *  
 */

function email_after_post_approved($post_ID) {
  /**
   * Send notification to post author when transition from
   * pending to publish occurs, includes link to post. 
   * 
   */
    
  $type = get_post_type($post_ID);
  $posttypeslug = getPostTypeSlug($type);

  $bcc = "kp@greenpag.es, jb@greenpag.es";

  $post = get_post($post_ID);
  $user = get_userdata($post->post_author);
  $post_url = site_url() . '/' . $posttypeslug . '/' . $post->post_name;

  $headers  = 'Content-type: text/html' . "\r\n";
  $headers .= 'Bcc: ' . $bcc . "\r\n";

  $body  = '<table width="600px" style="font-size: 15px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">';
  $body .= '<tr><td align="center">';
  $body .= '<table width="640">';
  $body .= '<tr style="padding: 0 20px 5px 5px;">';
  $body .= '<td style="font-size: 18px; text-transform:non; color:rgb(100,100,100);padding:0 0 0 5px;">';
  $body .= 'Hi ' . $user->display_name . "!<br /><br />";
  $body .= 'Your post on greenpag.es has been approved.  Thanks for posting!<br /><br />';
  $body .= 'You can see your new post at:<br />';
  $body .= '<a href="'. $post_url . '" >' . $post_url."</a><br /><br />";
  $body .= "Keep on making an amazing world.<br /><br />";
  $body .= "The greenpag.es team<br />";

  $body .= '<div style="color: rgb(0, 154, 194);font=size:13px; ">';
  $body .= 'greenpag.es &nbsp; <br />';
  $body .= '<a href="mailto:hello@greenpag.es">hello@greenpag.es</a>&nbsp;';
  $body .= '<a href="'. get_site_url() .'">'. get_site_url() .'</a>';
  $body .= '<br />';
  $body .= '</div>';

  $body .= '</td></tr></table></td></tr></table><br /><br />';

  wp_mail($user->user_email, 'Your post on greenpag.es has been approved!', $body, $headers);

}
add_action('pending_to_publish', 'email_after_post_approved');

function force_comments_open($post_id) {
    /**
     * Ensure comments are always enabled for custom post types
     */

    global $wpdb;
    $post = get_post($post_id);
    $post_score = strtotime($post->post_date_gmt);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'force_comments_open');
		remove_action('publish_gp_events', 'force_comments_open');
		remove_action('publish_gp_advertorial', 'force_comments_open');
		remove_action('publish_gp_projects', 'force_comments_open');
    
		// update the post, which calls publish_gp_news again
		$comment_status =  'open';
		$table =           'wp_posts';
		
		$data =            array( 'comment_status' => $comment_status );
		$where =           array( 'ID' => $post_id );
		$format =          array( '%s' );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'force_comments_open');
		add_action('publish_gp_events', 'force_comments_open');
		add_action('publish_gp_advertorial', 'force_comments_open');
		add_action('publish_gp_projects', 'force_comments_open');
	}
}
add_action('publish_gp_news', 'force_comments_open');
add_action('publish_gp_events', 'force_comments_open');
add_action('publish_gp_advertorial', 'force_comments_open');
add_action('publish_gp_projects', 'force_comments_open');

function create_popularity_timestamp($post_id) {	
    /** 
     *  Sets date published in unix time as popularity score.
     *  Executed when post is published, including those coming
     *  from rss feeds set to auto publish.
     *    
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */
    
	global $wpdb;
    $post = get_post($post_id);
    $post_score = strtotime($post->post_date_gmt);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'create_popularity_timestamp');
		remove_action('publish_gp_events', 'create_popularity_timestamp');
		remove_action('publish_gp_advertorial', 'create_popularity_timestamp');
		remove_action('publish_gp_projects', 'create_popularity_timestamp');
	    
		// update the post, which calls publish_gp_news again
		$table =   'wp_posts';
		$data =    array( 'popularity_score' => $post_score );
		$where =   array( 'ID' => $post_id );
		$format =  array( '%s' );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'create_popularity_timestamp');
		add_action('publish_gp_events', 'create_popularity_timestamp');
		add_action('publish_gp_advertorial', 'create_popularity_timestamp');
		add_action('publish_gp_projects', 'create_popularity_timestamp');
	}
}
add_action('publish_gp_news', 'create_popularity_timestamp');
add_action('publish_gp_events', 'create_popularity_timestamp');
add_action('publish_gp_advertorial', 'create_popularity_timestamp');
add_action('publish_gp_projects', 'create_popularity_timestamp');

function set_post_location_data_as_decimal($post_id) {
    /** 
     * Store post latitude and longitude as decimal in wp_posts so
     * we can do math on the values when getting surrounding posts of 
     * map centre co-ordinates for users and posts in show_google_maps().
     * 
     * We do this as wp_post_meta only stores lat and long as 
     * a string which is useless for querying surrounding posts later on.
     * 
     * Also if no post location defined sets post author location as 
     * post location.
     *   
     * Executed when post is published, including those coming
     * from rss feeds set to auto publish. 
     *    
     * Author: Jesse Browne
     * 	       jb@greenpag.es
     **/
    
    global $wpdb, $post;
    $post = get_post($post_id);
    
    // Avoid an infinite loop by the following
	if ( !wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'set_post_location_data_as_decimal');
	    
		$post_author = get_userdata($post->post_author);
        $post_author_id = $post_author->ID;
    
        $location_meta_key = 	'gp_google_geo_location';
        $lat_meta_key = 		'gp_google_geo_latitude';
        $long_meta_key = 		'gp_google_geo_longitude';
        $country_meta_key = 	'gp_google_geo_country';
        $admin_lvl_one_key = 	'gp_google_geo_administrative_area_level_1';
        $admin_lvl_two_key = 	'gp_google_geo_administrative_area_level_2';
        $admin_lvl_three_key = 	'gp_google_geo_administrative_area_level_3';
        $locality_key = 		'gp_google_geo_locality';
        $locality_slug_key = 	'gp_google_geo_locality_slug';
        
        $post_location = get_post_meta($post_id, $location_meta_key, true);
        
        if ( empty($post_location) ) {
            $author_location =         get_user_meta($post_author_id, $location_meta_key, true);
            $author_lat =              get_user_meta($post_author_id, $lat_meta_key, true);
            $author_long =             get_user_meta($post_author_id, $long_meta_key, true);
            $author_country =          get_user_meta($post_author_id, $country_meta_key, true);
            $author_admin_lvl_one =    get_user_meta($post_author_id, $admin_lvl_one_key, true);
            $author_admin_lvl_two =    get_user_meta($post_author_id, $admin_lvl_two_key, true);
            $author_admin_lvl_three =  get_user_meta($post_author_id, $admin_lvl_three_key, true);
            $author_locality =         get_user_meta($post_author_id, $locality_key, true);
            $author_location_slug =    get_user_meta($post_author_id, $locality_slug_key, true);
        
            update_post_meta($post_id, $location_meta_key, $author_location); 
            update_post_meta($post_id, $lat_meta_key, $author_lat);
            update_post_meta($post_id, $long_meta_key, $author_long);
            update_post_meta($post_id, $country_meta_key, $author_country); 
            update_post_meta($post_id, $admin_lvl_one_key, $author_admin_lvl_one); 
            update_post_meta($post_id, $admin_lvl_two_key, $author_admin_lvl_two); 
            update_post_meta($post_id, $admin_lvl_three_key, $author_admin_lvl_three); 
            update_post_meta($post_id, $locality_key, $author_locality);
            update_post_meta($post_id, $locality_slug_key, $author_location_slug);
        }
    
        $post_lat  = (float) get_post_meta($post_id, $lat_meta_key, true);
        $post_long = (float) get_post_meta($post_id, $long_meta_key, true);
		
		// update the post, which calls publish_gp_news again
		$table = 'wp_posts';
		$data = array(
		            'post_latitude' => $post_lat,
		            'post_longitude' => $post_long
		        );
		$where = array(
		             'ID' => $post_id
		         );
		$format = array(
				      '%s',
		              '%s'
				  );

        $wpdb->update($table, $data, $where, $format);
		
		// re-hook this function
		add_action('publish_gp_news', 'set_post_location_data_as_decimal');
	}
}
add_action('publish_gp_news', 'set_post_location_data_as_decimal');

function member_permission_upgrade($post_id) {	
    /** 
     *  Checks number of approved posts a user has, then if is greater than 3,  
     *  Adds user meta called 'subscriber_approved' 'true' when subscribed publishes their third post
     *  Another function will check this user_meta value for true or false when setting posts to publish or pending
     *  The first 3 posts a user makes will require approval from GP staff
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     */
    
	global $wpdb;
    $post = get_post($post_id);    
    $post_author = get_userdata($post->post_author);
    $post_author_ID = $post_author->ID;
    
	$post_count = (int) $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = '" . $post_author_ID . "' AND post_status = 'publish'");
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'member_permission_upgrade');
		remove_action('publish_gp_events', 'member_permission_upgrade');
		remove_action('publish_gp_advertorial', 'member_permission_upgrade');
		remove_action('publish_gp_projects', 'member_permission_upgrade');
	    
	    if ($post_count > 2) {
		    // if user_post_count is greater than 3, change user meta
				
		    $meta_key =  'subscriber_approved';
		    $meta_value = true;
		
		    add_user_meta( $post_author_ID, $meta_key, $meta_value, true );
		    
		}
		
		// re-hook this function
		add_action('publish_gp_news', 'member_permission_upgrade');
		add_action('publish_gp_events', 'member_permission_upgrade');
		add_action('publish_gp_advertorial', 'member_permission_upgrade');
		add_action('publish_gp_projects', 'member_permission_upgrade');
		
	}
}
add_action('publish_gp_news', 'member_permission_upgrade');
add_action('publish_gp_events', 'member_permission_upgradep');
add_action('publish_gp_advertorial', 'member_permission_upgrade');
add_action('publish_gp_projects', 'member_permission_upgrade');

function set_post_to_pending_if_subscriber_not_approved($post_id) {	
    /** 
     *  If subscriber has published less than required amount of posts
     *  set post status to pending
     *  
     *  Author: Katie Patrick
     *  		katie.patrick@greenpag.es
     **/

	$post = get_post($post_id);
    $post_author = get_userdata($post->post_author);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
		remove_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');	    
	    
		if ( !get_user_role( array('administrator') ) ) { 
		    if ( !get_user_role( array('contributor') ) ) { 
	            if ( $post_author->subscriber_approved != true ) {
                    $update_post =                 array();
                    $update_post['ID'] =           $post_id;
                    $update_post['post_status'] =  'pending';
                    wp_update_post($update_post);
		        }
		    }
		}
		
		// re-hook this function
		add_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
		add_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');
		
	}
}
add_action('publish_gp_news', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_events', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_advertorial', 'set_post_to_pending_if_subscriber_not_approved');
add_action('publish_gp_projects', 'set_post_to_pending_if_subscriber_not_approved');

function fix_broken_images($post_id) {	
    /** 
     *  Remove The Conversation attribution image
     *  and fix broken img src in Greenpeace feeds
     *  
     *  Author: Jesse Browne
     *  		jb@greenpag.es
     **/

	$post = get_post($post_id);
    $post_author = get_userdata($post->post_author);
    
	// Avoid an infinite loop by the following
	if ( ! wp_is_post_revision( $post_id ) ){
	
		// unhook this function so it doesn't loop infinitely
		remove_action('publish_gp_news', 'fix_broken_images'); 
	    
		$content = $post->post_content;
		$content = str_replace('feedproxy.google.com', 'www.greenpeace.org', $content);
		$content = str_replace('<div id="the_conversation_attribution" style="float:right;">
        <a href="http://theconversation.com/"><br />
          <img src="http://theconversation.com/assets/logos/theconversation_vertical_100px-ab58f56b4507a90ced4077004eb0692e.png" alt="The Conversation"><br />
        </a>
      </div>', '', $content);
		
		$update_post =                  array();
        $update_post['ID'] =            $post_id;
        $update_post['post_content'] =  $content;
        wp_update_post($update_post);
		
		// re-hook this function
		add_action('publish_gp_news', 'fix_broken_images');
	}
}
add_action('publish_gp_news', 'fix_broken_images');

?>
