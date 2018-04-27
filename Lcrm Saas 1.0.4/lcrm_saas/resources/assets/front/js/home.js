/*textillate code*/

// lettering.js code


$(document).ready(function()
{
    $(function () {
        $('.tlt').textillate({
            in:{effect: 'rollIn', delay:100},
            out:{effect: 'fadeOut'},
            loop: true
        });

    });


    $('#container').imagesLoaded().done( function( instance ) {
        console.log('DONE  - all images have been successfully loaded');

        var $grid = $('.grid').isotope({
            itemSelector: '.grid-item',
            percentPosition: true,
            masonry: {
                columnWidth: '.grid-sizer'
            }
        });
        $('.home_filter-menu-group1').on( 'click', 'ul li', function() {
            var filterValue = $(this).attr('data-filter');
            $grid.isotope({ filter: filterValue });
            $(".home_filter-menu-group1 li").removeClass('active');
            $(this).addClass('active');

        });
        $(".sub-menu_icon").on('click',function(){
            $(".home_filter-menu-group1").css("display","none");
            $(".menu_toggle_icon1").css("display","block");
        });
        $('.menu_toggle_icon1').on("click",function(){
            $(".home_filter-menu-group1").css("display","block");
            $(".li-all").addClass("active");
            $(".menu_toggle_icon1").css("display","none");
        });
    });
});
