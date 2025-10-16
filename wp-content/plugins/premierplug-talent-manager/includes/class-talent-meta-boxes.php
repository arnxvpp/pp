<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Meta_Boxes {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_pp_talent', array($this, 'save_meta_boxes'), 10, 2);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'pptm_talent_profile',
            __('Talent Profile Information', 'premierplug-talent'),
            array($this, 'render_profile_meta_box'),
            'pp_talent',
            'normal',
            'high'
        );

        add_meta_box(
            'pptm_talent_contact',
            __('Contact Information', 'premierplug-talent'),
            array($this, 'render_contact_meta_box'),
            'pp_talent',
            'normal',
            'default'
        );

        add_meta_box(
            'pptm_talent_portfolio',
            __('Portfolio & Media', 'premierplug-talent'),
            array($this, 'render_portfolio_meta_box'),
            'pp_talent',
            'normal',
            'default'
        );

        add_meta_box(
            'pptm_talent_settings',
            __('Talent Settings', 'premierplug-talent'),
            array($this, 'render_settings_meta_box'),
            'pp_talent',
            'side',
            'default'
        );

        add_meta_box(
            'pptm_talent_analytics',
            __('Analytics', 'premierplug-talent'),
            array($this, 'render_analytics_meta_box'),
            'pp_talent',
            'side',
            'low'
        );
    }

    public function render_profile_meta_box($post) {
        wp_nonce_field('pptm_save_profile', 'pptm_profile_nonce');

        $headline = get_post_meta($post->ID, '_pptm_headline', true);
        $experience_years = get_post_meta($post->ID, '_pptm_experience_years', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="pptm_headline"><?php _e('Headline/Tagline', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="text" id="pptm_headline" name="pptm_headline" value="<?php echo esc_attr($headline); ?>" class="large-text" placeholder="e.g., Award-Winning Voice Actor & Content Creator" />
                    <p class="description"><?php _e('Short professional tagline or title', 'premierplug-talent'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="pptm_experience_years"><?php _e('Years of Experience', 'premierplug-talent'); ?></label></th>
                <td>
                    <input type="number" id="pptm_experience_years" name="pptm_experience_years" value="<?php echo esc_attr($experience_years); ?>" min="0" max="100" />
                    <p class="description"><?php _e('Total years in the industry', 'premierplug-talent'); ?></p>
                </td>
            </tr>
        </table>
        <?php
    }

    public function render_contact_meta_box($post) {
        wp_nonce_field('pptm_save_contact', 'pptm_contact_nonce');

        $email = get_post_meta($post->ID, '_pptm_contact_email', true);
        $phone = get_post_meta($post->ID, '_pptm_contact_phone', true);
        $website = get_post_meta($post->ID, '_pptm_website', true);
        $instagram = get_post_meta($post->ID, '_pptm_social_instagram', true);
        $linkedin = get_post_meta($post->ID, '_pptm_social_linkedin', true);
        $twitter = get_post_meta($post->ID, '_pptm_social_twitter', true);
        $youtube = get_post_meta($post->ID, '_pptm_social_youtube', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="pptm_contact_email"><?php _e('Email', 'premierplug-talent'); ?></label></th>
                <td><input type="email" id="pptm_contact_email" name="pptm_contact_email" value="<?php echo esc_attr($email); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="pptm_contact_phone"><?php _e('Phone', 'premierplug-talent'); ?></label></th>
                <td><input type="tel" id="pptm_contact_phone" name="pptm_contact_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" /></td>
            </tr>
            <tr>
                <th><label for="pptm_website"><?php _e('Website', 'premierplug-talent'); ?></label></th>
                <td><input type="url" id="pptm_website" name="pptm_website" value="<?php echo esc_attr($website); ?>" class="regular-text" placeholder="https://" /></td>
            </tr>
            <tr>
                <th colspan="2"><strong><?php _e('Social Media', 'premierplug-talent'); ?></strong></th>
            </tr>
            <tr>
                <th><label for="pptm_social_instagram"><?php _e('Instagram', 'premierplug-talent'); ?></label></th>
                <td><input type="url" id="pptm_social_instagram" name="pptm_social_instagram" value="<?php echo esc_attr($instagram); ?>" class="regular-text" placeholder="https://instagram.com/username" /></td>
            </tr>
            <tr>
                <th><label for="pptm_social_linkedin"><?php _e('LinkedIn', 'premierplug-talent'); ?></label></th>
                <td><input type="url" id="pptm_social_linkedin" name="pptm_social_linkedin" value="<?php echo esc_attr($linkedin); ?>" class="regular-text" placeholder="https://linkedin.com/in/username" /></td>
            </tr>
            <tr>
                <th><label for="pptm_social_twitter"><?php _e('Twitter/X', 'premierplug-talent'); ?></label></th>
                <td><input type="url" id="pptm_social_twitter" name="pptm_social_twitter" value="<?php echo esc_attr($twitter); ?>" class="regular-text" placeholder="https://twitter.com/username" /></td>
            </tr>
            <tr>
                <th><label for="pptm_social_youtube"><?php _e('YouTube', 'premierplug-talent'); ?></label></th>
                <td><input type="url" id="pptm_social_youtube" name="pptm_social_youtube" value="<?php echo esc_attr($youtube); ?>" class="regular-text" placeholder="https://youtube.com/channel/..." /></td>
            </tr>
        </table>
        <?php
    }

    public function render_portfolio_meta_box($post) {
        wp_nonce_field('pptm_save_portfolio', 'pptm_portfolio_nonce');

        $portfolio_items = get_post_meta($post->ID, '_pptm_portfolio_items', true);
        if (!is_array($portfolio_items)) {
            $portfolio_items = array();
        }
        ?>
        <div id="pptm-portfolio-container">
            <div id="pptm-portfolio-items">
                <?php foreach ($portfolio_items as $index => $item) : ?>
                    <div class="pptm-portfolio-item" data-index="<?php echo $index; ?>">
                        <div class="pptm-portfolio-item-header">
                            <h4><?php echo esc_html($item['title'] ?? __('Portfolio Item', 'premierplug-talent')); ?></h4>
                            <button type="button" class="button pptm-remove-portfolio-item"><?php _e('Remove', 'premierplug-talent'); ?></button>
                        </div>
                        <table class="form-table">
                            <tr>
                                <th><label><?php _e('Type', 'premierplug-talent'); ?></label></th>
                                <td>
                                    <select name="pptm_portfolio[<?php echo $index; ?>][type]">
                                        <option value="image" <?php selected($item['type'] ?? '', 'image'); ?>><?php _e('Image', 'premierplug-talent'); ?></option>
                                        <option value="video" <?php selected($item['type'] ?? '', 'video'); ?>><?php _e('Video', 'premierplug-talent'); ?></option>
                                        <option value="audio" <?php selected($item['type'] ?? '', 'audio'); ?>><?php _e('Audio', 'premierplug-talent'); ?></option>
                                        <option value="document" <?php selected($item['type'] ?? '', 'document'); ?>><?php _e('Document', 'premierplug-talent'); ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Title', 'premierplug-talent'); ?></label></th>
                                <td><input type="text" name="pptm_portfolio[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="regular-text" /></td>
                            </tr>
                            <tr>
                                <th><label><?php _e('File URL', 'premierplug-talent'); ?></label></th>
                                <td>
                                    <input type="url" name="pptm_portfolio[<?php echo $index; ?>][url]" value="<?php echo esc_attr($item['url'] ?? ''); ?>" class="large-text" />
                                    <button type="button" class="button pptm-upload-media"><?php _e('Upload/Select Media', 'premierplug-talent'); ?></button>
                                </td>
                            </tr>
                            <tr>
                                <th><label><?php _e('Description', 'premierplug-talent'); ?></label></th>
                                <td><textarea name="pptm_portfolio[<?php echo $index; ?>][description]" rows="3" class="large-text"><?php echo esc_textarea($item['description'] ?? ''); ?></textarea></td>
                            </tr>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" id="pptm-add-portfolio-item" class="button button-primary"><?php _e('Add Portfolio Item', 'premierplug-talent'); ?></button>
        </div>
        <style>
            .pptm-portfolio-item {
                background: #f9f9f9;
                border: 1px solid #ddd;
                padding: 15px;
                margin-bottom: 15px;
            }
            .pptm-portfolio-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .pptm-portfolio-item-header h4 {
                margin: 0;
            }
        </style>
        <?php
    }

    public function render_settings_meta_box($post) {
        wp_nonce_field('pptm_save_settings', 'pptm_settings_nonce');

        $availability = get_post_meta($post->ID, '_pptm_availability_status', true);
        if (empty($availability)) {
            $availability = 'available';
        }

        $featured = get_post_meta($post->ID, '_pptm_featured', true);
        ?>
        <p>
            <label for="pptm_availability_status"><strong><?php _e('Availability Status', 'premierplug-talent'); ?></strong></label><br />
            <select id="pptm_availability_status" name="pptm_availability_status" style="width: 100%;">
                <option value="available" <?php selected($availability, 'available'); ?>><?php _e('Available', 'premierplug-talent'); ?></option>
                <option value="booked" <?php selected($availability, 'booked'); ?>><?php _e('Booked', 'premierplug-talent'); ?></option>
                <option value="unavailable" <?php selected($availability, 'unavailable'); ?>><?php _e('Unavailable', 'premierplug-talent'); ?></option>
            </select>
        </p>
        <p>
            <label>
                <input type="checkbox" name="pptm_featured" value="1" <?php checked($featured, '1'); ?> />
                <strong><?php _e('Feature this talent', 'premierplug-talent'); ?></strong>
            </label><br />
            <span class="description"><?php _e('Featured talents appear on the homepage', 'premierplug-talent'); ?></span>
        </p>
        <?php
    }

    public function render_analytics_meta_box($post) {
        $view_count = get_post_meta($post->ID, '_pptm_view_count', true);
        $inquiry_count = get_post_meta($post->ID, '_pptm_inquiry_count', true);
        ?>
        <p><strong><?php _e('Profile Views:', 'premierplug-talent'); ?></strong> <?php echo absint($view_count); ?></p>
        <p><strong><?php _e('Inquiries:', 'premierplug-talent'); ?></strong> <?php echo absint($inquiry_count); ?></p>
        <?php
    }

    public function save_meta_boxes($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['pptm_profile_nonce']) && wp_verify_nonce($_POST['pptm_profile_nonce'], 'pptm_save_profile')) {
            if (isset($_POST['pptm_headline'])) {
                update_post_meta($post_id, '_pptm_headline', sanitize_text_field($_POST['pptm_headline']));
            }
            if (isset($_POST['pptm_experience_years'])) {
                update_post_meta($post_id, '_pptm_experience_years', absint($_POST['pptm_experience_years']));
            }
        }

        if (isset($_POST['pptm_contact_nonce']) && wp_verify_nonce($_POST['pptm_contact_nonce'], 'pptm_save_contact')) {
            update_post_meta($post_id, '_pptm_contact_email', sanitize_email($_POST['pptm_contact_email'] ?? ''));
            update_post_meta($post_id, '_pptm_contact_phone', sanitize_text_field($_POST['pptm_contact_phone'] ?? ''));
            update_post_meta($post_id, '_pptm_website', esc_url_raw($_POST['pptm_website'] ?? ''));
            update_post_meta($post_id, '_pptm_social_instagram', esc_url_raw($_POST['pptm_social_instagram'] ?? ''));
            update_post_meta($post_id, '_pptm_social_linkedin', esc_url_raw($_POST['pptm_social_linkedin'] ?? ''));
            update_post_meta($post_id, '_pptm_social_twitter', esc_url_raw($_POST['pptm_social_twitter'] ?? ''));
            update_post_meta($post_id, '_pptm_social_youtube', esc_url_raw($_POST['pptm_social_youtube'] ?? ''));
        }

        if (isset($_POST['pptm_portfolio_nonce']) && wp_verify_nonce($_POST['pptm_portfolio_nonce'], 'pptm_save_portfolio')) {
            if (isset($_POST['pptm_portfolio']) && is_array($_POST['pptm_portfolio'])) {
                $portfolio_items = array();
                foreach ($_POST['pptm_portfolio'] as $item) {
                    $portfolio_items[] = array(
                        'type' => sanitize_text_field($item['type'] ?? ''),
                        'title' => sanitize_text_field($item['title'] ?? ''),
                        'url' => esc_url_raw($item['url'] ?? ''),
                        'description' => sanitize_textarea_field($item['description'] ?? ''),
                    );
                }
                update_post_meta($post_id, '_pptm_portfolio_items', $portfolio_items);
            } else {
                delete_post_meta($post_id, '_pptm_portfolio_items');
            }
        }

        if (isset($_POST['pptm_settings_nonce']) && wp_verify_nonce($_POST['pptm_settings_nonce'], 'pptm_save_settings')) {
            if (isset($_POST['pptm_availability_status'])) {
                $allowed_statuses = array('available', 'booked', 'unavailable');
                $status = sanitize_text_field($_POST['pptm_availability_status']);
                if (in_array($status, $allowed_statuses)) {
                    update_post_meta($post_id, '_pptm_availability_status', $status);
                }
            }

            $featured = isset($_POST['pptm_featured']) ? '1' : '0';
            update_post_meta($post_id, '_pptm_featured', $featured);
        }

        do_action('pptm_after_save_talent', $post_id, $post);
    }
}
