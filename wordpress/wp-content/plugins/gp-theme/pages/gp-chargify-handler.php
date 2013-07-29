<?php
/**
 * Payment confirmation and first post creation form
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
    $user_id = $current_user->ID;
    
    // subscription_id subscription_id={subscription_id}&
    $subscription_id_key   = 'subscription_id';
    $subscription_id_value = ( !empty($_GET[$subscription_id_key]) ) ? $_GET[$subscription_id_key] : '';
    if (!empty($subscription_id_value)) { update_user_meta($user_id, $subscription_id_key, $subscription_id_value ); }
    
    // customer_id customer_id={customer_id}&
    $customer_id_key   = 'customer_id';
    $customer_id_value = ( !empty($_GET[$customer_id_key]) ) ? $_GET[$customer_id_key] : '';
    if (!empty($customer_id_value)) { update_user_meta($user_id, $customer_id_key, $customer_id_value ); }
    
    // product_handle product_handle={product_handle}&
    $product_handle_key   = 'product_handle';
    $product_handle_value = ( !empty($_GET[$product_handle_key]) ) ? $_GET[$product_handle_key] : '';
    if (!empty($product_handle_value)) { update_user_meta($user_id, $product_handle_key, $product_handle_value ); }
    
    // product_id product_id={product_id}&
    $product_id_key   = 'product_id';
    $product_id_value = ( !empty($_GET[$product_id_key]) ) ? $_GET[$product_id_key] : '';
    if (!empty($product_id_value)) { update_user_meta($user_id, $product_id_key, $product_id_value ); }
    
    // signup_revenue signup_revenue={signup_revenue}&
    $signup_revenue_key   = 'signup_revenue';
    $signup_revenue_value = ( !empty($_GET[$signup_revenue_key]) ) ? $_GET[$signup_revenue_key] : '';
    if (!empty($signup_revenue_value)) { update_user_meta($user_id, $signup_revenue_key, $signup_revenue_value ); }
    
    // signup_payment_id signup_payment_id={signup_payment_id}
    $signup_payment_id_key   = 'signup_payment_id';
    $signup_payment_id_value = ( !empty($_GET[$signup_payment_id_key]) ) ? $_GET[$signup_payment_id_key] : '';
    if (!empty($signup_payment_id_value)) { update_user_meta($user_id, $signup_payment_id_key, $signup_payment_id_value ); }
    
    // Advertiser_signup_time
    $adv_signup_time_key     = 'adv_signup_time';
    $adv_signup_time_value   = time();
    if (!empty($adv_signup_time_value)) { update_user_meta($user_id, $adv_signup_time_key, $signup_payment_id_value ); }
    
    // Set regular advertiser status to active
    $reg_advertiser_key       = 'reg_advertiser';
    $reg_advertiser_value     = '1';
    update_user_meta($user_id, $reg_advertiser_key, $reg_advertiser_value);
    
    // Set ad serving status to active
    $budget_status_key       = 'budget_status';
    $budget_status_value     = 'active';
    update_user_meta($user_id, $budget_status_key, $budget_status_value);
    
    ?>
    <h1 name="heading">Excellent. Now you'll need to create your first post!</h1>
    <p>Your post will be shown around the site in the region that you set and remain in the products section of greepag.es. 
    You will be billed for the clicks from this post at a maximum spend of <?php echo $product_handle_value; ?></p>
    <p>Your first two posts will be approved by a GP team member within 24 hours of posting. After that, you can create 
    new product posts as often as you like and they'll be visible without the need for approval! 
    They will show on the site until your budget has been used for the week.</p>
    <?php 
    
} else {
    
    ?><p>Not sure how you got here without being logged in. Head on back to the <a href="<?php echo $site_url; ?>">home page</a>  
    to see some more interesting things.</p><?php

}
?>

