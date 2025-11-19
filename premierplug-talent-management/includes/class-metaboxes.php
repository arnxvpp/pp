<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Metaboxes {

    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_metaboxes'));
        add_action('save_post_talent', array(__CLASS__, 'save_talent_details'));
    }

    public static function add_metaboxes() {
        add_meta_box(
            'talent_contact_details',
            __('Contact Information', 'premierplug-talent'),
            array(__CLASS__, 'render_contact_metabox'),
            'talent',
            'normal',
            'high'
        );

        add_meta_box(
            'talent_professional_details',
            __('Professional Information', 'premierplug-talent'),
            array(__CLASS__, 'render_professional_metabox'),
            'talent',
            'normal',
            'high'
        );

        add_meta_box(
            'talent_social_media',
            __('Social Media', 'premierplug-talent'),
            array(__CLASS__, 'render_social_metabox'),
            'talent',
            'side',
            'default'
        );
    }

    public static function render_contact_metabox($post) {
        wp_nonce_field('talent_details_nonce', 'talent_details_nonce_field');

        $email = get_post_meta($post->ID, '_talent_email', true);
        $phone = get_post_meta($post->ID, '_talent_phone', true);
        $website = get_post_meta($post->ID, '_talent_website', true);
        $location = get_post_meta($post->ID, '_talent_location', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="talent_email"><?php _e('Email', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="email" id="talent_email" name="talent_email" value="<?php echo esc_attr($email); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="talent_phone"><?php _e('Phone', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="tel" id="talent_phone" name="talent_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="talent_website"><?php _e('Website', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="url" id="talent_website" name="talent_website" value="<?php echo esc_attr($website); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="talent_location"><?php _e('Location', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="text" id="talent_location" name="talent_location" value="<?php echo esc_attr($location); ?>" class="regular-text" placeholder="City, Country" />
                </td>
            </tr>
        </table>
        <?php
    }

    public static function render_professional_metabox($post) {
        $experience = get_post_meta($post->ID, '_talent_experience', true);
        $rate = get_post_meta($post->ID, '_talent_rate', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="talent_experience"><?php _e('Years of Experience', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="number" id="talent_experience" name="talent_experience" value="<?php echo esc_attr($experience); ?>" min="0" max="99" />
                </td>
            </tr>
            <tr>
                <th><label for="talent_rate"><?php _e('Rate/Fee', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="text" id="talent_rate" name="talent_rate" value="<?php echo esc_attr($rate); ?>" class="regular-text" placeholder="e.g., $500/hour, POA" />
                    <p class="description"><?php _e('Enter rate or "Price on Application"', 'premierplug-talent'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    public static function render_social_metabox($post) {
        $linkedin = get_post_meta($post->ID, '_talent_linkedin', true);
        $twitter = get_post_meta($post->ID, '_talent_twitter', true);
        $instagram = get_post_meta($post->ID, '_talent_instagram', true);
        $youtube = get_post_meta($post->ID, '_talent_youtube', true);
        ?>
        <p>
            <label for="talent_linkedin"><strong><?php _e('LinkedIn', 'premierplug-talent'); ?></strong></label><br>
            <input type="url" id="talent_linkedin" name="talent_linkedin" value="<?php echo esc_attr($linkedin); ?>" class="widefat" placeholder="https://linkedin.com/in/username" />
        </p>
        <p>
            <label for="talent_twitter"><strong><?php _e('Twitter/X', 'premierplug-talent'); ?></strong></label><br>
            <input type="url" id="talent_twitter" name="talent_twitter" value="<?php echo esc_attr($twitter); ?>" class="widefat" placeholder="https://twitter.com/username" />
        </p>
        <p>
            <label for="talent_instagram"><strong><?php _e('Instagram', 'premierplug-talent'); ?></strong></label><br>
            <input type="url" id="talent_instagram" name="talent_instagram" value="<?php echo esc_attr($instagram); ?>" class="widefat" placeholder="https://instagram.com/username" />
        </p>
        <p>
            <label for="talent_youtube"><strong><?php _e('YouTube', 'premierplug-talent'); ?></strong></label><br>
            <input type="url" id="talent_youtube" name="talent_youtube" value="<?php echo esc_attr($youtube); ?>" class="widefat" placeholder="https://youtube.com/@username" />
        </p>
        <?php
    }

    public static function save_talent_details($post_id) {
        if (!isset($_POST['talent_details_nonce_field']) || !wp_verify_nonce($_POST['talent_details_nonce_field'], 'talent_details_nonce')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'talent_email',
            'talent_phone',
            'talent_website',
            'talent_location',
            'talent_experience',
            'talent_rate',
            'talent_linkedin',
            'talent_twitter',
            'talent_instagram',
            'talent_youtube',
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
}
