<?php
global $sitemaptypes;
$posttypes = Config::getPostTypes();

if ($_POST['gp_self'] == 1 ) {
    check_admin_referer('gp-theme-update_gp_sitemaps');

    foreach ( $sitemaptypes as $sitemapvalue ) {
        foreach ( $posttypes as $posttypevalue ) {
            if ($posttypevalue['enabled'] == true) {
                if ( is_array( $_POST[$sitemapvalue['id']] ) && in_array( $posttypevalue['id'] , $_POST[$sitemapvalue['id']] ) ) {
                    #echo 'gp-' . $sitemapvalue['id'] . '_' . $posttypevalue['id'] . ' 1<br />';
                    update_option('gp-' . $sitemapvalue['id'] . '_' . $posttypevalue['id'], true);
                } else {
                    #echo 'gp-' . $sitemapvalue['id'] . '_' . $posttypevalue['id'] . ' 0<br />';
                    update_option('gp-' . $sitemapvalue['id'] . '_' . $posttypevalue['id'], false);
                }
            }
        }
    }

    echo '<div class="updated"><p><strong>Options saved</strong></p></div>';
}
?>

<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br />
	</div>
	<h2>Sitemap Settings</h2>
	<form name="gp_sitemaps_form" method="post"
		action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<?php
		if ( function_exists('wp_nonce_field') ) {
		    wp_nonce_field('gp-theme-update_gp_sitemaps');
		}
		?>

		<h3>Enable Sitemaps</h3>
		<table class="form-table">
			<?php
			if ( is_array( $sitemaptypes ) && is_array( $posttypes ) ) {
			    echo '<tr><td></td>';
			    foreach ( $posttypes as $posttypevalue ) {
			        if ($posttypevalue['enabled'] == true) {
			            if ($posttypevalue['plural'] == true) {
			                echo '<th>' . $posttypevalue['name'] . 's</th>';
			            } else {
			                echo '<th>' . $posttypevalue['name'] . '</th>';
			            }
			        }
			    }
			    echo '</tr>';
			    foreach ( $sitemaptypes as $sitemapvalue ) {
			        echo '<tr><th>' . $sitemapvalue['name']. '</th>';
			        foreach ( $posttypes as $posttypevalue ) {
			            if ($posttypevalue['enabled'] == true) {
			                if ( get_option('gp-' . $sitemapvalue['id'] . '_' . $posttypevalue['id']) == true ) {
			                    $checked = ' checked="checked"';
			                    $xmlmaps[] = 'sitemap-' . $sitemapvalue['id'] . '-' . $posttypevalue['id'] . '\.xml';
			                } else {
			                    $checked = '';
			                }
			                echo '<td><input type="checkbox" name="' . $sitemapvalue['id'] . '[]" value="' . $posttypevalue['id'] . '"' . $checked . ' /></td>';
			            }
			        }
			        echo '</tr>';
			    }
			}
			?>
		</table>

		<h4>
			Add this to your <i>.htaccess</i> file <u>before</u> your Wordpress
			rules.
		</h4>
		<?php
		echo '&lt;IfModule mod_rewrite.c&gt;<br />';
		echo 'RewriteEngine On<br />';
		echo 'RewriteBase /<br />';
		echo 'RewriteRule sitemap\.xml gp-sitemap-index.php [L]<br />';
		foreach ($xmlmaps as $xmlmap) {
			echo 'RewriteRule ' . $xmlmap . ' gp-sitemaps.php [L]<br />';
		}
		echo '&lt;/IfModule&gt;<br />';
   		?>

		<p>
			<input type="submit" name="Submit" value="Save Changes" id="submit" class="button-primary" />
		</p>

		<input type="hidden" name="gp_self" value="1">
	</form>
</div>
