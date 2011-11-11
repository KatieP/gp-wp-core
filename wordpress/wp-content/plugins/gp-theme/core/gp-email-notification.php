<?php

function wp_new_user_notification($user_id, $plaintext_pass = '') {
  $user = new WP_User($user_id);

  $user_login = stripslashes($user->user_login);
  $user_email = stripslashes($user->user_email);

  // The blogname option is escaped with esc_html on the way into the dtaabase 
  // in sanitize_option.  We want to reverse this for the plain text arena of 
  // emails.
  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
  $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
  $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

  @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

  if ( empty($plaintext_pass) )
    return;

  $message  = "Welcome to Green Pages!\r\n\r\n";
  $message .= sprintf(__('Username: %s'), $user_login) . "\r\n";
  $message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
  $message .= wp_login_url() . "\r\n";

  wp_mail($user_email, sprintf(__('[%s] Site Registration'), $blogname), $message);
}

?>
