<?php
/**
 * Article Relationships Class
 *
 * Manages many-to-many relationships between talents and articles
 * Creates and manages custom database table for relationships
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Relationships {

    /**
     * Table name
     */
    private static $table_name = null;

    /**
     * Initialize the class
     */
    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'talent_article_relationships';

        add_action('delete_post', array(__CLASS__, 'cleanup_relationships'));
        add_action('trashed_post', array(__CLASS__, 'cleanup_relationships'));
    }

    /**
     * Create database table on plugin activation
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'talent_article_relationships';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            talent_id BIGINT UNSIGNED NOT NULL,
            article_id BIGINT UNSIGNED NOT NULL,
            is_primary_talent TINYINT(1) DEFAULT 0,
            display_order INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY unique_relationship (talent_id, article_id),
            KEY idx_talent (talent_id),
            KEY idx_article (article_id),
            KEY idx_primary (is_primary_talent)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    /**
     * Link a talent to an article
     *
     * @param int $talent_id Talent post ID
     * @param int $article_id Article post ID
     * @param bool $is_primary Whether this is the primary talent
     * @param int $order Display order
     * @return int|false Insert ID or false on failure
     */
    public static function link_talent_to_article($talent_id, $article_id, $is_primary = false, $order = 0) {
        global $wpdb;

        if (!self::validate_ids($talent_id, $article_id)) {
            return false;
        }

        $data = array(
            'talent_id' => $talent_id,
            'article_id' => $article_id,
            'is_primary_talent' => $is_primary ? 1 : 0,
            'display_order' => $order,
        );

        $format = array('%d', '%d', '%d', '%d');

        $result = $wpdb->insert(self::$table_name, $data, $format);

        if ($result) {
            do_action('pptm_talent_article_linked', $talent_id, $article_id);
            return $wpdb->insert_id;
        }

        return false;
    }

    /**
     * Unlink a talent from an article
     *
     * @param int $talent_id Talent post ID
     * @param int $article_id Article post ID
     * @return bool True on success, false on failure
     */
    public static function unlink_talent_from_article($talent_id, $article_id) {
        global $wpdb;

        $result = $wpdb->delete(
            self::$table_name,
            array(
                'talent_id' => $talent_id,
                'article_id' => $article_id,
            ),
            array('%d', '%d')
        );

        if ($result) {
            do_action('pptm_talent_article_unlinked', $talent_id, $article_id);
            return true;
        }

        return false;
    }

    /**
     * Get all talents linked to an article
     *
     * @param int $article_id Article post ID
     * @param array $args Optional query arguments
     * @return array Array of talent IDs with relationship data
     */
    public static function get_article_talents($article_id, $args = array()) {
        global $wpdb;

        $defaults = array(
            'orderby' => 'display_order',
            'order' => 'ASC',
            'primary_first' => true,
        );

        $args = wp_parse_args($args, $defaults);

        $order_clause = $args['primary_first']
            ? 'ORDER BY is_primary_talent DESC, display_order ASC'
            : "ORDER BY {$args['orderby']} {$args['order']}";

        $query = $wpdb->prepare(
            "SELECT talent_id, is_primary_talent, display_order
            FROM " . self::$table_name . "
            WHERE article_id = %d
            $order_clause",
            $article_id
        );

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Get all articles linked to a talent
     *
     * @param int $talent_id Talent post ID
     * @param array $args Optional query arguments
     * @return array Array of article IDs with relationship data
     */
    public static function get_talent_articles($talent_id, $args = array()) {
        global $wpdb;

        $defaults = array(
            'article_type' => '',
            'limit' => -1,
            'offset' => 0,
            'orderby' => 'created_at',
            'order' => 'DESC',
        );

        $args = wp_parse_args($args, $defaults);

        $where_clause = $wpdb->prepare('WHERE r.talent_id = %d', $talent_id);

        if (!empty($args['article_type'])) {
            $where_clause .= $wpdb->prepare(' AND p.post_type = %s', $args['article_type']);
        }

        $limit_clause = '';
        if ($args['limit'] > 0) {
            $limit_clause = $wpdb->prepare('LIMIT %d OFFSET %d', $args['limit'], $args['offset']);
        }

        $query = "SELECT r.article_id, r.is_primary_talent, r.display_order, p.post_type, p.post_date
                  FROM " . self::$table_name . " r
                  INNER JOIN {$wpdb->posts} p ON r.article_id = p.ID
                  $where_clause
                  AND p.post_status = 'publish'
                  ORDER BY r.{$args['orderby']} {$args['order']}
                  $limit_clause";

        return $wpdb->get_results($query, ARRAY_A);
    }

    /**
     * Set primary talent for an article
     *
     * @param int $article_id Article post ID
     * @param int $talent_id Talent post ID to set as primary
     * @return bool True on success, false on failure
     */
    public static function set_primary_talent($article_id, $talent_id) {
        global $wpdb;

        $wpdb->update(
            self::$table_name,
            array('is_primary_talent' => 0),
            array('article_id' => $article_id),
            array('%d'),
            array('%d')
        );

        $result = $wpdb->update(
            self::$table_name,
            array('is_primary_talent' => 1),
            array(
                'article_id' => $article_id,
                'talent_id' => $talent_id,
            ),
            array('%d'),
            array('%d', '%d')
        );

        return $result !== false;
    }

    /**
     * Update display order for talents in an article
     *
     * @param int $article_id Article post ID
     * @param array $talent_order Array of talent IDs in desired order
     * @return bool True on success, false on failure
     */
    public static function update_display_order($article_id, $talent_order) {
        global $wpdb;

        if (empty($talent_order) || !is_array($talent_order)) {
            return false;
        }

        foreach ($talent_order as $order => $talent_id) {
            $wpdb->update(
                self::$table_name,
                array('display_order' => $order),
                array(
                    'article_id' => $article_id,
                    'talent_id' => $talent_id,
                ),
                array('%d'),
                array('%d', '%d')
            );
        }

        return true;
    }

    /**
     * Check if a talent is linked to an article
     *
     * @param int $talent_id Talent post ID
     * @param int $article_id Article post ID
     * @return bool True if linked, false otherwise
     */
    public static function is_linked($talent_id, $article_id) {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM " . self::$table_name . "
            WHERE talent_id = %d AND article_id = %d",
            $talent_id,
            $article_id
        ));

        return $count > 0;
    }

    /**
     * Get count of articles for a talent
     *
     * @param int $talent_id Talent post ID
     * @param string $article_type Optional article type filter
     * @return int Number of articles
     */
    public static function get_talent_article_count($talent_id, $article_type = '') {
        global $wpdb;

        $where_clause = $wpdb->prepare('WHERE r.talent_id = %d', $talent_id);

        if (!empty($article_type)) {
            $where_clause .= $wpdb->prepare(' AND p.post_type = %s', $article_type);
        }

        $query = "SELECT COUNT(*)
                  FROM " . self::$table_name . " r
                  INNER JOIN {$wpdb->posts} p ON r.article_id = p.ID
                  $where_clause
                  AND p.post_status = 'publish'";

        return (int) $wpdb->get_var($query);
    }

    /**
     * Get count of talents for an article
     *
     * @param int $article_id Article post ID
     * @return int Number of talents
     */
    public static function get_article_talent_count($article_id) {
        global $wpdb;

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM " . self::$table_name . "
            WHERE article_id = %d",
            $article_id
        ));

        return (int) $count;
    }

    /**
     * Cleanup relationships when a post is deleted
     *
     * @param int $post_id Post ID being deleted
     */
    public static function cleanup_relationships($post_id) {
        global $wpdb;

        $post_type = get_post_type($post_id);

        if ($post_type === 'talent') {
            $wpdb->delete(
                self::$table_name,
                array('talent_id' => $post_id),
                array('%d')
            );
        } elseif (PPTM_Article_Post_Types::is_article_type($post_type)) {
            $wpdb->delete(
                self::$table_name,
                array('article_id' => $post_id),
                array('%d')
            );
        }
    }

    /**
     * Validate talent and article IDs
     *
     * @param int $talent_id Talent post ID
     * @param int $article_id Article post ID
     * @return bool True if valid, false otherwise
     */
    private static function validate_ids($talent_id, $article_id) {
        $talent = get_post($talent_id);
        $article = get_post($article_id);

        if (!$talent || $talent->post_type !== 'talent') {
            return false;
        }

        if (!$article || !PPTM_Article_Post_Types::is_article_type($article->post_type)) {
            return false;
        }

        return true;
    }

    /**
     * Get primary talent for an article
     *
     * @param int $article_id Article post ID
     * @return int|false Primary talent ID or false if none
     */
    public static function get_primary_talent($article_id) {
        global $wpdb;

        $talent_id = $wpdb->get_var($wpdb->prepare(
            "SELECT talent_id FROM " . self::$table_name . "
            WHERE article_id = %d AND is_primary_talent = 1",
            $article_id
        ));

        return $talent_id ? (int) $talent_id : false;
    }

    /**
     * Batch link talents to an article
     *
     * @param int $article_id Article post ID
     * @param array $talent_ids Array of talent IDs
     * @param int $primary_talent_id Optional primary talent ID
     * @return bool True on success, false on failure
     */
    public static function batch_link_talents($article_id, $talent_ids, $primary_talent_id = 0) {
        if (empty($talent_ids) || !is_array($talent_ids)) {
            return false;
        }

        foreach ($talent_ids as $order => $talent_id) {
            $is_primary = ($talent_id == $primary_talent_id);
            self::link_talent_to_article($talent_id, $article_id, $is_primary, $order);
        }

        return true;
    }

    /**
     * Replace all talents for an article
     *
     * @param int $article_id Article post ID
     * @param array $talent_ids Array of talent IDs
     * @param int $primary_talent_id Optional primary talent ID
     * @return bool True on success, false on failure
     */
    public static function replace_article_talents($article_id, $talent_ids, $primary_talent_id = 0) {
        global $wpdb;

        $wpdb->delete(
            self::$table_name,
            array('article_id' => $article_id),
            array('%d')
        );

        return self::batch_link_talents($article_id, $talent_ids, $primary_talent_id);
    }
}
