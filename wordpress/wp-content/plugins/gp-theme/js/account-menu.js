$("#auth-tools").ready(function() {
        $("#auth-tools > li").removeClass('no-js');

        $("#auth-tools > li > a").click(function(e) {
                e.preventDefault();

                $("#auth-tools li ul").not($(this).next("ul")).hide();
                $("#auth-tools li a").not($(this)).removeClass("selected");
                $(this).next("ul").toggle();

                if($(this).next("ul").css("display") == "none"){
                        $(this).removeClass("selected");
                }else{
                        $(this).addClass("selected");
                }
        });

        $(document).bind('click', function(e) {
                if ($(e.target).parents("#auth-tools").length == 0){
                        $("#auth-tools li ul").hide();
                        $("#auth-tools li a").removeClass("selected");
                }

        });
});
