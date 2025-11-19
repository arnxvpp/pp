<?php
/**
 * PremierPlug Theme Functions
 *
 * @package PremierPlug
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
function premierplug_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 100,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('responsive-embeds');

    register_nav_menus(array(
        'primary' => __('Primary Navigation', 'premierplug'),
        'footer'  => __('Footer Navigation', 'premierplug'),
    ));
}
add_action('after_setup_theme', 'premierplug_setup');

/**
 * Enqueue Styles
 */
function premierplug_enqueue_styles() {
    wp_enqueue_style(
        'premierplug-style',
        get_stylesheet_uri(),
        array(),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-navigation-fix',
        PREMIERPLUG_THEME_URI . '/assets/css/navigation-dropdown-fix.css',
        array('premierplug-style'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-print',
        PREMIERPLUG_THEME_URI . '/assets/css/print.css',
        array('premierplug-style'),
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
        'lodash',
        'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js',
        array(),
        '4.17.21',
        false
    );

    wp_enqueue_script(
        'premierplug-vendor',
        PREMIERPLUG_THEME_URI . '/assets/js/vendor.js',
        array('jquery'),
        PREMIERPLUG_VERSION,
        false
    );

    wp_enqueue_script(
        'premierplug-main',
        PREMIERPLUG_THEME_URI . '/assets/js/main.js',
        array('jquery', 'lodash', 'premierplug-vendor'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_enqueue_script(
        'premierplug-custom',
        PREMIERPLUG_THEME_URI . '/assets/js/custom.js',
        array('jquery', 'lodash', 'premierplug-main'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_enqueue_script(
        'premierplug-navigation',
        PREMIERPLUG_THEME_URI . '/assets/js/navigation-dropdown-fix.js',
        array('jquery', 'premierplug-custom'),
        PREMIERPLUG_VERSION,
        true
    );
}
add_action('wp_enqueue_scripts', 'premierplug_enqueue_scripts');

/**
 * Custom Navigation Walker for Multi-Level Menus
 */
class PremierPlug_Walker_Nav_Menu extends Walker_Nav_Menu {
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul>\n";
    }

    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        if ($args->walker->has_children) {
            $classes[] = 'menu-item-has-children';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= $indent . '<li' . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '_self';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';
        $atts['href']   = !empty($item->url) ? $item->url : 'javascript:void(0);';
        $atts['class']  = $depth > 0 ? 'linkTo' : '';

        if ($args->walker->has_children && $depth === 0) {
            $atts['href'] = 'javascript:void(0);';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}

/**
 * Add body classes
 */
function premierplug_body_classes($classes) {
    if (!is_user_logged_in()) {
        $classes[] = 'role--anonymous';
    }
    return $classes;
}
add_filter('body_class', 'premierplug_body_classes');

/**
 * Customize excerpt length
 */
function premierplug_excerpt_length($length) {
    return 30;
}
add_filter('excerpt_length', 'premierplug_excerpt_length');

/**
 * Customize excerpt more
 */
function premierplug_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'premierplug_excerpt_more');
