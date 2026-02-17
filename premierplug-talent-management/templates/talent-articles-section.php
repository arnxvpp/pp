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

        <?php
        foreach ($article_counts as $type_key => $count) :
            if ($type_key === 'all' || $count <= 0) continue;
            $type_obj = get_post_type_object($type_key);
            if (!$type_obj) continue;
        ?>
            <button class="pptm-tab-btn" data-type="<?php echo esc_attr($type_key); ?>">
                <?php echo esc_html($type_obj->labels->name); ?>
                <span class="pptm-tab-count"><?php echo esc_html($count); ?></span>
            </button>
        <?php endforeach; ?>
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
