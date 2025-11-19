<?php

if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Supabase {

    private static $instance = null;
    private $supabase_url;
    private $supabase_key;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->supabase_url = defined('SUPABASE_URL') ? SUPABASE_URL : get_option('pptm_supabase_url', '');
        $this->supabase_key = defined('SUPABASE_KEY') ? SUPABASE_KEY : get_option('pptm_supabase_key', '');
    }

    public function sync_talent($post_id) {
        if (!$this->is_configured()) {
            return false;
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'talent') {
            return false;
        }

        $talent_data = $this->prepare_talent_data($post_id);

        return $this->upsert_talent($talent_data);
    }

    private function prepare_talent_data($post_id) {
        $post = get_post($post_id);

        $categories = wp_get_post_terms($post_id, 'talent_category', array('fields' => 'names'));
        $skills = wp_get_post_terms($post_id, 'talent_skill', array('fields' => 'names'));
        $availability = wp_get_post_terms($post_id, 'talent_availability', array('fields' => 'names'));

        $thumbnail_id = get_post_thumbnail_id($post_id);
        $thumbnail_url = $thumbnail_id ? wp_get_attachment_url($thumbnail_id) : '';

        return array(
            'id' => $post_id,
            'name' => $post->post_title,
            'bio' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'email' => get_post_meta($post_id, '_talent_email', true),
            'phone' => get_post_meta($post_id, '_talent_phone', true),
            'website' => get_post_meta($post_id, '_talent_website', true),
            'categories' => $categories,
            'skills' => $skills,
            'availability' => !empty($availability) ? $availability[0] : 'available',
            'photo_url' => $thumbnail_url,
            'experience_years' => (int) get_post_meta($post_id, '_talent_experience', true),
            'rate' => get_post_meta($post_id, '_talent_rate', true),
            'location' => get_post_meta($post_id, '_talent_location', true),
            'social_links' => array(
                'linkedin' => get_post_meta($post_id, '_talent_linkedin', true),
                'twitter' => get_post_meta($post_id, '_talent_twitter', true),
                'instagram' => get_post_meta($post_id, '_talent_instagram', true),
                'youtube' => get_post_meta($post_id, '_talent_youtube', true),
            ),
            'status' => $post->post_status,
            'created_at' => $post->post_date,
            'updated_at' => $post->post_modified,
        );
    }

    private function upsert_talent($data) {
        $response = wp_remote_post($this->supabase_url . '/rest/v1/talents', array(
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates'
            ),
            'body' => json_encode($data),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('Supabase sync error: ' . $response->get_error_message());
            return false;
        }

        $code = wp_remote_retrieve_response_code($response);
        return ($code === 200 || $code === 201);
    }

    public function delete_talent($post_id) {
        if (!$this->is_configured()) {
            return false;
        }

        $response = wp_remote_request($this->supabase_url . '/rest/v1/talents?id=eq.' . $post_id, array(
            'method' => 'DELETE',
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('Supabase delete error: ' . $response->get_error_message());
            return false;
        }

        return true;
    }

    public function get_talents($filters = array()) {
        if (!$this->is_configured()) {
            return array();
        }

        $query_params = array();

        if (!empty($filters['category'])) {
            $query_params['categories'] = 'cs.{' . $filters['category'] . '}';
        }

        if (!empty($filters['skill'])) {
            $query_params['skills'] = 'cs.{' . $filters['skill'] . '}';
        }

        if (!empty($filters['availability'])) {
            $query_params['availability'] = 'eq.' . $filters['availability'];
        }

        $url = $this->supabase_url . '/rest/v1/talents';
        if (!empty($query_params)) {
            $url .= '?' . http_build_query($query_params);
        }

        $response = wp_remote_get($url, array(
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
            ),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            error_log('Supabase fetch error: ' . $response->get_error_message());
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $talents = json_decode($body, true);

        return is_array($talents) ? $talents : array();
    }

    public function is_configured() {
        return !empty($this->supabase_url) && !empty($this->supabase_key);
    }
}

add_action('save_post_talent', function($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    PPTM_Supabase::get_instance()->sync_talent($post_id);
}, 10, 1);

add_action('before_delete_post', function($post_id) {
    $post = get_post($post_id);
    if ($post && $post->post_type === 'talent') {
        PPTM_Supabase::get_instance()->delete_talent($post_id);
    }
});
