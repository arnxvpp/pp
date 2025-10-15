<?php
/**
 * 404 Error Template
 *
 * @package PremierPlug
 * @since 1.0.0
 */

get_header();
?>

<main class="site-content">
    <div class="content-container">
        <div class="gutter-container">
            <section class="error-404 not-found">
                <header class="page-header">
                    <h1 class="page-title"><?php esc_html_e('Page Not Found', 'premierplug'); ?></h1>
                </header>

                <div class="page-content">
                    <p><?php esc_html_e('It looks like nothing was found at this location. Try searching for what you need:', 'premierplug'); ?></p>

                    <?php get_search_form(); ?>

                    <div class="widget widget_recent_entries">
                        <h2><?php esc_html_e('Recent Posts', 'premierplug'); ?></h2>
                        <ul>
                            <?php
                            wp_list_pages(array(
                                'title_li' => '',
                                'number'   => 5,
                                'sort_column' => 'post_date',
                                'sort_order' => 'DESC',
                            ));
                            ?>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<?php get_footer(); ?>
