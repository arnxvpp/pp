<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Admin {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_page'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_filter('manage_talent_posts_columns', array(__CLASS__, 'set_custom_columns'));
        add_action('manage_talent_posts_custom_column', array(__CLASS__, 'custom_column_content'), 10, 2);
    }

    public static function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=talent',
            __('Talent Settings', 'premierplug-talent'),
            __('Settings', 'premierplug-talent'),
            'manage_options',
            'talent-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }

    public static function register_settings() {
        register_setting('pptm_settings_group', 'pptm_supabase_url');
        register_setting('pptm_settings_group', 'pptm_supabase_key');
        register_setting('pptm_settings_group', 'pptm_enable_search');
        register_setting('pptm_settings_group', 'pptm_enable_filters');
    }

    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Talent Management Settings', 'premierplug-talent'); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields('pptm_settings_group'); ?>
                <?php do_settings_sections('pptm_settings_group'); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="pptm_supabase_url"><?php _e('Supabase URL', 'premierplug-talent'); ?></label></th>
                        <td>
                            <input type="url" id="pptm_supabase_url" name="pptm_supabase_url" value="<?php echo esc_attr(get_option('pptm_supabase_url')); ?>" class="regular-text" />
                            <p class="description"><?php _e('Your Supabase project URL', 'premierplug-talent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="pptm_supabase_key"><?php _e('Supabase Anon Key', 'premierplug-talent'); ?></label></th>
                        <td>
                            <input type="text" id="pptm_supabase_key" name="pptm_supabase_key" value="<?php echo esc_attr(get_option('pptm_supabase_key')); ?>>" class="regular-text" />
                            <p class="description"><?php _e('Your Supabase anon/public key', 'premierplug-talent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enable Search', 'premierplug-talent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="pptm_enable_search" value="1" <?php checked(1, get_option('pptm_enable_search'), true); ?> />
                                <?php _e('Enable talent search functionality', 'premierplug-talent'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php _e('Enable Filters', 'premierplug-talent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="pptm_enable_filters" value="1" <?php checked(1, get_option('pptm_enable_filters'), true); ?> />
                                <?php _e('Enable category and skill filters', 'premierplug-talent'); ?>
                            </label>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>

            <hr>

            <h2><?php _e('Shortcodes', 'premierplug-talent'); ?></h2>
            <p><?php _e('Use these shortcodes to display talents:', 'premierplug-talent'); ?></p>
            <ul>
                <li><code>[talent_grid category="motion-pictures" limit="12" columns="3"]</code></li>
                <li><code>[talent_list category="speakers"]</code></li>
                <li><code>[talent_single id="123"]</code></li>
                <li><code>[talent_search]</code></li>
            </ul>
        </div>
        <?php
    }

    public static function set_custom_columns($columns) {
        $new_columns = array(
            'cb' => $columns['cb'],
            'featured_image' => __('Photo', 'premierplug-talent'),
            'title' => $columns['title'],
            'talent_category' => $columns['taxonomy-talent_category'],
            'talent_skill' => $columns['taxonomy-talent_skill'],
            'talent_contact' => __('Contact', 'premierplug-talent'),
            'date' => $columns['date'],
        );
        return $new_columns;
    }

    public static function custom_column_content($column, $post_id) {
        switch ($column) {
            case 'featured_image':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50));
                } else {
                    echo '—';
                }
                break;

            case 'talent_contact':
                $email = get_post_meta($post_id, '_talent_email', true);
                if ($email) {
                    echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
                } else {
                    echo '—';
                }
                break;
        }
    }
}
