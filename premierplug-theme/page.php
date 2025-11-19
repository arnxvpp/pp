<?php
/**
 * Template for displaying all pages
 *
 * @package PremierPlug
 */

get_header();
?>

<main class="site-main">
    <?php
    while (have_posts()) :
        the_post();
        ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if (has_post_thumbnail()) : ?>
                <div class="hero-container" style="background-image: url('<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full'); ?>');">
                    <div class="hero-text-container">
                        <div class="gutter-container">
                            <h1><?php the_title(); ?></h1>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="hero-container bg-white" style="padding-top: 120px;">
                    <div class="hero-text-container">
                        <div class="gutter-container">
                            <h1 style="color: #1a1a1a;"><?php the_title(); ?></h1>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <div class="gutter-container">
                    <div class="page-content">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </article>
        <?php
    endwhile;
    ?>
</main>

<?php
get_footer();
