<?php
/**
 * Default Content Template Part
 *
 * @package PremierPlug
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if (has_post_thumbnail() && is_single()) : ?>
        <div class="post-thumbnail">
            <?php the_post_thumbnail('premierplug-hero'); ?>
        </div>
    <?php endif; ?>

    <header class="entry-header">
        <?php
        if (is_singular()) :
            the_title('<h1 class="entry-title">', '</h1>');
        else :
            the_title('<h2 class="entry-title"><a href="' . esc_url(get_permalink()) . '">', '</a></h2>');
        endif;
        ?>

        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
                <span class="byline">
                    <?php
                    printf(
                        esc_html__('by %s', 'premierplug'),
                        '<span class="author">' . esc_html(get_the_author()) . '</span>'
                    );
                    ?>
                </span>
            </div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php
        if (is_singular()) :
            the_content();

            wp_link_pages(array(
                'before' => '<div class="page-links">' . esc_html__('Pages:', 'premierplug'),
                'after'  => '</div>',
            ));
        else :
            the_excerpt();
        ?>
            <a href="<?php the_permalink(); ?>" class="read-more">
                <?php esc_html_e('Read More', 'premierplug'); ?>
            </a>
        <?php endif; ?>
    </div>
</article>
