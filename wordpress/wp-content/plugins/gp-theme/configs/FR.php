<?php

class Config {

	private static $posttypes, $states, $meta;

	public static function init() {
	    global $wpdb;

		$posttypes = array(
			array(
				'id' => 'gp_news', 
				'name' => 'News', 
				'plural' => false,
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => true,
				'priority' => '1',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'News' ),
					'labels' => array(
						'name' => _x( 'News', 'post type general name' ),
						'singular_name' => _x( 'News', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'news' ),
						'add_new_item' => __( 'Add New News' ),
						'edit_item' => __( 'Edit News' ),
						'new_item' => __( 'New News' ),
						'view_item' => __( 'View News' ),
						'search_items' => __( 'Search News' ),
						'not_found' =>  __( 'No news found' ),
						'not_found_in_trash' => __( 'No news found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/newspaper.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'news', 'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_jobs_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'News Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'news-category' )
				)
			),
			array(
				'id' => 'gp_events', 
				'name' => 'Event', 
				'plural' => true,
				'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'),
				'enabled' => true,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Event Location'), 
					array('id' => 'postEventDate', 'title' => 'Event Date')
				), 
				'args' => array(
					'label' => __( 'Events' ),
					'labels' => array(
						'name' => _x( 'Events', 'post type general name' ),
						'singular_name' => _x( 'Event', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'events' ),
						'add_new_item' => __( 'Add New Event' ),
						'edit_item' => __( 'Edit Event' ),
						'new_item' => __( 'New Event' ),
						'view_item' => __( 'View Event' ),
						'search_items' => __( 'Search Events' ),
						'not_found' =>  __( 'No events found' ),
						'not_found_in_trash' => __( 'No events found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/date.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'events', 'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_events_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Event Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'event-category' )
				)
			),
			array(
				'id' => 'gp_jobs', 
				'name' => 'Job', 
				'plural' => true,
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => false,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Jobs' ),
					'labels' => array(
						'name' => _x( 'Jobs', 'post type general name' ),
						'singular_name' => _x( 'Job', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'jobs' ),
						'add_new_item' => __( 'Add New Job' ),
						'edit_item' => __( 'Edit Job' ),
						'new_item' => __( 'New Job' ),
						'view_item' => __( 'View Job' ),
						'search_items' => __( 'Search Jobs' ),
						'not_found' =>  __( 'No jobs found' ),
						'not_found_in_trash' => __( 'No jobs found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/user_gray.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'jobs', 'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_jobs_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Job Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'job-category' )
				)
			),
			array(
				'id' => 'gp_competitions', 
				'name' => 'Competition', 
				'plural' => true,
				'columns' => array('author', 'categories', 'tags', 'comments', 'date', 'dates'),
				'enabled' => true,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location'), 
					array('id' => 'postCompetitionDate', 'title' => 'Competition Date')
				), 
				'args' => array(
					'label' => __( 'Competitions' ),
					'labels' => array(
						'name' => _x( 'Competitions', 'post type general name' ),
						'singular_name' => _x( 'Competition', 'post type singular name' ),
						'add_new' => _x( 'Add New ($250)', 'competitions' ),
						'add_new_item' => __( 'Add New Competition - Price $250 (Charged only when post is approved for publication.)' ),
						'edit_item' => __( 'Edit Competition' ),
						'new_item' => __( 'New Competition' ),
						'view_item' => __( 'View Competition' ),
						'search_items' => __( 'Search Competitions' ),
						'not_found' =>  __( 'No competitions found' ),
						'not_found_in_trash' => __( 'No competitions found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/rosette.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'competitions' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_competitions_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Competition Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'competition-category' )
				)
			),
			array(
				'id' => 'gp_people', 
				'name' => 'People', 
				'plural' => false, 
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => true,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Interviews' ),
					'labels' => array(
						'name' => _x( 'Interviews', 'post type general name' ),
						'singular_name' => _x( 'Interview', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'Interview' ),
						'add_new_item' => __( 'Add New Interview' ),
						'edit_item' => __( 'Edit Interview' ),
						'new_item' => __( 'New Interview' ),
						'view_item' => __( 'View Interview' ),
						'search_items' => __( 'Search Interviews' ),
						'not_found' =>  __( 'No interviews found' ),
						'not_found_in_trash' => __( 'No interviews found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/cup.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'people' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_interviews_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Interviews Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'interview-category' )
				)
			),
			array(
				'id' => 'gp_katiepatrick', 
				'name' => 'Katie Patrick', 
				'plural' => false, 
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => false,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Katie Patrick' ),
					'labels' => array(
						'name' => _x( 'Katie Patrick', 'post type general name' ),
						'singular_name' => _x( 'Katie Patrick', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'Story' ),
						'add_new_item' => __( 'Add New Story' ),
						'edit_item' => __( 'Edit Story' ),
						'new_item' => __( 'New Story' ),
						'view_item' => __( 'View Story' ),
						'search_items' => __( 'Search Stories' ),
						'not_found' =>  __( 'No stories found' ),
						'not_found_in_trash' => __( 'No stories found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/katiepatrick.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'katie-patrick' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_katiepatrick_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Katie Patrick Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'katie-patrick-category' )
				)
			),
			array(
				'id' => 'gp_productreview', 
				'name' => 'Product Review', 
				'plural' => false, 
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => false,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Product Reviews' ),
					'labels' => array(
						'name' => _x( 'Product Review', 'post type general name' ),
						'singular_name' => _x( 'Product Review', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'Product Review' ),
						'add_new_item' => __( 'Add New Product Review' ),
						'edit_item' => __( 'Edit Product Review' ),
						'new_item' => __( 'New Product Review' ),
						'view_item' => __( 'View Product Review' ),
						'search_items' => __( 'Search Product Reviews' ),
						'not_found' =>  __( 'No product reviews found' ),
						'not_found_in_trash' => __( 'No product reviews found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-productreview.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'product-review' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_productreview_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Product Review Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'product-review-category' )
				)
			),
			array(
				'id' => 'gp_advertorial', 
				'name' => 'Product', 
				'plural' => true,
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => true,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location'), 
					array('id' => 'postProductURL', 'title' => 'Purchase URL')
				), 
				'args' => array(
					'label' => __( 'Products' ),
					'labels' => array(
						'name' => _x( 'Products', 'post type general name' ),
						'singular_name' => _x( 'Product', 'post type singular name' ),
						'add_new' => _x( 'Add New ($89)', 'Product' ),
						'add_new_item' => __( 'Post your eco friendly product here for $89! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; All posts are approved within 24 hours.' ),
						'edit_item' => __( 'Edit Product' ),
						'new_item' => __( 'New Product' ),
						'view_item' => __( 'View Product' ),
						'search_items' => __( 'Search Products' ),
						'not_found' =>  __( 'No Products Found' ),
						'not_found_in_trash' => __( 'No Products Found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-advertorial.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'eco-friendly-products' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_advertorial_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Product Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'advertorial-category' )
				)
			),
			array(
				'id' => 'gp_projects', 
				'name' => 'Project', 
				'plural' => true, 
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => true,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Projects' ),
					'labels' => array(
						'name' => _x( 'Projects', 'post type general name' ),
						'singular_name' => _x( 'Project', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'Project' ),
						'add_new_item' => __( 'Add New Project' ),
						'edit_item' => __( 'Edit Project' ),
						'new_item' => __( 'New Project' ),
						'view_item' => __( 'View Project' ),
						'search_items' => __( 'Search Projects' ),
						'not_found' =>  __( 'No projects found' ),
						'not_found_in_trash' => __( 'No projects found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/transmit.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'projects' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_projects_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Project Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'projects-category' )
				)
			),
			array(
				'id' => 'gp_greengurus', 
				'name' => 'Green Gurus', 
				'plural' => false, 
				'columns' => array('author', 'categories', 'tags', 'comments', 'date'),
				'enabled' => false,
				'priority' => '0.6',
				'changefreq' => 'monthly',
				'keywords' => 'science, environment',
				'GPmeta' => array(
					array('id' => 'postGeoLoc', 'title' => 'Post Location')
				), 
				'args' => array(
					'label' => __( 'Green Gurus' ),
					'labels' => array(
						'name' => _x( 'Green Gurus', 'post type general name' ),
						'singular_name' => _x( 'Green Guru', 'post type singular name' ),
						'add_new' => _x( 'Add New', 'Guru Story' ),
						'add_new_item' => __( 'Add New Guru Story' ),
						'edit_item' => __( 'Edit Guru Story' ),
						'new_item' => __( 'New Guru Story' ),
						'view_item' => __( 'View Guru Stories' ),
						'search_items' => __( 'Search Guru Stories' ),
						'not_found' =>  __( 'No guru stories found' ),
						'not_found_in_trash' => __( 'No guru stories found in Trash' ),
						'parent_item_colon' => ''
					),
					'public' => true,
					'can_export' => true,
					'show_ui' => true,
					'_builtin' => false,
					'_edit_link' => 'post.php?post=%d', // ?
					'capability_type' => 'post',
					'menu_icon' => get_bloginfo( 'template_url' ).'/template/icon-greenguru.png',
					'hierarchical' => false,
					'rewrite' => array( 'slug' => 'green-gurus' ,'with_front' => FALSE ),
					'supports'=> array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'comments', 'revisions', 'page-attributes', 'custom-fields' ),
					'show_in_nav_menus' => true,
					'taxonomies' => array( 'gp_greengurus_category', 'post_tag'),
					'has_archive' => true
				), 
				'taxonomy' => array(
					'label' => __( 'Green Gurus Category' ),
					'labels' => array(
						'name' => _x( 'Categories', 'taxonomy general name' ),
						'singular_name' => _x( 'Category', 'taxonomy singular name' ),
						'search_items' =>  __( 'Search Categories' ),
						'popular_items' => __( 'Popular Categories' ),
						'all_items' => __( 'All Categories' ),
						'parent_item' => null,
						'parent_item_colon' => null,
						'edit_item' => __( 'Edit Category' ),
						'update_item' => __( 'Update Category' ),
						'add_new_item' => __( 'Add New Category' ),
						'new_item_name' => __( 'New Category Name' ),
						'separate_items_with_commas' => __( 'Separate categories with commas' ),
						'add_or_remove_items' => __( 'Add or remove categories' ),
						'choose_from_most_used' => __( 'Choose from the most used categories' )
					),
					'hierarchical' => true,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'green-gurus-category' )
				)
			)
		);
		
		// Unset disabled post types since we don't need them
		foreach ( $posttypes as $kA => $vA ) {
			if ( $vA['enabled'] === false ) {
				unset($posttypes[$kA]);
			}
		}
		$posttypes = array_values($posttypes); # Reset array key index
		
		// Modify admin post widgets based on user role
		if ( get_user_role( array('contributor') ) ) {
			$modsupport = array(
				'gp_news' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
				'gp_projects' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
				'gp_events' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' )
			);
			
			foreach ( $posttypes as $kA => $vA ) {
				foreach ( $modsupport as $kB => $vB ) {
					if ( $vA['id'] == $kB ) {
						unset($posttypes[$kA]['args']['supports']);
						$posttypes[$kA]['args']['supports'] = $vB;
					}
				}
			}
		}
		
		self::$posttypes = $posttypes;
		
		$query = "SELECT a.code, a.name, a.subset, a.subset_plural, b.subset_count
		    FROM " . $wpdb->base_prefix . "debian_iso_3166_2 AS a
		    INNER JOIN (
                SELECT subset, count(*) AS subset_count
                FROM " . $wpdb->base_prefix . "debian_iso_3166_2 WHERE country = 'FR' 
                    AND parent = ''
                GROUP BY subset 
		    ) AS b 
		    ON a.subset = b.subset 
		    WHERE a.country = 'FR' 
		        AND a.parent = ''
		    ORDER BY b.subset_count DESC, a.subset, a.name";

		$states = $wpdb->get_results( $query, ARRAY_A );
		
		if ( !$states )  { $states = array(); }
		
		self::$states = $states;
		
		
		$meta = array(
		        'facebook_id' => '268597359918650'
		);
		
		self::$meta = $meta;
	}
	
	public static function getPostTypes() {
		self::init();
		return self::$posttypes;
	}
	
	public static function getStates() {
	    self::init();
	    return self::$states;
	}
	
	public static function getMeta() {
	    self::init();
	    return self::$meta;
	}
}

?>