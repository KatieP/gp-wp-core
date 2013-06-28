<?php
//Payment confirmation and first post creation form
//1. Inserts chargify object from signup into wp_usermeta
//2. Explains to client how advertising works
//3. Inlcudes gravity form for advertiser to create their first post


global $current_user;
$user_id = $current_user->ID;

//subscription_id subscription_id={subscription_id}&
$subscription_id_key   = 'subscription_id';
$subscription_id_value = ( !empty($_GET[$subscription_id_key]) ) ? $_GET['subscription_id'] : '';
if (!empty($subscription_id_value)) { update_user_meta($user_id, $subscription_id_key, $subscription_id_value ); }

//customer_id customer_id={customer_id}&
$customer_id_key   = 'customer_id';
$customer_id_value = ( !empty($_GET[$customer_id_key]) ) ? $_GET['customer_id'] : '';
if (!empty($customer_id_value)) { update_user_meta($user_id, $customer_id_key, $customer_id_value ); }

//product_handle product_handle={product_handle}&
$subscription_id_key   = 'subscription_id';
$subscription_id_value = ( !empty($_GET[$subscription_id_key]) ) ? $_GET['subscription_id'] : '';
if (!empty($subscription_id_value)) { update_user_meta($user_id, $subscription_id_key, $subscription_id_value ); }

//product_id product_id={product_id}&
$product_handle_key   = 'product_handle';
$product_handle_value = ( !empty($_GET[$product_handle_key]) ) ? $_GET['product_handle'] : '';
if (!empty($product_handle_value)) { update_user_meta($user_id, $product_handle, $product_handle_value ); }

//signup_revenue signup_revenue={signup_revenue}&
$signup_revenue_key   = 'signup_revenue';
$signup_revenue_value = ( !empty($_GET[$signup_revenue_key]) ) ? $_GET['signup_revenue'] : '';
if (!empty($signup_revenue_value)) { update_user_meta($user_id, $signup_revenue_key, $signup_revenue_value ); }

//signup_payment_id signup_payment_id={signup_payment_id}
$signup_payment_id_key   = 'signup_payment_id';
$signup_payment_id_value = ( !empty($_GET[$signup_payment_id_key]) ) ? $_GET['signup_payment_id'] : '';
if (!empty($signup_payment_id_value)) { update_user_meta($user_id, $signup_payment_id_key, $signup_payment_id_value ); }

//Advertiser_signup_time

$adv_signup_time_key   = 'adv_signup_time';
$adv_signup_time_value = time();
if (!empty($adv_signup_time_value)) { update_user_meta($user_id, $adv_signup_time_key, $signup_payment_id_value ); }

?>