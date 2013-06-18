<?php
/**
 * The main welcome page - designed to describe the core of gp 
 * called in (no title) page in wp-admin pages menu
 * http://greenpag.es/welcome
 */
 
global $current_user;
$site_url = get_site_url(); 
?>
<div class="icon-container">
    <div class="icon-container-row">
        <?php if ( !is_user_logged_in() ) { ?>
        <h1 class="loop-title">
            <a href="<?php echo $site_url; ?>/forms/member-registration-form/" target="_blank">
                Get connected with your local movement
            </a>
        </h1>
        <div>
        	<iframe src="http://player.vimeo.com/video/65615184?byline=0&amp;portrait=0" width="522" height="292" frameborder="0"></iframe>
        </div>
        <div id="post-product-button-bar">
            <a href="<?php echo $site_url; ?>/forms/member-registration-form/">
                <span id="product-button">Join!</span>
            </a>
        </div> 
        <div class="clear"></div> 
        <div class="icon-body-text">
                Sign up to share your projects and get involved with awesome 
                world-changing news, projects and events happening in 
                your local area. For example if you are based in Los Angeles and 
                are passionate about solar, you&#8217;ll be invited to join in
                all the solar related activities in LA. 
        </div>
    </div>
    <div class="icon-container-row">
        <div class="icon-body-text">
            Already a member? <a href="">Log in</a>.
        </div>
    </div>
    <?php } ?>
    <div class="icon-container-row">
        <h1 id="big-heading">Create a post on greenpag.es</h1>
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status and user role
                     if ( !is_user_logged_in() ) {
                         echo $site_url .'/get-involved/become-a-content-partner/';
                     } else if ( is_user_logged_in() && get_user_role( array('subscriber') ) ) {
                         echo $site_url .'/get-involved/become-a-content-partner/';
                     } else if ( is_user_logged_in() && get_user_role( array('contributor') ) ) {
                         echo $site_url .'/forms/create-news-post/';                         
                     } else if ( is_user_logged_in() && get_user_role( array('administrator') ) ) {
                         echo $site_url .'/forms/create-news-post/';
                     }
                 ?>"> 
            <div class="inner-icon-container">
                <span class="icon-thunderbolt"></span>
                <span class="icon-heading-text">News</span>
                <span class="icon-heading-desc">Post NGO media releases</span>
            </div> 
        </a> 
        <a href="<?php echo $site_url; ?>/forms/create-event-post/"> 
            <div class="inner-icon-container">
                <span class="icon-party-balloon"></span>
                <span class="icon-heading-text">Events</span>  
                <span class="icon-heading-desc">Add to the <br />calendar</span>
            </div> 
        </a> 
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status and user role
                     if ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) {
                         echo $site_url .'/forms/create-product-post-subscriber/';
                     } else {
                         echo $site_url .'/forms/create-product-post/';                         
                     } 
                 ?>">
            <div class="inner-icon-container">
                <span class="icon-light-on"></span>
                <span class="icon-heading-text">Products</span>  
                <span class="icon-heading-desc">Promote an eco product</span> 
            </div> 
        </a> 
        <a href="<?php echo $site_url; ?>/forms/create-project-post/"> 
            <div class="inner-icon-container">
                <span class="icon-cog-semifull"></span>
                <span class="icon-heading-text">Projects</span> 
                <span class="icon-heading-desc">Find support for a project</span> 
            </div> 
        </a>
        <a href="<?php echo $site_url; ?>/forms/member-registration-form/">
            <img src="<?php echo $site_url; ?>/wp-content/themes/gp-au-theme/template/images/berkeley-chart5.jpeg" 
                 alt="Welcome to greenpag.es" title="Welcome to greenpag.es"/>
        </a>        
    </div>
    <div class="icon-container-row">
        <div class="icon-body-text">
            Want to promote your business? <a href="<?php echo $site_url; ?>/advertisers/">Post an ad</a>.
        </div>      
    </div>
</div>