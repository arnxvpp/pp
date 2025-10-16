<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Public {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_filter('template_include', array($this, 'template_include'));
        add_action('wp_footer', array($this, 'track_talent_view'));
    }

    public function enqueue_scripts() {
        if (is_singular('pp_talent') || is_post_type_archive('pp_talent') || is_tax('talent_segment') || is_tax('talent_skill')) {
            wp_enqueue_style(
                'pptm-public',
                PPTM_PLUGIN_URL . 'assets/css/public.css',
                array(),
                PPTM_VERSION
            );

            wp_enqueue_script(
                'pptm-public',
                PPTM_PLUGIN_URL . 'assets/js/public.js',
                array('jquery'),
                PPTM_VERSION,
                true
            );

            wp_localize_script('pptm-public', 'pptmData', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'filter_nonce' => wp_create_nonce('pptm_filter_nonce'),
                'inquiry_nonce' => wp_create_nonce('pptm_inquiry_nonce'),
            ));
        }
    }

    public function template_include($template) {
        if (is_post_type_archive('pp_talent') || is_tax('talent_segment')) {
            $plugin_template = PPTM_PLUGIN_DIR . 'public/templates/archive-talent.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        if (is_singular('pp_talent')) {
            $plugin_template = PPTM_PLUGIN_DIR . 'public/templates/single-talent.php';
            if (file_exists($plugin_template)) {
                return $plugin_template;
            }
        }

        return $template;
    }

    public function track_talent_view() {
        if (!is_singular('pp_talent') || !get_option('pptm_enable_analytics', true)) {
            return;
        }

        $post_id = get_the_ID();
        ?>
        <script>
        jQuery(document).ready(function($) {
            $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                action: 'pptm_track_view',
                post_id: <?php echo absint($post_id); ?>
            });
        });
        </script>
        <?php
    }
}
