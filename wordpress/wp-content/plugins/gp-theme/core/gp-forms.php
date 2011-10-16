<?php
/*
 * This needs to be completely refactored. I fell into some really old horrible bad habits doing it the Wordpress way and wasted a lot of time. 
 * "The first draft of anything is shit." ~Ernest Hemingway~
 * It's actually very important to me personally and I plan on doing this "properly" soon.
 * Eddy
 * 
 * TODO:
 * 1. Post submit action 'reset' destroys the session and thus destroys the session info for all the other forms too.
 * 2. Implement full DB session management. (Possibly look at Redis)
 * 3. Intergrate file uploads properly.
 * 4. Should allow for file uploads in more than 1 place on the form.
 * 5. Add display error messages for each form item. Not just at the top of the form.
 * 6. How do we store array infomation in db? Serialize? Json? Implode? Give an option to choose? 
 * 7. Need to expand on form validation techniques, a lot!
 * 8. Save should skip to next incomplete form page.
 * 9. No need for multiple pages.
 */ 
  
function gp_publish_form() {
	global $current_user, $forms, $systemformvars, $return_data, $return_results, $errors, $UID, $post_data, $form_id, $default_error_msgs;

	if (is_user_logged_in()) {
		$UID = $current_user->ID;
	} else {
		$UID = 0;
	}
	
	$systemformvars = array(
		'FORM_STATE' => 	array('type' => 'a', 'default' => array(), 'auth' => false),
		'UID' => 			array('type' => 'd', 'default' => $UID, 'auth' => false),
		'CREATED' => 		array('type' => 's', 'default' => $CREATED, 'auth' => false),
		'LAST_ACTIVITY' => 	array('type' => 's', 'default' => strtotime('now'), 'auth' => false),
		'SUBMITTED' => 		array('type' => 's', 'default' => $SUBMITTED, 'auth' => false),
		'USER_AGENT' => 	array('type' => 's', 'default' => cleanstring($_SERVER['HTTP_USER_AGENT']), 'auth' => false),
		'REMOTE_ADDR' => 	array('type' => 's', 'default' => cleanstring($_SERVER['REMOTE_ADDR']), 'auth' => false),
		'HTTP_REFERER' => 	array('type' => 's', 'default' => cleanstring($_SERVER['HTTP_REFERER']), 'auth' => false),	
		'session_id' => 	array('type' => 's', 'where' => true, 'default' => session_id(), 'auth' => false)
	);
	
	$default_error_msgs = array(
		'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
		'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
		'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
		'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
		'email' => 'Not a valid email address. Please fill in the required items.',
		'filesize' => 'File size too large.',
		'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
		'multivalue' => 'Too many or too few values selected.'
	);
	
	if (isset($forms[current_page()]['errors'])) {
		$default_error_msgs = array_replace($default_error_msgs, $forms[current_page()]['errors']);
	}
	
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// Only create a new db session on first post. We don't need to flood the db with empty results.
		session_handler();
		
		$return_data = get_form_data();
		$return_results = get_form_state();
		
		$submit = check_submit_type();
		
		if ($submit == 'reset') {
			post_redirect();
		}
		
		if ($submit == 'save-fwd' || $submit == 'save-bck' || $submit == 'save-to' || $submit == 'save' || $submit == 'save-slf' || $submit == 'skip-fwd' || $submit == 'skip-bck' || $submit == 'skip-to') {
			$post_data = get_post_data();
			$errors = validate_form_data();
			
			if ($errors[0] == false) {
				$return_results[page_position()] = '1';
				$success = true;
			} else {
				$return_results[page_position()] = '0';
				$success = false;
			}
			
			if ($submit == 'skip-fwd' || $submit == 'skip-bck' || $submit == 'skip-to') {
				if ($return_results[page_position()] != 1) {
					$return_results[page_position()] = '2';
				}
				$success = true;
			}
		}
		
		if ($submit == 'clear-fwd' || $submit == 'clear-bck' || $submit == 'clear-to' || $submit == 'clear' || $submit == 'clear-slf') {
			$post_data = get_empty_data();
			$return_results[page_position()] = '-1';
			$success = true;
		}
		
		// mark complete if none of the form fields require validation
		if (!isset($forms[current_page()]['db']['cols']) || count($forms[current_page()]['db']['cols']) == 0) {
			$return_results[page_position()] = '1';
		}
		
		// if there is no session and we don't land on the starting form page then mark the previous form pages incomplete
		for ($i=0;$i<page_position();$i++) {
			if ($return_results[$i]=='-1') {
				$return_results[$i] = '2';
			}
		}
		
		if ($success == true || $submit == 'skip-fwd' || $submit == 'skip-bck' || $submit == 'skip-to') {
			save_form_data();
		}
		
		check_pages();
		
		// If next page is a redirect to an external site we'd better make sure all the previous form pages are completely filled out.
		if ($submit == 'skip-fwd' || $submit == 'save-fwd' || $submit == 'clear-fwd') {
			$next_page = array_slice($forms, page_position()+1, 1);
			$next_page_id = key($next_page);
			if (isset($next_page[$next_page_id]['redirect'])) {
				$count_results = array_count_values(array_slice($return_results, 0, page_position()+1));
				if (($count_results[1] < page_position()+1) || $count_results[1] == null) {
					$errors[0] = true;
					array_push($errors[1], 'Form is incomplete. You cannot progress until all previous form pages are completed.');
					$success = false;
				}
				# should I reset session on success?
			}
		}

		if ($success == false) {
			return array('data' => $post_data, 'error' => $errors[1]);
		}
		
		if ($success == true) {
			if ($submit != 'skip-fwd' || $submit != 'skip-bck' || $submit != 'skip-to') {
				send_email_notification();
			}
			
			post_redirect($submit);
		}
		
	} else {
		if (isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]['id'])) {
			$return_data = get_form_data();
			$return_results = get_form_state();
			$errors = validate_form_data();
			
			return array('data' => $return_data, 'error' => $errors[1]);
		} else {
			$return_results = get_form_state();
		}
	}
}

function send_email_notification() {
	global $forms, $form_id;
	
	if (isset($forms[current_page()]['email_notification']['subject']) && isset($forms[current_page()]['email_notification']['message'])) {
		$return_data = get_form_data();
		
		$message = $forms[current_page()]['email_notification']['message'];
		
		$cols = array();
		foreach ($forms as $key => $value) {
			$next_cols = array_keys($value['db']['cols']);
			if ($next_cols != null) {
				$cols = array_merge($cols, $next_cols);
			}
		}
		
		foreach ($cols as $key => $value) {
			$message = str_replace("{{" . $value . "}}", nl2br_limit($return_data[$value]), $message);
		}

		$notification_recipients = array_map( 'trim', explode( ',', get_option($form_id . '_sendmail') ) );
		add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html"; ' ) );
		foreach ($notification_recipients as $recipient) {
			if ( is_email($recipient) ) {
				wp_mail( $recipient, $forms[current_page()][email_notification]['subject'], $message );
			}
		}
	}
}

function get_form_state() {
	global $forms, $return_data, $form_id;
	
	if (page_position()+1 == count_pages()) {
		$i = 0;
		foreach ($forms as $value) {
			$FORM_STATE[] = '1';
			$i++;
		}
		return $FORM_STATE;
	}
	
	if (isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]['id'])) {
		$FORM_STATE = explode(",", $return_data['FORM_STATE']);
		return $FORM_STATE;
	} 
	
	$i = 0;
	foreach ($forms as $value) {
		if (page_position() > $i) {
			$FORM_STATE[] = '2';
		} else {
			$FORM_STATE[] = '-1';
		}
		$i++;
	}

	return $FORM_STATE;
}

function get_form_data() {
	global $wpdb, $forms, $form_id, $systemformvars;

	if (isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]['id'])) {
		if ($systemformvars['UID']['default'] != 0) {$adduser = ' AND UID = ' . $systemformvars['UID']['default'];} else {$adduser = '';}
		
		#if (count($forms[current_page()]['db']['cols']) > 0) {$cols = implode(", ", array_keys($forms[current_page()]['db']['cols'])) . ", ";}
		
		$cols = array();
		foreach ($forms as $key => $value) {
			$next_cols = $value['db']['cols'];
			if ($next_cols != null) {
				$cols = array_merge($cols, $next_cols);
			}
		}
		
		if (count($cols) > 0) {$select_cols = implode(", ", array_keys($cols)) . ", ";}
		
		$qrystring = $wpdb->prepare("SELECT " . $select_cols . "FORM_STATE, session_id FROM ".$forms[current_page()]['db']['name']." WHERE ID = ".$_SESSION[$form_id]['id']." AND session_id = '".$systemformvars['session_id']['default']."'".$adduser);
		$qryresults = $wpdb->get_row($qrystring);
		
		// if session id's don't match reset form.
		if (($qryresults->session_id != session_id()) || $qryresults == null) {
			if (page_position()+1 != count_pages()) { // ingore last page
				post_redirect();
			}
		} 
		
		foreach ($cols as $key => $value) {
			if (isset($qryresults->$key)) {
				
				if ($key['type'] == 'a') {
					$return_form[$key] = explode(",", $qryresults->$key);
				} else {
					$return_form[$key] = $qryresults->$key;
				}
				
			} else {
				$return_form[$key] = null;
			}
		}
		$return_form['FORM_STATE'] = $qryresults->FORM_STATE;
	}
	return $return_form;
}

function get_post_data() {
	global $forms;
	
	$return_form = array();
	foreach ($forms[current_page()]['db']['cols'] as $key => $value) {
		if (isset($_POST[$key])) {
			$return_form[$key] = $_POST[$key];
		} else {
			$return_form[$key] = null;
		}
	}

	return $return_form;
}

function get_empty_data() {
	global $forms;
	
	$return_form = array();
	foreach ($forms[current_page()]['db']['cols'] as $key => $value) {
		unset($return_form[$key]);
		$return_form[$key] = null;	
	}

	return $return_form;
}

function validate_form_data() {
	global $forms, $return_data, $form_id, $post_data, $default_error_msgs;
	
	$errors = false;
	$error_msgs = array();
	
	if (isset($forms[current_page()]['db']['cols'])) {
		foreach ($forms[current_page()]['db']['cols'] as $key => $value) {
			$check_errors = true;
			
			if ($value['auth'] == true) {
				if (is_user_logged_in()) {
					$check_errors = false;
				}
			}
			
			if (($check_errors == true) && isset($value['validate'])) {
				$error = false;
				foreach ($value['validate'] as $kValidate => $vValidate) {
					if (!$error) {
						$func = "validate_" . $kValidate;
						
						// Everything in this statement is complete b.s. Fix it!!!
						if (function_exists($func)) {
							unset($input1);
						
							if ($kValidate == 'short' && array_key_exists('lenght', $vValidate)) {
								$input1 = $vValidate['lenght'];
							}
							
							if ($kValidate == 'multivalue' && array_key_exists('limit', $vValidate)) {
								$input1 = $vValidate['limit'];
							}
							
							if (isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]['id']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
								$item = $return_data[$key];
							} else {
								$item = cleanstring($post_data[$key]);
							}
							
							if ($func($item, $input1)) {
								$error = true;
								$errors = true;
								$error_msg = '';
		
								if (array_key_exists('error', $vValidate)) {
									$error_msg = $vValidate['error'];
								} else {
									$error_msg = $default_error_msgs[$kValidate];
								}
								
								$error_msgs[$key] = $error_msg;
							} else {
								$error = false;
							}
						}
					}
				}
			}
		}
	}
	return array($errors, $error_msgs);
}

function current_page() {
	return basename(get_permalink());
}

function count_pages() {
	global $forms;
	return count($forms);
}

function count_results() {
	global $forms;
	#return count($FORM_STATE);
}

function page_position() {
	global $forms;
	return array_search(current_page(), array_keys($forms));
}

function check_pages() {
	global $forms;
	
	$page_position = page_position();
	
	if ($page_position > 0) {
		if (array_fill(0, $page_position+1, '-1') === array_slice($forms, 0, $page_position+1)) {
			post_redirect();
		}
	}
	
	if (count_pages() > count_results()) {
		#post_redirect();
	}
	
	return;
}

function check_submit_type($submit=null) {
	global $forms;
	
	$submit_values = array('save', 'save-slf', 'save-fwd', 'save-bck', 'save-to', 'skip', 'skip-fwd', 'skip-bck', 'skip-to', 'clear', 'clear-slf', 'clear-fwd', 'clear-bck', 'clear-to', 'reset');
	
	if (in_array($submit, $submit_values)) {
		return $submit;
	}
	
	foreach ($submit_values as $value) {
		if ($_POST[$value]) {
			return $value; 
		}
	}
	
	foreach ($forms as $key => $value) {
		if ($_POST[$key]) {
			return 'skip-to'; 
		}
	}
	
	return 'reset';
}

function get_page_by_position($position) {
	global $forms;
	$i = 0;
	foreach ($forms as $key => $value) {
		if ($i == $position) {
			return $key;
		}
		$i++;
	}
	return get_page_by_position(0);
}

function get_send_address($submit) {
	global $forms;

	$page_position = page_position();

	$self = array('save', 'save-slf', 'clear', 'clear-slf');
	$forward = array('save-fwd', 'skip', 'skip-fwd', 'clear-fwd');
	$back = array('save-bck', 'skip-bck', 'clear-bck');
	$to = array('save-to', 'skip-to', 'clear-to');
	
	if (isset($forms[get_page_by_position($page_position+1)]['redirect']) && $submit == 'save-fwd') {
		$return_data = get_form_data();
		
		$cols = array();
		foreach ($forms as $key => $value) {
			$next_cols = array_keys($value['db']['cols']);
			if ($next_cols != null) {
				$cols = array_merge($cols, $next_cols);
			}
		}

		$redirect = $forms[get_page_by_position($page_position+1)]['redirect'];
		foreach ($cols as $key => $value) {
			$redirect = str_replace("{{" . $value . "}}", urlencode($return_data[$value]), $redirect);
		}
		
		return $redirect;
	}
	
	if (in_array($submit, $self)) {
		return '../' . get_page_by_position($page_position) . '/';
	}
	
	if (in_array($submit, $forward)) {
		return '../' . get_page_by_position($page_position+1) . '/';
	}
	
	if (in_array($submit, $back)) {
		return '../' . get_page_by_position($page_position-1) . '/';
	}
	
	if (in_array($submit, $to)) {
		foreach ($forms as $key => $value) {
			if ($_POST[$key]) {
				return '../' . $key . '/';
			}	
		}
	}

	reset_session();
	return get_page_by_position(0);
}

function reset_session() {
	// If a form is reset this will regerate a new session id and unset all the session form data. It doesn't destory the entire session.
	// This is to keep current user session alive but start basically a new "form session" as they are recorded in the db with the current session id for reference.
	
	global $form_id;

	unset($_SESSION[$form_id]);
	$_SESSION['CREATED'] = strtotime('now');
	session_regenerate_id();
}

function post_redirect($submit=null) {
	$url = get_send_address(check_submit_type($submit));
	wp_redirect($url, 302);
}

function session_handler($db_id=null) {
	// Handle form session data
	
	global $form_id, $UID;
	
	if (isset($_SESSION[$form_id]) && isset($_SESSION[$form_id]['id'])) {
		$_SESSION[$form_id] = array('uid' => $UID, 'id' => $_SESSION[$form_id]['id']);
		return;
	}
	
	if ($db_id != null) {
		$_SESSION[$form_id] = array('uid' => $UID, 'id' => $db_id);
		return;
	}
	
	$_SESSION[$form_id] = array('uid' => $UID, 'id' => null);

	if (count_pages() == page_position()+1) {
		reset_session();
	}
	
	return;
}

function breadcrumbs() {
	global $forms, $return_results;
	
	// if there is no session and we don't land on the starting form page then mark the previous form pages incomplete
	for ($i=0;$i<page_position();$i++) {
		if ($return_results[$i]=='-1') {
			$return_results[$i] = '2';
		}
	}
	
	echo '<nav id="breadcrumb-steps"><ul>';
	$i=0;
	#$return_results = get_form_state();
	foreach ($forms as $key => $value) {
		$addicon='';
		if ($return_results[$i]=='-1') {$addclass='inactive';}
		if ($return_results[$i]=='0') {$addclass='fail';$addicon='<img class="alert-icon" src="' . get_bloginfo('template_url') . '/template/famfamfam_silk_icons_v013/icons/cross.png" />';}
		if ($return_results[$i]=='1') {$addclass='success';$addicon='<img class="alert-icon" src="' .get_bloginfo('template_url') . '/template/famfamfam_silk_icons_v013/icons/tick.png" />';}
		if ($return_results[$i]=='2') {$addclass='incomplete';$addicon='<img class="alert-icon" src="' . get_bloginfo('template_url') . '/template/famfamfam_silk_icons_v013/icons/cross.png" />';}
		if (count_pages() == page_position()) {$addclass='success';$addicon='<img class="alert-icon" src="' .get_bloginfo('template_url') . '/template/famfamfam_silk_icons_v013/icons/tick.png" />';}
		if ($key == current_page()) {$addclass='active';}
		
		if ($i == 0) {$addspacer='';} else {$addspacer='<div class="go-l"></div>';}
		
		if ($forms[$key]['allow_skipto'] == true) {
			#echo '<li class="' . $addclass . '">' . $addspacer . $addicon . '<a href="../' . $key . '">' . $value['breadcrumb_displayname'] . '</a></li>';
			echo '<li class="' . $addclass . '">' . $addspacer . $addicon . '<input type="submit" class="text-button" name="' . $key . '" value="' . $value['breadcrumb_displayname'] . '"/></li>';
		} else {
			echo '<li class="' . $addclass . '">' . $addspacer . $addicon . $value['breadcrumb_displayname'] . '</li>';
		}
		
		$i++;
	}
	echo '</ul><div class="clear"></div></nav>';
}

function errors() {
	global $forms, $form_data, $errors, $default_error_msgs;

	if ( count($errors[1]) > 0 ) {
		if ( count($errors[1]) >= 2 ) {
			$error_msg = $default_error_msgs['multi'];
		} else {
			foreach ($errors[1] as $key => $value) {
				$error_msg = $value;
			}
		}
		
		echo '<div class="form-error">' . $error_msg . '</div>';
	}
}

function save_form_data() {
	global $wpdb, $form_id, $forms, $return_data, $return_results, $systemformvars, $post_data;
	
	$where_data = array();
	$where_types = array();
	$data_types = array();
	
	$systemformvars['FORM_STATE']['default'] = $return_results;
	
	foreach ($forms[current_page()]['db']['cols'] as $key => $value) {
		if ($value['type'] == 'a') {
			if ($key != 'dir_images') { // special: file uploads is a seperate process. we do this so we don't overwrite the value
				$value_type = 's';
				$value_default = implode(',', $post_data[$key]);
			}
		} else {
			$value_type = $value['type'];
			$value_default = $post_data[$key];
		}
		
		if ($value['where'] == true) {
			$where_data[$key] = $value_default;
			$where_types[] = '%' . $value_type;
		} else {
			if ($key != 'dir_images') { // special: file uploads is a seperate process. we do this so we don't overwrite the value
				$form_data[$key] = $value_default;
				$data_types[] = '%' . $value_type;
			}
		}
	}

	foreach ($systemformvars as $key => $value) {
		if ($value['type'] == 'a') {
			$value_type = 's';
			$value_default = implode(',', $value['default']);
		} else {
			$value_type = $value['type'];
			$value_default = $value['default'];
		}
		
		if ($value['where'] == true) {
			$where_data[$key] = $value_default;
			$where_types[] = '%' . $value_type;
		} else {
			$form_data[$key] = $value_default;
			$data_types[] = '%' . $value_type;
		}
	}
	
	if (isset($_SESSION[$form_id])) {
		$where_data['ID'] = $_SESSION[$form_id]['id'];
		$where_types[] = '%d'; # for table ID
		
		$update_result = $wpdb->update( $forms[current_page()]['db']['name'], $form_data, $where_data, $data_types, $where_types);
		if ($update_result === 0 || $update_result === false) {
			array_pop($where_data);
			array_pop($where_types);
			$wpdb->insert( $forms[current_page()]['db']['name'], array_merge($form_data, $where_data), array_merge($data_types, $where_types));
			session_handler($wpdb->insert_id);
			
		}
	} else {
		$data_types = array_merge($data_types, $where_types);
		$wpdb->insert( $forms[current_page()]['db']['name'], array_merge($form_data, $where_data), array_merge($data_types, $where_types));
		session_handler($wpdb->insert_id);
	}
}

?>