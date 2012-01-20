$(document).ready(function() {
    var $sharebar = $("#gp_sharebar"),
    $window = $(window),
    offset = $sharebar.offset(),
    topPadding = 20;
 
    $window.scroll(function() {
        if ($window.scrollTop() > (offset.top-topPadding)) {
            $sharebar.addClass("fixed");
        } else {
            $sharebar.removeClass("fixed");
        }
    });
});
