<?php
/**
 * Single Article Template
 *
 * Single article view with linked talents
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

get_header();

while (have_posts()) : the_post();
    $is_featured = get_post_meta(get_the_ID(), '_pptm_is_featured', true) === '1';
    $publication_date = get_post_meta(get_the_ID(), '_pptm_publication_date', true);
    $source_name = get_post_meta(get_the_ID(), '_pptm_source_name', true);
    $source_url = get_post_meta(get_the_ID(), '_pptm_source_url', true);
    $author_name = get_post_meta(get_the_ID(), '_pptm_author_name', true);
    $linked_talents = PPTM_Article_Queries::get_article_talents(get_the_ID());
    $related_articles = PPTM_Article_Queries::get_related_articles(get_the_ID(), 3);
    ?>

    <article class="pptm-single-article">
        <header class="pptm-article-header">
            <?php if ($is_featured) : ?>
                <span class="pptm-featured-badge-large">
                    <span class="dashicons dashicons-star-filled"></span>
                    <?php esc_html_e('Featured Article', 'premierplug-talent'); ?>
                </span>
            <?php endif; ?>

            <div class="pptm-article-meta-top">
                <span class="pptm-article-type-badge pptm-type-<?php echo esc_attr(get_post_type()); ?>">
                    <?php echo esc_html(PPTM_Article_Post_Types::get_type_label(get_post_type())); ?>
                </span>
                <time class="pptm-article-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                    <?php
                    if (!empty($publication_date)) {
                        echo esc_html(date_i18n(get_option('date_format'), strtotime($publication_date)));
                    } else {
                        echo get_the_date();
                    }
                    ?>
                </time>
            </div>

            <h1 class="pptm-article-title"><?php the_title(); ?></h1>

            <div class="pptm-article-meta-details">
                <?php if (!empty($author_name)) : ?>
                    <div class="pptm-article-author">
                        <span class="dashicons dashicons-admin-users"></span>
                        <strong><?php esc_html_e('Author:', 'premierplug-talent'); ?></strong>
                        <?php echo esc_html($author_name); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($source_name)) : ?>
                    <div class="pptm-article-source-info">
                        <span class="dashicons dashicons-admin-links"></span>
                        <strong><?php esc_html_e('Source:', 'premierplug-talent'); ?></strong>
                        <?php if (!empty($source_url)) : ?>
                            <a href="<?php echo esc_url($source_url); ?>" target="_blank" rel="noopener">
                                <?php echo esc_html($source_name); ?>
                            </a>
                        <?php else : ?>
                            <?php echo esc_html($source_name); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (has_post_thumbnail()) : ?>
                <div class="pptm-article-featured-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
        </header>

        <div class="pptm-article-content">
            <?php the_content(); ?>
        </div>

        <?php if (!empty($linked_talents)) : ?>
            <aside class="pptm-article-talents">
                <h3><?php esc_html_e('Featured Talents', 'premierplug-talent'); ?></h3>
                <div class="pptm-talents-grid">
                    <?php foreach ($linked_talents as $talent) : ?>
                        <div class="pptm-talent-mini-card">
                            <?php if (has_post_thumbnail($talent->ID)) : ?>
                                <div class="pptm-talent-mini-image">
                                    <a href="<?php echo esc_url(get_permalink($talent->ID)); ?>">
                                        <?php echo get_the_post_thumbnail($talent->ID, 'thumbnail'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="pptm-talent-mini-info">
                                <h4>
                                    <a href="<?php echo esc_url(get_permalink($talent->ID)); ?>">
                                        <?php echo esc_html($talent->post_title); ?>
                                    </a>
                                </h4>
                                <?php
                                $categories = wp_get_post_terms($talent->ID, 'talent_category');
                                if (!empty($categories) && !is_wp_error($categories)) {
                                    echo '<span class="pptm-talent-category">' . esc_html($categories[0]->name) . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        <?php endif; ?>

        <?php if (!empty($related_articles)) : ?>
            <aside class="pptm-related-articles">
                <h3><?php esc_html_e('Related Articles', 'premierplug-talent'); ?></h3>
                <div class="pptm-articles-grid pptm-grid-columns-3">
                    <?php
                    global $post;
                    foreach ($related_articles as $post) {
                        setup_postdata($post);
                        include(plugin_dir_path(__FILE__) . 'article-card.php');
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </aside>
        <?php endif; ?>
    </article>

<?php endwhile; ?>

<?php get_footer(); ?>
