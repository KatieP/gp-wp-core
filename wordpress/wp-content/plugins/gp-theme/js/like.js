$(document).ready(function() { 
    $('.star-mini').parent().click(function() {
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
		
    });

    $('.star-mini').parent().each(function() {
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
	
    });

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


