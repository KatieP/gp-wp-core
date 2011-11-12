<?php
header ("Content-Type: text/xml");

/** Include the bootstrap for setting up WordPress environment */
include('./wp-load.php');

global $wpdb;
global $newposttypes;

if (!empty($_SERVER['HTTPS'])) {
	$url = "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
} else {
	$url = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
}

foreach ( $sitemaptypes as $sitemaptype ) {
	foreach ( $newposttypes as $posttypevalue ) {
		if ($posttypevalue['enabled'] == true) {
			if ( get_option('gp-' . $sitemaptype['id'] . '_' . $posttypevalue['id']) == true ) {
				$pos = strpos( $url, 'sitemap-' . $sitemaptype['id'] . '-' . $posttypevalue['id'] . '.xml' );
				if ( $pos >= 1 ) {
					$sitemap = $sitemaptype['id'];
					$posttype = $posttypevalue['id'];
					$postchangefreq = $posttypevalue['changefreq'];
					$postpriority = $posttypevalue['priority'];
					$postkeywords = $posttypevalue['keywords'];
				}
			}
		}
	}
}

echo '<?xml version="1.0" encoding="UTF-8"?>';
if ( $sitemap == 'googlenews' ) { echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">'; }
if ( $sitemap == 'sitemap' || !$sitemap ) { echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">'; }

$querystr = $wpdb->prepare( "SELECT wp_posts.ID, wp_posts.post_title, wp_posts.post_date, m0.meta_value as _thumbnail_id FROM wp_posts left join wp_postmeta as m0 on m0.post_id=wp_posts.ID and m0.meta_key='_thumbnail_id' WHERE wp_posts.post_status='publish' AND wp_posts.post_type = %s AND m0.meta_value >= 1 LIMIT 1000;", $posttype );
$pageposts = $wpdb->get_results($querystr, OBJECT);
$numPosts = $wpdb->num_rows-1;

if ($pageposts && $numPosts != -1) {
	foreach ($pageposts as $post) {
		echo '<url>';
		echo '<loc>' . ent2ncr(get_permalink($post->ID)) . '</loc>';
		if ( $sitemap == 'googlenews' ) {
				
				echo '<news:news>';
					echo '<news:publication>';
						echo '<news:name>Green Pages</news:name>';
						echo '<news:language>en</news:language>';
					echo '</news:publication>';
					echo '<news:genres>Blog</news:genres>';
					echo '<news:publication_date>' . makeIso8601TimeStamp($post->post_date) . '</news:publication_date>';
					echo '<news:title>' . esc_html($post->post_title) . '</news:title>';
					foreach ( $newposttypes as $posttypevalue ) {
						if ( $posttype == $posttypevalue['id'] ) {
							echo '<news:keywords>' . esc_html($postkeywords) . '</news:keywords>';
						}
					}
				echo '</news:news>';
		}
		
		if ( $sitemap == 'sitemap' ) {
			echo '<lastmod>' . makeIso8601TimeStamp($post->post_date) . '</lastmod>';
			echo '<changefreq>' . esc_html($postchangefreq) . '</changefreq>';
			echo '<priority>' . esc_html($postpriority) . '</priority>';
			if ( $post->_thumbnail_id ) {
				echo '<image:image>';
					$postimage = wp_get_attachment_image_src( $post->_thumbnail_id, 'homepage-thumbnail' );
	       			echo '<image:loc>' . esc_html( $postimage[0] ) . '</image:loc>'; 
	    		echo '</image:image>';
			}
    		#<video:video>     
      			#<video:content_loc>http://www.example.com/video123.flv</video:content_loc>
      			#<video:player_loc allow_embed="yes" autoplay="ap=1">http://www.example.com/videoplayer.swf?video=123</video:player_loc>
      			#<video:thumbnail_loc>http://www.example.com/thumbs/123.jpg</video:thumbnail_loc>
      			#<video:title>Grilling steaks for summer</video:title>  
      			#<video:description>Get perfectly done steaks every time</video:description>
    		#</video:video>
		}
		echo '</url>';
	}
}
echo '</urlset>';

?>