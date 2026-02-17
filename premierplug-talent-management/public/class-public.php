<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Public {

    public static function init() {
        add_action('wp_ajax_pptm_search_talents', array(__CLASS__, 'ajax_search_talents'));
        add_action('wp_ajax_nopriv_pptm_search_talents', array(__CLASS__, 'ajax_search_talents'));
        add_filter('single_template', array(__CLASS__, 'talent_single_template'));
        add_filter('archive_template', array(__CLASS__, 'talent_archive_template'));
        add_filter('single_template', array(__CLASS__, 'article_single_template'));
        add_filter('archive_template', array(__CLASS__, 'article_archive_template'));
    }

    public static function ajax_search_talents() {
        check_ajax_referer('pptm_nonce', 'nonce');

        $category = isset($_POST['category']) ? sanitize_text_field($_POST['category']) : '';
        $skill = isset($_POST['skill']) ? sanitize_text_field($_POST['skill']) : '';
        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        $args = array(
            'post_type' => 'talent',
            'posts_per_page' => 20,
            'post_status' => 'publish',
        );

        if (!empty($search)) {
            $args['s'] = $search;
        }

        $tax_query = array();

        if (!empty($category)) {
            $tax_query[] = array(
                'taxonomy' => 'talent_category',
                'field' => 'slug',
                'terms' => $category,
            );
        }

        if (!empty($skill)) {
            $tax_query[] = array(
                'taxonomy' => 'talent_skill',
                'field' => 'slug',
                'terms' => $skill,
            );
        }

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $query = new WP_Query($args);

        $talents = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $talents[] = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'excerpt' => get_the_excerpt(),
                    'permalink' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'medium'),
                    'categories' => wp_get_post_terms(get_the_ID(), 'talent_category', array('fields' => 'names')),
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success($talents);
    }

    public static function talent_single_template($template) {
        if (is_singular('talent')) {
            $custom_template = PPTM_PLUGIN_DIR . 'templates/single-talent.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    public static function talent_archive_template($template) {
        if (is_post_type_archive('talent') || is_tax('talent_category') || is_tax('talent_skill')) {
            $custom_template = PPTM_PLUGIN_DIR . 'templates/archive-talent.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    public static function article_single_template($template) {
        if (is_singular() && class_exists('PPTM_Article_Post_Types') && PPTM_Article_Post_Types::is_article_type(get_post_type())) {
            $custom_template = PPTM_PLUGIN_DIR . 'templates/single-article.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }

    public static function article_archive_template($template) {
        if (!class_exists('PPTM_Article_Post_Types')) {
            return $template;
        }
        $post_type = get_query_var('post_type');
        if (is_post_type_archive() && $post_type && PPTM_Article_Post_Types::is_article_type($post_type)) {
            $custom_template = PPTM_PLUGIN_DIR . 'templates/archive-articles.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
        return $template;
    }
}
