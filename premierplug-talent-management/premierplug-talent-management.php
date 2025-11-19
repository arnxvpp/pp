<?php
/**
 * Plugin Name: PremierPlug Talent Management
 * Plugin URI: https://premierplug.org
 * Description: Complete talent management system with profiles, categories, search, and Supabase integration for PremierPlug agency.
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
define('PPTM_PLUGIN_FILE', __FILE__);

class PremierPlug_Talent_Management {

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
        require_once PPTM_PLUGIN_DIR . 'includes/class-post-type.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-taxonomies.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-supabase.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-metaboxes.php';
        require_once PPTM_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once PPTM_PLUGIN_DIR . 'admin/class-admin.php';
        require_once PPTM_PLUGIN_DIR . 'public/class-public.php';
    }

    private function init_hooks() {
        register_activation_hook(PPTM_PLUGIN_FILE, array($this, 'activate'));
        register_deactivation_hook(PPTM_PLUGIN_FILE, array($this, 'deactivate'));

        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function init() {
        PPTM_Post_Type::init();
        PPTM_Taxonomies::init();
        PPTM_Metaboxes::init();
        PPTM_Shortcodes::init();
        PPTM_Admin::init();
        PPTM_Public::init();
    }

    public function activate() {
        PPTM_Post_Type::register();
        PPTM_Taxonomies::register();
        flush_rewrite_rules();

        $this->create_default_categories();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }

    private function create_default_categories() {
        $categories = array(
            'Motion Pictures' => 'Actors, Directors, Producers',
            'Digital Media' => 'Content Creators, Influencers',
            'Speakers' => 'Keynote Speakers, Motivational Speakers',
            'Television' => 'TV Personalities, Hosts',
            'Voiceovers' => 'Voice Artists, Narrators',
            'Music' => 'Musicians, Singers, Bands',
        );

        foreach ($categories as $name => $description) {
            if (!term_exists($name, 'talent_category')) {
                wp_insert_term($name, 'talent_category', array(
                    'description' => $description,
                    'slug' => sanitize_title($name)
                ));
            }
        }
    }

    public function enqueue_public_assets() {
        wp_enqueue_style(
            'pptm-public',
            PPTM_PLUGIN_URL . 'assets/css/public.css',
            array(),
            PPTM_VERSION
        );

        wp_enqueue_script(
            'pptm-public',
            PPTM_PLUGIN_URL . 'assets/js/public.js',
            array('jquery'),
            PPTM_VERSION,
            true
        );

        wp_localize_script('pptm-public', 'pptmData', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('pptm_nonce')
        ));
    }

    public function enqueue_admin_assets($hook) {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'talent') {
            wp_enqueue_style(
                'pptm-admin',
                PPTM_PLUGIN_URL . 'assets/css/admin.css',
                array(),
                PPTM_VERSION
            );

            wp_enqueue_script(
                'pptm-admin',
                PPTM_PLUGIN_URL . 'assets/js/admin.js',
                array('jquery'),
                PPTM_VERSION,
                true
            );
        }
    }
}

function pptm() {
    return PremierPlug_Talent_Management::get_instance();
}

pptm();
