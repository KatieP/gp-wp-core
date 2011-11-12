<?php
global $current_user, $wpdb, $wp_roles;

if (!is_page()) {
	return false;
}


  $query = "SELECT wp_users.ID FROM wp_users LEFT JOIN wp_usermeta on wp_usermeta.user_id=wp_users.ID WHERE wp_users.user_status = 0 AND wp_usermeta.meta_key = 'wp_capabilities' AND wp_usermeta.meta_value RLIKE '[[:<:]]contributor[[:>:]]' ORDER BY wp_users.display_name;";
  $contributors = $wpdb->get_results($query);
  if ($contributors) {
  	$cp_string .= '<div class="contentpartnerslist">';
    foreach($contributors as $contributor) {
      $thisuser = get_userdata($contributor->ID);
      $cp_string .= '<a href="' . get_author_posts_url($thisuser->ID) . '" title="Posts by "' . esc_attr($thisuser->display_name) . '">' . get_avatar( $thisuser->ID, '50', '', $thisuser->display_name ) . '<span>' . $thisuser->display_name . '</span></a>';
    }
   	$cp_string .= '</div><div class="clear"></div>';
  }
?>