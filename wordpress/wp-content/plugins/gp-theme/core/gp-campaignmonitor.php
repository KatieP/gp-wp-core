<?php
function cm_subscribe($item = '', $subscribe = "false", $userid = NULL) {
	global $current_site, $gp, $wpdb;

	if (empty($item)) {return false;}
	if (!(bool)$subscribe)  {return false;}

	if ( is_array( $gp->campaignmonitor ) ) {
		foreach ( $gp->campaignmonitor as $key => $value ) {
			if ($key == $current_site->id) {
				$cm_api = $value['api'];
				$cm_lists = $value['lists'];
			}			
		}
	}
	
	if ( is_array( $cm_lists ) ) {
		foreach ( $cm_lists as $key => $value ) {
			if ($key == $item) {$cm_listId = $value['api'];}
		}
	}

	if (!isset($cm_listId)) {return false;}
	
	if ( is_int( $userid ) &&  !is_user_logged_in() ) {
		$user_data = get_userdata( $userid );
		$user_email = $user_data->user_email;
		$user_name = $user_data->display_name;
		$user_id = $userid;
		$user_postcode = $user_data->locale_postcode;
	}
	
	if ( is_user_logged_in() ) {
		global $current_user;
		$user_email = $current_user->user_email;
		$user_name = $current_user->display_name;
		$user_id = $current_user->ID;
		$user_postcode = $current_user->locale_postcode;
	}
	
	require_once( GP_PLUGIN_DIR . '/lib/createsend-api/csrest_subscribers.php' );
	
	$wrap = new CS_REST_Subscribers($cm_listId, $cm_api);

	if ($subscribe == "true") {
		$resubscribe = "true";
	} else {
		$resubscribe = "false";
	}
	
	$result = $wrap->add(array(
		'EmailAddress' => $user_email,
	    'Name' => $user_name,
		'State' => 'Active',
	    'CustomFields' => array(
	        array(
	            'Key' => 'Wordpress-id',
	            'Value' => $user_id
	        ),
	        array(
	            'Key' => 'postcode',
	            'Value' => $user_postcode
	        )
	    ),
	    'Resubscribe' => $resubscribe
	));
	
	if ($resubscribe == "false") {$result = $wrap->unsubscribe($user_email);}

	if($result->was_successful()) {
		return true;
	} else {
		return false;
	}
	/*** SHOULD ADD SOMETHING HERE TO MONITOR SUCCESSFUL OR UNSUCCESSFUL UPDATE RESULT ***/
}


function cm_update_current_user() {
	/*
     * Campaign Monitor
     * 
     * We are going to check if logged in user is subscribed to the mail list or not.
     * 
     * We must check the results against Wordpress $current_user->subscription["subscription-greenrazor"] value. If $subscriberGreenRazor = true then $current_user->subscription["subscription-greenrazor"] must be updated to true as well.
     * 
     * true = don't show subscribe dialog
     * false = do show subscribe dialog
     * 
     * Note1: This isn't the best way to do this. Ideally Create/Send should send the results itself. In this case there us a margin of error - if the user never visits the site or their profile update page then the value of $current_user->subscription["subscription-greenrazor"] may be incorrect.
     * Note2: There is a timing issue with this - any updates to $current_user->subscription["subscription-greenrazor"] do not take effect until next time the user visits a page.
	 */
	
	#$current_user = wp_get_current_user(); #is global variable?! should change this everywhere.
	global $current_user, $current_site, $gp, $wpdb;
	
	$subscriberGreenRazor = false;
	$list_id = 'subscription-greenrazor';
	
	if (!is_user_logged_in()) {return;}
		
	require_once( GP_PLUGIN_DIR . '/lib/createsend-api/csrest_subscribers.php' );

	if ( is_array( $gp->campaignmonitor ) ) {
		foreach ( $gp->campaignmonitor as $key => $value ) {
			if ($key == $current_site->id) {
				$cm_api = $value['api'];
				$cm_list = $value['lists'][$list_id];
			}			
		}
	}

	$wrap = new CS_REST_Subscribers($cm_list['api'], $cm_api);
	$result = $wrap->get($current_user->user_email);

	if($result->was_successful()) {
		$subscriberGreenRazor = true;
	}

	$subscription_post = $current_user->{$wpdb->prefix . 'subscription'};
	
	if ($subscription_post[$list_id] !== "true" && $subscriberGreenRazor == true) {
		if (is_array($subscription_post)) {
			if (array_key_exists($list_id, $subscription_post)) {
				$subscription_post[$list_id]='true';
			} else {
				$subscription_post = $subscription_post + array($list_id=>'true');
			}
		} else {
			$subscription_post = array($list_id=>'true');
		}
		update_usermeta($current_user->ID, $wpdb->prefix . 'subscription', $subscription_post );
	}
        
	if ($subscription_post[$list_id] === "true" && $subscriberGreenRazor == false) {
		if (is_array($subscription_post)) {
			if (array_key_exists($list_id, $subscription_post)) {
				$subscription_post[$list_id]='false';
			} else {
				$subscription_post = $subscription_post + array($list_id=>'false');
			}
		} else {
			$subscription_post = array($list_id=>'false');
		}
        update_usermeta($current_user->ID, $wpdb->prefix . 'subscription', $subscription_post );
	}
}
?>