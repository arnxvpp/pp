<?php
if (!defined('ABSPATH')) {
    exit;
}

$talent_id = isset($post) ? $post->ID : get_the_ID();
$email = get_post_meta($talent_id, '_talent_email', true);
$phone = get_post_meta($talent_id, '_talent_phone', true);
$website = get_post_meta($talent_id, '_talent_website', true);
$location = get_post_meta($talent_id, '_talent_location', true);
$experience = get_post_meta($talent_id, '_talent_experience', true);
$rate = get_post_meta($talent_id, '_talent_rate', true);
$linkedin = get_post_meta($talent_id, '_talent_linkedin', true);
$twitter = get_post_meta($talent_id, '_talent_twitter', true);
$instagram = get_post_meta($talent_id, '_talent_instagram', true);
$youtube = get_post_meta($talent_id, '_talent_youtube', true);

$categories = wp_get_post_terms($talent_id, 'talent_category', array('fields' => 'names'));
$skills = wp_get_post_terms($talent_id, 'talent_skill', array('fields' => 'names'));
?>

<div class="pptm-talent-single">
    <div class="pptm-talent-header">
        <?php if (has_post_thumbnail($talent_id)): ?>
            <div class="pptm-talent-photo">
                <?php echo get_the_post_thumbnail($talent_id, 'large'); ?>
            </div>
        <?php endif; ?>

        <div class="pptm-talent-header-content">
            <h1 class="pptm-talent-name"><?php echo get_the_title($talent_id); ?></h1>

            <?php if (!empty($categories)): ?>
                <div class="pptm-talent-categories">
                    <?php echo esc_html(implode(', ', $categories)); ?>
                </div>
            <?php endif; ?>

            <?php if ($location): ?>
                <div class="pptm-talent-location">
                    <i class="dashicons dashicons-location"></i>
                    <?php echo esc_html($location); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="pptm-talent-body">
        <div class="pptm-talent-main">
            <?php if (has_excerpt($talent_id)): ?>
                <div class="pptm-talent-excerpt">
                    <?php echo wpautop(get_the_excerpt($talent_id)); ?>
                </div>
            <?php endif; ?>

            <div class="pptm-talent-bio">
                <?php echo wpautop(get_post_field('post_content', $talent_id)); ?>
            </div>

            <?php if (!empty($skills)): ?>
                <div class="pptm-talent-skills">
                    <h3>Skills</h3>
                    <div class="pptm-skills-list">
                        <?php foreach ($skills as $skill): ?>
                            <span class="pptm-skill-tag"><?php echo esc_html($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="pptm-talent-sidebar">
            <div class="pptm-talent-info-box">
                <h3>Contact Information</h3>

                <?php if ($email): ?>
                    <div class="pptm-info-item">
                        <strong>Email:</strong>
                        <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($phone): ?>
                    <div class="pptm-info-item">
                        <strong>Phone:</strong>
                        <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_html($phone); ?></a>
                    </div>
                <?php endif; ?>

                <?php if ($website): ?>
                    <div class="pptm-info-item">
                        <strong>Website:</strong>
                        <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener"><?php echo esc_html($website); ?></a>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($experience || $rate): ?>
                <div class="pptm-talent-info-box">
                    <h3>Professional Details</h3>

                    <?php if ($experience): ?>
                        <div class="pptm-info-item">
                            <strong>Experience:</strong>
                            <?php echo esc_html($experience); ?> years
                        </div>
                    <?php endif; ?>

                    <?php if ($rate): ?>
                        <div class="pptm-info-item">
                            <strong>Rate:</strong>
                            <?php echo esc_html($rate); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($linkedin || $twitter || $instagram || $youtube): ?>
                <div class="pptm-talent-info-box">
                    <h3>Social Media</h3>
                    <div class="pptm-social-links">
                        <?php if ($linkedin): ?>
                            <a href="<?php echo esc_url($linkedin); ?>" target="_blank" rel="noopener" class="pptm-social-link">
                                LinkedIn
                            </a>
                        <?php endif; ?>

                        <?php if ($twitter): ?>
                            <a href="<?php echo esc_url($twitter); ?>" target="_blank" rel="noopener" class="pptm-social-link">
                                Twitter
                            </a>
                        <?php endif; ?>

                        <?php if ($instagram): ?>
                            <a href="<?php echo esc_url($instagram); ?>" target="_blank" rel="noopener" class="pptm-social-link">
                                Instagram
                            </a>
                        <?php endif; ?>

                        <?php if ($youtube): ?>
                            <a href="<?php echo esc_url($youtube); ?>" target="_blank" rel="noopener" class="pptm-social-link">
                                YouTube
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
