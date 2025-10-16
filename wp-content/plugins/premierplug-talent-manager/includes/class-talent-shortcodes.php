<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Shortcodes {

    public function __construct() {
        add_shortcode('talent_grid', array($this, 'talent_grid_shortcode'));
        add_shortcode('featured_talents', array($this, 'featured_talents_shortcode'));
        add_shortcode('talent_segments', array($this, 'talent_segments_shortcode'));
    }

    public function talent_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'segment' => '',
            'count' => 12,
            'columns' => 3,
            'featured' => false,
        ), $atts);

        $args = array(
            'post_type' => 'pp_talent',
            'post_status' => 'publish',
            'posts_per_page' => absint($atts['count']),
        );

        if (!empty($atts['segment'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'talent_segment',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['segment']),
                ),
            );
        }

        if ($atts['featured']) {
            $args['meta_query'] = array(
                array(
                    'key' => '_pptm_featured',
                    'value' => '1',
                ),
            );
        }

        $query = new WP_Query($args);

        ob_start();
        if ($query->have_posts()) {
            echo '<div class="pptm-talent-grid pptm-columns-' . absint($atts['columns']) . '">';
            while ($query->have_posts()) {
                $query->the_post();
                include PPTM_PLUGIN_DIR . 'public/partials/talent-card.php';
            }
            echo '</div>';
        }

        wp_reset_postdata();

        return ob_get_clean();
    }

    public function featured_talents_shortcode($atts) {
        $atts['featured'] = true;
        return $this->talent_grid_shortcode($atts);
    }

    public function talent_segments_shortcode($atts) {
        $atts = shortcode_atts(array(
            'show_count' => true,
        ), $atts);

        $terms = get_terms(array(
            'taxonomy' => 'talent_segment',
            'hide_empty' => true,
        ));

        if (empty($terms) || is_wp_error($terms)) {
            return '';
        }

        ob_start();
        echo '<div class="pptm-segment-list">';
        foreach ($terms as $term) {
            $count = $atts['show_count'] ? ' <span class="count">(' . $term->count . ')</span>' : '';
            echo '<a href="' . esc_url(get_term_link($term)) . '" class="pptm-segment-item">';
            echo esc_html($term->name) . $count;
            echo '</a>';
        }
        echo '</div>';

        return ob_get_clean();
    }
}
