<?php

class PremierPlug_Custom_Post_Type_Manager {

    private $option_name = 'premierplug_custom_post_types';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'), 100);
        add_action('admin_post_save_custom_post_type', array($this, 'save_custom_post_type'));
        add_action('admin_post_delete_custom_post_type', array($this, 'delete_custom_post_type'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('init', array($this, 'register_dynamic_post_types'), 5);
        add_action('init', array($this, 'register_dynamic_taxonomies'), 6);
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=talent',
            'Custom Post Types',
            'Custom Types',
            'manage_options',
            'premierplug-custom-types',
            array($this, 'render_admin_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ($hook === 'talent_page_premierplug-custom-types') {
            wp_enqueue_style('premierplug-custom-types-admin', plugins_url('assets/css/custom-types-admin.css', dirname(__FILE__)));
            wp_enqueue_script('premierplug-custom-types-admin', plugins_url('assets/js/custom-types-admin.js', dirname(__FILE__)), array('jquery'), '1.0', true);
        }
    }

    public function get_custom_post_types() {
        $custom_types = get_option($this->option_name, array());

        if (empty($custom_types)) {
            $custom_types = $this->get_default_types();
            update_option($this->option_name, $custom_types);
        }

        return $custom_types;
    }

    private function get_default_types() {
        return array(
            'articles' => array(
                'type' => 'article_type',
                'enabled' => true,
                'items' => array(
                    'press_release' => array(
                        'id' => 'press_release',
                        'label' => 'Press Releases',
                        'singular' => 'Press Release',
                        'slug' => 'press-releases',
                        'icon' => 'dashicons-megaphone',
                        'enabled' => true
                    ),
                    'industry_insight' => array(
                        'id' => 'industry_insight',
                        'label' => 'Industry Insights',
                        'singular' => 'Industry Insight',
                        'slug' => 'industry-insights',
                        'icon' => 'dashicons-lightbulb',
                        'enabled' => true
                    ),
                    'thought_leadership' => array(
                        'id' => 'thought_leadership',
                        'label' => 'Thought Leadership',
                        'singular' => 'Thought Leadership',
                        'slug' => 'thought-leadership',
                        'icon' => 'dashicons-awards',
                        'enabled' => true
                    ),
                    'company_news' => array(
                        'id' => 'company_news',
                        'label' => 'Company News',
                        'singular' => 'Company News',
                        'slug' => 'company-news',
                        'icon' => 'dashicons-admin-home',
                        'enabled' => true
                    ),
                    'case_study' => array(
                        'id' => 'case_study',
                        'label' => 'Case Studies',
                        'singular' => 'Case Study',
                        'slug' => 'case-studies',
                        'icon' => 'dashicons-portfolio',
                        'enabled' => true
                    )
                )
            ),
            'talent_types' => array(
                'type' => 'talent_type',
                'enabled' => true,
                'items' => array()
            ),
            'service_types' => array(
                'type' => 'service_type',
                'enabled' => true,
                'items' => array()
            )
        );
    }

    public function register_dynamic_post_types() {
        $custom_types = $this->get_custom_post_types();

        if (isset($custom_types['articles']) && $custom_types['articles']['enabled']) {
            foreach ($custom_types['articles']['items'] as $type_id => $type_data) {
                if ($type_data['enabled']) {
                    $this->register_article_type($type_data);
                }
            }
        }
    }

    public function register_dynamic_taxonomies() {
        $custom_types = $this->get_custom_post_types();

        if (isset($custom_types['talent_types']) && $custom_types['talent_types']['enabled']) {
            foreach ($custom_types['talent_types']['items'] as $type_id => $type_data) {
                if ($type_data['enabled']) {
                    $this->register_talent_taxonomy($type_data);
                }
            }
        }

        if (isset($custom_types['service_types']) && $custom_types['service_types']['enabled']) {
            foreach ($custom_types['service_types']['items'] as $type_id => $type_data) {
                if ($type_data['enabled']) {
                    $this->register_service_taxonomy($type_data);
                }
            }
        }
    }

    private function register_article_type($type_data) {
        $labels = array(
            'name'               => $type_data['label'],
            'singular_name'      => $type_data['singular'],
            'menu_name'          => $type_data['label'],
            'add_new'            => 'Add New',
            'add_new_item'       => 'Add New ' . $type_data['singular'],
            'edit_item'          => 'Edit ' . $type_data['singular'],
            'new_item'           => 'New ' . $type_data['singular'],
            'view_item'          => 'View ' . $type_data['singular'],
            'search_items'       => 'Search ' . $type_data['label'],
            'not_found'          => 'No ' . strtolower($type_data['label']) . ' found',
            'not_found_in_trash' => 'No ' . strtolower($type_data['label']) . ' found in trash',
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=talent',
            'query_var'          => true,
            'rewrite'            => array('slug' => $type_data['slug']),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'menu_icon'          => $type_data['icon'],
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'show_in_rest'       => true,
        );

        register_post_type('article_' . $type_data['id'], $args);
    }

    private function register_talent_taxonomy($type_data) {
        $labels = array(
            'name'              => $type_data['label'],
            'singular_name'     => $type_data['singular'],
            'search_items'      => 'Search ' . $type_data['label'],
            'all_items'         => 'All ' . $type_data['label'],
            'parent_item'       => 'Parent ' . $type_data['singular'],
            'parent_item_colon' => 'Parent ' . $type_data['singular'] . ':',
            'edit_item'         => 'Edit ' . $type_data['singular'],
            'update_item'       => 'Update ' . $type_data['singular'],
            'add_new_item'      => 'Add New ' . $type_data['singular'],
            'new_item_name'     => 'New ' . $type_data['singular'] . ' Name',
            'menu_name'         => $type_data['label'],
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => $type_data['slug']),
            'show_in_rest'      => true,
        );

        register_taxonomy('talent_' . $type_data['id'], array('talent'), $args);
    }

    private function register_service_taxonomy($type_data) {
        $labels = array(
            'name'              => $type_data['label'],
            'singular_name'     => $type_data['singular'],
            'search_items'      => 'Search ' . $type_data['label'],
            'all_items'         => 'All ' . $type_data['label'],
            'parent_item'       => 'Parent ' . $type_data['singular'],
            'parent_item_colon' => 'Parent ' . $type_data['singular'] . ':',
            'edit_item'         => 'Edit ' . $type_data['singular'],
            'update_item'       => 'Update ' . $type_data['singular'],
            'add_new_item'      => 'Add New ' . $type_data['singular'],
            'new_item_name'     => 'New ' . $type_data['singular'] . ' Name',
            'menu_name'         => $type_data['label'],
        );

        $args = array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => $type_data['slug']),
            'show_in_rest'      => true,
        );

        register_taxonomy('service_' . $type_data['id'], array('talent'), $args);
    }

    public function save_custom_post_type() {
        check_admin_referer('save_custom_post_type');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $category = sanitize_text_field($_POST['category']);
        $type_id = sanitize_title($_POST['type_id']);
        $label = sanitize_text_field($_POST['label']);
        $singular = sanitize_text_field($_POST['singular']);
        $slug = sanitize_title($_POST['slug']);
        $icon = sanitize_text_field($_POST['icon']);

        $custom_types = $this->get_custom_post_types();

        $custom_types[$category]['items'][$type_id] = array(
            'id' => $type_id,
            'label' => $label,
            'singular' => $singular,
            'slug' => $slug,
            'icon' => $icon,
            'enabled' => true
        );

        update_option($this->option_name, $custom_types);

        flush_rewrite_rules();

        wp_redirect(admin_url('edit.php?post_type=talent&page=premierplug-custom-types&saved=1'));
        exit;
    }

    public function delete_custom_post_type() {
        check_admin_referer('delete_custom_post_type');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $category = sanitize_text_field($_POST['category']);
        $type_id = sanitize_text_field($_POST['type_id']);

        $custom_types = $this->get_custom_post_types();

        if (isset($custom_types[$category]['items'][$type_id])) {
            unset($custom_types[$category]['items'][$type_id]);
            update_option($this->option_name, $custom_types);
        }

        flush_rewrite_rules();

        wp_redirect(admin_url('edit.php?post_type=talent&page=premierplug-custom-types&deleted=1'));
        exit;
    }

    public function render_admin_page() {
        $custom_types = $this->get_custom_post_types();
        $saved = isset($_GET['saved']);
        $deleted = isset($_GET['deleted']);
        ?>
        <div class="wrap premierplug-custom-types">
            <h1>Custom Post Types Manager</h1>

            <?php if ($saved): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Custom type saved successfully!</p>
                </div>
            <?php endif; ?>

            <?php if ($deleted): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Custom type deleted successfully!</p>
                </div>
            <?php endif; ?>

            <p class="description">Manage article types, talent categories, service types, and more.</p>

            <div class="custom-types-container">
                <div class="custom-types-section">
                    <h2>Article Types</h2>
                    <p class="description">Create different types of articles (press releases, case studies, etc.)</p>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Slug</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($custom_types['articles']['items'] as $type): ?>
                                <tr>
                                    <td><?php echo esc_html($type['label']); ?></td>
                                    <td><code><?php echo esc_html($type['slug']); ?></code></td>
                                    <td><span class="dashicons <?php echo esc_attr($type['icon']); ?>"></span></td>
                                    <td><?php echo $type['enabled'] ? '<span class="status-enabled">Enabled</span>' : '<span class="status-disabled">Disabled</span>'; ?></td>
                                    <td>
                                        <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                                            <input type="hidden" name="action" value="delete_custom_post_type">
                                            <input type="hidden" name="category" value="articles">
                                            <input type="hidden" name="type_id" value="<?php echo esc_attr($type['id']); ?>">
                                            <?php wp_nonce_field('delete_custom_post_type'); ?>
                                            <button type="submit" class="button button-small button-link-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button class="button button-primary add-new-type" data-category="articles">Add New Article Type</button>
                </div>

                <div class="custom-types-section">
                    <h2>Talent Types</h2>
                    <p class="description">Create categories for organizing talent (actors, musicians, speakers, etc.)</p>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Slug</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($custom_types['talent_types']['items'])): ?>
                                <tr><td colspan="5">No talent types yet. Click "Add New Talent Type" to create one.</td></tr>
                            <?php else: ?>
                                <?php foreach ($custom_types['talent_types']['items'] as $type): ?>
                                    <tr>
                                        <td><?php echo esc_html($type['label']); ?></td>
                                        <td><code><?php echo esc_html($type['slug']); ?></code></td>
                                        <td><span class="dashicons <?php echo esc_attr($type['icon']); ?>"></span></td>
                                        <td><?php echo $type['enabled'] ? '<span class="status-enabled">Enabled</span>' : '<span class="status-disabled">Disabled</span>'; ?></td>
                                        <td>
                                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_custom_post_type">
                                                <input type="hidden" name="category" value="talent_types">
                                                <input type="hidden" name="type_id" value="<?php echo esc_attr($type['id']); ?>">
                                                <?php wp_nonce_field('delete_custom_post_type'); ?>
                                                <button type="submit" class="button button-small button-link-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button class="button button-primary add-new-type" data-category="talent_types">Add New Talent Type</button>
                </div>

                <div class="custom-types-section">
                    <h2>Service Types</h2>
                    <p class="description">Create categories for services you offer</p>

                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Slug</th>
                                <th>Icon</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($custom_types['service_types']['items'])): ?>
                                <tr><td colspan="5">No service types yet. Click "Add New Service Type" to create one.</td></tr>
                            <?php else: ?>
                                <?php foreach ($custom_types['service_types']['items'] as $type): ?>
                                    <tr>
                                        <td><?php echo esc_html($type['label']); ?></td>
                                        <td><code><?php echo esc_html($type['slug']); ?></code></td>
                                        <td><span class="dashicons <?php echo esc_attr($type['icon']); ?>"></span></td>
                                        <td><?php echo $type['enabled'] ? '<span class="status-enabled">Enabled</span>' : '<span class="status-disabled">Disabled</span>'; ?></td>
                                        <td>
                                            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;">
                                                <input type="hidden" name="action" value="delete_custom_post_type">
                                                <input type="hidden" name="category" value="service_types">
                                                <input type="hidden" name="type_id" value="<?php echo esc_attr($type['id']); ?>">
                                                <?php wp_nonce_field('delete_custom_post_type'); ?>
                                                <button type="submit" class="button button-small button-link-delete" onclick="return confirm('Are you sure?');">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <button class="button button-primary add-new-type" data-category="service_types">Add New Service Type</button>
                </div>
            </div>

            <div id="add-type-modal" class="modal" style="display:none;">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Add New Custom Type</h2>
                    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                        <input type="hidden" name="action" value="save_custom_post_type">
                        <input type="hidden" name="category" id="modal-category" value="">
                        <?php wp_nonce_field('save_custom_post_type'); ?>

                        <table class="form-table">
                            <tr>
                                <th><label for="type_id">ID (internal)</label></th>
                                <td><input type="text" name="type_id" id="type_id" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th><label for="label">Plural Label</label></th>
                                <td><input type="text" name="label" id="label" class="regular-text" required placeholder="e.g., Case Studies"></td>
                            </tr>
                            <tr>
                                <th><label for="singular">Singular Label</label></th>
                                <td><input type="text" name="singular" id="singular" class="regular-text" required placeholder="e.g., Case Study"></td>
                            </tr>
                            <tr>
                                <th><label for="slug">URL Slug</label></th>
                                <td><input type="text" name="slug" id="slug" class="regular-text" required placeholder="e.g., case-studies"></td>
                            </tr>
                            <tr>
                                <th><label for="icon">Dashicon</label></th>
                                <td>
                                    <input type="text" name="icon" id="icon" class="regular-text" required value="dashicons-admin-post">
                                    <p class="description">Use a <a href="https://developer.wordpress.org/resource/dashicons/" target="_blank">Dashicons</a> class (e.g., dashicons-star-filled)</p>
                                </td>
                            </tr>
                        </table>

                        <p class="submit">
                            <button type="submit" class="button button-primary">Save Custom Type</button>
                            <button type="button" class="button cancel-modal">Cancel</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
        <?php
    }
}
