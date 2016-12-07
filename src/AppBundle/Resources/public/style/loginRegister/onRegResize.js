/**
 * Created by arnoldas on 16.12.7.
 */
// title font size changing script
jQuery(document).ready(function() {
    var viewportWidth = $(window).width();
    console.log(viewportWidth);
    if (viewportWidth < 1188) {
        $('.border').removeClass('borderLeft').addClass('borderTop');
        $('.login').css("margin",0).css("text-align","center").css("margin-bottom","10px");
    }else{
        $('.border').removeClass('borderTop').addClass('borderLeft');
        $('.login').css("margin-bottom","0px").css("margin","130px 60px").css("text-align","none");
    }
    $(window).resize(function () {

        var viewportWidth = $(window).width();
        console.log(viewportWidth);
        if (viewportWidth < 1188) {
            $('.border').removeClass('borderLeft').addClass('borderTop');
            $('.login').css("margin",0).css("text-align","center").css("margin-bottom","10px");
        }else{
            $('.border').removeClass('borderTop').addClass('borderLeft');
            $('.login').css("margin-bottom","0px").css("margin","130px 60px").css("text-align","none");
        }
    });


});
