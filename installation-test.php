<?php
/**
 * WP 3D Model Viewer - Installation Test
 * 
 * This file helps diagnose installation issues.
 * Upload this file to your plugin directory and access it via browser.
 */

// Basic file structure check
$plugin_dir = __DIR__;
$required_files = array(
    'wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer-activator.php',
    'includes/class-wp-3d-model-viewer-deactivator.php',
    'includes/class-wp-3d-model-viewer-loader.php',
    'includes/class-wp-3d-model-viewer-i18n.php',
    'includes/class-wp-3d-model-viewer-cpt.php',
    'admin/class-wp-3d-model-viewer-admin.php',
    'public/class-wp-3d-model-viewer-public.php'
);

?>
<!DOCTYPE html>
<html>
<head>
    <title>WP 3D Model Viewer - Installation Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        code { background-color: #f4f4f4; padding: 2px 4px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>WP 3D Model Viewer - Installation Test</h1>
    
    <h2>Plugin Directory Information</h2>
    <p><strong>Plugin Directory:</strong> <code><?php echo htmlspecialchars($plugin_dir); ?></code></p>
    <p><strong>Directory Name:</strong> <code><?php echo htmlspecialchars(basename($plugin_dir)); ?></code></p>
    
    <?php if (basename($plugin_dir) !== 'wp-3d-model-viewer'): ?>
        <div class="warning">
            <strong>⚠️ Warning:</strong> Plugin directory should be named 'wp-3d-model-viewer' but is named '<?php echo htmlspecialchars(basename($plugin_dir)); ?>'. This may cause activation issues.
        </div>
    <?php endif; ?>
    
    <h2>Required Files Check</h2>
    <table>
        <tr>
            <th>File</th>
            <th>Status</th>
            <th>Size</th>
            <th>Readable</th>
        </tr>
        <?php foreach ($required_files as $file): ?>
            <?php 
            $file_path = $plugin_dir . '/' . $file;
            $exists = file_exists($file_path);
            $readable = $exists ? is_readable($file_path) : false;
            $size = $exists ? filesize($file_path) : 0;
            ?>
            <tr>
                <td><code><?php echo htmlspecialchars($file); ?></code></td>
                <td class="<?php echo $exists ? 'success' : 'error'; ?>">
                    <?php echo $exists ? '✅ EXISTS' : '❌ MISSING'; ?>
                </td>
                <td><?php echo $exists ? number_format($size) . ' bytes' : '-'; ?></td>
                <td class="<?php echo $readable ? 'success' : 'error'; ?>">
                    <?php echo $readable ? '✅ YES' : '❌ NO'; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    
    <h2>PHP Environment</h2>
    <table>
        <tr><th>Setting</th><th>Value</th></tr>
        <tr><td>PHP Version</td><td><?php echo PHP_VERSION; ?></td></tr>
        <tr><td>PHP SAPI</td><td><?php echo PHP_SAPI; ?></td></tr>
        <tr><td>Current Working Directory</td><td><code><?php echo getcwd(); ?></code></td></tr>
        <tr><td>Include Path</td><td><code><?php echo get_include_path(); ?></code></td></tr>
    </table>
    
    <h2>WordPress Functions Test</h2>
    <?php if (function_exists('plugin_dir_path')): ?>
        <p class="success">✅ WordPress functions are available</p>
        <p><strong>plugin_dir_path result:</strong> <code><?php echo plugin_dir_path(__FILE__); ?></code></p>
    <?php else: ?>
        <p class="error">❌ WordPress functions not available - this test should be run from WordPress admin</p>
    <?php endif; ?>
    
    <h2>Recommendations</h2>
    <?php
    $missing_files = array_filter($required_files, function($file) use ($plugin_dir) {
        return !file_exists($plugin_dir . '/' . $file);
    });
    
    if (!empty($missing_files)):
    ?>
        <div class="error">
            <h3>Missing Files Detected</h3>
            <p>The following files are missing and need to be uploaded:</p>
            <ul>
                <?php foreach ($missing_files as $file): ?>
                    <li><code><?php echo htmlspecialchars($file); ?></code></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php else: ?>
        <div class="success">
            <h3>✅ All Required Files Present</h3>
            <p>All core plugin files are present and readable.</p>
        </div>
    <?php endif; ?>
    
    <h2>Installation Instructions</h2>
    <ol>
        <li>Ensure the plugin folder is named exactly <code>wp-3d-model-viewer</code></li>
        <li>Upload all plugin files to <code>/wp-content/plugins/wp-3d-model-viewer/</code></li>
        <li>Set correct file permissions:
            <ul>
                <li>Folders: 755 <code>chmod 755 wp-3d-model-viewer/</code></li>
                <li>Files: 644 <code>chmod 644 wp-3d-model-viewer/*.php</code></li>
            </ul>
        </li>
        <li>Delete this test file before activating the plugin</li>
        <li>Try activating the plugin through WordPress admin</li>
    </ol>
    
    <p><em>Delete this file (installation-test.php) before activating the plugin in WordPress.</em></p>
</body>
</html>
