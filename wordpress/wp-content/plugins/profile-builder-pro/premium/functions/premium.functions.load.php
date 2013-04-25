<?php if (!defined('PROFILE_BUILDER_VERSION')) exit('No direct script access allowed');
/**
 * Functions Load
 *
 */
 
// Set up the AJAX hooks
add_action("wp_ajax_hook_wppb_delete", 'wppb_delete' );
function wppb_delete(){

	if (isset($_POST['_ajax_nonce'])){
		
		if ((isset($_POST['what'])) && ($_POST['what'] == 'avatar')){
			if (! wp_verify_nonce($_POST['_ajax_nonce'], 'user'.$_POST['currentUser'].'_nonce_avatar') ){
				echo $retVal = __('The user-validation has failed - the avatar was not deleted!', 'profilebuilder');
				die();
				
			}else{
				update_user_meta( $_POST['currentUser'], $_POST['customFieldName'], '');
				update_user_meta( $_POST['currentUser'], 'resized_avatar_'.$_POST['customFieldID'], '');
				echo 'done';
				die();
			}
		}elseif ((isset($_POST['what'])) && ($_POST['what'] == 'attachment')){
			if (! wp_verify_nonce($_POST['_ajax_nonce'], 'user'.$_POST['currentUser'].'_nonce_upload') ){
				echo $retVal = __('The user-validation has failed - the attachment was not deleted!', 'profilebuilder');
				die();
				
			}else{
				update_user_meta( $_POST['currentUser'], $_POST['customFieldName'], '');
				echo 'done';
				die();
			}
		}
	}
}


//the function to check the validity of the serial number and save a variable in the DB; purely visual
function wppb_check_serial_number($oldVal, $newVal){

	$serial_number_set = get_option('wppb_profile_builder_pro_serial','not_found');
	
	$response = wp_remote_get( 'http://cozmoslabs.com/check_serialnumber.php?serialNumberSent='.$serial_number_set );
	
	if (is_wp_error($response)){
		update_option( 'serial_number_availability', 'serverDown' ); //server down
		
	}elseif((trim($response['body']) != 'notFound') && (trim($response['body']) != 'found')){
			update_option( 'serial_number_availability', 'serverDown' );  //unknown response parameter
			update_option('wppb_profile_builder_pro_serial', '');  //reset the entered password, since the user will need to try again later
			
	}else{
		update_option( 'serial_number_availability', trim($response['body']) ); //either found or notFound
	}
	
}
add_action( 'update_option_wppb_profile_builder_pro_serial', 'wppb_check_serial_number', 10, 2 );


//the function used to overwrite the avatar across the wp installation
function wppb_changeDefaultAvatar($avatar, $id_or_email, $size, $default, $alt) {

  global $wpdb;
  
  /* Get user info. */ 
  if(is_object($id_or_email)){
	$my_user_id = $id_or_email->user_id;
  }
  elseif(is_numeric($id_or_email)){
	$my_user_id = $id_or_email; 
  }elseif(!is_integer($id_or_email)){
	$user_info = get_user_by_email($id_or_email);
	$my_user_id = $user_info->ID;
  }else  
	$my_user_id = $id_or_email; 

  $arraySettingsPresent = get_option('wppb_custom_fields','not_found');
  if ($arraySettingsPresent != 'not_found'){
	$wppbFetchArray = get_option('wppb_custom_fields');
	foreach( $wppbFetchArray as $value ){
	  if ( $value['item_type'] == 'avatar'){
		$customUserAvatar = get_user_meta($my_user_id, 'resized_avatar_'.$value['id'], true);
		if (($customUserAvatar != '') || ($customUserAvatar != null)){				
			$avatar = "<img alt='{$alt}' src='{$customUserAvatar}' class='avatar avatar-{$value['item_options']} photo avatar-default' height='{$size}' width='{$size}' />";
		}
	  }
	}
  }

  return $avatar;
}


//the function used to resize the avatar image; the new function uses a user ID as parameter to make pages load faster
function wppb_resize_avatar($userID){

	// include the admin image API
	require_once(ABSPATH . '/wp-admin/includes/image.php');
	
	
	// retrieve first a list of all the current custom fields
	$wppbFetchArray = get_option('wppb_custom_fields');
	
	foreach ( $wppbFetchArray as $key => $value){
		if ($value['item_type'] == 'avatar'){
		
			// retrieve the original image (in original size)
			$originalAvatar = get_user_meta($userID, $value['item_metaName'], true);
			
			// we need to check if this field has an image uploaded, or else we would get an error
			if ($originalAvatar != ''){
			
				// retrieve width and height of the image
				$width = $height = '';
				
				//this checks if it only has 1 component
				if (is_numeric($value['item_options'])){
					$width = $height = $value['item_options'];
				//this checks if the entered value has 2 components
				}else{
					$sentValue = explode(',',$value['item_options']);
					$width = $sentValue[0];
					$height = $sentValue[1];
				}
					
			
				// retrieve the path where exactly in the upload dir the image is : /profile_builder/avatars/userID_ID_originalAvatar_NAME.EXTENSION
				if (is_array($originalAvatar)){
					$searchOld = strpos ( (string)$originalAvatar[0], '/profile_builder/avatars/' );
					$imagePartialPath = substr($originalAvatar[0], $searchOld);
					
				}else{
					$searchOld = strpos ( (string)$originalAvatar, '/profile_builder/avatars/' );
					$imagePartialPath = substr($originalAvatar, $searchOld);
				}
					
				// get path to image to be resized
				$wpUploadPath = wp_upload_dir(); // Array of key => value pairs
				$imagePath = $wpUploadPath['basedir'].$imagePartialPath;
				
				//add a filter for the user to select crop or resizing
				$crop = true;
				$crop = apply_filters('wppb_image_crop_resize', $crop);
				
				//we need to check if the image is not, in fact, smaller then the preset values, or it will give a fatal error
				$imageSize = getimagesize($imagePath);

				
				if (($imageSize[0] > $width) && ($imageSize[1] > $heaight)){
					$thumb = image_resize($imagePath, $width, $height, $crop);
					// value to add in the usermeta as saved image
					$copyFrom = strpos( (string)$thumb, '/profile_builder/' );
					$newImagePartial = substr($thumb, $copyFrom);

				}else{
					// value to add in the usermeta as saved image
					$copyFrom = strpos( (string)$imagePath, '/profile_builder/' );
					$newImagePartial = substr($imagePath, $copyFrom);
				}
				
				$newImage1 = $wpUploadPath['baseurl'].$newImagePartial;
				$newImage2 = $wpUploadPath['basedir'].$newImagePartial;
				
				// this if can be done using the built-in filter of wp_upload_dir if needed
				if (PHP_OS == "WIN32" || PHP_OS == "WINNT")
					$newImage2 = str_replace('\\', '/', $newImage2);

				update_user_meta( $userID, 'resized_avatar_'.$value['id'], $newImage1);
				update_user_meta( $userID, 'resized_avatar_'.$value['id'].'_relative_path', $newImage2);
			}
		}
	}
}



if ( is_admin() ){
	// add a hook to delete the user from the _signups table if either the email confirmation is activated, or it is a wpmu installation
	function wppb_delete_user_from_signups_table($user_id) {
		global $wpdb;

		$userLogin = $wpdb->get_var("SELECT user_login, user_email FROM " . $wpdb->users . " WHERE ID = '" . $user_id . "' LIMIT 1");
		if ( is_multisite() )
			$delete = $wpdb->query("DELETE FROM ".$wpdb->signups ." WHERE user_login = '" .$userLogin ."'");
		else
			$delete = $wpdb->query("DELETE FROM " . $wpdb->prefix . "signups WHERE user_login = '" .$userLogin ."'");
	}
	
	if (is_multisite())
		add_action( 'wpmu_delete_user', 'wppb_delete_user_from_signups_table');
	else{
		$wppb_generalSettings = get_option('wppb_general_settings');
				
		if ($wppb_generalSettings['emailConfirmation'] == 'yes')
			add_action( 'delete_user', 'wppb_delete_user_from_signups_table');
	}

}else{
	//check if the plugin has the addons module
	$addonPresent = WPPB_PLUGIN_DIR . '/premium/addon/addon.php';
	if (file_exists($addonPresent)){
		//check to see if the redirecting addon is present and activated
		$wppb_addon_settings = get_option('wppb_addon_settings'); //fetch the descriptions array
		if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
			
			//get the currently loaded page
			global $pagenow;

			//the part for the WP register page
			if (($pagenow == 'wp-login.php') && (isset($_GET['action'])) && ($_GET['action'] == 'register')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['registerRedirect'] == 'yes') && (trim($customRedirectSettings['registerRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['registerRedirectTarget']);
						$findHttp = strpos( (string)$redirectLink, 'http' );
						
						if ($findHttp === false)
							wp_redirect( 'http://'.$redirectLink );
						else wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for the WP password recovery
			}elseif (($pagenow == 'wp-login.php') && (isset($_GET['action'])) && ($_GET['action'] == 'lostpassword')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['recoverRedirect'] == 'yes') && (trim($customRedirectSettings['recoverRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['recoverRedirectTarget']);
						$findHttp = strpos( (string)$redirectLink, 'http');
						
						if ($findHttp === false)
							wp_redirect( 'http://'.$redirectLink );
						else wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for WP login; BEFORE login; this part only covers when the user isn't logged in and NOT when he just logged out
			}elseif ((($pagenow == 'wp-login.php') && (!isset($_GET['action'])) && (!isset($_GET['loggedout']))) || (isset($_GET['redirect_to']) && ($_GET['action'] != 'logout'))){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['loginRedirect'] == 'yes') && (trim($customRedirectSettings['loginRedirectTarget']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['loginRedirectTarget']);
						$findHttp = strpos( (string)$redirectLink, 'http' );
						
						if ($findHttp === false)
							wp_redirect( 'http://'.$redirectLink );
						else wp_redirect( $redirectLink );
						
						exit;
					}
				}
			//the part for WP login; AFTER logout; this part only covers when the user was logged in and has logged out
			}elseif (($pagenow == 'wp-login.php') && (isset($_GET['loggedout'])) && ($_GET['loggedout'] == 'true')){
				$customRedirectSettings = get_option('customRedirectSettings','not_found');
				
				if ($customRedirectSettings != 'not_found'){
					if (($customRedirectSettings['loginRedirectLogout'] == 'yes') && (trim($customRedirectSettings['loginRedirectTargetLogout']) != '')){
						include ('wp-includes/pluggable.php');
						
						$redirectLink = trim($customRedirectSettings['loginRedirectTargetLogout']);					
						$findHttp = strpos( (string)$redirectLink, 'http' );
						
						if ($findHttp === false)
							wp_redirect( 'http://'.$redirectLink );
						else wp_redirect( $redirectLink );
						
						exit;
					}
				}
				
			}
		}
	}
}
	

//the function needed to block access to the admin-panel (if requisted)
function wppb_restrict_dashboard_access(){

	$capabilities = apply_filters('wppb_redirect_capability', 'manage_options');
	
	if (!is_admin())
        return '';

	elseif ((is_admin()) && (!current_user_can( $capabilities ))){
			//check to see if the redirecting addon is present and activated
			$wppb_addon_settings = get_option('wppb_addon_settings');
			if ($wppb_addon_settings['wppb_customRedirect'] == 'show'){
		
			$customRedirectSettings = get_option('customRedirectSettings','not_found');
			if ($customRedirectSettings != 'not_found'){
				if (($customRedirectSettings['dashboardRedirect'] == 'yes') && (trim($customRedirectSettings['dashboardRedirectTarget']) != '')){
				
					$redirectLink = trim($customRedirectSettings['dashboardRedirectTarget']);
					$findHttp = strpos( (string)$redirectLink, 'http' );
					
					if ($findHttp === false)
						$redirectLink = 'http://'.$redirectLink;
					
					wp_redirect( $redirectLink );
					exit;

				}
			}
		}
	}
}
add_action('admin_init','wppb_restrict_dashboard_access');



/**
 * Registers the css to the datepicker on the front-end
 *
 */
function wppb_register_datepicker_styles() {

	$myStyleUrl = WPPB_PLUGIN_URL.'/premium/assets/css/ui-lightness/jquery-ui-1.8.14.custom.css';
	wp_register_style('wppb_jqueryStyleSheet', $myStyleUrl);
}
add_action('init', 'wppb_register_datepicker_styles');

/**
 * Add the css to the datepicker on the front-end
 *
 * @uses $wppb_shortcode_on_front global. Used to check if the shortcode is present on the page.
 * $wppb_shortcode_on_front global is set to true in wppb_front_end_profile_info() and wppb_front_end_register()
 */
function wppb_add_datepicker_styles() {
	global  $wppb_shortcode_on_front;
	
	if( $wppb_shortcode_on_front == true ){
		wp_print_styles( 'wppb_jqueryStyleSheet' );
	}
}
add_action('wp_footer', 'wppb_add_datepicker_styles');

/**
 * Registers the datepicker js to the fontend and wppb_init js
 *
 */
function wppb_register_datepicker_script() {

	wp_register_script( 'wppb_jqueryDatepicker2', WPPB_PLUGIN_URL.'/premium/assets/js/jquery-ui-datepicker.min.js', array( 'jquery', 'jquery-ui-core' ) );
	wp_register_script( 'wppb_init', WPPB_PLUGIN_URL.'/premium/assets/js/wppb_init.js', array( 'wppb_jqueryDatepicker2' ) );
}    
add_action('init', 'wppb_register_datepicker_script');

/**
 * Add the datepicker to the fontend and wppb_init 
 *
 * @uses $wppb_shortcode_on_front global. Used to check if the shortcode is present on the page..
 * $wppb_shortcode_on_front global is set to true in wppb_front_end_profile_info() and wppb_front_end_register()
 */
function wppb_add_datepicker_script() {

	global  $wppb_shortcode_on_front;
	
	if( $wppb_shortcode_on_front == true ){
		wp_print_scripts( 'wppb_jqueryDatepicker2' );
		wp_print_scripts( 'wppb_init' );
	}
}    
add_action('wp_footer', 'wppb_add_datepicker_script');


add_action( 'admin_print_styles-profile.php', 'wppb_add_datepicker_styles_admin_panel');
add_action( 'admin_print_styles-user-edit.php', 'wppb_add_datepicker_styles_admin_panel');

/* function to add the css to the datepicker on the admin side */
function wppb_add_datepicker_styles_admin_panel(  ) {
	
		$myStyleUrl = WPPB_PLUGIN_URL.'/premium/assets/css/ui-lightness/jquery-ui-1.8.14.custom.css';
		wp_register_style('wppb_admin_jqueryStyleSheet', $myStyleUrl);
		wp_enqueue_style( 'wppb_admin_jqueryStyleSheet');
	
}


/* add the dateformat as a variable for more personalization possibilities */
add_action('wp_head','wppb_add_datepicker_dateformat');
function wppb_add_datepicker_dateformat(){

	$dateFormat = apply_filters('wppb_datepicker_format', 'mm/dd/yy');
?>
	<script type="text/javascript">
		var dateFormatVar = "<?php echo $dateFormat = apply_filters('wppb_datepicker_format', 'mm/dd/yy'); ?>";
	</script>
<?php
}


/* function to add the jquery for the datepicker on the admin side */
add_action( 'admin_enqueue_scripts', 'wppb_add_datepicker_script_admin_panel');
function wppb_add_datepicker_script_admin_panel( $hook ) {

	if(( $hook == 'profile.php' ) || ($hook == 'user-edit.php')){
	
?>
			<script type="text/javascript">
				var dateFormatVar = "<?php echo $dateFormat = apply_filters('wppb_datepicker_format', 'mm/dd/yy'); ?>";
			</script>
<?php
	
		wp_enqueue_script('jquery-ui-core');
		
		wp_register_script( 'wppb_admin_jqueryDatepicker2', WPPB_PLUGIN_URL.'/premium/assets/js/jquery-ui-datepicker.min.js');
		wp_enqueue_script( 'wppb_admin_jqueryDatepicker2' );
	}   
}    

// Add the rewrite rule
add_action( 'init', 'wppb_rrr_add_rules' );
function wppb_rrr_add_rules() {

	add_rewrite_rule( '([^/]*)/user/([^/]+)','index.php?pagename=$matches[1]&username=$matches[2]', 'top' );
	//add_rewrite_rule( '([^/]*)/user//?$','index.php?pagename=$matches[1]', 'top' );
}

// Add the store_id var so that WP recognizes it
add_filter( 'query_vars', 'wppb_rrr_add_query_var' );
function wppb_rrr_add_query_var( $vars ) {
	$vars[] = 'username';
	return $vars;
}

// Enqueue the userlisting javascript only on the needed page
function wppb_enqueue_userlisting_script($hook){

	if( $hook == 'users_page_ProfileBuilderOptionsAndSettings' )
		wp_enqueue_script('userlisting_script_handlder', WPPB_PLUGIN_URL.'/premium/assets/js/userlisting.scripts.js', '', PROFILE_BUILDER_VERSION);
}
add_action('admin_enqueue_scripts', 'wppb_enqueue_userlisting_script');

// This function offers compatibility with the all in one event calendar plugin
function wppb_aioec_compatibility(){

	wp_deregister_script( 'jquery.tools-form');
}
add_action('admin_print_styles-users_page_ProfileBuilderOptionsAndSettings', 'wppb_aioec_compatibility');

//functions needed for the userlisting on single-sites
function wppb_signup_schema($oldVal, $newVal){

	// Declare these as global in case schema.php is included from a function.
	global $wpdb, $wp_queries, $charset_collate;

	if ($newVal['emailConfirmation'] == 'yes'){
		/**
		 * The database character collate.
		 * @var string
		 * @global string
		 * @name $charset_collate
		 */
		$charset_collate = '';
		
		if ( ! empty( $wpdb->charset ) )
			$charset_collate = "DEFAULT CHARACTER SET ".$wpdb->charset;
		if ( ! empty( $wpdb->collate ) )
			$charset_collate .= " COLLATE ".$wpdb->collate;
		$tableName = $wpdb->prefix.'signups';

		$sql = "
			CREATE TABLE $tableName (
				  domain varchar(200) NOT NULL default '',
				  path varchar(100) NOT NULL default '',
				  title longtext NOT NULL,
				  user_login varchar(60) NOT NULL default '',
				  user_email varchar(100) NOT NULL default '',
				  registered datetime NOT NULL default '0000-00-00 00:00:00',
				  activated datetime NOT NULL default '0000-00-00 00:00:00',
				  active tinyint(1) NOT NULL default '0',
				  activation_key varchar(50) NOT NULL default '',
				  meta longtext,
				  KEY activation_key (activation_key),
				  KEY domain (domain)
			) $charset_collate;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$res = dbDelta($sql);
	}
}
add_action( 'update_option_wppb_general_settings', 'wppb_signup_schema', 10, 2 );


// ADMIN APPROVAL FEATURE //
function wppb_add_header_script(){
?>
	<script type="text/javascript">	
		// script to add an extra link to the users page listing the unapproved users
			jQuery(document).ready(function() {
				jQuery.post( ajaxurl ,  { action:"wppb_get_approved_unapproved_number"}, function(response) {
					jQuery('.wrap ul.subsubsub').append('| <li class="listUnapprovedUsers"><a class="UnapprovedUsersLink" href="?user_status=unapproved">Unapproved</a> <font id="unapprovedUserNo" color="grey">('+response.number1+')</font></li>');
					jQuery('.wrap ul.subsubsub').append(' | <li class="listApprovedUsers"><a class="ApprovedUsersLink" href="?user_status=approved">Approved</a> <font id="approvedUserNo" color="grey">('+response.number2+')</font></li>');
						
				});			
			});
	
		// script to create a confirmation box for the user upon approving/unapproving a user
		function confirmChangeUserStatus(nonceField, currentUser, returnTo, ajaxurl, actionText) {
			test = jQuery('#approveId'+currentUser).text();		
			
			actionText = '<?php _e('Do you want to', 'profilebuilder');?>'+' '+ test.toLowerCase() +' '+'<?php _e('the current user?', 'profilebuilder');?>';
		
			if (confirm(actionText)) {
				jQuery.post( ajaxurl ,  { action:"wppb_change_user_status", currentUser:currentUser, actionText:actionText, _ajax_nonce:nonceField}, function(response) {

					if(response.error!="error"){
						jQuery('#approveId'+currentUser).text(response.string1);
						jQuery('#spanUserID'+currentUser).text(response.string2);
						jQuery('#unapprovedUserNo').text('('+response.number1+')');
						jQuery('#approvedUserNo').text('('+response.number2+')');
						
					}
				});			
			}
		}
	</script>	
<?php
}
function wppb_send_new_user_status_email($userID, $newStatus){

	$user_info = get_userdata($userID);

	$bloginfo = get_bloginfo( 'name' );	
	
	$userMessageFromNewUserStatus = $bloginfo;
	$userMessageFromNewUserStatus = apply_filters('wppb_new_user_status_from_email_content', $userMessageFromNewUserStatus, $bloginfo, $userID, $newStatus);
	
	if ($newStatus == 'approved'){
		$userMessageSubjectNewUserStatus = __('Your account on', 'profilebuilder') .' '. $bloginfo .' '. __('has been approved!', 'profilebuilder');
		$userMessageContentNewUserStatus = __('An administrator has just approved your account on', 'profilebuilder') .' '. $bloginfo .' ('.$user_info->user_login.').';
		
	}elseif ($newStatus == 'unapproved'){
		$userMessageSubjectNewUserStatus = __('Your account on', 'profilebuilder') .' '. $bloginfo .' '. __('has been unapproved!', 'profilebuilder');
		$userMessageContentNewUserStatus = __('An administrator has just unapproved your account on', 'profilebuilder') .' '. $bloginfo .' ('.$user_info->user_login.').';	
	}
	
	$userMessageSubjectNewUserStatus = apply_filters('wppb_new_user_status_subject_email_content', $userMessageSubjectNewUserStatus, $userMessageFromNewUserStatus, $newStatus, $userID, $bloginfo);
	$userMessageContentNewUserStatus = apply_filters('wppb_new_user_status_body_content', $userMessageContentNewUserStatus, $userMessageFromNewUserStatus, $user_name, $passw1);
	
	wp_mail( $user_info->user_email , $userMessageSubjectNewUserStatus, $userMessageContentNewUserStatus);
}


function wppb_calculate_approved_unapproved_user_ratio(){

	global $wpdb;
	$arrayID = array();
	$retVal = array();

	// Get term by name ''unapproved'' in user_status taxonomy.
	$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
	$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
	
	$result = mysql_query("SELECT ID FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->term_relationships AS t2 ON t1.ID = t2.object_id WHERE t2.term_taxonomy_id = $term_taxonomy_id");
	if ($result == false)
		$retVal[0] = 0;
	else
		$retVal[0] = mysql_num_rows($result);

	$nrOfUsers = count_users();
	$retVal[1] = $nrOfUsers['total_users'] - $retVal[0];

	return $retVal;
}

function wppb_get_approved_unapproved_number(){
	
	$retVal = wppb_calculate_approved_unapproved_user_ratio();
	
	header( 'Content-type: application/json' );
	die( json_encode( array( 'number1' => $retVal[0], 'number2' => $retVal[1] ) ) );
}

function wppb_change_user_status(){

	global $wpdb;
	$arrayID = array();
	$string1 = '';
	$string2 = '';
	$number1 = '';
	$number2 = '';
	$error = '';

	if (isset($_POST['currentUser'])){
		$user_id = trim($_POST['currentUser']);
		
			if (isset($_POST['_ajax_nonce'])){
				$nonce = $_POST['_ajax_nonce'];
				
				if (! wp_verify_nonce($nonce, '_nonce_'.$user_id .'_') )
					$error = 'error';
					
				elseif (!wp_get_object_terms( $user_id, 'user_status' )){
					// the object was empty ->the user is currently approved. So the next action which was initiated was an unapprove
					wp_set_object_terms( $user_id, array( 'unapproved' ), 'user_status', false);
					clean_object_term_cache( $user_id, 'user_status' );
					
					$string1 = __('Approve', 'profilebuilder');
					$string2 = __('Unapproved', 'profilebuilder');
					
					$retVal = wppb_calculate_approved_unapproved_user_ratio();
					
					wppb_send_new_user_status_email($user_id, 'unapproved');
					
				}else{
					// the object was not empty ->the user is currently unapproved. So the next action which was initiated was an approve
					wp_set_object_terms( $user_id, NULL, 'user_status' );
					clean_object_term_cache( $user_id, 'user_status' );
					
					$string1 = __('Unapprove', 'profilebuilder');
					$string2 = __('Approved', 'profilebuilder');
					
					$retVal = wppb_calculate_approved_unapproved_user_ratio();
					
					wppb_send_new_user_status_email($user_id, 'approved');
					
				}	
			}else
				$error = 'error';
		
			
	}else
		$error = 'error';

	
	header( 'Content-type: application/json' );
	die( json_encode( array( 'string1' => $string1, 'string2' => $string2, 'number1' => $retVal[0], 'number2' => $retVal[1], 'error' => $error ) ) );
	
}

// Function(s) to add a new option for each user (Approve/Unapprove) besides the existing Edit | Delete options
function wppb_add_new_option($userID, $actions){

	//add the nonce field
	$wppb_nonce = wp_create_nonce( '_nonce_'.$userID.'_');
	
	
	if (!wp_get_object_terms( $userID, 'user_status' )){ // the object was empty ->the user is currently approved.
		$action = __('unapprove', 'profilebuilder');
		$text = __('Unapprove', 'profilebuilder');
		
	}else{
		$action = __('approve', 'profilebuilder');
		$text = __('Approve', 'profilebuilder');
	}
		
	$final = __('Are you sure you want to', 'profilebuilder') .' '. $action .' '. __('this user?', 'profilebuilder');
	$final = apply_filters('wppb_change_user_status_notification_message', $final);
	
	$actions['user_status'] = '<a id="approveId'.$userID.'" href="javascript:confirmChangeUserStatus(\''.$wppb_nonce.'\',\''.$userID.'\',\''.wppb_curpageurl().'\',\''.get_bloginfo('url').'/wp-admin/admin-ajax.php\',\''.$final.'\')">'.$text.'</a>';

	return $actions;
		
}	

// function 1 to add a new option for each user in case the admin approval feature is activated
function wppb_add_new_option_handler($actions, $user_object){

	if ( !is_multisite() && get_current_user_id() != $user_object->ID && current_user_can( 'delete_user', $user_object->ID ) ){
		$actions = wppb_add_new_option($user_object->ID, $actions);
		
	}elseif ( current_user_can( 'delete_user', $user_object->ID )){
		$super_admins = get_super_admins();
		
		if (! in_array( $user_object->user_login, $super_admins )){
			$actions = wppb_add_new_option($user_object->ID, $actions);
		}
	}
	
	return $actions;
}

function wppb_add_activate_status_column( $column ) {
    $column['user_status'] = 'User-status';
	
    return $column;
}
 
// function to populate user status in the new custom-created column
function wppb_add_activate_status_column_content( $val, $column_name, $user_id ) {
    $user = get_userdata( $user_id );
 
    switch ($column_name) {
        case 'user_status' :
			if (!wp_get_object_terms( $user_id, 'user_status' ))
				return '<span id="spanUserID'.$user_id.'">'.__('Approved', 'profilebuilder').'</span>';
				
			else
				return '<span id="spanUserID'.$user_id.'">'.__('Unapproved', 'profilebuilder').'</span>';

            break;
        default:
    }
 
    return $return;
}

// function to register the new user_status taxonomy for the admin approval
function wppb_register_user_status_taxonomy() {

	register_taxonomy('user_status','user',array('public' => false));
}

// function to create a new wp error in case the admin approval feature is active and the given user is still unapproved
function wppb_unapproved_user_admin_error_message_handler($userdata, $password){

	if (wp_get_object_terms( $userdata->ID, 'user_status' )){
		$errorMessage = __('<strong>ERROR</strong>: Your account has to be confirmed by an administrator before you can log in.', 'profilebuilder');
	
		return new WP_Error('wppb_unapproved_user_admin_error_message', $errorMessage);
	}else
	
		return $userdata;
}

// function to prohibit user from using the default wp password recovery feature
function wppb_unapproved_user_password_reovery($allow, $userID){

	if (wp_get_object_terms( $userID, 'user_status' ))
		return new WP_Error('wppb_no_password_reset', __('Your account has to be confirmed by an administrator before you can use the "Password Reset" feature.', 'profilebuilder'));
	else
		return true;
}

// function to add the "unapproved" status for the user who just registered using the WP registration form (only if the admin approval feature is active)
function wppb_update_user_status_on_admin_registration($user_id){

	wp_set_object_terms( $user_id, array( 'unapproved' ), 'user_status', false);
	clean_object_term_cache( $user_id, 'user_status' );	
}


//function to alter the default wp query
function wppb_custom_user_search_user_status_query($wp_user_query) {
	global $wpdb;

	$arrayID = array();
	
	// Get term by name ''unapproved'' in user_status taxonomy.
	$user_statusTaxID = get_term_by('name', 'unapproved', 'user_status');
	$term_taxonomy_id = $user_statusTaxID->term_taxonomy_id;
	
	$result = mysql_query("SELECT ID FROM $wpdb->users AS t1 LEFT OUTER JOIN $wpdb->term_relationships AS t2 ON t1.ID = t2.object_id WHERE t2.term_taxonomy_id = $term_taxonomy_id");
	while ($row = mysql_fetch_assoc($result))
		array_push($arrayID, $row['ID']);
		
	$nrOfIDs=count($arrayID);
	$arrayID= implode( ',', $arrayID );
	
	if ($_GET['user_status'] == 'unapproved')
		$wp_user_query->query_where .= " AND $wpdb->users.ID IN ($arrayID)";
	else{
		if ($nrOfIDs)
			$wp_user_query->query_where .= " AND $wpdb->users.ID NOT IN ($arrayID)";
	}

	return $wp_user_query;
}
	
// Set up the AJAX hooks
add_action( 'wp_ajax_wppb_change_user_status', 'wppb_change_user_status' );	
add_action( 'wp_ajax_wppb_get_approved_unapproved_number', 'wppb_get_approved_unapproved_number' );	
	
$wppb_generalSettings = get_option('wppb_general_settings');
if($wppb_generalSettings['adminApproval'] == 'yes'){
	
	
	add_filter( 'manage_users_custom_column', 'wppb_add_activate_status_column_content', 10, 3 );
	add_action( 'init', 'wppb_register_user_status_taxonomy', 1 );
	add_filter( 'wp_authenticate_user', 'wppb_unapproved_user_admin_error_message_handler', 10, 2 );
	add_filter( 'allow_password_reset', 'wppb_unapproved_user_password_reovery', 10, 2 );
	add_filter( 'manage_users_columns', 'wppb_add_activate_status_column' );
	add_filter( 'wpmu_users_columns', 'wppb_add_activate_status_column' );
	add_filter( 'ms_user_row_actions', 'wppb_add_new_option_handler',10, 2 );
	add_filter( 'user_row_actions', 'wppb_add_new_option_handler',10, 2 );
	add_action( 'admin_head', 'wppb_add_header_script' );
	add_action( 'user_register', 'wppb_update_user_status_on_admin_registration' );

	if (isset($_GET['user_status']))
		add_action( 'pre_user_query', 'wppb_custom_user_search_user_status_query' );
}
// END ADMIN APPROVAL FEATURE









// WAY OF ADDING LOGIN IN THE WIDGET AREA
add_action( 'widgets_init', 'wppb_register_login_widget' );

function wppb_register_login_widget() {
	register_widget( 'wppb_login_widget' );
}

class wppb_login_widget extends WP_Widget {

	function wppb_login_widget() {
		$widget_ops = array( 'classname' => 'login', 'description' => __('This login widget lets you add a login form in the sidebar.', 'profilebuilder') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'wppb-login-widget' );
		
		do_action( 'wppb_login_widget_settings', $widget_ops, $control_ops);
		
		$this->WP_Widget( 'wppb-login-widget', __('Profile Builder Login Widget', 'profilebuilder'), $widget_ops, $control_ops );
		
	}

	function widget( $args, $instance ) {
		extract( $args );

		$title = apply_filters('wppb_login_widget_title', $instance['title'] );
		$redirect = trim($instance['redirect']);
		$register = trim($instance['register']);
		$lostpass = trim($instance['lostpass']);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo do_shortcode('[wppb-login display="false" redirect="'.$redirect.'" submit="widget"]');
		

		if ( $register ){
			/* Load registration file. */
			require_once( ABSPATH . WPINC . '/registration.php' );

			/* Check if users can register. */
			$registration = get_option( 'users_can_register' );
			
			if ( current_user_can( 'create_users' ) || $registration ){
				$link = '<a href="'.$register.'" alt="Register" title="Register">'. __('Register', 'profilebuilder') .'</a>';
				$registerLink = '<br/>'.__("Don't have an account?", "profilebuilder") . ' '. $link . '<br/>';
				echo $registerLink = apply_filters('wppb_login_widget_register', $registerLink, $link );
			}
		}
		

		if ( $lostpass && !is_user_logged_in() ){
			$link = '<br/><a href="'.$lostpass.'" alt="Lost Password" title="Lost Password">'. __('Lost Your Password?', 'profilebuilder') .'</a>';
			echo $link = apply_filters('wppb_login_widget_lost_password', $link, $lostpass );
		}

		do_action( 'wppb_login_widget_display', $args, $instance);	
			
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['redirect'] = strip_tags( $new_instance['redirect'] );
		$instance['register'] = strip_tags( $new_instance['register'] );
		$instance['lostpass'] = strip_tags( $new_instance['lostpass'] );

		do_action( 'wppb_login_widget_update_action', $new_instance, $old_instance);
		
		return $instance;
	
	}


	function form( $instance ) {

		$defaults = array( 'title' => __('Login', 'profilebuilder'), 'redirect' => '', 'register' => '', 'lostpass' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'hybrid'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'redirect' ); ?>"><?php _e('After login redirect URL:', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'redirect' ); ?>" name="<?php echo $this->get_field_name( 'redirect' ); ?>" value="<?php echo $instance['redirect']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'register' ); ?>"><?php _e('Register page URL (optional)', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'register' ); ?>" name="<?php echo $this->get_field_name( 'register' ); ?>" value="<?php echo $instance['register']; ?>" style="width:100%;" />
		</p>		
		
		<p>
			<label for="<?php echo $this->get_field_id( 'lostpass' ); ?>"><?php _e('Password Recovery page URL (optional)', 'profilebuilder'); ?></label>
			<input id="<?php echo $this->get_field_id( 'lostpass' ); ?>" name="<?php echo $this->get_field_name( 'lostpass' ); ?>" value="<?php echo $instance['lostpass']; ?>" style="width:100%;" />
		</p>

	<?php
	
		do_action( 'wppb_login_widget_after_display', $instance);
	}
}
// END WAY OF ADDING LOGIN IN THE WIDGET AREA
