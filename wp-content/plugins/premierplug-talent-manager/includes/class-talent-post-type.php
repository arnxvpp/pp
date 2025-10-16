<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_filter('manage_pp_talent_posts_columns', array($this, 'add_custom_columns'));
        add_action('manage_pp_talent_posts_custom_column', array($this, 'render_custom_columns'), 10, 2);
        add_filter('manage_edit-pp_talent_sortable_columns', array($this, 'sortable_columns'));
    }

    public function register_post_type() {
        $labels = array(
            'name' => _x('Talents', 'Post Type General Name', 'premierplug-talent'),
            'singular_name' => _x('Talent', 'Post Type Singular Name', 'premierplug-talent'),
            'menu_name' => __('Talents', 'premierplug-talent'),
            'name_admin_bar' => __('Talent', 'premierplug-talent'),
            'archives' => __('Talent Archives', 'premierplug-talent'),
            'attributes' => __('Talent Attributes', 'premierplug-talent'),
            'parent_item_colon' => __('Parent Talent:', 'premierplug-talent'),
            'all_items' => __('All Talents', 'premierplug-talent'),
            'add_new_item' => __('Add New Talent', 'premierplug-talent'),
            'add_new' => __('Add New', 'premierplug-talent'),
            'new_item' => __('New Talent', 'premierplug-talent'),
            'edit_item' => __('Edit Talent', 'premierplug-talent'),
            'update_item' => __('Update Talent', 'premierplug-talent'),
            'view_item' => __('View Talent', 'premierplug-talent'),
            'view_items' => __('View Talents', 'premierplug-talent'),
            'search_items' => __('Search Talent', 'premierplug-talent'),
            'not_found' => __('Not found', 'premierplug-talent'),
            'not_found_in_trash' => __('Not found in Trash', 'premierplug-talent'),
            'featured_image' => __('Profile Image', 'premierplug-talent'),
            'set_featured_image' => __('Set profile image', 'premierplug-talent'),
            'remove_featured_image' => __('Remove profile image', 'premierplug-talent'),
            'use_featured_image' => __('Use as profile image', 'premierplug-talent'),
            'insert_into_item' => __('Insert into talent', 'premierplug-talent'),
            'uploaded_to_this_item' => __('Uploaded to this talent', 'premierplug-talent'),
            'items_list' => __('Talents list', 'premierplug-talent'),
            'items_list_navigation' => __('Talents list navigation', 'premierplug-talent'),
            'filter_items_list' => __('Filter talents list', 'premierplug-talent'),
        );

        $args = array(
            'label' => __('Talent', 'premierplug-talent'),
            'description' => __('Talent profiles for PremierPlug', 'premierplug-talent'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions'),
            'taxonomies' => array('talent_segment', 'talent_skill'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-groups',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => 'talent-roster',
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => 'talent',
                'with_front' => false,
            ),
        );

        register_post_type('pp_talent', $args);
    }

    public function add_custom_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumbnail'] = __('Photo', 'premierplug-talent');
        $new_columns['title'] = $columns['title'];
        $new_columns['segment'] = __('Segment', 'premierplug-talent');
        $new_columns['skills'] = __('Skills', 'premierplug-talent');
        $new_columns['availability'] = __('Availability', 'premierplug-talent');
        $new_columns['featured'] = __('Featured', 'premierplug-talent');
        $new_columns['views'] = __('Views', 'premierplug-talent');
        $new_columns['date'] = $columns['date'];

        return $new_columns;
    }

    public function render_custom_columns($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50), array('style' => 'border-radius: 50%;'));
                } else {
                    echo '<span class="dashicons dashicons-admin-users" style="font-size: 50px; color: #ccc;"></span>';
                }
                break;

            case 'segment':
                $terms = get_the_terms($post_id, 'talent_segment');
                if ($terms && !is_wp_error($terms)) {
                    $segment_names = array();
                    foreach ($terms as $term) {
                        $segment_names[] = esc_html($term->name);
                    }
                    echo implode(', ', $segment_names);
                } else {
                    echo '—';
                }
                break;

            case 'skills':
                $terms = get_the_terms($post_id, 'talent_skill');
                if ($terms && !is_wp_error($terms)) {
                    $skill_count = count($terms);
                    if ($skill_count > 3) {
                        $first_three = array_slice($terms, 0, 3);
                        $names = array_map(function($term) {
                            return esc_html($term->name);
                        }, $first_three);
                        echo implode(', ', $names) . ' +' . ($skill_count - 3);
                    } else {
                        $names = array_map(function($term) {
                            return esc_html($term->name);
                        }, $terms);
                        echo implode(', ', $names);
                    }
                } else {
                    echo '—';
                }
                break;

            case 'availability':
                $availability = get_post_meta($post_id, '_pptm_availability_status', true);
                $status_labels = array(
                    'available' => '<span style="color: #46b450;">●</span> Available',
                    'booked' => '<span style="color: #ffb900;">●</span> Booked',
                    'unavailable' => '<span style="color: #dc3232;">●</span> Unavailable',
                );
                echo isset($status_labels[$availability]) ? $status_labels[$availability] : '—';
                break;

            case 'featured':
                $featured = get_post_meta($post_id, '_pptm_featured', true);
                if ($featured) {
                    echo '<span class="dashicons dashicons-star-filled" style="color: #BC1F2F;" title="Featured"></span>';
                } else {
                    echo '—';
                }
                break;

            case 'views':
                $views = get_post_meta($post_id, '_pptm_view_count', true);
                echo $views ? absint($views) : '0';
                break;
        }
    }

    public function sortable_columns($columns) {
        $columns['availability'] = 'availability';
        $columns['views'] = 'views';
        $columns['featured'] = 'featured';
        return $columns;
    }
}
