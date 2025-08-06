<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Remove all plugin data when uninstalling
 */
function wp_3d_model_viewer_uninstall() {
	
	// Remove plugin options
	delete_option( 'wp_3d_model_viewer_settings' );
	
	// Remove any transients
	delete_transient( 'wp_3d_model_viewer_cache' );
	
	// Drop custom database tables
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'wp3d_models';
	$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
	
	// Clear any cached data
	wp_cache_flush();
	
	// Remove any scheduled events
	wp_clear_scheduled_hook( 'wp_3d_model_viewer_cleanup' );
	
	// For multisite installations
	if ( is_multisite() ) {
		
		// Get all blog ids
		$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
		
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			
			// Remove options for each site
			delete_option( 'wp_3d_model_viewer_settings' );
			delete_transient( 'wp_3d_model_viewer_cache' );
			
			// Drop tables for each site
			$table_name = $wpdb->prefix . 'wp3d_models';
			$wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
			
			restore_current_blog();
		}
	}
	
	// Remove any user meta data related to the plugin
	$wpdb->query( "DELETE FROM {$wpdb->usermeta} WHERE meta_key LIKE 'wp_3d_model_viewer_%'" );
	
	// Remove any post meta data related to the plugin
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'wp_3d_model_viewer_%'" );
	
	// Clear any remaining cache
	if ( function_exists( 'wp_cache_flush' ) ) {
		wp_cache_flush();
	}
}

// Run the uninstall function
wp_3d_model_viewer_uninstall();
