<?php
/**
 * Single Talent Template
 * Matches existing PremierPlug design patterns
 */

get_header();

while (have_posts()) : the_post();

    $talent_id = get_the_ID();
    $headline = get_post_meta($talent_id, '_pptm_headline', true);
    $experience_years = get_post_meta($talent_id, '_pptm_experience_years', true);
    $availability = get_post_meta($talent_id, '_pptm_availability_status', true);

    $segments = get_the_terms($talent_id, 'talent_segment');
    $skills = get_the_terms($talent_id, 'talent_skill');

    $contact_email = get_post_meta($talent_id, '_pptm_contact_email', true);
    $contact_phone = get_post_meta($talent_id, '_pptm_contact_phone', true);
    $website = get_post_meta($talent_id, '_pptm_website', true);
    $instagram = get_post_meta($talent_id, '_pptm_social_instagram', true);
    $linkedin = get_post_meta($talent_id, '_pptm_social_linkedin', true);
    $twitter = get_post_meta($talent_id, '_pptm_social_twitter', true);
    $youtube = get_post_meta($talent_id, '_pptm_social_youtube', true);

    $portfolio_items = get_post_meta($talent_id, '_pptm_portfolio_items', true);
    if (!is_array($portfolio_items)) {
        $portfolio_items = array();
    }

    $hero_image = has_post_thumbnail() ? get_the_post_thumbnail_url($talent_id, 'full') : '';
?>

<div class="layout-container">

    <!-- Hero Section with Talent Image -->
    <section class="hero-container full_vh var3 bg-black" id="talent-profile-hero">
        <div class="hero-text-container vertical-align">
            <div class="gutter-container">
                <?php if ($segments && !is_wp_error($segments)) : ?>
                    <header>
                        <div class="h8" data-aos="fade-up" data-aos-once="true" data-aos-delay="600">
                            <?php echo esc_html($segments[0]->name); ?>
                        </div>
                    </header>
                <?php endif; ?>

                <hr class="prehead" data-aos="fade-up" data-aos-delay="600" data-aos-once="true" />

                <h1 class="headline-md" data-aos="fade-up" data-aos-offset="0" data-aos-delay="600" data-aos-once="true">
                    <?php the_title(); ?>
                </h1>

                <?php if ($headline) : ?>
                    <h5 class="" data-aos="fade-up" data-aos-delay="700" data-aos-once="true">
                        <?php echo esc_html($headline); ?>
                    </h5>
                <?php endif; ?>

                <a href="#contact-talent" class="btn btn-hero" data-aos="fade-up" data-aos-delay="800" data-aos-once="true">
                    <?php _e('Contact This Talent', 'premierplug-talent'); ?>
                </a>
            </div>
        </div>

        <?php if ($hero_image) : ?>
            <div class="preloader">
                <img src="<?php echo esc_url($hero_image); ?>" alt="" role="presentation">
            </div>
            <div class="hero-image-container hero-mask single-hero-image">
                <img src="<?php echo esc_url($hero_image); ?>"
                     data-desktop="<?php echo esc_url($hero_image); ?>"
                     data-mobile="<?php echo esc_url($hero_image); ?>"
                     alt=" "
                     role="presentation" />
            </div>
        <?php endif; ?>
    </section>

    <!-- About Section -->
    <?php if (get_the_content()) : ?>
    <section class="jumbo-text text-module pad-tb bg-red col-2" id="talent-about">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade-up">
                    <?php _e('About', 'premierplug-talent'); ?>
                </div>
            </header>

            <div class="content-container">
                <div class="offset-article">
                    <div class="jumbo-text-body" data-aos="fade-up" data-aos-delay="200">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Experience & Skills Section -->
    <?php if ($experience_years || ($skills && !is_wp_error($skills))) : ?>
    <section class="jumbo-text text-module pad-tb bg-white col-2" id="talent-experience">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade-up">
                    <?php _e('Experience & Skills', 'premierplug-talent'); ?>
                </div>
            </header>

            <div class="content-container">
                <div class="offset-article">
                    <?php if ($experience_years) : ?>
                        <div class="talent-experience-block" data-aos="fade-up">
                            <h6><?php _e('Years of Experience', 'premierplug-talent'); ?></h6>
                            <p class="experience-number"><?php echo absint($experience_years); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($skills && !is_wp_error($skills)) : ?>
                        <div class="talent-skills-block" data-aos="fade-up" data-aos-delay="200">
                            <h6><?php _e('Skills & Specializations', 'premierplug-talent'); ?></h6>
                            <div class="skills-list">
                                <?php foreach ($skills as $skill) : ?>
                                    <span class="skill-badge"><?php echo esc_html($skill->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($segments && !is_wp_error($segments)) : ?>
                        <div class="talent-segments-block" data-aos="fade-up" data-aos-delay="300">
                            <h6><?php _e('Segments', 'premierplug-talent'); ?></h6>
                            <div class="segments-list">
                                <?php foreach ($segments as $segment) : ?>
                                    <span class="segment-badge"><?php echo esc_html($segment->name); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Portfolio Section -->
    <?php if (!empty($portfolio_items)) : ?>
    <section class="image-grid image-grid-row pad-tb bg-white" id="talent-portfolio">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade-up">
                    <?php _e('Portfolio', 'premierplug-talent'); ?>
                </div>
            </header>

            <div class="pptm-portfolio-grid">
                <?php foreach ($portfolio_items as $item) :
                    if (empty($item['url'])) continue;
                    $type = $item['type'] ?? 'image';
                ?>
                    <div class="portfolio-item portfolio-item-<?php echo esc_attr($type); ?>" data-aos="fade-up">
                        <?php if ($type === 'image') : ?>
                            <a href="<?php echo esc_url($item['url']); ?>" class="portfolio-image-link" data-lightbox="talent-portfolio">
                                <img src="<?php echo esc_url($item['url']); ?>" alt="<?php echo esc_attr($item['title'] ?? ''); ?>" />
                                <?php if (!empty($item['title'])) : ?>
                                    <div class="portfolio-overlay">
                                        <h6><?php echo esc_html($item['title']); ?></h6>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php elseif ($type === 'video') : ?>
                            <div class="portfolio-video">
                                <video controls>
                                    <source src="<?php echo esc_url($item['url']); ?>" />
                                </video>
                                <?php if (!empty($item['title'])) : ?>
                                    <h6><?php echo esc_html($item['title']); ?></h6>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($type === 'audio') : ?>
                            <div class="portfolio-audio">
                                <?php if (!empty($item['title'])) : ?>
                                    <h6><?php echo esc_html($item['title']); ?></h6>
                                <?php endif; ?>
                                <audio controls>
                                    <source src="<?php echo esc_url($item['url']); ?>" />
                                </audio>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Contact Section -->
    <section class="contact-us bg-black min-full-vh pad-tb vh-center" id="contact-talent">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade"><?php _e('Contact', 'premierplug-talent'); ?></div>
            </header>

            <div class="content-container">
                <div class="title-target">
                    <h5 data-aos="fade" data-aos-delay="200">
                        <?php _e('Interested in working together? Get in touch.', 'premierplug-talent'); ?>
                    </h5>
                </div>

                <form id="pptm-inquiry-form" class="webform-submission-form" data-aos="fade" data-aos-delay="400">
                    <input type="hidden" name="talent_id" value="<?php echo absint($talent_id); ?>" />

                    <div class="form-item">
                        <label for="inquiry-name" class="form-required"><?php _e('First Name', 'premierplug-talent'); ?></label>
                        <input type="text" id="inquiry-name" name="name" required class="form-text required" />
                    </div>

                    <div class="form-item">
                        <label for="inquiry-email" class="form-required"><?php _e('Email', 'premierplug-talent'); ?></label>
                        <input type="email" id="inquiry-email" name="email" required class="form-email required" />
                    </div>

                    <div class="form-item">
                        <label for="inquiry-phone"><?php _e('Phone', 'premierplug-talent'); ?></label>
                        <input type="tel" id="inquiry-phone" name="phone" class="form-tel" />
                    </div>

                    <div class="form-item">
                        <label for="inquiry-organization"><?php _e('Organization', 'premierplug-talent'); ?></label>
                        <input type="text" id="inquiry-organization" name="organization" class="form-text" />
                    </div>

                    <div class="form-item">
                        <label for="inquiry-country" class="form-required"><?php _e('Country', 'premierplug-talent'); ?></label>
                        <select id="inquiry-country" name="country" required class="form-select required">
                            <option value="">- <?php _e('Select', 'premierplug-talent'); ?> -</option>
                            <option value="India">India</option>
                            <option value="United States">United States</option>
                            <option value="United Kingdom">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                            <option value="Germany">Germany</option>
                            <option value="France">France</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-item">
                        <label for="inquiry-message" class="form-required"><?php _e('Message', 'premierplug-talent'); ?></label>
                        <textarea id="inquiry-message" name="message" rows="5" required class="form-textarea required"></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="webform-button--submit button button--primary">
                            <?php _e('Submit', 'premierplug-talent'); ?>
                        </button>
                    </div>

                    <div id="pptm-inquiry-response" style="display: none;"></div>
                </form>
            </div>
        </div>
    </section>

</div>

<?php endwhile; ?>

<?php get_footer(); ?>
