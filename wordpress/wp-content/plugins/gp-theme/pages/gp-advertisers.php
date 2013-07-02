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


#$author_name = get_usermeta->'name';
global $current_user;
$name = $current_user->display_name;
$user_email = $current_user->user_email;
$user_ID = $current_user->ID;
		
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
	
	<br /><br /><br />
	
	<h1><strong>Hi <?php echo $name; ?>! Choose an advertiser plan</strong></h1>
	<div class="author_analytics" id="ad-table">
		<table>
			<tr> 
				<td><a href="#" id="3313295" onClick="show_12_plan();"><div class="advertiser_option_box">&nbsp;$12&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
				<td><a href="#" id="27029" onClick="show_39_plan();"><div class="advertiser_option_box">&nbsp;$39&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
				<td><a href="#" id="27028" onClick="show_99_plan();"><div class="advertiser_option_box">&nbsp;$99&nbsp;</div><span class="grey-text">weekly max spend</span></a></td>
				<td><a href="#" id="3313296" onClick="show_249_plan();"><div class="advertiser_option_box">$249</div><span class="grey-text">weekly max spend</span></a></td>
				<td><a href="#" id="3313297" onClick="show_499_plan();"><div class="advertiser_option_box">$499</div><span class="grey-text">weekly max spend</span></a></td>
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
    	<input type="button" value="<-- Back" onclick="hide_ad_payment_form(); return false;" />
    	<div class="clear"></div>
        <?php 
		if ( !is_user_logged_in() ) {
			$site_url = get_site_url();
			echo 'You\'ll need to be logged in to setup you ad. <a href="'.$site_url.'/welcome">Create an account</a> or <a href="'.$site_url.'/wp-login">log in</a>.';
		} else {
			// Display fields billing details
			// Todo: Add a paypal option
			// Grab user email and id from $current_user so it can be added to billing form and sent to chargify
			$user_details = '?email='. $user_email .'&reference='. $user_ID;		
			?>
			<div id="show_12_plan" class="hidden">
        		<iframe src="https://green-pages.chargify.com/h/3313295/subscriptions/new<?php echo $user_details; ?>" class="chargify-frame" scrolling="no"></iframe>
			</div>			
			<div id="show_39_plan" class="hidden">		
				<iframe src="https://green-pages.chargify.com/h/27029/subscriptions/new<?php echo $user_details; ?>" class="chargify-frame" scrolling="no"></iframe>
			</div>
			<div id="show_99_plan" class="hidden">		
				<iframe src="https://green-pages.chargify.com/h/27028/subscriptions/new<?php echo $user_details; ?>" class="chargify-frame" scrolling="no"></iframe>
			</div>		
			<div id="show_249_plan" class="hidden">
				<iframe src="https://green-pages.chargify.com/h/3313296/subscriptions/new<?php echo $user_details; ?>" class="chargify-frame" scrolling="no"></iframe>
			</div>		
			<div id="show_499_plan" class="hidden">		
				<iframe src="https://green-pages.chargify.com/h/3313297/subscriptions/new<?php echo $user_details; ?>" class="chargify-frame" scrolling="no"></iframe>
			</div>	
		    <?php
		}
		?>

    </div>
	<!--//INITIAL PAGE INFORMATION--//-->
	
	<div id="ad-info" >
	
		<h1>How does greenpag.es advertising work?</h1>

		<p>Greenpag.es offers an extremely effective kind of online advertising: you get to create your own editorial! 
		We've learned over the years that product editorials receive over 1000 percent 
		greater click through rates than common online ads such as banner ads, search ads on Google or Facebook ads. You create the 		
		editorial post - we send it out to the greenpag.es members and you only pay for the clicks you receive in 
		cost-per-click model. No click, no payment! You can upgrade, downgrade or pause your advertiser plan at 
		any time.</p>

		<p><a href="http://www.thegreenpages.com.au/wp-content/uploads/2012/04/circle2-wide.jpg?39a4ff">
			<img class="alignleft size-full wp-image-15017" title="green pages advertising" 
			     src="http://www.thegreenpages.com.au/wp-content/uploads/2012/04/circle2-wide.jpg?39a4ff" 
			     alt="green pages advertising" width="600" height="350" /></a>
		</p>


		<p>1. You chose a plan - you will never be billed more that the plan you choose.</p>

		<p>2. Create an editorial post about your product or service</p>
		
		<p>3. You product editorial will be shown to the greenpag.es members and appear on the homepage feed until you reach your weekly maximum expenditure. 
		Once your budget has been reached, your product editorials will be paused from view. They will resume again when the 
		next week's billing cycle commences.</p>

		<p>4. The more frequently you post, the more people will view your product editorial. If you want more coverage, keep the posts fresh and coming!</p>

		<p>5. All product editorials are posted with an icon on the maps and searchable in the products section.</p>

        
		
		<div>

			<br /><br />
			<h1><strong><a href="<?php echo $site_url;?>/about/media-kit/">Who reads greenpag.es?</a></a></strong></h1>
			<br /><br />
			
			
			<h1><strong><a name="directory">1. Monthly Editorials Subscription $39 / month</a></strong></h1>
			<p>Be discovered by thousands of enthusiastic sustainability professionals and green consumers. 
			This subscription allows you to create a sponsored editorial on greenpag.es every month! 
			You should receive about 10 - 40 clicks from each editorial you post. 
			You'll be able to check your clicks from your user profile.</p>
	
			<p>The subscription plan also includes a listing in The Green Pages Directory which lists over 10,000 environment and sustainability businesses. <a href="http://directory.thegreenpages.com.au/index.asp?page_id=105&amp;id=4608&amp;company_id=2050"   target="_blank">See an example page for Biome</a>. </p>
			<div style="float: right; margin-top: 20px;">
			    <iframe src="http://player.vimeo.com/video/41346738" frameborder="0" width="350" height="218"></iframe>
			</div>
	
    		<p>The Directory Page includes:</p>
			<ul>
				<li>Website Link</li>
				<li>All business contact details</li>
				<li>150 words</li>
				<li>Five images</li>
				<li>Google Maps</li>
				<li>Inquiry Form</li>
			</ul>
			<div id="my-advertise">
				<div id="listing">
				    <span>
				        <a href="https://green-pages.chargify.com/h/3313295/subscriptions/new">
				        
					        <input type="button" value="Subscription $39/m" />
					    </a>
					</span>
				</div>
			</div>
			<div class="clear"></div>
		
			<br /><br /><br />
		
			<h1><strong><a name="product">2. Product Post $89 / post</a></strong></h1>
			<p>Perfect for one-off product promotions and press releases. 
				You can post your own editorial in our ’Products’ page. Your post will be promoted to our 20,000 members, to our Facebook page, Twitter and will remain on the GP homepage for approximately one day. 
				<a href="<?php echo $site_url;?>/eco-friendly-products/" target="_blank">See an example here.</a></p>
				<div style="float: right; margin-top: 20px;">
					<iframe src="http://player.vimeo.com/video/41352429" width="350" height="218" frameborder="0" 
					        webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
				</div>

				<p>The Product Post includes:</p>
				<ul>
					<li>Feature in the Green Razor</li>
					<li>Posted Facebook and Twitter</li>
					<li>Feature on our home page</li>
					<li>&#8216;Buy It!&#8217; button</li>
					<li>500 words</li>
					<li>2 Images</li>
				</ul>
				<div id="my-advertise">
					<div id="advertorial">
					    <span>
					        <a href="<?php echo $site_url;?>/forms/create-product-post/">
						        <input type="button" value="Post a Product $89" />
						    </a>
						</span>
					</div>
				</div>
				<div class="clear"></div>
	
				<br /><br /><br />
	
				<h1><strong><a name="email">3. Exclusive Email $3,500</a></strong></h1>
				<p> The most powerful means of gaining instant recognition by 20,000 of Australia’s environmental decision makers. 
				We will send out a Green Pages endorsed email recommending your product or service as ‘Product of the Month’. 
				This is a html email that can be tailored to your business with your own images, links and personal story. 
				Only one per month is available and we only accept leading green businesses as it includes our endorsement.</p>
				<div style="float: right; margin-top: 20px;">
					<iframe src="http://player.vimeo.com/video/41414230" width="350" height="218" frameborder="0" 
					        webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
				</div>
				<p>The Email Blast includes:</p>
				<ul>
					<li>Green Pages endorsement</li>
					<li>Exclusive share of voice</li>
					<li>Beautifully designed template</li>
					<li>Images</li>
					<li>Unlimited words</li>
					<li>Access to green professionals</li>
				</ul>
	
				<div id="my-advertise">
					<div id="listing">
						<span>
							<a href="https://green-pages.chargify.com/h/796281/subscriptions/new">
								<input type="button" value="Exclusive Email $3500" />
							</a>
						</span>
					</div>
				</div>

				<br /><br /><br /><br /><br /><br />

				<div class="clear"></div>
				<br /><br /><br /><br /><br /><br />
				
				<h1><strong>Ten reasons why Greenpag.es is a better deal that the other online advertising options</strong></h1>
			
				<p><strong>1. Google Adwords</strong> you are going to bid higher and higher to increase your click through rate. Only a small amount of clicks each 	week will be available to you. Some Google Ads are as high as $20 per click. <em>The GP difference: On Green Pages, the most you&#8217;ll pay is $3.90 per click and many advertisers get as low as $1 / click for products that are popular with the members. </em> 			</p>
			
				<p><strong>2. Facebook</strong> requires a minimum spend of $10 / day which amounts to about $300 / month which is quite steep for most small 			businesses. They require about a $2 &#8211; $3 per click spend. <em>The GP difference: On Green Pages, your minimum spend is $12 per week (1 tenth of Facebook!). Clicks on Green Pages work out on average, at lower cost than on Facebook.</em> </p>
			
				<p><strong>3. Newspaper sites such as Sydney Morning Herald</strong> charge $70 CPM for display ads, which have an ever increasingly low click through rate. Click through rates are on average 0.05% <em>The GP difference: Greenpag.es only offers editorial advertisements which have 10% - 20% click through rate.</em> </p>

				<p><strong>4. Yellow Pages</strong> offers only flat rate and print advertising. </p>

				<p><strong>5. Yelp</strong> charge per impression which is un-transparent and work out to a $150 &#8211; $200 CPM. This is around 10 times the industry average CPM rate. They don&#8217;t even show you conversion rates from impressions to clicks! They also require a 6 month commitment. <a href="http://www.raymondfong.net/misc/a-candid-yelp-advertising-review-is-yelp-ripping-people-off/"  onclick="javascript:_gaq.push(['_trackPageview','/yoast-ga/14971/2/outbound-article/']);">http://www.raymondfong.net/misc/a-candid-yelp-advertising-review-is-yelp-ripping-people-off/</a> <em>The GP difference: On Green Pages, we provide click guarantees and show your clicks and impressions transparently on your member profile. There is no time commitment and you can cancel any time. We work hard to make sure are advertising rates are less that the industry standard.</em></p>

				<p><strong>6. Green Pages</strong> offers an affordable flat rate per click at $1.70 - $1.90 </p>

				<p><strong>7. Green Pages</strong> uses trackable links which mean our links to your site will help your SEO. <em>The GP difference: Most other sites prevent trackable outbound links. On Green Pages, we do allow links to be trackable by Google, that means your link on Green Pages will help your site&#8217;s SEO, meaning it will help you get higher up on Google&#8217;s search results. </em> </p>

				<p><strong>8. We offer</strong> personalised service and will work with each client individually to ensure you get a good result for your subscription. Your $39 / month means the world to us. If you ever feel you are not getting a result, let us know and we&#8217;ll attend to it immediately to supplement your advertising. <em>The GP difference: You will always have a &#8216;real human&#8217; to call or email any time about any issue at all. We are prepared to add any supplementary promotion you need if an ad is not working well.</em></p>

				<p><strong>9. Your money</strong> supports our unique content model of distributing material direct from NGO&#8217;s to a wider audience. Many NGOs rely on Green Pages is their greatest means of letting people know about the important problems they are solving. <em>The GP difference: Your money is going to support Green Pages, an independent technology company that provides the world&#8217;s only aggregate of all NGO news. We bring together the news, campaigns and projects that are saving the planet and put them in touch with thousands of people every day. The more advertisers we have, the better equipped we are to improve and built this service. </em> </p>

				<p><strong>10. Greenpag.es</strong> has a loyal following of over 20,000 members and between 1,000 and 5,000 visitors reading every day. Green Pages is well known in the environmental community as a place to find environmental products and services across 400 industry sectors. <em>The GP difference: Your advertising hits the spot. The Green Pages readership is the core of the professional environmental community. There is no other membership and mailing available that has such direct access to green, environment and sustainability professionals.</em></p>

			</div>

			<br /><br /><br />

		</div>

