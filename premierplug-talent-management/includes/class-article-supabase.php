<?php
/**
 * Article Supabase Sync Class
 *
 * Handles syncing articles and relationships to Supabase database in real-time
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Supabase {

    /**
     * Supabase credentials
     */
    private static $supabase_url = null;
    private static $supabase_key = null;

    /**
     * Initialize the class
     */
    public static function init() {
        self::$supabase_url = defined('SUPABASE_URL') ? SUPABASE_URL : getenv('VITE_SUPABASE_URL');
        self::$supabase_key = defined('SUPABASE_KEY') ? SUPABASE_KEY : getenv('VITE_SUPABASE_ANON_KEY');

        if (self::is_configured()) {
            add_action('save_post', array(__CLASS__, 'sync_article_on_save'), 20, 2);
            add_action('delete_post', array(__CLASS__, 'delete_article_on_delete'), 10);
            add_action('pptm_talent_article_linked', array(__CLASS__, 'sync_relationship'), 10, 2);
            add_action('pptm_talent_article_unlinked', array(__CLASS__, 'delete_relationship'), 10, 2);
        }
    }

    /**
     * Check if Supabase is configured
     */
    public static function is_configured() {
        return !empty(self::$supabase_url) && !empty(self::$supabase_key);
    }

    /**
     * Sync article to Supabase on save
     */
    public static function sync_article_on_save($post_id, $post) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!PPTM_Article_Post_Types::is_article_type($post->post_type)) {
            return;
        }

        if ($post->post_status !== 'publish') {
            return;
        }

        $article_data = self::prepare_article_data($post);

        if (!$article_data) {
            return;
        }

        $result = self::upsert_article($article_data);

        if ($result) {
            error_log('PPTM: Article synced to Supabase - ID: ' . $post_id);
        } else {
            error_log('PPTM: Failed to sync article to Supabase - ID: ' . $post_id);
        }
    }

    /**
     * Prepare article data for Supabase
     */
    private static function prepare_article_data($post) {
        $thumbnail_url = '';
        if (has_post_thumbnail($post->ID)) {
            $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'large');
        }

        $publication_date = get_post_meta($post->ID, '_pptm_publication_date', true);
        $source_name = get_post_meta($post->ID, '_pptm_source_name', true);
        $source_url = get_post_meta($post->ID, '_pptm_source_url', true);
        $author_name = get_post_meta($post->ID, '_pptm_author_name', true);
        $is_featured = get_post_meta($post->ID, '_pptm_is_featured', true);

        return array(
            'wordpress_post_id' => $post->ID,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'article_type' => $post->post_type,
            'featured_image_url' => $thumbnail_url,
            'publication_date' => !empty($publication_date) ? $publication_date . 'T00:00:00Z' : null,
            'source_name' => $source_name,
            'source_url' => $source_url,
            'author_name' => $author_name,
            'is_featured' => ($is_featured === '1'),
            'slug' => $post->post_name,
            'status' => $post->post_status,
            'updated_at' => current_time('mysql', true),
        );
    }

    /**
     * Upsert article to Supabase
     */
    private static function upsert_article($article_data) {
        if (!self::is_configured()) {
            return false;
        }

        $url = self::$supabase_url . '/rest/v1/talent_articles';

        $response = wp_remote_post($url, array(
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates',
            ),
            'body' => wp_json_encode($article_data),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('PPTM Supabase Error: ' . $response->get_error_message());
            return false;
        }

        $status_code = wp_remote_retrieve_response_code($response);

        return in_array($status_code, array(200, 201));
    }

    /**
     * Delete article from Supabase
     */
    public static function delete_article_on_delete($post_id) {
        $post = get_post($post_id);

        if (!$post || !PPTM_Article_Post_Types::is_article_type($post->post_type)) {
            return;
        }

        if (!self::is_configured()) {
            return;
        }

        $url = self::$supabase_url . '/rest/v1/talent_articles?wordpress_post_id=eq.' . $post_id;

        $response = wp_remote_request($url, array(
            'method' => 'DELETE',
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('PPTM Supabase Delete Error: ' . $response->get_error_message());
            return false;
        }

        error_log('PPTM: Article deleted from Supabase - ID: ' . $post_id);

        return true;
    }

    /**
     * Sync relationship to Supabase
     */
    public static function sync_relationship($talent_id, $article_id) {
        if (!self::is_configured()) {
            return;
        }

        $article = get_post($article_id);
        if (!$article) {
            return;
        }

        $supabase_article_id = self::get_supabase_article_id($article_id);
        if (!$supabase_article_id) {
            error_log('PPTM: Could not find Supabase article ID for WordPress post ' . $article_id);
            return;
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'talent_article_relationships';

        $relationship = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE talent_id = %d AND article_id = %d",
            $talent_id,
            $article_id
        ), ARRAY_A);

        if (!$relationship) {
            return;
        }

        $relationship_data = array(
            'talent_id' => $talent_id,
            'article_id' => $supabase_article_id,
            'is_primary_talent' => (bool) $relationship['is_primary_talent'],
            'display_order' => (int) $relationship['display_order'],
        );

        $url = self::$supabase_url . '/rest/v1/talent_article_relationships';

        $response = wp_remote_post($url, array(
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates',
            ),
            'body' => wp_json_encode($relationship_data),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('PPTM Relationship Sync Error: ' . $response->get_error_message());
            return false;
        }

        error_log('PPTM: Relationship synced - Talent: ' . $talent_id . ', Article: ' . $article_id);

        return true;
    }

    /**
     * Delete relationship from Supabase
     */
    public static function delete_relationship($talent_id, $article_id) {
        if (!self::is_configured()) {
            return;
        }

        $supabase_article_id = self::get_supabase_article_id($article_id);
        if (!$supabase_article_id) {
            return;
        }

        $url = self::$supabase_url . '/rest/v1/talent_article_relationships?talent_id=eq.' . $talent_id . '&article_id=eq.' . $supabase_article_id;

        $response = wp_remote_request($url, array(
            'method' => 'DELETE',
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('PPTM Relationship Delete Error: ' . $response->get_error_message());
            return false;
        }

        error_log('PPTM: Relationship deleted - Talent: ' . $talent_id . ', Article: ' . $article_id);

        return true;
    }

    /**
     * Get Supabase article ID from WordPress post ID
     */
    private static function get_supabase_article_id($wordpress_post_id) {
        if (!self::is_configured()) {
            return false;
        }

        $url = self::$supabase_url . '/rest/v1/talent_articles?wordpress_post_id=eq.' . $wordpress_post_id . '&select=id';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!empty($data) && isset($data[0]['id'])) {
            return $data[0]['id'];
        }

        return false;
    }

    /**
     * Batch sync all articles to Supabase
     */
    public static function batch_sync_articles() {
        if (!self::is_configured()) {
            return array('success' => false, 'message' => 'Supabase not configured');
        }

        $article_types = PPTM_Article_Post_Types::get_article_types();

        $args = array(
            'post_type' => $article_types,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
        );

        $query = new WP_Query($args);
        $synced = 0;
        $failed = 0;

        if ($query->have_posts()) {
            foreach ($query->posts as $post_id) {
                $post = get_post($post_id);
                $article_data = self::prepare_article_data($post);

                if ($article_data && self::upsert_article($article_data)) {
                    $synced++;
                } else {
                    $failed++;
                }
            }
        }

        return array(
            'success' => true,
            'synced' => $synced,
            'failed' => $failed,
        );
    }

    /**
     * Batch sync all relationships to Supabase
     */
    public static function batch_sync_relationships() {
        if (!self::is_configured()) {
            return array('success' => false, 'message' => 'Supabase not configured');
        }

        global $wpdb;
        $table_name = $wpdb->prefix . 'talent_article_relationships';

        $relationships = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

        $synced = 0;
        $failed = 0;

        foreach ($relationships as $rel) {
            if (self::sync_relationship($rel['talent_id'], $rel['article_id'])) {
                $synced++;
            } else {
                $failed++;
            }
        }

        return array(
            'success' => true,
            'synced' => $synced,
            'failed' => $failed,
        );
    }

    /**
     * Test Supabase connection
     */
    public static function test_connection() {
        if (!self::is_configured()) {
            return array('success' => false, 'message' => 'Supabase credentials not configured');
        }

        $url = self::$supabase_url . '/rest/v1/talent_articles?limit=1';

        $response = wp_remote_get($url, array(
            'headers' => array(
                'apikey' => self::$supabase_key,
                'Authorization' => 'Bearer ' . self::$supabase_key,
            ),
            'timeout' => 10,
        ));

        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'message' => 'Connection failed: ' . $response->get_error_message()
            );
        }

        $status_code = wp_remote_retrieve_response_code($response);

        if ($status_code === 200) {
            return array('success' => true, 'message' => 'Connection successful');
        } else {
            return array(
                'success' => false,
                'message' => 'Connection failed with status code: ' . $status_code
            );
        }
    }
}
