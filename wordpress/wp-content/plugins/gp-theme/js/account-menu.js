
				//$(".auth-youraccount-start").removeClass("menu-open");

		    // $(".auth-youraccount-start").toggleClass("menu-open");

(function($) {
    $.fn.renderDash = function(openDash, fn) {
        var container = $(this);
        container.removeClass('no-js');
        $(openDash).hide();

        container.bind('click', function(event) {
            event.preventDefault();
            clickStart();
        });

        $(document.body).bind('mouseup', function(event) {
            $(openDash).hide();

        });

        function clickStart() {
            $(openDash).toggle().bind('mouseup', function(event) {
                event.stopPropagation();
            });
        }
    };
})(jQuery);