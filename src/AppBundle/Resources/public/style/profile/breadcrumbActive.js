/**
 * Created by arnoldas on 16.12.7.
 */
var pageNameSplitArr = location.pathname.split('/');
document.onreadystatechange = function () {

    if (document.readyState == "complete") {
        for (var i=0; i<=location.pathname.split('/').length;i++){
            if (pageNameSplitArr[i] == "") {

                $('.nvBtn').removeClass('active');
                $('.nvBtn').first().addClass('active');
            }
            else if (pageNameSplitArr[i] == "changePassword") {
                $('.nvBtn').removeClass('active');
                $('.nvBtn').eq(2).addClass('active');
            }
            else if (pageNameSplitArr[i] == "tests" || pageNameSplitArr[i] == "questions" || pageNameSplitArr[i] == "answers") {
                $('.nvBtn').removeClass('active');
                $('.nvBtn').eq(1).addClass('active');
            }

        }


    }
};
