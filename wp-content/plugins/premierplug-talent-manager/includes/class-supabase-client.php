<?php
if (!defined('ABSPATH')) {
    exit;
}

class PPTM_Supabase_Client {

    private static $instance = null;
    private $supabase_url;
    private $supabase_key;
    private $cache_duration = 900;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->supabase_url = defined('VITE_SUPABASE_URL') ? VITE_SUPABASE_URL : getenv('VITE_SUPABASE_URL');
        $this->supabase_key = defined('VITE_SUPABASE_SUPABASE_ANON_KEY') ? VITE_SUPABASE_SUPABASE_ANON_KEY : getenv('VITE_SUPABASE_SUPABASE_ANON_KEY');

        if (empty($this->supabase_url) || empty($this->supabase_key)) {
            $this->load_env_file();
        }

        $cache_duration = get_option('pptm_cache_duration', 900);
        $this->cache_duration = absint($cache_duration);
    }

    private function load_env_file() {
        $env_file = ABSPATH . '.env';
        if (file_exists($env_file)) {
            $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                if ($key === 'VITE_SUPABASE_URL') {
                    $this->supabase_url = $value;
                } elseif ($key === 'VITE_SUPABASE_SUPABASE_ANON_KEY') {
                    $this->supabase_key = $value;
                }
            }
        }
    }

    private function make_request($method, $endpoint, $data = null, $params = null) {
        if (empty($this->supabase_url) || empty($this->supabase_key)) {
            return new WP_Error('supabase_config', 'Supabase configuration is missing');
        }

        $url = trailingslashit($this->supabase_url) . 'rest/v1/' . $endpoint;

        if (!empty($params)) {
            $url = add_query_arg($params, $url);
        }

        $args = array(
            'method' => $method,
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'return=representation'
            ),
            'timeout' => 30,
        );

        if ($data !== null) {
            $args['body'] = wp_json_encode($data);
        }

        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 400) {
            return new WP_Error('supabase_error', $body, array('status' => $code));
        }

        return json_decode($body, true);
    }

    public function select($table, $params = array()) {
        $cache_key = 'pptm_' . $table . '_' . md5(serialize($params));
        $cached = get_transient($cache_key);

        if (false !== $cached) {
            return $cached;
        }

        $result = $this->make_request('GET', $table, null, $params);

        if (!is_wp_error($result)) {
            set_transient($cache_key, $result, $this->cache_duration);
        }

        return $result;
    }

    public function insert($table, $data) {
        $result = $this->make_request('POST', $table, $data);

        $this->clear_table_cache($table);

        return $result;
    }

    public function update($table, $data, $params) {
        $result = $this->make_request('PATCH', $table, $data, $params);

        $this->clear_table_cache($table);

        return $result;
    }

    public function delete($table, $params) {
        $result = $this->make_request('DELETE', $table, null, $params);

        $this->clear_table_cache($table);

        return $result;
    }

    public function upsert($table, $data) {
        $args = array(
            'method' => 'POST',
            'headers' => array(
                'apikey' => $this->supabase_key,
                'Authorization' => 'Bearer ' . $this->supabase_key,
                'Content-Type' => 'application/json',
                'Prefer' => 'resolution=merge-duplicates,return=representation'
            ),
            'body' => wp_json_encode($data),
            'timeout' => 30,
        );

        $url = trailingslashit($this->supabase_url) . 'rest/v1/' . $table;
        $response = wp_remote_request($url, $args);

        if (is_wp_error($response)) {
            return $response;
        }

        $body = wp_remote_retrieve_body($response);
        $code = wp_remote_retrieve_response_code($response);

        if ($code >= 400) {
            return new WP_Error('supabase_error', $body, array('status' => $code));
        }

        $this->clear_table_cache($table);

        return json_decode($body, true);
    }

    public function clear_table_cache($table) {
        global $wpdb;

        $pattern = '_transient_pptm_' . $table . '_%';
        $sql = $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $pattern
        );
        $wpdb->query($sql);
    }

    public function clear_all_cache() {
        global $wpdb;

        $pattern = '_transient_pptm_%';
        $sql = $wpdb->prepare(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
            $pattern
        );
        $wpdb->query($sql);
    }

    public function is_configured() {
        return !empty($this->supabase_url) && !empty($this->supabase_key);
    }
}
