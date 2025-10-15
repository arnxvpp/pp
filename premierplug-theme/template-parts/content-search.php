<?php
/**
 * Search Results Content Template Part
 *
 * @package PremierPlug
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('search-result'); ?>>
    <header class="entry-header">
        <h2 class="entry-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
            </div>
        <?php endif; ?>
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
