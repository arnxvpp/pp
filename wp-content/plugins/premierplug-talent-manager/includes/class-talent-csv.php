<?php
/**
 * Talent CSV Import/Export Handler
 *
 * @package PremierPlug_Talent_Manager
 */

if (!defined('ABSPATH')) {
    exit;
}

class PremierPlug_Talent_CSV {

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_page'), 20);
        add_action('admin_post_talent_export_csv', array($this, 'export_talents'));
        add_action('admin_post_talent_import_csv', array($this, 'import_talents'));
    }

    /**
     * Add menu page
     */
    public function add_menu_page() {
        add_submenu_page(
            'edit.php?post_type=talent',
            'Import/Export',
            'Import/Export',
            'manage_options',
            'talent-import-export',
            array($this, 'render_page')
        );
    }

    /**
     * Render import/export page
     */
    public function render_page() {
        ?>
        <div class="wrap">
            <h1>Talent Import/Export</h1>

            <div class="card">
                <h2>Export Talents</h2>
                <p>Export all talents to CSV file for backup or migration.</p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                    <input type="hidden" name="action" value="talent_export_csv">
                    <?php wp_nonce_field('talent_export_csv', 'export_nonce'); ?>
                    <button type="submit" class="button button-primary">Export to CSV</button>
                </form>
            </div>

            <div class="card" style="margin-top: 20px;">
                <h2>Import Talents</h2>
                <p>Import talents from CSV file. File must have headers: Name, Email, Phone, Bio, Category, Skills, Rate, Status</p>
                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="talent_import_csv">
                    <?php wp_nonce_field('talent_import_csv', 'import_nonce'); ?>
                    <p>
                        <input type="file" name="csv_file" accept=".csv" required>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="update_existing" value="1">
                            Update existing talents (match by email)
                        </label>
                    </p>
                    <button type="submit" class="button button-primary">Import CSV</button>
                </form>
            </div>
        </div>
        <?php
    }

    /**
     * Export talents to CSV
     */
    public function export_talents() {
        check_admin_referer('talent_export_csv', 'export_nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $talents = get_posts(array(
            'post_type' => 'talent',
            'posts_per_page' => -1,
            'post_status' => array('publish', 'draft', 'pending')
        ));

        $filename = 'talents-export-' . date('Y-m-d-His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        fputcsv($output, array(
            'ID', 'Name', 'Email', 'Phone', 'Bio',
            'Category', 'Skills', 'Rate', 'Status', 'Featured'
        ));

        foreach ($talents as $talent) {
            $categories = wp_get_post_terms($talent->ID, 'talent_category', array('fields' => 'names'));
            $skills = wp_get_post_terms($talent->ID, 'talent_skill', array('fields' => 'names'));

            fputcsv($output, array(
                $talent->ID,
                $talent->post_title,
                get_post_meta($talent->ID, '_talent_email', true),
                get_post_meta($talent->ID, '_talent_phone', true),
                wp_trim_words($talent->post_content, 100),
                implode('|', $categories),
                implode('|', $skills),
                get_post_meta($talent->ID, '_talent_rate', true),
                $talent->post_status,
                get_post_meta($talent->ID, '_talent_featured', true) ? 'Yes' : 'No'
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Import talents from CSV
     */
    public function import_talents() {
        check_admin_referer('talent_import_csv', 'import_nonce');

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        if (empty($_FILES['csv_file']['tmp_name'])) {
            wp_die('No file uploaded');
        }

        $file = $_FILES['csv_file']['tmp_name'];
        $update_existing = isset($_POST['update_existing']);

        $handle = fopen($file, 'r');
        $headers = fgetcsv($handle);

        $imported = 0;
        $updated = 0;
        $errors = array();

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) < 5) continue;

            $talent_data = array_combine($headers, $data);

            $existing = null;
            if ($update_existing && !empty($talent_data['Email'])) {
                $existing = get_posts(array(
                    'post_type' => 'talent',
                    'meta_key' => '_talent_email',
                    'meta_value' => $talent_data['Email'],
                    'posts_per_page' => 1
                ));
            }

            $post_id = 0;

            if ($existing && !empty($existing[0])) {
                $post_id = $existing[0]->ID;
                wp_update_post(array(
                    'ID' => $post_id,
                    'post_title' => $talent_data['Name'],
                    'post_content' => $talent_data['Bio']
                ));
                $updated++;
            } else {
                $post_id = wp_insert_post(array(
                    'post_type' => 'talent',
                    'post_title' => $talent_data['Name'],
                    'post_content' => $talent_data['Bio'],
                    'post_status' => !empty($talent_data['Status']) ? $talent_data['Status'] : 'publish'
                ));
                $imported++;
            }

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_talent_email', sanitize_email($talent_data['Email']));
                update_post_meta($post_id, '_talent_phone', sanitize_text_field($talent_data['Phone']));
                update_post_meta($post_id, '_talent_rate', sanitize_text_field($talent_data['Rate']));
                update_post_meta($post_id, '_talent_featured', $talent_data['Featured'] === 'Yes' ? 1 : 0);

                if (!empty($talent_data['Category'])) {
                    $cats = explode('|', $talent_data['Category']);
                    wp_set_object_terms($post_id, $cats, 'talent_category');
                }

                if (!empty($talent_data['Skills'])) {
                    $skills = explode('|', $talent_data['Skills']);
                    wp_set_object_terms($post_id, $skills, 'talent_skill');
                }
            } else {
                $errors[] = 'Failed to import: ' . $talent_data['Name'];
            }
        }

        fclose($handle);

        $message = sprintf(
            'Import complete! Imported: %d, Updated: %d, Errors: %d',
            $imported,
            $updated,
            count($errors)
        );

        wp_redirect(add_query_arg(array(
            'page' => 'talent-import-export',
            'message' => urlencode($message)
        ), admin_url('edit.php?post_type=talent')));
        exit;
    }
}

new PremierPlug_Talent_CSV();
