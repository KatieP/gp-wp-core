$(document).ready(function() { 
    $('.like_heart').click(function() {
        var id = $(this).parent('div').attr('id');
        var action;
        
        $(this).removeClass('hover');
        
		if ($(this).hasClass('favorited')) {
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
					parent.toggleClass('favorited');
				}
			}
		});
		
    });

	$('.like_heart').hover(
		function(){
			if (!$(this).hasClass('favorited')) {
				$(this).addClass('hover');
			}
		},
		function(){
			if (!$(this).hasClass('favorited')) {
				$(this).removeClass('hover');
			}                       
		}
	);
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