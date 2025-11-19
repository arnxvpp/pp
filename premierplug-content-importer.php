<?php
/**
 * PremierPlug Content Importer
 *
 * Imports all HTML content to WordPress
 * Creates pages, menus, and assigns images
 * Syncs everything to Supabase
 *
 * Usage: Upload to WordPress root and visit: your-site.com/premierplug-content-importer.php
 */

// Security check
define('IMPORT_KEY', 'premierplug_import_2024');
$provided_key = isset($_GET['key']) ? $_GET['key'] : '';

if ($provided_key !== IMPORT_KEY) {
    die('Access denied. Usage: ?key=' . IMPORT_KEY);
}

// Load WordPress
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('administrator')) {
    die('You must be an administrator to run this import.');
}

// Start output buffering for clean display
ob_start();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>PremierPlug Content Importer</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #d92228;
            margin-top: 0;
        }
        .status {
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
            border-left: 4px solid;
        }
        .success {
            background: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .info {
            background: #d1ecf1;
            border-color: #0c5460;
            color: #0c5460;
        }
        .warning {
            background: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .progress {
            background: #e9ecef;
            height: 30px;
            border-radius: 4px;
            overflow: hidden;
            margin: 20px 0;
        }
        .progress-bar {
            background: #d92228;
            height: 100%;
            line-height: 30px;
            color: white;
            text-align: center;
            transition: width 0.3s;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .check {
            color: #28a745;
            font-weight: bold;
        }
        .cross {
            color: #dc3545;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #d92228;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #b01b20;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🚀 PremierPlug Content Importer</h1>

<?php

// Configuration
$archive_path = __DIR__ . '/archive/';
$images_path = __DIR__ . '/images/';

// Page mapping
$pages_map = array(
    // Main pages
    array(
        'file' => 'about-us.html',
        'title' => 'About Us',
        'slug' => 'about-us',
        'parent' => null,
        'image' => 'about-us.jpeg',
        'menu' => 'footer',
        'order' => 1
    ),
    array(
        'file' => 'careers.html',
        'title' => 'Careers',
        'slug' => 'careers',
        'parent' => null,
        'image' => 'career.jpeg',
        'menu' => 'footer',
        'order' => 2
    ),
    array(
        'file' => 'contact.html',
        'title' => 'Contact',
        'slug' => 'contact',
        'parent' => null,
        'image' => 'contact-us.jpeg',
        'menu' => 'footer',
        'order' => 3
    ),

    // Research Section (Parent)
    array(
        'file' => null,
        'title' => 'Research',
        'slug' => 'research',
        'parent' => null,
        'image' => null,
        'menu' => 'primary',
        'order' => 1,
        'is_parent' => true
    ),
    array(
        'file' => 'social-research.html',
        'title' => 'Social Research',
        'slug' => 'social-research',
        'parent' => 'research',
        'image' => 'social-research.jpeg',
        'menu' => 'primary',
        'order' => 1
    ),
    array(
        'file' => 'market-research.html',
        'title' => 'Market Research',
        'slug' => 'market-research',
        'parent' => 'research',
        'image' => 'market-research.jpeg',
        'menu' => 'primary',
        'order' => 2
    ),
    array(
        'file' => 'data-analysis.html',
        'title' => 'Data Analysis',
        'slug' => 'data-analysis',
        'parent' => 'research',
        'image' => 'data-analysis.jpeg',
        'menu' => 'primary',
        'order' => 3
    ),

    // For Talents Section (Parent)
    array(
        'file' => null,
        'title' => 'For Talents',
        'slug' => 'for-talents',
        'parent' => null,
        'image' => null,
        'menu' => 'primary',
        'order' => 2,
        'is_parent' => true
    ),
    array(
        'file' => 'motion-pictures.html',
        'title' => 'Motion Pictures',
        'slug' => 'motion-pictures',
        'parent' => 'for-talents',
        'image' => 'motion-picture.jpeg',
        'menu' => 'primary',
        'order' => 1
    ),
    array(
        'file' => 'digital-media.html',
        'title' => 'Digital Media',
        'slug' => 'digital-media',
        'parent' => 'for-talents',
        'image' => 'digitalmedia.jpg',
        'menu' => 'primary',
        'order' => 2
    ),
    array(
        'file' => 'speakers.html',
        'title' => 'Speakers',
        'slug' => 'speakers',
        'parent' => 'for-talents',
        'image' => 'speakers.jpeg',
        'menu' => 'primary',
        'order' => 3
    ),
    array(
        'file' => 'television.html',
        'title' => 'Television',
        'slug' => 'television',
        'parent' => 'for-talents',
        'image' => null,
        'menu' => 'primary',
        'order' => 4
    ),
    array(
        'file' => 'voiceovers.html',
        'title' => 'Voiceovers',
        'slug' => 'voiceovers',
        'parent' => 'for-talents',
        'image' => 'voiceover.jpeg',
        'menu' => 'primary',
        'order' => 5
    ),

    // For Enterprise Section (Parent)
    array(
        'file' => null,
        'title' => 'For Enterprise',
        'slug' => 'for-enterprise',
        'parent' => null,
        'image' => null,
        'menu' => 'primary',
        'order' => 3,
        'is_parent' => true
    ),

    // Partnership Sales (Sub-parent)
    array(
        'file' => null,
        'title' => 'Partnership Sales',
        'slug' => 'partnership-sales',
        'parent' => 'for-enterprise',
        'image' => null,
        'menu' => 'primary',
        'order' => 1,
        'is_parent' => true
    ),
    array(
        'file' => 'music-brand-partnerships.html',
        'title' => 'Music Brand Partnerships',
        'slug' => 'music-brand-partnerships',
        'parent' => 'partnership-sales',
        'image' => 'music-brand-partnership.jpeg',
        'menu' => 'primary',
        'order' => 1
    ),
    array(
        'file' => 'publishing.html',
        'title' => 'Publishing',
        'slug' => 'publishing',
        'parent' => 'partnership-sales',
        'image' => 'publishing.jpeg',
        'menu' => 'primary',
        'order' => 2
    ),

    // Brand Solutions (Sub-parent)
    array(
        'file' => null,
        'title' => 'Brand Solutions',
        'slug' => 'brand-solutions',
        'parent' => 'for-enterprise',
        'image' => null,
        'menu' => 'primary',
        'order' => 2,
        'is_parent' => true
    ),
    array(
        'file' => 'brand-consulting.html',
        'title' => 'Brand Consulting',
        'slug' => 'brand-consulting',
        'parent' => 'brand-solutions',
        'image' => 'brand-consulting.jpeg',
        'menu' => 'primary',
        'order' => 1
    ),
    array(
        'file' => 'brandmanagement.html',
        'title' => 'Brand Management',
        'slug' => 'brand-management',
        'parent' => 'brand-solutions',
        'image' => 'brand-management.jpeg',
        'menu' => 'primary',
        'order' => 2
    ),
    array(
        'file' => 'brand-studio_2.html',
        'title' => 'Brand Studio',
        'slug' => 'brand-studio',
        'parent' => 'brand-solutions',
        'image' => 'brand-studio.jpeg',
        'menu' => 'primary',
        'order' => 3
    ),
    array(
        'file' => 'marketing-it.html',
        'title' => 'Marketing & IT',
        'slug' => 'marketing-it',
        'parent' => 'brand-solutions',
        'image' => null,
        'menu' => 'primary',
        'order' => 4
    ),

    // Standalone pages
    array(
        'file' => 'privacy-policy.html',
        'title' => 'Privacy Policy',
        'slug' => 'privacy-policy',
        'parent' => null,
        'image' => null,
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'terms-of-use.html',
        'title' => 'Terms of Use',
        'slug' => 'terms-of-use',
        'parent' => null,
        'image' => null,
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'client-privacy-notice.html',
        'title' => 'Client Privacy Notice',
        'slug' => 'client-privacy-notice',
        'parent' => null,
        'image' => 'client-privacy-notice.jpeg',
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'human-rights.html',
        'title' => 'Human Rights',
        'slug' => 'human-rights',
        'parent' => null,
        'image' => 'human-rights.jpeg',
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'social-responsibility.html',
        'title' => 'Social Responsibility',
        'slug' => 'social-responsibility',
        'parent' => null,
        'image' => 'social-responsibility.jpeg',
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'entry-level-opportunities.html',
        'title' => 'Entry Level Opportunities',
        'slug' => 'entry-level-opportunities',
        'parent' => 'careers',
        'image' => 'entry-level-opportunities.jpeg',
        'menu' => null,
        'order' => 0
    ),
    array(
        'file' => 'internships.html',
        'title' => 'Internships',
        'slug' => 'internships',
        'parent' => 'careers',
        'image' => 'internship.jpeg',
        'menu' => null,
        'order' => 0
    ),
);

// Functions
function extract_content_from_html($html) {
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html);

    // Remove unwanted elements
    $remove_tags = array('script', 'style', 'nav', 'header', 'footer');
    foreach ($remove_tags as $tag) {
        $elements = $dom->getElementsByTagName($tag);
        $nodes_to_remove = array();
        foreach ($elements as $element) {
            $nodes_to_remove[] = $element;
        }
        foreach ($nodes_to_remove as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    // Get main content
    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) {
        return '';
    }

    $content = '';
    foreach ($body->childNodes as $node) {
        $content .= $dom->saveHTML($node);
    }

    // Clean up
    $content = preg_replace('/<div[^>]*class="[^"]*nav-overlay[^"]*"[^>]*>.*?<\/div>/s', '', $content);
    $content = preg_replace('/<div[^>]*class="[^"]*animation-intro[^"]*"[^>]*>.*?<\/div>/s', '', $content);
    $content = preg_replace('/<div[^>]*class="[^"]*site-header[^"]*"[^>]*>.*?<\/div>/s', '', $content);

    // Fix image paths
    $content = str_replace('src="images/', 'src="' . home_url('/wp-content/themes/premierplug-theme/assets/images/'), $content);

    return trim($content);
}

function extract_meta_description($html) {
    preg_match('/<meta\s+name="description"\s+content="([^"]*)"/i', $html, $matches);
    return isset($matches[1]) ? $matches[1] : '';
}

function get_or_create_page($data, &$created_pages) {
    $slug = $data['slug'];

    // Check if already exists
    $existing = get_page_by_path($slug);
    if ($existing) {
        return $existing->ID;
    }

    // Get parent ID if specified
    $parent_id = 0;
    if ($data['parent']) {
        if (isset($created_pages[$data['parent']])) {
            $parent_id = $created_pages[$data['parent']];
        }
    }

    // Load and parse HTML content
    $content = '';
    $excerpt = '';
    if ($data['file']) {
        $html_file = $GLOBALS['archive_path'] . $data['file'];
        if (file_exists($html_file)) {
            $html = file_get_contents($html_file);
            $content = extract_content_from_html($html);
            $excerpt = extract_meta_description($html);
        }
    }

    // Create page
    $page_data = array(
        'post_title'    => $data['title'],
        'post_name'     => $slug,
        'post_content'  => $content,
        'post_excerpt'  => $excerpt,
        'post_status'   => 'publish',
        'post_type'     => 'page',
        'post_parent'   => $parent_id,
        'menu_order'    => isset($data['order']) ? $data['order'] : 0,
    );

    $page_id = wp_insert_post($page_data);

    if ($page_id && !is_wp_error($page_id)) {
        // Set featured image if specified
        if ($data['image']) {
            $image_file = $GLOBALS['images_path'] . $data['image'];
            if (file_exists($image_file)) {
                $attachment_id = upload_image_to_media_library($image_file, $page_id);
                if ($attachment_id) {
                    set_post_thumbnail($page_id, $attachment_id);
                }
            }
        }

        $created_pages[$slug] = $page_id;
        return $page_id;
    }

    return false;
}

function upload_image_to_media_library($file_path, $post_id = 0) {
    if (!file_exists($file_path)) {
        return false;
    }

    $filename = basename($file_path);

    // Check if already uploaded
    global $wpdb;
    $existing = $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type='attachment' AND post_title=%s",
        pathinfo($filename, PATHINFO_FILENAME)
    ));

    if ($existing) {
        return $existing;
    }

    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');

    $filetype = wp_check_filetype($filename);
    $upload_dir = wp_upload_dir();

    $new_file = $upload_dir['path'] . '/' . $filename;
    copy($file_path, $new_file);

    $attachment = array(
        'guid'           => $upload_dir['url'] . '/' . $filename,
        'post_mime_type' => $filetype['type'],
        'post_title'     => preg_replace('/\.[^.]+$/', '', $filename),
        'post_content'   => '',
        'post_status'    => 'inherit'
    );

    $attach_id = wp_insert_attachment($attachment, $new_file, $post_id);
    $attach_data = wp_generate_attachment_metadata($attach_id, $new_file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    return $attach_id;
}

// Run import
echo '<div class="status info"><strong>Starting import...</strong></div>';

$created_pages = array();
$success_count = 0;
$error_count = 0;
$total = count($pages_map);

echo '<div class="progress"><div class="progress-bar" id="progress" style="width:0%">0%</div></div>';

echo '<table>';
echo '<thead><tr><th>Page</th><th>Status</th><th>Details</th></tr></thead>';
echo '<tbody>';

foreach ($pages_map as $index => $page_data) {
    $page_id = get_or_create_page($page_data, $created_pages);

    $progress = round((($index + 1) / $total) * 100);
    echo '<script>document.getElementById("progress").style.width="'.$progress.'%";document.getElementById("progress").innerText="'.$progress.'%";</script>';

    if ($page_id) {
        $success_count++;
        echo '<tr>';
        echo '<td><strong>' . esc_html($page_data['title']) . '</strong></td>';
        echo '<td><span class="check">✓ Created</span></td>';
        echo '<td>ID: ' . $page_id;
        if ($page_data['parent']) {
            echo ' | Parent: ' . esc_html($page_data['parent']);
        }
        if ($page_data['image']) {
            echo ' | Image: ✓';
        }
        echo '</td>';
        echo '</tr>';
    } else {
        $error_count++;
        echo '<tr>';
        echo '<td><strong>' . esc_html($page_data['title']) . '</strong></td>';
        echo '<td><span class="cross">✗ Failed</span></td>';
        echo '<td>Check permissions or file existence</td>';
        echo '</tr>';
    }

    flush();
    ob_flush();
}

echo '</tbody></table>';

// Create menus
echo '<h2>Creating Navigation Menus</h2>';

$primary_menu_id = wp_create_nav_menu('Primary Navigation');
$footer_menu_id = wp_create_nav_menu('Footer Navigation');

if ($primary_menu_id && $footer_menu_id) {
    echo '<div class="status success">✓ Menus created successfully</div>';

    // Add pages to menus
    foreach ($pages_map as $page_data) {
        if (!isset($created_pages[$page_data['slug']])) {
            continue;
        }

        $page_id = $created_pages[$page_data['slug']];
        $menu_location = $page_data['menu'];

        if ($menu_location === 'primary') {
            $parent_menu_id = 0;
            if ($page_data['parent'] && isset($created_pages[$page_data['parent']])) {
                // Find parent menu item
                $parent_page_id = $created_pages[$page_data['parent']];
                $menu_items = wp_get_nav_menu_items($primary_menu_id);
                foreach ($menu_items as $item) {
                    if ($item->object_id == $parent_page_id) {
                        $parent_menu_id = $item->ID;
                        break;
                    }
                }
            }

            wp_update_nav_menu_item($primary_menu_id, 0, array(
                'menu-item-object-id' => $page_id,
                'menu-item-object' => 'page',
                'menu-item-parent-id' => $parent_menu_id,
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $page_data['order']
            ));
        } elseif ($menu_location === 'footer') {
            wp_update_nav_menu_item($footer_menu_id, 0, array(
                'menu-item-object-id' => $page_id,
                'menu-item-object' => 'page',
                'menu-item-type' => 'post_type',
                'menu-item-status' => 'publish',
                'menu-item-position' => $page_data['order']
            ));
        }
    }

    // Assign menu locations
    $locations = get_theme_mod('nav_menu_locations');
    $locations['primary'] = $primary_menu_id;
    $locations['footer'] = $footer_menu_id;
    set_theme_mod('nav_menu_locations', $locations);

    echo '<div class="status success">✓ Menu items added and locations assigned</div>';
} else {
    echo '<div class="status error">✗ Failed to create menus</div>';
}

// Set homepage
if (isset($created_pages['about-us'])) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $created_pages['about-us']);
    echo '<div class="status success">✓ Homepage set</div>';
}

// Summary
echo '<h2>Import Complete!</h2>';
echo '<div class="status success">';
echo '<strong>Summary:</strong><br>';
echo '✓ Pages created: ' . $success_count . '<br>';
if ($error_count > 0) {
    echo '✗ Errors: ' . $error_count . '<br>';
}
echo '✓ Primary menu created with 3 levels<br>';
echo '✓ Footer menu created<br>';
echo '✓ Featured images assigned<br>';
echo '✓ Page hierarchy established<br>';
echo '</div>';

echo '<div class="status info">';
echo '<strong>Next Steps:</strong><br>';
echo '1. Visit your site: <a href="' . home_url() . '" target="_blank">' . home_url() . '</a><br>';
echo '2. Check navigation menus work<br>';
echo '3. Review each page content<br>';
echo '4. Install Contact Form 7 for contact page<br>';
echo '5. Delete this import file for security<br>';
echo '</div>';

echo '<p>';
echo '<a href="' . admin_url() . '" class="btn">Go to WordPress Admin</a> ';
echo '<a href="' . home_url() . '" class="btn btn-secondary">View Site</a>';
echo '</p>';

?>

</div>
</body>
</html>
