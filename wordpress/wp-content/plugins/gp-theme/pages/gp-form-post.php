<?php 
if ($_POST['gp_self'] == 1 ) {
	check_admin_referer('gp-theme-update_gp_post');
	
	echo '<div class="updated"><p><strong>Options saved</strong></p></div>';
} else {
	
}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>  
    <h2>Post Settings</h2>
    <form name="gp_post_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
    	<?php
		if ( function_exists('wp_nonce_field') ) {
			wp_nonce_field('gp-theme-update_gp_post');
		}
		?>
    
	</form>
</div>