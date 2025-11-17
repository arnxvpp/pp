<?php
/**
 * PremierPlug Theme Functions
 *
 * @package PremierPlug
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PREMIERPLUG_VERSION', '1.0.0');
define('PREMIERPLUG_THEME_DIR', get_template_directory());
define('PREMIERPLUG_THEME_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function premierplug_theme_setup() {
    load_theme_textdomain('premierplug', PREMIERPLUG_THEME_DIR . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo', array(
        'height'      => 500,
        'width'       => 500,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');

    register_nav_menus(array(
        'primary'       => esc_html__('Primary Menu (Overlay)', 'premierplug'),
        'footer'        => esc_html__('Footer Menu', 'premierplug'),
    ));

    add_image_size('premierplug-hero', 1920, 1080, true);
    add_image_size('premierplug-featured', 800, 600, true);
}
add_action('after_setup_theme', 'premierplug_theme_setup');

/**
 * Enqueue Styles
 */
function premierplug_enqueue_styles() {
    // Self-hosted fonts (replaces Typekit)
    wp_enqueue_style(
        'premierplug-fonts',
        PREMIERPLUG_THEME_URI . '/assets/css/fonts.css',
        array(),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style('premierplug-style', get_stylesheet_uri(), array('premierplug-fonts'), PREMIERPLUG_VERSION);

    wp_enqueue_style(
        'premierplug-base',
        PREMIERPLUG_THEME_URI . '/assets/css/css_IY5cou33-Z4h9ItNyj7yrjAFHPSeHIWcP84YQeF024I.css',
        array('premierplug-fonts'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-main',
        PREMIERPLUG_THEME_URI . '/assets/css/css_h9OGQ3YXQzwOiNrq3miMMXsKb0gdhD3HNu3iTHZ-EIY.css',
        array('premierplug-base'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-print',
        PREMIERPLUG_THEME_URI . '/assets/css/css_NLD5UbnuV0gugBA-jekdwhJwL_TOG1O02JwgJVsX-lQ.css',
        array(),
        PREMIERPLUG_VERSION,
        'print'
    );
}
add_action('wp_enqueue_scripts', 'premierplug_enqueue_styles');

/**
 * Enqueue Scripts
 */
function premierplug_enqueue_scripts() {
    wp_enqueue_script('jquery');

    wp_enqueue_script(
        'premierplug-vendor',
        PREMIERPLUG_THEME_URI . '/assets/js/js_C8k3LpuSV-zrb3jpsAqDOCZTPoUZuiYqQmYtXwpZn6s.js',
        array('jquery'),
        PREMIERPLUG_VERSION,
        true
    );

    // Typekit removed - using self-hosted fonts instead

    wp_enqueue_script(
        'premierplug-main',
        PREMIERPLUG_THEME_URI . '/assets/js/js_nMHYJKXGedL7WvMtfqTeTvz_QKUCogMfWJZRTS30Qb0.js',
        array('jquery', 'premierplug-vendor'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_enqueue_script(
        'lodash',
        'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js',
        array(),
        '4.17.21',
        false
    );

    wp_enqueue_script(
        'hoverintent',
        'https://cdnjs.cloudflare.com/ajax/libs/jquery-hoverintent/1.10.2/jquery.hoverIntent.min.js',
        array('jquery'),
        '1.10.2',
        true
    );

    wp_enqueue_script(
        'premierplug-custom',
        PREMIERPLUG_THEME_URI . '/assets/js/js_DN2J3ll5I8mAnGkTsnDsnHkTTd7TtSkd2gb9ibNdN68.js',
        array('jquery', 'lodash', 'premierplug-main', 'hoverintent'),
        PREMIERPLUG_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'premierplug_enqueue_scripts');

/**
 * Custom Walker for Multi-Level Navigation
 */
class PremierPlug_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul>\n";
    }
}

/**
 * Widget Areas
 */
function premierplug_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'premierplug'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here.', 'premierplug'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'premierplug_widgets_init');

/**
 * Add body classes
 */
function premierplug_body_classes($classes) {
    $classes[] = 'role--anonymous';

    if (is_front_page()) {
        $classes[] = 'homepage';
    }

    return $classes;
}
add_filter('body_class', 'premierplug_body_classes');

/**
 * Customizer Options
 */
function premierplug_customize_register($wp_customize) {
    $wp_customize->add_section('premierplug_homepage', array(
        'title'    => esc_html__('Homepage Settings', 'premierplug'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('premierplug_enable_intro_animation', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('premierplug_enable_intro_animation', array(
        'label'    => esc_html__('Enable Intro Animation', 'premierplug'),
        'section'  => 'premierplug_homepage',
        'type'     => 'checkbox',
    ));

    $wp_customize->add_setting('premierplug_slogan', array(
        'default'           => 'Plugged It Premier.',
        'sanitize_callback' => 'sanitize_text_field',
    ));

    $wp_customize->add_control('premierplug_slogan', array(
        'label'    => esc_html__('Homepage Slogan', 'premierplug'),
        'section'  => 'premierplug_homepage',
        'type'     => 'text',
    ));
}
add_action('customize_register', 'premierplug_customize_register');

/**
 * Add lazy loading to images
 */
function premierplug_lazy_load_images($content) {
    if (is_admin() || is_feed() || wp_is_mobile()) {
        return $content;
    }

    $content = preg_replace(
        '/<img(.*?)src=/i',
        '<img$1loading="lazy" src=',
        $content
    );

    return $content;
}
add_filter('the_content', 'premierplug_lazy_load_images', 99);
add_filter('post_thumbnail_html', 'premierplug_lazy_load_images', 99);

/**
 * Remove query strings from static resources
 */
function premierplug_remove_query_strings($src) {
    if (strpos($src, '?ver=')) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('style_loader_src', 'premierplug_remove_query_strings', 10, 2);
add_filter('script_loader_src', 'premierplug_remove_query_strings', 10, 2);

/**
 * Disable embeds
 */
function premierplug_disable_embeds() {
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'premierplug_disable_embeds');

/**
 * Remove emoji scripts
 */
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

/**
 * Defer JavaScript loading
 */
function premierplug_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array(
        'premierplug-custom',
        'lodash',
        'premierplug-vendor'
    );

    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }

    return $tag;
}
add_filter('script_loader_tag', 'premierplug_defer_scripts', 10, 3);

/**
 * Load SEO Functions
 */
if (!file_exists(PREMIERPLUG_THEME_DIR . '/includes')) {
    mkdir(PREMIERPLUG_THEME_DIR . '/includes', 0755, true);
}
require_once PREMIERPLUG_THEME_DIR . '/includes/seo-functions.php';
