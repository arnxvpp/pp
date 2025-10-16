<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Taxonomies {

    public function __construct() {
        add_action('init', array($this, 'register_taxonomies'));
        add_action('init', array($this, 'create_default_segments'), 20);
    }

    public function register_taxonomies() {
        $segment_labels = array(
            'name' => _x('Talent Segments', 'Taxonomy General Name', 'premierplug-talent'),
            'singular_name' => _x('Talent Segment', 'Taxonomy Singular Name', 'premierplug-talent'),
            'menu_name' => __('Segments', 'premierplug-talent'),
            'all_items' => __('All Segments', 'premierplug-talent'),
            'parent_item' => __('Parent Segment', 'premierplug-talent'),
            'parent_item_colon' => __('Parent Segment:', 'premierplug-talent'),
            'new_item_name' => __('New Segment Name', 'premierplug-talent'),
            'add_new_item' => __('Add New Segment', 'premierplug-talent'),
            'edit_item' => __('Edit Segment', 'premierplug-talent'),
            'update_item' => __('Update Segment', 'premierplug-talent'),
            'view_item' => __('View Segment', 'premierplug-talent'),
            'separate_items_with_commas' => __('Separate segments with commas', 'premierplug-talent'),
            'add_or_remove_items' => __('Add or remove segments', 'premierplug-talent'),
            'choose_from_most_used' => __('Choose from the most used', 'premierplug-talent'),
            'popular_items' => __('Popular Segments', 'premierplug-talent'),
            'search_items' => __('Search Segments', 'premierplug-talent'),
            'not_found' => __('Not Found', 'premierplug-talent'),
            'no_terms' => __('No segments', 'premierplug-talent'),
            'items_list' => __('Segments list', 'premierplug-talent'),
            'items_list_navigation' => __('Segments list navigation', 'premierplug-talent'),
        );

        $segment_args = array(
            'labels' => $segment_labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => false,
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => 'talent-segment',
                'with_front' => false,
            ),
        );

        register_taxonomy('talent_segment', array('pp_talent'), $segment_args);

        $skill_labels = array(
            'name' => _x('Skills', 'Taxonomy General Name', 'premierplug-talent'),
            'singular_name' => _x('Skill', 'Taxonomy Singular Name', 'premierplug-talent'),
            'menu_name' => __('Skills', 'premierplug-talent'),
            'all_items' => __('All Skills', 'premierplug-talent'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'new_item_name' => __('New Skill Name', 'premierplug-talent'),
            'add_new_item' => __('Add New Skill', 'premierplug-talent'),
            'edit_item' => __('Edit Skill', 'premierplug-talent'),
            'update_item' => __('Update Skill', 'premierplug-talent'),
            'view_item' => __('View Skill', 'premierplug-talent'),
            'separate_items_with_commas' => __('Separate skills with commas', 'premierplug-talent'),
            'add_or_remove_items' => __('Add or remove skills', 'premierplug-talent'),
            'choose_from_most_used' => __('Choose from the most used', 'premierplug-talent'),
            'popular_items' => __('Popular Skills', 'premierplug-talent'),
            'search_items' => __('Search Skills', 'premierplug-talent'),
            'not_found' => __('Not Found', 'premierplug-talent'),
            'no_terms' => __('No skills', 'premierplug-talent'),
            'items_list' => __('Skills list', 'premierplug-talent'),
            'items_list_navigation' => __('Skills list navigation', 'premierplug-talent'),
        );

        $skill_args = array(
            'labels' => $skill_labels,
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'show_in_nav_menus' => true,
            'show_tagcloud' => true,
            'show_in_rest' => true,
            'rewrite' => array(
                'slug' => 'talent-skill',
                'with_front' => false,
            ),
        );

        register_taxonomy('talent_skill', array('pp_talent'), $skill_args);
    }

    public function create_default_segments() {
        $segments = array(
            array(
                'name' => 'Digital Media',
                'slug' => 'digital-media',
                'description' => 'Content creators, influencers, and digital media professionals',
            ),
            array(
                'name' => 'Television',
                'slug' => 'television',
                'description' => 'TV personalities, hosts, and television professionals',
            ),
            array(
                'name' => 'Voiceovers',
                'slug' => 'voiceovers',
                'description' => 'Voice actors, dubbing artists, and voiceover professionals',
            ),
            array(
                'name' => 'Speakers',
                'slug' => 'speakers',
                'description' => 'Public speakers, motivational speakers, and event presenters',
            ),
            array(
                'name' => 'Motion Pictures',
                'slug' => 'motion-pictures',
                'description' => 'Film actors, directors, and motion picture professionals',
            ),
        );

        foreach ($segments as $segment) {
            if (!term_exists($segment['slug'], 'talent_segment')) {
                wp_insert_term(
                    $segment['name'],
                    'talent_segment',
                    array(
                        'slug' => $segment['slug'],
                        'description' => $segment['description'],
                    )
                );
            }
        }
    }
}
