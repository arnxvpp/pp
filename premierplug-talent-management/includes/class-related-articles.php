<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Related_Articles {

    public static function init() {
        add_filter('the_content', array(__CLASS__, 'add_related_articles'), 40);
        add_shortcode('pptm_related', array(__CLASS__, 'related_articles_shortcode'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_related_styles'));
    }

    public static function enqueue_related_styles() {
        $custom_css = "
        .pptm-related-articles {
            margin: 40px 0;
            padding: 30px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        .pptm-related-title {
            font-size: 22px;
            font-weight: 700;
            color: #222;
            margin: 0 0 25px 0;
            padding-bottom: 15px;
            border-bottom: 2px solid #ddd;
        }
        .pptm-related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }
        .pptm-related-article {
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            flex-direction: column;
        }
        .pptm-related-article:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }
        .pptm-related-thumbnail {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }
        .pptm-related-content {
            padding: 15px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .pptm-related-article-title {
            font-size: 16px;
            font-weight: 600;
            color: #222;
            margin: 0 0 8px 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pptm-related-article-title a {
            color: inherit;
            text-decoration: none;
        }
        .pptm-related-article-title a:hover {
            color: #0073aa;
        }
        .pptm-related-excerpt {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
            margin: 0 0 10px 0;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .pptm-related-meta {
            font-size: 12px;
            color: #999;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        .pptm-related-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .pptm-related-views {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        @media (max-width: 768px) {
            .pptm-related-articles {
                padding: 20px 15px;
                margin: 30px -15px;
                border-radius: 0;
            }
            .pptm-related-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .pptm-related-thumbnail {
                height: 150px;
            }
        }
        ";
        wp_add_inline_style('pptm-public', $custom_css);
    }

    public static function add_related_articles($content) {
        if (!is_singular()) {
            return $content;
        }

        global $post;
        $post_types = array('article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post');

        if (!in_array($post->post_type, $post_types)) {
            return $content;
        }

        $show_related = get_option('pptm_show_related_articles', 'yes');
        if ($show_related !== 'yes') {
            return $content;
        }

        $related_html = self::render_related_articles($post->ID);

        return $content . $related_html;
    }

    public static function render_related_articles($post_id, $count = null) {
        if (!$count) {
            $count = get_option('pptm_related_articles_count', 3);
        }

        $related_posts = self::get_related_articles($post_id, $count);

        if (empty($related_posts)) {
            return '';
        }

        ob_start();
        ?>
        <div class="pptm-related-articles">
            <h3 class="pptm-related-title"><?php echo esc_html(get_option('pptm_related_articles_title', 'Related Articles')); ?></h3>
            <div class="pptm-related-grid">
                <?php foreach ($related_posts as $related_post): ?>
                    <article class="pptm-related-article">
                        <?php if (has_post_thumbnail($related_post->ID)): ?>
                            <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                <?php echo get_the_post_thumbnail($related_post->ID, 'medium', array('class' => 'pptm-related-thumbnail')); ?>
                            </a>
                        <?php endif; ?>
                        <div class="pptm-related-content">
                            <h4 class="pptm-related-article-title">
                                <a href="<?php echo esc_url(get_permalink($related_post->ID)); ?>">
                                    <?php echo esc_html($related_post->post_title); ?>
                                </a>
                            </h4>
                            <?php if ($related_post->post_excerpt): ?>
                                <div class="pptm-related-excerpt">
                                    <?php echo esc_html(wp_trim_words($related_post->post_excerpt, 15)); ?>
                                </div>
                            <?php endif; ?>
                            <div class="pptm-related-meta">
                                <span class="pptm-related-date">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    <?php echo human_time_diff(get_the_time('U', $related_post->ID), current_time('timestamp')) . ' ago'; ?>
                                </span>
                                <?php
                                $views = get_post_meta($related_post->ID, '_pptm_views', true);
                                if ($views):
                                ?>
                                    <span class="pptm-related-views">
                                        <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                        </svg>
                                        <?php echo number_format($views); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function get_related_articles($post_id, $count = 3) {
        $current_post = get_post($post_id);
        if (!$current_post) {
            return array();
        }

        $related_posts = array();

        $related_posts = array_merge($related_posts, self::get_by_linked_talent($post_id, $count));

        if (count($related_posts) < $count) {
            $needed = $count - count($related_posts);
            $exclude_ids = wp_list_pluck($related_posts, 'ID');
            $exclude_ids[] = $post_id;

            $category_posts = self::get_by_category($current_post->post_type, $exclude_ids, $needed);
            $related_posts = array_merge($related_posts, $category_posts);
        }

        if (count($related_posts) < $count) {
            $needed = $count - count($related_posts);
            $exclude_ids = wp_list_pluck($related_posts, 'ID');
            $exclude_ids[] = $post_id;

            $popular_posts = self::get_popular_posts($current_post->post_type, $exclude_ids, $needed);
            $related_posts = array_merge($related_posts, $popular_posts);
        }

        if (count($related_posts) < $count) {
            $needed = $count - count($related_posts);
            $exclude_ids = wp_list_pluck($related_posts, 'ID');
            $exclude_ids[] = $post_id;

            $recent_posts = self::get_recent_posts($current_post->post_type, $exclude_ids, $needed);
            $related_posts = array_merge($related_posts, $recent_posts);
        }

        return array_slice($related_posts, 0, $count);
    }

    private static function get_by_linked_talent($post_id, $count) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'pptm_article_relationships';

        $talent_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT talent_id FROM {$table_name} WHERE article_id = %d",
            $post_id
        ));

        if (empty($talent_ids)) {
            return array();
        }

        $placeholders = implode(',', array_fill(0, count($talent_ids), '%d'));

        $article_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT article_id FROM {$table_name}
            WHERE talent_id IN ($placeholders)
            AND article_id != %d
            LIMIT %d",
            array_merge($talent_ids, array($post_id, $count))
        ));

        if (empty($article_ids)) {
            return array();
        }

        $posts = get_posts(array(
            'post__in' => $article_ids,
            'post_type' => 'any',
            'posts_per_page' => $count,
            'orderby' => 'date',
            'order' => 'DESC',
        ));

        return $posts;
    }

    private static function get_by_category($post_type, $exclude_ids, $count) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $count,
            'post__not_in' => $exclude_ids,
            'orderby' => 'rand',
        );

        return get_posts($args);
    }

    private static function get_popular_posts($post_type, $exclude_ids, $count) {
        $args = array(
            'post_type' => array('article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post'),
            'posts_per_page' => $count,
            'post__not_in' => $exclude_ids,
            'meta_key' => '_pptm_views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'date_query' => array(
                array(
                    'after' => '30 days ago',
                ),
            ),
        );

        return get_posts($args);
    }

    private static function get_recent_posts($post_type, $exclude_ids, $count) {
        $args = array(
            'post_type' => array('article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post'),
            'posts_per_page' => $count,
            'post__not_in' => $exclude_ids,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        return get_posts($args);
    }

    public static function related_articles_shortcode($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
            'count' => 3,
        ), $atts);

        return self::render_related_articles($atts['post_id'], $atts['count']);
    }

    public static function track_view($post_id) {
        if (!is_singular()) {
            return;
        }

        $views = get_post_meta($post_id, '_pptm_views', true);
        $views = $views ? intval($views) : 0;
        $views++;

        update_post_meta($post_id, '_pptm_views', $views);

        $viewed_today = get_post_meta($post_id, '_pptm_views_today_' . date('Y-m-d'), true);
        $viewed_today = $viewed_today ? intval($viewed_today) : 0;
        $viewed_today++;

        update_post_meta($post_id, '_pptm_views_today_' . date('Y-m-d'), $viewed_today);
    }
}

add_action('wp', function() {
    if (is_singular()) {
        PPTM_Related_Articles::track_view(get_the_ID());
    }
});
