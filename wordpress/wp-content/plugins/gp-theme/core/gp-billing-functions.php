<?php

/**
 * 
 * Chargify related functions called from with 
 * wordpress environment and by cron jobs
 * 
 */

function get_billing_history($subscription_id,  $component_id) {

    $chargify_key =       '3FAaEvUO_ksasbblajon';
	$chargify_auth =      $chargify_key .':x';
	$chargify_auth_url =  'https://'. $chargify_auth .'green-pages.chargify.com/subscriptions/';
    $chargify_url =       'https://green-pages.chargify.com/subscriptions/' . $subscription_id . '/components/' . $component_id . '/usages.json';
    
    // Chargify api key: 3FAaEvUO_ksasbblajon
    // http://docs.chargify.com/api-authentication

    $ch = curl_init($chargify_auth_url);

    $array = array();
 
    array_push($array, 'Content-Type: application/json;', 'Accept: application/json;', 'charset=utf-8;');

    curl_setopt($ch, CURLOPT_HTTPHEADER, $array);
    curl_setopt($ch, CURLOPT_URL, $chargify_url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_USERPWD, $chargify_auth);

    $json_result = curl_exec($ch);
    $result = json_decode($json_result);
    
    curl_close($ch);
    return $result;    

}

function theme_profile_billing($profile_pid) {
    /**
     * Billing panel on profile page
     * 
     * Allows user to upgrade or downgrade cost per click advertising plans
     * and update credit card details
     * 
     * Shows some advertiser history
     * 
     * Authors: Katie Patrick & Jesse Browne
     *          kp@greenpag.es
     *          jb@greenpag.es
     **/
	
	global $current_user;

	if ( ( $current_user->reg_advertiser == '1' ) || ( get_user_role( array('administrator') ) ) ) {} else { return; }
	
	$profile_author =                  get_user_by('slug', $profile_pid);
	$profile_author_id =               $profile_author->ID;
    $site_url =                        get_site_url();
    $user_ID =                         $current_user->ID;
    $product_id =                      $profile_author->product_id;
    $subscription_id =                 $profile_author->subscription_id;
    $budget_status =                   $profile_author->budget_status;
    $advertiser_signup_time =          $profile_author->adv_signup_time;
    $chargify_self_service_page_url =  ( !empty($subscription_id) ) ? create_update_payment_url($profile_author) : '';
    $component_id = 				   get_component_id($product_id);
    
    if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}

    if (!empty($chargify_self_service_page_url)) {
        ?><a href="<?php echo $chargify_self_service_page_url; ?>" target="_blank"><h3>Update my credit card details</h3></a><?php    
    } else {
        ?>
        <h3>You currently aren't signed up to a plan with us.</h3>
        <h3><a href="<?php echo $site_url; ?>/advertisers/">Choose a plan.</a></h3>
        <p>Doesn't sound right?</p>
        <p>Send us an email at hello[at]greenpag.es and we'll get to the bottom of it.</p>
        <?php 
    }    
    
    $plan = get_product_name($product_id);

    if ( !empty($product_id) && !empty($plan) ) {
	    
        if ( $budget_status != 'cancelled' ) {
            ?><h3>You are on the <?php echo $plan; ?></h3><?php
        } else {
            ?><h3>You were on the <?php echo $plan; ?>, however your subscription is currently cancelled.</h3><?php   
        }
        upgrade_plan($product_id, $budget_status);
	
		if (!empty($component_id)) {
		
			$history = get_billing_history($subscription_id,  $component_id); 
			
			?><h3>Current Subscription History</h3><?php 
			
			$i = 0; 
			foreach ($history as $usage) {
			    $date =             substr( $usage->usage->created_at, 0, 10 );
			    $clicks =           $usage->usage->quantity;
			    $plan_cpc =         get_cost_per_click($product_id);
			    $cpc =              ( $plan_cpc != NULL ) ? (float) $plan_cpc : (float) 0.0;
			    $billable =         ( $plan_cpc != NULL ) ? ( (int) $clicks ) * $cpc : (float) 0.0;
			    $pretty_cpc =       number_format($cpc, 2);
			    $pretty_billable =  number_format($billable, 2);
			    $total_billed +=    $billable; 
    				
			    if ( $i == 2 ) { ?>
				    
				    <table class="author_analytics">
				        <tr>
					        <td>Activity Date</td>
					        <td>Clicks</td>
					        <td>Cost Per Click</td>
					        <td>Billing Amount</td>
				        </tr> <?php 
			    }
			        
    			if ($date == $prev_date) { 
    			    $sum_clicks +=          $clicks;
    			    $sum_billable =         ( $plan_cpc != NULL ) ? ( (int) $sum_clicks ) * $cpc : (float) 0.0;
    			    $sum_pretty_billable =  number_format($sum_billable, 2);
    			} else { 
                    if ( !empty($sum_clicks) ) { ?>
            			<tr>
            				<td><?php echo $prev_date; ?></td>
            				<td><?php echo $sum_clicks; ?></td>
            				<td><?php echo '$'. $pretty_cpc; ?></td>
            				<td><?php echo '$'. $sum_pretty_billable; ?></td>
            			</tr><?php
        			    $sum_clicks =           '';
        			    $sum_billable =         '';
        			    $sum_pretty_billable =  '';
                    } elseif ( !empty( $prev_date ) ) { ?>
            			<tr>
            				<td><?php echo $date; ?></td>
            				<td><?php echo $clicks; ?></td>
            				<td><?php echo '$'. $pretty_cpc; ?></td>
            				<td><?php echo '$'. $pretty_billable; ?></td>
            			</tr><?php
                    }
                }
                    
                $prev_date = $date;
                $i++;
		    }
			
		    if ($i >= 2) { ?></table><?php }
			 	
            $total_billed = number_format($total_billed, 2); ?>
            <table class="author_analytics">
                <tr>
        	        <td><strong>Total billed:</strong></td>
        			<td><?php echo '$'.$total_billed; ?></td>
        		</tr>
    		</table> <?php

		} elseif ( $product_id == '27023') { ?>
			<h3>
			    <p>Why don't you change your subscription to a cost per click plan? 
		    	You'll be able to create unlimited product posts only pay for the clicks you receive. 
				Simply choose a plan from the 'upgrade' menu above.</p>
		    <h3> <?php
		}	
		downgrade_plan($product_id, $budget_status);
	}
}

function upgrade_plan($product_id, $budget_status) {
    /**
     * Show appropriate list of advertising plans for user to upgrade to
     * Called by theme_profile_billing()
     * 
     **/
    	
    if ($product_id != '3313297') {} else { return; }
    $site_url = get_site_url();

    if ( $budget_status != 'cancelled' ) {
        ?><h3>Upgrade</h3><?php
        $name = 'upgrade'; 
    } else {
        ?><h3>Reactivate</h3><?php
        $name = 'reactivate';   
    }
    
    ?><form action="<?php echo $site_url; ?>/chargify-upgrade-downgrade-handler/" method="post"><?php

    if ( $name == 'reactivate' ) {
        switch ($product_id) {
            case '3313297':	//$499/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313297"> &nbsp&nbsp&nbsp $449/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '3313296':	//$249/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313296">  &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '27028': //$99/wk
    			echo '<select name="'. $name .'">
    			 		<option value="27028">  &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;	
    		case '27029': //$39/wk
    			echo '<select name="'. $name .'">
    		     		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;		
    		case '3313295': //$12/wk
    			echo '<select name="'. $name .'">
    			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
    		  		</select>';			
    		  	break;
    	}
		?><input type="submit" value="Confirm Reactivation">
    	</form>
    	<div class="clear"></div><?php 
		return;
    }
    
    switch ($product_id) {
		case '3313296':	//$249/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27028': //$99/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297">  &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296">  &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27029': //$39/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp  </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;		
		case '3313295': //$12/wk
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;
		case '27023': //Directory $39 / month
			echo '<select name="'. $name .'">
			 		<option value="3313297"> &nbsp&nbsp&nbsp $499/week plan &nbsp&nbsp&nbsp </option>
		  	 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
	}
	
	?><input type="submit" value="Confirm Upgrade">
	</form>
	<div class="clear"></div><?php 
}


function downgrade_plan($product_id, $budget_status) {
    /**
     * Show appropriate list of advertising plans for user to downgrade to
     * Called by theme_profile_billing()
     * 
     **/

    if ($budget_status == 'cancelled') { return; }	
    $site_url = get_site_url();
    
    ?><h3>Downgrade</h3>
    <form action="<?php echo $site_url; ?>/chargify-upgrade-downgrade-handler/" method="post">
    <?php
    
    switch ($product_id) {
		case '3313297':	//$499/wk
			echo '<select name="downgrade">
			 		<option value="3313296"> &nbsp&nbsp&nbsp $249/week plan &nbsp&nbsp&nbsp </option>
		     		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '3313296': //$249/wk
			echo '<select name="downgrade">
			 		<option value="27028"> &nbsp&nbsp&nbsp $99/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;
		 case '27028': //$99/wk
			echo '<select name="downgrade">
			 		<option value="27029"> &nbsp&nbsp&nbsp $39/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
		case '27029': //$39/wk
			echo '<select name="downgrade">
			 		<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;		
		case '3313295': //$12/wk
			echo '<select name="downgrade">
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;			
		case '27023': //Directory $39 / month
			echo '<select name="downgrade">
					<option value="3313295"> &nbsp&nbsp&nbsp $12/week plan &nbsp&nbsp&nbsp </option>
			 		<option value="cancel"> &nbsp&nbsp&nbsp Cancel Advertising &nbsp&nbsp&nbsp </option>
		  		</select>';			
		  	break;	
	}
	?><input type="submit" value="Confirm Downgrade">
	</form>
	<div class="clear"></div><?php 
}

function create_update_payment_url($profile_author) {
    /**
     *  This creates the SHA1 token at the end of the url for the update_payment, requires first 10 digits 
     *  $token = sha1 ("update_payment--3364787--OG6AQ4YsCTh2lRfEP6p3");
     *
     *  Author: Katie Patrick 
     *          kp@greenpag.es
     **/
	
	$site_key =            'OG6AQ4YsCTh2lRfEP6p3'; // Site Shared Key:
	$subscription_id =     $profile_author->subscription_id;
	$sha_input =           'update_payment--'. $subscription_id .'--'. $site_key;
	$token =               sha1($sha_input);
	$token_10 =            substr($token, 0, 10);  // returns first 10 digits of sha

	$update_payment_url =  'https://green-pages.chargify.com/update_payment/' . $subscription_id.'/'. $token_10;

	return $update_payment_url;
}

function get_product_name($product_id) {
    /**
     * Map of product_id to names of plans
     */
    
    $plan_type_map = array( "3313295"  => "$12 / week plan",
							"27029"    => "$39 / week plan",
							"27028"    => "$99 / week plan",
							"3313296"  => "$249 / week plan",
							"3313297"  => "$499 / week plan",
                            "3325582"  => "Free CPC Plan",
							"27023"    => "Directory page $39 / month plan" );
                        
    $product_name = $plan_type_map[$product_id];
    
    return $product_name;
    
}

function get_component_id($product_id) {
    /**
	 * Return component id mapped to product id
	 * for Chargify metered billing components
	 **/
    
    $component_map = array( '3313295'  => '3207',
							'27029'    => '3207',
							'27028'    => '3207',
							'3313296'  => '20016',
							'3313297'  => '20017',
                            '3325582'  => '21135',
							'27023'    => '' );
                        
    $component_id = $component_map[$product_id];

    return $component_id;
}

function get_cost_per_click($product_id) {
    /**
     * As it sounds, returns cost per click depending 
     * on which chargify subscription user is on
     */
    switch ($product_id)   {
        case '3313295':
            // $12 per week plan
            $cpc = 1.9;
            break;
        case '27029':
            // $39 per week plan
            $cpc = 1.9;
            break;
        case '27028':
            // $99 per week plan
            $cpc = 1.9;
            break; 
        case '3313296':
            // $249 per week plan
            $cpc = 1.8;
            break; 
        case '3313297':
            // $499 per week plan
            $cpc = 1.7;
            break;
        case '3325582':
            // Free CPC plan
            $cpc = NULL;
            break;                                   
    }
    return $cpc;   
}

function get_click_cap($product_id) {
    /**
     * Calculate maximum clicks available per week
     * based on which plan advertiser is signed up to.
     * 
     * Called by metered-billing-cron.php
     * 
     * Author: Jesse Browne
     *         jb@greenpag.es
     *  
     **/
    
    switch ($product_id)   {
        case '3313295':
            // $12 per week plan
            $cap = (int) (12.00 / 1.9);
            break;
        case '27029':
            // $39 per week plan
            $cap = (int) (39.00 / 1.9);
            break;
        case '27028':
            // $99 per week plan
            $cap = (int) (99.00 / 1.9);
            break; 
        case '3313296':
            // $249 per week plan
            $cap = (int) (249.00 / 1.8);
            break; 
        case '3313297':
            // $499 per week plan
            $cap = (int) (449.00 / 1.7);
            break;
        case '3325582':
            // Free cpc plan
            $cap = (int) 1000;
            break;
        case '27023':
            // $39 per month old plan
            $cap = (int) 1000;
            break;                                              
    }
    
    return $cap;
}

function get_clicks_for_post($post_row, $user_id, $analytics, $start_range, $end_range) {
    /**
     * Returns total outbound clicks from a post from Google Analytics,
     * gets product button clicks also. 
     * 
     * Called by metered-billing-cron.php and weekly-advertiser-email.php
     * 
     * @TODO Refactor the shit out of the analytics function for profile page, 
     *       this function could be called from there. 
     *       Maybe move analytics functions to separate file?
     *       
     * Authors: Initial work on analytics done by 
     *          Katie Patrick, Stephanie 'Cord' Melton &
     *          Jesse Browne
     *          jb@greenpag.es
     * 
     */
    
	$analytics->setDateRange($start_range, $end_range);	        //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));

   	#SET UP POST ID AND AUTHOR ID DATA, POST DATE, GET LINK CLICKS DATA FROM GA 
	$profile_author_id =  $user_id;
	$post_id =            $post_row->ID;
	$click_track_tag =    '/yoast-ga/' . $post_id . '/' . $profile_author_id . '/outbound-article/';

	$clickURL = ($analytics->getPageviewsURL($click_track_tag));
	$sumClick = 0;

	foreach ($clickURL as $data) {
   		$sumClick = $sumClick + $data;
	}

    // Get url product button is linked to
    $sql_product_url = 'SELECT meta_value 
                        FROM wp_postmeta 
                        WHERE post_id = "'. $post_id .'"
                            AND meta_key = "gp_advertorial_product_url";';

    $product_url_results =  mysql_query($sql_product_url);
    mysql_data_seek($product_url_results, 0);
    $product_url_row =      mysql_fetch_object($product_url_results);	
	$product_url =          $product_url_row->meta_value;

	if ( !empty($product_url) ) {		# IF 'BUY IT' BUTTON ACTIVATED, GET CLICKS
	    $click_track_tag_product_button = '/outbound/product-button/' . $post_id . '/' . $profile_author_id . '/' . $product_url . '/'; 	         
		$clickURL_product_button = ($analytics->getPageviewsURL($click_track_tag_product_button));
            
		foreach ($clickURL_product_button as $data) {
   			$sumClick = $sumClick + $data;
		}
	}
        
    return $sumClick;
}

?>