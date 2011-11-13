<?php

add_action('wp_head', 'signuppageheaders');

function signuppageheaders() {
	echo "<meta name='robots' content='noindex,nofollow' />\n";
}

if ( !is_multisite() ) {
	wp_redirect( site_url('wp-login.php?action=register') );
	die();
}

if ( is_object( $wp_object_cache ) )
	$wp_object_cache->cache_enabled = false;

do_action( 'activate_header' );

function do_activate_header() {
	do_action( 'activate_wp_head' );
}
add_action( 'wp_head', 'do_activate_header' );

?>

<style>
	.message, .error {
		background-color: #ffebe8;
		border: 1px solid #cc0000;
		margin-bottom: 8px;
		padding: 6px;
		border-radius: 3px;
	}
	
	.message {
		background-color: #ffffe0;
		border: 1px solid #e6db55;
	}
</style>

<?php

if ( empty($_GET['key']) && empty($_POST['key']) ) { ?>

	<h3><?php _e('Activation Key Required') ?></h3>
	<form name="activateform" id="activateform" method="post" action="/activate">
		<p>
		    <label for="key"><?php _e('Activation Key:') ?></label>
		    <br /><input type="text" name="key" id="key" value="" size="50" />
		</p>
		<p class="submit">
		    <input id="submit" type="submit" name="Submit" class="submit" value="<?php esc_attr_e('Activate') ?>" />
		</p>
	</form>

<?php } else {

	$key = !empty($_GET['key']) ? $_GET['key'] : $_POST['key'];
	$result = wpmu_activate_signup($key);
	if ( is_wp_error($result) ) {
		if ( 'already_active' == $result->get_error_code() || 'blog_taken' == $result->get_error_code() ) {
		    $signup = $result->get_error_data();
			?>
			<h3><?php _e('Your account is now active!'); ?></h3>
			<?php
			echo '<p class="lead-in">';
			if ( $signup->domain . $signup->path == '' ) {
				printf( __('Your account has been activated. You may now <a href="%1$s">log in</a> to the site using your chosen username of &#8220;%2$s&#8221;.  Please check your email inbox at %3$s for your password and login instructions. If you do not receive an email, please check your junk or spam folder. If you still do not receive an email within an hour, you can <a href="%4$s">reset your password</a>.'), network_site_url( 'wp-login.php', 'login' ), $signup->user_login, $signup->user_email, network_site_url( 'wp-login.php?action=lostpassword', 'login' ) );
			} else {
				printf( __('Your site at <a href="%1$s">%2$s</a> is active. You may now log in to your site using your chosen username of &#8220;%3$s&#8221;.  Please check your email inbox at %4$s for your password and login instructions.  If you do not receive an email, please check your junk or spam folder.  If you still do not receive an email within an hour, you can <a href="%5$s">reset your password</a>.'), 'http://' . $signup->domain, $signup->domain, $signup->user_login, $signup->user_email, network_site_url( 'wp-login.php?action=lostpassword' ) );
			}
			echo '</p>';
		} else {
			?>
			<h3><?php _e('An error occurred during the activation'); ?></h3>
			<?php
		    echo '<p>'.$result->get_error_message().'</p>';
		}
	} else {
		extract($result);
		$url = get_blogaddress_by_id( (int) $blog_id);
		$user = new WP_User( (int) $user_id);
		?>
		<h3><?php _e('Your account is now active!'); ?></h3>

		<div id="signup-welcome">
			<p><span class="h3"><?php _e('Username:'); ?></span> <?php echo $user->user_login ?></p>
			<p><span class="h3"><?php _e('Password:'); ?></span> <?php echo $password; ?></p>
		</div>

		<?php if ( $url != network_home_url('', 'http') ) : ?>
			<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">View your site</a> or <a href="%2$s">Log in</a>'), $url, $url . 'wp-login.php' ); ?></p>
		<?php else: ?>
			<p class="view"><?php printf( __('Your account is now activated. <a href="%1$s">Log in</a> or go back to the <a href="%2$s">homepage</a>.' ), network_site_url('wp-login.php', 'login'), network_home_url() ); ?></p>
		<?php endif;
	}
}
?>

<script type="text/javascript">
	var key_input = document.getElementById('key');
	key_input && key_input.focus();
</script>