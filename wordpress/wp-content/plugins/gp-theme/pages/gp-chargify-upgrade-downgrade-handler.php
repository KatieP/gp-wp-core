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
if ( is_user_logged_in() ) {
    global $current_user;
    $user_id =   $current_user->ID;
    $site_url =  get_site_url();
    
    echo '<p>Hello World.</p>';
    
    if ($_POST['upgrade']) {
        echo 'Upgrading to plan with product_id '. $_POST['upgrade'];
    }
    
    if ($_POST['downgrade']) {
        echo 'Upgrading to plan with product_id '. $_POST['downgrade'];
    }
} else {
    ?><p>Not sure how you got here without being logged in, head back to the <a href="<?php echo $site_url; ?>">home page</a>, there's nothing to see here.</p><?php
}
?>
