<?php
/**
 * Talent Articles Section Template
 *
 * Displays tabbed articles section on talent single page
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

$talent_id = get_the_ID();
$article_counts = PPTM_Article_Queries::get_talent_articles_count_by_type($talent_id);

if ($article_counts['all'] === 0) {
    return;
}
?>

<section class="pptm-talent-articles-section">
    <h2 class="pptm-section-title"><?php esc_html_e('Latest Updates & Coverage', 'premierplug-talent'); ?></h2>

    <div class="pptm-article-tabs" data-talent-id="<?php echo esc_attr($talent_id); ?>">
        <button class="pptm-tab-btn active" data-type="all">
            <?php esc_html_e('All', 'premierplug-talent'); ?>
            <span class="pptm-tab-count"><?php echo esc_html($article_counts['all']); ?></span>
        </button>

        <?php if ($article_counts['press_release'] > 0) : ?>
            <button class="pptm-tab-btn" data-type="press_release">
                <?php esc_html_e('Press Releases', 'premierplug-talent'); ?>
                <span class="pptm-tab-count"><?php echo esc_html($article_counts['press_release']); ?></span>
            </button>
        <?php endif; ?>

        <?php if ($article_counts['blog_article'] > 0) : ?>
            <button class="pptm-tab-btn" data-type="blog_article">
                <?php esc_html_e('Blog', 'premierplug-talent'); ?>
                <span class="pptm-tab-count"><?php echo esc_html($article_counts['blog_article']); ?></span>
            </button>
        <?php endif; ?>

        <?php if ($article_counts['award'] > 0) : ?>
            <button class="pptm-tab-btn" data-type="award">
                <?php esc_html_e('Awards', 'premierplug-talent'); ?>
                <span class="pptm-tab-count"><?php echo esc_html($article_counts['award']); ?></span>
            </button>
        <?php endif; ?>

        <?php if ($article_counts['news'] > 0) : ?>
            <button class="pptm-tab-btn" data-type="news">
                <?php esc_html_e('News', 'premierplug-talent'); ?>
                <span class="pptm-tab-count"><?php echo esc_html($article_counts['news']); ?></span>
            </button>
        <?php endif; ?>

        <?php if ($article_counts['media_coverage'] > 0) : ?>
            <button class="pptm-tab-btn" data-type="media_coverage">
                <?php esc_html_e('Media Coverage', 'premierplug-talent'); ?>
                <span class="pptm-tab-count"><?php echo esc_html($article_counts['media_coverage']); ?></span>
            </button>
        <?php endif; ?>
    </div>

    <div class="pptm-articles-grid-container">
        <div class="pptm-articles-grid" id="pptm-talent-articles-grid">
            <?php echo do_shortcode('[talent_articles id="' . $talent_id . '"]'); ?>
        </div>
    </div>

    <div class="pptm-loading" style="display: none;">
        <span class="spinner is-active"></span>
        <p><?php esc_html_e('Loading articles...', 'premierplug-talent'); ?></p>
    </div>
</section>
