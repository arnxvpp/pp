<?php
/**
 * Article Shortcodes Class
 *
 * Provides shortcodes for displaying articles on the frontend
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Shortcodes {

    /**
     * Initialize the class
     */
    public static function init() {
        add_shortcode('talent_articles', array(__CLASS__, 'talent_articles_shortcode'));
        add_shortcode('recent_articles', array(__CLASS__, 'recent_articles_shortcode'));
        add_shortcode('article_grid', array(__CLASS__, 'article_grid_shortcode'));
        add_shortcode('featured_articles', array(__CLASS__, 'featured_articles_shortcode'));

        add_action('wp_ajax_pptm_load_talent_articles', array(__CLASS__, 'ajax_load_talent_articles'));
        add_action('wp_ajax_nopriv_pptm_load_talent_articles', array(__CLASS__, 'ajax_load_talent_articles'));
    }

    /**
     * Talent articles shortcode
     * Usage: [talent_articles id="123" type="press_release" limit="10"]
     */
    public static function talent_articles_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => get_the_ID(),
            'type' => '',
            'limit' => -1,
            'featured_only' => false,
        ), $atts);

        $talent_id = intval($atts['id']);

        if (!$talent_id) {
            return '<p>' . esc_html__('Invalid talent ID', 'premierplug-talent') . '</p>';
        }

        $query = PPTM_Article_Queries::get_talent_articles($talent_id, array(
            'article_type' => $atts['type'],
            'posts_per_page' => intval($atts['limit']),
            'featured_only' => filter_var($atts['featured_only'], FILTER_VALIDATE_BOOLEAN),
        ));

        if (!$query->have_posts()) {
            return '<p class="pptm-no-articles">' . esc_html__('No articles found.', 'premierplug-talent') . '</p>';
        }

        ob_start();
        ?>
        <div class="pptm-articles-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php self::render_article_card(get_post()); ?>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Recent articles shortcode
     * Usage: [recent_articles limit="5" type="blog_article"]
     */
    public static function recent_articles_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 10,
            'type' => '',
            'featured_only' => false,
        ), $atts);

        $query = PPTM_Article_Queries::get_recent_articles(array(
            'posts_per_page' => intval($atts['limit']),
            'article_type' => $atts['type'],
            'featured_only' => filter_var($atts['featured_only'], FILTER_VALIDATE_BOOLEAN),
        ));

        if (!$query->have_posts()) {
            return '<p class="pptm-no-articles">' . esc_html__('No articles found.', 'premierplug-talent') . '</p>';
        }

        ob_start();
        ?>
        <div class="pptm-articles-list">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <article class="pptm-article-list-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="pptm-article-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="pptm-article-content">
                        <span class="pptm-article-type-badge">
                            <?php echo esc_html(PPTM_Article_Post_Types::get_type_label(get_post_type())); ?>
                        </span>
                        <h3 class="pptm-article-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="pptm-article-meta">
                            <span class="pptm-article-date"><?php echo get_the_date(); ?></span>
                        </div>
                        <div class="pptm-article-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="pptm-article-link">
                            <?php esc_html_e('Read More', 'premierplug-talent'); ?> &rarr;
                        </a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Article grid shortcode
     * Usage: [article_grid type="press_release" limit="12" columns="3"]
     */
    public static function article_grid_shortcode($atts) {
        $atts = shortcode_atts(array(
            'type' => '',
            'limit' => 12,
            'columns' => 3,
            'featured_only' => false,
        ), $atts);

        $query = PPTM_Article_Queries::get_recent_articles(array(
            'posts_per_page' => intval($atts['limit']),
            'article_type' => $atts['type'],
            'featured_only' => filter_var($atts['featured_only'], FILTER_VALIDATE_BOOLEAN),
        ));

        if (!$query->have_posts()) {
            return '<p class="pptm-no-articles">' . esc_html__('No articles found.', 'premierplug-talent') . '</p>';
        }

        $columns = intval($atts['columns']);
        $columns_class = 'pptm-grid-columns-' . $columns;

        ob_start();
        ?>
        <div class="pptm-articles-grid <?php echo esc_attr($columns_class); ?>">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php self::render_article_card(get_post()); ?>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Featured articles shortcode
     * Usage: [featured_articles limit="5"]
     */
    public static function featured_articles_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 5,
        ), $atts);

        $query = PPTM_Article_Queries::get_featured_articles(intval($atts['limit']));

        if (!$query->have_posts()) {
            return '<p class="pptm-no-articles">' . esc_html__('No featured articles found.', 'premierplug-talent') . '</p>';
        }

        ob_start();
        ?>
        <div class="pptm-featured-articles">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php self::render_article_card(get_post(), true); ?>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();

        return ob_get_clean();
    }

    /**
     * Render article card (reusable)
     */
    private static function render_article_card($post, $is_featured = false) {
        $is_featured = $is_featured || get_post_meta($post->ID, '_pptm_is_featured', true) === '1';
        $source_name = get_post_meta($post->ID, '_pptm_source_name', true);
        ?>
        <article class="pptm-article-card <?php echo $is_featured ? 'is-featured' : ''; ?>">
            <?php if (has_post_thumbnail($post)) : ?>
                <div class="pptm-article-card-image">
                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                        <?php echo get_the_post_thumbnail($post, 'medium'); ?>
                    </a>
                    <?php if ($is_featured) : ?>
                        <span class="pptm-featured-badge"><?php esc_html_e('Featured', 'premierplug-talent'); ?></span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="pptm-article-card-content">
                <div class="pptm-article-card-meta">
                    <span class="pptm-article-type">
                        <?php echo esc_html(PPTM_Article_Post_Types::get_type_label($post->post_type)); ?>
                    </span>
                    <span class="pptm-article-date"><?php echo get_the_date('', $post); ?></span>
                </div>
                <h3 class="pptm-article-card-title">
                    <a href="<?php echo esc_url(get_permalink($post)); ?>">
                        <?php echo esc_html($post->post_title); ?>
                    </a>
                </h3>
                <?php if (!empty($post->post_excerpt)) : ?>
                    <div class="pptm-article-card-excerpt">
                        <?php echo wp_kses_post(wp_trim_words($post->post_excerpt, 20)); ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($source_name)) : ?>
                    <div class="pptm-article-source">
                        <strong><?php esc_html_e('Source:', 'premierplug-talent'); ?></strong>
                        <?php echo esc_html($source_name); ?>
                    </div>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }

    /**
     * AJAX load talent articles
     */
    public static function ajax_load_talent_articles() {
        $talent_id = isset($_POST['talent_id']) ? intval($_POST['talent_id']) : 0;
        $article_type = isset($_POST['article_type']) ? sanitize_text_field($_POST['article_type']) : '';

        if (!$talent_id) {
            wp_send_json_error('Invalid talent ID');
        }

        $query = PPTM_Article_Queries::get_talent_articles($talent_id, array(
            'article_type' => $article_type,
            'posts_per_page' => -1,
        ));

        if (!$query->have_posts()) {
            wp_send_json_success(array(
                'html' => '<p class="pptm-no-articles">' . esc_html__('No articles found.', 'premierplug-talent') . '</p>'
            ));
        }

        ob_start();
        while ($query->have_posts()) {
            $query->the_post();
            self::render_article_card(get_post());
        }
        wp_reset_postdata();

        $html = ob_get_clean();

        wp_send_json_success(array('html' => $html));
    }
}
