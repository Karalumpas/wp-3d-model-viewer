<?php
/**
 * Debug helper for WP 3D Model Viewer plugin
 * 
 * This file can be used to debug file existence and path issues
 * when the plugin fails to load properly.
 */

// Check if WordPress is loaded
if ( ! defined( 'WPINC' ) ) {
    die( 'This file requires WordPress to be loaded.' );
}

echo '<h2>WP 3D Model Viewer - Debug Information</h2>';

$plugin_dir = plugin_dir_path( __FILE__ );
echo '<p><strong>Plugin Directory:</strong> ' . esc_html( $plugin_dir ) . '</p>';

$files_to_check = [
    'wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer-activator.php',
    'includes/class-wp-3d-model-viewer-deactivator.php',
    'includes/class-wp-3d-model-viewer-loader.php',
    'includes/class-wp-3d-model-viewer-i18n.php',
    'includes/class-wp-3d-model-viewer-cpt.php',
];

echo '<h3>File Existence Check:</h3>';
echo '<ul>';
foreach ( $files_to_check as $file ) {
    $full_path = $plugin_dir . $file;
    $exists = file_exists( $full_path );
    $readable = is_readable( $full_path );
    
    echo '<li>';
    echo '<strong>' . esc_html( $file ) . ':</strong> ';
    if ( $exists ) {
        echo '<span style="color: green;">EXISTS</span>';
        if ( $readable ) {
            echo ' <span style="color: green;">(READABLE)</span>';
        } else {
            echo ' <span style="color: red;">(NOT READABLE)</span>';
        }
        echo ' - Size: ' . filesize( $full_path ) . ' bytes';
    } else {
        echo '<span style="color: red;">MISSING</span>';
    }
    echo '<br>';
    echo '<small>Full path: ' . esc_html( $full_path ) . '</small>';
    echo '</li>';
}
echo '</ul>';

// Check WordPress constants
echo '<h3>WordPress Constants:</h3>';
echo '<ul>';
$constants = [
    'WPINC',
    'WP_PLUGIN_DIR',
    'WP_CONTENT_DIR',
    'ABSPATH'
];

foreach ( $constants as $constant ) {
    echo '<li><strong>' . $constant . ':</strong> ';
    if ( defined( $constant ) ) {
        echo esc_html( constant( $constant ) );
    } else {
        echo '<span style="color: red;">NOT DEFINED</span>';
    }
    echo '</li>';
}
echo '</ul>';

// Check PHP version and extensions
echo '<h3>PHP Environment:</h3>';
echo '<ul>';
echo '<li><strong>PHP Version:</strong> ' . PHP_VERSION . '</li>';
echo '<li><strong>PHP SAPI:</strong> ' . PHP_SAPI . '</li>';
echo '<li><strong>Current Working Directory:</strong> ' . getcwd() . '</li>';
echo '<li><strong>Include Path:</strong> ' . get_include_path() . '</li>';
echo '</ul>';
?>
