/**
 * Created by arnoldas on 16.12.7.
 */
// title font size changing script
jQuery(document).ready(function() {
    var viewportWidth = $(window).width();
    if (viewportWidth < 800) {
        $(".title").removeClass("titleL").addClass("titleM");
    }else{
        $('.title').removeClass('titleM').addClass('titleL');
    }
    $(window).resize(function () {
        var viewportWidth = $(window).width();
        if (viewportWidth < 800) {
            $(".title").removeClass("titleL").addClass("titleM");
        }else{
            $('.title').removeClass('titleM').addClass('titleL');
        }
    });


});
