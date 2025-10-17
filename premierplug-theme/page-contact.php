<?php
/**
 * Template Name: Contact Page
 * Description: Template for Contact Us page with form
 *
 * @package PremierPlug
 */

get_header(); ?>

<div id="main">
    <section id="content">

        <?php while (have_posts()) : the_post(); ?>

            <!-- Hero Section -->
            <?php if (has_post_thumbnail()) : ?>
                <section class="hero-container full_vh var1 bg-black mask-out">
                    <div class="hero-text-container vertical-align">
                        <div class="gutter-container">
                            <h1 data-aos="fade-up" data-aos-delay="600" data-aos-once="true">
                                <?php the_title(); ?>
                            </h1>
                        </div>
                    </div>
                    <div class="preloader">
                        <?php the_post_thumbnail('premierplug-hero'); ?>
                    </div>
                    <div class="hero-image-container hero-mask single-hero-image">
                        <?php the_post_thumbnail('premierplug-hero', array('alt' => ' ', 'role' => 'presentation')); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Content Section -->
            <section class="jumbo-text text-module pad-tb bg-white col-2">
                <div class="gutter-container">
                    <div class="content-container">
                        <div class="offset-article">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Form Section -->
            <section class="contact-us bg-black min-full-vh pad-tb vh-center">
                <div class="gutter-container">
                    <header>
                        <div class="h8" data-aos="fade">Contact Us</div>
                    </header>
                    <div class="content-container">
                        <div class="title-target">
                            <h5 data-aos="fade" data-aos-delay="200">
                                Want to see how we can help your brand? Get in touch.
                            </h5>
                        </div>

                        <?php
                        // Display Contact Form 7 if shortcode exists
                        if (function_exists('wpcf7_contact_form')) {
                            echo do_shortcode('[contact-form-7 id="1" title="Contact form"]');
                        } else {
                            // Fallback form
                            ?>
                            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="contact-form">
                                <input type="hidden" name="action" value="premierplug_contact">
                                <?php wp_nonce_field('premierplug_contact', 'premierplug_contact_nonce'); ?>

                                <div class="form-item">
                                    <label for="contact-name">First Name *</label>
                                    <input type="text" id="contact-name" name="first_name" required>
                                </div>

                                <div class="form-item">
                                    <label for="contact-last">Last Name *</label>
                                    <input type="text" id="contact-last" name="last_name" required>
                                </div>

                                <div class="form-item">
                                    <label for="contact-email">Email *</label>
                                    <input type="email" id="contact-email" name="email" required>
                                </div>

                                <div class="form-item">
                                    <label for="contact-phone">Phone</label>
                                    <input type="tel" id="contact-phone" name="phone">
                                </div>

                                <div class="form-item">
                                    <label for="contact-organization">Organization</label>
                                    <input type="text" id="contact-organization" name="organization">
                                </div>

                                <div class="form-item">
                                    <label for="contact-message">Message</label>
                                    <textarea id="contact-message" name="message" rows="5"></textarea>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="button button--primary">Submit</button>
                                </div>
                            </form>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </section>

        <?php endwhile; ?>

    </section>
</div>

<?php get_footer(); ?>
