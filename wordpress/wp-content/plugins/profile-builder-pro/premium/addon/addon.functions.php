<?php
// function to display an error message in the front end in case the shortcode was used but the userlisting wasn't activated
function wppb_list_all_users_display_error($atts){
	$userlistingFilterArray['addonNotActivated'] = '<p class="error">'. __('You need to activate the User-Listing feature from within the "Addons" tab!', 'profilebuilder') .'<br/>'. __('You can find it in Profile Builder\'s menu.', 'profilebuilder').'</p>';
	$userlistingFilterArray['addonNotActivated'] = apply_filters('wppb_not_addon_not_activated', $userlistingFilterArray['addonNotActivated']);
	return $userlistingFilterArray['addonNotActivated'];
}

//the function for the user-listing
function wppb_list_all_users($atts){

	$userlistingFilterArray = array();

	global $wppbFetchArray;
	global $roles;
	
	$wppbFetchArray = get_option('wppb_custom_fields');

	//get value set in the shortcode as parameter, default to "public" if not set
	extract(shortcode_atts(array('visibility' => 'public', 'roles' => '*'), $atts));
	
	//if the visibility was set to "restricted" then we need to check if the current user browsing the site/blog is logged in or not
	if ($visibility == 'restricted'){
		if ( is_user_logged_in() ) {
			$retVal = wppb_custom_userlisting_contents($roles);
			return $retVal;
			
		}elseif ( !is_user_logged_in() ) {
			$userlistingFilterArray['notLoggedIn'] = '<p class="error">'. __('You need to be logged in to view the userlisting!', 'profilebuilder') .'</p>';
			$userlistingFilterArray['notLoggedIn'] = apply_filters('wppb_not_logged_in_error_message', $userlistingFilterArray['notLoggedIn']);
			return $userlistingFilterArray['notLoggedIn'];
			
		}
	}else{
		$retVal = wppb_custom_userlisting_contents($roles);
		return $retVal;
	}
	
}

//function to return the links for the sortable headers
function wppb_get_address($criteria){
	// Concatenate the get variables to add to the page numbering string
	$queryURL = '';
	if (count($_GET)) {
		$first = true;
		foreach ($_GET as $key => $value) {
			if ($key != 'searchFor')
				if ($key != 'setSortingCriteria')
					if ($key != 'setSortingOrder') {
						if ($first){
							$param = '?';
							$first = false;
						}else
							$param = '&';
						$queryURL .= $param.$key.'='.$value;
					}
		}
	}

	if ($queryURL == '')
		$finalQueryParam = '?';
	else
		$finalQueryParam = '&';
		
	$searchFor = '';
	if ((isset($_REQUEST['searchFor'])) && (trim($_REQUEST['searchFor']) != __('Search Users by All Fields', 'profilebuilder')))
		$searchFor = '&searchFor='.trim($_REQUEST['searchFor']);
		
	
	$sortingParam = '';
	if (isset($_GET['setSortingCriteria']) && ($_GET['setSortingCriteria'] == $criteria)){
		if (isset($_GET['setSortingOrder']) && ($_GET['setSortingOrder'] == 'desc')){				
			$sortingParam .= $finalQueryParam.'setSortingCriteria='.$criteria.'&setSortingOrder=asc'.$searchFor;
			
		}elseif (isset($_GET['setSortingOrder']) && ($_GET['setSortingOrder'] == 'asc')){				
			$sortingParam .= $finalQueryParam.'setSortingCriteria='.$criteria.'&setSortingOrder=desc'.$searchFor;
		}
	}else{
			$customUserListingSettings = get_option('customUserListingSettings');
			$sortingParam .= $finalQueryParam.'setSortingCriteria='.$criteria.'&setSortingOrder='.$customUserListingSettings['sortingOrder'].$searchFor;
	}
	
	return $queryURL.$sortingParam;
}

//function to decode each sort tagname
function decode_sortTag($tagName){
	
	global $wppbFetchArray;	
	
	if ($tagName == 'extra_search_all_fields'){
		$value = __('Search Users by All Fields', 'profilebuilder');
		
		if (isset($_REQUEST['searchFor']))
			if (trim($_REQUEST['searchFor']) != $value)
				$value = trim($_REQUEST['searchFor']);
		
		$setSortingCriteria = '';	
		$setSortingOrder = '';	
		if (isset($_GET['setSortingCriteria']))
			$setSortingCriteria = '&setSortingCriteria='.$_GET['setSortingCriteria'];
		if (isset($_GET['setSortingOrder']))
			$setSortingOrder = '&setSortingOrder='.$_GET['setSortingOrder'];
	
		return '
			<form method="post" action="?page=1'.$setSortingCriteria.$setSortingOrder.'" id="userListingForm">
				<table id="searchTable">
					<tr id="searchTableRow">
						<td id="searchTableDataCell1" class="searchTableDataCell1">
							<input onfocus="if(this.value == \''.__('Search Users by All Fields', 'profilebuilder').'\'){this.value = \'\';}" type="text" onblur="if(this.value == \'\'){this.value=\''.__('Search Users by All Fields', 'profilebuilder').'\';}" id="searchAllFields" name="searchFor" title="'.__('Leave Blank and Press Search to List All Users', 'profilebuilder').'" value="'.$value.'" />
						</td>
						<td id="searchTableDataCell2" class="searchTableDataCell2">
							<input type="hidden" name="action" value="searchAllFields" />
							<input type="submit" name="searchButton" class="searchAllButton" value="' . __('Search', 'profilebuilder') .'" />
						</tr>
					</tr>
				</table>
			</form>';
	
    }elseif ($tagName == 'sort_user_name'){
		$headTitle = __('Username', 'profilebuilder');
		$headTitle = apply_filters('sort_user_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('login').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
	
	}elseif ($tagName == 'sort_first_last_name'){
		$headTitle = __('First/Lastname', 'profilebuilder');
		$headTitle = apply_filters('sort_first_last_name_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('name').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_email'){
		$headTitle = __('Email', 'profilebuilder');
		$headTitle = apply_filters('sort_email_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('email').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_registration_date'){
		$headTitle = __('Sign-up Date', 'profilebuilder');
		$headTitle = apply_filters('sort_registration_date_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('registered').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_first_name'){
		$headTitle = __('Firstname', 'profilebuilder');
		$headTitle = apply_filters('sort_first_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('firstname').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_last_name'){
		$headTitle = __('Lastname', 'profilebuilder');
		$headTitle = apply_filters('sort_last_name_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('lastname').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_display_name'){
		$headTitle = __('Display Name', 'profilebuilder');
		$headTitle = apply_filters('sort_display_name_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('nicename').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_website'){
		$headTitle = __('Website', 'profilebuilder');
		$headTitle = apply_filters('sort_website_filter', $headTitle);
		
        return '<a href="'.wppb_get_address('url').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
	
	}elseif ($tagName == 'sort_biographical_info'){
		$headTitle = __('Biographical Info', 'profilebuilder');
		$headTitle = apply_filters('sort_biographical_info_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('bio').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
		
	}elseif ($tagName == 'sort_number_of_posts'){
		$headTitle = __('Posts', 'profilebuilder');
		$headTitle = apply_filters('sort_number_of_posts_filter', $headTitle);
	
        return '<a href="'.wppb_get_address('post_count').'" id="sortLink" class="sortLink">'.$headTitle.'</a>';
	}else{
		foreach($wppbFetchArray as $key => $value)
			if ($tagName == 'sort_'.$value['item_title'])
				return '<a href="'.wppb_get_address($value['item_metaName']).'" id="sortLink" class="sortLink">'.$value['item_title'].'</a>';
	}		
}

//function to decode the meta tags
function wppb_decode_metaTag($tagName, $object){
	global $wppbFetchArray;
	
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);
	
	if ($tagName == 'extra_more_info_link'){
		$userData = get_the_author_meta( 'user_login', $object->data->ID );
		
		$more = '';
		$url = get_permalink();
		if (isset($_GET['page_id'])){
			$more = $url.'&userID='.$object->data->ID;
			$more = apply_filters ('wppb_userlisting_more_info_link_structure1', $more, $url, $object->data->ID);
		}else{
			//do we need to add an extra slash?
			$slash = '';
			if ($url[strlen($url)-1] != '/')
					$slash = '/';
			if ($userlistingFilterArray['getUserByID'] === false){
				$more = $url.$slash.'user/'.$object->data->ID;
				$more = apply_filters ('wppb_userlisting_more_info_link_structure1', $more, $url, $slash, $object->data->ID);
			}else{
				$more = $url.$slash.'user/'.$userData;
				$more = apply_filters ('wppb_userlisting_more_info_link_structure1', $more, $url, $slash, $userData);
			}
		}
		
		//return '<a href="'.$more.'" class="wppb-more"><img src="'.WPPB_PLUGIN_URL.'/assets/images/arrow_right.png" title="'. __('Click here to see more information about this user.', 'profilebuilder') .'" alt=">"></a>';
		return $userlistingFilterArray['moreLink'] = apply_filters('wppb_userlisting_more_info_link', '<span id="wppb-more-span" class="wppb-more-span"><a href="'.$more.'" class="wppb-more" id="wppb-more" title="'. __('Click here to see more information about this user', 'profilebuilder') .'" alt="'. __('More...', 'profilebuilder') .'">'. __('More...', 'profilebuilder') .'</a></span>', $more);
		
	}elseif($tagName == 'meta_user_name'){
		$userData = get_the_author_meta( 'user_login', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
	
	}elseif ($tagName == 'meta_email'){
		$userData = get_the_author_meta( 'user_email', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	}elseif ($tagName == 'meta_first_last_name'){
		$userData1 = get_the_author_meta( 'first_name', $object->data->ID );
		$userData2 = get_the_author_meta( 'last_name', $object->data->ID );
		
		if (($userData1 != '') && ($userData2 != ''))
			$userData = $userData1 .' '. $userData2;
		elseif ($userData1 == '')
			$userData = $userData2;
		elseif ($userData2 == '')
			$userData = $userData1;
		else
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_first_name'){
		$userData = get_the_author_meta( 'first_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;		
		
	}elseif ($tagName == 'meta_role'){
		if ( !isset($object->roles[0]))
			return '';
			
		return $role = ucfirst($object->roles[0]);
	
	}elseif ($tagName == 'meta_number_of_posts'){
		$args = array('author'=> $object->data->ID, 'numberposts'=> -1);
		$allPosts = get_posts($args);
		$postsNumber = count($allPosts);
			
		return '<a href="'.get_author_posts_url($object->data->ID).'" id="postNumberLink" class="postNumberLink">'.$postsNumber.'</a>';	
		
	}elseif ($tagName == 'meta_registration_date'){
		$time = '';
		for ($i=0; $i<strlen($object->data->user_registered); $i++){
			if ($object->data->user_registered[$i] == ' ')
				break;
			else
				$time .= $object->data->user_registered[$i];
		}
	
		return $time;
	
	}elseif ($tagName == 'meta_last_name'){
		$userData = get_the_author_meta( 'last_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_nickname'){
		$userData = get_the_author_meta( 'nickname', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_display_name'){
		$userData = get_the_author_meta( 'display_name', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_website'){
		$userData = get_the_author_meta( 'user_url', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_biographical_info'){
		$userData = get_the_author_meta( 'description', $object->data->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
		
	}elseif ($tagName == 'extra_avatar_or_gravatar'){
		$customUserListingSettings = get_option('customUserListingSettings','not_found');
		$avatarSize = apply_filters('wppb_userlisting_avatar_size', $customUserListingSettings['avatarSize']);		
		return $avatarImage = get_avatar($object->data->ID, $avatarSize );	
		
		
	}else{
		global $wppbFetchArray;	
	
		if (count($wppbFetchArray) >= 1){
			foreach($wppbFetchArray as $key => $value){
				if ('meta_'.$value['item_title'] == $tagName){
				
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData;
						}
						case "checkbox":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							
							if ($userData == '')
								return $userData = '-';
							
							$userDataArray = explode(',', $userData);
							$checkBoxValue = $value['item_options'];
							$newValue = str_replace(' ', '#@space@#', $checkBoxValue);  //we need to escape the spaces in the options list, because it won't save
							$checkboxValue = explode(',', $value['item_options']);
							$checkboxValue2 = explode(',', $newValue);
							$nr = count($userDataArray);
								
							$userData = '';
							
							for($i=0; $i<$nr-2; $i++)
								$userData .= $userDataArray[$i]. ', ';
								$userData .= $userDataArray[$nr-2];
							return $userData;
						}
						case "radio":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}
						case "select":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "countrySelect":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "timeZone":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "datepicker":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "textarea":{
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return nl2br($userData);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($object->data->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$object->data->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								return  $ret = __('No uploaded attachment', 'profilebuilder');
							else
								return $fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
						}
						case "avatar":{
							$customUserListingSettings = get_option('customUserListingSettings','not_found');
							$avatarSize = apply_filters('wppb_userlisting_avatar_size', $customUserListingSettings['avatarSize']);
							return $avatarImage = get_avatar($object->data->ID, $avatarSize );						
						}
					}
				}
			}
		}
	}	
}

//function to render 404 page in case a user doesn't exist
function wppb_set404(){
	global $wp_query;
	global $wpdb;
	$nrOfIDs = 0;
	
	// if admin approval is activated, then give 404 if the user was manually requested
	$wppb_generalSettings = get_option('wppb_general_settings');
	if($wppb_generalSettings['adminApproval'] == 'yes'){
		$arrayID = array();
	
		// Get term by name ''unapproved'' in user_status taxonomy.
		$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
		$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
		
		$result = mysql_query("SELECT ID FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->term_relationships AS t0 ON t1.ID = t0.object_id WHERE t0.term_taxonomy_id = $term_taxonomy_id");
		if (is_resource($result)){
			while ($row = mysql_fetch_assoc($result))
				array_push($arrayID, $row['ID']);
		}
		
		$nrOfIDs=count($arrayID);
	}
	
	
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);
	
	$invoke404 = false;
	
	//get user ID
	if (isset($_GET['userID'])){
		$userID = get_userdata($_GET['userID']);
		if (is_object($userID)){
			if ($nrOfIDs){
				if (in_array($userID->ID, $arrayID)) 
					$invoke404 = true;
			}else{
				$username = $userID->user_login;
				$user = get_userdatabylogin($username);
				if (($user === false) || ($user == null))
					$invoke404 = true;
			}
		}
	}else{
		if ($userlistingFilterArray['getUserByID'] === false){
			$userID = get_query_var( 'username' );
			if ($nrOfIDs){
				if (in_array($userID, $arrayID))
					$invoke404 = true;
			}else{
				$user = get_userdata($userID);
				if (is_object($user)){
					$username = $user->user_login;
					$user = get_userdatabylogin($username);
					if (($userID !== '') && ($user === false))
						$invoke404 = true;
				}
			}
			
		}else{
			$username = get_query_var( 'username' );
			$user = get_userdatabylogin($username);
			if (is_object($user)){
				if ($nrOfIDs){
					if (in_array($user->ID, $arrayID))
						$invoke404 = true;
				}else{
					if (($username !== '') && ($user === false))
						$invoke404 = true;
				}
			}
		}
	}
	
	if ($invoke404)
		$wp_query->set_404(); 

}
add_action('template_redirect', 'wppb_set404');

//function  to decode all the extra tags
function wppb_decode_extraTag($tagName){
	//filter to get current user by either username or id(default); get user by username?
	$userlistingFilterArray['getUserByID'] = false;
	$userlistingFilterArray['getUserByID'] = apply_filters('wppb_userlisting_get_user_by_id', $userlistingFilterArray['getUserByID']);

	//get user ID
	if (isset($_GET['userID'])){
		$user = get_userdata($_GET['userID']);
		$username = $user->user_login;
	}else{
		if ($userlistingFilterArray['getUserByID'] === false){
			$userID = get_query_var( 'username' );
			$user = get_userdata($userID);
			$username = $user->user_login;
		}else
			$username = get_query_var( 'username' );
	}
	
	$user = get_userdatabylogin($username);

	if ($user->ID == null)
		return '';
	
	if ($tagName == 'extra_go_back_link'){
		//return '<a href=\'javascript:history.go(-1)\' class="wppb-back"><img src="'.WPPB_PLUGIN_URL.'/assets/images/arrow_left.png" title="'. __('Click here to go back', 'profilebuilder') .'" alt="<"/></a>';
		return $userlistingFilterArray['backLink'] = apply_filters('wppb_userlisting_go_back_link', '<div id="wppb-back-span" class="wppb-back-span"><a href=\'javascript:history.go(-1)\' class="wppb-back" id="wppb-back" title="'. __('Click here to go back', 'profilebuilder') .'" alt="'. __('Back', 'profilebuilder') .'">'. __('Back', 'profilebuilder') .'</a></div>');

	}elseif($tagName == 'meta_user_name'){
		$userData = get_the_author_meta( 'user_login', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
	
	}elseif ($tagName == 'meta_email'){
		$userData = get_the_author_meta( 'user_email', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_first_name'){
		$userData = get_the_author_meta( 'first_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_last_name'){
		$userData = get_the_author_meta( 'last_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_nickname'){
		$userData = get_the_author_meta( 'nickname', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_display_name'){
		$userData = get_the_author_meta( 'display_name', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_website'){
		$userData = get_the_author_meta( 'user_url', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	
	}elseif ($tagName == 'meta_aim'){
		$userData = get_the_author_meta( 'aim', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;

	}elseif ($tagName == 'meta_yim'){
		$userData = get_the_author_meta( 'yim', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;

	}elseif ($tagName == 'meta_jabber'){
		$userData = get_the_author_meta( 'jabber', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
	
	}elseif ($tagName == 'meta_biographical_info'){
		$userData = get_the_author_meta( 'description', $user->ID );
		if ($userData == '')
			$userData = '-';
		return $userData;
			
	}else{
		global $wppbFetchArray;
	
		if (count($wppbFetchArray) >= 1){
			foreach($wppbFetchArray as $key => $value){
				if ('meta_'.$value['item_title'] == $tagName){
				
					switch ($value['item_type']) {
						case "input":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
								
							return $userData;
						}
						case "checkbox":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							
							if ($userData == '')
								return $userData = '-';
							
							$userDataArray = explode(',', $userData);
							$checkBoxValue = $value['item_options'];
							$newValue = str_replace(' ', '#@space@#', $checkBoxValue);  //we need to escape the spaces in the options list, because it won't save
							$checkboxValue = explode(',', $value['item_options']);
							$checkboxValue2 = explode(',', $newValue);
							$nr = count($userDataArray);
								
							$userData = '';
							
							for($i=0; $i<$nr-2; $i++)
								$userData .= $userDataArray[$i]. ', ';
								$userData .= $userDataArray[$nr-2];
							return $userData;
						}
						case "radio":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}
						case "select":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "countrySelect":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "timeZone":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "datepicker":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return $userData;
						}						
						case "textarea":{
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							if ($userData == '')
								$userData = '-';
							
							return nl2br($userData);
						}
						case "upload":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$script = WPPB_PLUGIN_URL . '/premium/functions/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);
							$fileName = str_replace ( get_bloginfo('home').'/wp-content/uploads/profile_builder/attachments/userID_'.$user->ID.'_attachment_', '', $userData );
							
							if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/attachments/'))
								return '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: </span><span class="wppb-description-delimiter2">'. __('No uploaded attachment', 'profilebuilder') .'</span>';
							else
								return '<span class="wppb-description-delimiter2"><u>'. __('Current file', 'profilebuilder') .'</u>: '.$fileName.'<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a></span>';
						}
						case "avatar":{
							$imgSource = WPPB_PLUGIN_URL . '/assets/images/';
							$userData = get_user_meta($user->ID, $value['item_metaName'], true);  // to use for the link
							$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
							
							//this checks if it only has 1 component
							if (is_numeric($value['item_options'])){
								$width = $height = $value['item_options'];
							//this checks if the entered value has 2 components
							}else{
								$sentValue = explode(',',$value['item_options']);
								$width = $sentValue[0];
								$height = $sentValue[1];
							}

							if ($userData != ''){
								if ($userData2 == ''){
									wppb_resize_avatar($user->ID);
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
									
								}
								
								$imgRelativePath = get_user_meta($user->ID, 'resized_avatar_'.$value['id'].'_relative_path', true); //get relative path
								//get image info
								$info = getimagesize($imgRelativePath);

								
								//this checks if it only has 1 component
								if (is_numeric($item_options)){
									$width = $height = $item_options;
								//this checks if the entered value has 2 components
								}else{
									$sentValue = explode(',',$item_options);
									$width = $sentValue[0];
									$height = $sentValue[1];
								}
								
								//call the avatar resize function if needed
								if (($info[0] != $width) || ($info[1] != $height)){
									wppb_resize_avatar($user->ID);
									//re-fetch user-data
									$userData2 = get_user_meta($user->ID, 'resized_avatar_'.$value['id'], true); 	//to use for the preview
								}
								
								if (($userData == '') || ($userData == get_bloginfo('url').'/wp-content/uploads/profile_builder/avatars/'))
									return $avatarImage = get_avatar($user->ID, $value['item_options'] );
								else{
									
										
									// display the resized image
									$retUserData = '<span class="avatar-border"><IMG SRC="'.$userData2.'" TITLE="'. __('Avatar', 'profilebuilder') .'" ALT="'. __('Avatar', 'profilebuilder') .'" HEIGHT='.$info[1].' WIDTH='.$info[0].'></span>';
									// display a link to the bigger image to see it clearly
									return $retUserData .= '<a href="'.$userData.'" target="_blank" class="wppb-cattachment"><img src="'.$imgSource.'attachment.png" title="'. __('Click to see the current attachment', 'profilebuilder') .'"></a>';
								}
								
							}else 
								return $avatarImage = get_avatar($user->ID, $width );						
						}
					}
					
				}elseif('meta_description_'.$value['item_title'] == $tagName){
					return $value['item_desc'];
				}
			}
		}
	}	
}

//function to parse the interWhileContent
function wppb_parse_interWhileContent($string, $object){

	$stringLength = strlen($string);
	$partialContent = '';
	$nCount = 0;
	
	while ($nCount < $stringLength){
		if (($string[$nCount] == '%') && ($string[$nCount+1] == '%')){
			$nCount = $nCount+2;
			$tagName = '';
			
			while(($string[$nCount] != '%') && ($string[$nCount+1] != '%')){
				$tagName .= $string[$nCount];
				$nCount++;
			}
			$tagName .= $string[$nCount];
			$nCount = $nCount+3;
			
			$partialContent .= wppb_decode_metaTag($tagName, $object);
		}else{
			$partialContent .= $string[$nCount];
			$nCount++;
		}
	}
	
	return $partialContent;
}

//function to handle the case when a search was requested but there were no results
function no_results_found_handler($content){

	$retContent = '';
	$formEnd = strpos( (string)$content, '</form>' );
	
	for ($i=0; $i<$formEnd+7; $i++){
		$retContent .= $content[$i];
	}	
	
	$userlistingFilterArray['noResultsFound'] = '<p class="noResults" id="noResults">'. __('No results found!', 'profilebuilder') .'</p>';
	$userlistingFilterArray['noResultsFound'] = apply_filters('wppb_no_results_found_message', $userlistingFilterArray['noResultsFound']);
	
	return $retContent.$userlistingFilterArray['noResultsFound'];
}

//the function to extract the raw html code (and more) from the back-end
function wppb_custom_userlisting_contents($allowedRoles){
	ob_start();

	$customUserListingSettings = get_option('customUserListingSettings','not_found');
	
	if ($customUserListingSettings != 'not_found'){
	
		$finalContent = '';
		$username = '';
		$username = get_query_var( 'username' );
		
		
		if (($username != '') || (isset($_GET['userID']))){
			$content = $customUserListingSettings['singleUserlisting'];
			$contentLength = strlen($content);
			
			$i = 0;
		
			while($i < $contentLength){
				if (($content[$i] == '%') && ($content[$i+1] == '%')){
					$i = $i+2;
					$tagName = '';
					
					while(($content[$i] != '%') && ($content[$i+1] != '%')){
						$tagName .= $content[$i];
						$i++;
					}
					$tagName .= $content[$i];
					$i = $i+3;
					
					$finalContent .= wppb_decode_extraTag($tagName);
				}else{
					$finalContent .= $content[$i];
					$i++;
				}
			}
			
			echo html_entity_decode($finalContent);
			
		}else{
			$content = $customUserListingSettings['allUserlisting'];
			$interWhileContent = '';
			$contentLength = strlen($content);
			$startWhileUsers = strpos( (string)$content, '%%extra_while_users%%' );
			$endWhileUsers = strpos( (string)$content, '%%extra_end_while_users%%' );
			
			/*
			if (isset($_GET['page'])){
				$pageNum = $_GET['page'];
				$pageNum = $pageNum - 1;
				
			}else 
				$pageNum = 0;
			*/
			$pageNum = get_query_var ('page');
			if ($pageNum > 0)
				$pageNum = $pageNum - 1;
				
			
			// query users
			$getUsersArg = '';
			$getUsersArg = apply_filters('wppb_userlisting_get_users_param', $getUsersArg);
			
			$args = array(
				'number' => $customUserListingSettings['sortingNumber'],
				'offset' => $pageNum*$customUserListingSettings['sortingNumber'],
				'role' => $getUsersArg,
				'search' => '',
				'fields' => 'all_with_meta'
			);
			if ( isset( $_REQUEST['setSortingCriteria'] ) )
				$args['orderby'] = $_REQUEST['setSortingCriteria'];

			if ( isset( $_REQUEST['setSortingOrder'] ) )
				$args['order'] = $_REQUEST['setSortingOrder'];
			
			// Query the user IDs for this page
			$wp_user_search = new WP_User_Query( $args );
			$thisPageOnly = $wp_user_search->get_results();
			$totalUsers = $wp_user_search->get_total();
			// end query users
			
			//start creating the pagination
			include 'pagination.class.php';
			if (($totalUsers != '0') || ($totalUsers != 0)){
				$pagination = new wppb_pagination;
				$first = __('&laquo;&laquo; First', 'profilebuilder');
				$prev = __('&laquo; Prev', 'profilebuilder');
				$next = __('Next &raquo; ', 'profilebuilder');
				$last = __('Last &raquo;&raquo;', 'profilebuilder');
				/*if (isset($_GET['page']))
					$currentPage = trim($_GET['page']);
				else
					$currentPage = '1';*/

				$currentPage = get_query_var ('page');

				if ($currentPage == 0)
					$currentPage = 1;
			}
			
			//specify results per page
			if (isset($_POST['searchFor'])){
				if ((trim($_POST['searchFor']) == __('Search Users by All Fields', 'profilebuilder')) || (trim($_POST['searchFor']) == '')){
					if (($totalUsers != '0') || ($totalUsers != 0))
						$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], '', $first, $prev, $next, $last, $currentPage); 
				}else{
					if (($totalUsers != '0') || ($totalUsers != 0))
						$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], trim($_POST['searchFor']), $first, $prev, $next, $last, $currentPage);
				}
			}elseif (isset($_GET['searchFor'])){
				if (($totalUsers != '0') || ($totalUsers != 0))
					$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], trim($_GET['searchFor']), $first, $prev, $next, $last, $currentPage);
			}else{
				if (($totalUsers != '0') || ($totalUsers != 0))
					$userInfoPages = $pagination->generate($totalUsers, $customUserListingSettings['sortingNumber'], '', $first, $prev, $next, $last, $currentPage); 
			}
			
			$i = 0;
		
			while($i < $contentLength){
				if ($startWhileUsers == $i){
					$i = $i + 21;					
					
					while ($i < $endWhileUsers){
						$interWhileContent .= $content[$i];
						$i++;
					}

					foreach ($thisPageOnly as $localKey => $localValue)
						$finalContent .= wppb_parse_interWhileContent($interWhileContent, $localValue);
					
				}elseif (($content[$i] == '%') && ($content[$i+1] == '%')){
					$i = $i+2;
					$tagName = '';
					
					while(($content[$i] != '%') && ($content[$i+1] != '%')){
						$tagName .= $content[$i];
						$i++;
					}
					$tagName .= $content[$i];
					$i = $i+3;
					
					$finalContent .= decode_sortTag($tagName);
				}else{
					$finalContent .= $content[$i];
					$i++;
				}
			}
			
			if (($totalUsers != '0') || ($totalUsers != 0))
				echo html_entity_decode($finalContent);
			else{
				$finalContent = no_results_found_handler($finalContent);
				echo html_entity_decode($finalContent);
			}
			
			if (($totalUsers != '0') || ($totalUsers != 0)){
				$pageNumbers = '<br/><div class="pageNumberDisplay" id="pageNumberDisplay" align="right">'.$pagination->links().'</div>';
				$userlistingFilterArray['userlistingTablePagination'] = apply_filters('wppb_userlisting_userlisting_table_pagination', $pageNumbers);
				echo $userlistingFilterArray['userlistingTablePagination'];
			}
			
		}
	}
	
	$output = ob_get_contents();
    ob_end_clean();
	
    return $output;
}


//function to alter the default wp query
add_action( 'pre_user_query', 'wppb_custom_user_search_query' );
function wppb_custom_user_search_query($wp_user_query) {
	global $roles;
	global $wpdb;	
	
	$wppbFetchArray = get_option('wppb_custom_fields');
	$allowedRoles = explode(',', $roles);	

	
	//filter to set search fields; search all: true - search basic fields only: false
	$userlistingFilterArray['searchALlFields'] = apply_filters('wppb_userlisting_search_all_fields', true);
	
	//search was requested
	if (isset($_REQUEST['searchFor'])){
		
		//was a valid string enterd in the search form?
		if ($_REQUEST['searchFor'] != __('Search Users by All Fields', 'profilebuilder')){
			
			//set the FROM condition
			$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";			
			$wp_user_query->query_from = "
								FROM  $wpdb->users AS t1 
								LEFT OUTER JOIN $wpdb->usermeta AS t2 ON t1.ID = t2.user_id AND t2.meta_key = 'first_name' 
								LEFT OUTER JOIN $wpdb->usermeta AS t3 ON t1.ID = t3.user_id AND t3.meta_key = 'last_name' 
								LEFT OUTER JOIN $wpdb->usermeta AS t4 ON t1.ID = t4.user_id AND t4.meta_key = 'nickname'";
			if ($userlistingFilterArray['searchALlFields'] === true){
				$wp_user_query->query_from .= " 
								LEFT OUTER JOIN $wpdb->usermeta AS t5 ON t1.ID = t5.user_id AND t5.meta_key = 'description' 
								LEFT OUTER JOIN $wpdb->usermeta AS t6 ON t1.ID = t6.user_id AND t6.meta_key = 'aim' 
								LEFT OUTER JOIN $wpdb->usermeta AS t7 ON t1.ID = t7.user_id AND t7.meta_key = 'yim' 
								LEFT OUTER JOIN $wpdb->usermeta AS t8 ON t1.ID = t8.user_id AND t8.meta_key = 'jabber' 
								LEFT OUTER JOIN $wpdb->usermeta AS t9 ON t1.ID = t9.user_id AND t9.meta_key = '".$wpdb->prefix."capabilities'";	
				
				//set the FROM condition for the custom fields
				$i = 9;
				foreach($wppbFetchArray as $key => $value)
				if ($value['item_type'] != 'heading'){
					$i++;
					$wp_user_query->query_from .= " ";
					$wp_user_query->query_from .= "LEFT OUTER JOIN $wpdb->usermeta AS t".$i." ON t1.ID = t".$i.".user_id AND t".$i.".meta_key = '".$value['item_metaName']."'";
				}
			}
			
			//set the WHERE condition
			$wp_user_query->query_where = "
										WHERE ( 
											t2.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR
											t3.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR
											t4.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%'";
			if ($userlistingFilterArray['searchALlFields'] === true){
				$wp_user_query->query_where .= " 
										OR
											t5.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR
											t6.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR
											t7.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR
											t8.meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.user_login LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.user_nicename LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.user_email LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.user_url LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.user_registered LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%' 
										OR 
											t1.display_name LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%'";

				//add WHERE conditions for the custom fields								
				$i = 9;
				foreach($wppbFetchArray as $key => $value)
					if ($value['item_type'] != 'heading'){
						$i++;
						$wp_user_query->query_where .= " ";
						$wp_user_query->query_where .= "OR t".$i.".meta_value LIKE '%".mysql_real_escape_string(trim($_REQUEST['searchFor']))."%'";
					}
			}
			
			$wp_user_query->query_where .= ")";
			//limit to certain roles only	
			if ((count($allowedRoles) > 0) && ($allowedRoles[0] != '*')){
				$wp_user_query->query_where .= " AND (";
				foreach ($allowedRoles as $thisKey => $thisValue){
					$wp_user_query->query_where .= "t9.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
					if ($thisKey < count($allowedRoles)-1)
						$wp_user_query->query_where .= " OR ";
				}
				
				$wp_user_query->query_where .= ")";	
			}		

			if ((isset($_GET['setSortingCriteria'])) && (isset($_GET['setSortingOrder']))){
				if (trim($_GET['setSortingCriteria']) == 'post_count'){
					$where = get_posts_by_author_sql('post');
					$wp_user_query->query_from .= " LEFT OUTER JOIN (SELECT post_author, COUNT(*) as post_count FROM $wpdb->posts $where GROUP BY post_author) p ON (t1.ID = p.post_author)";
					
				}elseif (trim($_GET['setSortingCriteria']) == 'firstname')
					$wp_user_query->query_orderby = "ORDER BY t2.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
					
				elseif (trim($_GET['setSortingCriteria']) == 'lastname')
					$wp_user_query->query_orderby = "ORDER BY t3.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
					
				elseif (trim($_GET['setSortingCriteria']) == 'bio')
					$wp_user_query->query_orderby = "ORDER BY t5.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
					
				else{
					$i = 9;
					foreach($wppbFetchArray as $thisKey => $thisValue)
						if ($thisValue['item_type'] != 'heading'){
							$i++;
							if (trim($_GET['setSortingCriteria']) == $thisValue['item_metaName'])
								$wp_user_query->query_orderby = "ORDER BY t".$i.".meta_value ".strtoupper(trim($_GET['setSortingOrder']));
						}
				}	
			}
			
			return $wp_user_query;
		
		//display only certain roles
		}elseif ((count($allowedRoles) > 0) && ($allowedRoles[0] != '*')){
			$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";			
				$wp_user_query->query_from = "FROM  $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->usermeta AS t9 ON t1.ID = t9.user_id AND t9.meta_key = '".$wpdb->prefix."capabilities'";	
			$wp_user_query->query_where = "WHERE";
			
			foreach ($allowedRoles as $thisKey => $thisValue){
				$wp_user_query->query_where .= " ";
				$wp_user_query->query_where .= "t9.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
				if ($thisKey < count($allowedRoles)-1)
					$wp_user_query->query_where .= " OR";
			}
		}

		return $wp_user_query;
	
	//display only certain roles if no search was requested
	}else{
		if ((isset($_GET['setSortingCriteria'])) && (isset($_GET['setSortingOrder']))){			
			if (trim($_GET['setSortingCriteria']) == 'firstname'){
				$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";		
				$wp_user_query->query_from = "FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->usermeta AS t2 ON t1.ID = t2.user_id AND t2.meta_key = 'first_name'";
				$wp_user_query->query_orderby = "ORDER BY t2.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
				
			}elseif (trim($_GET['setSortingCriteria']) == 'lastname'){
				$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";	
				$wp_user_query->query_from = "FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->usermeta AS t3 ON t1.ID = t3.user_id AND t3.meta_key = 'last_name'";	
				$wp_user_query->query_orderby = "ORDER BY t3.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
			
			}elseif (trim($_GET['setSortingCriteria']) == 'bio'){
				$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";	
				$wp_user_query->query_from = "FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->usermeta AS t5 ON t1.ID = t5.user_id AND t5.meta_key = 'description'";	
				$wp_user_query->query_orderby = "ORDER BY t5.meta_value ".strtoupper(trim($_GET['setSortingOrder']));
				
			}else{
				if ((trim($_GET['setSortingCriteria']) != 'user_login') && (trim($_GET['setSortingCriteria']) != 'login') && (trim($_GET['setSortingCriteria']) != 'user_url') && (trim($_GET['setSortingCriteria']) != 'name') && (trim($_GET['setSortingCriteria']) != 'registered') && (trim($_GET['setSortingCriteria']) != 'nicename') && (trim($_GET['setSortingCriteria']) != 'url') && (trim($_GET['setSortingCriteria']) != 'post_count')){
					$i = 9;
					foreach($wppbFetchArray as $thisKey => $thisValue)
						if ($thisValue['item_type'] != 'heading'){
							$wp_user_query->query_fields = "SQL_CALC_FOUND_ROWS t1.ID";	
							$i++;
							if (trim($_GET['setSortingCriteria']) == $thisValue['item_metaName']){
								$wp_user_query->query_from = "FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->usermeta AS t".$i." ON t1.ID = t".$i.".user_id AND t".$i.".meta_key = '".$thisValue['item_metaName']."'";	
								$wp_user_query->query_orderby = "ORDER BY t".$i.".meta_value ".strtoupper(trim($_GET['setSortingOrder']));
							}
						}
				}
			}
			
			// this if checks if the roles parameter has been set (in the case that a searching parameter has been found)
			if ((count($allowedRoles) > 0) && ($allowedRoles[0] != '*')){
				// this if checks to see if the user didn't request a sorting after WP default fields
				if ((trim($_GET['setSortingCriteria']) == 'post_count') || (trim($_GET['setSortingCriteria']) == 'login') || (trim($_GET['setSortingCriteria']) == 'name') || (trim($_GET['setSortingCriteria']) == 'registered') || (trim($_GET['setSortingCriteria']) == 'nicename') || (trim($_GET['setSortingCriteria']) == 'email') || (trim($_GET['setSortingCriteria']) == 'url')){
					$wp_user_query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id AND $wpdb->usermeta.meta_key = '".$wpdb->prefix."capabilities'";	
				
					$wp_user_query->query_where .= " AND (";
					foreach ($allowedRoles as $thisKey => $thisValue){
						$wp_user_query->query_where .= "$wpdb->usermeta.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
						if ($thisKey < count($allowedRoles)-1)
							$wp_user_query->query_where .= " OR ";
					}
					$wp_user_query->query_where .= ")";
					
				}else{
					//custom fields and the rest of the fields not handled by WP_USER_QUERY
					$wp_user_query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta AS t9 ON t1.ID = t9.user_id AND t9.meta_key = '".$wpdb->prefix."capabilities'";	
			
					$wp_user_query->query_where .= " AND (";
					foreach ($allowedRoles as $thisKey => $thisValue){
					$wp_user_query->query_where .= "t9.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
						if ($thisKey < count($allowedRoles)-1)
							$wp_user_query->query_where .= " OR ";
					}
					$wp_user_query->query_where .= ")";
				}
			}
			
		}else{
			// this if checks if the roles parameter has been set (in the case that a searching parameter wasn't found, e.g. on a fresh pageload)
			if ((count($allowedRoles) > 0) && ($allowedRoles[0] != '*')){
				$wp_user_query->query_from .= " LEFT OUTER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id AND $wpdb->usermeta.meta_key = '".$wpdb->prefix."capabilities'";	
			
				$wp_user_query->query_where .= " AND (";
				foreach ($allowedRoles as $thisKey => $thisValue){
					$wp_user_query->query_where .= "$wpdb->usermeta.meta_value LIKE '%".mysql_real_escape_string(trim($thisValue))."%'";
					if ($thisKey < count($allowedRoles)-1)
						$wp_user_query->query_where .= " OR ";
				}
				$wp_user_query->query_where .= ")";
			}
		}
		
		// ADMIN APPROVAL ADDON
		$wppb_generalSettings = get_option('wppb_general_settings');
		if($wppb_generalSettings['adminApproval'] == 'yes'){
			$arrayID = array();
		
			// Get term by name ''unapproved'' in user_status taxonomy.
			$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
			$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
			
			$result = mysql_query("SELECT ID FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->term_relationships AS t0 ON t1.ID = t0.object_id WHERE t0.term_taxonomy_id = $term_taxonomy_id");
			while ($row = mysql_fetch_assoc($result))
				array_push($arrayID, $row['ID']);
				
			$nrOfIDs=count($arrayID);
			$arrayID= implode( ',', $arrayID );
			
			if ($nrOfIDs)
				$wp_user_query->query_where .= " AND $wpdb->users.ID NOT IN ($arrayID)";
			
		}
		// END ADMIN APPROVAL
		
		return $wp_user_query;
	}
}



/* the function to display error message on the registration page */
function wppb_add_captcha_error_message(){

	$reCaptchaSettings = get_option('reCaptchaSettings', 'not_found');
	if ($reCaptchaSettings == 'not_found'){
		$publickey = ""; 
		$privatekey = "";

	}else{
		$publickey = trim($reCaptchaSettings['publicKey']); 
		$privatekey = trim($reCaptchaSettings['privateKey']);
	}
 
	// don't include this library if it was included by the reCAPTCHA Plugin already
	if (!function_exists( '_recaptcha_qsencode' )){
		require_once('recaptcha.library.php');
	}
	
 
	$resp = recaptcha_check_answer ($privatekey, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
 
	if (!empty($_POST)){
		if (!$resp->is_valid){  // What happens when the CAPTCHA was entered incorrectly
			return __("The reCAPTCHA wasn't entered correctly. Go back and try it again!", "profilebuilder");
		}else{  // Your code here to handle a successful verification
			return '';
		}
	}
 
}
$wppb_addon_settings = get_option('wppb_addon_settings');
if ($wppb_addon_settings['wppb_reCaptcha'] == 'show')
	add_filter('wppb_register_extra_error', 'wppb_add_captcha_error_message');
 
 
 
/* the function to add recaptcha to the registration form o PB */
function wppb_add_recaptcha_to_registration_form () {

	$reCaptchaSettings = get_option('reCaptchaSettings', 'not_found');
	if ($reCaptchaSettings == 'not_found'){
		$publickey = ""; 
		$privatekey = "";

	}else{
		$publickey = trim($reCaptchaSettings['publicKey']); 
		$privatekey = trim($reCaptchaSettings['privateKey']);
	}
 
	// don't include this library if it was included by the reCAPTCHA Plugin already
	if (!function_exists( '_recaptcha_qsencode' )){
		require_once('recaptcha.library.php');
	}

	return recaptcha_get_html($publickey);	
} 
?>