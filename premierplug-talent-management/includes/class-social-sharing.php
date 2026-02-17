<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Social_Sharing {

    public static function init() {
        add_filter('the_content', array(__CLASS__, 'add_share_buttons'), 30);
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_sharing_assets'));
        add_shortcode('pptm_share', array(__CLASS__, 'share_buttons_shortcode'));
        add_action('wp_ajax_pptm_track_share', array(__CLASS__, 'track_share_ajax'));
        add_action('wp_ajax_nopriv_pptm_track_share', array(__CLASS__, 'track_share_ajax'));
    }

    public static function enqueue_sharing_assets() {
        wp_enqueue_style('pptm-social-sharing', PPTM_PLUGIN_URL . 'assets/css/social-sharing.css', array(), PPTM_VERSION);
        wp_enqueue_script('pptm-social-sharing', PPTM_PLUGIN_URL . 'assets/js/social-sharing.js', array('jquery'), PPTM_VERSION, true);

        wp_localize_script('pptm-social-sharing', 'pptmSharing', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pptm_share_nonce'),
            'copyText' => __('Link copied!', 'premierplug-talent')
        ));
    }

    public static function add_share_buttons($content) {
        if (!is_singular()) {
            return $content;
        }

        global $post;
        $post_types = array('article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study', 'post');

        if (!in_array($post->post_type, $post_types)) {
            return $content;
        }

        $position = get_option('pptm_share_buttons_position', 'bottom');

        $buttons = self::render_share_buttons();

        if ($position === 'top') {
            return $buttons . $content;
        } elseif ($position === 'both') {
            return $buttons . $content . $buttons;
        } else {
            return $content . $buttons;
        }
    }

    public static function render_share_buttons($post_id = null) {
        if (!$post_id) {
            $post_id = get_the_ID();
        }

        $title = get_the_title($post_id);
        $url = get_permalink($post_id);
        $encoded_url = urlencode($url);
        $encoded_title = urlencode($title);

        $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
        $twitter_url = 'https://twitter.com/intent/tweet?url=' . $encoded_url . '&text=' . $encoded_title;
        $linkedin_url = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $encoded_url . '&title=' . $encoded_title;
        $whatsapp_url = 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;
        $email_url = 'mailto:?subject=' . $encoded_title . '&body=' . $encoded_url;

        $enabled_networks = get_option('pptm_share_buttons_networks', array('facebook', 'twitter', 'linkedin', 'whatsapp', 'email', 'copy'));

        ob_start();
        ?>
        <div class="pptm-share-buttons" data-post-id="<?php echo esc_attr($post_id); ?>">
            <div class="pptm-share-title"><?php echo esc_html(get_option('pptm_share_buttons_text', 'Share this:')); ?></div>
            <div class="pptm-share-buttons-container">
                <?php if (in_array('facebook', $enabled_networks)): ?>
                <a href="<?php echo esc_url($facebook_url); ?>"
                   class="pptm-share-btn pptm-share-facebook"
                   data-network="facebook"
                   target="_blank"
                   rel="noopener"
                   aria-label="Share on Facebook">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    <span>Facebook</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('twitter', $enabled_networks)): ?>
                <a href="<?php echo esc_url($twitter_url); ?>"
                   class="pptm-share-btn pptm-share-twitter"
                   data-network="twitter"
                   target="_blank"
                   rel="noopener"
                   aria-label="Share on Twitter">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                    </svg>
                    <span>Twitter</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('linkedin', $enabled_networks)): ?>
                <a href="<?php echo esc_url($linkedin_url); ?>"
                   class="pptm-share-btn pptm-share-linkedin"
                   data-network="linkedin"
                   target="_blank"
                   rel="noopener"
                   aria-label="Share on LinkedIn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                    </svg>
                    <span>LinkedIn</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('whatsapp', $enabled_networks)): ?>
                <a href="<?php echo esc_url($whatsapp_url); ?>"
                   class="pptm-share-btn pptm-share-whatsapp"
                   data-network="whatsapp"
                   target="_blank"
                   rel="noopener"
                   aria-label="Share on WhatsApp">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                    <span>WhatsApp</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('email', $enabled_networks)): ?>
                <a href="<?php echo esc_url($email_url); ?>"
                   class="pptm-share-btn pptm-share-email"
                   data-network="email"
                   aria-label="Share via Email">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                    <span>Email</span>
                </a>
                <?php endif; ?>

                <?php if (in_array('copy', $enabled_networks)): ?>
                <button class="pptm-share-btn pptm-share-copy"
                        data-network="copy"
                        data-url="<?php echo esc_url($url); ?>"
                        aria-label="Copy link">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                    </svg>
                    <span>Copy</span>
                </button>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function share_buttons_shortcode($atts) {
        $atts = shortcode_atts(array(
            'post_id' => get_the_ID(),
        ), $atts);

        return self::render_share_buttons($atts['post_id']);
    }

    public static function track_share_ajax() {
        check_ajax_referer('pptm_share_nonce', 'nonce');

        $post_id = intval($_POST['post_id']);
        $network = sanitize_text_field($_POST['network']);

        $share_counts = get_post_meta($post_id, '_pptm_share_counts', true);
        if (!is_array($share_counts)) {
            $share_counts = array();
        }

        if (!isset($share_counts[$network])) {
            $share_counts[$network] = 0;
        }

        $share_counts[$network]++;
        $network_counts = $share_counts;
        unset($network_counts['total']);
        $share_counts['total'] = array_sum($network_counts);

        update_post_meta($post_id, '_pptm_share_counts', $share_counts);

        wp_send_json_success(array(
            'network' => $network,
            'count' => $share_counts[$network],
            'total' => $share_counts['total']
        ));
    }

    public static function get_share_count($post_id, $network = 'total') {
        $share_counts = get_post_meta($post_id, '_pptm_share_counts', true);

        if (!is_array($share_counts)) {
            return 0;
        }

        return isset($share_counts[$network]) ? $share_counts[$network] : 0;
    }
}
