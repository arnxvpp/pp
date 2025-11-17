<?php
/**
 * WordPress Site Diagnostic Tool
 * Upload this file to your WordPress root directory
 * Access via: https://wp.premierplug.org/diagnostic-check.php
 *
 * IMPORTANT: Delete this file after use (security risk)
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <title>PremierPlug WordPress Diagnostic</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .header {
            background: #BC1F2F;
            color: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .section {
            background: white;
            padding: 25px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .check {
            display: flex;
            justify-content: space-between;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        .check:last-child { border-bottom: none; }
        .status {
            font-weight: bold;
            padding: 4px 12px;
            border-radius: 4px;
        }
        .status.pass { background: #d4edda; color: #155724; }
        .status.fail { background: #f8d7da; color: #721c24; }
        .status.warn { background: #fff3cd; color: #856404; }
        h2 { color: #BC1F2F; margin-top: 0; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin: 10px 0; }
        .error-box { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 4px; margin: 10px 0; border-left: 4px solid #dc3545; }
        pre { background: #f4f4f4; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .button {
            display: inline-block;
            background: #BC1F2F;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>🔍 PremierPlug WordPress Diagnostic Tool</h1>
    <p>Checking system requirements and configuration...</p>
</div>

<?php
// Initialize results
$results = array(
    'php_version' => array('status' => 'unknown', 'message' => ''),
    'wordpress' => array('status' => 'unknown', 'message' => ''),
    'database' => array('status' => 'unknown', 'message' => ''),
    'theme' => array('status' => 'unknown', 'message' => ''),
    'plugin' => array('status' => 'unknown', 'message' => ''),
    'permissions' => array('status' => 'unknown', 'message' => ''),
    'supabase' => array('status' => 'unknown', 'message' => ''),
);

// Check 1: PHP Version
$php_version = phpversion();
if (version_compare($php_version, '7.4.0', '>=')) {
    $results['php_version'] = array('status' => 'pass', 'message' => 'PHP ' . $php_version);
} else {
    $results['php_version'] = array('status' => 'fail', 'message' => 'PHP ' . $php_version . ' (Requires 7.4+)');
}

// Check 2: WordPress Files
if (file_exists('wp-config.php') && file_exists('wp-load.php')) {
    $results['wordpress'] = array('status' => 'pass', 'message' => 'WordPress files found');

    // Try to load WordPress
    try {
        define('WP_USE_THEMES', false);
        require_once('wp-load.php');

        if (defined('WP_DEBUG')) {
            $debug_status = WP_DEBUG ? 'Enabled' : 'Disabled';
            $results['wordpress']['message'] .= ' (Debug: ' . $debug_status . ')';
        }
    } catch (Exception $e) {
        $results['wordpress'] = array('status' => 'fail', 'message' => 'WordPress load failed: ' . $e->getMessage());
    }
} else {
    $results['wordpress'] = array('status' => 'fail', 'message' => 'WordPress files not found');
}

// Check 3: Database Connection
if (defined('DB_HOST')) {
    try {
        $conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($conn) {
            $results['database'] = array('status' => 'pass', 'message' => 'Connected to ' . DB_NAME);
            mysqli_close($conn);
        } else {
            $results['database'] = array('status' => 'fail', 'message' => 'Connection failed: ' . mysqli_connect_error());
        }
    } catch (Exception $e) {
        $results['database'] = array('status' => 'fail', 'message' => 'Error: ' . $e->getMessage());
    }
} else {
    $results['database'] = array('status' => 'fail', 'message' => 'Database constants not defined');
}

// Check 4: Theme Files
$theme_path = 'wp-content/themes/premierplug-theme';
if (is_dir($theme_path)) {
    $required_files = array('style.css', 'functions.php', 'index.php', 'header.php', 'footer.php');
    $missing_files = array();

    foreach ($required_files as $file) {
        if (!file_exists($theme_path . '/' . $file)) {
            $missing_files[] = $file;
        }
    }

    if (empty($missing_files)) {
        $results['theme'] = array('status' => 'pass', 'message' => 'All theme files present');
    } else {
        $results['theme'] = array('status' => 'fail', 'message' => 'Missing: ' . implode(', ', $missing_files));
    }
} else {
    $results['theme'] = array('status' => 'fail', 'message' => 'Theme directory not found');
}

// Check 5: Plugin Files
$plugin_path = 'wp-content/plugins/premierplug-talent-manager';
if (is_dir($plugin_path)) {
    if (file_exists($plugin_path . '/premierplug-talent-manager.php')) {
        $results['plugin'] = array('status' => 'pass', 'message' => 'Plugin files present');
    } else {
        $results['plugin'] = array('status' => 'fail', 'message' => 'Main plugin file missing');
    }
} else {
    $results['plugin'] = array('status' => 'warn', 'message' => 'Plugin directory not found (not uploaded yet?)');
}

// Check 6: File Permissions
$upload_dir = 'wp-content/uploads';
if (is_dir($upload_dir)) {
    if (is_writable($upload_dir)) {
        $results['permissions'] = array('status' => 'pass', 'message' => 'Uploads directory writable');
    } else {
        $results['permissions'] = array('status' => 'fail', 'message' => 'Uploads directory not writable');
    }
} else {
    $results['permissions'] = array('status' => 'warn', 'message' => 'Uploads directory not found');
}

// Check 7: Supabase Configuration
$env_file = '.env';
if (file_exists($env_file)) {
    $env_contents = file_get_contents($env_file);
    if (strpos($env_contents, 'VITE_SUPABASE_URL') !== false && strpos($env_contents, 'VITE_SUPABASE_ANON_KEY') !== false) {
        $results['supabase'] = array('status' => 'pass', 'message' => 'Environment file configured');
    } else {
        $results['supabase'] = array('status' => 'warn', 'message' => 'Environment file incomplete');
    }
} else {
    $results['supabase'] = array('status' => 'warn', 'message' => 'Environment file not found');
}

// Display Results
?>

<div class="section">
    <h2>System Requirements</h2>

    <div class="check">
        <span>PHP Version</span>
        <span class="status <?php echo $results['php_version']['status']; ?>">
            <?php echo $results['php_version']['message']; ?>
        </span>
    </div>

    <div class="check">
        <span>WordPress Installation</span>
        <span class="status <?php echo $results['wordpress']['status']; ?>">
            <?php echo $results['wordpress']['message']; ?>
        </span>
    </div>

    <div class="check">
        <span>Database Connection</span>
        <span class="status <?php echo $results['database']['status']; ?>">
            <?php echo $results['database']['message']; ?>
        </span>
    </div>
</div>

<div class="section">
    <h2>PremierPlug Components</h2>

    <div class="check">
        <span>PremierPlug Theme</span>
        <span class="status <?php echo $results['theme']['status']; ?>">
            <?php echo $results['theme']['message']; ?>
        </span>
    </div>

    <div class="check">
        <span>Talent Manager Plugin</span>
        <span class="status <?php echo $results['plugin']['status']; ?>">
            <?php echo $results['plugin']['message']; ?>
        </span>
    </div>

    <div class="check">
        <span>Supabase Configuration</span>
        <span class="status <?php echo $results['supabase']['status']; ?>">
            <?php echo $results['supabase']['message']; ?>
        </span>
    </div>
</div>

<div class="section">
    <h2>File System</h2>

    <div class="check">
        <span>Upload Permissions</span>
        <span class="status <?php echo $results['permissions']['status']; ?>">
            <?php echo $results['permissions']['message']; ?>
        </span>
    </div>
</div>

<?php
// Check for errors in debug log
$debug_log = 'wp-content/debug.log';
if (file_exists($debug_log)) {
    $log_contents = file_get_contents($debug_log);
    $recent_errors = array_slice(explode("\n", $log_contents), -20);

    echo '<div class="section">';
    echo '<h2>Recent Debug Log (Last 20 lines)</h2>';
    echo '<div class="error-box">';
    echo '<pre>' . htmlspecialchars(implode("\n", $recent_errors)) . '</pre>';
    echo '</div>';
    echo '</div>';
}
?>

<div class="section">
    <h2>PHP Information</h2>
    <div class="info">
        <strong>PHP Version:</strong> <?php echo PHP_VERSION; ?><br>
        <strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?><br>
        <strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s<br>
        <strong>Upload Max Filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?><br>
        <strong>Post Max Size:</strong> <?php echo ini_get('post_max_size'); ?><br>
    </div>
</div>

<?php if (function_exists('get_option')): ?>
<div class="section">
    <h2>WordPress Configuration</h2>
    <div class="info">
        <strong>Site URL:</strong> <?php echo get_option('siteurl'); ?><br>
        <strong>Home URL:</strong> <?php echo get_option('home'); ?><br>
        <strong>Active Theme:</strong> <?php echo get_option('template'); ?><br>
        <strong>WordPress Version:</strong> <?php echo get_bloginfo('version'); ?><br>
    </div>
</div>

<?php
// Check active plugins
$active_plugins = get_option('active_plugins');
if ($active_plugins) {
    echo '<div class="section">';
    echo '<h2>Active Plugins</h2>';
    echo '<ul>';
    foreach ($active_plugins as $plugin) {
        echo '<li>' . htmlspecialchars($plugin) . '</li>';
    }
    echo '</ul>';
    echo '</div>';
}
?>
<?php endif; ?>

<div class="section">
    <h2>Server Information</h2>
    <div class="info">
        <strong>Server Software:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?><br>
        <strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
        <strong>Current Directory:</strong> <?php echo getcwd(); ?><br>
    </div>
</div>

<div class="section">
    <h2>Next Steps</h2>

    <?php
    $all_pass = true;
    foreach ($results as $result) {
        if ($result['status'] === 'fail') {
            $all_pass = false;
            break;
        }
    }

    if ($all_pass) {
        echo '<div class="info">';
        echo '<strong>✓ All checks passed!</strong><br><br>';
        echo 'Your WordPress installation appears healthy. If you\'re seeing a white screen:';
        echo '<ul>';
        echo '<li>Check the debug.log above for errors</li>';
        echo '<li>Try disabling all plugins via phpMyAdmin</li>';
        echo '<li>Switch to a default WordPress theme temporarily</li>';
        echo '</ul>';
        echo '</div>';
    } else {
        echo '<div class="error-box">';
        echo '<strong>⚠ Issues detected!</strong><br><br>';
        echo 'Please fix the failed checks above before proceeding.';
        echo '</div>';
    }
    ?>

    <a href="WHITE-SCREEN-FIX-GUIDE.md" class="button">View Fix Guide</a>
    <a href="/" class="button">Back to Site</a>
</div>

<div class="section" style="background: #fff3cd; border-left: 4px solid #856404;">
    <h2>⚠️ Security Warning</h2>
    <p><strong>DELETE THIS FILE IMMEDIATELY AFTER USE!</strong></p>
    <p>This diagnostic tool exposes sensitive server information. Run this command to delete it:</p>
    <pre>rm diagnostic-check.php</pre>
</div>

</body>
</html>
