<?php
/**
 * Article Card Template
 *
 * Reusable card component for displaying articles
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

global $post;

$is_featured = get_post_meta($post->ID, '_pptm_is_featured', true) === '1';
$source_name = get_post_meta($post->ID, '_pptm_source_name', true);
$publication_date = get_post_meta($post->ID, '_pptm_publication_date', true);

$display_date = !empty($publication_date) ? date_i18n(get_option('date_format'), strtotime($publication_date)) : get_the_date();
?>

<article class="pptm-article-card <?php echo $is_featured ? 'is-featured' : ''; ?>">
    <?php if (has_post_thumbnail()) : ?>
        <div class="pptm-article-card-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
            <?php if ($is_featured) : ?>
                <span class="pptm-featured-badge">
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php esc_html_e('Featured', 'premierplug-talent'); ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="pptm-article-card-content">
        <div class="pptm-article-card-meta">
            <span class="pptm-article-type-badge pptm-type-<?php echo esc_attr(get_post_type()); ?>">
                <?php echo esc_html(PPTM_Article_Post_Types::get_type_label(get_post_type())); ?>
            </span>
            <span class="pptm-article-date">
                <span class="dashicons dashicons-calendar-alt"></span>
                <?php echo esc_html($display_date); ?>
            </span>
        </div>

        <h3 class="pptm-article-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if (has_excerpt()) : ?>
            <div class="pptm-article-card-excerpt">
                <?php echo wp_kses_post(wp_trim_words(get_the_excerpt(), 20)); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($source_name)) : ?>
            <div class="pptm-article-source">
                <span class="dashicons dashicons-admin-links"></span>
                <strong><?php esc_html_e('Source:', 'premierplug-talent'); ?></strong>
                <?php echo esc_html($source_name); ?>
            </div>
        <?php endif; ?>

        <div class="pptm-article-card-footer">
            <a href="<?php the_permalink(); ?>" class="pptm-read-more">
                <?php esc_html_e('Read More', 'premierplug-talent'); ?>
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </a>
        </div>
    </div>
</article>
