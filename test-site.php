<?php
/**
 * Simple Site Tester
 * Upload to WordPress root and visit to see actual errors
 * https://wp.premierplug.org/test-site.php
 */

// Show ALL errors
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PremierPlug Site Test</title>
    <style>
        body { font-family: system-ui; margin: 40px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .pass { color: #155724; background: #d4edda; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .fail { color: #721c24; background: #f8d7da; padding: 10px; border-radius: 4px; margin: 10px 0; }
        .warn { color: #856404; background: #fff3cd; padding: 10px; border-radius: 4px; margin: 10px 0; }
        pre { background: #f4f4f4; padding: 15px; overflow-x: auto; border-left: 4px solid #BC1F2F; }
        h1 { color: #BC1F2F; }
        .code { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 4px; font-family: monospace; }
    </style>
</head>
<body>

<h1>🔍 PremierPlug Site Loader Test</h1>

<?php
echo "<div class='section'>";
echo "<h2>Step 1: Loading WordPress Core</h2>";

try {
    if (!file_exists('wp-load.php')) {
        throw new Exception('wp-load.php not found!');
    }

    define('WP_USE_THEMES', false);
    require_once('wp-load.php');

    echo "<div class='pass'>✓ WordPress loaded successfully</div>";
    echo "<p>WordPress Version: " . get_bloginfo('version') . "</p>";
    echo "<p>Site URL: " . home_url() . "</p>";

} catch (Throwable $e) {
    echo "<div class='fail'>✗ FAILED to load WordPress</div>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    die();
}
echo "</div>";

// Test 2: Check Theme
echo "<div class='section'>";
echo "<h2>Step 2: Checking Active Theme</h2>";

$theme = wp_get_theme();
echo "<p><strong>Active Theme:</strong> " . $theme->get('Name') . "</p>";
echo "<p><strong>Theme Directory:</strong> " . get_template_directory() . "</p>";

$theme_files = array(
    'style.css',
    'functions.php',
    'index.php',
    'header.php',
    'footer.php',
    'front-page.php',
);

foreach ($theme_files as $file) {
    $path = get_template_directory() . '/' . $file;
    if (file_exists($path)) {
        echo "<div class='pass'>✓ $file exists</div>";
    } else {
        echo "<div class='fail'>✗ $file MISSING</div>";
    }
}
echo "</div>";

// Test 3: Try to Load Theme Header
echo "<div class='section'>";
echo "<h2>Step 3: Testing Theme Header Load</h2>";

try {
    ob_start();
    get_header();
    $header_output = ob_get_clean();

    if (empty($header_output)) {
        echo "<div class='warn'>⚠ Header loaded but output is empty</div>";
    } else {
        echo "<div class='pass'>✓ Header loaded successfully (" . strlen($header_output) . " bytes)</div>";
        echo "<h4>Header Output Preview:</h4>";
        echo "<div class='code'>" . htmlspecialchars(substr($header_output, 0, 500)) . "...</div>";
    }

} catch (Throwable $e) {
    echo "<div class='fail'>✗ FAILED to load header.php</div>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// Test 4: Check Active Plugins
echo "<div class='section'>";
echo "<h2>Step 4: Checking Active Plugins</h2>";

$active_plugins = get_option('active_plugins');
if (empty($active_plugins)) {
    echo "<div class='warn'>⚠ No active plugins</div>";
} else {
    echo "<p>Found " . count($active_plugins) . " active plugin(s):</p>";
    foreach ($active_plugins as $plugin) {
        $plugin_file = WP_PLUGIN_DIR . '/' . $plugin;
        if (file_exists($plugin_file)) {
            echo "<div class='pass'>✓ $plugin</div>";

            // Try to check for syntax errors
            $output = shell_exec("php -l " . escapeshellarg($plugin_file) . " 2>&1");
            if (strpos($output, 'No syntax errors') === false) {
                echo "<div class='fail'>Syntax error in plugin!</div>";
                echo "<pre>" . htmlspecialchars($output) . "</pre>";
            }
        } else {
            echo "<div class='fail'>✗ $plugin (FILE MISSING!)</div>";
        }
    }
}
echo "</div>";

// Test 5: Try to Load Full Page
echo "<div class='section'>";
echo "<h2>Step 5: Testing Full Page Load</h2>";

echo "<p>Attempting to load homepage with theme...</p>";

try {
    // Clear any previous output
    while (ob_get_level()) {
        ob_end_clean();
    }

    // Start fresh buffer
    ob_start();

    // Load with theme
    define('WP_USE_THEMES', true);

    // Include index.php
    $index_file = get_template_directory() . '/index.php';

    if (file_exists($index_file)) {
        include($index_file);
        $page_output = ob_get_clean();

        if (!empty($page_output)) {
            echo "<div class='pass'>✓ Page generated successfully (" . strlen($page_output) . " bytes)</div>";

            // Check for common issues
            if (strpos($page_output, '<html') !== false) {
                echo "<div class='pass'>✓ HTML structure present</div>";
            }
            if (strpos($page_output, '</body>') !== false) {
                echo "<div class='pass'>✓ Complete HTML document</div>";
            }
            if (strpos($page_output, 'Fatal error') !== false) {
                echo "<div class='fail'>✗ PHP Fatal Error in output!</div>";
            }

            // Show first part of output
            echo "<h4>Page Output Preview (first 1000 chars):</h4>";
            echo "<div class='code'>" . htmlspecialchars(substr($page_output, 0, 1000)) . "...</div>";

            // Save to file for inspection
            file_put_contents('test-output.html', $page_output);
            echo "<p><a href='test-output.html' target='_blank'>View Full Generated HTML</a></p>";

        } else {
            echo "<div class='fail'>✗ Page loaded but output is EMPTY!</div>";
            echo "<p>This usually means:</p>";
            echo "<ul>";
            echo "<li>Header/footer not being called in index.php</li>";
            echo "<li>Output buffering issue</li>";
            echo "<li>wp_head() or wp_footer() missing</li>";
            echo "</ul>";
        }
    } else {
        echo "<div class='fail'>✗ index.php not found at: $index_file</div>";
    }

} catch (Throwable $e) {
    ob_end_clean();
    echo "<div class='fail'>✗ EXCEPTION while loading page</div>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "\n\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
echo "</div>";

// Test 6: Check for PHP Errors in Log
echo "<div class='section'>";
echo "<h2>Step 6: Recent PHP Errors</h2>";

$error_log = 'wp-content/debug.log';
if (file_exists($error_log)) {
    $log_content = file_get_contents($error_log);
    $lines = explode("\n", $log_content);
    $recent = array_slice($lines, -30);

    if (!empty(trim(implode('', $recent)))) {
        echo "<div class='fail'>Found errors in debug.log:</div>";
        echo "<pre>" . htmlspecialchars(implode("\n", $recent)) . "</pre>";
    } else {
        echo "<div class='pass'>✓ No errors in debug.log</div>";
    }
} else {
    echo "<div class='warn'>⚠ No debug.log file (create wp-config.php with WP_DEBUG enabled)</div>";
}
echo "</div>";

?>

<div class="section">
    <h2>Action Items</h2>
    <ol>
        <li><a href="/">Visit Homepage</a> - Try to load actual site</li>
        <li><a href="test-output.html">View Generated HTML</a> - See what was actually generated</li>
        <li><a href="enable-debug.php">Enable Debug Mode</a> - Turn on error reporting</li>
        <li><strong style="color:red;">DELETE these test files after fixing!</strong></li>
    </ol>
</div>

<div class="section" style="background: #fff3cd;">
    <h2>⚠️ Delete Test Files</h2>
    <p>Run these commands after fixing the issue:</p>
    <pre>rm test-site.php
rm test-output.html
rm enable-debug.php
rm diagnostic-check.php</pre>
</div>

</body>
</html>
