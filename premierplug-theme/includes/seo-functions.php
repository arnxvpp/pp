<?php
/**
 * SEO Functions for PremierPlug Theme
 * Handles Schema Markup, Meta Tags, and Indian Market Optimization
 *
 * @package PremierPlug
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Organization Schema Markup to Homepage
 */
function premierplug_organization_schema() {
    if (!is_front_page()) {
        return;
    }

    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'PremierPlug',
        'url' => home_url(),
        'logo' => get_theme_mod('custom_logo') ? wp_get_attachment_url(get_theme_mod('custom_logo')) : '',
        'description' => get_bloginfo('description'),
        'address' => array(
            '@type' => 'PostalAddress',
            'addressCountry' => 'IN',
            'addressRegion' => 'Maharashtra',
            'addressLocality' => 'Mumbai',
        ),
        'contactPoint' => array(
            '@type' => 'ContactPoint',
            'contactType' => 'Customer Service',
            'areaServed' => array('IN'),
            'availableLanguage' => array('English', 'Hindi'),
        ),
        'sameAs' => array(
            'https://www.facebook.com/premierplug',
            'https://www.twitter.com/premierplug',
            'https://www.linkedin.com/company/premierplug',
            'https://www.instagram.com/premierplug',
        ),
    );

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'premierplug_organization_schema');

/**
 * Add LocalBusiness Schema for Contact Page
 */
function premierplug_local_business_schema() {
    if (!is_page('contact') && !is_page('contact-us')) {
        return;
    }

    $locations = array(
        array(
            '@type' => 'LocalBusiness',
            'name' => 'PremierPlug Mumbai',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => 'Mumbai',
                'addressRegion' => 'Maharashtra',
                'postalCode' => '',
                'addressCountry' => 'IN',
            ),
        ),
        array(
            '@type' => 'LocalBusiness',
            'name' => 'PremierPlug Delhi',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => 'Delhi',
                'addressRegion' => 'Delhi',
                'postalCode' => '',
                'addressCountry' => 'IN',
            ),
        ),
        array(
            '@type' => 'LocalBusiness',
            'name' => 'PremierPlug Bangalore',
            'address' => array(
                '@type' => 'PostalAddress',
                'streetAddress' => '',
                'addressLocality' => 'Bangalore',
                'addressRegion' => 'Karnataka',
                'postalCode' => '',
                'addressCountry' => 'IN',
            ),
        ),
    );

    foreach ($locations as $location) {
        echo '<script type="application/ld+json">' . wp_json_encode($location, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'premierplug_local_business_schema');

/**
 * Add Service Schema for Service Pages
 */
function premierplug_service_schema() {
    if (!is_singular('page')) {
        return;
    }

    $service_pages = array(
        'brand-consulting' => array(
            'name' => 'Brand Consulting Services India',
            'description' => 'Expert brand consulting and strategic marketing services in Mumbai, Delhi, and Bangalore',
            'areaServed' => 'India',
            'serviceType' => 'Brand Consulting',
        ),
        'brandmanagement' => array(
            'name' => 'Brand Management Services',
            'description' => 'Comprehensive brand management solutions for enterprises across India',
            'areaServed' => 'India',
            'serviceType' => 'Brand Management',
        ),
        'digital-media' => array(
            'name' => 'Influencer Marketing Agency India',
            'description' => 'Leading influencer marketing and digital media services in India',
            'areaServed' => 'India',
            'serviceType' => 'Influencer Marketing',
        ),
        'market-research' => array(
            'name' => 'Market Research Services India',
            'description' => 'Professional market research and consumer insights for Indian market',
            'areaServed' => 'India',
            'serviceType' => 'Market Research',
        ),
    );

    $page_slug = get_post_field('post_name', get_post());

    if (isset($service_pages[$page_slug])) {
        $service = $service_pages[$page_slug];
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $service['name'],
            'description' => $service['description'],
            'provider' => array(
                '@type' => 'Organization',
                'name' => 'PremierPlug',
                'url' => home_url(),
            ),
            'areaServed' => array(
                '@type' => 'Country',
                'name' => $service['areaServed'],
            ),
            'serviceType' => $service['serviceType'],
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'premierplug_service_schema');

/**
 * Add Breadcrumb Schema
 */
function premierplug_breadcrumb_schema() {
    if (is_front_page()) {
        return;
    }

    $items = array();
    $position = 1;

    $items[] = array(
        '@type' => 'ListItem',
        'position' => $position++,
        'name' => 'Home',
        'item' => home_url(),
    );

    if (is_singular()) {
        $post = get_queried_object();

        if ($post->post_parent) {
            $ancestors = array_reverse(get_post_ancestors($post->ID));
            foreach ($ancestors as $ancestor) {
                $items[] = array(
                    '@type' => 'ListItem',
                    'position' => $position++,
                    'name' => get_the_title($ancestor),
                    'item' => get_permalink($ancestor),
                );
            }
        }

        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink(),
        );
    } elseif (is_category() || is_tag() || is_tax()) {
        $term = get_queried_object();
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $term->name,
            'item' => get_term_link($term),
        );
    } elseif (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        $post_type_obj = get_post_type_object($post_type);
        $items[] = array(
            '@type' => 'ListItem',
            'position' => $position,
            'name' => $post_type_obj->labels->name,
            'item' => get_post_type_archive_link($post_type),
        );
    }

    if (count($items) > 1) {
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        );

        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'premierplug_breadcrumb_schema');

/**
 * Enhanced SEO Title
 */
function premierplug_seo_title($title) {
    if (is_front_page()) {
        return 'PremierPlug | Brand Consulting & Talent Management India | Mumbai, Delhi, Bangalore';
    }

    if (is_singular()) {
        $post_type = get_post_type();

        if ($post_type === 'pp_talent') {
            return get_the_title() . ' | Talent Profile | PremierPlug India';
        }
    }

    if (is_post_type_archive('pp_talent')) {
        return 'Talent Roster | Book Celebrities & Speakers | PremierPlug India';
    }

    return $title . ' | PremierPlug India';
}
add_filter('wp_title', 'premierplug_seo_title', 10, 1);
add_filter('pre_get_document_title', 'premierplug_seo_title', 10, 1);

/**
 * Enhanced Meta Descriptions
 */
function premierplug_meta_description() {
    $description = '';

    if (is_front_page()) {
        $description = 'Leading brand consulting, talent management, and entertainment marketing agency in India. Mumbai, Delhi, Bangalore. Celebrity endorsements, influencer partnerships, market research.';
    } elseif (is_singular()) {
        $post = get_queried_object();

        if ($post->post_type === 'pp_talent') {
            $headline = get_post_meta($post->ID, '_pptm_headline', true);
            $description = $headline ? $headline . ' | ' : '';
            $description .= wp_trim_words(strip_tags($post->post_content), 25);
        } elseif (has_excerpt()) {
            $description = get_the_excerpt();
        } else {
            $description = wp_trim_words(strip_tags($post->post_content), 25);
        }
    } elseif (is_post_type_archive('pp_talent')) {
        $description = 'Browse our roster of talented celebrities, speakers, influencers, and voice artists. Book top talent for your brand campaigns and events across India.';
    } elseif (is_tax('talent_segment') || is_tax('talent_skill')) {
        $term = get_queried_object();
        $description = $term->description ?: 'View all ' . $term->name . ' professionals in our talent roster. Book talent for your next project.';
    }

    if ($description) {
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    }
}
add_action('wp_head', 'premierplug_meta_description');

/**
 * Open Graph Tags
 */
function premierplug_og_tags() {
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url(get_permalink()) . '">' . "\n";

    if (is_singular()) {
        echo '<meta property="og:title" content="' . esc_attr(get_the_title()) . '">' . "\n";

        if (has_post_thumbnail()) {
            $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }
    } else {
        echo '<meta property="og:title" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    }

    echo '<meta property="og:locale" content="en_IN">' . "\n";
}
add_action('wp_head', 'premierplug_og_tags');

/**
 * Twitter Card Tags
 */
function premierplug_twitter_cards() {
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:site" content="@premierplug">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr(get_the_title()) . '">' . "\n";

    if (is_singular() && has_post_thumbnail()) {
        $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
        echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
    }
}
add_action('wp_head', 'premierplug_twitter_cards');

/**
 * Canonical URL
 */
function premierplug_canonical_url() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . esc_url(get_permalink()) . '">' . "\n";
    } elseif (is_post_type_archive()) {
        $post_type = get_query_var('post_type');
        echo '<link rel="canonical" href="' . esc_url(get_post_type_archive_link($post_type)) . '">' . "\n";
    } elseif (is_front_page()) {
        echo '<link rel="canonical" href="' . esc_url(home_url('/')) . '">' . "\n";
    }
}
add_action('wp_head', 'premierplug_canonical_url');

/**
 * Add hreflang tags for Indian market
 */
function premierplug_hreflang_tags() {
    $current_url = get_permalink();

    echo '<link rel="alternate" hreflang="en-in" href="' . esc_url($current_url) . '">' . "\n";
    echo '<link rel="alternate" hreflang="en" href="' . esc_url($current_url) . '">' . "\n";
    echo '<link rel="alternate" hreflang="x-default" href="' . esc_url($current_url) . '">' . "\n";
}
add_action('wp_head', 'premierplug_hreflang_tags');

/**
 * Add Geo Meta Tags for Indian Cities
 */
function premierplug_geo_tags() {
    if (is_singular('page')) {
        $page_slug = get_post_field('post_name', get_post());

        $geo_locations = array(
            'mumbai' => array('lat' => '19.0760', 'lng' => '72.8777', 'region' => 'IN-MH', 'placename' => 'Mumbai'),
            'delhi' => array('lat' => '28.7041', 'lng' => '77.1025', 'region' => 'IN-DL', 'placename' => 'Delhi'),
            'bangalore' => array('lat' => '12.9716', 'lng' => '77.5946', 'region' => 'IN-KA', 'placename' => 'Bangalore'),
            'chennai' => array('lat' => '13.0827', 'lng' => '80.2707', 'region' => 'IN-TN', 'placename' => 'Chennai'),
            'hyderabad' => array('lat' => '17.3850', 'lng' => '78.4867', 'region' => 'IN-TG', 'placename' => 'Hyderabad'),
            'pune' => array('lat' => '18.5204', 'lng' => '73.8567', 'region' => 'IN-MH', 'placename' => 'Pune'),
        );

        foreach ($geo_locations as $slug => $geo) {
            if (strpos($page_slug, $slug) !== false) {
                echo '<meta name="geo.position" content="' . $geo['lat'] . ';' . $geo['lng'] . '">' . "\n";
                echo '<meta name="geo.placename" content="' . esc_attr($geo['placename']) . '">' . "\n";
                echo '<meta name="geo.region" content="' . esc_attr($geo['region']) . '">' . "\n";
                break;
            }
        }
    }
}
add_action('wp_head', 'premierplug_geo_tags');
