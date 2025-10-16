<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_AJAX {

    private $supabase;

    public function __construct() {
        $this->supabase = PPTM_Supabase_Client::get_instance();

        add_action('wp_ajax_pptm_filter_talents', array($this, 'filter_talents'));
        add_action('wp_ajax_nopriv_pptm_filter_talents', array($this, 'filter_talents'));

        add_action('wp_ajax_pptm_submit_inquiry', array($this, 'submit_inquiry'));
        add_action('wp_ajax_nopriv_pptm_submit_inquiry', array($this, 'submit_inquiry'));

        add_action('wp_ajax_pptm_track_view', array($this, 'track_view'));
        add_action('wp_ajax_nopriv_pptm_track_view', array($this, 'track_view'));
    }

    public function filter_talents() {
        check_ajax_referer('pptm_filter_nonce', 'nonce');

        $args = array(
            'post_type' => 'pp_talent',
            'post_status' => 'publish',
            'posts_per_page' => absint($_POST['per_page'] ?? 12),
            'paged' => absint($_POST['page'] ?? 1),
        );

        if (!empty($_POST['segments'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'talent_segment',
                'field' => 'slug',
                'terms' => array_map('sanitize_text_field', $_POST['segments']),
            );
        }

        if (!empty($_POST['skills'])) {
            $args['tax_query'][] = array(
                'taxonomy' => 'talent_skill',
                'field' => 'slug',
                'terms' => array_map('sanitize_text_field', $_POST['skills']),
            );
        }

        if (!empty($_POST['availability'])) {
            $args['meta_query'][] = array(
                'key' => '_pptm_availability_status',
                'value' => sanitize_text_field($_POST['availability']),
            );
        }

        if (!empty($_POST['search'])) {
            $args['s'] = sanitize_text_field($_POST['search']);
        }

        if (!empty($_POST['featured'])) {
            $args['meta_query'][] = array(
                'key' => '_pptm_featured',
                'value' => '1',
            );
        }

        $query = new WP_Query($args);

        ob_start();
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                include PPTM_PLUGIN_DIR . 'public/partials/talent-card.php';
            }
        } else {
            echo '<p class="no-talents-found">' . esc_html__('No talents found matching your criteria.', 'premierplug-talent') . '</p>';
        }
        $html = ob_get_clean();

        wp_reset_postdata();

        wp_send_json_success(array(
            'html' => $html,
            'found' => $query->found_posts,
            'max_pages' => $query->max_num_pages,
        ));
    }

    public function submit_inquiry() {
        check_ajax_referer('pptm_inquiry_nonce', 'nonce');

        $talent_id = absint($_POST['talent_id'] ?? 0);
        $name = sanitize_text_field($_POST['name'] ?? '');
        $email = sanitize_email($_POST['email'] ?? '');
        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $organization = sanitize_text_field($_POST['organization'] ?? '');
        $country = sanitize_text_field($_POST['country'] ?? '');
        $message = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($name) || empty($email) || empty($message)) {
            wp_send_json_error(array('message' => __('Please fill in all required fields.', 'premierplug-talent')));
        }

        if ($this->supabase->is_configured()) {
            $supabase_talent_id = get_post_meta($talent_id, '_pptm_supabase_id', true);

            $result = $this->supabase->insert('talent_inquiries', array(
                'talent_id' => $supabase_talent_id,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'organization' => $organization,
                'country' => $country,
                'message' => $message,
                'inquiry_type' => 'information',
                'status' => 'new',
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ));

            if (is_wp_error($result)) {
                wp_send_json_error(array('message' => __('Failed to submit inquiry. Please try again.', 'premierplug-talent')));
            }
        }

        $inquiry_count = get_post_meta($talent_id, '_pptm_inquiry_count', true);
        update_post_meta($talent_id, '_pptm_inquiry_count', absint($inquiry_count) + 1);

        $this->send_inquiry_notification($talent_id, $name, $email, $message);

        wp_send_json_success(array('message' => __('Your inquiry has been submitted successfully!', 'premierplug-talent')));
    }

    public function track_view() {
        $post_id = absint($_POST['post_id'] ?? 0);

        if (!$post_id || get_post_type($post_id) !== 'pp_talent') {
            wp_send_json_error();
        }

        $view_count = get_post_meta($post_id, '_pptm_view_count', true);
        update_post_meta($post_id, '_pptm_view_count', absint($view_count) + 1);

        if ($this->supabase->is_configured()) {
            $supabase_talent_id = get_post_meta($post_id, '_pptm_supabase_id', true);

            if ($supabase_talent_id) {
                $this->supabase->insert('talent_analytics', array(
                    'talent_id' => $supabase_talent_id,
                    'event_type' => 'view',
                    'event_date' => current_time('Y-m-d'),
                    'count' => 1,
                ));
            }
        }

        wp_send_json_success();
    }

    private function send_inquiry_notification($talent_id, $name, $email, $message) {
        $talent_title = get_the_title($talent_id);
        $admin_email = get_option('admin_email');

        $subject = sprintf(__('[Talent Inquiry] New inquiry for %s', 'premierplug-talent'), $talent_title);

        $body = sprintf(
            __('New talent inquiry received:%1$s%1$sTalent: %2$s%1$sFrom: %3$s (%4$s)%1$sMessage:%1$s%5$s', 'premierplug-talent'),
            "\n\n",
            $talent_title,
            $name,
            $email,
            $message
        );

        wp_mail($admin_email, $subject, $body);
    }
}
