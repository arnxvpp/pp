<?php
/**
 * Template Name: About Page
 * Description: Template for About Us and similar company pages
 *
 * @package PremierPlug
 */

get_header(); ?>

<div id="main">
    <section id="content">

        <?php while (have_posts()) : the_post(); ?>

            <!-- Hero Section with Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
                <section class="hero-container full_vh var4 bg-black">
                    <div class="hero-text-container bottom-align">
                        <div class="gutter-container">
                            <header>
                                <div class="h8" data-aos="fade-up" data-aos-once="true" data-aos-delay="600"></div>
                            </header>
                            <h1 data-aos="fade-up" data-aos-delay="600" data-aos-once="true">
                                <?php the_title(); ?>
                            </h1>
                        </div>
                    </div>
                    <div class="preloader">
                        <?php the_post_thumbnail('premierplug-hero', array('alt' => get_the_title(), 'role' => 'presentation')); ?>
                    </div>
                    <div class="hero-image-container hero-mask single-hero-image">
                        <?php the_post_thumbnail('premierplug-hero', array('alt' => ' ', 'role' => 'presentation')); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Content Area -->
            <div id="content-area">
                <article <?php post_class(); ?>>
                    <div class="content">
                        <?php the_content(); ?>
                    </div>
                </article>
            </div>

        <?php endwhile; ?>

    </section>
</div>

<?php get_footer(); ?>
