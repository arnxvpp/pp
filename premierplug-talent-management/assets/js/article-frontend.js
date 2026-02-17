/**
 * Article Management System - Frontend JavaScript
 *
 * Handles tab switching, AJAX loading, and interactivity
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

(function($) {
    'use strict';

    var PPTM_Articles = {
        init: function() {
            this.bindEvents();
        },

        bindEvents: function() {
            $(document).on('click', '.pptm-tab-btn', this.handleTabClick);
            $(document).ready(function() {
                PPTM_Articles.initSmoothScroll();
            });
        },

        handleTabClick: function(e) {
            e.preventDefault();

            var $button = $(this);
            var articleType = $button.data('type');
            var talentId = $button.closest('.pptm-article-tabs').data('talent-id');

            if ($button.hasClass('active')) {
                return;
            }

            $button.closest('.pptm-article-tabs').find('.pptm-tab-btn').removeClass('active');
            $button.addClass('active');

            PPTM_Articles.loadArticles(talentId, articleType);
        },

        loadArticles: function(talentId, articleType) {
            var $grid = $('#pptm-talent-articles-grid');
            var $loading = $('.pptm-loading');

            $grid.css('opacity', '0.5');
            $loading.show();

            $.ajax({
                url: (typeof pptm_ajax !== 'undefined' && pptm_ajax.ajax_url) ? pptm_ajax.ajax_url : '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'pptm_load_talent_articles',
                    talent_id: talentId,
                    article_type: articleType === 'all' ? '' : articleType
                },
                success: function(response) {
                    if (response.success) {
                        $grid.html(response.data.html);
                        PPTM_Articles.animateIn($grid);
                    } else {
                        $grid.html('<p class="pptm-no-articles">Failed to load articles.</p>');
                    }
                    $loading.hide();
                },
                error: function() {
                    $grid.html('<p class="pptm-no-articles">Error loading articles. Please try again.</p>');
                    $grid.css('opacity', '1');
                    $loading.hide();
                }
            });
        },

        animateIn: function($element) {
            $element.css('opacity', '0');

            setTimeout(function() {
                $element.css({
                    'opacity': '1',
                    'transition': 'opacity 0.4s ease-in-out'
                });
            }, 100);
        },

        initSmoothScroll: function() {
            $('a[href^="#"]').on('click', function(e) {
                var href = this.getAttribute('href');
                if (!href || href === '#') {
                    return;
                }
                var target = $(href);

                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 600);
                }
            });
        }
    };

    PPTM_Articles.init();

})(jQuery);
