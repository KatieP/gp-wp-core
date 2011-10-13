<?php
global $wpdb, $current_user, $wpdb;

global $form_id, $forms;
require_once( GP_PLUGIN_DIR . '/core/gp-forms.php' );
if (!is_page()) {return false;}

$UID = $current_user->ID;

$received = date("F j, Y, g:i a");
#$feedback = nl2br_limit($form_data['data']['feedback_content'], 1);

$form_id = 'gp_feedback';
$forms = array(
	'feedback' => array( 
		'breadcrumb_displayname' => 'Submit Your Feedback',
		'email_notification' => array(
			'subject' => '[GreenPages] New Feedback',
			'message' => "<b>Received:</b> {$received}<br />
				<br />
				<b>From:</b> {{contact_email}}<br />
				<br />
				<b>Feedback:</b><br />
				{{feedback_content}}<br />
				<br />"
		),
		'db' => array(
			'name' => $wpdb->prefix . 'gp_feedback',
			'cols' => array (
				'contact_email' => 		array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'Email field is empty. Please fill in the required items.'), 'email' => array('error' => 'Invalid email address. Please fill in the required items.'))),
				'feedback_content' => 	array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Feedback field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'Feedback field is too short. Please fill in the required items.')))
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
	'feedback-2' => array( 
		'breadcrumb_displayname' => 'Complete!',
		'allow_expiredsessions' => true
	)
);

$form_data = gp_publish_form();
?>

<?php if (current_page() == 'feedback') { ?>

<form name="gp_feedback_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>

	<p>Welcome! &nbsp;To submit your feedback to Green Pages, let us know your thoughts in the field below.</p>
	
	<?php
	if ( function_exists('wp_nonce_field') ) {
		wp_nonce_field('gp-theme-insert_gp_feedback_form');
	}

	if (!is_user_logged_in()) {
	?>
	<label>Your Email Address*</label>
	<input type="text" name="contact_email" id="contact_email" value="<?php echo $form_data['data']['contact_email']; ?>" autocomplete="off" maxlenght="255" <?php if ($form_data['error']['contact_email']) {echo 'style="border-color:red;"';} ?> />
	<?php 	
	} else {
		$userdata = get_userdata($current_user->ID);
	?>
		<input type="hidden" name="contact_email" id="contact_email" value="<?php echo $userdata->user_email; ?>" />
	<?php } ?>

	<label>Your Feedback*</label>
	Love the new site? Hate the new site? Let us know what you think! It is limited to 550 characters or about 80 words so be succinct!

	<textarea name="feedback_content" id="feedback_content" rows="10" <?php if ($form_data['error']['feedback_content']) {echo 'style="border-color:red;"';} ?>><?php echo $form_data['data']['feedback_content']; ?></textarea>

	<input type="submit" name="save-fwd" value="Submit Feedback" />
	<input type="submit" name="clear-slf" class="secondary" value="Clear" />
	
	<div class="clear"></div>

</form>
<?php } ?>

<?php if (current_page() == 'feedback-2') { ?>

<h2 class="green">Thank You!</h2>

<form name="gp_feedback_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php breadcrumbs(); ?>

	<p>Thank you! You're feedback has been successfully submitted.</p>

	<input type="submit" name="reset" value="Start again" />
</form>

<?php } ?>