<?php
/**
 * Articles Manager Admin Class
 *
 * Admin dashboard for managing all articles
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Articles_Manager {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
        add_action('wp_ajax_pptm_sync_all_articles', array(__CLASS__, 'ajax_sync_all_articles'));
        add_action('wp_ajax_pptm_test_supabase_connection', array(__CLASS__, 'ajax_test_connection'));
    }

    /**
     * Render dashboard page
     */
    public static function render_dashboard() {
        if (!current_user_can('edit_posts')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'premierplug-talent'));
        }

        $stats = PPTM_Article_Queries::get_article_stats();
        $recent_articles = PPTM_Article_Queries::get_recent_articles(array('posts_per_page' => 10));
        ?>
        <div class="wrap pptm-articles-dashboard">
            <h1><?php esc_html_e('Articles Management', 'premierplug-talent'); ?></h1>

            <div class="pptm-dashboard-stats">
                <div class="pptm-stat-box">
                    <h3><?php echo esc_html($stats['total']); ?></h3>
                    <p><?php esc_html_e('Total Articles', 'premierplug-talent'); ?></p>
                </div>
                <div class="pptm-stat-box">
                    <h3><?php echo esc_html($stats['featured']); ?></h3>
                    <p><?php esc_html_e('Featured Articles', 'premierplug-talent'); ?></p>
                </div>
                <div class="pptm-stat-box">
                    <h3><?php echo esc_html($stats['with_talents']); ?></h3>
                    <p><?php esc_html_e('Linked to Talents', 'premierplug-talent'); ?></p>
                </div>
            </div>

            <div class="pptm-dashboard-grid">
                <div class="pptm-dashboard-main">
                    <div class="pptm-panel">
                        <h2><?php esc_html_e('Articles by Type', 'premierplug-talent'); ?></h2>
                        <table class="widefat">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Type', 'premierplug-talent'); ?></th>
                                    <th><?php esc_html_e('Count', 'premierplug-talent'); ?></th>
                                    <th><?php esc_html_e('Actions', 'premierplug-talent'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stats['by_type'] as $type => $count) : ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo esc_html(PPTM_Article_Post_Types::get_type_label($type)); ?></strong>
                                        </td>
                                        <td><?php echo esc_html($count); ?></td>
                                        <td>
                                            <a href="<?php echo esc_url(admin_url('edit.php?post_type=' . $type)); ?>" class="button button-small">
                                                <?php esc_html_e('View All', 'premierplug-talent'); ?>
                                            </a>
                                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=' . $type)); ?>" class="button button-primary button-small">
                                                <?php esc_html_e('Add New', 'premierplug-talent'); ?>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="pptm-panel">
                        <h2><?php esc_html_e('Recent Articles', 'premierplug-talent'); ?></h2>
                        <?php if ($recent_articles->have_posts()) : ?>
                            <table class="widefat striped">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Title', 'premierplug-talent'); ?></th>
                                        <th><?php esc_html_e('Type', 'premierplug-talent'); ?></th>
                                        <th><?php esc_html_e('Date', 'premierplug-talent'); ?></th>
                                        <th><?php esc_html_e('Talents', 'premierplug-talent'); ?></th>
                                        <th><?php esc_html_e('Actions', 'premierplug-talent'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($recent_articles->have_posts()) : $recent_articles->the_post(); ?>
                                        <?php
                                        $talent_count = PPTM_Article_Relationships::get_article_talent_count(get_the_ID());
                                        $is_featured = get_post_meta(get_the_ID(), '_pptm_is_featured', true);
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php the_title(); ?></strong>
                                                <?php if ($is_featured === '1') : ?>
                                                    <span class="dashicons dashicons-star-filled" style="color: #ffb900;" title="Featured"></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo esc_html(PPTM_Article_Post_Types::get_type_label(get_post_type())); ?></td>
                                            <td><?php echo get_the_date(); ?></td>
                                            <td><?php echo esc_html($talent_count); ?> <?php esc_html_e('talents', 'premierplug-talent'); ?></td>
                                            <td>
                                                <a href="<?php echo esc_url(get_edit_post_link()); ?>" class="button button-small">
                                                    <?php esc_html_e('Edit', 'premierplug-talent'); ?>
                                                </a>
                                                <a href="<?php the_permalink(); ?>" class="button button-small" target="_blank">
                                                    <?php esc_html_e('View', 'premierplug-talent'); ?>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                            <?php wp_reset_postdata(); ?>
                        <?php else : ?>
                            <p><?php esc_html_e('No articles found.', 'premierplug-talent'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pptm-dashboard-sidebar">
                    <div class="pptm-panel">
                        <h3><?php esc_html_e('Quick Actions', 'premierplug-talent'); ?></h3>
                        <div class="pptm-actions">
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=press_release')); ?>" class="button button-primary button-large">
                                <?php esc_html_e('Add Press Release', 'premierplug-talent'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=blog_article')); ?>" class="button button-primary button-large">
                                <?php esc_html_e('Add Blog Article', 'premierplug-talent'); ?>
                            </a>
                            <a href="<?php echo esc_url(admin_url('post-new.php?post_type=award')); ?>" class="button button-primary button-large">
                                <?php esc_html_e('Add Award', 'premierplug-talent'); ?>
                            </a>
                        </div>
                    </div>

                    <?php if (PPTM_Article_Supabase::is_configured()) : ?>
                        <div class="pptm-panel">
                            <h3><?php esc_html_e('Supabase Sync', 'premierplug-talent'); ?></h3>
                            <p><?php esc_html_e('Sync all articles and relationships to Supabase database.', 'premierplug-talent'); ?></p>
                            <button type="button" class="button button-secondary" id="pptm-test-connection">
                                <?php esc_html_e('Test Connection', 'premierplug-talent'); ?>
                            </button>
                            <button type="button" class="button" id="pptm-sync-all">
                                <?php esc_html_e('Sync All Articles', 'premierplug-talent'); ?>
                            </button>
                            <div id="pptm-sync-status" class="pptm-sync-status"></div>
                        </div>
                    <?php else : ?>
                        <div class="pptm-panel pptm-notice-warning">
                            <h3><?php esc_html_e('Supabase Not Configured', 'premierplug-talent'); ?></h3>
                            <p><?php esc_html_e('Add Supabase credentials to wp-config.php to enable real-time sync.', 'premierplug-talent'); ?></p>
                            <pre style="background: #f5f5f5; padding: 10px; border-radius: 3px; font-size: 12px;">
define('SUPABASE_URL', 'your-url');
define('SUPABASE_KEY', 'your-key');</pre>
                        </div>
                    <?php endif; ?>

                    <div class="pptm-panel">
                        <h3><?php esc_html_e('Documentation', 'premierplug-talent'); ?></h3>
                        <ul class="pptm-doc-links">
                            <li><a href="#" target="_blank"><?php esc_html_e('How to Link Talents', 'premierplug-talent'); ?></a></li>
                            <li><a href="#" target="_blank"><?php esc_html_e('Using Shortcodes', 'premierplug-talent'); ?></a></li>
                            <li><a href="#" target="_blank"><?php esc_html_e('Supabase Setup', 'premierplug-talent'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <style>
        .pptm-articles-dashboard { margin: 20px 0; }
        .pptm-dashboard-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin: 20px 0;
        }
        .pptm-stat-box {
            background: #fff;
            padding: 20px;
            border-left: 4px solid #BC1F2F;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .pptm-stat-box h3 {
            margin: 0 0 5px 0;
            font-size: 32px;
            color: #BC1F2F;
        }
        .pptm-stat-box p {
            margin: 0;
            color: #666;
        }
        .pptm-dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .pptm-panel {
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .pptm-panel h2, .pptm-panel h3 {
            margin-top: 0;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }
        .pptm-actions { display: flex; flex-direction: column; gap: 10px; }
        .pptm-actions .button { justify-content: center; }
        .pptm-sync-status {
            margin-top: 15px;
            padding: 10px;
            border-radius: 3px;
            display: none;
        }
        .pptm-sync-status.success {
            background: #d4edda;
            color: #155724;
            display: block;
        }
        .pptm-sync-status.error {
            background: #f8d7da;
            color: #721c24;
            display: block;
        }
        .pptm-notice-warning {
            border-left: 4px solid #ffb900;
        }
        .pptm-doc-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .pptm-doc-links li {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .pptm-doc-links li:last-child { border-bottom: none; }
        </style>

        <script>
        jQuery(document).ready(function($) {
            $('#pptm-test-connection').on('click', function() {
                var $button = $(this);
                $button.prop('disabled', true).text('Testing...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: { action: 'pptm_test_supabase_connection' },
                    success: function(response) {
                        var $status = $('#pptm-sync-status');
                        $status.removeClass('success error');

                        if (response.success) {
                            $status.addClass('success').text(response.data.message).show();
                        } else {
                            $status.addClass('error').text(response.data).show();
                        }

                        $button.prop('disabled', false).text('Test Connection');
                    },
                    error: function() {
                        $('#pptm-sync-status')
                            .removeClass('success')
                            .addClass('error')
                            .text('Connection failed')
                            .show();
                        $button.prop('disabled', false).text('Test Connection');
                    }
                });
            });

            $('#pptm-sync-all').on('click', function() {
                if (!confirm('This will sync all articles to Supabase. Continue?')) {
                    return;
                }

                var $button = $(this);
                $button.prop('disabled', true).text('Syncing...');

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: { action: 'pptm_sync_all_articles' },
                    success: function(response) {
                        var $status = $('#pptm-sync-status');
                        $status.removeClass('success error');

                        if (response.success) {
                            $status.addClass('success')
                                .text('Synced: ' + response.data.synced + ' articles, Failed: ' + response.data.failed)
                                .show();
                        } else {
                            $status.addClass('error').text('Sync failed').show();
                        }

                        $button.prop('disabled', false).text('Sync All Articles');
                    },
                    error: function() {
                        $('#pptm-sync-status')
                            .removeClass('success')
                            .addClass('error')
                            .text('Sync failed')
                            .show();
                        $button.prop('disabled', false).text('Sync All Articles');
                    }
                });
            });
        });
        </script>
        <?php
    }

    /**
     * Enqueue admin assets
     */
    public static function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_talent-articles') {
            return;
        }

        wp_enqueue_style('pptm-admin', plugins_url('assets/css/admin.css', dirname(__FILE__)), array(), '1.1.0');
    }

    /**
     * AJAX sync all articles
     */
    public static function ajax_sync_all_articles() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        $result = PPTM_Article_Supabase::batch_sync_articles();

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result['message']);
        }
    }

    /**
     * AJAX test Supabase connection
     */
    public static function ajax_test_connection() {
        if (!current_user_can('edit_posts')) {
            wp_send_json_error('Unauthorized');
        }

        $result = PPTM_Article_Supabase::test_connection();

        if ($result['success']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result['message']);
        }
    }
}
