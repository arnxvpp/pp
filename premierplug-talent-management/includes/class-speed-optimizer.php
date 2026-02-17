<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Speed_Optimizer {

    public static function init() {
        add_action('wp_enqueue_scripts', array(__CLASS__, 'optimize_scripts_styles'), 999);
        add_filter('the_content', array(__CLASS__, 'add_lazy_loading'), 50);
        add_filter('post_thumbnail_html', array(__CLASS__, 'add_lazy_loading_to_thumbnails'), 10, 5);
        add_action('wp_head', array(__CLASS__, 'add_preload_tags'), 0);
        add_filter('wp_get_attachment_image_attributes', array(__CLASS__, 'add_webp_support'), 10, 3);
    }

    public static function optimize_scripts_styles() {
        if (get_option('pptm_defer_javascript', 'no') === 'yes') {
            add_filter('script_loader_tag', array(__CLASS__, 'defer_scripts'), 10, 2);
        }

        if (get_option('pptm_async_css', 'no') === 'yes') {
            add_filter('style_loader_tag', array(__CLASS__, 'async_styles'), 10, 2);
        }
    }

    public static function defer_scripts($tag, $handle) {
        $excluded_handles = array('jquery', 'jquery-core', 'jquery-migrate');

        if (in_array($handle, $excluded_handles)) {
            return $tag;
        }

        if (strpos($tag, 'defer') !== false || strpos($tag, 'async') !== false) {
            return $tag;
        }

        return str_replace('<script ', '<script defer ', $tag);
    }

    public static function async_styles($html, $handle) {
        if (strpos($handle, 'pptm') !== 0) {
            return $html;
        }

        $html = str_replace("media='all'", "media='print' onload=\"this.media='all'\"", $html);

        return $html;
    }

    public static function add_lazy_loading($content) {
        if (get_option('pptm_lazy_load_images', 'yes') !== 'yes') {
            return $content;
        }

        if (is_feed() || is_admin()) {
            return $content;
        }

        $content = preg_replace_callback('/<img([^>]+)>/i', array(__CLASS__, 'add_lazy_attributes'), $content);

        return $content;
    }

    public static function add_lazy_loading_to_thumbnails($html, $post_id, $post_thumbnail_id, $size, $attr) {
        if (get_option('pptm_lazy_load_images', 'yes') !== 'yes') {
            return $html;
        }

        if (strpos($html, 'loading=') !== false) {
            return $html;
        }

        return str_replace('<img ', '<img loading="lazy" ', $html);
    }

    private static function add_lazy_attributes($matches) {
        $img_tag = $matches[0];

        if (strpos($img_tag, 'loading=') !== false) {
            return $img_tag;
        }

        if (strpos($img_tag, 'data-src=') !== false) {
            return $img_tag;
        }

        return str_replace('<img ', '<img loading="lazy" ', $img_tag);
    }

    public static function add_preload_tags() {
        $preload_enabled = get_option('pptm_preload_resources', 'no');

        if ($preload_enabled !== 'yes') {
            return;
        }

        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
            if ($logo_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($logo_url) . '">' . "\n";
            }
        }

        if (is_singular() && has_post_thumbnail()) {
            $featured_image_url = get_the_post_thumbnail_url(get_the_ID(), 'large');
            if ($featured_image_url) {
                echo '<link rel="preload" as="image" href="' . esc_url($featured_image_url) . '">' . "\n";
            }
        }

        echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
        echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
        echo '<link rel="dns-prefetch" href="//www.google-analytics.com">' . "\n";
        echo '<link rel="dns-prefetch" href="//www.googletagmanager.com">' . "\n";
    }

    public static function add_webp_support($attr, $attachment, $size) {
        if (get_option('pptm_webp_support', 'no') !== 'yes') {
            return $attr;
        }

        return $attr;
    }

    private static function get_wp_filesystem() {
        global $wp_filesystem;
        if (!$wp_filesystem) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            WP_Filesystem();
        }
        return $wp_filesystem;
    }

    public static function add_browser_caching() {
        $cache_enabled = get_option('pptm_browser_caching', 'no');

        if ($cache_enabled !== 'yes' || is_admin()) {
            return;
        }

        $htaccess_file = ABSPATH . '.htaccess';
        $wp_filesystem = self::get_wp_filesystem();

        if (!$wp_filesystem || !$wp_filesystem->exists($htaccess_file) || !$wp_filesystem->is_writable($htaccess_file)) {
            return;
        }

        $htaccess_content = $wp_filesystem->get_contents($htaccess_file);

        if (strpos($htaccess_content, '# BEGIN PremierPlug Cache') !== false) {
            return;
        }

        $cache_rules = "\n# BEGIN PremierPlug Cache\n";
        $cache_rules .= "<IfModule mod_expires.c>\n";
        $cache_rules .= "ExpiresActive On\n";
        $cache_rules .= "ExpiresByType image/jpg \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType image/jpeg \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType image/gif \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType image/png \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType image/webp \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType image/svg+xml \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType text/css \"access plus 1 month\"\n";
        $cache_rules .= "ExpiresByType text/javascript \"access plus 1 month\"\n";
        $cache_rules .= "ExpiresByType application/javascript \"access plus 1 month\"\n";
        $cache_rules .= "ExpiresByType application/x-javascript \"access plus 1 month\"\n";
        $cache_rules .= "ExpiresByType application/pdf \"access plus 1 month\"\n";
        $cache_rules .= "ExpiresByType application/x-font-ttf \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType application/x-font-woff \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType application/font-woff \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType application/font-woff2 \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType font/woff \"access plus 1 year\"\n";
        $cache_rules .= "ExpiresByType font/woff2 \"access plus 1 year\"\n";
        $cache_rules .= "</IfModule>\n";
        $cache_rules .= "\n<IfModule mod_headers.c>\n";
        $cache_rules .= "<FilesMatch \"\\.(ico|pdf|flv|jpg|jpeg|png|gif|webp|svg|js|css|swf|woff|woff2|ttf)$\">\n";
        $cache_rules .= "Header set Cache-Control \"max-age=31536000, public\"\n";
        $cache_rules .= "</FilesMatch>\n";
        $cache_rules .= "</IfModule>\n";
        $cache_rules .= "# END PremierPlug Cache\n\n";

        $new_content = $cache_rules . $htaccess_content;

        $wp_filesystem->put_contents($htaccess_file, $new_content, FS_CHMOD_FILE);
    }

    public static function remove_browser_caching() {
        $htaccess_file = ABSPATH . '.htaccess';
        $wp_filesystem = self::get_wp_filesystem();

        if (!$wp_filesystem || !$wp_filesystem->exists($htaccess_file) || !$wp_filesystem->is_writable($htaccess_file)) {
            return;
        }

        $htaccess_content = $wp_filesystem->get_contents($htaccess_file);

        $htaccess_content = preg_replace('/# BEGIN PremierPlug Cache.*?# END PremierPlug Cache\n\n/s', '', $htaccess_content);

        $wp_filesystem->put_contents($htaccess_file, $htaccess_content, FS_CHMOD_FILE);
    }

    public static function minify_html($html) {
        if (get_option('pptm_minify_html', 'no') !== 'yes') {
            return $html;
        }

        if (is_admin()) {
            return $html;
        }

        $html = preg_replace('/<!--(?!\[if\s).*?-->/s', '', $html);

        $html = preg_replace('/\s+/', ' ', $html);

        $html = preg_replace('/>\s+</', '><', $html);

        return trim($html);
    }

    public static function get_page_speed_score() {
        return array(
            'lazy_loading' => get_option('pptm_lazy_load_images', 'yes') === 'yes',
            'defer_js' => get_option('pptm_defer_javascript', 'no') === 'yes',
            'browser_caching' => get_option('pptm_browser_caching', 'no') === 'yes',
            'preload_resources' => get_option('pptm_preload_resources', 'no') === 'yes',
            'minify_html' => get_option('pptm_minify_html', 'no') === 'yes',
        );
    }
}

if (get_option('pptm_minify_html', 'no') === 'yes') {
    add_action('template_redirect', function() {
        ob_start(array('PPTM_Speed_Optimizer', 'minify_html'));
    });
}
