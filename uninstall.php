<?php
/**
 * Remove plugin data on uninstall.
 *
 * @package WP_3D_Model_Viewer
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove all plugin data when uninstalling.
 *
 * @return void
 */
function wp_3d_model_viewer_uninstall(): void {
	// Remove plugin options.
	delete_option( 'wp_3d_model_viewer_settings' );

	// Remove any transients.
	delete_transient( 'wp_3d_model_viewer_cache' );

	// Drop custom database table.
	global $wpdb;
	$table_name = $wpdb->prefix . 'wp3d_models';
	$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared

	// Remove any scheduled events.
	wp_clear_scheduled_hook( 'wp_3d_model_viewer_cleanup' );

	// For multisite installations.
	if ( is_multisite() ) {
		// Get all blog IDs.
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );

			// Remove options and transients for each site.
			delete_option( 'wp_3d_model_viewer_settings' );
			delete_transient( 'wp_3d_model_viewer_cache' );

			// Drop tables for each site.
			$table_name = $wpdb->prefix . 'wp3d_models';
			$wpdb->query( $wpdb->prepare( 'DROP TABLE IF EXISTS %i', $table_name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared

			restore_current_blog();
		}
	}

	// Remove any user meta data related to the plugin.
	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'wp_3d_model_viewer_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared

	// Remove any post meta data related to the plugin.
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'wp_3d_model_viewer_%'" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared

	// Clear any remaining cache.
	wp_cache_flush();
}

wp_3d_model_viewer_uninstall();
