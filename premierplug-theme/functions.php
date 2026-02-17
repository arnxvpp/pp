<?php
/**
 * PremierPlug Theme Functions
 *
 * @package PremierPlug
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PREMIERPLUG_VERSION', '1.0.1');
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
        'premierplug-main-design-system',
        PREMIERPLUG_THEME_URI . '/assets/css/main-design-system.css',
        array('premierplug-style'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-system-ui',
        PREMIERPLUG_THEME_URI . '/assets/css/system-ui.css',
        array('premierplug-main-design-system'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-layout',
        PREMIERPLUG_THEME_URI . '/assets/css/layout.css',
        array('premierplug-system-ui'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-navigation-fix',
        PREMIERPLUG_THEME_URI . '/assets/css/navigation-dropdown-fix.css',
        array('premierplug-layout'),
        PREMIERPLUG_VERSION
    );

    wp_enqueue_style(
        'premierplug-print',
        PREMIERPLUG_THEME_URI . '/assets/css/print.css',
        array('premierplug-navigation-fix'),
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
        'premierplug-lodash',
        'https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js',
        array(),
        '4.17.21',
        true
    );

    wp_enqueue_script(
        'premierplug-vendor',
        PREMIERPLUG_THEME_URI . '/assets/js/vendor.js',
        array('jquery'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_enqueue_script(
        'premierplug-main',
        PREMIERPLUG_THEME_URI . '/assets/js/main.js',
        array('jquery', 'premierplug-lodash', 'premierplug-vendor'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_enqueue_script(
        'premierplug-custom',
        PREMIERPLUG_THEME_URI . '/assets/js/custom.js',
        array('jquery', 'premierplug-lodash', 'premierplug-main'),
        PREMIERPLUG_VERSION,
        true
    );

    wp_add_inline_script('premierplug-main', '
        if (typeof Modernizr === "undefined") {
            window.Modernizr = { mq: function(q) { return window.matchMedia(q).matches; } };
        }
        if (typeof Hammer === "undefined") {
            window.Hammer = function(el) { this.on = function() { return this; }; };
        }
        if (typeof jQuery !== "undefined" && !jQuery.fn.velocity) {
            jQuery.fn.velocity = function(props, opts) { return this.animate(props, opts); };
        }
        if (typeof jQuery !== "undefined" && !jQuery.fn.hoverIntent) {
            jQuery.fn.hoverIntent = function(over, out) { return this.on("mouseenter", over).on("mouseleave", out); };
        }
    ', 'after');

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
        $args = (object) $args;
        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $has_children = !empty($args->walker) && $args->walker->has_children;

        if ($has_children) {
            $classes[] = 'menu-item-has-children';
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= $indent . '<li' . $class_names . '>';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target) ? $item->target : '_self';
        $atts['rel']    = !empty($item->xfn) ? $item->xfn : '';

        if ($has_children) {
            $atts['href'] = '#';
            $atts['class'] = '';
        } else {
            $atts['href'] = !empty($item->url) ? $item->url : '#';
            $atts['class'] = 'linkTo';
        }

        $atts = apply_filters('nav_menu_link_attributes', $atts, $item, $args, $depth);

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if ($attr === 'href' || !empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $before = isset($args->before) ? $args->before : '';
        $after = isset($args->after) ? $args->after : '';
        $link_before = isset($args->link_before) ? $args->link_before : '';
        $link_after = isset($args->link_after) ? $args->link_after : '';

        $item_output = $before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $link_before . $title . $link_after;
        $item_output .= '</a>';
        $item_output .= $after;

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
