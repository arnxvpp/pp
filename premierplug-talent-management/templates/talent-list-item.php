<?php
if (!defined('ABSPATH')) {
    exit;
}

$talent_id = get_the_ID();
$categories = wp_get_post_terms($talent_id, 'talent_category', array('fields' => 'names'));
$location = get_post_meta($talent_id, '_talent_location', true);
?>

<div class="pptm-talent-list-item">
    <?php if (has_post_thumbnail()): ?>
        <div class="pptm-talent-list-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('thumbnail'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="pptm-talent-list-content">
        <h4 class="pptm-talent-list-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h4>

        <?php if (!empty($categories)): ?>
            <div class="pptm-talent-list-categories">
                <?php echo esc_html(implode(', ', $categories)); ?>
            </div>
        <?php endif; ?>

        <?php if ($location): ?>
            <span class="pptm-talent-list-location">
                <?php echo esc_html($location); ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="pptm-talent-list-action">
        <a href="<?php the_permalink(); ?>" class="pptm-view-profile">View Profile</a>
    </div>
</div>
