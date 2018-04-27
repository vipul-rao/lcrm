/*this is header js*/
$(document).ready(function() {
    $(".menuicon").click(function () {
        $(".navbar-nav").removeClass("hide").show(3000);

    });

    $(".menuicon").click(function () {
        $('.menuicon').addClass("hide");
        $('.menuicon1').addClass("hide");
    });
    $(".close1").click(function () {
        $(".navbar-nav").addClass("hide");
        $(".menuicon").removeClass("hide");
        $(".menuicon1").removeClass("hide");
        // location.reload();

    });
    $(".menuicon").click(function () {
$(".submenu,.menu_toggle_icon1").addClass("menu_hide");

    });
    $(".close1").click(function () {
        $(".submenu,.menu_toggle_icon1").removeClass("menu_hide");

    });
    $('.navbar-toggler').click(function () {

        $(".listinline").toggleClass("menu_hide");
        $(".navbar-nav").toggleClass('hide');
    });


    });






