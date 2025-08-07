<?php
/**
 * 3D Model Upload MIME Type Test
 * 
 * Place this file in your WordPress root directory and visit it in your browser
 * to verify that 3D model MIME types are properly registered.
 * 
 * URL: http://yoursite.com/test-3d-mime-types.php
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to view this test.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>3D Model MIME Type Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .status-enabled { background-color: #d4edda; }
        .status-disabled { background-color: #f8d7da; }
    </style>
</head>
<body>
    <h1>3D Model Upload MIME Type Test</h1>
    
    <?php
    // Get current WordPress allowed MIME types
    $allowed_mimes = get_allowed_mime_types();
    
    // Define 3D model extensions we're looking for
    $expected_3d_types = array(
        'glb' => 'model/gltf-binary',
        'gltf' => 'model/gltf+json',
        'obj' => 'application/octet-stream',
        'fbx' => 'application/x-tgif',
        'dae' => 'model/vnd.collada+xml',
        'usdz' => 'model/vnd.usdz+zip',
        'usd' => 'model/vnd.pixar.usd'
    );
    
    // Check plugin settings
    $plugin_options = get_option('wp_3d_model_viewer_options');
    $plugin_mime_types = isset($plugin_options['allowed_mime_types']) ? $plugin_options['allowed_mime_types'] : array();
    
    echo "<h2>Plugin Settings Status</h2>";
    if (empty($plugin_mime_types)) {
        echo "<p class='info'>No plugin settings found. Plugin should default to GLB and GLTF support.</p>";
    } else {
        echo "<p class='success'>Plugin has " . count($plugin_mime_types) . " MIME types configured.</p>";
        echo "<ul>";
        foreach ($plugin_mime_types as $mime_type) {
            echo "<li>" . esc_html($mime_type) . "</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2>WordPress MIME Type Registration Test</h2>";
    echo "<table>";
    echo "<tr><th>Extension</th><th>Expected MIME Type</th><th>Status</th><th>Actual MIME Type</th></tr>";
    
    $all_good = true;
    foreach ($expected_3d_types as $ext => $expected_mime) {
        $is_registered = isset($allowed_mimes[$ext]);
        $actual_mime = $is_registered ? $allowed_mimes[$ext] : 'Not registered';
        $status_class = $is_registered ? 'status-enabled' : 'status-disabled';
        $status_text = $is_registered ? '✓ Enabled' : '✗ Disabled';
        
        if (!$is_registered && in_array($expected_mime, array('model/gltf-binary', 'model/gltf+json'))) {
            $all_good = false;
        }
        
        echo "<tr class='{$status_class}'>";
        echo "<td>.{$ext}</td>";
        echo "<td>{$expected_mime}</td>";
        echo "<td>{$status_text}</td>";
        echo "<td>{$actual_mime}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    if ($all_good) {
        echo "<h2 class='success'>✓ Test Result: SUCCESS</h2>";
        echo "<p>At minimum, GLB and GLTF files should now be uploadable through the WordPress media library.</p>";
    } else {
        echo "<h2 class='error'>✗ Test Result: ISSUES DETECTED</h2>";
        echo "<p>GLB and/or GLTF MIME types are not properly registered. The plugin may not be active or configured correctly.</p>";
    }
    
    echo "<h2>Upload Test Instructions</h2>";
    echo "<ol>";
    echo "<li>Go to your WordPress admin: <a href='" . admin_url('upload.php') . "'>Media Library</a></li>";
    echo "<li>Click 'Add New'</li>";
    echo "<li>Try uploading a .glb or .gltf file</li>";
    echo "<li>If successful, the file should appear in your media library</li>";
    echo "</ol>";
    
    echo "<h2>Troubleshooting</h2>";
    echo "<ul>";
    echo "<li>Ensure the WP 3D Model Viewer plugin is activated</li>";
    echo "<li>Check plugin settings at: <a href='" . admin_url('options-general.php?page=wp-3d-model-viewer') . "'>Settings → 3D Viewer</a></li>";
    echo "<li>Verify GLB and GLTF options are checked in 'File Upload Settings'</li>";
    echo "<li>Check server upload limits (file size, execution time)</li>";
    echo "<li>Review WordPress debug logs for errors</li>";
    echo "</ul>";
    
    echo "<hr>";
    echo "<p><small>Test performed on: " . date('Y-m-d H:i:s') . " | WordPress " . get_bloginfo('version') . "</small></p>";
    ?>
    
    <script>
    // Auto-refresh every 10 seconds if there are issues
    <?php if (!$all_good): ?>
    setTimeout(function() {
        if (confirm('Issues detected. Refresh to test again?')) {
            window.location.reload();
        }
    }, 10000);
    <?php endif; ?>
    </script>
</body>
</html>
