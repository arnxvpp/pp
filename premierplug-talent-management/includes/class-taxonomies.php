<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Taxonomies {

    public static function init() {
        add_action('init', array(__CLASS__, 'register'));
    }

    public static function register() {
        self::register_category();
        self::register_skill();
        self::register_availability();
    }

    private static function register_category() {
        $labels = array(
            'name'                       => _x('Talent Categories', 'Taxonomy General Name', 'premierplug-talent'),
            'singular_name'              => _x('Talent Category', 'Taxonomy Singular Name', 'premierplug-talent'),
            'menu_name'                  => __('Categories', 'premierplug-talent'),
            'all_items'                  => __('All Categories', 'premierplug-talent'),
            'parent_item'                => __('Parent Category', 'premierplug-talent'),
            'parent_item_colon'          => __('Parent Category:', 'premierplug-talent'),
            'new_item_name'              => __('New Category Name', 'premierplug-talent'),
            'add_new_item'               => __('Add New Category', 'premierplug-talent'),
            'edit_item'                  => __('Edit Category', 'premierplug-talent'),
            'update_item'                => __('Update Category', 'premierplug-talent'),
            'view_item'                  => __('View Category', 'premierplug-talent'),
            'separate_items_with_commas' => __('Separate categories with commas', 'premierplug-talent'),
            'add_or_remove_items'        => __('Add or remove categories', 'premierplug-talent'),
            'choose_from_most_used'      => __('Choose from the most used', 'premierplug-talent'),
            'popular_items'              => __('Popular Categories', 'premierplug-talent'),
            'search_items'               => __('Search Categories', 'premierplug-talent'),
            'not_found'                  => __('Not Found', 'premierplug-talent'),
            'no_terms'                   => __('No categories', 'premierplug-talent'),
            'items_list'                 => __('Categories list', 'premierplug-talent'),
            'items_list_navigation'      => __('Categories list navigation', 'premierplug-talent'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'talent-category'),
        );

        register_taxonomy('talent_category', array('talent'), $args);
    }

    private static function register_skill() {
        $labels = array(
            'name'                       => _x('Skills', 'Taxonomy General Name', 'premierplug-talent'),
            'singular_name'              => _x('Skill', 'Taxonomy Singular Name', 'premierplug-talent'),
            'menu_name'                  => __('Skills', 'premierplug-talent'),
            'all_items'                  => __('All Skills', 'premierplug-talent'),
            'new_item_name'              => __('New Skill Name', 'premierplug-talent'),
            'add_new_item'               => __('Add New Skill', 'premierplug-talent'),
            'edit_item'                  => __('Edit Skill', 'premierplug-talent'),
            'update_item'                => __('Update Skill', 'premierplug-talent'),
            'view_item'                  => __('View Skill', 'premierplug-talent'),
            'separate_items_with_commas' => __('Separate skills with commas', 'premierplug-talent'),
            'add_or_remove_items'        => __('Add or remove skills', 'premierplug-talent'),
            'choose_from_most_used'      => __('Choose from the most used', 'premierplug-talent'),
            'popular_items'              => __('Popular Skills', 'premierplug-talent'),
            'search_items'               => __('Search Skills', 'premierplug-talent'),
            'not_found'                  => __('Not Found', 'premierplug-talent'),
            'no_terms'                   => __('No skills', 'premierplug-talent'),
            'items_list'                 => __('Skills list', 'premierplug-talent'),
            'items_list_navigation'      => __('Skills list navigation', 'premierplug-talent'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'talent-skill'),
        );

        register_taxonomy('talent_skill', array('talent'), $args);
    }

    private static function register_availability() {
        $labels = array(
            'name'                       => _x('Availability', 'Taxonomy General Name', 'premierplug-talent'),
            'singular_name'              => _x('Availability', 'Taxonomy Singular Name', 'premierplug-talent'),
            'menu_name'                  => __('Availability', 'premierplug-talent'),
            'all_items'                  => __('All Availability', 'premierplug-talent'),
            'new_item_name'              => __('New Availability', 'premierplug-talent'),
            'add_new_item'               => __('Add New Availability', 'premierplug-talent'),
            'edit_item'                  => __('Edit Availability', 'premierplug-talent'),
            'update_item'                => __('Update Availability', 'premierplug-talent'),
        );

        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => false,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_rest'               => true,
            'rewrite'                    => array('slug' => 'talent-availability'),
        );

        register_taxonomy('talent_availability', array('talent'), $args);
    }
}
