<?php
/**
 * Sends new product data to chargify api and updates user meta for an existing advertiser that
 * upgrades or downgrades to a different plan from their profile page.
 * 
 *  1. Inserts chargify data from signup into wp_usermeta
 *  2. Explains to client how advertising works
 *  3. Inlcudes gravity form for advertiser to create their first post
 *  
 *  Authors: Katie Patrick & Jesse Browne
 *  		 kp@greenpag.es
 *           jb@greenpag.es
 *           
 **/

global $current_user;
$user_id = $current_user->ID;

echo '<p>Hello World</p>.';


?>
