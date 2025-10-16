<?php
/**
 * Template for displaying talent archive
 * Matches existing PremierPlug design patterns
 */

get_header();

$current_term = get_queried_object();
$is_tax = is_tax('talent_segment');
$page_title = $is_tax ? $current_term->name : __('Our Talent Roster', 'premierplug-talent');
$page_description = $is_tax && !empty($current_term->description) ? $current_term->description : __('Discover exceptional talent across all our segments', 'premierplug-talent');
?>

<div class="layout-container">

    <!-- Hero Section matching existing design -->
    <section class="hero-container full_vh var3 bg-black" id="talent-hero">
        <div class="hero-text-container vertical-align">
            <div class="gutter-container">
                <header>
                    <div class="h8" data-aos="fade-up" data-aos-once="true" data-aos-delay="600">
                        <?php echo esc_html($is_tax ? __('Talent Segment', 'premierplug-talent') : __('Browse Talent', 'premierplug-talent')); ?>
                    </div>
                </header>

                <hr class="prehead" data-aos="fade-up" data-aos-delay="600" data-aos-once="true" />

                <h1 class="headline-md" data-aos="fade-up" data-aos-offset="0" data-aos-delay="600" data-aos-once="true">
                    <?php echo esc_html($page_title); ?>
                </h1>

                <?php if ($page_description) : ?>
                    <p class="hero-description" data-aos="fade-up" data-aos-delay="700" data-aos-once="true">
                        <?php echo esc_html($page_description); ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <div class="preloader">
            <?php
            $hero_image = get_theme_mod('pptm_talent_hero_image', get_template_directory_uri() . '/assets/images/digitalmedia.jpg');
            ?>
            <img src="<?php echo esc_url($hero_image); ?>" alt="" role="presentation">
        </div>
        <div class="hero-image-container hero-mask single-hero-image">
            <img src="<?php echo esc_url($hero_image); ?>"
                 data-desktop="<?php echo esc_url($hero_image); ?>"
                 data-mobile="<?php echo esc_url($hero_image); ?>"
                 alt=" "
                 role="presentation" />
        </div>
    </section>

    <!-- Filter Section matching existing design -->
    <section class="jumbo-text text-module pad-tb bg-red col-2" id="talent-filters">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade-up">
                    <?php _e('Filter Talents', 'premierplug-talent'); ?>
                </div>
            </header>

            <div class="content-container">
                <div class="offset-article">
                    <div id="pptm-filter-form">
                        <!-- Search -->
                        <div class="filter-group">
                            <label for="pptm-search"><?php _e('Search', 'premierplug-talent'); ?></label>
                            <input type="text" id="pptm-search" placeholder="<?php esc_attr_e('Search by name...', 'premierplug-talent'); ?>" />
                        </div>

                        <!-- Segment Filter -->
                        <?php if (!$is_tax) : ?>
                        <div class="filter-group">
                            <label><?php _e('Segments', 'premierplug-talent'); ?></label>
                            <?php
                            $segments = get_terms(array(
                                'taxonomy' => 'talent_segment',
                                'hide_empty' => true,
                            ));
                            if ($segments && !is_wp_error($segments)) :
                                foreach ($segments as $segment) : ?>
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="segments[]" value="<?php echo esc_attr($segment->slug); ?>" />
                                        <?php echo esc_html($segment->name); ?>
                                    </label>
                                <?php endforeach;
                            endif;
                            ?>
                        </div>
                        <?php endif; ?>

                        <!-- Availability Filter -->
                        <div class="filter-group">
                            <label for="pptm-availability"><?php _e('Availability', 'premierplug-talent'); ?></label>
                            <select id="pptm-availability">
                                <option value=""><?php _e('All', 'premierplug-talent'); ?></option>
                                <option value="available"><?php _e('Available', 'premierplug-talent'); ?></option>
                                <option value="booked"><?php _e('Booked', 'premierplug-talent'); ?></option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="button" id="pptm-apply-filters" class="btn btn-hero">
                                <?php _e('Apply Filters', 'premierplug-talent'); ?>
                            </button>
                            <button type="button" id="pptm-clear-filters" class="btn btn-secondary">
                                <?php _e('Clear', 'premierplug-talent'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Talent Grid Section matching existing design -->
    <section class="jumbo-text text-module pad-tb bg-white" id="talent-grid-section">
        <div class="gutter-container">
            <header>
                <div class="h8" data-aos="fade-up">
                    <span id="pptm-results-count"><?php echo sprintf(__('%d Talents Found', 'premierplug-talent'), $wp_query->found_posts); ?></span>
                </div>
            </header>

            <div id="pptm-talent-grid" class="pptm-talent-grid">
                <?php
                if (have_posts()) :
                    while (have_posts()) : the_post();
                        include PPTM_PLUGIN_DIR . 'public/partials/talent-card.php';
                    endwhile;
                else :
                    echo '<p class="no-talents-found">' . esc_html__('No talents found. Try adjusting your filters.', 'premierplug-talent') . '</p>';
                endif;
                ?>
            </div>

            <!-- Pagination -->
            <?php
            $big = 999999999;
            $pagination = paginate_links(array(
                'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                'format' => '?paged=%#%',
                'current' => max(1, get_query_var('paged')),
                'total' => $wp_query->max_num_pages,
                'prev_text' => '←',
                'next_text' => '→',
            ));

            if ($pagination) :
                echo '<div class="pptm-pagination">' . $pagination . '</div>';
            endif;
            ?>

            <div id="pptm-loading" class="pptm-loading" style="display: none;">
                <div class="loading-spinner"></div>
            </div>
        </div>
    </section>

</div>

<?php get_footer(); ?>
