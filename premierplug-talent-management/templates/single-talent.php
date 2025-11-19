<?php
get_header();
?>

<div class="pptm-single-talent-wrapper">
    <?php while (have_posts()): the_post(); ?>
        <?php include PPTM_PLUGIN_DIR . 'templates/talent-single.php'; ?>
    <?php endwhile; ?>
</div>

<?php
get_footer();
