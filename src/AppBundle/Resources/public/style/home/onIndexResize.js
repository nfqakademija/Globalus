/**
 * Created by arnoldas on 16.12.7.
 */
// title font size changing script
jQuery(document).ready(function() {
    var viewportWidth = $(window).width();
    if (viewportWidth < 600) {
        $(".title").removeClass("titleH").addClass("titleL");
    }else{
        $('.title').removeClass('titleL').addClass('titleH');
    }
    $(window).resize(function () {
        var viewportWidth = $(window).width();
        if (viewportWidth < 600) {
            $(".title").removeClass("titleH").addClass("titleL");
        }else{
            $('.title').removeClass('titleL').addClass('titleH');
        }
    });


});
