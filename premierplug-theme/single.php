<?php
/**
 * Single Post Template
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

                        <div class="entry-meta">
                            <span class="posted-on">
                                <?php echo esc_html(get_the_date()); ?>
                            </span>
                            <span class="byline">
                                <?php
                                printf(
                                    esc_html__('by %s', 'premierplug'),
                                    '<span class="author">' . esc_html(get_the_author()) . '</span>'
                                );
                                ?>
                            </span>
                        </div>
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

                    <?php if (has_category() || has_tag()) : ?>
                        <footer class="entry-footer">
                            <?php
                            $categories_list = get_the_category_list(esc_html__(', ', 'premierplug'));
                            if ($categories_list) {
                                printf('<span class="cat-links">' . esc_html__('Posted in %1$s', 'premierplug') . '</span>', $categories_list);
                            }

                            $tags_list = get_the_tag_list('', esc_html__(', ', 'premierplug'));
                            if ($tags_list) {
                                printf('<span class="tags-links">' . esc_html__('Tagged %1$s', 'premierplug') . '</span>', $tags_list);
                            }
                            ?>
                        </footer>
                    <?php endif; ?>
                </div>
            </article>

            <div class="gutter-container">
                <?php
                the_post_navigation(array(
                    'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'premierplug') . '</span> <span class="nav-title">%title</span>',
                    'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'premierplug') . '</span> <span class="nav-title">%title</span>',
                ));

                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;
                ?>
            </div>

            <?php
        endwhile;
        ?>
    </div>
</main>

<?php get_footer(); ?>
