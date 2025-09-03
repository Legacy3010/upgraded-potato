// WP-YouFlix Netflix Interactivity
jQuery(document).ready(function($) {
    // Carousel Logic
    function updateCarouselButtons(carousel) {
        var scrollLeft = carousel.scrollLeft();
        var scrollWidth = carousel.get(0).scrollWidth;
        var width = carousel.width();
        var prevButton = carousel.siblings('.wp-youflix-carousel-prev');
        var nextButton = carousel.siblings('.wp-youflix-carousel-next');

        if (scrollLeft < 1) {
            prevButton.hide();
        } else {
            prevButton.show();
        }

        if (scrollLeft + width >= scrollWidth - 1) {
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

    // Lightbox Modal Logic
    var modal = $('#wp-youflix-modal');
    var videoFrame = modal.find('iframe');
    var closeModal = $('.wp-youflix-modal-close');

    $('.wp-youflix-video-thumb').on('click', function(e) {
        e.preventDefault();
        var videoId = $(this).data('video-id');
        var embedUrl = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';

        videoFrame.attr('src', embedUrl);
        $('body').addClass('wp-youflix-modal-open');
        modal.show();
    });

    function closePlayer() {
        videoFrame.attr('src', '');
        $('body').removeClass('wp-youflix-modal-open');
        modal.hide();
    }

    closeModal.on('click', function() {
        closePlayer();
    });

    modal.on('click', function(e) {
        if ($(e.target).is(modal)) {
            closePlayer();
        }
    });

    $(document).on('keydown', function(e) {
        if (e.key === "Escape" && modal.is(':visible')) {
            closePlayer();
        }
    });
});
