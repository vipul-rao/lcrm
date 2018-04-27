$(document).ready(function () {
    $('.map')
        .gmap3({
            center: [latitude, longitude],
            zoom: 8
        })
        .marker([{
            position: [latitude, longitude],
            icon: "front/images/location.png"
        }])
        .on('click', function (marker) {
            marker.setIcon('front/images/location.png');
        });
});
