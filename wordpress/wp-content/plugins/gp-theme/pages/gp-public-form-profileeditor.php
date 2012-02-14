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
        <td colspan="2" align="center"><h2>Update profile</h2></td>
    </tr>
    <tr>
      <td>First Name</td>
      <td><input type="text" name="first_name" id="first_name" value="<?php echo $userdata->first_name ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td>Last Name</td>
      <td><input type="text" name="last_name" class="mid2" id="last_name" value="<?php echo $userdata->last_name ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td>Email <span style="color: #F00">*</span></td>
      <td><input type="text" name="email" class="mid2" id="email" value="<?php echo $userdata->user_email ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td>New Password </td>
      <td><input type="password" name="pass1" class="mid2" id="pass1" value="" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td>New Password Confirm </td>
      <td><input type="password" name="pass2" class="mid2" id="pass2" value="" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td align="right" colspan="2"><span style="color: #F00">*</span> <span style="padding-right:40px;">mandatory fields</span></td>
    </tr>
    <tr><td colspan="2"><h3>Extra profile information</h3></td></tr>
    <tr>
        <td>Facebook URL</td>
        <td><input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Twitter</td>
        <td><input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Date Of Birth</td>
        <td><input type="text" name="dob" id="dob" value="<?php echo esc_attr( get_the_author_meta( 'dob', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Phone</td>
        <td><input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Address</td>
        <td><input type="text" name="address" id="address" value="<?php echo esc_attr( get_the_author_meta( 'address', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>City</td>
        <td><input type="text" name="city" id="city" value="<?php echo esc_attr( get_the_author_meta( 'city', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Province</td>
        <td><input type="text" name="province" id="province" value="<?php echo esc_attr( get_the_author_meta( 'province', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
        <td>Postal Code</td>
        <td><input type="text" name="postalcode" id="postalcode" value="<?php echo esc_attr( get_the_author_meta( 'postalcode', $userdata->ID ) ); ?>" style="width: 300px;" /></td>
    </tr>
    <tr>
      <td align="center" colspan="2"><input type="submit" value="Update" /></td>
    </tr>
  </table>
  <input type="hidden" name="action" value="update" />
</form>