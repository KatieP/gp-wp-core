<?php
global $wpdb, $current_user, $wpdb;

global $form_id, $forms;
require_once( GP_PLUGIN_DIR . '/core/gp-forms.php' );
if (!is_page()) {return false;}

$UID = $current_user->ID;

$received = date("F j, Y, g:i a");
#$notes = nl2br_limit($form_data['data']['notes'], 1);

$form_id = 'gp_adinquiries';
$forms = array(
	'advertise-with-us' => array(
		'breadcrumb_displayname' => 'Submit Your Inquiry',
		'email_notification' => array(
			'subject' => '[GreenPages] New Advertising Inquiry',
			'message' => "<b>Received:</b> {$received}<br />
				<br />
				<b>Company/Org Name:</b> {{org_name}}<br />
				<br />
				<b>Interested in:</b> {{ad_opts}}<br />
				<br />
				<b>First Name:</b> {{contact_firstname}}<br />
				<b>Last Name:</b> {{contact_lastname}}<br />
				<b>Email:</b> {{contact_email}}<br />
				<b>Phone:</b> {{contact_phone}}<br />
				<br />
				<b>Notes:</b><br />
				{{notes}}<br />
				<br />"
		),
		'db' => array(
			'name' => $wpdb->prefix . 'gp_adinquiries',
			'cols' => array (
				'contact_firstname' => 	array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'First Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 2, 'error' => 'First Name field is too short. Please fill in the required items.'))),
				'contact_lastname' => 	array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'Last Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 2, 'error' => 'Last Name field is too short. Please fill in the required items.'))),
				'contact_email' => 		array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'Email field is empty. Please fill in the required items.'), 'email' => array('error' => 'Invalid email address. Please fill in the required items.'))),
				'contact_phone' => 		array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Phone field is empty. Please fill in the required items.'), 'short' => array('lenght' => 6, 'error' => 'Phone field is too short. Please fill in the required items.'))),
				'org_name' => 			array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Organization Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'Organization Name field is too short. Please fill in the required items.'))),
				'ad_opts' => 			array('type' => 'a', 'default' => null, 'auth' => false),
				'notes' => 				array('type' => 's', 'default' => null, 'auth' => false)
			)
		),
		'errors' => array(
			'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
			'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
			'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
			'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
			'email' => 'Not a valid email address. Please fill in the required items.',
			'filesize' => 'File size too large.',
			'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
			'multivalue' => 'Too many or too few values selected.'
		)
	),
	
	//Note: The last form page should always be "thank you" page because form sessions are destroyed when the form has been successfully completed. There should be no other form data here! 
	'advertise-with-us-2' => array( 
		'breadcrumb_displayname' => 'Complete!',
		'allow_expiredsessions' => true
	)
);

$form_data = gp_publish_form();

$adoptions = array(
	'display' => 'Web Display Advertising',
	'advertorial' => 'Advertorial "New Stuff" Feature',
	'event' => 'Events Promotion',
	'competition' => 'Competitions Promotion',
	'directory' => 'Business Directory Listing'
);
?>

<?php if (current_page() == 'advertise-with-us') { ?>

<form name="gp_adinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>
	
	<p>Welcome!  To submit your inquiry to list your company with Green Pages and or advertise, fill out details in the fields below.</p>
	
	<h3>Your Contact Details</h3>
	<?php
	if ( function_exists('wp_nonce_field') ) {
		wp_nonce_field('gp-theme-insert_gp_drinquiries_form');
	}
	
	if ( !is_user_logged_in() ) {
	?>
	<table>
		<tr><td>
			<label>First Name*</label>
			<input type="text" name="contact_firstname" id="contact_firstname" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['contact_firstname']; ?>" <?php if ($form_data['error']['contact_firstname']) {echo 'style="border-color:red;"';} ?> />
		</td><td>
			<label>Last Name*</label>
			<input type="text" name="contact_lastname" id="contact_lastname" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['contact_lastname']; ?>" <?php if ($form_data['error']['contact_lastname']) {echo 'style="border-color:red;"';} ?> />
		</td></tr>
		<tr><td>
			<label>Email*</label>
			<input type="text" name="contact_email" id="contact_email" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['contact_email']; ?>" <?php if ($form_data['error']['contact_email']) {echo 'style="border-color:red;"';} ?> />
		</td><td>
			<label>Phone*</label>
			<input type="text" name="contact_phone" id="contact_phone" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['contact_phone']; ?>" <?php if ($form_data['error']['contact_phone']) {echo 'style="border-color:red;"';} ?> />
		</td></tr>
	</table>
	<?php 	
	} else {
		$userdata = get_userdata($current_user->ID);
	?>
		<input type="hidden" name="contact_firstname" id="contact_firstname" value="<?php echo $userdata->user_firstname; ?>" />
		<input type="hidden" name="contact_lastname" id="contact_lastname" value="<?php echo $userdata->user_lastname; ?>" />
		<input type="hidden" name="contact_email" id="contact_email" value="<?php echo $userdata->user_email; ?>" />
	<?php	
	}
	
	if ( is_user_logged_in() ) {
	?>
	<label>Contact Phone*</label>
	<input type="text" name="contact_phone" id="contact_phone" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['contact_phone']; ?>" <?php if ($form_data['error']['contact_phone']) {echo 'style="border-color:red;"';} ?> />
	<?php
	}
	?>
	
	<label>Company/Organization Name*</label>
	Who are you requesting advertising on behalf of?
	<input type="text" name="org_name" id="org_name" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_name']; ?>" <?php if ($form_data['error']['org_name']) {echo 'style="border-color:red;"';} ?> />

	<label>What type of advertising are you interested in?</label>
	<table>
		<?php
		foreach ($adoptions as $key => $value) {
			if (isset($form_data['data']['ad_opts'])) {
				if (in_array($key, $form_data['data']['ad_opts'])) {$adopt_selected = ' checked';} else {$adopt_selected = '';}
			}
			echo '<tr><td>' . $value . '</td><td><input type="checkbox" name="ad_opts[]" id="gp_adopt" value="' . $key . '"' . $adopt_selected . ' /></td></tr>';
		} 
		?>
	</table>
	
	<label>Anything else?</label>
	If you have anything else to tell us, you can write it here.
	<textarea name="notes" id="notes" rows="10"><?php echo $form_data['data']['notes']; ?></textarea>
	
	<input type="submit" name="save-fwd" value="Submit Inquiry" />
	<input type="submit" name="clear-slf" class="secondary" value="Clear" />
	
	<div class="clear"></div>
</form>
<?php } ?>


<?php if (current_page() == 'advertise-with-us-2') { ?>

<h2 class="green">Thank You!</h2>

<form name="gp_adinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php breadcrumbs(); ?>

	<p>Thank you! You're advertising inquiry has been successfully submitted. You will contacted by a member of our sales team within 1 business day.</p>

	<input type="submit" name="reset" value="Start again" />
</form>

<?php } ?>