<?php
/**
 * Search Results Template
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
                <h1 class="page-title">
                    <?php
                    printf(
                        esc_html__('Search Results for: %s', 'premierplug'),
                        '<span>' . get_search_query() . '</span>'
                    );
                    ?>
                </h1>
            </header>

            <?php
            if (have_posts()) :
                ?>
                <div class="search-results">
                    <?php
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content', 'search');
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
