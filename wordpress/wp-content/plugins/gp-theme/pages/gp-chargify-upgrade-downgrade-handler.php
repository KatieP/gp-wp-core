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

    global $current_user, $wpdb, $post;
    $user_id =   $current_user->ID;
    $user_nicename =   $current_user->nicename;
    $site_url =  get_site_url();

    if ( ($_POST['upgrade']) || ($_POST['downgrade']) || ($_POST['reactivate']) ) {

        $product_id_key   = 'product_id';

        ?><p>Adjusting your plan right now, please do not exit or reload this page while update in progress ...</p><?php
        
        if ($_POST['upgrade']) {
            $product_id_value =  $_POST['upgrade'];
            $action =            ( $product_id_value != 'cancel' ) ? 'upgrade-downgrade' : 'cancel' ;
        }

        if ($_POST['downgrade']) {
            $product_id_value =  $_POST['downgrade'];
            $action =            ( $product_id_value != 'cancel' ) ? 'upgrade-downgrade' : 'cancel' ;
        }

        if ($_POST['reactivate']) {
            $product_id_value =  $_POST['reactivate'];
            $action =            'reactivate';
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
        
        $ch = curl_init($chargify_auth_url);

        $array = array();
        array_push($array, 'Content-Type: application/json;', 'Accept: application/json;', 'charset=utf-8;');
        
        /* Update, cancel or reactivate depending on users choice and status */
        switch ($action) {
            case 'cancel':
                $chargify_url = 'https://green-pages.chargify.com/subscriptions/' . $subscription_id .'.json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
                // Set budget status to cancelled and all gp_advertorial posts to 'pending'
                $budget_status_value = 'cancelled';
                $post_status_sql =     'UPDATE wp_posts 
                                        SET post_status = replace(post_status, "publish", "pending") 
                                        WHERE post_status = "publish" 
                                            AND post_type = "gp_advertorial" 
                                            AND wp_posts.post_author = "' . $user_id . '";';
                mysql_query($post_status_sql); 
                break;
            case 'reactivate':
                $chargify_url = 'https://green-pages.chargify.com/subscriptions/' . $subscription_id .'/reactivate.json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                // Set budget status to active and all gp_advertorial posts to 'publish'
                $budget_status_value = 'active';
                $post_status_sql =     'UPDATE wp_posts 
                                        SET post_status = replace(post_status, "pending", "publish") 
                                        WHERE post_status = "pending" 
                                            AND post_type = "gp_advertorial" 
                                            AND wp_posts.post_author = "' . $user_id . '";';
                mysql_query($post_status_sql); 
                break;                
            case 'upgrade-downgrade':
                $chargify_url = 'https://green-pages.chargify.com/subscriptions/' . $subscription_id .'/migrations.json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                $budget_status_value = 'active';
                break;            
        }
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $array);
        curl_setopt($ch, CURLOPT_URL, $chargify_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_USERPWD, $chargify_auth);

        $json_result = curl_exec($ch);

        $result = json_decode($json_result);

        if ($result->subscription->product->id) {
                         
            $product_handle_key      = 'product_handle';
            update_user_meta( $user_id, $product_handle_key, $result->subscription->product->handle );
            
            $adv_signup_time_key     = 'adv_signup_time';
            $adv_signup_time_value   = strtotime($result->subscription->current_period_started_at);
            update_user_meta($user_id, $adv_signup_time_key, $adv_signup_time_value );
            
            $budget_status_key       = 'budget_status';
            update_user_meta($user_id, $budget_status_key, $budget_status_value);

            $product_name            = $result->subscription->product->name; 

            if ($budget_status_value != 'cancelled') {
                update_user_meta( $user_id, $product_id_key, $product_id_value );
                $success_message = 'Your subscription has been successfully adjusted to '. $product_name.', update is now complete.';
            } else {
                $success_message = 'Your '. $product_name.' has been successfully cancelled, update is now complete. You can reactivate from
                                    your profile page at any time.';
            }
            
            ?><p><?php echo $success_message; ?></p>
            <p>Thanks for using Green Pages!</p>
            
            <?php
            echo '<p>Go back to your <href=" '.$site_url.'/profile/'.$user_nicename.' ">profile page</a></p>';
            
         
         
          
        } else {
            ?><p>Uh oh, something whet wrong processing your adjustment!</p>
            <p>Hit the back button on your browser to return to your profile page and try again.</p><?php 
        }    

    }

} else {

    ?><p>Not sure how you got here without being logged in, head back to the <a href="<?php echo $site_url; ?>">home page</a></p><?php

}
?>
