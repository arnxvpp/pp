/**
 * Navigation Dropdown Fix
 * Fixes the Research dropdown and all parent menu items functionality
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        var isMobile = function() {
            return window.matchMedia("(max-width: 768px)").matches || $(window).width() <= 768;
        };

        var closeAllSubmenus = function($except) {
            $('.global-nav ul ul').not($except).removeClass('select fadeInUp').addClass('fadeOutUp');
            setTimeout(function() {
                $('.global-nav ul ul').not($except).css({
                    'display': 'none',
                    'opacity': '0',
                    'visibility': 'hidden'
                }).removeClass('fadeOutUp');
            }, 400);
            $('.global-nav a').not($except.siblings('a')).removeClass('active');
        };

        var openSubmenu = function($submenu, $link) {
            $submenu.removeClass('fadeOutUp').addClass('select fadeInUp').css({
                'display': 'block',
                'opacity': '1',
                'visibility': 'visible'
            });
            $link.addClass('active');
        };

        var closeSubmenu = function($submenu, $link) {
            $submenu.removeClass('select fadeInUp').addClass('fadeOutUp');
            setTimeout(function() {
                $submenu.css({
                    'display': 'none',
                    'opacity': '0',
                    'visibility': 'hidden'
                }).removeClass('fadeOutUp');
            }, 400);
            $link.removeClass('active');
        };

        $('body').off('click.navfix').on('click.navfix', '.global-nav > ul > li > a[href="javascript:void(0);"]', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $link = $(this);
            var $submenu = $link.siblings('ul');

            if($submenu.length > 0) {
                if($submenu.hasClass('select')) {
                    closeSubmenu($submenu, $link);
                } else {
                    closeAllSubmenus($submenu);
                    openSubmenu($submenu, $link);
                }
            }
        });

        $('body').off('click.navfix-nested').on('click.navfix-nested', '.global-nav ul ul > li > a[href="javascript:void(0);"]', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $link = $(this);
            var $submenu = $link.siblings('ul');

            if($submenu.length > 0) {
                $link.closest('ul').find('ul').not($submenu).removeClass('select fadeInUp').addClass('fadeOutUp');
                $link.closest('ul').find('a').not($link).removeClass('active');

                if($submenu.hasClass('select')) {
                    closeSubmenu($submenu, $link);
                } else {
                    openSubmenu($submenu, $link);
                }
            }
        });

        $(document).on('click.navfix-close', function(e) {
            if(!$(e.target).closest('.global-nav').length && !$(e.target).closest('.nav-trigger').length) {
                closeAllSubmenus($());
            }
        });

        console.log('Navigation dropdown fix loaded successfully');
    });

})(jQuery);
