jQuery(document).ready(function($) {
    var $navTrigger = $('#nav-trigger');
    var $navOverlay = $('.nav-overlay');

    $navTrigger.on('click', function(e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $navOverlay.toggleClass('active');
        $('body').toggleClass('nav-open');
    });

    $('.nav-overlay a').on('click', function() {
        if ($(this).attr('href') !== 'javascript:void(0);' && $(this).attr('href') !== 'javascript:;') {
            $navTrigger.removeClass('active');
            $navOverlay.removeClass('active');
            $('body').removeClass('nav-open');
        }
    });

    $(document).on('keyup', function(e) {
        if (e.key === 'Escape' && $navOverlay.hasClass('active')) {
            $navTrigger.removeClass('active');
            $navOverlay.removeClass('active');
            $('body').removeClass('nav-open');
        }
    });

    setTimeout(function() {
        $('.animation-intro').fadeOut(500);
    }, 3000);
});
