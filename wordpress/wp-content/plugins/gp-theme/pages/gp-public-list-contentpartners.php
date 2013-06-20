<?php
// Shows list of content parters
// Sorts content partners by nation for display purpouses
// ToDo** Find the user's country and show user's country shows first 

global $current_user, $wpdb, $wp_roles;

if (!is_page()) {
	return false;
}

// Match the first loop with the user's country

// Get country code and content-partner details
$query = "SELECT wp_users.ID 
          FROM wp_users 
              LEFT JOIN wp_usermeta on wp_usermeta.user_id=wp_users.ID 
              LEFT JOIN wp_usermeta AS m1 on m1.user_id=wp_users.ID and m1.meta_key = 'gp_google_geo_country'
          WHERE wp_users.user_status = 0      
              AND wp_usermeta.meta_key = 'wp_capabilities'
              AND wp_usermeta.meta_value RLIKE '[[:<:]]contributor[[:>:]]'               
          ORDER BY m1.meta_value, wp_users.display_name;";
  
$country_map = get_country_map();     
  
$contributors = $wpdb->get_results($query);
  
if ($contributors) {
  	$cp_string .= '<div class="contentpartnerslist">';
    $temp_country_code = '';
    
    foreach($contributors as $contributor) {
        	     
        $thisuser = get_userdata($contributor->ID);
        
        $country_code = $thisuser->gp_google_geo_country;
        $country_full_name = $country_map[$country_code];
        
        if ($country_code != $temp_country_code) {
        	$cp_string .= '<div class="clear"></div>
        	               <h3>'. $country_full_name .'</h3>
        	               <div class="clear"></div>';
        }
           
        $cp_string .= '<a href="' . get_author_posts_url($thisuser->ID) . '" title="Posts by ' . esc_attr($thisuser->display_name) . '">' . 
                           get_avatar( $thisuser->ID, '50', '', $thisuser->display_name ) . '<span>' . $thisuser->display_name . '</span>
                       </a>';
                       
        $temp_country_code = $country_code;
    }
   	$cp_string .= '</div><div class="clear"></div>';
}
?>