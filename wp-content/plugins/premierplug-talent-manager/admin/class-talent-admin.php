<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Admin {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_notices', array($this, 'admin_notices'));
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=pp_talent',
            __('Talent Settings', 'premierplug-talent'),
            __('Settings', 'premierplug-talent'),
            'manage_options',
            'pptm-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'edit.php?post_type=pp_talent',
            __('Talent Analytics', 'premierplug-talent'),
            __('Analytics', 'premierplug-talent'),
            'manage_options',
            'pptm-analytics',
            array($this, 'render_analytics_page')
        );

        add_submenu_page(
            'edit.php?post_type=pp_talent',
            __('Import Talents', 'premierplug-talent'),
            __('Import', 'premierplug-talent'),
            'manage_options',
            'pptm-import',
            array($this, 'render_import_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'pp_talent') === false && strpos($hook, 'pptm-') === false) {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');

        wp_enqueue_script(
            'pptm-admin',
            PPTM_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            PPTM_VERSION,
            true
        );

        wp_enqueue_style(
            'pptm-admin',
            PPTM_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            PPTM_VERSION
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['pptm_settings_submit'])) {
            check_admin_referer('pptm_settings');

            update_option('pptm_talents_per_page', absint($_POST['talents_per_page'] ?? 12));
            update_option('pptm_enable_analytics', isset($_POST['enable_analytics']));
            update_option('pptm_cache_duration', absint($_POST['cache_duration'] ?? 900));
            update_option('pptm_sync_enabled', isset($_POST['sync_enabled']));

            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'premierplug-talent') . '</p></div>';
        }

        $talents_per_page = get_option('pptm_talents_per_page', 12);
        $enable_analytics = get_option('pptm_enable_analytics', true);
        $cache_duration = get_option('pptm_cache_duration', 900);
        $sync_enabled = get_option('pptm_sync_enabled', true);

        $supabase = PPTM_Supabase_Client::get_instance();
        $supabase_configured = $supabase->is_configured();
        ?>
        <div class="wrap">
            <h1><?php _e('Talent Manager Settings', 'premierplug-talent'); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field('pptm_settings'); ?>

                <table class="form-table">
                    <tr>
                        <th><label for="talents_per_page"><?php _e('Talents Per Page', 'premierplug-talent'); ?></label></th>
                        <td>
                            <input type="number" id="talents_per_page" name="talents_per_page" value="<?php echo esc_attr($talents_per_page); ?>" min="1" max="100" />
                            <p class="description"><?php _e('Number of talents to display per page on the archive', 'premierplug-talent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Enable Analytics', 'premierplug-talent'); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="enable_analytics" value="1" <?php checked($enable_analytics); ?> />
                                <?php _e('Track talent profile views and inquiries', 'premierplug-talent'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th><label for="cache_duration"><?php _e('Cache Duration (seconds)', 'premierplug-talent'); ?></label></th>
                        <td>
                            <input type="number" id="cache_duration" name="cache_duration" value="<?php echo esc_attr($cache_duration); ?>" min="0" step="60" />
                            <p class="description"><?php _e('How long to cache Supabase queries. 0 = no caching', 'premierplug-talent'); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th><?php _e('Supabase Sync', 'premierplug-talent'); ?></th>
                        <td>
                            <?php if ($supabase_configured) : ?>
                                <label>
                                    <input type="checkbox" name="sync_enabled" value="1" <?php checked($sync_enabled); ?> />
                                    <?php _e('Enable automatic sync with Supabase', 'premierplug-talent'); ?>
                                </label>
                                <p class="description" style="color: green;"><?php _e('✓ Supabase is configured and connected', 'premierplug-talent'); ?></p>
                            <?php else : ?>
                                <p class="description" style="color: red;"><?php _e('✗ Supabase is not configured. Please check your .env file.', 'premierplug-talent'); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <?php submit_button(__('Save Settings', 'premierplug-talent'), 'primary', 'pptm_settings_submit'); ?>
            </form>
        </div>
        <?php
    }

    public function render_analytics_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Talent Analytics', 'premierplug-talent'); ?></h1>

            <div class="pptm-analytics-dashboard">
                <?php
                $total_talents = wp_count_posts('pp_talent')->publish;
                $featured_count = count(get_posts(array(
                    'post_type' => 'pp_talent',
                    'post_status' => 'publish',
                    'meta_key' => '_pptm_featured',
                    'meta_value' => '1',
                    'fields' => 'ids',
                    'posts_per_page' => -1,
                )));

                global $wpdb;
                $total_views = $wpdb->get_var("
                    SELECT SUM(meta_value)
                    FROM {$wpdb->postmeta}
                    WHERE meta_key = '_pptm_view_count'
                ");

                $total_inquiries = $wpdb->get_var("
                    SELECT SUM(meta_value)
                    FROM {$wpdb->postmeta}
                    WHERE meta_key = '_pptm_inquiry_count'
                ");
                ?>

                <div class="pptm-stat-cards">
                    <div class="pptm-stat-card">
                        <h3><?php echo absint($total_talents); ?></h3>
                        <p><?php _e('Total Talents', 'premierplug-talent'); ?></p>
                    </div>
                    <div class="pptm-stat-card">
                        <h3><?php echo absint($featured_count); ?></h3>
                        <p><?php _e('Featured Talents', 'premierplug-talent'); ?></p>
                    </div>
                    <div class="pptm-stat-card">
                        <h3><?php echo absint($total_views); ?></h3>
                        <p><?php _e('Total Profile Views', 'premierplug-talent'); ?></p>
                    </div>
                    <div class="pptm-stat-card">
                        <h3><?php echo absint($total_inquiries); ?></h3>
                        <p><?php _e('Total Inquiries', 'premierplug-talent'); ?></p>
                    </div>
                </div>

                <h2><?php _e('Top Viewed Talents', 'premierplug-talent'); ?></h2>
                <?php
                $top_talents = get_posts(array(
                    'post_type' => 'pp_talent',
                    'post_status' => 'publish',
                    'posts_per_page' => 10,
                    'meta_key' => '_pptm_view_count',
                    'orderby' => 'meta_value_num',
                    'order' => 'DESC',
                ));

                if ($top_talents) {
                    echo '<table class="wp-list-table widefat fixed striped">';
                    echo '<thead><tr><th>Talent</th><th>Views</th><th>Inquiries</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($top_talents as $talent) {
                        $views = get_post_meta($talent->ID, '_pptm_view_count', true);
                        $inquiries = get_post_meta($talent->ID, '_pptm_inquiry_count', true);
                        echo '<tr>';
                        echo '<td><a href="' . get_edit_post_link($talent->ID) . '">' . esc_html($talent->post_title) . '</a></td>';
                        echo '<td>' . absint($views) . '</td>';
                        echo '<td>' . absint($inquiries) . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                }
                ?>
            </div>
        </div>
        <?php
    }

    public function render_import_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php _e('Import Talents', 'premierplug-talent'); ?></h1>
            <p><?php _e('CSV import functionality coming soon...', 'premierplug-talent'); ?></p>
        </div>
        <?php
    }

    public function admin_notices() {
        $supabase = PPTM_Supabase_Client::get_instance();
        if (!$supabase->is_configured()) {
            $screen = get_current_screen();
            if ($screen && strpos($screen->id, 'pp_talent') !== false) {
                echo '<div class="notice notice-warning"><p>';
                echo __('Supabase is not configured. Talent data sync is disabled. Please configure your Supabase credentials in the .env file.', 'premierplug-talent');
                echo '</p></div>';
            }
        }
    }
}
