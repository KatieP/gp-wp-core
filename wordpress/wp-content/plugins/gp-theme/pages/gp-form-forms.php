<?php 
if ($_POST['gp_self'] == 1 ) {
	
	check_admin_referer('gp-theme-update_gp_forms');
	
	$gp_drinquiries_pages = $_POST['gp_drinquiries_pages'];
	update_option('gp_drinquiries_pages', $gp_drinquiries_pages);
	
	$gp_drinquiries_sendmail = $_POST['gp_drinquiries_sendmail'];
	update_option('gp_drinquiries_sendmail', $gp_drinquiries_sendmail);
	
	$gp_adinquiries_pages = $_POST['gp_adinquiries_pages'];
	update_option('gp_adinquiries_pages', $gp_adinquiries_pages);
	
	$gp_adinquiries_sendmail = $_POST['gp_adinquiries_sendmail'];
	update_option('gp_adinquiries_sendmail', $gp_adinquiries_sendmail);
	
	$gp_cpinquiries_pages = $_POST['gp_cpinquiries_pages'];
	update_option('gp_cpinquiries_pages', $gp_cpinquiries_pages);
	
	$gp_cpinquiries_sendmail = $_POST['gp_cpinquiries_sendmail'];
	update_option('gp_cpinquiries_sendmail', $gp_cpinquiries_sendmail);
	
	$gp_feedback_pages = $_POST['gp_feedback_pages'];
	update_option('gp_feedback_pages', $gp_feedback_pages);
	
	$gp_feedback_sendmail = $_POST['gp_feedback_sendmail'];
	update_option('gp_feedback_sendmail', $gp_feedback_sendmail);
	
	echo '<div class="updated"><p><strong>Options saved</strong></p></div>';
	
} else {
	
	$gp_drinquiries_pages = get_option('gp_drinquiries_pages');
	$gp_adinquiries_pages = get_option('gp_adinquiries_pages');
	$gp_cpinquiries_pages = get_option('gp_cpinquiries_pages');
	$gp_feedback_pages = get_option('gp_feedback_pages');
	$gp_drinquiries_sendmail = get_option('gp_drinquiries_sendmail');
	$gp_adinquiries_sendmail = get_option('gp_adinquiries_sendmail');
	$gp_cpinquiries_sendmail = get_option('gp_cpinquiries_sendmail');
	$gp_feedback_sendmail = get_option('gp_feedback_sendmail');
}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>  
    <h2>Form Settings</h2>

	<form name="gp_forms_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<?php
		if ( function_exists('wp_nonce_field') ) {
			wp_nonce_field('gp-theme-update_gp_forms');
		}
		?>
		
		<h3>Directory Inquiries</h3>
		<table class="form-table">
			<tr><th>Pages (slugs)</th><td><input type="text" name="gp_drinquiries_pages" value="<?php echo $gp_drinquiries_pages; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
			<tr><th>Notification Emails Recipients</th><td><input type="text" name="gp_drinquiries_sendmail" value="<?php echo $gp_drinquiries_sendmail; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
		</table>
		
		<br /></br />
		<h3>Advertising Inquiries</h3>
		<table class="form-table">
			<tr><th>Pages (slugs)</th><td><input type="text" name="gp_adinquiries_pages" value="<?php echo $gp_adinquiries_pages; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
			<tr><th>Notification Email Recipients</th><td><input type="text" name="gp_adinquiries_sendmail" value="<?php echo $gp_adinquiries_sendmail; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
		</table>
		
		<br /></br />
		<h3>Content Partner Inquiries</h3>
		<table class="form-table">
			<tr><th>Pages (slugs)</th><td><input type="text" name="gp_cpinquiries_pages" value="<?php echo $gp_cpinquiries_pages; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
			<tr><th>Notification Emails Recipients</th><td><input type="text" name="gp_cpinquiries_sendmail" value="<?php echo $gp_cpinquiries_sendmail; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
		</table>
		
		<br /></br />
		<h3>Feedback</h3>
		<table class="form-table">
			<tr><th>Pages (slugs)</th><td><input type="text" name="gp_feedback_pages" value="<?php echo $gp_feedback_pages; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
			<tr><th>Notification Email Recipients</th><td><input type="text" name="gp_feedback_sendmail" value="<?php echo $gp_feedback_sendmail; ?>" size="255" class="regular-text" /> <span class="description">Comma-seperated values.</span></td></tr>
		</table>
		
		<p><input type="submit" name="Submit" value="Save Changes" id="#submit" class="button-primary" /></p>
		
		<input type="hidden" name="gp_self" value="1">
	</form>
</div>
