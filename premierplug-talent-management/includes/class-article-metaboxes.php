<?php
/**
 * Article Metaboxes Class
 *
 * Adds metaboxes for:
 * - Linking talents to articles (on article edit screens)
 * - Displaying linked articles (on talent edit screens)
 * - Article metadata (publication date, source, author, etc.)
 *
 * @package PremierPlug_Talent_Management
 * @since 1.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Article_Metaboxes {

    /**
     * Initialize the class
     */
    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'add_metaboxes'));
        add_action('save_post', array(__CLASS__, 'save_article_metadata'), 10, 2);
        add_action('save_post', array(__CLASS__, 'save_talent_relationships'), 10, 2);
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
        add_action('wp_ajax_pptm_search_talents', array(__CLASS__, 'ajax_search_talents'));
    }

    /**
     * Get all article post types from custom post type manager
     */
    private static function get_all_article_post_types() {
        $custom_types = get_option('premierplug_custom_post_types', array());
        $article_types = array();

        if (isset($custom_types['articles']['items'])) {
            foreach ($custom_types['articles']['items'] as $type_data) {
                if (!empty($type_data['enabled'])) {
                    $article_types[] = 'article_' . $type_data['id'];
                }
            }
        }

        return $article_types;
    }

    /**
     * Check if post type is an article type
     */
    private static function is_article_type($post_type) {
        return strpos($post_type, 'article_') === 0;
    }

    /**
     * Get type label from post type
     */
    private static function get_type_label($post_type) {
        if (strpos($post_type, 'article_') !== 0) {
            return $post_type;
        }

        $post_type_obj = get_post_type_object($post_type);
        return $post_type_obj ? $post_type_obj->labels->singular_name : $post_type;
    }

    /**
     * Add metaboxes
     */
    public static function add_metaboxes() {
        $article_types = self::get_all_article_post_types();

        foreach ($article_types as $type) {
            add_meta_box(
                'pptm_article_talents',
                __('Link to Talents', 'premierplug-talent'),
                array(__CLASS__, 'render_talents_metabox'),
                $type,
                'side',
                'high'
            );

            add_meta_box(
                'pptm_article_details',
                __('Article Details', 'premierplug-talent'),
                array(__CLASS__, 'render_details_metabox'),
                $type,
                'normal',
                'high'
            );
        }

        add_meta_box(
            'pptm_talent_articles',
            __('Linked Articles', 'premierplug-talent'),
            array(__CLASS__, 'render_talent_articles_metabox'),
            'talent',
            'normal',
            'default'
        );
    }

    /**
     * Render talents metabox (on article edit screens)
     */
    public static function render_talents_metabox($post) {
        wp_nonce_field('pptm_save_article_talents', 'pptm_article_talents_nonce');

        $linked_talents = PPTM_Article_Relationships::get_article_talents($post->ID);
        $primary_talent_id = PPTM_Article_Relationships::get_primary_talent($post->ID);
        ?>
        <div class="pptm-talents-selector">
            <div class="pptm-search-talents">
                <input type="text"
                       id="pptm-talent-search"
                       placeholder="<?php esc_attr_e('Search talents...', 'premierplug-talent'); ?>"
                       class="widefat" />
                <div id="pptm-talent-search-results" class="pptm-search-results"></div>
            </div>

            <div class="pptm-selected-talents" id="pptm-selected-talents">
                <?php if (!empty($linked_talents)) : ?>
                    <?php foreach ($linked_talents as $index => $link) : ?>
                        <?php
                        $talent = get_post($link['talent_id']);
                        if (!$talent) continue;
                        ?>
                        <div class="pptm-talent-item" data-talent-id="<?php echo esc_attr($link['talent_id']); ?>">
                            <span class="pptm-drag-handle dashicons dashicons-menu"></span>
                            <span class="pptm-talent-name"><?php echo esc_html($talent->post_title); ?></span>
                            <label class="pptm-primary-label">
                                <input type="radio"
                                       name="pptm_primary_talent"
                                       value="<?php echo esc_attr($link['talent_id']); ?>"
                                       <?php checked($link['talent_id'], $primary_talent_id); ?> />
                                <?php esc_html_e('Primary', 'premierplug-talent'); ?>
                            </label>
                            <button type="button" class="pptm-remove-talent button-link-delete">
                                <?php esc_html_e('Remove', 'premierplug-talent'); ?>
                            </button>
                            <input type="hidden" name="pptm_talents[]" value="<?php echo esc_attr($link['talent_id']); ?>" />
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p class="pptm-no-talents"><?php esc_html_e('No talents linked yet. Search above to add talents.', 'premierplug-talent'); ?></p>
                <?php endif; ?>
            </div>

            <p class="description">
                <?php esc_html_e('Search and select talents to link to this article. Drag to reorder. Mark one as primary.', 'premierplug-talent'); ?>
            </p>
        </div>

        <style>
        .pptm-talents-selector { padding: 10px 0; }
        .pptm-search-talents { position: relative; margin-bottom: 15px; }
        .pptm-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        .pptm-search-results.active { display: block; }
        .pptm-search-result-item {
            padding: 8px 12px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
        }
        .pptm-search-result-item:hover { background: #f0f0f0; }
        .pptm-selected-talents { margin: 15px 0; }
        .pptm-talent-item {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 3px;
            margin-bottom: 8px;
            gap: 10px;
        }
        .pptm-drag-handle {
            cursor: move;
            color: #999;
        }
        .pptm-talent-name {
            flex: 1;
            font-weight: 500;
        }
        .pptm-primary-label {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            font-size: 12px;
        }
        .pptm-remove-talent {
            color: #b32d2e;
            text-decoration: none;
        }
        .pptm-no-talents {
            color: #666;
            font-style: italic;
            margin: 15px 0;
        }
        </style>
        <?php
    }

    /**
     * Render article details metabox
     */
    public static function render_details_metabox($post) {
        wp_nonce_field('pptm_save_article_details', 'pptm_article_details_nonce');

        $publication_date = get_post_meta($post->ID, '_pptm_publication_date', true);
        $source_name = get_post_meta($post->ID, '_pptm_source_name', true);
        $source_url = get_post_meta($post->ID, '_pptm_source_url', true);
        $author_name = get_post_meta($post->ID, '_pptm_author_name', true);
        $is_featured = get_post_meta($post->ID, '_pptm_is_featured', true);
        ?>
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="pptm_publication_date">
                        <?php esc_html_e('Publication Date', 'premierplug-talent'); ?>
                    </label>
                </th>
                <td>
                    <input type="date"
                           id="pptm_publication_date"
                           name="pptm_publication_date"
                           value="<?php echo esc_attr($publication_date); ?>"
                           class="regular-text" />
                    <p class="description">
                        <?php esc_html_e('When was this article published?', 'premierplug-talent'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="pptm_source_name">
                        <?php esc_html_e('Source/Publication', 'premierplug-talent'); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           id="pptm_source_name"
                           name="pptm_source_name"
                           value="<?php echo esc_attr($source_name); ?>"
                           class="regular-text"
                           placeholder="<?php esc_attr_e('e.g., Hollywood Reporter', 'premierplug-talent'); ?>" />
                    <p class="description">
                        <?php esc_html_e('Name of the publication or website', 'premierplug-talent'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="pptm_source_url">
                        <?php esc_html_e('Source URL', 'premierplug-talent'); ?>
                    </label>
                </th>
                <td>
                    <input type="url"
                           id="pptm_source_url"
                           name="pptm_source_url"
                           value="<?php echo esc_url($source_url); ?>"
                           class="regular-text"
                           placeholder="https://" />
                    <p class="description">
                        <?php esc_html_e('Link to the original article', 'premierplug-talent'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <label for="pptm_author_name">
                        <?php esc_html_e('Author Name', 'premierplug-talent'); ?>
                    </label>
                </th>
                <td>
                    <input type="text"
                           id="pptm_author_name"
                           name="pptm_author_name"
                           value="<?php echo esc_attr($author_name); ?>"
                           class="regular-text"
                           placeholder="<?php esc_attr_e('e.g., John Smith', 'premierplug-talent'); ?>" />
                    <p class="description">
                        <?php esc_html_e('Name of the article author', 'premierplug-talent'); ?>
                    </p>
                </td>
            </tr>

            <tr>
                <th scope="row">
                    <?php esc_html_e('Featured Article', 'premierplug-talent'); ?>
                </th>
                <td>
                    <label>
                        <input type="checkbox"
                               name="pptm_is_featured"
                               value="1"
                               <?php checked($is_featured, '1'); ?> />
                        <?php esc_html_e('Mark this as a featured article', 'premierplug-talent'); ?>
                    </label>
                    <p class="description">
                        <?php esc_html_e('Featured articles appear prominently on talent profiles', 'premierplug-talent'); ?>
                    </p>
                </td>
            </tr>
        </table>
        <?php
    }

    /**
     * Render talent articles metabox (on talent edit screens)
     */
    public static function render_talent_articles_metabox($post) {
        $articles = PPTM_Article_Relationships::get_talent_articles($post->ID);
        ?>
        <div class="pptm-talent-articles-list">
            <?php if (!empty($articles)) : ?>
                <table class="widefat fixed striped">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title', 'premierplug-talent'); ?></th>
                            <th><?php esc_html_e('Type', 'premierplug-talent'); ?></th>
                            <th><?php esc_html_e('Date', 'premierplug-talent'); ?></th>
                            <th><?php esc_html_e('Primary', 'premierplug-talent'); ?></th>
                            <th><?php esc_html_e('Actions', 'premierplug-talent'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article) : ?>
                            <?php
                            $article_post = get_post($article['article_id']);
                            if (!$article_post) continue;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html($article_post->post_title); ?></strong>
                                </td>
                                <td>
                                    <?php echo esc_html(self::get_type_label($article['post_type'])); ?>
                                </td>
                                <td>
                                    <?php echo esc_html(date_i18n(get_option('date_format'), strtotime($article['post_date']))); ?>
                                </td>
                                <td>
                                    <?php if ($article['is_primary_talent']) : ?>
                                        <span class="dashicons dashicons-star-filled" style="color: #ffb900;"></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(get_edit_post_link($article['article_id'])); ?>" class="button button-small">
                                        <?php esc_html_e('Edit', 'premierplug-talent'); ?>
                                    </a>
                                    <a href="<?php echo esc_url(get_permalink($article['article_id'])); ?>" class="button button-small" target="_blank">
                                        <?php esc_html_e('View', 'premierplug-talent'); ?>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p><?php esc_html_e('No articles linked to this talent yet.', 'premierplug-talent'); ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Save article metadata
     */
    public static function save_article_metadata($post_id, $post) {
        if (!isset($_POST['pptm_article_details_nonce']) ||
            !wp_verify_nonce($_POST['pptm_article_details_nonce'], 'pptm_save_article_details')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!self::is_article_type($post->post_type)) {
            return;
        }

        $fields = array(
            'pptm_publication_date',
            'pptm_source_name',
            'pptm_source_url',
            'pptm_author_name',
        );

        foreach ($fields as $field) {
            $meta_key = '_' . $field;
            if (isset($_POST[$field])) {
                $value = sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, $meta_key, $value);
            }
        }

        $is_featured = isset($_POST['pptm_is_featured']) ? '1' : '0';
        update_post_meta($post_id, '_pptm_is_featured', $is_featured);
    }

    /**
     * Save talent relationships
     */
    public static function save_talent_relationships($post_id, $post) {
        if (!isset($_POST['pptm_article_talents_nonce']) ||
            !wp_verify_nonce($_POST['pptm_article_talents_nonce'], 'pptm_save_article_talents')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (!self::is_article_type($post->post_type)) {
            return;
        }

        $talent_ids = isset($_POST['pptm_talents']) ? array_map('intval', $_POST['pptm_talents']) : array();
        $primary_talent_id = isset($_POST['pptm_primary_talent']) ? intval($_POST['pptm_primary_talent']) : 0;

        PPTM_Article_Relationships::replace_article_talents($post_id, $talent_ids, $primary_talent_id);
    }

    /**
     * Enqueue admin scripts
     */
    public static function enqueue_admin_scripts($hook) {
        if (!in_array($hook, array('post.php', 'post-new.php'))) {
            return;
        }

        $screen = get_current_screen();
        if (!$screen || !self::is_article_type($screen->post_type)) {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');

        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                var searchTimeout;

                $("#pptm-talent-search").on("keyup", function() {
                    clearTimeout(searchTimeout);
                    var search = $(this).val();

                    if (search.length < 2) {
                        $("#pptm-talent-search-results").removeClass("active").empty();
                        return;
                    }

                    searchTimeout = setTimeout(function() {
                        $.ajax({
                            url: ajaxurl,
                            type: "POST",
                            data: {
                                action: "pptm_search_talents",
                                search: search
                            },
                            success: function(response) {
                                if (response.success) {
                                    var html = "";
                                    $.each(response.data, function(i, talent) {
                                        html += "<div class=\"pptm-search-result-item\" data-talent-id=\"" + talent.ID + "\" data-talent-name=\"" + talent.post_title + "\">" + talent.post_title + "</div>";
                                    });
                                    $("#pptm-talent-search-results").html(html).addClass("active");
                                }
                            }
                        });
                    }, 300);
                });

                $(document).on("click", ".pptm-search-result-item", function() {
                    var talentId = $(this).data("talent-id");
                    var talentName = $(this).data("talent-name");

                    if ($(".pptm-talent-item[data-talent-id=\"" + talentId + "\"]").length > 0) {
                        alert("This talent is already linked.");
                        return;
                    }

                    $(".pptm-no-talents").remove();

                    var html = "<div class=\"pptm-talent-item\" data-talent-id=\"" + talentId + "\">";
                    html += "<span class=\"pptm-drag-handle dashicons dashicons-menu\"></span>";
                    html += "<span class=\"pptm-talent-name\">" + talentName + "</span>";
                    html += "<label class=\"pptm-primary-label\">";
                    html += "<input type=\"radio\" name=\"pptm_primary_talent\" value=\"" + talentId + "\" />";
                    html += "Primary</label>";
                    html += "<button type=\"button\" class=\"pptm-remove-talent button-link-delete\">Remove</button>";
                    html += "<input type=\"hidden\" name=\"pptm_talents[]\" value=\"" + talentId + "\" />";
                    html += "</div>";

                    $("#pptm-selected-talents").append(html);
                    $("#pptm-talent-search").val("");
                    $("#pptm-talent-search-results").removeClass("active").empty();
                });

                $(document).on("click", ".pptm-remove-talent", function() {
                    $(this).closest(".pptm-talent-item").remove();

                    if ($(".pptm-talent-item").length === 0) {
                        $("#pptm-selected-talents").html("<p class=\"pptm-no-talents\">No talents linked yet. Search above to add talents.</p>");
                    }
                });

                $("#pptm-selected-talents").sortable({
                    handle: ".pptm-drag-handle",
                    placeholder: "pptm-talent-placeholder"
                });

                $(document).on("click", function(e) {
                    if (!$(e.target).closest(".pptm-search-talents").length) {
                        $("#pptm-talent-search-results").removeClass("active");
                    }
                });
            });
        ');
    }

    /**
     * AJAX search talents
     */
    public static function ajax_search_talents() {
        check_ajax_referer('pptm_save_article_talents', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error();
        }

        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';

        if (empty($search)) {
            wp_send_json_error();
        }

        $args = array(
            'post_type' => 'talent',
            'post_status' => 'publish',
            's' => $search,
            'posts_per_page' => 10,
        );

        $query = new WP_Query($args);
        $talents = array();

        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $talents[] = array(
                    'ID' => get_the_ID(),
                    'post_title' => get_the_title(),
                );
            }
            wp_reset_postdata();
        }

        wp_send_json_success($talents);
    }
}
