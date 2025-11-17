<?php
/**
 * Emergency Debug Enabler
 * Upload this file to WordPress root
 * Visit: https://wp.premierplug.org/enable-debug.php
 * This will automatically enable debug mode and show recent errors
 * DELETE after use!
 */

// Enable all error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

echo "<h1>Enabling WordPress Debug Mode...</h1>";

// Check if wp-config.php exists
if (!file_exists('wp-config.php')) {
    die('<p style="color:red;">ERROR: wp-config.php not found in this directory!</p>');
}

// Read wp-config.php
$config = file_get_contents('wp-config.php');

// Backup original
file_put_contents('wp-config.php.backup', $config);
echo "<p style='color:green;'>✓ Backed up wp-config.php to wp-config.php.backup</p>";

// Enable debug mode
$config = preg_replace(
    "/define\s*\(\s*'WP_DEBUG'\s*,\s*false\s*\)\s*;/",
    "define('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);\ndefine('WP_DEBUG_DISPLAY', true);\n@ini_set('display_errors', 1);",
    $config
);

// If WP_DEBUG wasn't found, add it
if (strpos($config, "WP_DEBUG', true") === false) {
    $config = str_replace(
        "<?php",
        "<?php\ndefine('WP_DEBUG', true);\ndefine('WP_DEBUG_LOG', true);\ndefine('WP_DEBUG_DISPLAY', true);\n@ini_set('display_errors', 1);",
        $config
    );
}

// Save modified config
file_put_contents('wp-config.php', $config);
echo "<p style='color:green;'>✓ Debug mode enabled in wp-config.php</p>";

// Check for existing debug log
echo "<h2>Checking for existing errors...</h2>";

if (file_exists('wp-content/debug.log')) {
    echo "<h3>Recent Debug Log Entries:</h3>";
    $log = file_get_contents('wp-content/debug.log');
    $lines = explode("\n", $log);
    $recent = array_slice($lines, -50);

    echo "<pre style='background:#f8d7da; padding:15px; border-left:4px solid #dc3545; overflow-x:auto;'>";
    echo htmlspecialchars(implode("\n", $recent));
    echo "</pre>";
} else {
    echo "<p>No debug.log found yet. Visit the homepage to generate errors.</p>";
}

// Try to load WordPress and catch any errors
echo "<h2>Testing WordPress Load...</h2>";

try {
    define('WP_USE_THEMES', false);
    ob_start();
    require_once('wp-load.php');
    $output = ob_get_clean();

    echo "<p style='color:green;'>✓ WordPress core loaded successfully!</p>";

    // Check if theme functions.php has errors
    echo "<h3>Testing Theme...</h3>";
    $theme_root = wp_get_theme()->get_stylesheet_directory();
    echo "<p>Theme directory: " . htmlspecialchars($theme_root) . "</p>";

    if (file_exists($theme_root . '/functions.php')) {
        echo "<p style='color:green;'>✓ Theme functions.php exists</p>";
    } else {
        echo "<p style='color:red;'>✗ Theme functions.php NOT found!</p>";
    }

    // Check active plugins
    echo "<h3>Active Plugins:</h3>";
    $active_plugins = get_option('active_plugins');
    echo "<ul>";
    foreach ($active_plugins as $plugin) {
        $plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
        $exists = file_exists($plugin_file) ? '✓' : '✗';
        $color = file_exists($plugin_file) ? 'green' : 'red';
        echo "<li style='color:$color;'>$exists " . htmlspecialchars($plugin) . "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<p style='color:red;'>ERROR loading WordPress: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre style='background:#f8d7da; padding:15px;'>";
    echo htmlspecialchars($e->getTraceAsString());
    echo "</pre>";
}

echo "<hr>";
echo "<h2>Next Steps:</h2>";
echo "<ol>";
echo "<li>Visit homepage: <a href='/'>https://wp.premierplug.org/</a></li>";
echo "<li>Check for errors displayed on page</li>";
echo "<li>Or check debug.log: <a href='wp-content/debug.log'>View Log</a></li>";
echo "<li><strong style='color:red;'>DELETE THIS FILE: enable-debug.php</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><a href='diagnostic-check.php'>Run Diagnostic Again</a> | <a href='/'>Back to Site</a></p>";
?>
