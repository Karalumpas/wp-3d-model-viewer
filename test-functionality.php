<?php
/**
 * Simple test for WP 3D Model Viewer functionality
 * This script tests basic plugin functionality without WordPress
 */

// Test 1: Check if main plugin file exists and has correct structure
echo "=== WP 3D Model Viewer - Basic Tests ===\n\n";

echo "Test 1: Plugin file structure\n";
$main_file = __DIR__ . '/wp-3d-model-viewer.php';
if (file_exists($main_file)) {
    echo "✓ Main plugin file exists\n";
    
    $content = file_get_contents($main_file);
    if (strpos($content, 'Plugin Name:') !== false) {
        echo "✓ Plugin header found\n";
    } else {
        echo "✗ Plugin header missing\n";
    }
} else {
    echo "✗ Main plugin file missing\n";
}

// Test 2: Check if required classes exist
echo "\nTest 2: Required classes\n";
$required_files = [
    'includes/class-wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer-cpt.php',
    'admin/class-wp-3d-model-viewer-admin.php',
    'public/class-wp-3d-model-viewer-public.php'
];

foreach ($required_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} missing\n";
    }
}

// Test 3: Check if admin assets are built
echo "\nTest 3: Built assets\n";
$asset_files = [
    'admin/js/wp-3d-model-viewer-admin.js',
    'admin/css/wp-3d-model-viewer-admin.css',
    'public/js/wp-3d-model-viewer-public.js',
    'public/css/wp-3d-model-viewer-public.css'
];

foreach ($asset_files as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} missing\n";
    }
}

// Test 4: Check for syntax errors in main files
echo "\nTest 4: PHP syntax validation\n";
$php_files = [
    'wp-3d-model-viewer.php',
    'includes/class-wp-3d-model-viewer-cpt.php',
    'admin/class-wp-3d-model-viewer-admin.php',
    'public/class-wp-3d-model-viewer-public.php'
];

foreach ($php_files as $file) {
    $output = [];
    $return_code = 0;
    exec("php -l " . escapeshellarg(__DIR__ . '/' . $file) . " 2>&1", $output, $return_code);
    
    if ($return_code === 0) {
        echo "✓ {$file} syntax OK\n";
    } else {
        echo "✗ {$file} syntax error: " . implode(' ', $output) . "\n";
    }
}

// Test 5: Check camera position functionality simulation
echo "\nTest 5: Camera position data structure\n";

// Simulate post meta data for camera position
$test_camera_data = [
    '_wp3d_camera_orbit' => '1.570rad 1.396rad 2.5m',
    '_wp3d_camera_target' => '0.0m 0.5m 0.0m',
    '_wp3d_zoom_level' => '75',
    '_wp3d_bg_color' => '#ffffff'
];

foreach ($test_camera_data as $key => $value) {
    if (is_string($value) && !empty($value)) {
        echo "✓ {$key}: {$value}\n";
    } else {
        echo "✗ {$key}: invalid data\n";
    }
}

echo "\n=== Test Summary ===\n";
echo "Basic plugin structure tests completed.\n";
echo "For full functionality testing, install in a WordPress environment.\n";
echo "\nNew features added:\n";
echo "- Interactive 3D model preview in admin\n";
echo "- Camera position capture functionality\n";
echo "- Enhanced UX with visual controls\n";
echo "- Responsive design improvements\n";
?>