<?php
/**
 * Index Template (Fallback)
 *
 * @package PremierPlug
 * @since 1.0.0
 */

get_header();
?>

<main class="site-content">
    <div class="content-container">
        <div class="gutter-container">
            <?php
            if (have_posts()) :
                while (have_posts()) :
                    the_post();
                    get_template_part('template-parts/content', get_post_type());
                endwhile;

                the_posts_navigation();
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
