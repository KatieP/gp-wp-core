<?php
//** All the information for advertisers to sign up **//

#1. A logged in user choses an advertiser option
#2. Explanatory content below disappears
#3. Great! You are on the $x plan. Please enter your billing details. Show hosted chargify page in a iframe.
#4. Create approval 'Great - that worked! Let's get started creating your first editorial post -> button to post form
#5. Go to member post form
#6. Member fills out form with editorial and submits - ensure advertiser can restrict by region
#7. Confirmation page says 'great job on your first post! Before going live, a real human on the GP team will need to approve your post within 24 hours. It's to make sure all our advetisers are promoting genuinely sustainable products. After you've successfully submitted 3 posts, you'll be able post directly to live without waiting for us. You'll be able to check your analytics and billing from your profile page (link to profile page).
#8. Activate 'billing' section on 'advertisers' tab on user profile.
#9. Show plan, rate and amount billed on advertiser/billing tab.

global $current_user;
$name =              $current_user->display_name;
$user_email =        $current_user->user_email;
$user_ID =           $current_user->ID;
$user_billing_url =  ($current_user->reg_advertiser == '1') ? get_author_posts_url($current_user->ID) . '#tab:billing' : '';
$site_url = get_site_url();

?>
    <script type="text/javascript">

    	function show_12_plan() {
    		document.getElementById("ad-table").className = "hidden";
      		document.getElementById("ad-info").className = "hidden";
    	    document.getElementById("ad-booking-info").className = "";
        	document.getElementById("show_12_plan").className = "";
        	document.getElementById("show_39_plan").className = "hidden";
        	document.getElementById("show_99_plan").className = "hidden";
        	document.getElementById("show_249_plan").className = "hidden";
        	document.getElementById("show_499_plan").className = "hidden";
    	}
    	
    	function show_39_plan() {
    		document.getElementById("ad-table").className = "hidden";
        	document.getElementById("ad-info").className = "hidden";
       		document.getElementById("ad-booking-info").className = "";
       		document.getElementById("show_12_plan").className = "hidden";
        	document.getElementById("show_39_plan").className = "";
        	document.getElementById("show_99_plan").className = "hidden";
        	document.getElementById("show_249_plan").className = "hidden";
        	document.getElementById("show_499_plan").className = "hidden";
    	}
    	
    	function show_99_plan() {
    		document.getElementById("ad-table").className = "hidden";
        	document.getElementById("ad-info").className = "hidden";
        	document.getElementById("ad-booking-info").className = "";
        	document.getElementById("show_12_plan").className = "hidden";
        	document.getElementById("show_39_plan").className = "hidden";
        	document.getElementById("show_99_plan").className = "";
        	document.getElementById("show_249_plan").className = "hidden";
        	document.getElementById("show_499_plan").className = "hidden";
    	}
    	
    	function show_249_plan() {
    		document.getElementById("ad-table").className = "hidden";
    	    document.getElementById("ad-info").className = "hidden";
    	    document.getElementById("ad-booking-info").className = "";
        	document.getElementById("show_12_plan").className = "hidden";
        	document.getElementById("show_39_plan").className = "hidden";
        	document.getElementById("show_99_plan").className = "hidden";
        	document.getElementById("show_249_plan").className = "";
        	document.getElementById("show_499_plan").className = "hidden";
    	}
    	
    	function show_499_plan() {
    		document.getElementById("ad-table").className = "hidden";
    	    document.getElementById("ad-info").className = "hidden";
    	    document.getElementById("ad-booking-info").className = "";
        	document.getElementById("show_12_plan").className = "hidden";
        	document.getElementById("show_39_plan").className = "hidden";
        	document.getElementById("show_99_plan").className = "hidden";
        	document.getElementById("show_249_plan").className = "hidden";
        	document.getElementById("show_499_plan").className = "";
    	}

    	function hide_ad_payment_form() {
      		document.getElementById("ad-table").className = "author_analytics";
    		document.getElementById("ad-info").className = "";
      		document.getElementById("ad-booking-info").className = "hidden";
   		}
	
	</script>

	<!--//TABLE OF AD OPTIONS--//-->

<div class = "icon-container">

	<div class = "icon-container-row">
	
	<div class = "icon-container-row">
	
	<div class = "icon-body-text"><p><p>Hi <?php echo $name; ?>! Greenpages offers an extremely effective kind of online advertising: You get to create your own editorials!</p>

		 To get started, first choose an advertiser plan</p></div>
		
			<div class="author_analytics" id="ad-table">
				<table>
					<tr> 
						<td><a href="javascript:void(0);" id="3313295" onClick="show_12_plan();"><div  class="advertiser_option_box">&nbsp;$12&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
						<td><a href="javascript:void(0);" id="27029"   onClick="show_39_plan();"><div  class="advertiser_option_box">&nbsp;$39&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
						<td><a href="javascript:void(0);" id="27028"   onClick="show_99_plan();"><div  class="advertiser_option_box">&nbsp;$99&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
						<td><a href="javascript:void(0);" id="3313296" onClick="show_249_plan();"><div class="advertiser_option_box">$249</div><span class="grey-text">weekly max spend</span></a></td>
						<td><a href="javascript:void(0);" id="3313297" onClick="show_499_plan();"><div class="advertiser_option_box">$499</div><span class="grey-text">weekly max spend</span></a></td>
					</tr>
	
					<tr> 
						<td>$1.90 / click </td>
						<td>$1.90 / click </td>
						<td>$1.90 / click </td>
						<td>$1.80 / click </td>
						<td>$1.70 / click </td>
					</tr>
				</table>
			</div>
    
    		<div class="clear"></div>

			<br /><br />

			<!--//BILLING FORMS--//-->

   			 <div id="ad-booking-info" class="hidden">
        		<?php 
				if ( !is_user_logged_in() ) {
					$site_url = get_site_url();
					echo 'You\'ll need to be logged in to setup you ad. <a href="'.$site_url.'/welcome">Create an account</a> or <a href="'.$site_url.'/wp-login">log in</a>.';
				} else {
						// Display fields billing details
					// Todo: Add a paypal option
					// Grab user email and id from $current_user so it can be added to billing form and sent to chargify
			
			$user_details =    '?email='. $user_email .'&reference='. $user_ID;
			$billing_url_12 =  ( !empty($user_billing_url) ) ? $user_billing_url : 'https://green-pages.chargify.com/h/3313295/subscriptions/new'. $user_details;
			$billing_url_39 =  ( !empty($user_billing_url) ) ? $user_billing_url : 'https://green-pages.chargify.com/h/27029/subscriptions/new'. $user_details;
			$billing_url_99 =  ( !empty($user_billing_url) ) ? $user_billing_url : 'https://green-pages.chargify.com/h/27028/subscriptions/new'. $user_details;
			$billing_url_249 = ( !empty($user_billing_url) ) ? $user_billing_url : 'https://green-pages.chargify.com/h/3313296/subscriptions/new'. $user_details;
			$billing_url_449 = ( !empty($user_billing_url) ) ? $user_billing_url : 'https://green-pages.chargify.com/h/3313297/subscriptions/new'. $user_details;
			
			?>
			<div id="show_12_plan" class="hidden">
				<div id="my-advertise">
				    <div id="advertorial">
				    	<span>
				    	    <a href="<?php echo $billing_url_12; ?>">
				    	        <input type="button" value="Confirm max $12/week plan">
				    	    </a>
				    	</span>
						<div class="clear"></div>	
				    </div>
				</div>
			</div>			
			<div id="show_39_plan" class="hidden">		
				<div id="my-advertise">
				    <div id="email">
				    	<span>
				    	    <a href="<?php echo $billing_url_39; ?>">
				    	        <input type="button" value="Confirm max $39/week plan">
				    	    </a>
				    	</span>
						<div class="clear"></div>	
				    </div>
				</div>
			</div>
			<div id="show_99_plan" class="hidden">	
				<div id="my-advertise">
				    <div id="competition">
				    	<span>
				    	    <a href="<?php echo $billing_url_99; ?>">
				    	        <input type="button" value="Confirm max $99/week plan">
				    	    </a>
				    	</span>
						<div class="clear"></div>	
				    </div>
				</div>	
			</div>		
			<div id="show_249_plan" class="hidden">
				<div id="my-advertise">
				    <div id="listing">
				    	<span>
				    	    <a href="<?php echo $billing_url_249; ?>">
				    	        <input type="button" value="Confirm max $249/week plan">
				    	    </a>
				    	</span>
						<div class="clear"></div>	
				    </div>
				</div>
			</div>		
			<div id="show_499_plan" class="hidden">	
				<div id="my-advertise">
				    <div id="volunteer">
				    	<span>
				    	    <a href="<?php echo $billing_url_449; ?>">
				    	        <input type="button" value="Confirm max $449/week plan">
				    	    </a>
				    	</span>
						<div class="clear"></div>	
				    </div>
				</div>
			</div>	
		    <?php
		}
		?>
		<div id="my-advertise">
		    <div id="grey">
		   	    <span>
    				<input type="button" value="<-- Back" onclick="hide_ad_payment_form(); return false;" />
    			</span>
    		</div>
    	</div>	
    	<div class="clear"></div>
    </div>
	<!--//INITIAL PAGE INFORMATION
	
	<div id="ad-info" >--//-->
	
	<div class = "icon-body-text">
	

		
		<p> You create the editorial post, then we send it out to the Greenpages members. You only pay for the clicks you receive in 
		cost-per-click model. No click, no payment! You can upgrade, downgrade or pause your advertiser plan at any time.</p>

		<p><a href="http://www.thegreenpages.com.au/wp-content/uploads/2012/04/circle2-wide.jpg?39a4ff">
			<img class="alignleft size-full wp-image-15017" title="green pages advertising" 
			     src="http://www.thegreenpages.com.au/wp-content/uploads/2012/04/circle2-wide.jpg?39a4ff" 
			     alt="green pages advertising" width="600" height="350" /></a>
		</p>
		
		<p>How it works</p>

		<p>Step 1. You chose a plan. You will never be billed more than the plan you choose.</p>

		<p>Step 2. Create an editorial post about your product or service.</p>
		
		<p>Your editorial post will be promoted on the www.greenpag.es site and appear on the homepage feed until you reach your weekly maximum expenditure. Once your budget has been reached, your editorial posts will be paused from view. Your posts will become visible again when the 
		next week's billing cycle commences.</p>

		<p>The more frequently you post, the more people will view your product editorial. If you want more coverage, keep the posts fresh and coming!</p>

		<p>We've got maps too! All product editorials have an icon on the maps</p>

        
		
		<div>

			
			<p><a href="<?php echo $site_url;?>/about/media-kit/">Learn more about who reads greenpag.es</p>
						
			<h1><strong><a name="directory">Product Editorial Subscription <br />$12/week - $499/week </a></strong></h1>
			<p>Be discovered by thousands of enthusiastic sustainability professionals and green consumers. 
			This subscription allows you to create sponsored editorials in our <a href="<?php echo $site_url;?>/eco-friendly-products/" target="_blank">’Products’ section</a> as often as you desire! You'll be able to check your clicks from your member profile.</p>
			
			<div style="float: center; margin-top: 20px;">
					<iframe src="http://player.vimeo.com/video/41352429" width="530" height="300" frameborder="0" 
					        webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
			</div>

			
			<p>Your post will be promoted to our 20,000 members, to our Facebook page, Twitter and will remain on the GP homepage for approximately one day. Each product post includes two images, 500 words and a 'Buy it!' or 'Inquire Now!' button.
				<a href="<?php echo $site_url;?>/eco-friendly-products/" target="_blank">See example.</a></p>
				
				<div id="my-advertise">
					<div id="advertorial">
					    <span>
					        <a href="<?php echo $site_url;?>/advertisers/">
						        <input type="button" value="Create a Product Post" />
						    </a>
						</span>
					</div>
				</div>
				<div class="clear"></div>
	
				<br /><br /><br />
	
				<h1><strong><a name="email">Exclusive Email $3,500</a></strong></h1>
				<p> We will send out a Green Pages endorsed email recommending your product or service as ‘Product of the Month’. 
				This is the most powerful means of gaining instant recognition from 20,000 of Australia’s environmental decision makers. 
				We provide a beautiful html email that can be tailored to your business with your own images, links and personal story. 
				Only one per month is available and we only accept leading green businesses as it includes our endorsement.</p>
				<div style="float: center; margin-top: 20px;">
					<iframe src="http://player.vimeo.com/video/41414230" width="530" height="300" frameborder="0" 
					        webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
				
				
				<div class = "clear"></div>
				
				</div>
					
				<div id="my-advertise">
					<div id="listing">
						<span>
							<a href="https://green-pages.chargify.com/h/796281/subscriptions/new">
								<input type="button" value="Book Email Now" />
							</a>
						</span>
					</div>
				</div>

				
				<div class="clear"></div>
				<br /><br /><br />
								
				<h1><strong>Ten reasons why Greenpages is a better deal that the other online advertising</strong></h1>
				
				<p>We've learned that product editorials receive over 900 percent 
					higher click-through-rates than common online ads such as website banner ads,  Google adwords or Facebook ads.</p>
			
				<p><strong>1. Google Adwords</strong> require you to bid higher and higher to increase your click through rate. Only a small amount of clicks each 	week will be available to you at a low price for less that $2.00 per click. Some Google Ads are as high as $20 per click in compeditive markets. <em>The GP difference: We offer a very affordable flat rate of $1.70 - $1.90 per click. </em> 			</p>
			
				<p><strong>2. Facebook</strong> requires a minimum spend of $10 / day which amounts to about $300 / month - quite steep for many small 			businesses. Facebook also require about a $2 &#8211; $3 per click spend. <em>The GP difference: On Green Pages, your minimum spend is $12 per week (1 tenth of Facebook!). Clicks on Green Pages are also lower cost than on Facebook.</em> </p>
			
				<p><strong>3. Newspaper sites such as New York Times, Sydney Morning Herald or Huffington Post</strong> charge a $70 CPM (one thousand page impressions) for display ads. Click through rates for these kind of banner or display ads are dropping dramatically and are on <a href="http://en.wikipedia.org/wiki/Click-through_rate">average only 0.2% - 0.4%</a> <em>The GP difference: We only offer editorial advertisements which have 10% - 20% click through rate and you only pay for the clicks you receive.</em> </p>

<p><strong>4. Greenpages</strong> has a loyal following of over 20,000 members and between 1,000 and 5,000 visitors every day. Green Pages is well known in the environmental community as a place to find environmental products and services. We work exclusively in the core community of environmental professionals.<em>The GP difference: Your advertising hits the spot. There is no other membership available that has such direct access to the world's leading green, environmental and sustainability professionals.</em></p>


				<p><strong>5. We provide an easy to use DIY advertiser centre.</strong> You can set up and serve your own ads, upgrade and downgrade at any time.  </p>

				<p><strong>6. Yelp</strong> charge a flat fee which is un-transparent and works out to possibly $150 &#8211; $200 CPM. This is around 10 times the industry average CPM rate. They don&#8217;t even show you conversion rates from impressions to clicks! They also require a 6 month commitment. <a href="http://www.raymondfong.net/misc/a-candid-yelp-advertising-review-is-yelp-ripping-people-off/"  onclick="javascript:_gaq.push(['_trackPageview','/yoast-ga/14971/2/outbound-article/']);">Read more</a> <em>The GP difference: We show your clicks and impressions transparently on your member profile. There is no time commitment and you can cancel any time. We work hard to make sure are advertising rates are less that the industry standard.</em></p>

				<p><strong>7. Green Pages</strong> offers an affordable flat rate per click at $1.70 - $1.90 </p>

				<p><strong>8. Green Pages</strong> uses trackable links which mean our links to your site will help your SEO. <em>The GP difference: Most other sites prevent trackable outbound links. On Green Pages, we do allow links to be trackable by Google, that means your link on Green Pages will help your site&#8217;s SEO, meaning it will help you get higher up on Google&#8217;s search results. </em> </p>

				<p><strong>9. We offer</strong> a personalised service and will work with each client individually to ensure you get a good result for your subscription. Your weekly subscription means the world to us. If you ever feel you are not getting a result, let us know and we&#8217;ll attend to it immediately to supplement your advertising. <em>The GP difference: You will always have a &#8216;real human&#8217; to call or email any time about any issue at all. We are prepared to add any supplementary promotion you need if an ad is not working well.</em></p>

				<p><strong>10. Your money</strong> supports our unique content model of distributing material direct from NGO&#8217;s to a wider audience. Many NGOs rely on Green Pages is their greatest means of letting people know about the important problems they are solving. <em>The GP difference: Your money is going to support Green Pages, an independent technology company that provides the world&#8217;s only aggregate of all NGO news. We bring together the news, campaigns and projects that are saving the planet and put them in touch with thousands of people every day. The more advertisers we have, the better equipped we are to improve and built this service. </em> </p>
				
				
				<div id="my-advertise">
					<div id="advertorial">
					    <span>
					        <a href="<?php echo $site_url;?>/advertisers/">
						        <input type="button" value="Create a Product Post Now" />
						    </a>
						</span>
					</div>
				</div>
				<div class="clear"></div>
	
				<br /><br /><br />

				
<br /><br /><br />

				</div>
			</div>
		</div>
	</div>
</div>

