<?php
/**
 * Plugin Name: PremierPlug Talent Manager
 * Plugin URI: https://premierplug.org
 * Description: Comprehensive talent management system for PremierPlug with Supabase integration. Manages talent profiles across Digital Media, Television, Voiceovers, Speakers, and Motion Pictures segments.
 * Version: 1.0.0
 * Author: PremierPlug Team
 * Author URI: https://premierplug.org
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: premierplug-talent
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit;
}

define('PPTM_VERSION', '1.0.0');
define('PPTM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PPTM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PPTM_PLUGIN_BASENAME', plugin_basename(__FILE__));

class PremierPlug_Talent_Manager {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    private function load_dependencies() {
        require_once PPTM_PLUGIN_DIR . 'includes/class-supabase-client.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-post-type.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-taxonomies.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-meta-boxes.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-sync.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-ajax.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-talent-shortcodes.php';
        require_once PPTM_PLUGIN_DIR . 'admin/class-talent-admin.php';
        require_once PPTM_PLUGIN_DIR . 'public/class-talent-public.php';
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'init'), 0);
    }

    public function init() {
        new PPTM_Talent_Post_Type();
        new PPTM_Talent_Taxonomies();
        new PPTM_Talent_Meta_Boxes();
        new PPTM_Talent_Sync();
        new PPTM_Talent_AJAX();
        new PPTM_Talent_Shortcodes();

        if (is_admin()) {
            new PPTM_Talent_Admin();
        } else {
            new PPTM_Talent_Public();
        }
    }

    public function activate() {
        flush_rewrite_rules();

        $this->create_default_options();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    private function create_default_options() {
        $default_options = array(
            'pptm_talents_per_page' => 12,
            'pptm_enable_analytics' => true,
            'pptm_cache_duration' => 900,
            'pptm_sync_enabled' => true,
        );

        foreach ($default_options as $option_name => $option_value) {
            if (false === get_option($option_name)) {
                add_option($option_name, $option_value);
            }
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain(
            'premierplug-talent',
            false,
            dirname(PPTM_PLUGIN_BASENAME) . '/languages/'
        );
    }
}

function PPTM() {
    return PremierPlug_Talent_Manager::get_instance();
}

PPTM();
