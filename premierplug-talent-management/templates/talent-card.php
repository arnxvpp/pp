<?php
if (!defined('ABSPATH')) {
    exit;
}

$talent_id = get_the_ID();
$categories = wp_get_post_terms($talent_id, 'talent_category', array('fields' => 'names'));
$email = get_post_meta($talent_id, '_talent_email', true);
$phone = get_post_meta($talent_id, '_talent_phone', true);
$location = get_post_meta($talent_id, '_talent_location', true);
?>

<div class="pptm-talent-card">
    <?php if (has_post_thumbnail()): ?>
        <div class="pptm-talent-image">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('medium'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="pptm-talent-content">
        <h3 class="pptm-talent-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if (!empty($categories)): ?>
            <div class="pptm-talent-categories">
                <?php echo esc_html(implode(', ', $categories)); ?>
            </div>
        <?php endif; ?>

        <?php if (has_excerpt()): ?>
            <div class="pptm-talent-excerpt">
                <?php the_excerpt(); ?>
            </div>
        <?php endif; ?>

        <div class="pptm-talent-meta">
            <?php if ($location): ?>
                <span class="pptm-talent-location">
                    <i class="dashicons dashicons-location"></i>
                    <?php echo esc_html($location); ?>
                </span>
            <?php endif; ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="pptm-talent-link">View Profile</a>
    </div>
</div>
