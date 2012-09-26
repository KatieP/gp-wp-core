<?php
/**
 * The main welcome page - designed to describe the core of gp 
 * called in (no title) page in wp-admin pages menu
 * http://greenpag.es/welcome
 */
 
global $current_user;

?>
<div class="icon-container">
    <h1 class="loop-title">
        <?php
        # Display main heading depending on logged in status
        if ( !is_user_logged_in() ) {
            ?>
            <a href="/wp-register" target="_blank">
                Welcome! Lets get started
            </a>
            <?php
        } else {
            
            echo '<a href="#">
                      Welcome '. $current_user->display_name .'!
                  </a>';
        }
        ?>
    </h1>
    <div class="icon-container-row">
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status and user role
                     if ( !is_user_logged_in() ) {
                         echo '/get-involved/become-a-content-partner/';
                     } else if ( is_user_logged_in() && get_user_role( array('subscriber') ) ) {
                         echo '/get-involved/become-a-content-partner/';
                     } else if ( is_user_logged_in() && get_user_role( array('contributor') ) ) {
                         echo '/forms/create-news-post/';                         
                     } else if ( is_user_logged_in() && get_user_role( array('administrator') ) ) {
                         echo '/forms/create-news-post/';
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
                         echo '/forms/create-my-event-post-public/';
                     } else {
                         echo '/forms/create-event-post/';
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
                         echo '/forms/create-my-product-post-public/';
                     } else if ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) {
                         echo '/forms/create-product-post-subscriber/';
                     } else {
                         echo '/forms/create-product-post/';                         
                     } 
                 ?>">
            <div class="inner-icon-container">
                <span class="icon-light-on"></span>
                <span class="icon-heading-text">Products</span>  
                <span class="icon-heading-desc">Promote an eco product</span> 
            </div> 
        </a> 
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status and user role
                     if ( !is_user_logged_in() ) {
                         echo '/forms/post-my-competition-public/';
                     } else if ( is_user_logged_in()  && $current_user->reg_advertiser == 1 ) {
                         echo '/forms/create-competition-post-subscriber/';
                     } else {
                         echo '/create-competition-post/';                         
                     } 
                 ?>"> 
            <div class="inner-icon-container">
                <span class="icon-trophy-2"></span>
                <span class="icon-heading-text">Competitions</span>  
                <span class="icon-heading-desc">Create a giveaway</span> 
            </div> 
        </a> 
        <a href="<?php
                     # Direct user to appropriate form depending on logged in status
                     if ( !is_user_logged_in() ) {
                         echo '/forms/create-my-project-post-public/';
                     } else {
                         echo '/forms/create-project-post/';
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
            <a href="/wp-register" target="_blank">
                Get connected with your local movement
            </a>
        </h1>
        <a href="/wp-register">
            <img src="/wp-content/themes/gp-au-theme/template/images/berkeley-chart5.jpeg" 
                 alt="Welcome to greenpag.es" title="Welcome to greenpag.es"/>
        </a>
    </div>
    <div class="icon-container-row">
        <div class="icon-body-text">
        <?php
            # Display copy depending on logged in status
            if ( !is_user_logged_in() ) {
                ?>
                Sign up to become a member so you can receive notifications of all of the news, 
                events and projects and awesome world-changing activities that are happening in 
                your local area. For example if you are based in Los Angeles and you add the 
                &#8216;solar&#8217; keyword tag to your member profile, you&#8217;ll be notified 
                of all solar related activities in LA.
                <?php
            } else {
                ?>
                Update your profile, set your location and keyword tags of interest and connect 
                with your green movement. 
                <?php
            }
            ?>
        </div>
        <div id="post-product-button-bar">
            <?php
            # Display orange button depending on logged in status
            if ( !is_user_logged_in() ) {
                ?>
                <a href="/wp-register" target="_blank">
                    <span id="product-button">Register</span>
                </a>
                <?php
            } else {
                ?>
                <a href="/wp-admin/profile.php" target="_blank">
                    <span id="product-button">Edit Profile</span>
                </a>
                <?php
            }
            ?>
        </div>
        <div class="clear"></div>
    </div>
    <div class="icon-container-row">
        <h1 class="loop-title">
            <?php
            # Display link depending on logged in status
            if ( !is_user_logged_in() ) {
                ?>
                <a href="/wp-register" target="_blank">
                    Want to promote your business?
                </a>
                <?php
            } else {
                ?>
                <a href="/forms/sign-up-for-monthly-advertiser-subscription-39-month/" target="_blank">
                    Want to promote your business?
                </a>
                <?php
            }
            ?>        
        </h1>
        <div class="icon-body-text">
            Join our $39 / month advertiser subscription plan. You&#8217;ll receive a page in 
            our green business directory + monthly product posts and / or competitions that we 
            share with our community. <a href="/about/rate-card/">Rate Card</a>. <a href="/about/media-kit/">Media Kit.</a>
        </div>
        <div id="post-product-button-bar">
            <?php
            # Display orange button depending on logged in status
            if ( !is_user_logged_in() ) {
                ?>
                <a href="/wp-register" target="_blank">
                    <span id="product-button">Advertise</span>
                </a>
                <?php
            } else {
                ?>
                <!-- Need to add css to change color of this button
                <a href="/forms/sign-up-for-monthly-advertiser-subscription-39-month/" target="_blank">
                    <span id="product-button">Advertise</span>
                </a>
                -->
                <?php
            }
            ?>
        </div>
        <div class="clear"></div>        
    </div>
</div>