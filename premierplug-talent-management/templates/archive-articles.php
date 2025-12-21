<?php
/**
 * Archive Articles Template
 *
 * Archive page template for article post types
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

get_header();
?>

<main class="pptm-articles-archive">
    <div class="pptm-archive-header">
        <h1 class="pptm-archive-title">
            <?php
            if (is_post_type_archive()) {
                post_type_archive_title();
            } else {
                the_archive_title();
            }
            ?>
        </h1>
        <?php if (is_post_type_archive() && get_the_archive_description()) : ?>
            <div class="pptm-archive-description">
                <?php the_archive_description(); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if (have_posts()) : ?>
        <div class="pptm-articles-grid pptm-grid-columns-3">
            <?php while (have_posts()) : the_post(); ?>
                <?php include(plugin_dir_path(__FILE__) . 'article-card.php'); ?>
            <?php endwhile; ?>
        </div>

        <div class="pptm-pagination">
            <?php
            the_posts_pagination(array(
                'mid_size' => 2,
                'prev_text' => __('&larr; Previous', 'premierplug-talent'),
                'next_text' => __('Next &rarr;', 'premierplug-talent'),
            ));
            ?>
        </div>
    <?php else : ?>
        <div class="pptm-no-results">
            <p><?php esc_html_e('No articles found.', 'premierplug-talent'); ?></p>
        </div>
    <?php endif; ?>
</main>

<?php
get_footer();
