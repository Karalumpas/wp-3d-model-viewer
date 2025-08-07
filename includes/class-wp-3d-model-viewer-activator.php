<?php

/**
 * Fired during plugin activation
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		// Check file integrity first
		if ( ! self::check_file_integrity() ) {
			wp_die( 
				__( 'Plugin activation failed: Some required files are missing. Please reinstall the plugin.', 'wp-3d-model-viewer' ),
				__( 'Plugin Activation Error', 'wp-3d-model-viewer' ),
				array( 'back_link' => true )
			);
		}

		// Create database tables if needed
		self::create_tables();

		// Set default options
		self::set_default_options();

		// Flush rewrite rules
		flush_rewrite_rules();

	}

	/**
	 * Create database tables for the plugin.
	 *
	 * @since    1.0.0
	 */
	private static function create_tables() {
		global $wpdb;

		$table_name = $wpdb->prefix . 'wp3d_models';

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
			model_url varchar(255) NOT NULL,
			poster_url varchar(255) DEFAULT '',
			alt_text text DEFAULT '',
			width varchar(20) DEFAULT '100%',
			height varchar(20) DEFAULT '400px',
			auto_rotate tinyint(1) DEFAULT 0,
			camera_controls tinyint(1) DEFAULT 1,
			ar_enabled tinyint(1) DEFAULT 0,
			ar_modes varchar(100) DEFAULT 'webxr scene-viewer quick-look',
			ios_src varchar(255) DEFAULT '',
			background_color varchar(20) DEFAULT '#ffffff',
			created_at datetime DEFAULT CURRENT_TIMESTAMP,
			updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	/**
	 * Set default plugin options.
	 *
	 * @since    1.0.0
	 */
	private static function set_default_options() {
		$default_options = array(
			'default_width' => '100%',
			'default_height' => '400px',
			'default_background_color' => '#ffffff',
			'enable_ar_by_default' => false,
			'enable_auto_rotate_by_default' => false,
			'enable_camera_controls_by_default' => true,
			'max_file_size' => 10485760, // 10MB
			'allowed_file_types' => array( 'gltf', 'glb', 'obj', 'fbx', 'dae', 'usdz' ),
			'lazy_loading' => true,
			'compression_enabled' => true,
		);

		add_option( 'wp_3d_model_viewer_settings', $default_options );
	}

	/**
	 * Check if all required plugin files exist.
	 *
	 * @since    1.0.0
	 * @return   bool True if all files exist, false otherwise
	 */
	private static function check_file_integrity() {
		$plugin_dir = plugin_dir_path( dirname( __FILE__ ) );
		
		$required_files = array(
			'wp-3d-model-viewer.php',
			'includes/class-wp-3d-model-viewer.php',
			'includes/class-wp-3d-model-viewer-loader.php',
			'includes/class-wp-3d-model-viewer-i18n.php',
			'includes/class-wp-3d-model-viewer-cpt.php',
			'admin/class-wp-3d-model-viewer-admin.php',
			'public/class-wp-3d-model-viewer-public.php'
		);

		foreach ( $required_files as $file ) {
			if ( ! file_exists( $plugin_dir . $file ) ) {
				return false;
			}
		}

		return true;
	}

}
