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
      <td align="center" colspan="2"><span style="color: #FF0000; font-size: 11px;">Your email notifications have been changed successfully</span></td>
    </tr>
    <?php elseif($errmsg!=""): ?>
    <tr>
      <td align="center" colspan="2"><span style="color: #FF0000; font-size: 11px;"><?php echo $errmsg;?></span></td>
    </tr>
    <?php endif;?>
           
     <tr>
    	<td>
    	    <input type="radio" name="daily_email" id="daily_email" 
    	           value="<?php echo esc_attr( get_the_author_meta( 'daily_email', $userdata->ID ) ); ?>" checked />
            <strong>Daily: 'The Green Laser'</strong> Get notified immediately of news, events and projects happening near you</td>
    <br />     
    </tr> 
    
    <tr>
    	<td>
    		<input type="radio" name="weekly_email" id="weekly_email" value="<?php echo esc_attr( get_the_author_meta( 'weekly_email', $userdata->ID ) ); ?>"  />
        	<strong>Weekly: 'The Green Razor'</strong> The best of your environmental movement in a weekly email
        </td>
    </tr>
    <br />
    
    <tr>
       <td>
     		<input type="radio" name="monthly_email" id="monthly_email" value="<?php echo esc_attr( get_the_author_meta( 'monthly_email', $userdata->ID ) ); ?>"  />
        	<strong>Monthly: 'The Green Phaser'</strong> The best of the Green Pages Community of the month
      	</td>
    </tr>
        
    <tr>
      <td align="center" colspan="2"><input type="submit" value="Update" /></td>
    </tr>
  </table>
  <input type="hidden" name="action" value="update" />
</form>