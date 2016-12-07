/**
 * Created by arnoldas on 16.12.7.
 */
// title font size changing script
jQuery(document).ready(function() {
    var viewportWidth = $(window).width();
    console.log(viewportWidth);
    if (viewportWidth < 1188) {
        $('.border').removeClass('borderLeft').addClass('borderTop');
        $('.social').css("margin",0);
    }else{
        $('.border').removeClass('borderTop').addClass('borderLeft');
        $('.social').css("margin","12px");

    }
    $(window).resize(function () {

        var viewportWidth = $(window).width();
        console.log(viewportWidth);
        if (viewportWidth < 1188) {
            $('.border').removeClass('borderLeft').addClass('borderTop');
            $('.social').css("margin",0);
        }else{
            $('.border').removeClass('borderTop').addClass('borderLeft');
            $('.social').css("margin","12px");

        }
    });


});
