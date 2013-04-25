<?php
function wppb_displayAddons(){
?>
	<form method="post" action="options.php#add-ons">
		<?php $wppb_addonOptions = get_option('wppb_addon_settings'); ?>
		<?php settings_fields('wppb_addon_settings'); ?>
		
		
		<h2><?php _e('Activate/Deactivate Addons', 'profilebuilder');?></h2>
		<h3><?php _e('Activate/Deactivate Addons', 'profilebuilder');?></h3>
		<table id="wp-list-table widefat fixed pages" cellspacing="0">
			<thead>
				<tr>
					<th id="manage-column" id="addonHeader" scope="col"><?php _e('Name/Description', 'profilebuilder');?></th>
					<th id="manage-column" scope="col"><?php _e('Status', 'profilebuilder');?></th>
				</tr>
			</thead>
				<tbody>
					<tr>  
						<td id="manage-columnCell"><?php _e('User-Listing', 'profilebuilder');?></td> 
						<td> 
							<input type="radio" name="wppb_addon_settings[wppb_userListing]" value="show" <?php if ($wppb_addonOptions['wppb_userListing'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_userListing]" value="hide" <?php if ($wppb_addonOptions['wppb_userListing'] == 'hide') echo 'checked';?>/><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('Custom Redirects', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_customRedirect]" value="show" <?php if ($wppb_addonOptions['wppb_customRedirect'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_customRedirect]" value="hide" <?php if ($wppb_addonOptions['wppb_customRedirect'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
					<tr>  
						<td id="manage-columnCell"><?php _e('reCAPTCHA', 'profilebuilder');?></td> 
						<td id="manage-columnCell"> 
							<input type="radio" name="wppb_addon_settings[wppb_reCaptcha]" value="show" <?php if ($wppb_addonOptions['wppb_reCaptcha'] == 'show') echo 'checked';?> /><font size="1"><?php _e('Active', 'profilebuilder');?></font><span style="padding-left:20px"></span>
							<input type="radio" name="wppb_addon_settings[wppb_reCaptcha]" value="hide" <?php if ($wppb_addonOptions['wppb_reCaptcha'] == 'hide') echo 'checked';?> /><font size="1"><?php _e('Inactive', 'profilebuilder');?></font>
						</td> 
					</tr>
				</tbody>
		</table>
		<div align="right">
			<input type="hidden" name="action" value="update" />
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
			</p>
			</form>
		</div>
<?php
}
?>

<?php

function wppb_userListing(){
?>
	<style>
	  slider {}
	</style>
	<style>
	  slider2 {}
	</style>

<?php
	//first thing we will have to do is create a default settings on first-time run of the addon
	$customUserListingSettings = get_option('customUserListingSettings','not_found');
	$wppbFetchArray = get_option('wppb_custom_fields');
	if ($customUserListingSettings == 'not_found'){
		$customUserListingSettingsArg = array(
										 'sortingOrder'=> 'asc',
										 'sortingNumber'=> '25',
										 'avatarSize' => 16,
										 'allUserlisting' => '',
										 'singleUserlisting' => '');
		add_option('customUserListingSettings', $customUserListingSettingsArg);
	}
?>
	<form method="post" action="options.php#wppb_userListing" name="userlistingForm">
		<?php $customUserListingSettings = get_option('customUserListingSettings'); ?>
		<?php settings_fields('customUserListingSettings'); ?>
		
		<h2><?php _e('User-Listing', 'profilebuilder');?></h2>
		<h3><?php _e('User-Listing', 'profilebuilder');?></h3>
		<p>
		<?php _e('To create a page containing the users registered to this current site/blog, insert the following shortcode in a (blank) page: ', 'profilebuilder');?><strong>[wppb-list-users]</strong>.<br/>
		<?php _e('For instance, to create a userlisting shortcode listing only the editors and authors, visible to only the users currently logged in, you would use:', 'profilebuilder');?> <strong>[wppb-list-users visibility="restricted" roles="editor,author"]</strong>.
		</p><br/>
		
		<strong><?php _e('General Settings','profilebuilder');?></strong>
		<p>
		<?php _e('These settings are applied to the front-end userlisting.','profilebuilder');?>
		</p>
		
		
		<table class="sortingTable">
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Number of Users/Page: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingNumberSelect" name="customUserListingSettings[sortingNumber]">
						<option <?php if ($customUserListingSettings['sortingNumber'] == '5'){ echo 'selected="yes" ';} ?> value="5">5</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '10'){ echo 'selected="yes" ';} ?> value="10">10</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '25'){ echo 'selected="yes" ';} ?> value="25">25</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '50'){ echo 'selected ="yes" ';} ?> value="50">50</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '100'){ echo 'selected ="yes" ';} ?> value="100">100</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '150'){ echo 'selected ="yes" ';} ?> value="150">150</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '200'){ echo 'selected ="yes" ';} ?> value="200">200</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '250'){ echo 'selected ="yes" ';} ?> value="250">250</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '500'){ echo 'selected ="yes" ';} ?> value="500">500</option>
						<option <?php if ($customUserListingSettings['sortingNumber'] == '1000'){ echo 'selected ="yes" ';} ?> value="1000">1000</option>
					</select>
				</td>
			</tr>
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Default Sorting Order: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingOrderSelect" name="customUserListingSettings[sortingOrder]">
						<option <?php if ($customUserListingSettings['sortingOrder'] == 'asc'){ echo 'selected="yes" ';} ?> value="asc">Ascending</option>
						<option <?php if ($customUserListingSettings['sortingOrder'] == 'desc'){ echo 'selected ="yes" ';} ?> value="desc">Descending</option>
					</select>
				</td>
			</tr>
		</table>
		<br/>
		
		<strong><?php _e('"All-Userlisting" Template','profilebuilder');?></strong>
		<p>
		<?php _e('With the userlisting templates you can customize the look, feel and information listed by the shortcode.','profilebuilder');?><br/>
		<?php _e('The "All Users Listing" template is used to list all users. It\'s displayed on each page access where the shortcode is present.','profilebuilder');?>
		</p>
		<table class="fieldTable">
			<tr class="sortingTableRow">
				<td class="sortingTableCell1"><span style="padding-left:20px"> &rarr; <?php _e('Avatar size: ', 'profilebuilder');?></span></td>
				<td class="sortingTableCell2">
					<select id="sortingNumberSelect" name="customUserListingSettings[avatarSize]">
						<?php
							for($i=20; $i<=200; $i++){
								echo '<option ';
								if ($customUserListingSettings['avatarSize'] == $i)
									echo 'selected="yes" ';
							echo ' value="'.$i.'">'.$i.'</option>';
							}
						?>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "Sort By" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertSortField" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<optgroup label="Default WordPress Fields">
							<option value="%%sort_user_name%%">%%sort_user_name%%</option>
							<option value="%%sort_first_last_name%%">%%sort_first_last_name%%</option>
							<option value="%%sort_email%%">%%sort_email%%</option>
							<option value="%%sort_website%%">%%sort_website%%</option>
							<option value="%%sort_biographical_info%%">%%sort_biographical_info%%</option>
							<option value="%%sort_registration_date%%">%%sort_registration_date%%</option>
							<option value="%%sort_first_name%%">%%sort_first_name%%</option>
							<option value="%%sort_last_name%%">%%sort_last_name%%</option>
							<option value="%%sort_display_name%%">%%sort_display_name%%</option>
							<option value="%%sort_number_of_posts%%">%%sort_number_of_posts%%</option>
						</optgroup>
						<optgroup label="Custom Fields">
							<?php
							foreach($wppbFetchArray as $key => $value)
								echo '<option value="%%sort_'.$value['item_title'].'%%">%%sort_'.$value['item_title'].'%%</option>';
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "User-Meta" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertUserMetaField" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<optgroup label="Default WordPress Fields">
							<option value="%%meta_user_name%%">%%meta_user_name%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_first_last_name%%">%%meta_first_last_name%%</option>
							<option value="%%meta_role%%">%%meta_role%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_registration_date%%">%%meta_registration_date%%</option>
							<option value="%%meta_first_name%%">%%meta_first_name%%</option>
							<option value="%%meta_last_name%%">%%meta_last_name%%</option>
							<option value="%%meta_nickname%%">%%meta_nickname%%</option>
							<option value="%%meta_display_name%%">%%meta_display_name%%</option>
							<option value="%%meta_website%%">%%meta_website%%</option>
							<option value="%%meta_biographical_info%%">%%meta_biographical_info%%</option>
							<option value="%%meta_number_of_posts%%">%%meta_number_of_posts%%</option>
						</optgroup>
						<optgroup label="Custom Fields">
							<?php
							foreach($wppbFetchArray as $key => $value)
								echo '<option value="%%meta_'.$value['item_title'].'%%">%%meta_'.$value['item_title'].'%%</option>';
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert Extra Functions:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertExtraFunction" onchange="wppb_insertAtCursor(allUserlisting,this.value)">
						<option></option>
						<option value="%%extra_more_info_link%%">%%extra_more_info_link%%</option>
						<option value="%%extra_while_users%%">%%extra_while_users%%</option>
						<option value="%%extra_end_while_users%%">%%extra_end_while_users%%</option>
						<option value="%%extra_search_all_fields%%">%%extra_search_all_fields%%</option>
						<option value="%%extra_avatar_or_gravatar%%">%%extra_avatar_or_gravatar%%</option>
					</select>
				</td>
			</tr>
			<tr class="fieldTableExtraRow">
				<td class="fieldTableCell4" colspan="2">
					<?php echo '<button type="button" name="allUserlistingButton" id="allUserlistingButton" class="button">'. __('Show/Hide Default "All-Userlisting" Code','profilebuilder').'</button>';?>
					<slider><br/><br/>
						<?php echo '<b>'.__('If you wish to use a default userlisting, just copy the following code and paste it in the textarea below:', 'profilebuilder').'</b>';?><br/><br/>
						&lt;table id="userListingTable" cellspacing="0"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;thead&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading1" scope="col" colspan="2"&gt;&lt;span&gt;%%sort_user_name%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading2" scope="col"&gt;&lt;span&gt;%%sort_first_last_name%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading3" scope="col"&gt;&lt;span&gt;Role&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading4" scope="col"&gt;&lt;span&gt;%%sort_number_of_posts%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading5" scope="col"&gt;&lt;span&gt;%%sort_registration_date%%&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;th class="userListingTableHeading6" scope="col"&gt;&lt;span&gt;More&lt;/span&gt;&lt;/th&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/thead&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tbody&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%%extra_while_users%%<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="tableRow" onmouseover="style.backgroundColor='grey'; style.color='white';" onmouseout="style.backgroundColor=''; style.color='';"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="avatarColumn"&gt;%%extra_avatar_or_gravatar%%&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="loginNameColumn"&gt;&lt;span&gt;%%meta_user_name%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="nameColumn"&gt;&lt;span&gt;%%meta_first_last_name%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="roleColumn"&gt;&lt;span&gt;%%meta_role%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="postsColumn"&gt;&lt;span&gt;%%meta_number_of_posts%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="signUpColumn"&gt;&lt;span&gt;%%meta_registration_date%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="moreInfoColumn"&gt;&lt;span&gt;%%extra_more_info_link%%&lt;/span&gt;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;%%extra_end_while_users%%<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tbody&gt;<br/>
						&lt;/table&gt;<br/>
					</slider>
					<script>
						jQuery("slider").hide();
						jQuery("#allUserlistingButton").click(function () {
						  jQuery("slider").slideToggle("slow");
						});
					</script>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell3" colspan="2">
					<textarea id="allUserlisting" name="customUserListingSettings[allUserlisting]" wrap="off" onkeydown="return wppb_catchTab(this,event)"><?php echo $customUserListingSettings['allUserlisting'];?></textarea>
				</td>
			</tr>
		</table>	
		<br/><br/><br/><br/>
		
		<strong><?php _e('"Single-Userlisting" Template','profilebuilder');?></strong>
		<p>
		<?php _e('With the userlisting templates you can customize the look, feel and information listed by the shortcode.','profilebuilder');?><br/>
		<?php _e('The "Single User Listing" template is used to list an individual user. It\'s displayed when clickin on the "more info" link.','profilebuilder');?>
		</p>
		<table class="sortingTable">
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert "User-Meta" Field:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertUserMetaField" onchange="wppb_insertAtCursor(singleUserlisting,this.value)">
						<option></option>
						<optgroup label="Default WordPress Fields">
							<option value="%%meta_user_name%%">%%meta_user_name%%</option>
							<option value="%%meta_first_last_name%%">%%meta_first_last_name%%</option>
							<option value="%%meta_email%%">%%meta_email%%</option>
							<option value="%%meta_registration_date%%">%%meta_registration_date%%</option>
							<option value="%%meta_first_name%%">%%meta_first_name%%</option>
							<option value="%%meta_last_name%%">%%meta_last_name%%</option>
							<option value="%%meta_nickname%%">%%meta_nickname%%</option>
							<option value="%%meta_display_name%%">%%meta_display_name%%</option>
							<option value="%%meta_website%%">%%meta_website%%</option>
							<option value="%%meta_biographical_info%%">%%meta_biographical_info%%</option>
							<option value="%%meta_aim%%">%%meta_aim%%</option>
							<option value="%%meta_yim%%">%%meta_yim%%</option>
							<option value="%%meta_jabber%%">%%meta_jabber%%</option>
							<option value="%%meta_number_of_posts%%">%%meta_number_of_posts%%</option>
						</optgroup>
						<optgroup label="Custom Fields">
							<?php
							foreach($wppbFetchArray as $key => $value)
								echo '<option value="%%meta_'.$value['item_title'].'%%">%%meta_'.$value['item_title'].'%%</option>';
							?>
						</optgroup>
						<optgroup label="Custom Fields(Description)">
							<?php
							foreach($wppbFetchArray as $key => $value)
								echo '<option value="%%meta_description_'.$value['item_title'].'%%">%%meta_description_'.$value['item_title'].'%%</option>';
							?>
						</optgroup>
					</select>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell1">
					<span style="padding-left:20px"> &rarr; <?php _e('Insert Extra Functions:', 'profilebuilder'); ?></span>
				</td>
				<td class="fieldTableCell2">
					<select id="insertExtraFunction" onchange="wppb_insertAtCursor(singleUserlisting,this.value)">
						<option></option>
						<option value="%%extra_go_back_link%%">%%extra_go_back_link%%</option>

					</select>
				</td>
			</tr>
			<tr class="fieldTableExtraRow">
				<td class="fieldTableCell4" colspan="2">
					<?php echo '<button type="button" id="singleUserlistingButton" class="button">'. __('Show/Hide Default "Single-Userlisting" Code','profilebuilder').'</button>';?>
					<slider2><br/><br/>
						<?php echo '<b>'.__('If you wish to use a default userlisting, just copy the following code and paste it in the textarea below:', 'profilebuilder').'</b>';?><br/><br/>
						%%extra_go_back_link%%<br/>
						&lt;table id="userListingDisplayTable"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="header"&gt;&lt;strong&gt;Name&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Username:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_user_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;First Name:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_first_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Last Name:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_last_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Nickname:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_nickname%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Display name publicly as:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_display_name%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="header"&gt;&lt;strong&gt;Contact Info&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Website:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_website%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell1" colspan="2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="header"&gt;&lt;strong&gt;About Yourself&lt;/strong&gt;&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;tr class="userListingDisplayTableRow"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell2"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputName"&gt;Biographical Info:&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;td class="userListingDisplayTableCell3"&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;span id="inputValue"&gt;%%meta_biographical_info%%&lt;/span&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/td&gt;<br/>
						&nbsp;&nbsp;&nbsp;&nbsp;&lt;/tr&gt;<br/>
						&lt;/table&gt;<br/>
						%%extra_go_back_link%%<br/>
					</slider2>
					<script>
						jQuery("slider2").hide();
						jQuery("#singleUserlistingButton").click(function () {
						  jQuery("slider2").slideToggle("slow");
						});
					</script>
				</td>
			</tr>
			<tr class="fieldTableRow">
				<td class="fieldTableCell3" colspan="2">
					<textarea id="singleUserlisting" name="customUserListingSettings[singleUserlisting]" wrap="off" onkeydown="return wppb_catchTab(this,event)"><?php echo $customUserListingSettings['singleUserlisting'];?></textarea> 
				</td>
			</tr>
		</table>
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php
}


function wppb_customRedirect(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	$customRedirectSettings = get_option('customRedirectSettings','not_found');
		if ($customRedirectSettings == 'not_found'){
			$customRedirectSettingsArg = array( 'afterRegister' => 'no', 
												'afterLogin'=> 'no',
												'afterRegisterTarget' => '', 
												'afterLoginTarget'=> '',
												'loginRedirect' => 'no',
												'loginRedirectLogout' => 'no',
												'registerRedirect' => 'no',
												'recoverRedirect' => 'no',
												'dashboardRedirect' => 'no',
												'loginRedirectTarget' => '', 
												'loginRedirectTargetLogout' => '', 
												'registerRedirectTarget'=> '',
												'recoverRedirectTarget' => '', 
												'dashboardRedirectTarget' => '');
			add_option('customRedirectSettings', $customRedirectSettingsArg);
		}
?>
	
	<form method="post" action="options.php#wppb_customRedirect">
		<?php $customRedirectSettings = get_option('customRedirectSettings'); ?>
		<?php settings_fields('customRedirectSettings'); ?>

		
		
		<h2><?php _e('Custom Redirects', 'profilebuilder');?></h2>
		<h3><?php _e('Custom Redirects', 'profilebuilder');?></h3>


		<p>
			<?php _e('Redirects on custom page requests:', 'profilebuilder');?>
		</p>
		
		<table class="redirectTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Action', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Redirect', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('URL', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('After Registration:', 'profilebuilder');?></td>
				<td>
					<input type="radio" name="customRedirectSettings[afterRegister]" value="yes" <?php if ($customRedirectSettings['afterRegister'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[afterRegister]" value="no" <?php if ($customRedirectSettings['afterRegister'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2"><input name="customRedirectSettings[afterRegisterTarget]" class="redirectFirstInput" type="text" value="<?php echo $customRedirectSettings['afterRegisterTarget'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('After Login (*):', 'profilebuilder');?></td>
				<td>
					<input type="radio" name="customRedirectSettings[afterLogin]" value="yes" <?php if ($customRedirectSettings['afterLogin'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[afterLogin]" value="no" <?php if ($customRedirectSettings['afterLogin'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2"><input name="customRedirectSettings[afterLoginTarget]" class="redirectSecondInput" type="text" value="<?php echo $customRedirectSettings['afterLoginTarget'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1">
					<?php _e('Recover Password (**)', 'profilebuilder');?>
				</td>
				<td>
					<input type="radio" name="customRedirectSettings[recoverRedirect]" value="yes" <?php if ($customRedirectSettings['recoverRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[recoverRedirect]" value="no" <?php if ($customRedirectSettings['recoverRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="redirectTableCell2">
					<input name="customRedirectSettings[recoverRedirectTarget]" class="redirectThirdInput" type="text" value="<?php echo $customRedirectSettings['recoverRedirectTarget'];?>" />
				</td>
			</tr>
		</table>
		<?php echo '<font size="1" color="grey">(*) '.__('Does not.', 'profilebuilder').' </font>'; ?>
		<?php echo '<font size="1" color="grey">(**) '.__('When activated this feature will redirect the user on both the default Wordpress password recovery page and the "Lost password?" link used by Profile Builder on the front-end login page.', 'profilebuilder').' </font>'; ?>
		
		<br/><br/><br/>
		
		<p>
			<?php _e('Redirects on default WordPress page requests:', 'profilebuilder');?>
		</p>
		
		<table class="disableLoginAndRegistrationTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Requested WP Page', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Redirect', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('URL', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Login Page(*)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[loginRedirect]" value="yes" <?php if ($customRedirectSettings['loginRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[loginRedirect]" value="no" <?php if ($customRedirectSettings['loginRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[loginRedirectTarget]" class="loginRedirectTarget" type="text" value="<?php echo $customRedirectSettings['loginRedirectTarget'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Logout Page(**)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[loginRedirectLogout]" value="yes" <?php if ($customRedirectSettings['loginRedirectLogout'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[loginRedirectLogout]" value="no" <?php if ($customRedirectSettings['loginRedirectLogout'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[loginRedirectTargetLogout]" class="loginRedirectTarget" type="text" value="<?php echo $customRedirectSettings['loginRedirectTargetLogout'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Register Page', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[registerRedirect]" value="yes" <?php if ($customRedirectSettings['registerRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[registerRedirect]" value="no" <?php if ($customRedirectSettings['registerRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[registerRedirectTarget]" class="registerRedirectTarget" type="text" value="<?php echo $customRedirectSettings['registerRedirectTarget'];?>" />
				</td>
			</tr>
			<tr class="disableLoginAndRegistrationTableRow">
				<td class="disableLoginAndRegistrationTableCell1">
					<?php _e('Default WP Dashboard (***)', 'profilebuilder');?>
				</td>
				<td class="disableLoginAndRegistrationTableCell2">
					<input type="radio" name="customRedirectSettings[dashboardRedirect]" value="yes" <?php if ($customRedirectSettings['dashboardRedirect'] == 'yes') echo 'checked';?> /><font size="1"><?php _e('Yes', 'profilebuilder');?></font><span style="padding-left:20px"></span>
					<input type="radio" name="customRedirectSettings[dashboardRedirect]" value="no" <?php if ($customRedirectSettings['dashboardRedirect'] == 'no') echo 'checked';?>/><font size="1"><?php _e('No', 'profilebuilder');?></font>
				</td>
				<td class="disableLoginAndRegistrationTableCell3">
					<input name="customRedirectSettings[dashboardRedirectTarget]" class="dashboardRedirectTarget" type="text" value="<?php echo $customRedirectSettings['dashboardRedirectTarget'];?>" />
				</td>
			</tr>
		</table>
		<?php echo '<font size="1" color="grey">(*) '.__('Before login. Works best if used in conjuction with "After logout".', 'profilebuilder').' </font><br/>'; ?>
		<?php echo '<font size="1" color="grey">(**) '.__('After logout. Works best if used in conjuction with "Before login".', 'profilebuilder').' </font><br/>'; ?>
		<?php echo '<font size="1" color="grey">(***) '.__('Redirects every user-role EXCEPT the ones with administrator privilages (can manage options).', 'profilebuilder').' </font>'; ?>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php	
}



function wppb_reCaptcha(){
	//first thing we will have to do is create a default settings on first-time run of the addon
	$reCaptchaSettings = get_option('reCaptchaSettings','not_found');
	if ($reCaptchaSettings == 'not_found'){
		$reCaptchaSettings = array('publicKey' => '', 'privateKey' => '');
		add_option('reCaptchaSettings', $reCaptchaSettings);
	}
?>
	
	<form method="post" action="options.php#wppb_reCaptcha">
		<?php $reCaptchaSettings = get_option('reCaptchaSettings'); ?>
		<?php settings_fields('reCaptchaSettings'); ?>

		
		
		<h2><?php _e('reCAPTCHA', 'profilebuilder');?></h2>
		<h3><?php _e('reCAPTCHA', 'profilebuilder');?></h3>


		<p>
			<?php _e('Adds a reCAPTCHA form on the registration page created in the front-end (only).', 'profilebuilder');?><br/>
			<?php _e('For this you must get a public and private key from Google:', 'profilebuilder');?> <a href="http://www.google.com/recaptcha" target="new">www.google.com/recaptcha</a>
		</p>
		
		<table class="redirectTable">
			<thead class="disableLoginAndRegistrationTableHead">
				<tr>
					<th class="manage-column" scope="col"><?php _e('Key', 'profilebuilder');?></th>
					<th class="manage-column" scope="col"><?php _e('Code', 'profilebuilder');?></th>
				</tr>
			</thead>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('Public Key:', 'profilebuilder');?></td>
				<td class="redirectTableCell2"><input name="reCaptchaSettings[publicKey]" class="reCaptchaSettingsPubK" type="password" value="<?php echo $reCaptchaSettings['publicKey'];?>" /></td>
			</tr>
			<tr class="redirectTableRow">
				<td class="redirectTableCell1"><?php _e('Private Key:', 'profilebuilder');?></td>
				<td class="redirectTableCell2"><input name="reCaptchaSettings[privateKey]" class="reCaptchaSettingsPriK" type="password" value="<?php echo $reCaptchaSettings['privateKey'];?>" /></td>
			</tr>
		</table>
	
	<div align="right">
		<input type="hidden" name="action" value="update" />
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /> 
		</p>
	</form>
	</div>
	
<?php
}