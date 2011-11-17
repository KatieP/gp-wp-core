<?php

add_action('wp_head', 'signuppageheaders');

function signuppageheaders() {
	echo "<meta name='robots' content='noindex,nofollow' />\n";
}

if ( is_array( get_site_option( 'illegal_names' )) && isset( $_GET[ 'new' ] ) && in_array( $_GET[ 'new' ], get_site_option( 'illegal_names' ) ) == true ) {
	wp_redirect( network_home_url() );
	die();
}

function do_signup_header() {
	do_action( 'signup_header' );
}
add_action( 'wp_head', 'do_signup_header' );

if ( !is_multisite() ) {
	wp_redirect( site_url('wp-login.php?action=register') );
	die();
}

if ( !is_main_site() ) {
	wp_redirect( network_home_url( 'register' ) );
	die();
}

do_action( 'before_signup_form' );

function signup_user($user_name = '', $user_email = '', $errors = '') {
	global $current_site, $active_signup;

	if ( !is_wp_error($errors) )
		$errors = new WP_Error();

	// allow definition of default variables
	$filtered_results = apply_filters('signup_user_init', array('user_name' => $user_name, 'user_email' => $user_email, 'errors' => $errors ));
	$user_name = $filtered_results['user_name'];
	$user_email = $filtered_results['user_email'];
	$errors = $filtered_results['errors'];

	?>
	
	<h3><?php printf( __( 'Get your own %s account in seconds' ), $current_site->site_name ) ?></h3>
	<form id="setupform" method="post" action="/register">
		<input type="hidden" name="stage" value="validate-user-signup" />
		<?php do_action( 'signup_hidden_fields' ); ?>
		<?php show_user_form($user_name, $user_email, $errors); ?>
		<p class="submit"><input type="submit" name="submit" class="submit" value="<?php esc_attr_e('Create your account now!') ?>" /></p>
	</form>
	<?php
}

function validate_user_signup() {
	$result = validate_user_form();
	extract($result);

	if ( isset( $errors->errors['user_name'] ) ) {
		if ( in_array('Only lowercase letters (a-z) and numbers are allowed.', $errors->errors['user_name']) ) {
			unset($errors->errors['user_name']);
			
			$username = $result['user_name'];
			$raw_username = $username;
			$username = wp_strip_all_tags( $username );
			$username = remove_accents( $username );
			
			// Kill octets
			$username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
			$username = preg_replace( '/&.+?;/', '', $username ); // Kill entities
			
			// Strict + WP doesn't allow uppercase letters so I've added them here
			$username = preg_replace( '|[^a-zA-Z0-9 _.\-@]|i', '', $username );
			
			// Consolidate contiguous whitespace
			$username = preg_replace( '|\s+|', ' ', $username );
			
			if ($username != $raw_username) {
				$errors->add( 'user_name', __( 'This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
			}
		}
	}

	if ( $errors->get_error_code() ) {
		return $errors;
	}
	
	global $wpdb;
	
	// Format data
	$user_email = sanitize_email( $user_email );
	$key = substr( md5( time() . rand() . $user_email ), 0, 16 );
	$meta = serialize(apply_filters( 'add_signup_meta', array() ));
	
	$wpdb->insert( $wpdb->signups, array(
		'domain' => '',
		'path' => '',
		'title' => '',
		'user_login' => $username,
		'user_email' => $user_email,
		'registered' => current_time('mysql', true),
		'activation_key' => $key,
		'meta' => $meta
	) );

	wpmu_signup_user_notification($username, $user_email, $key, $meta);

	confirm_user_signup($username, $user_email);
	return true;
}

function confirm_user_signup($user_name, $user_email) {
	?>
	<p class="message">Registration complete! An authentication email has been sent to you. Once you click on the activation link you can then login.</p>
	<h2><?php printf( __( '%s is your new username' ), $user_name) ?></h2>
	<p><?php _e( 'But, before you can start using your new username, <strong>you must activate it</strong>.' ) ?></p>
	<p><?php printf(__( 'Check your inbox at <strong>%1$s</strong> and click the link given.' ),  $user_email) ?></p>
	<p><?php _e( 'If you do not activate your username within two days, you will have to sign up again.' ); ?></p>
	<?php
	do_action( 'signup_finished' );
}

function validate_user_form() {
	return wpmu_validate_user_signup(trim($_POST['user_name']), trim($_POST['user_email']));
}

function show_user_form($user_name = '', $user_email = '', $errors = '') {
	// User name
	echo '<label for="user_name">' . __('Username:') . '</label>';
	if ( $errmsg = $errors->get_error_message('user_name') ) {
		echo '<p id="login_error" class="error">'.$errmsg.'</p>';
	}
	echo '<input name="user_name" type="text" id="user_name" value="'. esc_attr($user_name) .'" maxlength="60" />';
	_e( '(Must be at least 4 characters, letters and numbers only.)' );
	?>

	<label for="user_email"><?php _e( 'Email&nbsp;Address:' ) ?></label>
	<?php if ( $errmsg = $errors->get_error_message('user_email') ) { ?>
		<p id="login_error" class="error"><?php echo $errmsg ?></p>
	<?php } ?>
	<input name="user_email" type="text" id="user_email" value="<?php  echo esc_attr($user_email) ?>" maxlength="200" />
	<?php _e('We send your registration email to this address. (Double-check your email address before continuing.)') ?>
	<?php
	if ( $errmsg = $errors->get_error_message('generic') ) {
		echo '<p id="login_error" class="error">' . $errmsg . '</p>';
	}
	do_action( 'signup_extra_fields', $errors );
}

// Main

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

$active_signup = get_site_option( 'registration' );
if ( $active_signup != 'none' )
	 $active_signup = 'user';

$active_signup = apply_filters( 'wpmu_active_signup', $active_signup ); // return "all", "none", "blog" or "user"

// Make the signup type translatable.
$i18n_signup['all'] = _x('all', 'Multisite active signup type');
$i18n_signup['none'] = _x('none', 'Multisite active signup type');
$i18n_signup['blog'] = _x('blog', 'Multisite active signup type');
$i18n_signup['user'] = _x('user', 'Multisite active signup type');

$current_user = wp_get_current_user();

if ( $active_signup == 'none' ) {
	echo '<p id="login_error" class="error">User registration has been disabled.</p>';
} else {
	$user_name = isset( $_POST[ 'user_name' ] ) ? $_POST[ 'user_name' ] : '';
	$user_email = isset( $_POST[ 'user_email' ] ) ? $_POST[ 'user_email' ] : '';
	
	if ( is_user_logged_in() == false && $active_signup == 'user' && $_POST[ 'stage' ] == 'validate-user-signup') {
		$errors = validate_user_signup();
	}

	if ( $errors !== true ) {
		do_action( 'preprocess_signup_form' ); // populate the form from invites, elsewhere?
		if ( is_user_logged_in() == false && $active_signup == 'user')
			signup_user( $user_name, $user_email, $errors );
		else
			echo '<p id="login_error" class="error">You are logged in already. No need to register again!</p>';
	}
}

do_action( 'after_signup_form' );

?>