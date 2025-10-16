<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Talent_Sync {

    private $supabase;

    public function __construct() {
        $this->supabase = PPTM_Supabase_Client::get_instance();

        add_action('pptm_after_save_talent', array($this, 'sync_talent_to_supabase'), 10, 2);
        add_action('before_delete_post', array($this, 'delete_talent_from_supabase'));
        add_action('transition_post_status', array($this, 'handle_talent_status_change'), 10, 3);
    }

    public function sync_talent_to_supabase($post_id, $post) {
        if (!$this->supabase->is_configured() || !get_option('pptm_sync_enabled', true)) {
            return;
        }

        if ($post->post_status !== 'publish') {
            return;
        }

        $talent_data = $this->prepare_talent_data($post_id, $post);

        $segments = $this->get_talent_segments($post_id);
        $skills = $this->get_talent_skills($post_id);

        $existing = $this->supabase->select('talents', array(
            'wordpress_post_id' => 'eq.' . $post_id
        ));

        if (!is_wp_error($existing) && !empty($existing)) {
            $supabase_id = $existing[0]['id'];
            $this->supabase->update('talents', $talent_data, array('id' => 'eq.' . $supabase_id));
        } else {
            $result = $this->supabase->insert('talents', $talent_data);
            if (!is_wp_error($result) && !empty($result)) {
                $supabase_id = $result[0]['id'];
                update_post_meta($post_id, '_pptm_supabase_id', $supabase_id);
            }
        }

        if (isset($supabase_id)) {
            $this->sync_talent_segments($supabase_id, $segments);
            $this->sync_talent_skills($supabase_id, $skills);
            $this->sync_talent_contact($supabase_id, $post_id);
            $this->sync_talent_media($supabase_id, $post_id);
        }
    }

    private function prepare_talent_data($post_id, $post) {
        $headline = get_post_meta($post_id, '_pptm_headline', true);
        $experience_years = get_post_meta($post_id, '_pptm_experience_years', true);
        $availability = get_post_meta($post_id, '_pptm_availability_status', true);
        $featured = get_post_meta($post_id, '_pptm_featured', true);
        $view_count = get_post_meta($post_id, '_pptm_view_count', true);

        $profile_image_url = '';
        if (has_post_thumbnail($post_id)) {
            $profile_image_url = get_the_post_thumbnail_url($post_id, 'full');
        }

        $primary_segment_id = $this->get_primary_segment_supabase_id($post_id);

        return array(
            'name' => $post->post_title,
            'slug' => $post->post_name,
            'headline' => $headline,
            'bio' => $post->post_content,
            'primary_segment_id' => $primary_segment_id,
            'featured' => ($featured === '1'),
            'availability_status' => $availability ?: 'available',
            'experience_years' => absint($experience_years),
            'profile_image_url' => $profile_image_url,
            'published' => true,
            'wordpress_post_id' => $post_id,
            'view_count' => absint($view_count),
            'updated_at' => current_time('mysql', true),
        );
    }

    private function get_primary_segment_supabase_id($post_id) {
        $terms = get_the_terms($post_id, 'talent_segment');
        if (!$terms || is_wp_error($terms)) {
            return null;
        }

        $segment_slug = $terms[0]->slug;

        $segments = $this->supabase->select('talent_segments', array(
            'slug' => 'eq.' . $segment_slug
        ));

        if (!is_wp_error($segments) && !empty($segments)) {
            return $segments[0]['id'];
        }

        return null;
    }

    private function get_talent_segments($post_id) {
        $terms = get_the_terms($post_id, 'talent_segment');
        if (!$terms || is_wp_error($terms)) {
            return array();
        }

        $segment_slugs = array_map(function($term) {
            return $term->slug;
        }, $terms);

        return $segment_slugs;
    }

    private function get_talent_skills($post_id) {
        $terms = get_the_terms($post_id, 'talent_skill');
        if (!$terms || is_wp_error($terms)) {
            return array();
        }

        $skill_names = array_map(function($term) {
            return $term->name;
        }, $terms);

        return $skill_names;
    }

    private function sync_talent_segments($supabase_talent_id, $segment_slugs) {
        $this->supabase->delete('talent_segment_relationships', array(
            'talent_id' => 'eq.' . $supabase_talent_id
        ));

        foreach ($segment_slugs as $slug) {
            $segments = $this->supabase->select('talent_segments', array(
                'slug' => 'eq.' . $slug
            ));

            if (!is_wp_error($segments) && !empty($segments)) {
                $segment_id = $segments[0]['id'];
                $this->supabase->insert('talent_segment_relationships', array(
                    'talent_id' => $supabase_talent_id,
                    'segment_id' => $segment_id
                ));
            }
        }
    }

    private function sync_talent_skills($supabase_talent_id, $skill_names) {
        $this->supabase->delete('talent_skill_relationships', array(
            'talent_id' => 'eq.' . $supabase_talent_id
        ));

        foreach ($skill_names as $skill_name) {
            $skill_slug = sanitize_title($skill_name);

            $skills = $this->supabase->select('talent_skills', array(
                'slug' => 'eq.' . $skill_slug
            ));

            if (is_wp_error($skills) || empty($skills)) {
                $result = $this->supabase->insert('talent_skills', array(
                    'name' => $skill_name,
                    'slug' => $skill_slug
                ));

                if (!is_wp_error($result) && !empty($result)) {
                    $skill_id = $result[0]['id'];
                } else {
                    continue;
                }
            } else {
                $skill_id = $skills[0]['id'];
            }

            $this->supabase->insert('talent_skill_relationships', array(
                'talent_id' => $supabase_talent_id,
                'skill_id' => $skill_id
            ));
        }
    }

    private function sync_talent_contact($supabase_talent_id, $post_id) {
        $contact_data = array(
            'talent_id' => $supabase_talent_id,
            'email' => get_post_meta($post_id, '_pptm_contact_email', true),
            'phone' => get_post_meta($post_id, '_pptm_contact_phone', true),
            'website' => get_post_meta($post_id, '_pptm_website', true),
            'social_instagram' => get_post_meta($post_id, '_pptm_social_instagram', true),
            'social_linkedin' => get_post_meta($post_id, '_pptm_social_linkedin', true),
            'social_twitter' => get_post_meta($post_id, '_pptm_social_twitter', true),
            'social_youtube' => get_post_meta($post_id, '_pptm_social_youtube', true),
            'updated_at' => current_time('mysql', true),
        );

        $this->supabase->upsert('talent_contacts', $contact_data);
    }

    private function sync_talent_media($supabase_talent_id, $post_id) {
        $this->supabase->delete('talent_media', array(
            'talent_id' => 'eq.' . $supabase_talent_id
        ));

        $portfolio_items = get_post_meta($post_id, '_pptm_portfolio_items', true);
        if (!is_array($portfolio_items)) {
            return;
        }

        foreach ($portfolio_items as $index => $item) {
            if (empty($item['url'])) {
                continue;
            }

            $this->supabase->insert('talent_media', array(
                'talent_id' => $supabase_talent_id,
                'media_type' => $item['type'] ?? 'image',
                'file_url' => $item['url'],
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'order_position' => $index
            ));
        }
    }

    public function delete_talent_from_supabase($post_id) {
        if (get_post_type($post_id) !== 'pp_talent') {
            return;
        }

        if (!$this->supabase->is_configured()) {
            return;
        }

        $this->supabase->delete('talents', array(
            'wordpress_post_id' => 'eq.' . $post_id
        ));
    }

    public function handle_talent_status_change($new_status, $old_status, $post) {
        if ($post->post_type !== 'pp_talent') {
            return;
        }

        if (!$this->supabase->is_configured()) {
            return;
        }

        $published = ($new_status === 'publish');

        $this->supabase->update('talents', array(
            'published' => $published
        ), array(
            'wordpress_post_id' => 'eq.' . $post->ID
        ));
    }
}
