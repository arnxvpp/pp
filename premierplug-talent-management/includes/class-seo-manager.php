<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_SEO_Manager {

    public static function init() {
        add_action('wp_head', array(__CLASS__, 'output_meta_tags'), 1);
        add_action('wp_head', array(__CLASS__, 'output_schema_markup'), 2);
        add_filter('document_title_parts', array(__CLASS__, 'optimize_document_title'));
    }

    public static function output_meta_tags() {
        if (!is_singular()) {
            return;
        }

        global $post;
        $post_types = array('talent', 'article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study');

        if (!in_array($post->post_type, $post_types)) {
            return;
        }

        $title = get_the_title();
        $description = self::get_meta_description($post);
        $url = get_permalink();
        $image = self::get_featured_image_url($post);
        $site_name = get_bloginfo('name');
        $author = get_the_author_meta('display_name', $post->post_author);

        echo "\n<!-- PremierPlug SEO Meta Tags -->\n";

        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
        echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";

        echo "\n<!-- Open Graph (Facebook, LinkedIn) -->\n";
        echo '<meta property="og:type" content="' . ($post->post_type === 'talent' ? 'profile' : 'article') . '">' . "\n";
        echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
        echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
        echo '<meta property="og:site_name" content="' . esc_attr($site_name) . '">' . "\n";

        if ($image) {
            echo '<meta property="og:image" content="' . esc_url($image) . '">' . "\n";
            echo '<meta property="og:image:width" content="1200">' . "\n";
            echo '<meta property="og:image:height" content="630">' . "\n";
        }

        if ($post->post_type !== 'talent') {
            echo '<meta property="article:published_time" content="' . get_the_date('c') . '">' . "\n";
            echo '<meta property="article:modified_time" content="' . get_the_modified_date('c') . '">' . "\n";
            echo '<meta property="article:author" content="' . esc_attr($author) . '">' . "\n";
        }

        echo "\n<!-- Twitter Card -->\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";

        if ($image) {
            echo '<meta name="twitter:image" content="' . esc_url($image) . '">' . "\n";
        }

        $twitter_handle = get_option('pptm_twitter_handle', '');
        if ($twitter_handle) {
            echo '<meta name="twitter:site" content="@' . esc_attr($twitter_handle) . '">' . "\n";
        }

        echo "<!-- End PremierPlug SEO -->\n\n";
    }

    public static function output_schema_markup() {
        if (!is_singular()) {
            return;
        }

        global $post;
        $post_types = array('talent', 'article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study');

        if (!in_array($post->post_type, $post_types)) {
            return;
        }

        if ($post->post_type === 'talent') {
            self::output_person_schema($post);
        } else {
            self::output_article_schema($post);
        }

        self::output_organization_schema();
    }

    private static function output_person_schema($post) {
        $name = get_the_title();
        $description = self::get_meta_description($post);
        $image = self::get_featured_image_url($post);
        $url = get_permalink();

        $categories = wp_get_post_terms($post->ID, 'talent_category');
        $job_title = !empty($categories) ? $categories[0]->name : 'Talent';

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Person',
            'name' => $name,
            'description' => $description,
            'url' => $url,
            'jobTitle' => $job_title,
        );

        if ($image) {
            $schema['image'] = $image;
        }

        $email = get_post_meta($post->ID, '_talent_email', true);
        if ($email) {
            $schema['email'] = $email;
        }

        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "\n" . '</script>' . "\n";
    }

    private static function output_article_schema($post) {
        $title = get_the_title();
        $description = self::get_meta_description($post);
        $image = self::get_featured_image_url($post);
        $url = get_permalink();
        $author = get_the_author_meta('display_name', $post->post_author);
        $published = get_the_date('c');
        $modified = get_the_modified_date('c');

        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $title,
            'description' => $description,
            'url' => $url,
            'datePublished' => $published,
            'dateModified' => $modified,
            'author' => array(
                '@type' => 'Person',
                'name' => $author
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
            ),
        );

        if ($image) {
            $schema['image'] = $image;
        }

        $logo_id = get_theme_mod('custom_logo');
        if ($logo_id) {
            $logo_url = wp_get_attachment_image_url($logo_id, 'full');
            if ($logo_url) {
                $schema['publisher']['logo'] = array(
                    '@type' => 'ImageObject',
                    'url' => $logo_url
                );
            }
        }

        echo '<script type="application/ld+json">' . "\n";
        echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        echo "\n" . '</script>' . "\n";
    }

    private static function output_organization_schema() {
        if (is_front_page()) {
            $schema = array(
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url(),
                'description' => get_bloginfo('description'),
            );

            $logo_id = get_theme_mod('custom_logo');
            if ($logo_id) {
                $logo_url = wp_get_attachment_image_url($logo_id, 'full');
                if ($logo_url) {
                    $schema['logo'] = $logo_url;
                }
            }

            $social_profiles = array();
            $twitter = get_option('pptm_twitter_url', '');
            $facebook = get_option('pptm_facebook_url', '');
            $linkedin = get_option('pptm_linkedin_url', '');
            $instagram = get_option('pptm_instagram_url', '');

            if ($twitter) $social_profiles[] = $twitter;
            if ($facebook) $social_profiles[] = $facebook;
            if ($linkedin) $social_profiles[] = $linkedin;
            if ($instagram) $social_profiles[] = $instagram;

            if (!empty($social_profiles)) {
                $schema['sameAs'] = $social_profiles;
            }

            echo '<script type="application/ld+json">' . "\n";
            echo wp_json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            echo "\n" . '</script>' . "\n";
        }
    }

    private static function get_meta_description($post) {
        $excerpt = get_the_excerpt($post);

        if (empty($excerpt)) {
            $content = strip_shortcodes($post->post_content);
            $content = wp_strip_all_tags($content);
            $excerpt = wp_trim_words($content, 30, '...');
        }

        $excerpt = str_replace(array("\r", "\n"), ' ', $excerpt);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);

        return substr($excerpt, 0, 160);
    }

    private static function get_featured_image_url($post) {
        if (has_post_thumbnail($post->ID)) {
            $image_id = get_post_thumbnail_id($post->ID);
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            return $image_url;
        }
        return '';
    }

    public static function optimize_document_title($title_parts) {
        if (is_singular(array('talent', 'article_press_release', 'article_industry_insight', 'article_thought_leadership', 'article_company_news', 'article_case_study'))) {
            $title_parts['title'] = get_the_title();
        }
        return $title_parts;
    }
}
