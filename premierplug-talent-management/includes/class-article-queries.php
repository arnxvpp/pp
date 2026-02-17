<?php
/**
 * Article Queries Class
 *
 * Helper functions for querying articles and relationships
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Queries {

    /**
     * Get all article post types from custom post type manager
     */
    private static function get_all_article_post_types() {
        $custom_types = get_option('premierplug_custom_post_types', array());
        $article_types = array();

        if (isset($custom_types['articles']['items'])) {
            foreach ($custom_types['articles']['items'] as $type_data) {
                if (!empty($type_data['enabled'])) {
                    $article_types[] = 'article_' . $type_data['id'];
                }
            }
        }

        return !empty($article_types) ? $article_types : array('article_press_release');
    }

    /**
     * Get articles for a talent with filters
     *
     * @param int $talent_id Talent post ID
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_talent_articles($talent_id, $args = array()) {
        $defaults = array(
            'article_type' => '',
            'posts_per_page' => -1,
            'paged' => 1,
            'orderby' => 'date',
            'order' => 'DESC',
            'featured_only' => false,
        );

        $args = wp_parse_args($args, $defaults);

        $article_relationships = PPTM_Article_Relationships::get_talent_articles($talent_id, array(
            'article_type' => $args['article_type'],
        ));

        if (empty($article_relationships)) {
            return new WP_Query(array('post__in' => array(0)));
        }

        $article_ids = wp_list_pluck($article_relationships, 'article_id');

        $query_args = array(
            'post_type' => self::get_all_article_post_types(),
            'post__in' => $article_ids,
            'posts_per_page' => $args['posts_per_page'],
            'paged' => $args['paged'],
            'orderby' => $args['orderby'],
            'order' => $args['order'],
            'post_status' => 'publish',
        );

        if (!empty($args['article_type'])) {
            $query_args['post_type'] = $args['article_type'];
        }

        if ($args['featured_only']) {
            $query_args['meta_query'] = array(
                array(
                    'key' => '_pptm_is_featured',
                    'value' => '1',
                    'compare' => '=',
                ),
            );
        }

        return new WP_Query($query_args);
    }

    /**
     * Get articles count by type for a talent
     *
     * @param int $talent_id Talent post ID
     * @return array Article counts by type
     */
    public static function get_talent_articles_count_by_type($talent_id) {
        $counts = array('all' => 0);

        $article_types = self::get_all_article_post_types();

        foreach ($article_types as $type) {
            $count = PPTM_Article_Relationships::get_talent_article_count($talent_id, $type);
            $counts[$type] = $count;
            $counts['all'] += $count;
        }

        return $counts;
    }

    /**
     * Get recent articles across all types
     *
     * @param array $args Query arguments
     * @return WP_Query
     */
    public static function get_recent_articles($args = array()) {
        $defaults = array(
            'posts_per_page' => 10,
            'article_type' => '',
            'featured_only' => false,
        );

        $args = wp_parse_args($args, $defaults);

        $query_args = array(
            'post_type' => !empty($args['article_type']) ? $args['article_type'] : self::get_all_article_post_types(),
            'posts_per_page' => $args['posts_per_page'],
            'orderby' => 'date',
            'order' => 'DESC',
            'post_status' => 'publish',
        );

        if ($args['featured_only']) {
            $query_args['meta_query'] = array(
                array(
                    'key' => '_pptm_is_featured',
                    'value' => '1',
                    'compare' => '=',
                ),
            );
        }

        return new WP_Query($query_args);
    }

    /**
     * Get talents for an article
     *
     * @param int $article_id Article post ID
     * @return array Array of talent post objects
     */
    public static function get_article_talents($article_id) {
        $talent_relationships = PPTM_Article_Relationships::get_article_talents($article_id);

        if (empty($talent_relationships)) {
            return array();
        }

        $talent_ids = wp_list_pluck($talent_relationships, 'talent_id');

        $args = array(
            'post_type' => 'talent',
            'post__in' => $talent_ids,
            'posts_per_page' => -1,
            'orderby' => 'post__in',
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);

        return $query->posts;
    }

    /**
     * Get featured articles for homepage/widgets
     *
     * @param int $limit Number of articles
     * @return WP_Query
     */
    public static function get_featured_articles($limit = 5) {
        return self::get_recent_articles(array(
            'posts_per_page' => $limit,
            'featured_only' => true,
        ));
    }

    /**
     * Search articles with filters
     *
     * @param string $search_term Search term
     * @param array $args Additional arguments
     * @return WP_Query
     */
    public static function search_articles($search_term, $args = array()) {
        $defaults = array(
            'posts_per_page' => 20,
            'article_type' => '',
            'paged' => 1,
        );

        $args = wp_parse_args($args, $defaults);

        $query_args = array(
            'post_type' => !empty($args['article_type']) ? $args['article_type'] : self::get_all_article_post_types(),
            's' => sanitize_text_field($search_term),
            'posts_per_page' => $args['posts_per_page'],
            'paged' => $args['paged'],
            'post_status' => 'publish',
        );

        return new WP_Query($query_args);
    }

    /**
     * Get article statistics
     *
     * @return array Statistics array
     */
    public static function get_article_stats() {
        $stats = array(
            'total' => 0,
            'by_type' => array(),
            'featured' => 0,
            'with_talents' => 0,
        );

        $article_types = self::get_all_article_post_types();

        foreach ($article_types as $type) {
            $count = wp_count_posts($type);
            $published = $count->publish ?? 0;
            $stats['by_type'][$type] = $published;
            $stats['total'] += $published;
        }

        $featured_args = array(
            'post_type' => $article_types,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_pptm_is_featured',
                    'value' => '1',
                ),
            ),
            'fields' => 'ids',
        );

        $featured_query = new WP_Query($featured_args);
        $stats['featured'] = $featured_query->found_posts;

        global $wpdb;
        $table_name = $wpdb->prefix . 'talent_article_relationships';
        $stats['with_talents'] = $wpdb->get_var("SELECT COUNT(DISTINCT article_id) FROM $table_name");

        return $stats;
    }

    /**
     * Get related articles based on shared talents
     *
     * @param int $article_id Current article ID
     * @param int $limit Number of related articles
     * @return array Array of post objects
     */
    public static function get_related_articles($article_id, $limit = 5) {
        $talents = PPTM_Article_Relationships::get_article_talents($article_id);

        if (empty($talents)) {
            return array();
        }

        $talent_ids = wp_list_pluck($talents, 'talent_id');

        global $wpdb;
        $table_name = $wpdb->prefix . 'talent_article_relationships';

        $talent_ids = array_map('intval', $talent_ids);
        $placeholders = implode(',', array_fill(0, count($talent_ids), '%d'));

        $query_args = array_merge($talent_ids, array($article_id, $limit));

        $related_article_ids = $wpdb->get_col($wpdb->prepare(
            "SELECT DISTINCT article_id
            FROM $table_name
            WHERE talent_id IN ($placeholders)
            AND article_id != %d
            LIMIT %d",
            $query_args
        ));

        if (empty($related_article_ids)) {
            return array();
        }

        $args = array(
            'post_type' => self::get_all_article_post_types(),
            'post__in' => $related_article_ids,
            'posts_per_page' => $limit,
            'post_status' => 'publish',
        );

        $query = new WP_Query($args);

        return $query->posts;
    }
}
