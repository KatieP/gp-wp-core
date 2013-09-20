<?php

/**
 * User profile page display funcitons are
 * stored here for now, except for billing
 * functions which are in their own file.
 *
 * @TODO Consider separating functions for
 * each tab / panel into their own files.
 *
 */

 /* SHOW MEMBERS POSTS */
function theme_profile_posts($profile_pid, $post_page, $post_tab, $post_type) {
	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);
	
	global $wpdb, $post, $current_user, $gp;
	$geo_currentlocation = $gp->location;
	$ns_loc = $gp->location['country_iso2'] . '\\Edition';
	$edition_posttypes = $ns_loc::getPostTypes();
	
	if ( strtolower($post_type) == "directory" ) {
		theme_profile_directory($profile_pid);
		return;	
	}
	
	$post_type_filter = "";
	$post_type_key = getPostTypeID_by_Slug($post_type);
	if ( $post_type_key ) {
		$post_type_filter = "" . $wpdb->prefix . "posts.post_type = '{$post_type_key}'";
	} else {
		foreach ($edition_posttypes as $value) {
		    if ( $value['enabled'] === true ) {
			    $post_type_filter .= $wpdb->prefix . "posts.post_type = '{$value['id']}' or ";
		    }
		}
		$post_type_filter = substr($post_type_filter, 0, -4);
	}
		
	$total = "SELECT DISTINCT COUNT(*) as count 
			  FROM $wpdb->posts 
			  WHERE 
		        post_status = 'publish' and 
				(" . $post_type_filter . ")	and " . 
				$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "'";			
					
	$totalposts = $wpdb->get_results($total, OBJECT);
	$ppp = 10;
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);	
	$on_page = $post_page;

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
		
	$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.* 
				 FROM $wpdb->posts
				 WHERE 
		            post_status = 'publish' and 
					(" . $post_type_filter . ")	and " . 
					$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
				ORDER BY " . $wpdb->prefix . "posts.post_date DESC 
				LIMIT " . $ppp . " 
				OFFSET " . $offset .";";				

	$pageposts = $wpdb->get_results($querystr, OBJECT);
		
	if ( $post_type_key ) {
		foreach ($edition_posttypes as $newposttype) {
			if ( $newposttype['enabled'] === true ) {
				if ($newposttype['id'] == $post_type_key) {$post_type_name = " " . $newposttype['name'];}
			}
		}
	}
		
	if ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) || get_user_role( array('administrator') ) ) {
		echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span>{$post_type_name} Posts";
		gp_select_createpost();
		echo "</div>";
	} else {
		echo "<div class=\"total-posts\"><span>{$wp_query->found_posts}</span>{$post_type_name} Posts</div>";
	}
		
	if ($pageposts) {
		$post_author_url = get_author_posts_url($profile_author->ID);
			
		foreach ($pageposts as $post) {
			
			setup_postdata($post);
			theme_index_feed_item();
				
		}
			
		if ( $wp_query->max_num_pages > 1 ) {
			theme_tagnumpagination( $on_page, $wp_query->max_num_pages, $post_tab, $post_type );
		}
	}	
}
 
/* SHOW MEMBERS FAVOURITE POSTS */
function theme_profile_favourites($profile_pid, $post_page, $post_tab, $post_type) {

	// note: Favourites are viewable by everyone!
	
	$profile_author = get_user_by('slug', $profile_pid);

	global $wpdb, $post, $current_user, $current_site, $gp;
	$geo_currentlocation = $gp->location;
	$ns_loc = $gp->location['country_iso2'] . '\\Edition';
	$edition_posttypes = $ns_loc::getPostTypes();
	
	$post_type_filter = "";
	$post_type_key = getPostTypeID_by_Slug($post_type);
	
	if ( $post_type_key ) {
		$post_type_filter = "" . $wpdb->prefix . "posts.post_type = '{$post_type_key}'";
	} else {
	    foreach ($edition_posttypes as $value) {
		    if ( $value['enabled'] === true ) {
			    $post_type_filter .= $wpdb->prefix . "posts.post_type = '{$value['id']}' or ";
		    }
		}
		$post_type_filter = substr($post_type_filter, 0, -4);
	}

	$total = "SELECT COUNT(*) as count
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID 
				WHERE post_status='publish' 
					AND (" . $post_type_filter . ")
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1;";
				
	$totalposts = $wpdb->get_results($total, OBJECT);
	
	#$ppp = intval(get_query_var('posts_per_page'));
	$ppp = 20;
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
	#$on_page = intval(get_query_var('paged'));	
	$on_page = $post_page;

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;

	$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.*
					, m1.meta_value as _thumbnail_id 
				FROM " . $wpdb->prefix . "posts 
				LEFT JOIN " . $wpdb->prefix . "usermeta as m0 on REPLACE(m0.meta_key, 'likepost_', '')=" . $wpdb->prefix . "posts.ID 
				LEFT JOIN " . $wpdb->prefix . "postmeta as m1 on m1.post_id=" . $wpdb->prefix . "posts.ID
				WHERE post_status='publish'
					AND (" . $post_type_filter . ")   
					AND m0.meta_value > 0 
					AND m0.user_id = $profile_author->ID 
					AND m0.meta_key LIKE 'likepost%' 
					AND m1.meta_value >= 1 
				ORDER BY m0.meta_value DESC
				LIMIT " . $ppp . " 
				OFFSET " . $offset;
					
	    $pageposts = $wpdb->get_results($querystr, OBJECT);

        if ( $post_type_key ) {
            foreach ($edition_posttypes as $newposttype) {
                if ( $newposttype['enabled'] === true ) {
                    if ($newposttype['id'] == $post_type_key) {
                        if ($newposttype['plural'] === true) {
                            $post_type_name = " " . $newposttype['name'] . "s";
                        } else {
                            $post_type_name = " " . $newposttype['name'];
                        }
                    }
                }
            }
        }
                
        if ($post_type_name) {
            echo "<div class=\"total-posts\">Favourite{$post_type_name}</div>";
        } else {
            echo "<div class=\"total-posts\">Favourites</div>";
        }	    
	    
		$previous_post_title = '';
		foreach ($pageposts as $post) {
		
			setup_postdata($post);
			if ($post->post_title != $previous_post_title) {
			    theme_index_feed_item();
			    $previous_post_title = $post->post_title;
			}
		}
		
		if ( $wp_query->max_num_pages > 1 ) {
			theme_tagnumpagination( $on_page, $wp_query->max_num_pages, $post_tab, $post_type );
		}
}

function list_posts_advertiser($profile_pid) {
    /**
     * Shows title, url, post-dateof posts by this advertiser
     *
     * @TODO add hide/show post button so advertisers can manage posts
     */
	
	global $current_user, $wpdb, $post;
	$site_url = get_site_url();
	$profile_author = get_user_by('slug', $profile_pid);
	
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}
	
	$querystr = "SELECT DISTINCT " . $wpdb->prefix . "posts.*
                 FROM $wpdb->posts
                 WHERE 
                    post_status = 'publish' and 
                    post_type = 'gp_advertorial' and 
                    ".$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
                 ORDER BY " . $wpdb->prefix . "posts.post_date DESC";              

	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	echo '<table class="advertiser_table">';
	
	foreach ($pageposts as $post) {

    	setup_postdata($post);
    	if ($post->post_title != $previous_post_title) {
    	
    		$post_id = $post->ID;
    		$post_status = get_post_status($post_id);
        	
			echo '<tr>
						<td class="advertiser_table">';
        					$link = get_permalink($post->ID);               
        					echo '<a href="'. $link .'">'. $post->post_title .'</a>
        				</td>
        				
        				<!--
        				<td class="author_analytics_date">';
        				#	Active/Paused 'post_hidden', 'post_shown'
        				
        				if ($post_status == 'publish') {
        					echo 'Hide_Post';
        					#$post = array();
        					#$post = $post_id;
        					#$post['post_status'] = 'post_hidden'; //Hides post
        					#wp_update_post($post);
        					
        					
        					} elseif ($post_status == 'post_hidden') {
        					
        						echo 'Show_Post';
        						#$post = array();
        						#$post = $post_id;
        						#$post['post_status'] = 'publish'; //Shows post
        						#wp_update_post($post);
        				}
        					
        				echo '</td>
        				-->
        				
        	 	  <tr>';
    	}
	}       

	echo '</table>';
}

/* SHOW MEMBERS ADVERTISING OPTIONS */
function theme_profile_advertise($profile_pid) {

	global $current_user;
	$site_url = get_site_url();
	$profile_author = get_user_by('slug', $profile_pid);

	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}
		
	# if user IS and advertiser
	if ($profile_author->reg_advertiser == true) {
		
		$product_id = $profile_author->product_id;
	
		$product_name = get_product_name($product_id);
		
		switch ($profile_author->budget_status) {
    		case 'active': //Active client with budget remaining
    			?><h3>You are on the <?php echo $product_name; ?></h3><?php
        		
        		echo '<p>You still have some budget left this week</p>
				<p>Want more clicks? There\'s no limit on how many posts you can make, so go for it! <br />
				Create another post now.</p>
				<a href="'. $site_url .'/forms/create-product-post-subscriber/"><input type="button" value="Create Another Product Post"></a>
				<div class="clear"></div><br /><br />';
				
				echo '<h3>My Product Posts</h3>';
				
				list_posts_advertiser($profile_pid);
        		break;
        		
    		case 'used_up': //Active client with budget used up for the week
    			?><h3>You are on the <?php echo $product_name; ?></h3><?php
        		
        		echo '<p>Wow, you\'re posts are so popular that your weekly budget is already used up!<br /> 
        		This means that your product posts will not show again until your next billing cycle commences.</p>
        		
        		<p>Want more clicks? Upgrade your weekly budget now.</p>';
			
				echo '<form action="'. $site_url .'/chargify-upgrade-downgrade-handler/" method="post">
        		         '. upgrade_dropdown($product_id) . '
        		    	 <input type="submit" value="Save plan">
        		      </form>
        			  <div class="clear"></div>';
				
				echo '<h3>My Product Posts</h3>';
				list_posts_advertiser($profile_pid);
        		break;
        		
    		case 'cancelled': //Previous active client who has cancelled
    			?><h3>You were on the <?php echo $product_name; ?></h3>
    			<p>Reactivate your account now</p><br /><?php
    			echo '<h3>My Product Posts</h3>';
        		break;
		}	
	
	} else { 
		# if user IS NOT an adverters and never has been
		# Set form urls for creating ad posts for regular monthly subscription advertisers and non regular advertisers
		$post_my_product_form = ($profile_author->reg_advertiser == 1) ? '/forms/create-product-post-subscriber/' : '/advertisers/';
    	$template_url = get_bloginfo('template_url');
    
    
		echo "
		<div id=\"my-advertise\">
			<div id=\"email\">
				<span><a href=\"" . $site_url . "/advertisers\" ><input type=\"button\" value=\"Create Your First Product Promotion!\" /></a></span>
				<div class=\"clear\"></div>
				<br />
				
				<p> Greenpages offers an extremely effective kind of online advertising: You get to create your own editorials!</p>
				<p>You create the editorial post, then we send it out to the Greenpages members. You only pay for the clicks you receive in 
				cost-per-click model. No click, no payment! You can upgrade, downgrade or pause your advertiser plan at any time.</p>

				
				
				<span><a href=\"" . $site_url . "/advertisers\" target=\"_blank\">Learn more</a></span>
			</div>
		</div>
		<div class=\"clear\"></div>
		";
	}
}

/* SHOW MEMBERS DIRECTORY OPTIONS */
function theme_profile_directory($profile_pid) {
	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;
	$directory_page_url = $profile_author->directory_page_url;
	
	echo "
	<div id=\"my-directory\">
	    <br />
		<a href=\"" . $directory_page_url . "\" target=\"_blank\"><h3>View My Directory Page</h3></a>
		<a href=\"/forms/update-my-directory-page/\">
		    <h3>Update my Directory Page details here</h3>
		</a>
	</div>
	";
}

/* SHOW MEMBERS POST ANALYTICS */
function theme_profile_analytics($profile_pid) {
	global $wpdb, $post, $current_user;

	$profile_author = get_user_by('slug', $profile_pid);
	$profile_author_id = $profile_author->ID;
    $site_url = get_site_url();
	
	if ( ( ( is_user_logged_in() ) && ( $current_user->ID == $profile_author->ID ) ) || get_user_role( array('administrator') ) ) {} else {return;}

	require 'ga/analytics.class.php';
	
	$total = "SELECT COUNT(*) as count 
			FROM $wpdb->posts " . 
				$wpdb->prefix . "posts, 
				$wpdb->postmeta " . 
				$wpdb->prefix . "postmeta 
			WHERE " . $wpdb->prefix . "posts.ID = " . 
				$wpdb->prefix . "postmeta.post_id and " . 
				$wpdb->prefix . "posts.post_status = 'publish' and (" . 
					$wpdb->prefix . "posts.post_type = 'gp_news' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_events' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_advertorial' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_projects' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_competitions' or " . 
					$wpdb->prefix . "posts.post_type = 'gp_people') 
				and " . 
				$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
				$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
				$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "'";
				
	$totalposts = $wpdb->get_results($total, OBJECT);

	$ppp = intval(get_query_var('posts_per_page'));
	$wp_query->found_posts = $totalposts[0]->count;
	$wp_query->max_num_pages = ceil($wp_query->found_posts / $ppp);		
	$on_page = intval(get_query_var('paged'));	

	if($on_page == 0){ $on_page = 1; }		
	$offset = ($on_page-1) * $ppp;
	
	$querystr = "SELECT " . $wpdb->prefix . "posts.* 
				FROM $wpdb->posts " . 
					$wpdb->prefix . "posts, 
					$wpdb->postmeta " . 
					$wpdb->prefix . "postmeta 
				WHERE " . $wpdb->prefix . "posts.ID = " . 
					$wpdb->prefix . "postmeta.post_id and " . 
					$wpdb->prefix . "posts.post_status = 'publish' and (" . 
						$wpdb->prefix . "posts.post_type = 'gp_news' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_events' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_advertorial' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_projects' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_competitions' or " . 
						$wpdb->prefix . "posts.post_type = 'gp_people') 
					and " . 
					$wpdb->prefix . "postmeta.meta_key = '_thumbnail_id' and " . 
					$wpdb->prefix . "postmeta.meta_value >= 1 and " . 
					$wpdb->prefix . "posts.post_author = '" . $profile_author->ID . "' 
				ORDER BY " . $wpdb->prefix . "posts.post_date DESC";
					
	$pageposts = $wpdb->get_results($querystr, OBJECT);
	
	# Profile meta variables for getting specific analytics data
	$old_crm_id =          $profile_author->old_crm_id;
	$directory_page_url =  $profile_author->directory_page_url;
	$facebook =            $profile_author->facebook;
	$linkedin =            $profile_author->linkedin;
	$twitter =             $profile_author->twitter;
	$skype =               $profile_author->skype;
	$url =                 $profile_author->user_url;
	
	if (!$pageposts && !empty($old_crm_id) ) {
		?>
		<div id="my-analytics">
		    <br />
			<?php theme_advertorialcreate_post(); ?>
			<p>Create your first Product of the Week Advertorial to unlock your Analytics.</p>
		</div>
		<?php 
		return;
	}
	
	if (!$pageposts) {
		?>
		<div id="my-analytics"></div>
		<?php 
		return;
	}
	
	# TABLE HEADINGS FOR POST ANALYTICS
	?>
	<div id="my-analytics">
		<h2>Post Analytics</h2>			
		<table class="author_analytics">
			<tr>
				<td class="author_analytics_title">Title</td>		
				<td class="author_analytics_type">Post Type</td>
				<td class="author_analytics_cost">Value</td>
				<td class="author_analytics_date">Date Posted</td> 
				<td class="author_analytics_category_impressions">Category Impressions</td>
				<td class="author_analytics_page_impressions">Page Views</td>
				<td class="author_analytics_clicks">Clicks</td>
			</tr>
	<?php	
	
	$analytics = new analytics('greenpagesadserving@gmail.com', 'greenpages01'); //sign in and grab profile			
  	$analytics->setProfileById('ga:42443499'); 			//$analytics->setProfileByName('Stage 1 - Green Pages');
				
	if ($pageposts) {		
	 	
	    $total_sumURL = 0;
	    
		foreach ($pageposts as $post) {
			setup_postdata($post);
		
			$post_url_ext = $post->post_name; //Need to get post_name for URL. Gets ful URl, but we only need /url extention for Google API			
			$type = get_post_type($post->ID);
				
			$post_type_map = getPostTypeSlug($type);
				
			$post_url_end = '/' . $post_type_map . '/' . $post_url_ext . '/';
			#echo $post_url_end . '<br />$post_url_end<br />';				

			$post_date = get_the_time('Y-m-d'); 				//Post Date
			#echo $post_date . ' ';
			$today_date = date('Y-m-d'); 						//Todays Date
			#echo $today_date . ' ';

  			$analytics->setDateRange($post_date, $today_date); 	//Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));
				
  			#print_r($analytics->getVisitors()); 				//get array of visitors by day

  			$pageViewURL = ($analytics->getPageviewsURL($post_url_end));	//Page views for specific URL
  			#echo $pageViewURL . ' $pageViewURL';
  			#var_dump ($pageViewURL);
  			$sumURL = 0;
  			foreach ($pageViewURL as $data) {
    			$sumURL = $sumURL + $data;
    			$total_sumURL = $total_sumURL + $data;
  			}
  			#echo ' <br />*** ' . $sumURL . ' ***<br /> ';			
			
  			$pageViewType = ($analytics->getPageviewsURL('/' . $post_type_map . '/'));	//Page views for the section landing page, e.g. the news page
  			$sumType = 0;
  			foreach ($pageViewType as $data) {
      			$sumType = $sumType + $data;
  			}
  				
  			$keywords = $analytics->getData(array(
            	'dimensions' => 'ga:keyword',
           	 	'metrics' => 'ga:visits',
            	'sort' => 'ga:keyword'
            	)
          	);	
          	
          	#SET UP POST ID AND AUTHOR ID DATA, POST DATE, GET LINK CLICKS DATA FROM GA 
          	$post_date_au = get_the_time('j-m-y');
	 		$post_id = $post->ID;
	 		$click_track_tag = '/yoast-ga/' . $post_id . '/' . $profile_author_id . '/outbound-article/';
			$clickURL = ($analytics->getPageviewsURL($click_track_tag));
  			$sumClick = 0;
			foreach ($clickURL as $data) {
    			$sumClick = $sumClick + $data;
  			}
			
			switch (get_post_type()) {		# CHECK POST TYPE AND ASSIGN APPROPRIATE TITLE, URL, COST AND GET BUTTON CLICKS DATA
			   
				case 'gp_advertorial':
					$post_title = 'Products';
					$post_url = '/eco-friendly-products';
					$post_price = '$89.00';
			  		$custom = get_post_custom($post->ID);
	 				$product_url = $custom["gp_advertorial_product_url"][0];	
	 				if ( !empty($product_url) ) {		# IF 'BUY IT' BUTTON ACTIVATED, GET CLICKS
	 					$click_track_tag_product_button = '/outbound/product-button/' . $post_id . '/' . $profile_author_id . '/' . $product_url . '/'; 
  						$clickURL_product_button = ($analytics->getPageviewsURL($click_track_tag_product_button));
  						foreach ($clickURL_product_button as $data) {
    						$sumClick = $sumClick + $data;
  						}
	 				}
	 				# GET PAGE IMPRESSIONS FOR OLD PRODUCT POSTS FROM BEFORE WE CHANGED URL AND ADD TO TOTAL
				 	$old_post_url_end = '/new-stuff/' . $post_url_ext . '/';
	 				$old_PageViewURL = ($analytics->getPageviewsURL($old_post_url_end));	//Page views for specific old URL
  					foreach ($old_PageViewURL as $data) {
    					$sumURL = $sumURL + $data;
    					$total_sumURL = $total_sumURL + $data;
  					}
		       		break;
				case 'gp_competitions':
					$post_title = 'Competitions';
					$post_url = '/competitions';
					$post_price = '$250.00';
		       		break;
		   		case 'gp_events':
		   			$post_title = 'Events';
		   			$post_url = '/events';
		   			$post_price = 'N/A';
		     		break;
		     	case 'gp_news':
				   	$post_title = 'News';
		   			$post_url = '/news';
		   			$post_price = 'N/A';		   			
		     		break;
		     	case 'gp_projects':
			    	$post_title = 'Projects';
			    	$post_url = '/projects';
			    	$post_price = 'N/A';
			        break;
			}
			
		  	if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    			$sumClick = 'Unavailable';
    		}
			
			# DISPLAY ROW OF ANALYTICS DATA FOR EACH POST BY THIS AUTHOR (PAGE IMPRESSIONS ETC)
			echo '<tr>				
					<td class="author_analytics_title"><a href="' . get_permalink($post->ID) . '" title="' . 
					esc_attr(get_the_title($post->ID)) . '" rel="bookmark">' . get_the_title($post->ID) . '</a></td>				
					<td class="author_analytics_type"><a href="' . $post_url . '">' . $post_title . '</a></td>					
					<td class="author_analytics_cost">' . $post_price . '</td>				
					<td class="author_analytics_date">' . $post_date_au . '</td>
					<td class="author_analytics_category_impressions">' . $sumType . '</td>
					<td class="author_analytics_page_impressions">' . $sumURL . '</td>	
					<td class="author_analytics_clicks">' . $sumClick . '</td>								
				</tr>';
		}
	}	
	?>
		</table>			

		<p>Your posts have been viewed a total of</p> 
		<p><span class="big-number"><?php echo $total_sumURL;?></span> times!</p>	
		<p></p>
		
		
		<?php 	# DIRECTORY PAGE ANALYTICS FOR ADVERTISERS WHO HAVE (OR HAVE HAD) A DIRECTORY PAGE
		if (!empty($old_crm_id)) {
		?>
			<h2>Directory Page Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Title</td>
					<td class="author_analytics_cost">Value</td>
					<td class="author_analytics_category_impressions">Category Impressions</td>
					<td class="author_analytics_page_impressions">Page views</td>
					<td class="author_analytics_clicks">Clicks</td>
				</tr>	

				<?php 		
				# SET AND RESET SOME VARIABLES AND GET DIRECTORY PAGE DATA FROM GA
				$start_date = '2012-01-01'; 	// Click tracking of Directory Pages began just after this Date
				$today_date = date('Y-m-d'); 	// Todays Date
				
	  			$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));
	  			
				$gp_legacy = new gp_legacy();
				$results = $gp_legacy->getDirectoryPages($old_crm_id);

				$dir_sumURL = 0;
				$list_sumURL = 0;
				$totaldir_sumURL = 0;
				$totallist_sumURL = 0;
				$directory_trail = '';
				$directory_trails = '';
				
				if (array_key_exists('listing_title', $results)) {
					$listing_title = $results['listing_title'];
				}
			
				if (array_key_exists('listing_path_example', $results)) {
					$listing_path = '<a href="http://directory.thegreenpages.com.au' . $results["listing_path_example"] . '">' . $listing_title . '</a>';
				}
				
				if (array_key_exists('listing_expired', $results)) {
					if (!$results['listing_expired']) {
						$listing_expired = ' <span class="listing_status active">(Active)</span>';
					} else {
						$listing_expired = ' <span class="listing_status expired">(Expired on ' . date("d/m/Y", $results['listing_expired']) . ')</span>';
					}
				}
				
				if (array_key_exists('directory', $results)) {
					$i = 0;
					foreach ($results["directory"] as $value) {
						if ($value["directory_path"]) {
							$dir_pageViewURL = ($analytics->getPageviewsURL(urlencode($value["directory_path"])));
							$dir_sumURL = 0;
							foreach ($dir_pageViewURL as $data) {
								$dir_sumURL = $dir_sumURL + (int)$data;
							}
							$totaldir_sumURL = $totaldir_sumURL + $dir_sumURL;
	 					}
	 					
						if ($value["listing_path"]) {
							$list_pageViewURL = ($analytics->getPageviewsURL(urlencode($value["listing_path"])));
							$list_sumURL = 0;
							foreach ($list_pageViewURL as $data) {
								$list_sumURL = $list_sumURL + (int)$data;
							}
							$totallist_sumURL = $totallist_sumURL + $list_sumURL;
	 					}
	
						if (is_array($value["directory_trail"])) {
							$j = 0;
							foreach ($value["directory_trail"] as $crumb) {
								if (is_array($crumb)) {
									if (++$j > 1) {
										$directory_trail = $directory_trail . $crumb['title'] . " &gt; ";
									}
								}
							}
							if ($directory_trail != '') {
								$directory_trails = $directory_trails . '<br />&nbsp;&nbsp;<a href="http://directory.thegreenpages.com.au' . $value["directory_path"] . '">' . substr($directory_trail, 0, -6) . '</a>';
							}
							$directory_trail = '';
						}
					}
				}		
	  			
	  			# GET CLICK DATA	  			
				$click_track_tag = '/outbound/directory/' . $profile_author_id . '/';
  				$clickURL = ($analytics->getPageviewsURL($click_track_tag));
  				$sumClick = 0;
				foreach ($clickURL as $data) {
    				$sumClick = $sumClick + $data;		// Clicks for that button from all posts
	  			}
	  			
  				if ($sumClick == 0) {					#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$sumClick = 'Unavailable';
    			}
    			?>
    			<tr>
    				<td class="author_analytics_title"><?php echo $listing_path . $listing_expired . "<br /><span class=\"author-analytics-featuredin\">Featured in:</span>" . $directory_trails; ?></td>
    				<td class="author_analytics_cost">$39 per month</td>
    				<td class="author_analytics_category_impressions"><?php echo $totaldir_sumURL; ?></td>
    				<td class="author_analytics_page_impressions"><?php echo $totallist_sumURL; ?></td>
    				<td class="author_analytics_clicks"><?php echo $sumClick; ?></td>
    			</tr>
    		</table>
    		<div id="post-filter">The ability to edit your Directory Page details yourself will be ready soon! In the meantime:</div>
    		<div id="post-filter">
    			<a href="mailto:jesse.browne@thegreenpages.com.au?Subject=Please%20Update%20My%20Directory%20Page%20Details" >Update my Directory Page details here</a>
    		</div>
    		<div id="post-filter"></div>
    		<?php 		
		}	
		?>
		
		<?php   # FOR CONTRIBUTORS / CONTENT PARTNERS - DISPLAY ACTIVIST BAR / DONATE JOIN BUTTON ANALYTICS DATA
		if ( get_user_role( array('contributor') ) || get_user_role( array($rolecontributor, 'administrator') ) ) {
			
			# SET AND RESET SOME VARIABLES AND GET ACTIVIST BAR DATA FROM GA
			$start_date = '2012-01-01'; 	// Click tracking of activist buttons began just after this Date
			$today_date = date('Y-m-d'); 	// Todays Date
				
  			$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));

  			$donate_url = $profile_author->contributors_donate_url;
			$join_url = $profile_author->contributors_join_url;
			$petition_url = $profile_author->contributors_petition_url;
			$volunteer_url = $profile_author->contributors_volunteer_url;
			
  			$button_labels = array('donate' => $donate_url, 
  									'join' =>  $join_url,
  									'petition' =>  $petition_url, 
  									'volunteer' =>  $volunteer_url);
  			$activist_clicks_sum = 0;
  			  			
			?>
			<h2>Activist Bar Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Activist Buttons</td>
					<td class="author_analytics_activist">Clicks</td>	
				</tr>

				<?php #DISPLAY TABLE ROWS WITH CLICK DATA FOR ACTIVIST BAR BUTTONS
		  		foreach ($button_labels as $label => $label_url) {
		  			if (!empty($label_url)) {
  					    $click_track_tag = '/outbound/activist-' . $label .'-button/' . $profile_author_id . '/' . $label_url . '/';
					    #var_dump($click_track_tag);
  					    $clickURL = ($analytics->getPageviewsURL($click_track_tag));
  					    $sumClick = 0;
					    foreach ($clickURL as $data) {
    					    $sumClick = $sumClick + $data;							// Clicks for that button from all posts
    					    $activist_clicks_sum = $activist_clicks_sum + $data;	// Total clicks for all activist bar buttons
  					    }
  					    if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    					    $sumClick = 'Unavailable';
    				    }
  					    echo '<tr>
  					   	         <td class="author_analytics_title">' . $label . '</td>
  					       	     <td class="author_analytics_activist">' . $sumClick . '</td>
  					   	      </tr>';
		  			}
  				}
		  		if ($activist_clicks_sum == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$activist_clicks_sum = 'Unavailable';
    			}
				?>
						
			</table>
			<?php
			theme_profilecreate_post();
			if($activist_clicks_sum != 0) { 	#IF CLICKS DATA RETURNED, DISPLAY TOTAL
			?>
				<p>Your activist buttons have been clicked a total of</p> 
				<p><span class="big-number"><?php echo $activist_clicks_sum;?></span> times!</p>	
				<br />
			<?php
			}
			?>
			<div class="post-details"><a href="<?php echo $site_url; ?>/wp-admin/profile.php">Enter or update urls for Activist Bar buttons</a></div>
			<br />
			<div class="clear"></div>
			<?php 
		} 
		?>
		
		<?php # Profile page analytics if profile contact fields data present
				
		if (!empty($facebook) || !empty($linkedin) || !empty($twitter) || !empty($skype) || !empty($url)) {

		# SET AND RESET SOME VARIABLES AND GET PROFILE PAGE DATA FROM GA
		$start_date = '2012-07-01'; 	// Click tracking of profile page contact fields began just after this Date
		$today_date = date('Y-m-d'); 	// Todays Dat		
  		$analytics->setDateRange($start_date, $today_date); //Set date in GA $analytics->setMonth(date('$post_date'), date('$new_date'));			
		?>
			<h2>Profile Page Analytics</h2>
			<table class="author_analytics">
				<tr>
					<td class="author_analytics_title">Profile Page </td>
					<td class="author_analytics_activist">Clicks</td>	
				</tr>

				<?php 				  			
  				$profile_labels = array('facebook' => $facebook, 
  										'linkedin' =>  $linkedin, 
  										'twitter' =>  $twitter, 
  										'skype' =>  $skype, 
  										'website' =>  $url);
  				$profile_clicks_sum = 0;
		     
				foreach ($profile_labels as $label => $label_url) {
					if (!empty($label_url)) {
					    $click_track_tag = '/outbound/profile-' . $label .'/' . $profile_author_id .'/';
					    #var_dump($click_track_tag);
  					    $clickURL = ($analytics->getPageviewsURL($click_track_tag));
  					    $sumClick = 0;
					    foreach ($clickURL as $data) {
    					    $sumClick = $sumClick + $data;							// Clicks for that button from all posts
    					    $profile_clicks_sum = $profile_clicks_sum + $data;	// Total clicks for all activist bar buttons
  					    }
  					    if ($sumClick == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    					    $sumClick = 'Unavailable';
    				    }
    					echo '<tr>
  					    	     <td class="author_analytics_title">' . $label . '</td>
  					        	 <td class="author_analytics_activist">' . $sumClick . '</td>
  					      	  </tr>';
					}
  				}
		  		if ($profile_clicks_sum == 0) {			#IF NO CLICKS YET, DISPLAY 'Unavailable'
    				$profile_clicks_sum = 'Unavailable';
    			}
  			 ?>           
            </table>
            <?php 
            if($profile_clicks_sum != 0) { 	#IF CLICKS DATA RETURNED, DISPLAY TOTAL
				?>
				<p>Your Profile Page contact buttons have been clicked a total of</p> 
				<p><span class="big-number"><?php echo $profile_clicks_sum;?></span> times!</p>	
				<br />
				<?php
			}
			?>
			<div class="post-details"><a href="<?php echo $site_url; ?>/wp-admin/profile.php">Enter or update urls/ids for Profile Page contact buttons</a></div>
			<br />
			<div id="post-details"></div>   
        <?php     
		}
    	?>		
	</div>
<?php 
}

/* SHOW MEMBERS FOLLOWING MEMBERSHIP */
function theme_profile_following($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Following
	</div>
	";
}

/* SHOW MEMBERS TOPIC MEMBERSHIP */
function theme_profile_topics($profile_pid) {
	// note: Favourites are viewable by everyone!
	
	echo "
	<div class=\"total-posts\">
		<span>0</span> Topics
	</div>
	";	
}

?>
