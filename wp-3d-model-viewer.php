<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Karalumpas/wp-3d-model-viewer
 * @since             1.0.0
 * @package           WP_3D_Model_Viewer
 *
 * @wordpress-plugin
 * Plugin Name:       WP 3D Model Viewer
 * Plugin URI:        https://github.com/Karalumpas/wp-3d-model-viewer
 * Description:       A powerful WordPress plugin for displaying interactive 3D models in the browser with WebGL technology, supporting GLTF, GLB, OBJ, and other popular 3D formats.
 * Version:           1.0.0
 * Author:            Karalumpas
 * Author URI:        https://github.com/Karalumpas
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       wp-3d-model-viewer
 * Domain Path:       /languages
 * Requires at least: 5.0
 * Tested up to:      6.4
 * Requires PHP:      7.4
 * Network:           false
 * Update URI:        https://github.com/Karalumpas/wp-3d-model-viewer
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WP_3D_MODEL_VIEWER_VERSION', '1.0.0' );

/**
 * Plugin constants
 */
define( 'WP_3D_MODEL_VIEWER_PLUGIN_FILE', __FILE__ );
define( 'WP_3D_MODEL_VIEWER_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WP_3D_MODEL_VIEWER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WP_3D_MODEL_VIEWER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WP_3D_MODEL_VIEWER_PLUGIN_NAME', 'WP 3D Model Viewer' );
define( 'WP_3D_MODEL_VIEWER_TEXT_DOMAIN', 'wp-3d-model-viewer' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-3d-model-viewer-activator.php
 */
function activate_wp_3d_model_viewer() {
	$activator_file = plugin_dir_path( __FILE__ ) . 'includes/class-wp-3d-model-viewer-activator.php';
	if ( file_exists( $activator_file ) ) {
		require_once $activator_file;
		WP_3D_Model_Viewer_Activator::activate();
	}
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-3d-model-viewer-deactivator.php
 */
function deactivate_wp_3d_model_viewer() {
	$deactivator_file = plugin_dir_path( __FILE__ ) . 'includes/class-wp-3d-model-viewer-deactivator.php';
	if ( file_exists( $deactivator_file ) ) {
		require_once $deactivator_file;
		WP_3D_Model_Viewer_Deactivator::deactivate();
	}
}

register_activation_hook( __FILE__, 'activate_wp_3d_model_viewer' );
register_deactivation_hook( __FILE__, 'deactivate_wp_3d_model_viewer' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
$core_file = plugin_dir_path( __FILE__ ) . 'includes/class-wp-3d-model-viewer.php';
if ( ! file_exists( $core_file ) ) {
	add_action( 'admin_notices', function() {
		echo '<div class="notice notice-error"><p>';
		echo esc_html__( 'WP 3D Model Viewer Error: Core plugin file is missing. Please reinstall the plugin.', 'wp-3d-model-viewer' );
		echo '</p></div>';
	});
	return;
}
require $core_file;

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_3d_model_viewer() {
	if ( class_exists( 'WP_3D_Model_Viewer' ) ) {
		$plugin = new WP_3D_Model_Viewer();
		$plugin->run();
	}
}
run_wp_3d_model_viewer();
