<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Post_Type {

    public static function init() {
        add_action('init', array(__CLASS__, 'register'));
    }

    public static function register() {
        $labels = array(
            'name'                  => _x('Talents', 'Post Type General Name', 'premierplug-talent'),
            'singular_name'         => _x('Talent', 'Post Type Singular Name', 'premierplug-talent'),
            'menu_name'             => __('Talent Management', 'premierplug-talent'),
            'name_admin_bar'        => __('Talent', 'premierplug-talent'),
            'archives'              => __('Talent Archives', 'premierplug-talent'),
            'attributes'            => __('Talent Attributes', 'premierplug-talent'),
            'parent_item_colon'     => __('Parent Talent:', 'premierplug-talent'),
            'all_items'             => __('All Talents', 'premierplug-talent'),
            'add_new_item'          => __('Add New Talent', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'new_item'              => __('New Talent', 'premierplug-talent'),
            'edit_item'             => __('Edit Talent', 'premierplug-talent'),
            'update_item'           => __('Update Talent', 'premierplug-talent'),
            'view_item'             => __('View Talent', 'premierplug-talent'),
            'view_items'            => __('View Talents', 'premierplug-talent'),
            'search_items'          => __('Search Talent', 'premierplug-talent'),
            'not_found'             => __('Not found', 'premierplug-talent'),
            'not_found_in_trash'    => __('Not found in Trash', 'premierplug-talent'),
            'featured_image'        => __('Talent Photo', 'premierplug-talent'),
            'set_featured_image'    => __('Set talent photo', 'premierplug-talent'),
            'remove_featured_image' => __('Remove talent photo', 'premierplug-talent'),
            'use_featured_image'    => __('Use as talent photo', 'premierplug-talent'),
            'insert_into_item'      => __('Insert into talent', 'premierplug-talent'),
            'uploaded_to_this_item' => __('Uploaded to this talent', 'premierplug-talent'),
            'items_list'            => __('Talents list', 'premierplug-talent'),
            'items_list_navigation' => __('Talents list navigation', 'premierplug-talent'),
            'filter_items_list'     => __('Filter talents list', 'premierplug-talent'),
        );

        $args = array(
            'label'                 => __('Talent', 'premierplug-talent'),
            'description'           => __('Talent profiles and management', 'premierplug-talent'),
            'labels'                => $labels,
            'supports'              => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'),
            'taxonomies'            => array('talent_category', 'talent_skill'),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 20,
            'menu_icon'             => 'dashicons-groups',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => 'talents',
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'post',
            'show_in_rest'          => true,
            'rewrite'               => array('slug' => 'talent'),
        );

        register_post_type('talent', $args);
    }
}
