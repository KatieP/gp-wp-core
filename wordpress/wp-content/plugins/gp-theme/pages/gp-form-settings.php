<?php 
/*
 * Should go back and do this - http://planetozh.com/blog/2009/09/top-10-most-common-coding-mistakes-in-wordpress-plugins/
 * and this - http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/
 */

if ($_POST['gp_self'] == 1 ) {
	
	check_admin_referer('gp-theme-update_gp_settings');
	
	$gp_abn = $_POST['gp_abn'];
	update_option('gp_abn', $gp_abn);

	$gp_fullcompanyname = $_POST['gp_fullcompanyname'];
	update_option('gp_fullcompanyname', $gp_fullcompanyname);

	$gp_companydisplayname = $_POST['gp_companydisplayname'];
	update_option('gp_companydisplayname', $gp_companydisplayname);

	$gp_slogan = $_POST['gp_slogan'];
	update_option('gp_slogan', $gp_slogan);

	$gp_phone1 = $_POST['gp_phone1'];
	update_option('gp_phone1', $gp_phone1);

	$gp_mobile1 = $_POST['gp_mobile1'];
	update_option('gp_mobile1', $gp_mobile1);
	
	$gp_fax1 = $_POST['gp_fax1'];
	update_option('gp_fax1', $gp_fax1);
	
	$gp_email1 = $_POST['gp_email1'];
	update_option('gp_email1', $gp_email1);
	
	$gp_skype1 = $_POST['gp_skype1'];
	update_option('gp_skype1', $gp_skype1);
	
	$gp_officeaddress = $_POST['gp_officeaddress'];
	update_option('gp_officeaddress', $gp_officeaddress);
	
	$gp_googlemaps = $_POST['gp_googlemaps'];
	update_option('gp_googlemaps', $gp_googlemaps);
	
	$gp_postaladdress = $_POST['gp_postaladdress'];
	update_option('gp_postaladdress', $gp_postaladdress);
	
	$gp_facebook = $_POST['gp_facebook'];
	update_option('gp_facebook', $gp_facebook);
	
	$gp_twitter = $_POST['gp_twitter'];
	update_option('gp_twitter', $gp_twitter);
	
	$gp_youtube = $_POST['gp_youtube'];
	update_option('gp_youtube', $gp_youtube);
	
	$gp_footertagline = $_POST['gp_footertagline'];
	update_option('gp_footertagline', $gp_footertagline);
	
	echo '<div class="updated"><p><strong>Options saved</strong></p></div>';
	
} else {
	
	$gp_abn = get_option('gp_abn');
	$gp_fullcompanyname = get_option('gp_fullcompanyname');
	$gp_companydisplayname = get_option('gp_companydisplayname');
	$gp_slogan = get_option('gp_slogan');
	$gp_phone1 = get_option('gp_phone1');
	$gp_mobile1 = get_option('gp_mobile1');
	$gp_fax1 = get_option('gp_fax1');
	$gp_email1 = get_option('gp_email1');
	$gp_skype1 = get_option('gp_skype1');
	$gp_officeaddress = get_option('gp_officeaddress');
	$gp_googlemaps = get_option('gp_googlemaps');
	$gp_postaladdress = get_option('gp_postaladdress');
	$gp_facebook = get_option('gp_facebook');
	$gp_twitter = get_option('gp_twitter');
	$gp_youtube = get_option('gp_youtube');
	$gp_footertagline = get_option('gp_footertagline');

}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>  
    <h2>Theme Settings</h2>

	<form name="gp_settings_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<?php
		if ( function_exists('wp_nonce_field') ) {
			wp_nonce_field('gp-theme-update_gp_settings');
		}
		?>
		
		<h3>Company Details</h3>
		<table class="form-table">
			<tr><th>ABN</th><td><input type="text" name="gp_abn" value="<?php echo $gp_abn; ?>" size="20" class="regular-text" /></td></tr>
			<tr><th>Full Company Name</th><td><input type="text" name="gp_fullcompanyname" value="<?php echo $gp_fullcompanyname; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>Company Display Name</th><td><input type="text" name="gp_companydisplayname" value="<?php echo $gp_companydisplayname; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>Slogan</th><td><input type="text" name="gp_slogan" value="<?php echo $gp_slogan; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>Footer Tagline</th><td><input type="text" name="gp_footertagline" value="<?php echo $gp_footertagline; ?>" size="255" class="regular-text" /></td></tr>
		</table>
		
		<h3>Contact Details</h3>
		<table class="form-table">
			<tr><th>Phone</th><td><input type="text" name="gp_phone1" value="<?php echo $gp_phone1; ?>" size="20" class="regular-text" /></td></tr>
			<tr><th>Mobile</th><td><input type="text" name="gp_mobile1" value="<?php echo $gp_mobile1; ?>" size="20" class="regular-text" /></td></tr>
			<tr><th>Fax</th><td><input type="text" name="gp_fax1" value="<?php echo $gp_fax1; ?>" size="20" class="regular-text" /></td></tr>
			<tr><th>Email</th><td><input type="text" name="gp_email1" value="<?php echo $gp_email1; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>Skype</th><td><input type="text" name="gp_skype1" value="<?php echo $gp_skype1; ?>" size="255" class="regular-text" /></td></tr>
			<tr>
				<th>Office Address</th>
				<td>
					<textarea name="gp_officeaddress" class="large-text"><?php echo $gp_officeaddress; ?></textarea>
					Google Maps: <input type="text" name="gp_googlemaps" value="<?php echo $gp_googlemaps; ?>" size="255" class="regular-text" />
				</td>
			</tr>
			<tr><th>Postal Address</th><td><textarea name="gp_postaladdress" class="large-text"><?php echo $gp_postaladdress; ?></textarea></td></tr>
		</table>
		
		<h3>Links</h3>
		<table class="form-table">
			<tr><th>Facebook</th><td><input type="text" name="gp_facebook" value="<?php echo $gp_facebook; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>Twitter</th><td><input type="text" name="gp_twitter" value="<?php echo $gp_twitter; ?>" size="255" class="regular-text" /></td></tr>
			<tr><th>YouTube</th><td><input type="text" name="gp_youtube" value="<?php echo $gp_youtube; ?>" size="255" class="regular-text" /></td></tr>
		</table>
		
		<p><input type="submit" name="Submit" value="Save Changes" id="#submit" class="button-primary" /></p>
		
		<input type="hidden" name="gp_self" value="1">
	</form>
</div>
