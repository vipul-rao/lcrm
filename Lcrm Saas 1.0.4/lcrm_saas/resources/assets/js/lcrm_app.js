"use strict"
$(function() {
    //hide menu in small screens
    if ($(window).width() <= 992) {
        $(".wrapper").addClass("hide_menu")
    }
    //Enable sidebar toggle
    $("[data-toggle='offcanvas'].sidebar-toggle").on('click', function (e) {
        e.preventDefault()
        e.stopPropagation()
        //Toggle Menu
        $(".wrapper").toggleClass("hide_menu")
        $("body").toggleClass('sidebar_left_opened')
    });
    // leftmenu init
    (function () {
        var animationSpeed = 300,
            subMenuSelector = '.sub_menu'
        $(".menu-dropdown:not('.active')").find(subMenuSelector).slideUp("fast")
        $(".menu-dropdown.active").find("li:not('.active')").find(".sub-submenu").slideUp("fast")
        $('.navigation li a').on('click', function(e) {
            var $this = $(this)
            var checkElement = $this.next()
            if (checkElement.is(subMenuSelector) && checkElement.is(':visible')) {
                checkElement.slideUp(animationSpeed, function() {
                    checkElement.removeClass('active')
                })
                checkElement.parent("li").removeClass("active")
            } else if ((checkElement.is(subMenuSelector)) && (!checkElement.is(':visible'))) {
                var parent = $this.parents('ul').first()
                var ul = parent.find('ul:visible').slideUp(animationSpeed)
                ul.removeClass('active')
                var parent_li = $this.parent("li")
                checkElement.slideDown(animationSpeed).addClass('active')
                parent.find('li.active').removeClass('active')
                parent_li.addClass('active')
            }
            if (checkElement.is(subMenuSelector)) {
                e.preventDefault()
            }
        })
    })()

    // INIT popovers
    $("[data-toggle='popover']").popover()
})

//======================= Card js ========================
$(document).on('click', '.card-header .clickable', function(e){
    var $this = $(this)
    if(!$this.hasClass('card-collapsed')) {
        $this.parents('.card').find('.card-body').slideUp()
        $this.addClass('card-collapsed')
        $this.closest('i').removeClass('fa-chevron-up').addClass('fa-chevron-down')
    } else {
        $this.parents('.card').find('.card-body').slideDown()
        $this.removeClass('card-collapsed')
        $this.closest('i').removeClass('fa-chevron-down').addClass('fa-chevron-up')
    }
})
$(document).on('click', '.card-header .removecard', function(e){
    $(this).parents('.card').hide("slow")
})
/*END*/
