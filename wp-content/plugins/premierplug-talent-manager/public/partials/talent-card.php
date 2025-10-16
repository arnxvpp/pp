<?php
/**
 * Talent Card Partial
 * Displays individual talent card in grid
 */

$talent_id = get_the_ID();
$headline = get_post_meta($talent_id, '_pptm_headline', true);
$availability = get_post_meta($talent_id, '_pptm_availability_status', true);
$experience_years = get_post_meta($talent_id, '_pptm_experience_years', true);

$segments = get_the_terms($talent_id, 'talent_segment');
$primary_segment = $segments && !is_wp_error($segments) ? $segments[0]->name : '';

$availability_class = 'available';
$availability_text = __('Available', 'premierplug-talent');

if ($availability === 'booked') {
    $availability_class = 'booked';
    $availability_text = __('Booked', 'premierplug-talent');
} elseif ($availability === 'unavailable') {
    $availability_class = 'unavailable';
    $availability_text = __('Unavailable', 'premierplug-talent');
}
?>

<div class="pptm-talent-card" data-aos="fade-up">
    <div class="talent-card-inner">
        <div class="talent-card-image">
            <a href="<?php the_permalink(); ?>">
                <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('medium', array('class' => 'talent-photo'));
                } else {
                    echo '<div class="talent-photo-placeholder"><span class="dashicons dashicons-admin-users"></span></div>';
                }
                ?>
            </a>
            <?php if (get_post_meta($talent_id, '_pptm_featured', true)) : ?>
                <span class="featured-badge" title="<?php esc_attr_e('Featured Talent', 'premierplug-talent'); ?>">
                    ★
                </span>
            <?php endif; ?>
        </div>

        <div class="talent-card-content">
            <?php if ($primary_segment) : ?>
                <span class="talent-segment-badge"><?php echo esc_html($primary_segment); ?></span>
            <?php endif; ?>

            <h3 class="talent-name">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>

            <?php if ($headline) : ?>
                <p class="talent-headline"><?php echo esc_html($headline); ?></p>
            <?php endif; ?>

            <?php if (has_excerpt()) : ?>
                <div class="talent-excerpt">
                    <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
                </div>
            <?php endif; ?>

            <div class="talent-card-meta">
                <?php if ($experience_years) : ?>
                    <span class="talent-experience">
                        <?php echo sprintf(_n('%d Year', '%d Years', $experience_years, 'premierplug-talent'), $experience_years); ?>
                    </span>
                <?php endif; ?>

                <span class="talent-availability <?php echo esc_attr($availability_class); ?>">
                    <span class="status-indicator"></span>
                    <?php echo esc_html($availability_text); ?>
                </span>
            </div>

            <a href="<?php the_permalink(); ?>" class="btn btn-talent-view">
                <?php _e('View Profile', 'premierplug-talent'); ?>
            </a>
        </div>
    </div>
</div>
