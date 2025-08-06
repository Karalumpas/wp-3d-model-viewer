<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		// Clean up any scheduled events
		wp_clear_scheduled_hook( 'wp_3d_model_viewer_cleanup' );

		// Flush rewrite rules
		flush_rewrite_rules();

		// Optional: Remove options (uncomment if you want to clean up on deactivation)
		// delete_option( 'wp_3d_model_viewer_settings' );

		// Optional: Remove database tables (uncomment if you want to clean up on deactivation)
		// self::drop_tables();

	}

	/**
	 * Drop database tables for the plugin.
	 *
	 * @since    1.0.0
	 */
	private static function drop_tables() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wp3d_models';
		$wpdb->query( $wpdb->prepare( "DROP TABLE IF EXISTS %s", $table_name ) );
	}

}
