<?php
$profile_author = get_profile_author();
        
if ( !is_user_logged_in() || ( is_user_logged_in() && $user_id != $profile_author->ID) ) {
        wp_safe_redirect('/profile/' . $profile_author->user_nicename);
        return false;
}

global $wpdb;

$wpdb->hide_errors(); nocache_headers();
 
global $userdata; get_currentuserinfo();
 
if(!empty($_POST['action'])){
 
    require_once(ABSPATH . 'wp-admin/includes/user.php');
    require_once(ABSPATH . WPINC . '/registration.php');
 
    check_admin_referer('update-profile_' . $user_ID);
 
    $errors = edit_user($user_ID);
 
    if ( is_wp_error( $errors ) ) {
        foreach( $errors->get_error_messages() as $message )
            $errmsg = "$message";
    }
 
    if($errmsg == '')
    {
        do_action('personal_options_update',$user_ID);
        $d_url = $_POST['dashboard_url'];
        wp_redirect( get_option("siteurl").'?page_id='.$post->ID.'&updated=true' );
    }
    else{
        $errmsg = '<div class="box-red">' . $errmsg . '</div>';
        $errcolor = 'style="background-color:#FFEBE8;border:1px solid #CC0000;"';
 
    }
}
 
get_currentuserinfo();

?>

<form name="profile" action="" method="post" enctype="multipart/form-data">
  <?php wp_nonce_field('update-profile_' . $user_ID) ?>
  <input type="hidden" name="from" value="profile" />
  <input type="hidden" name="action" value="update" />
  <input type="hidden" name="checkuser_id" value="<?php echo $user_ID ?>" />
  <input type="hidden" name="dashboard_url" value="<?php echo get_option("dashboard_url"); ?>" />
  <input type="hidden" name="user_id" id="user_id" value="<?php echo $user_id; ?>" />
  <table width="100%" cellspacing="0" cellpadding="0" border="0">
  <?php if ( isset($_GET['updated']) ):
$d_url = $_GET['d'];?>
    <tr>
      <td align="center" colspan="2"><span style="color: #FF0000; font-size: 11px;">Your profile changed successfully</span></td>
    </tr>
    <?php elseif($errmsg!=""): ?>
    <tr>
      <td align="center" colspan="2"><span style="color: #FF0000; font-size: 11px;"><?php echo $errmsg;?></span></td>
    </tr>
    <?php endif;?>
    <tr>
        <td colspan="2" align="left"><h2>Update profile</h2></td>
    </tr>
    <tr>
      <td>Profile Photo</td>
      <td><input type="text" name="avatar" id="avatar" value="<?php echo $userdata->avatar ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td>First Name</td>
      <td><input type="text" name="first_name" id="first_name" value="<?php echo $userdata->first_name ?>" style="width: 150px;" /></td>
    </tr>
    <tr>
      <td>Last Name</td>
      <td><input type="text" name="last_name" class="mid2" id="last_name" value="<?php echo $userdata->last_name ?>" style="width: 150px;" /></td>
    </tr>
    
    <tr>
      <td>Nickname <span style="color: #F00">*</span></td>
      <td><input type="text" name="nick_name" class="mid2" id="nick_name" value="<?php echo $userdata->nick_name ?>" style="width: 300px;" /></td>
    </tr>
    
    <tr>
      <td>How would you like your name to display?<span style="color: #F00">*</span></td>
      <td><input type="text" name="display_name" class="mid2" id="display_name" value="<?php echo $userdata->display_name ?>" style="width: 300px;" /></td>
    </tr>
    
    <tr>
      <td>Email <span style="color: #F00">*</span></td>
      <td><input type="text" name="email" class="mid2" id="email" value="<?php echo $userdata->user_email ?>" style="width: 300px;" /></td>
    </tr>
    
     <tr>
        <td>Location</td><!--need to add google api here-->
        <td><input type="text" name="location" id="location" value="<?php echo esc_attr( get_the_author_meta( 'location', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    
    <tr>
      <td>New Password </td>
      <td><input type="password" name="pass1" class="mid2" id="pass1" value="" style="width: 100px;" /></td>
    </tr>
    <tr>
      <td>New Password Confirm </td>
      <td><input type="password" name="pass2" class="mid2" id="pass2" value="" style="width: 100px;" /></td>
    </tr>
    <tr>
      <td align="right" colspan="2"><span style="color: #F00">*</span> <span style="padding-right:40px;">mandatory fields</span></td>
    </tr>
    
    <tr><td colspan="2"><h3>Your 'green' identity</h3></td></tr>
    
     <tr>
        <td>My bio</td>
        <td><input type="text" name="description" id="description" value="<?php echo esc_attr( get_the_author_meta( 'description', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr> 
    
    <tr>
        <td>How I Would Change the World (in 50 words or less!)</td>
        <td><input type="text" name="bio_change" id="bio_change" value="<?php echo esc_attr( get_the_author_meta( 'bio_change', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    
     <tr>
        <td> Projects I Need Help With</td>
        <td><input type="text" name="bio_projects" id="bio_projects" value="<?php echo esc_attr( get_the_author_meta( 'bio_projects', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    
    <tr>
        <td>Green Stuff I'm Into</td><!--TAGS HERE!!!-->
        <td><input type="text" name="bio_stuff" id="bio_stuff" value="<?php echo esc_attr( get_the_author_meta( 'bio_stuff', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    
    <tr><td colspan="2"><h3>Contacts</h3></td></tr>
     <tr>
        <td>Website</td>
        <td><input type="text" name="user_url" id="user_url" value="<?php echo esc_attr( get_the_author_meta( 'user_url', $userdata->ID ) ); ?>" style="width: 400px;" /></td>
    </tr>
    <tr>
        <td>Facebook URL</td>
        <td><input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $userdata->ID ) ); ?>" style="width: 400px;" /></td>
    </tr>    
   <tr>
        <td>Linkedin URL</td>
        <td><input type="text" name="linkedin" id="linkedin" value="<?php echo esc_attr( get_the_author_meta( 'linkedin', $userdata->ID ) ); ?>" style="width: 400px;" /></td>
    </tr>
    <tr>
        <td>Twitter ID</td>
        <td><input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $userdata->ID ) ); ?>" style="width: 200px;" /></td>
    </tr>
    <tr>
        <td>Skype ID</td>
        <td><input type="text" name="skype" id="skype" value="<?php echo esc_attr( get_the_author_meta( 'skype', $userdata->ID ) ); ?>" style="width: 200px;" /></td>
    </tr>
    
    <tr><td colspan="2"><h3>Employment Details</h3></td></tr>
     <tr>
        <td>Job Title</td>
        <td><input type="text" name="employment_jobtitle" id="employment_jobtitle" value="<?php echo esc_attr( get_the_author_meta( 'employment_jobtitle', $userdata->ID ) ); ?>" style="width: 200px;" /></td>
    </tr>
     <tr>
        <td>Current Employer</td>
        <td><input type="text" name="employment_currentemployer" id="employment_currentemployer" value="<?php echo esc_attr( get_the_author_meta( 'employment_currentemployer', $userdata->ID ) ); ?>" style="width: 200px;" /></td>
    </tr>
    
    <tr>
      <td align="center" colspan="2"><input type="submit" value="Update" /></td>
    </tr>
  </table>
  <input type="hidden" name="action" value="update" />
</form>