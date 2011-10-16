<?php
global $wpdb, $current_user, $wpdb;

global $form_id, $forms;
require_once( GP_PLUGIN_DIR . '/core/gp-forms.php' );
if (!is_page()) {return false;}

$UID = $current_user->ID;

$received = date("F j, Y, g:i a");
#$org_description = nl2br_limit($form_data['data']['org_description'], 1);

$form_id = 'gp_drinquiries';
$forms = array(
	'list-your-business' => array(
		'allow_skipto' => true, 
		'breadcrumb_displayname' => 'Contact & business details',
		'email_notification' => array(
			'subject' => '[Green Pages] New Directory Inquiry',
			'message' => "<b>Received:</b> {$received}<br />
				<br />
				<b>Company/Org Name:</b> {{org_name}}<br />
				<b>Website URL:</b> {{org_url}}<br />
				<br />
				<b>Office or Postal Address:</b><br />
				{{org_street}}<br />
				{{org_city}}<br />
				{{org_postcode}}<br />
				{{org_state}}<br />
				<br />
				<b>Twitter ID:</b> {{org_twitter}}<br />
				<b>Facebook URL:</b> {{org_facebook}}<br />
				<br />
				<b>Description:</b><br />
				{{org_description}}<br />
				<br />
				<b>First Name:</b> {{contact_firstname}}<br />
				<b>Last Name:</b> {{contact_lastname}}<br />
				<b>Email:</b> {{contact_email}}<br />
				<b>Phone:</b> {{contact_phone}}<br />
				<br />"
		),
		'db' => array(
			'name' => $wpdb->prefix . 'gp_drinquiries',
			'cols' => array (
				'contact_firstname' => 	array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'First Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 2, 'error' => 'First Name field is too short. Please fill in the required items.'))),
				'contact_lastname' => 	array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'Last Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 2, 'error' => 'Last Name field is too short. Please fill in the required items.'))),
				'contact_email' => 		array('type' => 's', 'default' => null, 'auth' => true, 'validate' => array('empty' => array('error' => 'Email field is empty. Please fill in the required items.'), 'emailaddress' => array('error' => 'Invalid email address. Please fill in the required items.'))),
				'contact_phone' => 		array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Phone field is empty. Please fill in the required items.'), 'short' => array('lenght' => 6, 'error' => 'Phone field is too short. Please fill in the required items.'))),
				'org_name' => 			array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Organization Name field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'Organization Name field is too short. Please fill in the required items.'))),
				'org_url' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'org_storeurl' => 		array('type' => 's', 'default' => null, 'auth' => false),
				'org_street' => 		array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Address field field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'Address field is too short. Please fill in the required items.'))),
				'org_postcode' => 		array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Postcode field field is empty. Please fill in the required items.'), 'short' => array('lenght' => 4, 'error' => 'Postcode field is too short. Please fill in the required items.'))),
				'org_city' => 			array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'City field field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'City field is too short. Please fill in the required items.'))),
				'org_state' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'org_facebook' => 		array('type' => 's', 'default' => null, 'auth' => false),
				'org_twitter' => 		array('type' => 's', 'default' => null, 'auth' => false),
				'org_description' => 	array('type' => 's', 'default' => null, 'auth' => false, 'validate' => array('empty' => array('error' => 'Description field is empty. Please fill in the required items.'), 'short' => array('lenght' => 3, 'error' => 'Description field is too short. Please fill in the required items.')))
			)
		),
		'errors' => array(
			'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
			'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
			'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
			'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
			'emailaddress' => 'Not a valid email address. Please fill in the required items.',
			'filesize' => 'File size too large.',
			'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
			'multivalue' => 'Too many or too few values selected.'
		)
	),
	/*
	'list-your-business-2' => array(
		'allow_skipto' => true, 
		'breadcrumb_displayname' => 'Achievements',
		'db' => array(
			'name' => $wpdb->prefix . 'gp_drinquiries',
			'cols' => array (
				'dsc_biodiversity' => 		array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_carbon' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_history' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_energy' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_sustainability' => 	array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_envfacts' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_materials' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_social' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_packaging' => 			array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_recyclability' => 		array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_recycledcontent' => 	array('type' => 's', 'default' => null, 'auth' => false),
				'dsc_water' => 				array('type' => 's', 'default' => null, 'auth' => false)
			)
		),
		'errors' => array(
			'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
			'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
			'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
			'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
			'emailaddress' => 'Not a valid email address. Please fill in the required items.',
			'filesize' => 'File size too large.',
			'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
			'multivalue' => 'Too many or too few values selected.'
		)
	),
	'list-your-business-3' => array(
		'allow_skipto' => true, 
		'breadcrumb_displayname' => 'Certifications',
		'db' => array(
			'name' => $wpdb->prefix . 'gp_drinquiries',
			'cols' => array (
			)
		),
		'errors' => array(
			'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
			'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
			'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
			'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
			'emailaddress' => 'Not a valid email address. Please fill in the required items.',
			'filesize' => 'File size too large.',
			'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
			'multivalue' => 'Too many or too few values selected.'
		)
	),
	'list-your-business-4' => array(
		'allow_skipto' => true,  
		'breadcrumb_displayname' => 'Upload media',
		'db' => array(
			'name' => $wpdb->prefix . 'gp_drinquiries',
			'cols' => array (
				'dir_images' => 	array('type' => 'a', 'default' => null, 'auth' => false, 'validate' => array('multivalue' => array('limit' => 9, 'error' => 'Too many images uploaded. Limit 9.'), 'filesize' => array('size' => 2000000, 'error' => 'Image is too large. Please upload an image no greater than 2MB.'), 'filetype' => array('types' => array('jpeg', 'jpg', 'gif', 'png'), 'error' => 'Invalid file type. Please upload either .jpeg, .png or .gif file.')))
			)
		),
		'errors' => array(
			'system' => 'Something went horribly wrong! Please try again or contact a system administrator if the problem persists.',
			'multi' => 'Please check the following fields and make sure they\'re <u>not empty</u> or <u>too short</u>.',
			'empty' => 'You have 1 or more empty fields. Please fill in the required items.',
			'short' => 'You have 1 or more fields that are too short. Please fill in the required items.',
			'emailaddress' => 'Not a valid email address. Please fill in the required items.',
			'filesize' => 'File size too large.',
			'filetype' => 'File type not accepted. Please upload either .jpeg, .png or .gif file.',
			'multivalue' => 'Too many or too few values selected.'
		)
	),
	*/
	'list-your-business-5' => array( 
		'redirect' => 'https://green-pages.chargify.com/h/27023/subscriptions/new?first_name={{contact_firstname}}&last_name={{contact_lastname}}&email={{contact_email}}&reference=' . $current_user->display_name, 
		'breadcrumb_displayname' => 'Payment details',
	),
	
	//Note: The last form page should always be "thank you" page because form sessions are destroyed when the form has been successfully completed. There should be no other form data here! 
	'list-your-business-6' => array( 
		'breadcrumb_displayname' => 'Finish',
		'allow_expiredsessions' => true
	)
);

$form_data = gp_publish_form();

$states_au = array('NSW', 'QLD', 'VIC', 'WA', 'SA', 'NT', 'ACT', 'TAS');

?>

<?php if (current_page() == 'list-your-business') { ?>

<h2 class="green">Create your directory listing</h2>

<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>
	
	<?php
	 if ( is_user_logged_in() ) {
		echo "<p>Welcome " . $current_user->display_name . "!<br />To list your company or business in the Green Pages directory please fill in the details below.</p>";
	} else {
		echo "<p>Welcome!<br />To list your company or business in the Green Pages directory please fill in the details below.</p>";
	} 
	?>
	
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
	
	<h3>Business Details</h3>
	<table>
		<tr><td>
			<label>Company/Organization Name*</label>
			This is the name that will be the title of your page listing.
		</td><td></td></tr>
		<tr><td>
			<input type="text" name="org_name" id="org_name" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_name']; ?>" <?php if ($form_data['error']['org_name']) {echo 'style="border-color:red;"';} ?> />
		</td><td></td></tr>
		<tr><td>
			<label>Website URL</label>
			This is the url link that you would like to user to be directed to from your listing. It can be any page within your site.
		</td><td>
			<label>Online Store URL</label>
			Do you have a page where people can buy your products online? This link will be called 'Purchase Now' and should link to an area of your site where the users will be able to buy your product.		
		</td></tr>
		<tr><td>
			<input type="text" name="org_url" id="org_url" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_url']; ?>" />
		</td><td>
			<input type="text" name="org_storeurl" id="org_storeurl" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_storeurl']; ?>" />
		</td></tr>
		<tr><td>
			<label>Office or Postal Address*</label>
			The address that will appear on your listing.
		</td><td>
			<label>Postcode*</label>			
		</td></tr>
		<tr><td>
			<input type="text" name="org_street" id="org_street" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_street']; ?>" <?php if ($form_data['error']['org_street']) {echo 'style="border-color:red;"';} ?> />
		</td><td>
			<input type="text" name="org_postcode" id="org_postcode" autocomplete="off" maxlength="4" value="<?php echo $form_data['data']['org_postcode']; ?>" <?php if ($form_data['error']['org_postcode']) {echo 'style="border-color:red;"';} ?> />
		</td></tr>
		<tr><td>
			<label>City*</label>
		</td><td>
			<label>State*</label>			
		</td></tr>
		<tr><td>
			<input type="text" name="org_city" id="org_city" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_city']; ?>" <?php if ($form_data['error']['org_city']) {echo 'style="border-color:red;"';} ?> />
		</td><td>
			<select name="org_state" id="org_state">
				<?php
				foreach ($states_au as $state) {
					if ($state == $form_data['data']['org_state']) {$state_selected = ' selected';} else {$state_selected = '';}
  					echo '<option value="' . $state . '"' . $state_selected . '>' . $state . '</option>';
				}
  				?> 									
			</select>
		</td></tr>
		<tr><td>
			<label>Facebook Page</label>
			Find your company's page on facebook and copy the url address from the browser window - it will look something like http://www.facebook.com/mygreenbusiness1827471?ref=ts.
		</td><td>
			<label>Twitter ID</label>
			Add your company's twitter address in the format of '@yourname'			
		</td></tr>
		<tr><td>
			<input type="text" name="org_facebook" id="org_facebook" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_facebook']; ?>" />
		</td><td>
			<input type="text" name="org_twitter" id="org_twitter" autocomplete="off" maxlength="255" value="<?php echo $form_data['data']['org_twitter']; ?>" />
		</td></tr>
	</table>
	<label>Business Description*</label>
	This is the body of the text that describes your business and it's products. It is limited to 550 characters or about 80 words so be succinct!
	<textarea name="org_description" id="org_description" rows="10" <?php if ($form_data['error']['org_description']) {echo 'style="border-color:red;"';} ?>><?php echo $form_data['data']['org_description'] ?></textarea>
	
	<input type="submit" name="save-fwd" value="Save & Continue" />
	<input type="submit" name="clear-slf" class="secondary" value="Clear" />
	<input type="submit" name="reset" class="secondary" value="Start Over" />
	
	<div class="clear"></div>
</form>
<?php } ?>

<?php if (current_page() == 'list-your-business-2') { ?>
<h2 class="green">Step 3 of Your Directory Page: Environmental Certifications</h2>

<p>Fill out as few or as many of the environmental fields as you wish. Each allows for 1,000 characters or about 120 words.</p>

<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>

	<div>
		<p>This is the page where you can add detailed information about the environmental performance of your company. The readers want to know detailed environmental product information, certifications, awards and your unique green innovation.</p>
		<p>Your environmental data will make your business stand out and show your customers your true environmental integrity - so don't skip the detail of your sustainability story! If you can't complete all of the fields now, you can always save it and add or change it later.<p/>
		<p>Only the  environmental specification that you fill out will show on your directory page. No fields are compulsory and blank fields will not be visible to the reader.</p>
	</div>
	
	<label>Biodiversity & Land Management</label>
	<p>How does your product or service assist in improving biodiversity? Do you help improve land management practices? Explain any specific cases where you have improved an areas ecology or protected an area from desctruction.  Detail any species that have returned or any other quantitative data.<p/>
	<textarea value="" name="dsc_biodiversity" id="dsc_biodiversity" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_biodiversity']; ?></textarea> 			
	
	<label>Carbon Emissions</label>
	<p>List any facts or data you have about your efforts to reduce carbon emissions or how your product or service directly helps your clients reduce thier emissions.<p/>
	<textarea value="" name="dsc_carbon" id="dsc_carbon" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_carbon']; ?></textarea>
	
	<label>Company History</label>
	<p>Do you have a unique 'sustainability story'? Let the readers know what made you green, why you started, how you did it, where you came from or any revelations or turning points that the company has had in its journey towards become a green leader.<p/>
	<textarea value="" name="dsc_history" id="dsc_history" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_history']; ?></textarea>
	
	<label>Energy Efficiency</label>
	<p>List any facts or data you have about your efforts to increase your business's energy efficiency or how your product or service directly helps to save power usage.<p/>
	<textarea value="" name="dsc_energy" id="dsc_energy" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_energy']; ?></textarea>				
	
	<label>Coprorate Sustainability</label>
	<p>List if your company is listed on the Dow Jones Sustainability Index, St James Ethics Centre, Global Reporting Initiative or other corporate sustainablility reporting initiatives.<p/>
	<textarea value="" name="dsc_sustainability" id="dsc_sustainability" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_sustainability']; ?></textarea>
	
	<label>Key Environmental Facts</label>
	<p>Summarise the main facts and features that makes your product or service uniquely sustainable.<p/>
	<textarea value="" name="dsc_envfacts" id="dsc_envfacts" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_envfacts']; ?></textarea>			
	
	<label>Materials Use</label>
	<p>Do you use sustainable materials in the manufacturing of your products? Detail the main materials that are used in the product, what makes them sustainable and where they are sourced from.<p/>
	<textarea value="" name="dsc_materials" id="dsc_materials" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_materials']; ?></textarea>			
	
	<label>Social Responsibility</label>
	<p>What does your company do that helps the employees, foreign workers and surrounding society? How does your company help society work better as a whole? <p/>
	<textarea value="" name="dsc_social" id="dsc_social" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_social']; ?></textarea>				
	
	<label>Sustainable Packaging</label>
	<p>Do you use sustainable packaging for your products? Detail what the packaging is made from, what makes it sustainable and where it is sourced.<p/>
	<textarea value="" name="dsc_packaging" id="dsc_packaging" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_packaging']; ?></textarea>				
	
	<label>Recyclability After Use</label>
	<p>Can your product be easily recycled, re-used or composted after it has finished it's life'? Detail how the product is recyclabled after use, what can it be recycled into and some basic instructions explaining how the user can ensure the product gets to the recycling facility.<p/>
	<textarea value="" name="dsc_recyclability" id="dsc_recyclability" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_recyclability']; ?></textarea>
	
	<label>Recycled Content</label>
	<p>Does your product include any recycled content? Detail the amount of recycled content, where the recycled material is sourced from and also the source of any virgin material that is mixed with the recycled material. <p/>
	<textarea value="" name="dsc_recycledcontent" id="dsc_recycledcontent" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_recycledcontent']; ?></textarea>
	
	<label>Water Saving</label>
	<p>List any facts or data you have about your efforts to reduce water usage in your business processes or how your product or service directly helps to save water.<p/>
	<textarea value="" name="dsc_water" id="dsc_water" style="width:470px" rows="6"><?php echo $form_data['data']['dsc_water']; ?></textarea>
	
	<input type="submit" name="save-fwd" value="Save & Continue" />
	<input type="submit" name="skip-bck" class="secondary" value="Back" />
	<input type="submit" name="clear-slf" class="secondary" value="Clear" />
	<input type="submit" name="reset" class="secondary" value="Start Over" />

	<input type="hidden" name="gp_previouslink" id="gp_previouslink" value="<?php echo $gp_previouslink; ?>" />
	<input type="text" name="gp_robots" id="gp_robots" value="Humans only!" style="display:none;" />
	<input type="hidden" name="gp_self" value="1" />
	
	<div class="clear"></div>
</form>
<?php } ?>
	
<?php if (current_page() == 'list-your-business-3') { ?>
<h2 class="green">Third Party Environmental Certifications, Ratings and Eco Labels</h2>
	
<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	
	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>
		
	<h3>Global Eco Certifications</h3>					
	<div class="even-2col">	
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href="http://www.thegreepages.com.au/marinestewardshipcouncil/"><img src="media/ecocert/marine.jpg" alt="Marine Stewardship Council" /></a>
				</div>
				<p>Marine Stewardship Council</p>
				<input type="checkbox" name="ecocerts[]" value="29" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/fsc.jpg" alt="FSC" /></a>
				</div>
				<p>Forest Stewardship Council: Chain of Custody</p>
				<input type="checkbox" name="ecocerts[]" value="18" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/energystar.jpg" alt="Energy Star" /></a>
				</div>
				<p>Energy Star</p>
				<input type="checkbox" name="ecocerts[]" value="15" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/fsc.jpg" alt="FSC" /></a>
				</div>
				<p>Forest Stewardship Council: Forest Management</p>
				<input type="checkbox" name="ecocerts[]" value="19" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/iso14.jpg" alt="ISO 140001" /></a>
				</div>
				<p>ISO 140001</p>
				<input type="checkbox" name="ecocerts[]" value="28" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/rainforestall.jpg" alt="Rainforest Alliance" /></a>
				</div>
				<p>Rainforest Alliance</p>
				<input type="checkbox" name="ecocerts[]" value="" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/ecotourism.jpg" alt="Eco Tourism" /></a>
				</div>
				<p>Eco Certified Tourism</p>
				<input type="checkbox" name="ecocerts[]" value="8" />
				<a href="" class="moreinfo">More info</a>
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/ifoam.jpg" alt="ifoam" /></a>
				</div>
				<p>International Federation of Organic Agriculture Movements</p>
				<input type="checkbox" name="ecocerts[]" value="27" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/fairtrade.jpg" alt="Fair Trade Logo" /></a>
				</div>
				<p>Fair Trade Association</p>
				<input type="checkbox" name="ecocerts[]" value="17" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/cfree.jpg" alt="Chlorine Free" /></a>
				</div>
				<p>Totally Chlorine Free</p>
				<input type="checkbox" name="ecocerts[]" value="" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/greenglobecommunity.jpg" alt="Green Globe Community Standard" /></a>
				</div>
				<p>Green Globe Community Standard</p>
				<input type="checkbox" name="ecocerts[]" value="22" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/greenglobecompany.jpg" alt="Green Globe Company Standard" /></a>
				</div>
				<p>Green Globe Company Standard</p>
				<input type="checkbox" name="ecocerts[]" value="23" />
			</div>
		</div>
		<div class="clear"></div>
	</div>			
		
	<h3>Australian Certifications</h3>	
	<div class="even-2col">					
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/aco2.jpg" alt="Australian Certified Organic" /></a>
				</div>
				<p>Australian Certified Organic</p>
				<input type="checkbox" name="ecocerts[]" value="1" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/geca.jpg" alt="Good Environmental Choice" /></a>
				</div>
				<p>Good Environmental Choice</p>
				<input type="checkbox" name="ecocerts[]" value="21" />
			</div>
		</div>	
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/climatefriendly.jpg" alt="Climate Friendly" /></a>
				</div>
				<p>Climate Friendly</p>
				<input type="checkbox" name="ecocerts[]" value="5" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/nasaa.jpg" alt="NASAA" /></a>
				</div>
				<p>National Association for Sustainable Agriculture Australia</p>
				<input type="checkbox" name="ecocerts[]" value="30" />
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<h3>Australian Star Rating Systems</h3>	
	<div class="even-2col">	
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/energystar3.jpg" alt="Energy Rating 3 Star" /></a>
				</div>
				<p>Energy Rating 3 Star</p>
				<input type="checkbox" name="ecocerts[]" value="9" />
			</div>
		</div>
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/energystar4.jpg" alt="Energy Rating 4 Star" /></a>
				</div>
				<p>Energy Rating 4 Star</p>
				<input type="checkbox" name="ecocerts[]" value="10" />
			</div>
		</div>
		<div class="colA">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/energystar5.jpg" alt="Energy Rating 5 Star" /></a>
				</div>
				<p>Energy Rating 5 Star</p>
				<input type="checkbox" name="ecocerts[]" value="12" />
			</div>
		</div>		
		<div class="colB">
			<div class="ecocert-panel">
				<div class="ecocert-logo">
					<a href=""><img src="media/ecocert/waterstar5.jpg" alt="Water Efficiency Rating 1 Star" /></a>
				</div>
				<p>Water Efficiency Rating 1 Star</p>
				<input type="checkbox" name="ecocerts[]" value="" />
			</div>
		</div>
		<div class="clear"></div>
	</div>

	<input type="submit" name="save-fwd" value="Save & Continue" />
	<input type="submit" name="skip-bck" class="secondary" value="Back" />
	<input type="submit" name="clear-slf" class="secondary" value="Clear" />
	<input type="submit" name="reset" class="secondary" value="Start Over" />

	<input type="hidden" name="gp_previouslink" id="gp_previouslink" value="<?php echo $gp_previouslink; ?>" />
	<input type="text" name="gp_robots" id="gp_robots" value="Humans only!" style="display:none;" />
	<input type="hidden" name="gp_self" value="1" />
	
	<div class="clear"></div>
</form>
<?php } ?>

<?php if (current_page() == 'list-your-business-4') { ?>
<h2 class="green">Upload company media</h2>

<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php breadcrumbs(); ?>
	
	<?php errors(); ?>
</form>

<div id="fileupload">
    <form action="<?php echo GP_PLUGIN_URL; ?>/gp-fileupload.php" method="POST" enctype="multipart/form-data">
        <div class="fileupload-buttonbar">

            <label class="fileinput-button">
                <span>Add files...</span>
                <input type="file" name="dir_images[]" multiple>
            </label>
            <button type="submit" class="start">Start upload</button>
            <button type="reset" class="cancel">Cancel upload</button>
            <button type="button" class="delete">Delete files</button>

        </div>
    </form>
    <div class="fileupload-content">
        <table class="files"></table>
        <div class="fileupload-progressbar"></div>
    </div>
</div>
<script id="template-upload" type="text/x-jquery-tmpl">
    <tr class="template-upload{{if error}} ui-state-error{{/if}}">
        <td class="preview"></td>
        <td class="name">${name}</td>
        <td class="size">${sizef}</td>
        {{if error}}
            <td class="error" colspan="2">Error:
                {{if error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else}}${error}
                {{/if}}
            </td>
        {{else}}
            <td class="progress"><div></div></td>
            <td class="start"><button>Start</button></td>
        {{/if}}
        <td class="cancel"><button>Cancel</button></td>
    </tr>
</script>
<script id="template-download" type="text/x-jquery-tmpl">
    <tr class="template-download{{if error}} ui-state-error{{/if}}">
        {{if error}}
            <td></td>
            <td class="name">${name}</td>
            <td class="size">${sizef}</td>
            <td class="error" colspan="2">Error:
                {{if error === 1}}File exceeds upload_max_filesize (php.ini directive)
                {{else error === 2}}File exceeds MAX_FILE_SIZE (HTML form directive)
                {{else error === 3}}File was only partially uploaded
                {{else error === 4}}No File was uploaded
                {{else error === 5}}Missing a temporary folder
                {{else error === 6}}Failed to write file to disk
                {{else error === 7}}File upload stopped by extension
                {{else error === 'maxFileSize'}}File is too big
                {{else error === 'minFileSize'}}File is too small
                {{else error === 'acceptFileTypes'}}Filetype not allowed
                {{else error === 'maxNumberOfFiles'}}Max number of files exceeded
                {{else error === 'uploadedBytes'}}Uploaded bytes exceed file size
                {{else error === 'emptyResult'}}Empty file upload result
                {{else}}${error}
                {{/if}}
            </td>
        {{else}}
            <td class="preview">
                {{if thumbnail_url}}
                    <a href="${url}" target="_blank"><img src="${thumbnail_url}"></a>
                {{/if}}
            </td>
            <td class="name">
                <a href="${url}"{{if thumbnail_url}} target="_blank"{{/if}}>${name}</a>
            </td>
            <td class="size">${sizef}</td>
            <td colspan="2"></td>
        {{/if}}
        <td class="delete">
            <button data-type="${delete_type}" data-url="${delete_url}">Delete</button>
        </td>
    </tr>
</script>

<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	
	<input type="submit" name="save-fwd" value="Save & Continue" />
	<input type="submit" name="skip-bck" class="secondary" value="Back" />
	<input type="submit" name="reset" class="secondary" value="Start Over" />

	<input type="hidden" name="gp_previouslink" id="gp_previouslink" value="<?php echo $gp_previouslink; ?>" />
	<input type="text" name="gp_robots" id="gp_robots" value="Humans only!" style="display:none;" />
	<input type="hidden" name="gp_self" value="1" />
	
	<div class="clear"></div>
</form>

<?php } ?>

<?php if (current_page() == 'list-your-business-6') { ?>

<h2 class="green">Thank You!</h2>

<form name="gp_drinquiries_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
	<?php breadcrumbs(); ?>

	<p>Thank you! You're directory listing has been successfully submitted. You will contacted shortly. Please allow a few days for orders to be processed.</p>

	<input type="submit" name="reset" value="New order" />
</form>

<?php } ?>