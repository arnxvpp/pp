<?php
/**
 * Page Template
 *
 * @package PremierPlug
 * @since 1.0.0
 */

get_header();
?>

<main class="site-content">
    <div class="content-container">
        <?php
        while (have_posts()) :
            the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php if (has_post_thumbnail()) : ?>
                    <div class="hero-image">
                        <?php the_post_thumbnail('premierplug-hero'); ?>
                    </div>
                <?php endif; ?>

                <div class="gutter-container">
                    <header class="entry-header">
                        <h1 class="entry-title"><?php the_title(); ?></h1>
                    </header>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'premierplug'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>
                </div>
            </article>

            <?php
            if (comments_open() || get_comments_number()) :
                comments_template();
            endif;
            ?>

            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
