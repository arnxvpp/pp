<?php
/**
 * Template Name: Enterprise Solutions
 * Description: Template for enterprise segment pages (Brand Consulting, Studio, Management, Partnerships)
 *
 * @package PremierPlug
 */

get_header(); ?>

<div id="main">
    <section id="content">

        <?php while (have_posts()) : the_post(); ?>

            <!-- Hero Section -->
            <?php if (has_post_thumbnail()) : ?>
                <section class="hero-container full_vh var1 bg-black mask-out" id="hero-carousel">
                    <div class="hero-text-container vertical-align">
                        <div class="gutter-container">
                            <header>
                                <div class="h8" data-aos="fade-up" data-aos-once="true" data-aos-delay="600">For Enterprise</div>
                            </header>
                            <h1 data-aos="fade-up" data-aos-delay="600" data-aos-once="true">
                                <?php the_title(); ?>
                            </h1>
                        </div>
                    </div>
                    <div class="preloader">
                        <?php the_post_thumbnail('premierplug-hero', array('alt' => '', 'role' => 'presentation')); ?>
                    </div>
                    <div class="hero-image-container hero-mask single-hero-image">
                        <?php the_post_thumbnail('premierplug-hero', array('alt' => ' ', 'role' => 'presentation')); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Content Section -->
            <section class="jumbo-text text-module pad-tb bg-white col-2">
                <div class="gutter-container">
                    <header>
                        <div class="h8" data-aos="fade-up">
                            <?php echo esc_html(get_post_meta(get_the_ID(), '_section_header', true) ?: 'Our Approach'); ?>
                        </div>
                    </header>
                    <div class="content-container">
                        <div class="offset-article">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Services Offerings (if custom field exists) -->
            <?php if (have_rows('service_offerings')) : ?>
                <section class="jumbo-text text-module service-offerings pad-tb bg-white col-2">
                    <div class="gutter-container">
                        <header>
                            <div class="h8" data-aos="fade-up">What We Do</div>
                        </header>
                        <div class="content-container">
                            <div class="offset-article">
                                <h4 data-aos="fade-up">Our suite of integrated services work together based on bespoke client needs.</h4>
                                <div class="jumbo-text-body">
                                    <?php while (have_rows('service_offerings')) : the_row(); ?>
                                        <div data-aos="fade-up">
                                            <h6 class="list-title"><?php the_sub_field('title'); ?></h6>
                                            <div class="list-body-text">
                                                <?php the_sub_field('description'); ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Related Services -->
            <?php
            $related_args = array(
                'post_type' => 'post',
                'category_name' => 'enterprise-solutions',
                'posts_per_page' => 3,
                'post__not_in' => array(get_the_ID())
            );
            $related_query = new WP_Query($related_args);

            if ($related_query->have_posts()) :
            ?>
                <section class="related-services pad-tb bg-light">
                    <div class="gutter-container">
                        <header>
                            <div class="h8" data-aos="fade-up">Other Enterprise Solutions</div>
                        </header>
                        <div class="services-grid">
                            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                                <article class="service-card" data-aos="fade-up">
                                    <?php if (has_post_thumbnail()) : ?>
                                        <div class="service-image">
                                            <a href="<?php the_permalink(); ?>">
                                                <?php the_post_thumbnail('premierplug-featured'); ?>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="service-content">
                                        <h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                        <?php the_excerpt(); ?>
                                        <a href="<?php the_permalink(); ?>" class="read-more">Learn More →</a>
                                    </div>
                                </article>
                            <?php endwhile; wp_reset_postdata(); ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

        <?php endwhile; ?>

    </section>
</div>

<?php get_footer(); ?>
