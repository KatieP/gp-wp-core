<?php

/**
 * 
 * Chargify related functions called from with 
 * wordpress environment and by cron jobs
 * 
 */

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