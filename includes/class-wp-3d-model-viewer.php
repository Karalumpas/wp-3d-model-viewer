<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_3D_Model_Viewer_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WP_3D_MODEL_VIEWER_VERSION' ) ) {
			$this->version = WP_3D_MODEL_VIEWER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-3d-model-viewer';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_cpt_hooks();
		$this->define_public_hooks();
		$this->enable_3d_model_uploads();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_3D_Model_Viewer_Loader. Orchestrates the hooks of the plugin.
	 * - WP_3D_Model_Viewer_i18n. Defines internationalization functionality.
	 * - WP_3D_Model_Viewer_Admin. Defines all hooks for the admin area.
	 * - WP_3D_Model_Viewer_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wp-3d-model-viewer-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wp-3d-model-viewer-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-wp-3d-model-viewer-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'public/class-wp-3d-model-viewer-public.php';

		/**
		 * The class responsible for defining the custom post type functionality.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-wp-3d-model-viewer-cpt.php';

		$this->loader = new WP_3D_Model_Viewer_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_3D_Model_Viewer_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_3D_Model_Viewer_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_3D_Model_Viewer_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );

								// Add settings page.
				$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_options_page' );

				// Add Settings link to the plugin.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . 'wp-3d-model-viewer.php' );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

				// Handle settings save.
		$this->loader->add_action( 'admin_post_wp_3d_model_viewer_save_settings', $plugin_admin, 'save_settings' );
	}

	/**
	 * Register all of the hooks related to the custom post type functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_cpt_hooks() {

		$plugin_cpt = new WP_3D_Model_Viewer_CPT();

				// Register the custom post type.
		$this->loader->add_action( 'init', $plugin_cpt, 'register_post_type' );

				// Add admin columns.
		$this->loader->add_filter( 'manage_wp_3d_model_posts_columns', $plugin_cpt, 'add_admin_columns' );
		$this->loader->add_action( 'manage_wp_3d_model_posts_custom_column', $plugin_cpt, 'populate_admin_columns', 10, 2 );
		$this->loader->add_filter( 'manage_edit-wp_3d_model_sortable_columns', $plugin_cpt, 'make_columns_sortable' );

				// Add metaboxes.
		$this->loader->add_action( 'add_meta_boxes', $plugin_cpt, 'add_metaboxes' );
		$this->loader->add_action( 'save_post', $plugin_cpt, 'save_metabox_data' );

				// Enqueue admin scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_cpt, 'enqueue_admin_scripts' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_3D_Model_Viewer_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

				// Add shortcode.
		$this->loader->add_action( 'init', $plugin_public, 'register_shortcode' );

				// Add Gutenberg block.
		$this->loader->add_action( 'init', $plugin_public, 'register_block' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_3D_Model_Viewer_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Enable 3D model file uploads by adding MIME types.
	 *
	 * @since     1.0.0
	 */
	private function enable_3d_model_uploads() {
		$this->loader->add_filter( 'upload_mimes', $this, 'add_3d_model_mime_types' );
	}

	/**
	 * Add 3D model MIME types to allowed uploads.
	 *
	 * @since    1.0.0
	 * @param    array $mimes    Existing allowed MIME types.
	 * @return   array           Modified MIME types.
	 */
	public function add_3d_model_mime_types( $mimes ) {
		
		// Get plugin settings to determine which MIME types to enable
		$options = get_option( 'wp_3d_model_viewer_options' );
		$allowed_mime_types = isset( $options['allowed_mime_types'] ) ? $options['allowed_mime_types'] : array();

		// If no settings exist yet, enable GLB and GLTF by default
		if ( empty( $allowed_mime_types ) ) {
			$allowed_mime_types = array( 'model/gltf-binary', 'model/gltf+json' );
		}

		// Map MIME types to file extensions
		$mime_map = array(
			'model/gltf+json'          => array( 'gltf' ),
			'model/gltf-binary'        => array( 'glb' ),
			'application/octet-stream' => array( 'obj' ),
			'application/x-tgif'       => array( 'fbx' ),
			'model/vnd.collada+xml'    => array( 'dae' ),
			'model/vnd.usdz+zip'       => array( 'usdz' ),
			'model/vnd.pixar.usd'      => array( 'usd' ),
		);

		// Add enabled MIME types to WordPress upload allowlist
		foreach ( $allowed_mime_types as $mime_type ) {
			if ( isset( $mime_map[ $mime_type ] ) ) {
				foreach ( $mime_map[ $mime_type ] as $extension ) {
					$mimes[ $extension ] = $mime_type;
				}
			}
		}

		return $mimes;
	}
}
