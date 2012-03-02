$(document).ready(function() { 
    $('.favourite-profile').on('click', 'a', clickStar);
    
    function clickStar(event) {
    	event.preventDefault();

        var id = $(this).parent().attr('id');
        var action;
        
        $(this).children('.star-mini').removeClass('hover');
        
		if ($(this).children('.star-mini').hasClass('favorited')) {
			action = 'remove';
		} else {
			action = 'add';
		}
		
		var parent = $(this);
		// note: url '/like-this' is a wordpress page using template 'like' which is /like.php
		$.ajax({
			type: "POST",
			url: "/like-this",
			data: 'id='+id+'&action='+action+'&what=likepost',
			cache: false,
			success: function(html) {
				if (html != '0') {
					parent.children('.star-mini').toggleClass('favorited');
					
					var newcount = parent.children('.star-mini-number').text();
					
					if (parent.children('.star-mini').hasClass('favorited')) {
						parent.children('.star-mini-number').text(parseInt(newcount) + 1);
						parent.children('.star-mini-number-plus-one').hide();
						parent.children('.star-mini-number').show();
					}
					if (!parent.children('.star-mini').hasClass('favorited')) {
						if (parseInt(newcount) > 0) {
							parent.children('.star-mini-number').text(parseInt(newcount) - 1);
							parent.children('.star-mini-number-minus-one').hide();
							parent.children('.star-mini-number').show();
				    	}
					}
				}
			}
		});	
    }

    $('.favourite-profile > a').each(eachStar);
    
    function eachStar() {
    	$(this).hover(
			function(){
				$(this).children('.star-mini-number').hide();
				
				if ($(this).children('.star-login').length) {
					$(this).children('.star-mini').hide();
					$(this).children('.star-login').show();
					return;
				}
				
				if (!$(this).children('.star-mini').hasClass('favorited')) {
					$(this).children('.star-mini').addClass('hover');
					if (parseInt($(this).children('.star-mini-number').text()) == 0) {
						$(this).children('.star-mini-number-plus-one').fadeIn('slow');
					} else {
						$(this).children('.star-mini-number-plus-one').show();
					}
				}
				if ($(this).children('.star-mini').hasClass('favorited')) {
					$(this).children('.star-mini-number-minus-one').show();
				}
			},
			function(){
				if ($(this).children('.star-login').length) {
					$(this).children('.star-login').hide();
					$(this).children('.star-mini').show();
					if (parseInt($(this).children('.star-mini-number').text()) > 0) {
						$(this).children('.star-mini-number').show();
					} else {
						$(this).children('.star-mini-number').hide();
					}
					return;
				}
				
				$(this).children('.star-mini-number-plus-one').hide();
				$(this).children('.star-mini-number-minus-one').hide();
				
				if (!$(this).children('.star-mini').hasClass('favorited')) {
					$(this).children('.star-mini').removeClass('hover');
				}
				
				if (parseInt($(this).children('.star-mini-number').text()) > 0) {
					$(this).children('.star-mini-number').show();
				}
				if (parseInt($(this).children('.star-mini-number').text()) == 0) {
					$(this).children('.star-mini-number').hide();
				}
			}
		);
    }
});

$(document).ready(function() { 
    $('.follow_me').click(function() {
        var id = $(this).parent('div').attr('id');
        var action;
        
        $(this).removeClass('hover');
        
		if ($(this).hasClass('followed')) {
			action = 'add';
		} else {
			action = 'remove';
		}
		
		var parent = $(this);
		// note: url '/follow-this' is a wordpress page using template 'follow' which is /follow.php
		$.ajax({
			type: "POST",
			url: "/follow-this",
			data: 'id='+id+'&action='+action+'&what=user',
			cache: false,
			success: function(html) {
				if (html != '0') {
					parent.toggleClass('followed');
				}
			}
		});
		
    });

	$('.follow_me').hover(
		function(){
			if (!$(this).hasClass('followed')) {
				$(this).addClass('hover');
			}
		},
		function(){
			if (!$(this).hasClass('followed')) {
				$(this).removeClass('hover');
			}                       
		}
	);
});


$(document).ready(function() {
	$(".topic-select").click(function (e) {
		e.preventDefault();
		$(this).parent('.profile-postbox').next('.topic-container').children('.topic-content').slideToggle("slow");
	});	
});

$(document).ready(function() {
        $(".topic-bookmark > a").hover(
		function () {
        	$(this).next('.topic-bookmark-options').show();
        },
		function () {
			$(this).next('.topic-bookmark-options').hide();
		}
	);
});


$(document).ready(function() {
	
	$(".profile-tabs a").click(function(e){
		e.preventDefault;
	
		/*var hashURL = location.href.slice(location.href.indexOf('#'));
		var hashparts = hashURL.split(';');
		var hashvalues = [];
		
		for (var i = 0; i < hashparts.length; i++) {
			if (i == 0) {hashparts[i] = hashparts[i].substring(1);}
    		hashnext = hashparts[i].split(':');
    		hashvalues[hashnext[0]] = hashnext[1];
		}*/
		
		$(".profile-tabs a").removeClass('profile-tab-active');
		$(this).addClass('profile-tab-active');
	});
	
	$(window).hashchange(function(){
		var baseURL = location.href.slice(0, location.href.indexOf('#'));
		if(baseURL.charAt(baseURL.length-1) == "/") {baseURL = baseURL.slice(0, baseURL.length-1);}
		
		var profileURL = baseURL.slice(baseURL.lastIndexOf('/')).slice(1);
		
		var hashURL = location.href.slice(location.href.indexOf('#'));
		var hashparts = hashURL.split(';');
		var hashvalues = [];

		for (var i = 0; i < hashparts.length; i++) {
			if (i == 0) {hashparts[i] = hashparts[i].substring(1);}
    		hashnext = hashparts[i].split(':');
    		hashvalues[hashnext[0]] = hashnext[1];
		}
	
		if (hashvalues['tab'] == undefined) {tab = 'posts';} else {tab = hashvalues['tab'];}
		if (hashvalues['post'] == undefined) {post = 'all';} else {post = hashvalues['post'];}
		if (hashvalues['page'] == undefined || !(/^ *[0-9]+ *$/.test(hashvalues['page']))) {page = 1;} else {page = hashvalues['page'];}
		
		if ( tab == 'posts' ) {$('.profile-tab-posts').show();} else {$('.profile-tab-posts').hide();}
		if ( tab == 'favourites' ) {$('.profile-tab-favourites').show();} else {$('.profile-tab-favourites').hide();}
		
		$(".profile-timeout.top").fadeOut('slow');
		$(".profile-loading.top").fadeIn('slow');
		if ( hashvalues['page'] ) {
			$(".profile-timeout.bottom").fadeOut('slow');
			$(".profile-loading.bottom").fadeIn('slow');
		}

		$.ajax({
			type: "POST",
			url: "/get-profile-data",
			data: 'pid='+profileURL+'&tab='+tab+'&post='+post+'&page='+page,
			timeout: 100000,
			cache: false,
			success: function(data) {
				$(".profile-loading").fadeOut('slow');
				$(".profile-container").html(data);
				$('.profile-action-container').removeClass('no-js');
				if ( hashvalues['page'] ) {
					$(jQuery.browser.webkit ? "body": "html").animate({ scrollTop: $(".profile-tabs").offset().top }, 'slow');
				}
			},
			error: function(x, t, m) {
        		if(t==="timeout") {
        			$(".profile-loading").fadeOut('slow');
        			$(".profile-timeout").fadeIn('slow');
            		if ( hashvalues['page'] ) {
						$(jQuery.browser.webkit ? "body": "html").animate({ scrollTop: $(".profile-tabs").offset().top }, 'slow');
					}
        		}
    		}
		});
	});
	
	$(window).hashchange();
});


$(document).ready(function() {
	$('.profile-action-container').removeClass('no-js');
	$('.profile-action-items').hide();
	
	$('html').on('click', hideIt);
 	$('html').click(hideIt);

	$('.profile-action').click(closeIt);
	$('.profile-container').on('click', '.profile-action', closeIt);
    
    function hideIt(event) {
    	$('.profile-action-items').hide();
    }
    
    function closeIt(event) {
    	event.preventDefault();
		event.stopPropagation();
    
		if($(this).next('.profile-action-items').css("display") == "none"){
			$(this).next('.profile-action-items').show();		
		}else{
			$(this).next('.profile-action-items').hide();		
		}
	}
});