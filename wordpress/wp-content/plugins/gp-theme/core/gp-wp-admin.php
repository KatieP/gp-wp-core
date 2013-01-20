<?php
function gp_set_admin_menu() {
    #http://codex.wordpress.org/Administration_Menus
    add_menu_page( 'Theme Settings', 'Green Pages', 'manage_options', 'gp-settings', 'gp_load_settings', GP_PLUGIN_URL . '/gp-theme-icon.png', 100 );
    #add_submenu_page( 'gp-settings', 'URL Rewrite Rules', 'URL Rewrite Rules', 'manage_options', 'gp-urlrules', 'gp_load_urlrules' );
    add_submenu_page( 'gp-settings', 'Theme Settings', 'Theme Settings', 'manage_options', 'gp-settings', 'gp_load_settings' );
    add_submenu_page( 'gp-settings', 'Post Settings', 'Post Settings', 'manage_options', 'gp-post', 'gp_load_post' );
    add_submenu_page( 'gp-settings', 'Form Settings', 'Form Settings', 'manage_options', 'gp-forms', 'gp_load_forms' );
    add_submenu_page( 'gp-settings', 'Sitemap Settings', 'Sitemap Settings', 'manage_options', 'gp-sitemaps', 'gp_load_sitemaps' );
    add_submenu_page( 'gp-settings', 'Directory Inquiries', 'Directory Inquiries', 'manage_options', 'gp-directory-inquiries', 'gp_load_drinquiries' );
    add_submenu_page( 'gp-settings', 'Advertiser Inquiries', 'Advertiser Inquiries', 'manage_options', 'gp-advertiser-inquiries', 'gp_load_adinquiries' );
    add_submenu_page( 'gp-settings', 'Content Partner Inquiries', 'Content Partner Inquiries', 'manage_options', 'gp-content-partner-inquiries', 'gp_load_cpinquiries' );
    add_submenu_page( 'gp-settings', 'Feedback', 'Feedback', 'manage_options', 'gp-feedback', 'gp_load_feedback' );
}

function gp_load_settings() {
    include( GP_PLUGIN_DIR . '/pages/gp-form-settings.php' );
}

/*
function gp_load_urlrules() {
    include( GP_PLUGIN_DIR . '/pages/gp-form-urlrules.php' );
}
*/

function gp_load_post() {
    include( GP_PLUGIN_DIR . '/pages/gp-form-post.php' );
}

function gp_load_forms() {
    include( GP_PLUGIN_DIR . '/pages/gp-form-forms.php' );
}

function gp_load_sitemaps() {
    include( GP_PLUGIN_DIR . '/pages/gp-form-sitemaps.php' );
}

function gp_load_drinquiries() {
    include( GP_PLUGIN_DIR . '/pages/gp-list-drinquiries.php' );
}

function gp_load_adinquiries() {
    include( GP_PLUGIN_DIR . '/pages/gp-list-adinquiries.php' );
}

function gp_load_cpinquiries() {
    include( GP_PLUGIN_DIR . '/pages/gp-list-cpinquiries.php' );
}

function gp_load_feedback() {
    include( GP_PLUGIN_DIR . '/pages/gp-list-feedback.php' );
}

add_filter('admin_title', 'gp_admin_title');
function gp_admin_title($admin_title) {
    global $title;
    return get_bloginfo('name') . " &rsaquo; " . wp_specialchars( strip_tags( $title ) );
}

/* SCREEN OPTIONS / HIDE WIDGETS FROM CUSTOM POST TYPES */
# http://w-shadow.com/blog/2010/06/29/adding-stuff-to-wordpress-screen-options/
# http://w-shadow.com/blog/2010/06/30/add-new-buttons-alongside-screen-options-and-help/

function gp_remove_meta_boxes(){
    global $wp_meta_boxes, $gp;

    if ( !get_user_role( array('administrator') ) ) {
        unset( $wp_meta_boxes['post'] );
        unset( $wp_meta_boxes['page'] );
    }

    $ns_loc = $gp->location['country_iso2'] . '\\Edition';
    $edition_posttypes = $ns_loc::getPostTypes();

    if ( isset($edition_posttypes) ) {
        foreach ( $edition_posttypes as $posttype ) {

            if ( $posttype['enabled'] === false || get_user_role( $posttype['role_permissions'] ) === false ) {
                unset( $wp_meta_boxes[$posttype['id']] );
            }
             
            if ( get_user_role( array('administrator') ) === false ) {
                unset( $wp_meta_boxes[$posttype['id']]['advanced']['default']['wordbook_sectionid'] );
                unset( $wp_meta_boxes[$posttype['id']]['side']['core']['tagsdiv-post_tag'] );
                unset( $wp_meta_boxes[$posttype['id']]['side']['core']['gp_advertorial_categorydiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['side']['core']['gp_events_categorydiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['side']['core']['gp_competitions_categorydiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['side']['core']['pageparentdiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['postexcerpt'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['trackbacksdiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['postcustom'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['postexcerpt'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['commentstatusdiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['slugdiv'] );
                unset( $wp_meta_boxes[$posttype['id']]['normal']['core']['revisionsdiv'] );
            }
        }
    }
}
add_action( 'add_meta_boxes', 'gp_remove_meta_boxes', 0 );

/* DISABLE FLASH UPLOADER */
function disable_flash_uploader() {
    if ( get_user_role( array('administrator') ) === false ) {
        return false;
    } else {
        return true;
    }
}
add_filter( 'flash_uploader', 'disable_flash_uploader', 1 );

/* REMOVE FAVOURITE ACTIONS MENU */
function remove_favorite_actions() {
    return array();
}
add_filter( 'favorite_actions', 'remove_favorite_actions' );

/* ALLOWABLE FILE EXTENSION UPLOADS */
function yoursite_wp_handle_upload_prefilter($file) {
    if ( get_user_role( array('subscriber') ) ) {
        // This bit is for the flash uploader
        if ($file['type']=='application/octet-stream' && isset($file['tmp_name'])) {
            $file_size = getimagesize($file['tmp_name']);
            if (isset($file_size['error']) && $file_size['error']!=0) {
                $file['error'] = "Unexpected Error: {$file_size['error']}";
                return $file;
            } else {
                $file['type'] = $file_size['mime'];
            }
        }
        list($category,$type) = explode('/',$file['type']);
        if ('image'!=$category || !in_array($type,array('jpg','jpeg','gif','png'))) {
            $file['error'] = "Sorry, you can only upload a .GIF, a .JPG, or a .PNG image file.";
        } else if ($post_id = (isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : false)) {
            if (count(get_posts("post_type=attachment&post_parent={$post_id}"))>1)
                $file['error'] = "Sorry, you cannot upload more than two (2) images.";
        }
    }
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'yoursite_wp_handle_upload_prefilter');

/* RESTRICT VIEWING OTHER USERS POSTS & MEDIA LIBRARY */
function query_set_only_author( $wp_query ) {
    global $current_user;
    $the_admin_url = get_admin_url();
    $the_current_url = "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    if ( substr( $the_current_url, 0, strlen($the_admin_url) ) == $the_admin_url ) {
        if ( get_user_role( array('subscriber', 'contributor') ) ) {
            $wp_query->set( 'author', $current_user->ID );
        }
    }
}
add_action('pre_get_posts', 'query_set_only_author' );

/* REDIRECT USER AFTER LOGIN */
function redirect_user_to( $redirect_to, $user ) {
    if ( get_user_role( array('subscriber') ) ) {
        wp_safe_redirect('/wp-admin/profile.php');
    }
}
//add_filter( 'login_redirect', 'redirect_user_to', 10, 3 );


/* REMOVE "QUICK EDIT" MENU FROM /WP-ADMIN/EDIT.PHP */
function remove_quick_edit( $actions ) {
    if ( get_user_role( array('subscriber', 'contributor') ) ) {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}
add_filter('post_row_actions','remove_quick_edit',10,1);

function my_default_editor() {
    return 'tinymce';
}
add_filter( 'wp_default_editor', 'my_default_editor' );

/* function restrict_comment_editing( $caps, $cap ) {
 global $pagenow;

if ( get_user_role( array('administrator', 'editor') ) ) {
echo "test";
if ( 'edit_post' == $cap && 'edit-comments.php' == $pagenow ) {
$caps[] = 'moderate_comments';
}
}

return $caps;
}
add_filter('map_meta_cap', 'restrict_comment_editing', 10, 3); */

/* ! undocumented functions */
function modify_capabilities () {
    $role = get_role('contributor');
    $role->add_cap('upload_files');
    $role = get_role('subscriber');
    $role->add_cap('edit_posts');
    $role->add_cap('delete_posts');
    $role->add_cap('upload_files');
}
add_action( 'admin_init', 'modify_capabilities' );

/* note: http://codex.wordpress.org/Dashboard_Widgets_API */
function modify_dashboardwidgets () {
    global $wp_meta_boxes;
    if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['w3tc_latest']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['w3tc_pagespeed']);
    }
}
add_action( 'wp_dashboard_setup', 'modify_dashboardwidgets' );

/* RE-ORDER ADMIN MENU */
function menu_order_filter($menu) {
    $menu = array (
            0 => 'index.php',
            1 => 'separator1',
            2 => 'edit.php?post_type=gp_news',
            3 => 'edit.php?post_type=gp_events',
            4 => 'edit.php?post_type=gp_jobs',
            5 => 'edit.php?post_type=gp_competitions',
            6 => 'edit.php?post_type=gp_people',
            7 => 'edit.php?post_type=gp_advertorial',
            8 => 'edit.php?post_type=gp_productreview',
            9 => 'edit.php?post_type=gp_projects',
            10 => 'edit.php?post_type=gp_greengurus',
            11 => 'edit.php?post_type=gp_katiepatrick',
            12 => 'edit-comments.php',
            13 => 'separator2',
            14 => 'upload.php',
            15 => 'link-manager.php',
            16 => 'edit.php?post_type=page'
    );

    return $menu;
}
add_filter('custom_menu_order', create_function('', 'return true;'));
add_filter('menu_order', 'menu_order_filter');

/* ONLY ALLOW CERTAIN PAGES FOR SUBSCRIBERS (very hacky!) */
function redirect_disallowed_pages () {
    if ( get_user_role( array('subscriber') ) ) {
        $admin_url = get_admin_url();
        $allowed_urls = array(
                $admin_url . 'profile.php',
                $admin_url . 'post.php',
                $admin_url . 'admin-ajax.php',
                $admin_url . 'media-upload.php',
                $admin_url . 'wp-login.php',
                $admin_url . 'edit.php?post_type=gp_events',
                $admin_url . 'post-new.php?post_type=gp_events',
                $admin_url . 'edit.php?post_type=gp_competitions',
                $admin_url . 'post-new.php?post_type=gp_competitions',
                $admin_url . 'edit.php?post_type=gp_advertorial',
                $admin_url . 'post-new.php?post_type=gp_advertorial',
                $admin_url . 'edit.php?post_type=gp_projects',
                $admin_url . 'post-new.php?post_type=gp_projects'
        );

        $current_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

        $redirect_this = true;
        foreach ($allowed_urls as $allowed_url) {
            if (substr($current_url, 0, strlen($allowed_url)) == $allowed_url) {
                $redirect_this = false;
            }
        }

        if ($redirect_this == true) {
            wp_safe_redirect('/wp-admin/profile.php');
        }
    }
}
add_action( 'admin_init', 'redirect_disallowed_pages' );

function hideUpdateNag() {
    if ( !get_user_role( array('administrator') ) ) {
        remove_action( 'admin_notices', 'update_nag', 3 );
    }
}
add_action('admin_menu','hideUpdateNag');

/* MODIFY GENERIC POST TYPE VALUES */
/* function change_post_menu_label() {
 global $menu;

if ( get_user_role( array('subscriber') ) ) {
unset($menu[2]); # dashboard
unset($menu[4]); # seperator
unset($menu[28]); # jobs
unset($menu[30]); # people
unset($menu[31]); # katie patrick
unset($menu[32]); # product review
unset($menu[35]); # green gurus
unset($menu[26]); # news
unset($menu[10]); # media
}

if ( !get_user_role( array('administrator') ) ) {
unset($menu[5]);
}

if ( get_user_role( array('contributor') ) ) {
unset($menu[28]); # jobs
unset($menu[29]); # competitions
unset($menu[30]); # people
unset($menu[31]); # katie patrick
unset($menu[32]); # product review
unset($menu[35]); # green gurus
}

if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
unset($menu[25]); # comments
unset($menu[75]); # tools
unset($menu[80]); # settings
}

} */

function change_post_menu_label() {
    global $menu;

    if ( get_user_role( array('subscriber') ) ) {
        unset($menu[2]); # dashboard
        unset($menu[4]); # seperator
        unset($menu[29]); # people
        #unset($menu[31]); # projects
        unset($menu[26]); # news
        #unset($menu[27]); # events
        #unset($menu[30]); # advertorials
        unset($menu[10]); # media
    }

    if ( !get_user_role( array('administrator') ) ) {
        unset($menu[5]); # posts
    }

    if ( get_user_role( array('contributor') ) ) {
        unset($menu[28]); # competitions
        unset($menu[29]); # people
    }

    if ( get_user_role( array('contributor', 'author', 'subscriber') ) ) {
        unset($menu[5]); # posts
        unset($menu[15]); # links
        unset($menu[20]); # pages
        unset($menu[25]); # comments
        unset($menu[60]); # appearance
        unset($menu[65]); # plugins
        #unset($menu[70]); # users
        unset($menu[75]); # tools
        unset($menu[80]); # settings
        unset($menu[100]); # gp-theme
        unset($menu[101]); # gp-directory
        unset($menu[102]); # syndication
        unset($menu[103]); # performance
    }

}

add_action( 'admin_menu', 'change_post_menu_label' );

/* This only works for labels array, wordpress ignores everything else.

function change_post_object_label() {
global $wp_post_types;

$wp_post_types['post']->labels->name = 'News';
$wp_post_types['post']->labels->singular_name = 'News';
$wp_post_types['post']->labels->add_new = 'Add News';
$wp_post_types['post']->labels->add_new_item = 'Add News';
$wp_post_types['post']->labels->edit_item = 'Edit News';
$wp_post_types['post']->labels->new_item = 'News';
$wp_post_types['post']->labels->view_item = 'View News';
$wp_post_types['post']->labels->search_items = 'Search News';
$wp_post_types['post']->labels->not_found = 'No News found';
$wp_post_types['post']->labels->not_found_in_trash = 'No News found in Trash';
$wp_post_types['post']->labels->parent_item_colon = '';
$wp_post_types['post']->labels->menu_name = 'News';
$wp_post_types['post']->menu_icon = get_bloginfo( 'template_url' ).'/template/cup.png';
$wp_post_types['post']->rewrite = array( 'slug'=>'news', 'with_front'=>false, 'pages'=>true, 'feeds'=>true );

$newpost = array(
        'post' => array (
                'labels' => array(
                        'name' => _x( 'News', 'post type general name' ),
                        'singular_name' => _x( 'News', 'post type singular name' ),
                        'add_new' => _x( 'Add New', 'News' ),
                        'add_new_item' => __( 'Add New News' ),
                        'edit_item' => __( 'Edit News' ),
                        'new_item' => __( 'New News' ),
                        'view_item' => __( 'View News' ),
                        'search_items' => __( 'Search News' ),
                        'not_found' =>  __( 'No news found' ),
                        'not_found_in_trash' => __( 'No news found in Trash' ),
                        'parent_item_colon' => '',
                        'menu_name' => 'News'
                ),
                'menu_icon' => get_bloginfo( 'template_url' ).'/template/cup.png',
                'rewrite' => array( 'slug' => 'news' ,'with_front' => FALSE )
        )
);
$labels = array_merge($wp_post_types['post'], $newpost);
}

add_action( 'init', 'change_post_object_label' );
*/

/* CHANGE EXCERPT LENGTH */
function new_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'new_excerpt_length', 125);

/* CHANGE END OF EXCERPT */
function new_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'new_excerpt_more');

/* REMOVE WORDPRESS (in 3.1+) ADMIN BAR */
function my_function_admin_bar(){
    return false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

/* ADD & REMOVE CONTACT METHODS */
function my_new_contactmethods( $contactmethods ) {
    // Remove
    unset($contactmethods['aim']);
    unset($contactmethods['jabber']);
    unset($contactmethods['yim']);

    // Add Facebook
    $contactmethods['facebook'] = 'Facebook URL';

    // Add Linkedin
    $contactmethods['linkedin'] = 'Linkedin URL';

    // Add Twitter
    $contactmethods['twitter'] = 'Twitter ID';

    // Add Skype
    $contactmethods['skype'] = 'Skype ID';

    return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);

function sanitize_username( $username, $raw_username, $strict ) {
    $username = $raw_username;
    $username = wp_strip_all_tags( $username );
    $username = remove_accents( $username );
    // Kill octets
    $username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
    $username = preg_replace( '/&.+?;/', '', $username ); // Kill entities

    // If strict, reduce to ASCII for max portability.
    if ( $strict )
        $username = preg_replace( '|[^a-zA-Z0-9 _.\-]|i', '', $username );

    $username = trim( $username );
    // Consolidate contiguous whitespace
    $username = preg_replace( '|\s+|', ' ', $username );

    return $username;
}
add_filter( 'sanitize_user', 'sanitize_username', 10, 3 );
?>