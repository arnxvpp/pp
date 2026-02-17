<?php
/**
 * Article Post Types Class
 *
 * Registers 5 custom post types for article management:
 * - Press Releases
 * - Blog Articles
 * - Awards
 * - News
 * - Media Coverage
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Post_Types {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('admin_menu', array(__CLASS__, 'add_articles_menu_page'));
        add_filter('post_updated_messages', array(__CLASS__, 'custom_post_messages'));
    }

    /**
     * Register all article post types
     */
    public static function register_post_types() {
        self::register_press_release();
        self::register_blog_article();
        self::register_award();
        self::register_news();
        self::register_media_coverage();
    }

    /**
     * Register Press Release post type
     */
    private static function register_press_release() {
        $labels = array(
            'name'                  => _x('Press Releases', 'Post type general name', 'premierplug-talent'),
            'singular_name'         => _x('Press Release', 'Post type singular name', 'premierplug-talent'),
            'menu_name'             => _x('Press Releases', 'Admin Menu text', 'premierplug-talent'),
            'name_admin_bar'        => _x('Press Release', 'Add New on Toolbar', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'add_new_item'          => __('Add New Press Release', 'premierplug-talent'),
            'new_item'              => __('New Press Release', 'premierplug-talent'),
            'edit_item'             => __('Edit Press Release', 'premierplug-talent'),
            'view_item'             => __('View Press Release', 'premierplug-talent'),
            'all_items'             => __('All Press Releases', 'premierplug-talent'),
            'search_items'          => __('Search Press Releases', 'premierplug-talent'),
            'parent_item_colon'     => __('Parent Press Releases:', 'premierplug-talent'),
            'not_found'             => __('No press releases found.', 'premierplug-talent'),
            'not_found_in_trash'    => __('No press releases found in Trash.', 'premierplug-talent'),
            'featured_image'        => _x('Press Release Image', 'Overrides the "Featured Image" phrase', 'premierplug-talent'),
            'set_featured_image'    => _x('Set press release image', 'Overrides the "Set featured image" phrase', 'premierplug-talent'),
            'remove_featured_image' => _x('Remove press release image', 'Overrides the "Remove featured image" phrase', 'premierplug-talent'),
            'use_featured_image'    => _x('Use as press release image', 'Overrides the "Use as featured image" phrase', 'premierplug-talent'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'press-releases'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-megaphone',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
            'show_in_rest'       => true,
        );

        register_post_type('press_release', $args);
    }

    /**
     * Register Blog Article post type
     */
    private static function register_blog_article() {
        $labels = array(
            'name'                  => _x('Blog Articles', 'Post type general name', 'premierplug-talent'),
            'singular_name'         => _x('Blog Article', 'Post type singular name', 'premierplug-talent'),
            'menu_name'             => _x('Blog', 'Admin Menu text', 'premierplug-talent'),
            'name_admin_bar'        => _x('Blog Article', 'Add New on Toolbar', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'add_new_item'          => __('Add New Blog Article', 'premierplug-talent'),
            'new_item'              => __('New Blog Article', 'premierplug-talent'),
            'edit_item'             => __('Edit Blog Article', 'premierplug-talent'),
            'view_item'             => __('View Blog Article', 'premierplug-talent'),
            'all_items'             => __('All Blog Articles', 'premierplug-talent'),
            'search_items'          => __('Search Blog Articles', 'premierplug-talent'),
            'not_found'             => __('No blog articles found.', 'premierplug-talent'),
            'not_found_in_trash'    => __('No blog articles found in Trash.', 'premierplug-talent'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'blog'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-edit',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'comments', 'revisions'),
            'show_in_rest'       => true,
        );

        register_post_type('blog_article', $args);
    }

    /**
     * Register Award post type
     */
    private static function register_award() {
        $labels = array(
            'name'                  => _x('Awards', 'Post type general name', 'premierplug-talent'),
            'singular_name'         => _x('Award', 'Post type singular name', 'premierplug-talent'),
            'menu_name'             => _x('Awards', 'Admin Menu text', 'premierplug-talent'),
            'name_admin_bar'        => _x('Award', 'Add New on Toolbar', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'add_new_item'          => __('Add New Award', 'premierplug-talent'),
            'new_item'              => __('New Award', 'premierplug-talent'),
            'edit_item'             => __('Edit Award', 'premierplug-talent'),
            'view_item'             => __('View Award', 'premierplug-talent'),
            'all_items'             => __('All Awards', 'premierplug-talent'),
            'search_items'          => __('Search Awards', 'premierplug-talent'),
            'not_found'             => __('No awards found.', 'premierplug-talent'),
            'not_found_in_trash'    => __('No awards found in Trash.', 'premierplug-talent'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'awards'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-awards',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
            'show_in_rest'       => true,
        );

        register_post_type('award', $args);
    }

    /**
     * Register News post type
     */
    private static function register_news() {
        $labels = array(
            'name'                  => _x('News', 'Post type general name', 'premierplug-talent'),
            'singular_name'         => _x('News', 'Post type singular name', 'premierplug-talent'),
            'menu_name'             => _x('News', 'Admin Menu text', 'premierplug-talent'),
            'name_admin_bar'        => _x('News', 'Add New on Toolbar', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'add_new_item'          => __('Add New News', 'premierplug-talent'),
            'new_item'              => __('New News', 'premierplug-talent'),
            'edit_item'             => __('Edit News', 'premierplug-talent'),
            'view_item'             => __('View News', 'premierplug-talent'),
            'all_items'             => __('All News', 'premierplug-talent'),
            'search_items'          => __('Search News', 'premierplug-talent'),
            'not_found'             => __('No news found.', 'premierplug-talent'),
            'not_found_in_trash'    => __('No news found in Trash.', 'premierplug-talent'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'news'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-media-document',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
            'show_in_rest'       => true,
        );

        register_post_type('news', $args);
    }

    /**
     * Register Media Coverage post type
     */
    private static function register_media_coverage() {
        $labels = array(
            'name'                  => _x('Media Coverage', 'Post type general name', 'premierplug-talent'),
            'singular_name'         => _x('Media Coverage', 'Post type singular name', 'premierplug-talent'),
            'menu_name'             => _x('Media Coverage', 'Admin Menu text', 'premierplug-talent'),
            'name_admin_bar'        => _x('Media Coverage', 'Add New on Toolbar', 'premierplug-talent'),
            'add_new'               => __('Add New', 'premierplug-talent'),
            'add_new_item'          => __('Add New Media Coverage', 'premierplug-talent'),
            'new_item'              => __('New Media Coverage', 'premierplug-talent'),
            'edit_item'             => __('Edit Media Coverage', 'premierplug-talent'),
            'view_item'             => __('View Media Coverage', 'premierplug-talent'),
            'all_items'             => __('All Media Coverage', 'premierplug-talent'),
            'search_items'          => __('Search Media Coverage', 'premierplug-talent'),
            'not_found'             => __('No media coverage found.', 'premierplug-talent'),
            'not_found_in_trash'    => __('No media coverage found in Trash.', 'premierplug-talent'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'media-coverage'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-video-alt3',
            'supports'           => array('title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'),
            'show_in_rest'       => true,
        );

        register_post_type('media_coverage', $args);
    }

    /**
     * Add custom admin menu page for articles
     */
    public static function add_articles_menu_page() {
        add_menu_page(
            __('Articles', 'premierplug-talent'),
            __('Articles', 'premierplug-talent'),
            'edit_posts',
            'talent-articles',
            array('PPTM_Articles_Manager', 'render_dashboard'),
            'dashicons-portfolio',
            21
        );

        add_submenu_page(
            'talent-articles',
            __('All Articles', 'premierplug-talent'),
            __('All Articles', 'premierplug-talent'),
            'edit_posts',
            'talent-articles',
            array('PPTM_Articles_Manager', 'render_dashboard')
        );

        add_submenu_page(
            'talent-articles',
            __('Press Releases', 'premierplug-talent'),
            __('Press Releases', 'premierplug-talent'),
            'edit_posts',
            'edit.php?post_type=press_release'
        );

        add_submenu_page(
            'talent-articles',
            __('Blog', 'premierplug-talent'),
            __('Blog', 'premierplug-talent'),
            'edit_posts',
            'edit.php?post_type=blog_article'
        );

        add_submenu_page(
            'talent-articles',
            __('Awards', 'premierplug-talent'),
            __('Awards', 'premierplug-talent'),
            'edit_posts',
            'edit.php?post_type=award'
        );

        add_submenu_page(
            'talent-articles',
            __('News', 'premierplug-talent'),
            __('News', 'premierplug-talent'),
            'edit_posts',
            'edit.php?post_type=news'
        );

        add_submenu_page(
            'talent-articles',
            __('Media Coverage', 'premierplug-talent'),
            __('Media Coverage', 'premierplug-talent'),
            'edit_posts',
            'edit.php?post_type=media_coverage'
        );
    }

    /**
     * Custom post updated messages
     */
    public static function custom_post_messages($messages) {
        global $post;

        if (!$post) {
            return $messages;
        }

        $article_types = array(
            'press_release' => 'Press Release',
            'blog_article' => 'Blog Article',
            'award' => 'Award',
            'news' => 'News',
            'media_coverage' => 'Media Coverage',
        );

        $permalink = get_permalink($post->ID);
        $preview_url = add_query_arg('preview', 'true', $permalink);
        $post_date = date_i18n(__('M j, Y @ G:i', 'premierplug-talent'), strtotime($post->post_date));

        foreach ($article_types as $type => $label) {
            $messages[$type] = array(
                0  => '',
                1  => sprintf(__('%1$s updated. <a href="%2$s">View %1$s</a>', 'premierplug-talent'), $label, esc_url($permalink)),
                2  => __('Custom field updated.', 'premierplug-talent'),
                3  => __('Custom field deleted.', 'premierplug-talent'),
                4  => sprintf(__('%s updated.', 'premierplug-talent'), $label),
                5  => isset($_GET['revision']) ? sprintf(__('%s restored to revision from %s', 'premierplug-talent'), $label, wp_post_revision_title((int) $_GET['revision'], false)) : false,
                6  => sprintf(__('%1$s published. <a href="%2$s">View %1$s</a>', 'premierplug-talent'), $label, esc_url($permalink)),
                7  => sprintf(__('%s saved.', 'premierplug-talent'), $label),
                8  => sprintf(__('%1$s submitted. <a target="_blank" href="%2$s">Preview %1$s</a>', 'premierplug-talent'), $label, esc_url($preview_url)),
                9  => sprintf(__('%1$s scheduled for: <strong>%2$s</strong>. <a target="_blank" href="%3$s">Preview %1$s</a>', 'premierplug-talent'), $label, $post_date, esc_url($permalink)),
                10 => sprintf(__('%1$s draft updated. <a target="_blank" href="%2$s">Preview %1$s</a>', 'premierplug-talent'), $label, esc_url($preview_url)),
            );
        }

        return $messages;
    }

    /**
     * Get all article post types
     */
    public static function get_article_types() {
        return array(
            'press_release',
            'blog_article',
            'award',
            'news',
            'media_coverage',
        );
    }

    /**
     * Get article type label
     */
    public static function get_type_label($type) {
        $labels = array(
            'press_release' => __('Press Release', 'premierplug-talent'),
            'blog_article' => __('Blog Article', 'premierplug-talent'),
            'award' => __('Award', 'premierplug-talent'),
            'news' => __('News', 'premierplug-talent'),
            'media_coverage' => __('Media Coverage', 'premierplug-talent'),
        );

        return isset($labels[$type]) ? $labels[$type] : $type;
    }

    /**
     * Check if post type is an article type
     */
    public static function is_article_type($post_type) {
        return in_array($post_type, self::get_article_types());
    }
}
