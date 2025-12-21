<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Ad_Manager {

    public static function init() {
        add_action('wp_head', array(__CLASS__, 'output_header_ad'));
        add_action('wp_footer', array(__CLASS__, 'output_footer_ad'));
        add_filter('the_content', array(__CLASS__, 'insert_in_content_ads'), 20);
        add_action('dynamic_sidebar_before', array(__CLASS__, 'output_sidebar_ad_top'));
        add_action('dynamic_sidebar_after', array(__CLASS__, 'output_sidebar_ad_bottom'));
        add_shortcode('pptm_ad', array(__CLASS__, 'ad_shortcode'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_ad_styles'));
    }

    public static function enqueue_ad_styles() {
        $custom_css = "
        .pptm-ad-zone {
            margin: 20px 0;
            text-align: center;
            clear: both;
        }
        .pptm-ad-zone.header-ad {
            margin: 10px 0;
        }
        .pptm-ad-zone.footer-ad {
            margin: 30px 0 20px;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
        }
        .pptm-ad-zone.sidebar-ad {
            margin: 15px 0;
        }
        .pptm-ad-zone.in-content-ad {
            margin: 25px 0;
            padding: 15px 0;
        }
        .pptm-ad-label {
            font-size: 10px;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }
        .pptm-ad-container {
            display: inline-block;
            max-width: 100%;
        }
        @media (max-width: 768px) {
            .pptm-ad-zone.desktop-only {
                display: none;
            }
        }
        @media (min-width: 769px) {
            .pptm-ad-zone.mobile-only {
                display: none;
            }
        }
        .pptm-sticky-ad {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 9999;
            background: #fff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            padding: 10px;
            text-align: center;
        }
        .pptm-sticky-ad-close {
            position: absolute;
            top: 5px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
            color: #666;
            background: none;
            border: none;
            padding: 0;
            line-height: 1;
        }
        ";
        wp_add_inline_style('pptm-public', $custom_css);
    }

    public static function output_header_ad() {
        if (!self::should_show_ads()) {
            return;
        }

        $header_ad = get_option('pptm_header_ad_code', '');
        if (empty($header_ad)) {
            return;
        }

        echo '<div class="pptm-ad-zone header-ad desktop-only">';
        echo '<div class="pptm-ad-label">Advertisement</div>';
        echo '<div class="pptm-ad-container">';
        echo wp_kses_post(self::process_ad_code($header_ad));
        echo '</div>';
        echo '</div>';
    }

    public static function output_footer_ad() {
        if (!self::should_show_ads()) {
            return;
        }

        $footer_ad = get_option('pptm_footer_ad_code', '');
        if (empty($footer_ad)) {
            return;
        }

        echo '<div class="pptm-ad-zone footer-ad">';
        echo '<div class="pptm-ad-label">Advertisement</div>';
        echo '<div class="pptm-ad-container">';
        echo wp_kses_post(self::process_ad_code($footer_ad));
        echo '</div>';
        echo '</div>';
    }

    public static function output_sidebar_ad_top() {
        if (!self::should_show_ads()) {
            return;
        }

        $sidebar_ad = get_option('pptm_sidebar_ad_code', '');
        if (empty($sidebar_ad)) {
            return;
        }

        echo '<div class="pptm-ad-zone sidebar-ad">';
        echo '<div class="pptm-ad-label">Advertisement</div>';
        echo '<div class="pptm-ad-container">';
        echo wp_kses_post(self::process_ad_code($sidebar_ad));
        echo '</div>';
        echo '</div>';
    }

    public static function output_sidebar_ad_bottom() {
    }

    public static function insert_in_content_ads($content) {
        if (!is_singular() || !self::should_show_ads()) {
            return $content;
        }

        $in_content_ad = get_option('pptm_in_content_ad_code', '');
        if (empty($in_content_ad)) {
            return $content;
        }

        $paragraph_after = get_option('pptm_in_content_ad_position', 3);

        $paragraphs = explode('</p>', $content);
        $ad_inserted = false;

        foreach ($paragraphs as $index => $paragraph) {
            if ($index === intval($paragraph_after) && !$ad_inserted) {
                $ad_html = '<div class="pptm-ad-zone in-content-ad">';
                $ad_html .= '<div class="pptm-ad-label">Advertisement</div>';
                $ad_html .= '<div class="pptm-ad-container">';
                $ad_html .= self::process_ad_code($in_content_ad);
                $ad_html .= '</div>';
                $ad_html .= '</div>';

                $paragraphs[$index] .= '</p>' . $ad_html;
                $ad_inserted = true;
            } else {
                $paragraphs[$index] .= '</p>';
            }
        }

        return implode('', $paragraphs);
    }

    public static function output_mobile_sticky_ad() {
        if (!self::should_show_ads() || !wp_is_mobile()) {
            return;
        }

        $mobile_sticky_ad = get_option('pptm_mobile_sticky_ad_code', '');
        if (empty($mobile_sticky_ad)) {
            return;
        }

        echo '<div class="pptm-sticky-ad mobile-only" id="pptm-sticky-ad">';
        echo '<button class="pptm-sticky-ad-close" onclick="document.getElementById(\'pptm-sticky-ad\').style.display=\'none\';">&times;</button>';
        echo '<div class="pptm-ad-container">';
        echo wp_kses_post(self::process_ad_code($mobile_sticky_ad));
        echo '</div>';
        echo '</div>';
    }

    public static function ad_shortcode($atts) {
        $atts = shortcode_atts(array(
            'zone' => 'custom',
            'id' => '',
        ), $atts);

        if (empty($atts['id'])) {
            return '';
        }

        $ad_code = get_option('pptm_custom_ad_' . sanitize_key($atts['id']), '');

        if (empty($ad_code)) {
            return '';
        }

        $output = '<div class="pptm-ad-zone custom-ad">';
        $output .= '<div class="pptm-ad-label">Advertisement</div>';
        $output .= '<div class="pptm-ad-container">';
        $output .= self::process_ad_code($ad_code);
        $output .= '</div>';
        $output .= '</div>';

        return $output;
    }

    private static function should_show_ads() {
        $post_types = array('talent', 'article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post', 'page');

        if (is_singular($post_types)) {
            global $post;
            $disable_ads = get_post_meta($post->ID, '_pptm_disable_ads', true);
            if ($disable_ads === 'yes') {
                return false;
            }
            return true;
        }

        return is_home() || is_archive();
    }

    private static function process_ad_code($ad_code) {
        $ad_code = stripslashes($ad_code);

        $ad_code = str_replace(array('&lt;', '&gt;', '&quot;'), array('<', '>', '"'), $ad_code);

        return $ad_code;
    }

    public static function add_disable_ads_metabox() {
        $post_types = array('talent', 'article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post', 'page');

        add_meta_box(
            'pptm_disable_ads',
            'Advertisement Settings',
            array(__CLASS__, 'render_disable_ads_metabox'),
            $post_types,
            'side',
            'low'
        );
    }

    public static function render_disable_ads_metabox($post) {
        wp_nonce_field('pptm_disable_ads_nonce', 'pptm_disable_ads_nonce');
        $disable_ads = get_post_meta($post->ID, '_pptm_disable_ads', true);
        ?>
        <label>
            <input type="checkbox" name="pptm_disable_ads" value="yes" <?php checked($disable_ads, 'yes'); ?>>
            Disable ads on this page
        </label>
        <p class="description">Check this to hide all advertisements on this specific page.</p>
        <?php
    }

    public static function save_disable_ads_meta($post_id) {
        if (!isset($_POST['pptm_disable_ads_nonce']) || !wp_verify_nonce($_POST['pptm_disable_ads_nonce'], 'pptm_disable_ads_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $disable_ads = isset($_POST['pptm_disable_ads']) ? 'yes' : 'no';
        update_post_meta($post_id, '_pptm_disable_ads', $disable_ads);
    }
}
