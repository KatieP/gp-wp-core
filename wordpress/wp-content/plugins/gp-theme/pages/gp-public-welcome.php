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
        <?php
        # Display pink button depending on logged in status
        if ( !is_user_logged_in() ) {
        ?>
        <h1 class="loop-title">
            <a href="<?php echo $site_url; ?>/forms/member-registration-form/" target="_blank">
                Get connected with your local movement
            </a>
        </h1>
        <a href="<?php echo $site_url; ?>/forms/member-registration-form/">
            <img src="<?php echo $site_url; ?>/wp-content/themes/gp-au-theme/template/images/berkeley-chart5.jpeg" 
                 alt="Welcome to greenpag.es" title="Welcome to greenpag.es"/>
        </a>
        <?php 
        }

        # Display pink button depending on logged in status
        if ( !is_user_logged_in() ) {
        ?>
    	<div id="post-product-button-bar">
            <a href="<?php echo $site_url; ?>/forms/member-registration-form/" target="_blank">
                <span id="product-button">Register</span>
            </a>
        </div>
        <div class="clear"></div>            
        <?php
        } 
        ?>
        <div class="icon-body-text">
        <?php
            # Display copy depending on logged in status
            if ( !is_user_logged_in() ) {
                ?>
                Sign up to become a member to receive notifications of the news, 
                events and projects and awesome world-changing activities happening in 
                your local area. For example if you are based in Los Angeles and you add the 
                &#8216;solar&#8217; keyword tag to your member profile, you&#8217;ll be notified 
                of all solar related activities in LA.
                <?php
            } 
            ?>
        </div>
    </div>
    <div class="icon-container-row">
        <h1 class="loop-title">
            <a href="#" target="_blank">
                Create a post on greenpag.es
            </a>
        </h1>
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
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status
                     if ( !is_user_logged_in() ) {
                         echo $site_url .'/forms/create-my-event-post-public/';
                     } else {
                         echo $site_url .'/forms/create-event-post/';
                     }
                 ?>"> 
            <div class="inner-icon-container">
                <span class="icon-party-balloon"></span>
                <span class="icon-heading-text">Events</span>  
                <span class="icon-heading-desc">Add to the calendar</span>
            </div> 
        </a> 
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status and user role
                     if ( !is_user_logged_in() ) {
                         echo $site_url .'/forms/create-my-product-post-public/';
                     } else if ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) {
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
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status
                     if ( !is_user_logged_in() ) {
                         echo $site_url .'/forms/create-my-project-post-public/';
                     } else {
                         echo $site_url .'/forms/create-project-post/';
                     }
                 ?>"> 
            <div class="inner-icon-container">
                <span class="icon-cog-semifull"></span>
                <span class="icon-heading-text">Projects</span> 
                <span class="icon-heading-desc">Find support for a project</span> 
            </div> 
        </a>
    </div>
    <div class="icon-container-row">
        <h1 class="loop-title">
            <?php
            # Display link depending on logged in status
            if ( !is_user_logged_in() ) {
            ?>
                <a href="<?php echo $site_url; ?>/forms/member-registration-form/" target="_blank">
                    Want to promote your business?
                </a>
            <?php
            } 
            ?>      
        </h1>
        <div class="icon-body-text">
            Want to promote your business? <a href="<?php echo $site_url; ?>/about/rate-card/">Rate Card</a>. <a href="<?php echo $site_url; ?>/about/media-kit/">Media Kit.</a>
        </div>
        <?php
        # Display orange button depending on logged in status
        if ( !is_user_logged_in() ) {
        ?>
        	<div id="post-product-button-bar">
                <a href="<?php echo $site_url; ?>/forms/member-registration-form/" target="_blank">
                    <span id="product-button">Advertise</span>
                </a>
            </div>
        	<div class="clear"></div> 
        <?php
        }
        ?>       
    </div>
</div>