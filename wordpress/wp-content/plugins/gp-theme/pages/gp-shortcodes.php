<?php

function gp_shortcode_abn( $atts ) {
	return get_option('gp_abn');
}
add_shortcode( 'gp-abn', 'gp_shortcode_abn' );

function gp_shortcode_companynamefull( $atts ) {
	return get_option('gp_fullcompanyname');
}
add_shortcode( 'gp-companynamefull', 'gp_shortcode_companynamefull' );

function gp_shortcode_companyname( $atts ) {
	return get_option('gp_companydisplayname');
}
add_shortcode( 'gp-companyname', 'gp_shortcode_companyname' );

function gp_shortcode_slogan( $atts ) {
	return get_option('gp_slogan');
}
add_shortcode( 'gp-slogan', 'gp_shortcode_slogan' );

function gp_shortcode_phone1( $atts ) {
	return get_option('gp_phone1');
}
add_shortcode( 'gp-phone', 'gp_shortcode_phone1' );

function gp_shortcode_mobile1( $atts ) {
	return get_option('gp_mobile1');
}
add_shortcode( 'gp-mobile', 'gp_shortcode_mobile1' );

function gp_shortcode_fax1( $atts ) {
	return get_option('gp_fax1');
}
add_shortcode( 'gp-fax', 'gp_shortcode_fax1' );

function gp_shortcode_email1( $atts ) {
	return str_replace('@', ' [ at ] ', get_option('gp_email1'));
}
add_shortcode( 'gp-email', 'gp_shortcode_email1' );

function gp_shortcode_skype1( $atts ) {
	return '<a href="callto://' . get_option('gp_skype1') . '" title="Green Pages Skype Link">' . get_option('gp_skype1') . '</a>';
}
add_shortcode( 'gp-skype', 'gp_shortcode_skype1' );

function gp_shortcode_officeaddress( $atts ) {
	return '<address>' . nl2br_limit(get_option('gp_officeaddress'),'3') . '</address>';
}
add_shortcode( 'gp-officeaddress', 'gp_shortcode_officeaddress' );

function gp_shortcode_googlemaps( $atts ) {
	return get_option('gp_googlemaps');
}
add_shortcode( 'gp-googlemaps', 'gp_shortcode_googlemaps' );

function gp_shortcode_postaladdress( $atts ) {
	return '<address>' . nl2br_limit(get_option('gp_postaladdress'),'3') . '</address>';
}
add_shortcode( 'gp-postaladdress', 'gp_shortcode_postaladdress' );

function gp_shortcode_facebook( $atts ) {
	return '<a href="' . get_option('gp_facebook') . '" title="Green Pages Facebook Link">' . get_option('gp_facebook') . '</a>';
}
add_shortcode( 'gp-facebook', 'gp_shortcode_facebook' );

function gp_shortcode_twitter( $atts ) {
	return '<a href="' . get_option('gp_twitter') . '" title="Green Pages Twitter Link">' . get_option('gp_twitter') . '</a>';
}
add_shortcode( 'gp-twitter', 'gp_shortcode_twitter' );

function gp_shortcode_youtube( $atts ) {
	return '<a href="' . get_option('gp_youtube') . '" title="Green Pages Youtube Link">' . get_option('gp_youtube') . '</a>';
}
add_shortcode( 'gp-youtube', 'gp_shortcode_youtube' );

function gp_shortcode_adinquiries( $atts ) {
	if (!check_slug_optionlist('gp_adinquiries_pages')) { return false; }
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-adinquiries.php' );
}
add_shortcode( 'gp-advertise-form', 'gp_shortcode_adinquiries' );

function gp_shortcode_feedback( $atts ) {
	if (!check_slug_optionlist('gp_feedback_pages')) { return false; }
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-feedback.php' );
}
add_shortcode( 'gp-feedback-form', 'gp_shortcode_feedback' );

function gp_shortcode_contentpartnerslist( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-list-contentpartners.php' );
	return $cp_string;
}
add_shortcode( 'gp-contentpartners', 'gp_shortcode_contentpartnerslist' );

function gp_shortcode_drinquiries( $atts ) {
	if (!check_slug_optionlist('gp_drinquiries_pages')) { return false; }
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-drinquiries.php' );
}
add_shortcode( 'gp-directory-form', 'gp_shortcode_drinquiries' );

function gp_shortcode_mu_registration( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-registration.php' );
}
add_shortcode( 'gp-registration-form', 'gp_shortcode_mu_registration' );

function gp_shortcode_mu_activation( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-activation.php' );
}
add_shortcode( 'gp-activation-form', 'gp_shortcode_mu_activation' );

function gp_shortcode_profileeditor( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-profileeditor.php' );
}
add_shortcode( 'gp-profileeditor-form', 'gp_shortcode_profileeditor' );

function gp_shortcode_welcome( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-welcome.php' );
}
add_shortcode( 'gp-public-welcome', 'gp_shortcode_welcome' );

function gp_shortcode_profile_notification( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-form-profile_notification_editor.php' );
}
add_shortcode( 'gp-profile_notification', 'gp_shortcode_profile_notification' );

function gp_shortcode_world_map( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-world-map.php' );
}
add_shortcode( 'gp-world-map', 'gp_shortcode_world_map' );

function gp_shortcode_advertisers( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-advertisers.php' );
}
add_shortcode( 'gp-advertisers', 'gp_shortcode_advertisers' );

function gp_shortcode_delete_post( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-delete-post.php' );
}
add_shortcode( 'gp-delete-post', 'gp_shortcode_delete_post' );

function gp_shortcode_about( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-public-about.php' );
}
add_shortcode( 'gp-about', 'gp_shortcode_about' );

function gp_shortcode_redirect_to_profile_page( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-redirect-to-profile-page.php' );
}
add_shortcode( 'gp-redirect-to-profile-page', 'gp_shortcode_redirect_to_profile_page' );

function gp_shortcode_chargify_handler( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-chargify-handler.php' );
}
add_shortcode( 'gp-chargify-handler', 'gp_shortcode_chargify_handler' );

function gp_shortcode_chargify_upgrade_downgrade_handler( $atts ) {
	require_once( GP_PLUGIN_DIR . '/pages/gp-chargify-upgrade-downgrade-handler.php' );
}
add_shortcode( 'gp-chargify-upgrade-downgrade-handler', 'gp_shortcode_chargify_upgrade_downgrade_handler' );
?>
