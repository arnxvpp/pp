<?php
get_header();
?>

<div class="pptm-archive-talent-wrapper">
    <header class="pptm-archive-header">
        <h1 class="pptm-archive-title">
            <?php
            if (is_post_type_archive('talent')) {
                _e('All Talents', 'premierplug-talent');
            } elseif (is_tax('talent_category')) {
                single_term_title();
            } elseif (is_tax('talent_skill')) {
                single_term_title();
            }
            ?>
        </h1>

        <?php if (is_tax() && term_description()): ?>
            <div class="pptm-archive-description">
                <?php echo term_description(); ?>
            </div>
        <?php endif; ?>
    </header>

    <div class="pptm-talent-grid columns-3">
        <?php if (have_posts()): ?>
            <?php while (have_posts()): the_post(); ?>
                <?php include PPTM_PLUGIN_DIR . 'templates/talent-card.php'; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="pptm-no-results"><?php _e('No talents found.', 'premierplug-talent'); ?></p>
        <?php endif; ?>
    </div>

    <?php
    the_posts_pagination(array(
        'mid_size' => 2,
        'prev_text' => __('&laquo; Previous', 'premierplug-talent'),
        'next_text' => __('Next &raquo;', 'premierplug-talent'),
    ));
    ?>
</div>

<?php
get_footer();
