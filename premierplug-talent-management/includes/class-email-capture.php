<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Email_Capture {

    public static function init() {
        add_action('wp_footer', array(__CLASS__, 'output_popup_html'));
        add_action('wp_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
        add_shortcode('pptm_email_form', array(__CLASS__, 'inline_form_shortcode'));
        add_action('wp_ajax_pptm_subscribe', array(__CLASS__, 'handle_subscription'));
        add_action('wp_ajax_nopriv_pptm_subscribe', array(__CLASS__, 'handle_subscription'));
    }

    public static function enqueue_assets() {
        wp_enqueue_style('pptm-email-capture', PPTM_PLUGIN_URL . 'assets/css/email-capture.css', array(), PPTM_VERSION);
        wp_enqueue_script('pptm-email-capture', PPTM_PLUGIN_URL . 'assets/js/email-capture.js', array('jquery'), PPTM_VERSION, true);

        $popup_enabled = get_option('pptm_popup_enabled', 'no');
        $trigger_type = get_option('pptm_popup_trigger', 'exit');
        $trigger_value = get_option('pptm_popup_trigger_value', '5');
        $frequency_days = get_option('pptm_popup_frequency', '7');

        wp_localize_script('pptm-email-capture', 'pptmEmailCapture', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pptm_email_nonce'),
            'popup_enabled' => $popup_enabled === 'yes',
            'trigger_type' => $trigger_type,
            'trigger_value' => intval($trigger_value),
            'frequency_days' => intval($frequency_days),
            'messages' => array(
                'success' => __('Thank you for subscribing!', 'premierplug-talent'),
                'error' => __('Please enter a valid email address.', 'premierplug-talent'),
                'already_subscribed' => __('This email is already subscribed.', 'premierplug-talent'),
            )
        ));
    }

    public static function output_popup_html() {
        $popup_enabled = get_option('pptm_popup_enabled', 'no');

        if ($popup_enabled !== 'yes') {
            return;
        }

        if (current_user_can('manage_options') && get_option('pptm_popup_hide_admin', 'yes') === 'yes') {
            return;
        }

        $title = get_option('pptm_popup_title', 'Stay Updated!');
        $subtitle = get_option('pptm_popup_subtitle', 'Get the latest news and updates delivered to your inbox.');
        $button_text = get_option('pptm_popup_button_text', 'Subscribe');
        $privacy_text = get_option('pptm_popup_privacy_text', 'We respect your privacy. Unsubscribe anytime.');
        $custom_form = get_option('pptm_popup_custom_form', '');

        ?>
        <div id="pptm-email-popup" class="pptm-popup" style="display:none;">
            <div class="pptm-popup-overlay"></div>
            <div class="pptm-popup-container">
                <button class="pptm-popup-close" aria-label="Close">&times;</button>
                <div class="pptm-popup-content">
                    <div class="pptm-popup-header">
                        <h3 class="pptm-popup-title"><?php echo esc_html($title); ?></h3>
                        <?php if ($subtitle): ?>
                            <p class="pptm-popup-subtitle"><?php echo esc_html($subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="pptm-popup-body">
                        <?php if ($custom_form): ?>
                            <?php echo do_shortcode(wp_kses_post($custom_form)); ?>
                        <?php else: ?>
                            <form class="pptm-email-form" id="pptm-popup-form">
                                <div class="pptm-form-group">
                                    <input type="email"
                                           name="email"
                                           class="pptm-email-input"
                                           placeholder="Enter your email address"
                                           required>
                                </div>
                                <div class="pptm-form-group">
                                    <button type="submit" class="pptm-submit-button">
                                        <?php echo esc_html($button_text); ?>
                                    </button>
                                </div>
                                <div class="pptm-form-message"></div>
                                <?php if ($privacy_text): ?>
                                    <p class="pptm-privacy-text"><?php echo esc_html($privacy_text); ?></p>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function inline_form_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'Subscribe to Our Newsletter',
            'subtitle' => 'Get updates delivered to your inbox.',
            'button' => 'Subscribe',
            'style' => 'default',
        ), $atts);

        ob_start();
        ?>
        <div class="pptm-inline-form pptm-inline-form-<?php echo esc_attr($atts['style']); ?>">
            <div class="pptm-inline-form-header">
                <h3 class="pptm-inline-form-title"><?php echo esc_html($atts['title']); ?></h3>
                <?php if ($atts['subtitle']): ?>
                    <p class="pptm-inline-form-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                <?php endif; ?>
            </div>
            <form class="pptm-email-form pptm-inline-email-form">
                <div class="pptm-form-row">
                    <input type="email"
                           name="email"
                           class="pptm-email-input"
                           placeholder="Your email address"
                           required>
                    <button type="submit" class="pptm-submit-button">
                        <?php echo esc_html($atts['button']); ?>
                    </button>
                </div>
                <div class="pptm-form-message"></div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public static function handle_subscription() {
        check_ajax_referer('pptm_email_nonce', 'nonce');

        $email = sanitize_email($_POST['email']);

        if (!is_email($email)) {
            wp_send_json_error(array('message' => __('Invalid email address.', 'premierplug-talent')));
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'pptm_subscribers';

        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT id FROM {$table_name} WHERE email = %s",
            $email
        ));

        if ($existing) {
            wp_send_json_error(array('message' => __('This email is already subscribed.', 'premierplug-talent')));
        }

        $result = $wpdb->insert(
            $table_name,
            array(
                'email' => $email,
                'status' => 'active',
                'subscribed_at' => current_time('mysql'),
                'ip_address' => self::get_client_ip(),
                'user_agent' => isset($_SERVER['HTTP_USER_AGENT']) ? substr(sanitize_text_field(wp_unslash($_SERVER['HTTP_USER_AGENT'])), 0, 255) : '',
            ),
            array('%s', '%s', '%s', '%s', '%s')
        );

        if ($result) {
            do_action('pptm_new_subscriber', $email);

            self::send_welcome_email($email);

            wp_send_json_success(array('message' => __('Thank you for subscribing!', 'premierplug-talent')));
        } else {
            wp_send_json_error(array('message' => __('Something went wrong. Please try again.', 'premierplug-talent')));
        }
    }

    private static function send_welcome_email($email) {
        $send_welcome = get_option('pptm_send_welcome_email', 'yes');

        if ($send_welcome !== 'yes') {
            return;
        }

        $subject = get_option('pptm_welcome_email_subject', 'Welcome to ' . get_bloginfo('name'));
        $message = get_option('pptm_welcome_email_message', 'Thank you for subscribing to our newsletter!');

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, wpautop($message), $headers);
    }

    private static function get_client_ip() {
        $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        return filter_var($ip, FILTER_VALIDATE_IP) ? sanitize_text_field($ip) : '';
    }

    public static function create_subscribers_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'pptm_subscribers';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$table_name} (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            email varchar(255) NOT NULL,
            status varchar(20) DEFAULT 'active',
            subscribed_at datetime DEFAULT CURRENT_TIMESTAMP,
            unsubscribed_at datetime DEFAULT NULL,
            ip_address varchar(45) DEFAULT NULL,
            user_agent varchar(255) DEFAULT NULL,
            PRIMARY KEY (id),
            UNIQUE KEY email (email),
            KEY status (status)
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public static function get_subscriber_count() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pptm_subscribers';

        return $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE status = 'active'");
    }

    public static function export_subscribers() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'pptm_subscribers';

        $subscribers = $wpdb->get_results(
            "SELECT email, subscribed_at FROM {$table_name} WHERE status = 'active' ORDER BY subscribed_at DESC"
        );

        return $subscribers;
    }
}

register_activation_hook(PPTM_PLUGIN_FILE, array('PPTM_Email_Capture', 'create_subscribers_table'));
