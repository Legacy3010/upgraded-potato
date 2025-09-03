// WP-YouFlix Netflix Interactivity
jQuery(document).ready(function($) {
    function updateCarouselButtons(carousel) {
        var scrollLeft = carousel.scrollLeft();
        var scrollWidth = carousel.get(0).scrollWidth;
        var width = carousel.width();
        var prevButton = carousel.siblings('.wp-youflix-carousel-prev');
        var nextButton = carousel.siblings('.wp-youflix-carousel-next');

        if (scrollLeft === 0) {
            prevButton.hide();
        } else {
            prevButton.show();
        }

        if (scrollLeft + width >= scrollWidth - 10) { // -10 for tolerance
            nextButton.hide();
        } else {
            nextButton.show();
        }
    }

    $('.wp-youflix-video-carousel').each(function() {
        updateCarouselButtons($(this));
    });

    $('.wp-youflix-carousel-next').on('click', function() {
        var carousel = $(this).siblings('.wp-youflix-video-carousel');
        var scrollAmount = carousel.width() * 0.8;
        carousel.animate({
            scrollLeft: '+=' + scrollAmount
        }, 300, function() {
            updateCarouselButtons(carousel);
        });
    });

    $('.wp-youflix-carousel-prev').on('click', function() {
        var carousel = $(this).siblings('.wp-youflix-video-carousel');
        var scrollAmount = carousel.width() * 0.8;
        carousel.animate({
            scrollLeft: '-=' + scrollAmount
        }, 300, function() {
            updateCarouselButtons(carousel);
        });
    });

    $(window).on('resize', function() {
        $('.wp-youflix-video-carousel').each(function() {
            updateCarouselButtons($(this));
        });
    }).resize();
});
