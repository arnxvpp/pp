<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Analytics {

    public static function init() {
        add_action('wp_head', array(__CLASS__, 'output_gtag_code'), 1);
        add_action('wp_footer', array(__CLASS__, 'output_event_tracking_code'), 100);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_analytics'));
    }

    public static function output_gtag_code() {
        $ga4_id = get_option('pptm_ga4_measurement_id', '');

        if (empty($ga4_id)) {
            return;
        }

        if (current_user_can('manage_options') && get_option('pptm_analytics_exclude_admin', 'yes') === 'yes') {
            return;
        }

        ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo esc_js($ga4_id); ?>', {
                'send_page_view': true,
                'anonymize_ip': <?php echo get_option('pptm_analytics_anonymize_ip', 'yes') === 'yes' ? 'true' : 'false'; ?>,
                <?php if (is_singular()): ?>
                'page_title': '<?php echo esc_js(get_the_title()); ?>',
                'page_location': '<?php echo esc_js(get_permalink()); ?>',
                <?php if (get_post_type() === 'talent' || strpos(get_post_type(), 'article_') === 0): ?>
                'content_type': '<?php echo esc_js(get_post_type()); ?>',
                'content_id': '<?php echo esc_js(get_the_ID()); ?>',
                <?php endif; ?>
                <?php endif; ?>
            });

            <?php if (is_singular()): ?>
            gtag('event', 'page_view', {
                'page_title': '<?php echo esc_js(get_the_title()); ?>',
                'page_location': '<?php echo esc_js(get_permalink()); ?>',
                'content_type': '<?php echo esc_js(get_post_type()); ?>'
            });
            <?php endif; ?>
        </script>
        <?php
    }

    public static function output_event_tracking_code() {
        $ga4_id = get_option('pptm_ga4_measurement_id', '');

        if (empty($ga4_id)) {
            return;
        }

        if (current_user_can('manage_options') && get_option('pptm_analytics_exclude_admin', 'yes') === 'yes') {
            return;
        }

        ?>
        <script>
        (function() {
            if (typeof gtag === 'undefined') {
                return;
            }

            document.addEventListener('click', function(e) {
                var target = e.target;
                while (target && target.tagName !== 'A') {
                    target = target.parentElement;
                }

                if (!target) {
                    return;
                }

                var href = target.getAttribute('href');
                if (!href) {
                    return;
                }

                var isExternal = target.hostname && target.hostname !== window.location.hostname;

                if (isExternal) {
                    gtag('event', 'click', {
                        'event_category': 'outbound',
                        'event_label': href,
                        'transport_type': 'beacon'
                    });
                }

                var fileExtensions = /\.(pdf|doc|docx|xls|xlsx|zip|rar|mp3|mp4|avi)$/i;
                if (fileExtensions.test(href)) {
                    gtag('event', 'file_download', {
                        'file_extension': href.split('.').pop().toLowerCase(),
                        'file_name': href.split('/').pop(),
                        'link_url': href
                    });
                }

                if (target.classList.contains('pptm-talent-card') || target.closest('.pptm-talent-card')) {
                    gtag('event', 'select_content', {
                        'content_type': 'talent_profile',
                        'content_id': target.dataset.talentId || ''
                    });
                }
            });

            var scrollDepths = [25, 50, 75, 90];
            var scrollFired = {};

            window.addEventListener('scroll', function() {
                var scrollPercent = (window.scrollY + window.innerHeight) / document.documentElement.scrollHeight * 100;

                scrollDepths.forEach(function(depth) {
                    if (scrollPercent >= depth && !scrollFired[depth]) {
                        scrollFired[depth] = true;
                        gtag('event', 'scroll', {
                            'event_category': 'engagement',
                            'event_label': depth + '%',
                            'value': depth
                        });
                    }
                });
            });

            var videoElements = document.querySelectorAll('video');
            videoElements.forEach(function(video) {
                video.addEventListener('play', function() {
                    gtag('event', 'video_start', {
                        'video_url': video.currentSrc || video.src,
                        'video_title': video.title || 'Untitled'
                    });
                });

                video.addEventListener('ended', function() {
                    gtag('event', 'video_complete', {
                        'video_url': video.currentSrc || video.src,
                        'video_title': video.title || 'Untitled'
                    });
                });
            });

            var forms = document.querySelectorAll('form');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    var formId = form.id || form.className || 'unknown';
                    gtag('event', 'form_submit', {
                        'form_id': formId,
                        'form_name': form.name || formId
                    });
                });
            });

            var startTime = new Date().getTime();

            window.addEventListener('beforeunload', function() {
                var timeSpent = Math.round((new Date().getTime() - startTime) / 1000);

                if (timeSpent > 10) {
                    gtag('event', 'timing_complete', {
                        'name': 'page_read_time',
                        'value': timeSpent,
                        'event_category': 'engagement'
                    });
                }
            });

            <?php if (is_singular() && (get_post_type() === 'talent' || strpos(get_post_type(), 'article_') === 0)): ?>
            gtag('event', 'view_item', {
                'items': [{
                    'item_id': '<?php echo esc_js(get_the_ID()); ?>',
                    'item_name': '<?php echo esc_js(get_the_title()); ?>',
                    'item_category': '<?php echo esc_js(get_post_type()); ?>'
                }]
            });
            <?php endif; ?>

        })();
        </script>
        <?php
    }

    public static function enqueue_admin_analytics() {
        $screen = get_current_screen();

        if ($screen && $screen->id === 'dashboard') {
            wp_enqueue_style('pptm-analytics-admin', PPTM_PLUGIN_URL . 'assets/css/analytics-admin.css', array(), PPTM_VERSION);
        }
    }

    public static function get_post_analytics($post_id) {
        $views = get_post_meta($post_id, '_pptm_views', true);
        $shares = get_post_meta($post_id, '_pptm_share_counts', true);

        $total_shares = 0;
        if (is_array($shares)) {
            $total_shares = isset($shares['total']) ? $shares['total'] : array_sum(array_filter($shares, 'is_numeric'));
        }

        return array(
            'views' => $views ? intval($views) : 0,
            'shares' => $total_shares,
            'shares_by_network' => $shares,
        );
    }

    public static function get_popular_content($limit = 10, $days = 30) {
        $args = array(
            'post_type' => array('article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'talent', 'post'),
            'posts_per_page' => $limit,
            'meta_key' => '_pptm_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'date_query' => array(
                array(
                    'after' => $days . ' days ago',
                ),
            ),
        );

        $posts = get_posts($args);
        $popular = array();

        foreach ($posts as $post) {
            $analytics = self::get_post_analytics($post->ID);
            $popular[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'type' => $post->post_type,
                'url' => get_permalink($post->ID),
                'views' => $analytics['views'],
                'shares' => $analytics['shares'],
            );
        }

        return $popular;
    }

    public static function get_trending_talents($limit = 5) {
        $args = array(
            'post_type' => 'talent',
            'posts_per_page' => $limit,
            'meta_key' => '_pptm_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'date_query' => array(
                array(
                    'after' => '7 days ago',
                ),
            ),
        );

        return get_posts($args);
    }
}
