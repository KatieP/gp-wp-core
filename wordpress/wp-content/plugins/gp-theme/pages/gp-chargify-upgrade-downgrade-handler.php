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
 *           kp@greenpag.es
 *           jb@greenpag.es
 *
 **/

if ( is_user_logged_in() ) {

    global $current_user;
    $user_id =   $current_user->ID;
    $site_url =  get_site_url();

    if ( ($_POST['upgrade']) || ($_POST['downgrade']) ) {

        $product_id_key   = 'product_id';

        ?><p>Adjusting your plan right now, please do not exit or reload this page while update in progress ...</p><?php
        
        if ($_POST['upgrade']) {
            $product_id_value =  $_POST['upgrade'];
        }

        if ($_POST['downgrade']) {
            $product_id_value =  $_POST['downgrade'];
        }

        $subscription_id =    $current_user->subscription_id;
        $json =               '
                              {
                                  "migration":{
                                      "product_id": '. $product_id_value .'
                                  }
                              }
                              ';
        
        $chargify_key =       '3FAaEvUO_ksasbblajon';
        $chargify_auth =      $chargify_key .':x';
        $chargify_auth_url =  'https://'. $chargify_auth .'green-pages.chargify.com/subscriptions/';

        $chargify_url = 'https://green-pages.chargify.com/subscriptions/' . $subscription_id .'/migrations.json';

        $ch = curl_init($chargify_auth_url);

        $array = array();
        array_push($array, 'Content-Type: application/json;', 'Accept: application/json;', 'charset=utf-8;');

        curl_setopt($ch, CURLOPT_HTTPHEADER, $array);
        curl_setopt($ch, CURLOPT_URL, $chargify_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_USERPWD, $chargify_auth);

        $json_result = curl_exec($ch);

        $result = json_decode($json_result);

        if ($result->subscription->product->id) {

            update_user_meta( $user_id, $product_id_key, $product_id_value );
            
            $product_handle_key   =    'product_handle';
            update_user_meta( $user_id, $product_handle_key, $result->subscription->product->handle );
            
            $adv_signup_time_key     = 'adv_signup_time';
            $adv_signup_time_value   = strtotime($result->subscription->current_period_started_at);
            update_user_meta($user_id, $adv_signup_time_key, $adv_signup_time_value );
            
            $budget_status_key       = 'budget_status';
            $budget_status_value     = 'active';
            update_user_meta($user_id, $budget_status_key, $budget_status_value);

            ?><p>Your plan has been successfully adjusted, update is now complete.</p>
            <p>Thanks for using Green Pages!</p><?php 
        }

    }

} else {

    ?><p>Not sure how you got here without being logged in, head back to the <a href="<?php echo $site_url; ?>">home page</a>, 
    there's nothing to see here.</p><?php

}
?>
