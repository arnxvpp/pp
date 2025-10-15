<?php
/**
 * Archive Template
 *
 * @package PremierPlug
 * @since 1.0.0
 */

get_header();
?>

<main class="site-content">
    <div class="content-container">
        <div class="gutter-container">
            <header class="page-header">
                <?php
                the_archive_title('<h1 class="page-title">', '</h1>');
                the_archive_description('<div class="archive-description">', '</div>');
                ?>
            </header>

            <?php
            if (have_posts()) :
                ?>
                <div class="posts-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        ?>
                        <article id="post-<?php the_ID(); ?>" <?php post_class('post-item'); ?>>
                            <?php if (has_post_thumbnail()) : ?>
                                <div class="post-thumbnail">
                                    <a href="<?php the_permalink(); ?>">
                                        <?php the_post_thumbnail('premierplug-featured'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <header class="entry-header">
                                <h2 class="entry-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h2>

                                <div class="entry-meta">
                                    <span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
                                </div>
                            </header>

                            <div class="entry-excerpt">
                                <?php the_excerpt(); ?>
                            </div>

                            <footer class="entry-footer">
                                <a href="<?php the_permalink(); ?>" class="read-more">
                                    <?php esc_html_e('Read More', 'premierplug'); ?>
                                </a>
                            </footer>
                        </article>
                        <?php
                    endwhile;
                    ?>
                </div>

                <?php
                the_posts_pagination(array(
                    'prev_text' => esc_html__('Previous', 'premierplug'),
                    'next_text' => esc_html__('Next', 'premierplug'),
                ));
            else :
                get_template_part('template-parts/content', 'none');
            endif;
            ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>
