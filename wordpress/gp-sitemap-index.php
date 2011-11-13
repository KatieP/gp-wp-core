<?php
header ("Content-Type: text/xml");

/** Include the bootstrap for setting up WordPress environment */
include('./wp-load.php');

global $wpdb;
global $newposttypes;
global $sitemaptypes;

$querystr = "SELECT s.post_type, s.post_date FROM (SELECT post_type, max(post_date) AS mpd FROM " . $wpdb->prefix . "posts WHERE post_status = 'publish' GROUP BY post_type) AS f INNER JOIN " . $wpdb->prefix . "posts AS s ON s.post_type = f.post_type AND s.post_date = f.mpd ORDER BY f.mpd DESC LIMIT 10;";
$postdates = $wpdb->get_results($querystr, OBJECT);
$numPosts = $wpdb->num_rows-1;

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

if ($postdates && $numPosts != -1) {
	foreach ( $sitemaptypes as $sitemaptype ) {
		foreach ( $newposttypes as $posttypevalue ) {
			if ($posttypevalue['enabled'] == true) {
				if ( get_option('gp-' . $sitemaptype['id'] . '_' . $posttypevalue['id']) == true ) {
					echo '<sitemap>';
					echo '<loc>' . esc_html(site_url() . '/sitemap-' . $sitemaptype['id'] . '-' . $posttypevalue['id'] . '.xml') . '</loc>';
					foreach ( $postdates as $postdate ) {
						if ( $postdate->post_type == $posttypevalue['id'] ) { 
							echo '<lastmod>' . makeIso8601TimeStamp($postdate->post_date) . '</lastmod>'; 
						}
					}
					echo '</sitemap>';
				}
			}
		}
	}
}

echo '</sitemapindex>';
?>