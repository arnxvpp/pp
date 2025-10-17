<?php
/**
 * Talent Inquiry Handler
 *
 * @package PremierPlug_Talent_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class PremierPlug_Talent_Inquiry {

    private $supabase;
    private $table = 'talent_inquiries';

    public function __construct() {
        $this->supabase = PremierPlug_Supabase_Client::get_instance();

        add_action('wp_ajax_submit_talent_inquiry', array($this, 'handle_inquiry'));
        add_action('wp_ajax_nopriv_submit_talent_inquiry', array($this, 'handle_inquiry'));
        add_shortcode('talent_inquiry_form', array($this, 'render_form'));
    }

    /**
     * Handle AJAX inquiry submission
     */
    public function handle_inquiry() {
        check_ajax_referer('talent_inquiry_nonce', 'nonce');

        $talent_id = intval($_POST['talent_id']);
        $name = sanitize_text_field($_POST['name']);
        $email = sanitize_email($_POST['email']);
        $phone = sanitize_text_field($_POST['phone']);
        $company = sanitize_text_field($_POST['company']);
        $message = sanitize_textarea_field($_POST['message']);

        $errors = array();

        if (empty($name)) $errors[] = 'Name is required';
        if (empty($email) || !is_email($email)) $errors[] = 'Valid email required';
        if (empty($message)) $errors[] = 'Message is required';

        if (!empty($errors)) {
            wp_send_json_error(array('messages' => $errors));
        }

        $inquiry_data = array(
            'talent_id' => $talent_id,
            'wp_talent_id' => $talent_id,
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'company' => $company,
            'message' => $message,
            'status' => 'new',
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'created_at' => current_time('mysql', true)
        );

        $result = $this->supabase->insert($this->table, $inquiry_data);

        if ($result) {
            $this->send_admin_notification($inquiry_data);
            $this->send_client_confirmation($inquiry_data);

            do_action('premierplug_talent_inquiry_submitted', $inquiry_data);

            wp_send_json_success('Thank you! Your inquiry has been submitted.');
        } else {
            wp_send_json_error('Failed to submit inquiry. Please try again.');
        }
    }

    /**
     * Render inquiry form
     */
    public function render_form($atts) {
        $atts = shortcode_atts(array(
            'talent_id' => get_the_ID()
        ), $atts);

        ob_start();
        ?>
        <form class="talent-inquiry-form" data-talent-id="<?php echo esc_attr($atts['talent_id']); ?>">
            <?php wp_nonce_field('talent_inquiry_nonce', 'inquiry_nonce'); ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="inquiry_name">Name *</label>
                    <input type="text" id="inquiry_name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="inquiry_email">Email *</label>
                    <input type="email" id="inquiry_email" name="email" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="inquiry_phone">Phone</label>
                    <input type="tel" id="inquiry_phone" name="phone">
                </div>

                <div class="form-group">
                    <label for="inquiry_company">Company</label>
                    <input type="text" id="inquiry_company" name="company">
                </div>
            </div>

            <div class="form-group">
                <label for="inquiry_message">Message *</label>
                <textarea id="inquiry_message" name="message" rows="5" required></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Submit Inquiry</button>
            </div>

            <div class="form-response"></div>
        </form>

        <script>
        jQuery(document).ready(function($) {
            $('.talent-inquiry-form').on('submit', function(e) {
                e.preventDefault();

                var $form = $(this);
                var $response = $form.find('.form-response');
                var $button = $form.find('button[type="submit"]');

                $button.prop('disabled', true).text('Submitting...');
                $response.html('');

                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    type: 'POST',
                    data: {
                        action: 'submit_talent_inquiry',
                        nonce: $form.find('#inquiry_nonce').val(),
                        talent_id: $form.data('talent-id'),
                        name: $form.find('[name="name"]').val(),
                        email: $form.find('[name="email"]').val(),
                        phone: $form.find('[name="phone"]').val(),
                        company: $form.find('[name="company"]').val(),
                        message: $form.find('[name="message"]').val()
                    },
                    success: function(response) {
                        if (response.success) {
                            $response.html('<div class="alert alert-success">' + response.data + '</div>');
                            $form[0].reset();
                        } else {
                            $response.html('<div class="alert alert-error">' + response.data + '</div>');
                        }
                    },
                    error: function() {
                        $response.html('<div class="alert alert-error">Network error. Please try again.</div>');
                    },
                    complete: function() {
                        $button.prop('disabled', false).text('Submit Inquiry');
                    }
                });
            });
        });
        </script>
        <?php
        return ob_get_clean();
    }

    /**
     * Send admin notification
     */
    private function send_admin_notification($data) {
        $to = get_option('admin_email');
        $subject = 'New Talent Inquiry: ' . $data['name'];

        $message = sprintf(
            "New talent inquiry received:\n\n" .
            "Talent ID: %d\n" .
            "Name: %s\n" .
            "Email: %s\n" .
            "Phone: %s\n" .
            "Company: %s\n\n" .
            "Message:\n%s\n\n" .
            "---\n" .
            "Submitted from: %s\n" .
            "IP Address: %s",
            $data['talent_id'],
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['company'],
            $data['message'],
            get_permalink($data['talent_id']),
            $data['ip_address']
        );

        wp_mail($to, $subject, $message);
    }

    /**
     * Send client confirmation
     */
    private function send_client_confirmation($data) {
        $to = $data['email'];
        $subject = 'Thank you for your inquiry - PremierPlug';

        $message = sprintf(
            "Dear %s,\n\n" .
            "Thank you for your inquiry. We have received your message and will respond within 24-48 hours.\n\n" .
            "Your inquiry details:\n" .
            "Message: %s\n\n" .
            "Best regards,\n" .
            "PremierPlug Team\n\n" .
            "---\n" .
            "This is an automated confirmation email.",
            $data['name'],
            $data['message']
        );

        wp_mail($to, $subject, $message);
    }
}

new PremierPlug_Talent_Inquiry();
