<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Shortcodes {

    public static function init() {
        add_shortcode('talent_grid', array(__CLASS__, 'talent_grid'));
        add_shortcode('talent_list', array(__CLASS__, 'talent_list'));
        add_shortcode('talent_single', array(__CLASS__, 'talent_single'));
        add_shortcode('talent_search', array(__CLASS__, 'talent_search'));
    }

    public static function talent_grid($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'skill' => '',
            'limit' => 12,
            'columns' => 3,
        ), $atts);

        $args = array(
            'post_type' => 'talent',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish',
        );

        if (!empty($atts['category'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'talent_category',
                'field' => 'slug',
                'terms' => explode(',', $atts['category']),
            );
        }

        if (!empty($atts['skill'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'talent_skill',
                'field' => 'slug',
                'terms' => explode(',', $atts['skill']),
            );
        }

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="pptm-talent-grid columns-' . esc_attr($atts['columns']) . '">';

            while ($query->have_posts()) {
                $query->the_post();
                include PPTM_PLUGIN_DIR . 'templates/talent-card.php';
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="pptm-no-results">' . __('No talents found.', 'premierplug-talent') . '</p>';
        }

        return ob_get_clean();
    }

    public static function talent_list($atts) {
        $atts = shortcode_atts(array(
            'category' => '',
            'limit' => -1,
        ), $atts);

        $args = array(
            'post_type' => 'talent',
            'posts_per_page' => intval($atts['limit']),
            'post_status' => 'publish',
        );

        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'talent_category',
                    'field' => 'slug',
                    'terms' => explode(',', $atts['category']),
                )
            );
        }

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="pptm-talent-list">';

            while ($query->have_posts()) {
                $query->the_post();
                include PPTM_PLUGIN_DIR . 'templates/talent-list-item.php';
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="pptm-no-results">' . __('No talents found.', 'premierplug-talent') . '</p>';
        }

        return ob_get_clean();
    }

    public static function talent_single($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts);

        $post_id = intval($atts['id']);

        if (!$post_id) {
            return '<p>' . __('Invalid talent ID.', 'premierplug-talent') . '</p>';
        }

        $post = get_post($post_id);

        if (!$post || $post->post_type !== 'talent') {
            return '<p>' . __('Talent not found.', 'premierplug-talent') . '</p>';
        }

        ob_start();
        include PPTM_PLUGIN_DIR . 'templates/talent-single.php';
        return ob_get_clean();
    }

    public static function talent_search($atts) {
        ob_start();
        include PPTM_PLUGIN_DIR . 'templates/talent-search.php';
        return ob_get_clean();
    }
}
