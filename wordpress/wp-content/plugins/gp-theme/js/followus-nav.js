/*

Main Javascript for jQuery Realistic Hover Effect
Created by Adrian Pelletier
http://www.adrianpelletier.com

*/

/* =Realistic Navigation
============================================================================== */

	// Begin jQuery
	
	$(document).ready(function() {

	/* =Reflection Nav
	-------------------------------------------------------------------------- */	
		
		// Append span to each LI to add reflection
		
		$("#nav-reflection li").append("<span></span>");	
		
		// Animate buttons, move reflection and fade
		
		$("#nav-reflection a").hover(function() {
		    $(this).stop().animate({ marginTop: "-10px" }, 200);
		    $(this).parent().find("span").stop().animate({ marginTop: "18px", opacity: 0.25 }, 200);
		},function(){
		    $(this).stop().animate({ marginTop: "0px" }, 300);
		    $(this).parent().find("span").stop().animate({ marginTop: "1px", opacity: 1 }, 300);
		});
				
	/* =Shadow Nav
	-------------------------------------------------------------------------- */
	
		// Append shadow image to each LI
		
		// $("#nav-shadow li").append('<img class="shadow" src="wp-content/themes/greenpages/template/icons-shadow.jpg" width="29" height="10" />');
	
		// Animate buttons, shrink and fade shadow
		
		$("#nav-shadow li").hover(function() {
			var e = this;
		    $(e).find("a").stop().animate({ marginTop: "-4px" }, 250, function() {
		    	$(e).find("a").animate({ marginTop: "-3px" }, 250);
		    });
		    $(e).find("img.shadow").stop().animate({ width: "25px", height: "7px", marginleft: "4px", opacity: 0.25 }, 250);
		},function(){
			var e = this;
		    $(e).find("a").stop().animate({ marginTop: "-4px" }, 250, function() {
		    	$(e).find("a").animate({ marginTop: "0px" }, 250);
		    });
		    $(e).find("img.shadow").stop().animate({ width: "29px", height: "10px", marginLeft: "0px", opacity: 0.35 }, 250);
		});
						
	// End jQuery
	
	});