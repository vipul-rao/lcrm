/*about studio js code*/

$(".brandtext,.about_logo").hover(
    function () {
        $(".brand img").removeClass('about_logo');
        $(".brand h6").css("color","#8799A2");



    });

$(".brandtext,.about_logo").mouseout(function () {
    $(".brand img").addClass("about_logo");
    $(".brand h6").css("color","black");

});
$(".webtext,.about_logo1").hover(
    function () {
        $(".web img").removeClass('about_logo1');
        $(".web h6").css("color","#8799a3");
    });
$(".webtext,.about_logo1").mouseout(function () {
    $(".web img").addClass("about_logo1");
    $(".web h6").css("color","black");
});
$(".developementtext,.about_logo2").hover(
    function () {
        $(".developement img").removeClass('about_logo2');
        $(".developement h6").css("color","#8799a3");
    });
$(".developementtext,.about_logo2").mouseout(function () {
    $(".developement img").addClass("about_logo2");
    $(".developement h6").css("color","black");
});

$('#sponser3a').hover(
    function(){
        $(this).attr('src','front/images/left3.png');

    },
    function() {
        $(this).attr('src', 'front/images/back.png');

    });

$('#sponser4a').hover(
    function(){
        $(this).attr('src','front/images/right3.png');
    },
    function() {
        $(this).attr('src', 'front/images/right2.png');

    });