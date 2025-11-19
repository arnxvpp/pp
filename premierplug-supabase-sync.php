<?php
/**
 * Plugin Name: PremierPlug Supabase Sync
 * Description: Automatically syncs WordPress pages and menus to Supabase
 * Version: 1.0.0
 * Author: PremierPlug Team
 */

if (!defined('ABSPATH')) {
    exit;
}

class PremierPlug_Supabase_Sync {

    private $supabase_url;
    private $supabase_key;

    public function __construct() {
        $this->supabase_url = defined('SUPABASE_URL') ? SUPABASE_URL : getenv('SUPABASE_URL');
        $this->supabase_key = defined('SUPABASE_ANON_KEY') ? SUPABASE_ANON_KEY : getenv('SUPABASE_ANON_KEY');

        // Hooks
        add_action('save_post_page', array($this, 'sync_page_to_supabase'), 10, 3);
        add_action('delete_post', array($this, 'delete_page_from_supabase'), 10, 1);
        add_action('wp_update_nav_menu', array($this, 'sync_menu_to_supabase'), 10, 1);
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function add_admin_menu() {
        add_options_page(
            'Supabase Sync Settings',
            'Supabase Sync',
            'manage_options',
            'supabase-sync',
            array($this, 'settings_page')
        );
    }

    public function settings_page() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['supabase_settings'])) {
            check_admin_referer('supabase_settings');

            update_option('supabase_url', sanitize_text_field($_POST['supabase_url']));
            update_option('supabase_key', sanitize_text_field($_POST['supabase_key']));

            echo '<div class="notice notice-success"><p>Settings saved!</p></div>';
        }

        $saved_url = get_option('supabase_url', '');
        $saved_key = get_option('supabase_key', '');

        ?>
        <div class="wrap">
            <h1>Supabase Sync Settings</h1>
            <form method="post">
                <?php wp_nonce_field('supabase_settings'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="supabase_url">Supabase URL</label></th>
                        <td>
                            <input type="url" id="supabase_url" name="supabase_url"
                                   value="<?php echo esc_attr($saved_url); ?>"
                                   class="regular-text" placeholder="https://xxxxx.supabase.co">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="supabase_key">Supabase Anon Key</label></th>
                        <td>
                            <input type="text" id="supabase_key" name="supabase_key"
                                   value="<?php echo esc_attr($saved_key); ?>"
                                   class="large-text">
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="supabase_settings" class="button button-primary" value="Save Settings">
                </p>
            </form>

            <hr>

            <h2>Manual Sync</h2>
            <p>Click the button below to sync all existing pages to Supabase:</p>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="supabase_manual_sync">
                <?php wp_nonce_field('supabase_manual_sync'); ?>
                <input type="submit" class="button button-secondary" value="Sync All Pages Now">
            </form>
        </div>
        <?php
    }

    public function sync_page_to_supabase($post_id, $post, $update) {
        // Skip autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Get Supabase credentials
        $url = get_option('supabase_url', $this->supabase_url);
        $key = get_option('supabase_key', $this->supabase_key);

        if (empty($url) || empty($key)) {
            return;
        }

        // Prepare page data
        $data = array(
            'id' => $post_id,
            'title' => $post->post_title,
            'slug' => $post->post_name,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'parent_id' => $post->post_parent ? $post->post_parent : null,
            'menu_order' => $post->menu_order,
            'status' => $post->post_status,
            'template' => get_page_template_slug($post_id),
            'updated_at' => current_time('mysql', 1)
        );

        // Get featured image
        $thumbnail_id = get_post_thumbnail_id($post_id);
        if ($thumbnail_id) {
            $data['featured_image'] = wp_get_attachment_url($thumbnail_id);
        }

        // Send to Supabase
        $this->upsert_to_supabase($url, $key, 'pages', $data);
    }

    public function delete_page_from_supabase($post_id) {
        $post = get_post($post_id);
        if ($post->post_type !== 'page') {
            return;
        }

        $url = get_option('supabase_url', $this->supabase_url);
        $key = get_option('supabase_key', $this->supabase_key);

        if (empty($url) || empty($key)) {
            return;
        }

        $this->delete_from_supabase($url, $key, 'pages', $post_id);
    }

    public function sync_menu_to_supabase($menu_id) {
        $url = get_option('supabase_url', $this->supabase_url);
        $key = get_option('supabase_key', $this->supabase_key);

        if (empty($url) || empty($key)) {
            return;
        }

        $menu = wp_get_nav_menu_object($menu_id);
        $locations = get_nav_menu_locations();
        $location = '';

        foreach ($locations as $loc => $id) {
            if ($id == $menu_id) {
                $location = $loc;
                break;
            }
        }

        // Sync menu
        $menu_data = array(
            'id' => $menu_id,
            'name' => $menu->name,
            'location' => $location
        );
        $this->upsert_to_supabase($url, $key, 'menus', $menu_data);

        // Sync menu items
        $menu_items = wp_get_nav_menu_items($menu_id);
        if ($menu_items) {
            foreach ($menu_items as $item) {
                $item_data = array(
                    'id' => $item->ID,
                    'menu_id' => $menu_id,
                    'page_id' => $item->object_id,
                    'parent_id' => $item->menu_item_parent ? (int)$item->menu_item_parent : null,
                    'title' => $item->title,
                    'url' => $item->url,
                    'menu_order' => $item->menu_order
                );
                $this->upsert_to_supabase($url, $key, 'menu_items', $item_data);
            }
        }
    }

    private function upsert_to_supabase($url, $key, $table, $data) {
        $endpoint = rtrim($url, '/') . '/rest/v1/' . $table;

        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates'
            ),
            'body' => json_encode($data),
            'timeout' => 15
        ));

        if (is_wp_error($response)) {
            error_log('Supabase sync error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }

    private function delete_from_supabase($url, $key, $table, $id) {
        $endpoint = rtrim($url, '/') . '/rest/v1/' . $table . '?id=eq.' . $id;

        $response = wp_remote_request($endpoint, array(
            'method' => 'DELETE',
            'headers' => array(
                'apikey' => $key,
                'Authorization' => 'Bearer ' . $key,
            ),
            'timeout' => 15
        ));

        if (is_wp_error($response)) {
            error_log('Supabase delete error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }
}

// Initialize
new PremierPlug_Supabase_Sync();

// Handle manual sync
add_action('admin_post_supabase_manual_sync', function() {
    check_admin_referer('supabase_manual_sync');

    $sync = new PremierPlug_Supabase_Sync();
    $pages = get_pages(array('post_type' => 'page', 'post_status' => 'publish'));

    $count = 0;
    foreach ($pages as $page) {
        $sync->sync_page_to_supabase($page->ID, $page, true);
        $count++;
    }

    wp_redirect(add_query_arg(array(
        'page' => 'supabase-sync',
        'synced' => $count
    ), admin_url('options-general.php')));
    exit;
});
