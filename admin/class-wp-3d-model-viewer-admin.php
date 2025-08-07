<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/admin
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_3D_Model_Viewer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_3D_Model_Viewer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-3d-model-viewer-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_3D_Model_Viewer_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_3D_Model_Viewer_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wp-3d-model-viewer-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 */
		add_options_page( 
			__( 'WP 3D Model Viewer Settings', 'wp-3d-model-viewer' ), 
			__( '3D Viewer', 'wp-3d-model-viewer' ), 
			'manage_options', 
			'wp-3d-model-viewer', 
			array( $this, 'display_plugin_setup_page' )
		);
	}

	/**
	 * Initialize WordPress Settings API.
	 *
	 * @since    1.0.0
	 */
	public function admin_init() {
		
		// Register settings
		register_setting(
			'wp_3d_model_viewer_settings',
			'wp_3d_model_viewer_options',
			array( $this, 'validate_options' )
		);

		// Add settings sections
		add_settings_section(
			'wp_3d_model_viewer_general',
			__( 'General Settings', 'wp-3d-model-viewer' ),
			array( $this, 'general_section_callback' ),
			'wp-3d-model-viewer'
		);

		add_settings_section(
			'wp_3d_model_viewer_mime',
			__( 'File Upload Settings', 'wp-3d-model-viewer' ),
			array( $this, 'mime_section_callback' ),
			'wp-3d-model-viewer'
		);

		add_settings_section(
			'wp_3d_model_viewer_advanced',
			__( 'Advanced Settings', 'wp-3d-model-viewer' ),
			array( $this, 'advanced_section_callback' ),
			'wp-3d-model-viewer'
		);

		// Add settings fields
		$this->add_settings_fields();
	}

	/**
	 * Add settings fields to sections.
	 *
	 * @since    1.0.0
	 */
	private function add_settings_fields() {
		
		// General settings fields
		add_settings_field(
			'default_background_color',
			__( 'Default Background Color', 'wp-3d-model-viewer' ),
			array( $this, 'background_color_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_general'
		);

		add_settings_field(
			'default_zoom_level',
			__( 'Default Zoom Level', 'wp-3d-model-viewer' ),
			array( $this, 'zoom_level_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_general'
		);

		add_settings_field(
			'default_width',
			__( 'Default Width', 'wp-3d-model-viewer' ),
			array( $this, 'default_width_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_general'
		);

		add_settings_field(
			'default_height',
			__( 'Default Height', 'wp-3d-model-viewer' ),
			array( $this, 'default_height_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_general'
		);

		// MIME type settings fields
		add_settings_field(
			'allowed_mime_types',
			__( 'Allowed MIME Types', 'wp-3d-model-viewer' ),
			array( $this, 'mime_types_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_mime'
		);

		add_settings_field(
			'max_file_size',
			__( 'Maximum File Size (MB)', 'wp-3d-model-viewer' ),
			array( $this, 'max_file_size_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_mime'
		);

		// Advanced settings fields
		add_settings_field(
			'enable_debugging',
			__( 'Enable Debugging', 'wp-3d-model-viewer' ),
			array( $this, 'debugging_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_advanced'
		);

		add_settings_field(
			'lazy_loading',
			__( 'Enable Lazy Loading', 'wp-3d-model-viewer' ),
			array( $this, 'lazy_loading_field_callback' ),
			'wp-3d-model-viewer',
			'wp_3d_model_viewer_advanced'
		);

		add_settings_field(
			'enable_ar_by_default',
			__( 'Enable AR by Default', 'wp-3d-model-viewer' ),
			array( $this, 'ar_default_field_callback' ),
			'wp-3d-model-viewer',
		);
	}

	/**
	 * General settings section callback.
	 *
	 * @since    1.0.0
	 */
	public function general_section_callback() {
		echo '<p>' . __( 'Configure default settings for 3D model viewers.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * MIME types section callback.
	 *
	 * @since    1.0.0
	 */
	public function mime_section_callback() {
		echo '<p>' . __( 'Configure which file types are allowed for 3D model uploads.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Advanced settings section callback.
	 *
	 * @since    1.0.0
	 */
	public function advanced_section_callback() {
		echo '<p>' . __( 'Advanced configuration options and debugging features.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Background color field callback.
	 *
	 * @since    1.0.0
	 */
	public function background_color_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['default_background_color'] ) ? $options['default_background_color'] : '#ffffff';
		
		echo '<input type="color" id="default_background_color" name="wp_3d_model_viewer_options[default_background_color]" value="' . esc_attr( $value ) . '" />';
		echo '<p class="description">' . __( 'Default background color for 3D model viewers.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Zoom level field callback.
	 *
	 * @since    1.0.0
	 */
	public function zoom_level_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['default_zoom_level'] ) ? $options['default_zoom_level'] : '1.0';
		
		echo '<input type="range" id="default_zoom_level" name="wp_3d_model_viewer_options[default_zoom_level]" value="' . esc_attr( $value ) . '" min="0.1" max="5.0" step="0.1" />';
		echo '<span class="zoom-display">' . esc_html( $value ) . 'x</span>';
		echo '<p class="description">' . __( 'Default zoom level for 3D model viewers (0.1x to 5.0x).', 'wp-3d-model-viewer' ) . '</p>';
		
		echo '<script>
		document.getElementById("default_zoom_level").addEventListener("input", function() {
			document.querySelector(".zoom-display").textContent = this.value + "x";
		});
		</script>';
	}

	/**
	 * Default width field callback.
	 *
	 * @since    1.0.0
	 */
	public function default_width_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['default_width'] ) ? $options['default_width'] : '100%';
		
		echo '<input type="text" id="default_width" name="wp_3d_model_viewer_options[default_width]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . __( 'Default width for 3D model viewers (e.g., 100%, 800px, 50em).', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Default height field callback.
	 *
	 * @since    1.0.0
	 */
	public function default_height_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['default_height'] ) ? $options['default_height'] : '400px';
		
		echo '<input type="text" id="default_height" name="wp_3d_model_viewer_options[default_height]" value="' . esc_attr( $value ) . '" class="regular-text" />';
		echo '<p class="description">' . __( 'Default height for 3D model viewers (e.g., 400px, 50vh, 30em).', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * MIME types field callback.
	 *
	 * @since    1.0.0
	 */
	public function mime_types_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$allowed_types = isset( $options['allowed_mime_types'] ) ? $options['allowed_mime_types'] : array();
		
		$mime_types = array(
			'model/gltf+json' => array(
				'label' => 'GLTF (.gltf)',
				'extensions' => array( 'gltf' ),
				'description' => 'GL Transmission Format (JSON variant)'
			),
			'model/gltf-binary' => array(
				'label' => 'GLB (.glb)',
				'extensions' => array( 'glb' ),
				'description' => 'GL Transmission Format (Binary variant)'
			),
			'application/octet-stream' => array(
				'label' => 'OBJ (.obj)',
				'extensions' => array( 'obj' ),
				'description' => 'Wavefront OBJ (requires MTL file)'
			),
			'application/x-tgif' => array(
				'label' => 'FBX (.fbx)',
				'extensions' => array( 'fbx' ),
				'description' => 'Autodesk FBX format'
			),
			'model/vnd.collada+xml' => array(
				'label' => 'DAE (.dae)',
				'extensions' => array( 'dae' ),
				'description' => 'COLLADA Digital Asset Exchange'
			),
			'model/vnd.usdz+zip' => array(
				'label' => 'USDZ (.usdz)',
				'extensions' => array( 'usdz' ),
				'description' => 'USD (iOS AR format)'
			),
			'model/vnd.pixar.usd' => array(
				'label' => 'USD (.usd)',
				'extensions' => array( 'usd' ),
				'description' => 'Universal Scene Description'
			)
		);

		echo '<fieldset>';
		foreach ( $mime_types as $mime_type => $info ) {
			$checked = in_array( $mime_type, $allowed_types );
			echo '<label>';
			echo '<input type="checkbox" name="wp_3d_model_viewer_options[allowed_mime_types][]" value="' . esc_attr( $mime_type ) . '"' . checked( $checked, true, false ) . ' />';
			echo ' <strong>' . esc_html( $info['label'] ) . '</strong> - ' . esc_html( $info['description'] );
			echo '</label><br>';
		}
		echo '</fieldset>';
		echo '<p class="description">' . __( 'Select which 3D model file formats are allowed for upload. Note: Your server must also support these MIME types.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Max file size field callback.
	 *
	 * @since    1.0.0
	 */
	public function max_file_size_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['max_file_size'] ) ? $options['max_file_size'] : '10';
		
		echo '<input type="number" id="max_file_size" name="wp_3d_model_viewer_options[max_file_size]" value="' . esc_attr( $value ) . '" min="1" max="100" step="1" class="small-text" />';
		echo ' MB';
		echo '<p class="description">' . __( 'Maximum file size for 3D model uploads in megabytes (1-100 MB).', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Debugging field callback.
	 *
	 * @since    1.0.0
	 */
	public function debugging_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['enable_debugging'] ) ? $options['enable_debugging'] : '0';
		
		echo '<label>';
		echo '<input type="checkbox" id="enable_debugging" name="wp_3d_model_viewer_options[enable_debugging]" value="1"' . checked( $value, '1', false ) . ' />';
		echo ' ' . __( 'Enable debug mode', 'wp-3d-model-viewer' );
		echo '</label>';
		echo '<p class="description">' . __( 'Enable detailed logging and debugging information for troubleshooting. Only enable when needed as it may affect performance.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Lazy loading field callback.
	 *
	 * @since    1.0.0
	 */
	public function lazy_loading_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['lazy_loading'] ) ? $options['lazy_loading'] : '1';
		
		echo '<label>';
		echo '<input type="checkbox" id="lazy_loading" name="wp_3d_model_viewer_options[lazy_loading]" value="1"' . checked( $value, '1', false ) . ' />';
		echo ' ' . __( 'Enable lazy loading', 'wp-3d-model-viewer' );
		echo '</label>';
		echo '<p class="description">' . __( 'Load 3D models only when they come into view (recommended for performance).', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * AR default field callback.
	 *
	 * @since    1.0.0
	 */
	public function ar_default_field_callback() {
		$options = get_option( 'wp_3d_model_viewer_options' );
		$value = isset( $options['enable_ar_by_default'] ) ? $options['enable_ar_by_default'] : '0';
		
		echo '<label>';
		echo '<input type="checkbox" id="enable_ar_by_default" name="wp_3d_model_viewer_options[enable_ar_by_default]" value="1"' . checked( $value, '1', false ) . ' />';
		echo ' ' . __( 'Enable AR support by default', 'wp-3d-model-viewer' );
		echo '</label>';
		echo '<p class="description">' . __( 'Enable augmented reality viewing on compatible devices by default for new models.', 'wp-3d-model-viewer' ) . '</p>';
	}

	/**
	 * Validate and sanitize options.
	 *
	 * @since    1.0.0
	 * @param    array    $input    Raw input values.
	 * @return   array              Sanitized values.
	 */
	public function validate_options( $input ) {
		$output = array();

		// Sanitize background color
		if ( isset( $input['default_background_color'] ) ) {
			$output['default_background_color'] = sanitize_hex_color( $input['default_background_color'] );
		}

		// Sanitize zoom level
		if ( isset( $input['default_zoom_level'] ) ) {
			$zoom = floatval( $input['default_zoom_level'] );
			$output['default_zoom_level'] = max( 0.1, min( 5.0, $zoom ) );
		}

		// Sanitize dimensions
		if ( isset( $input['default_width'] ) ) {
			$output['default_width'] = sanitize_text_field( $input['default_width'] );
		}

		if ( isset( $input['default_height'] ) ) {
			$output['default_height'] = sanitize_text_field( $input['default_height'] );
		}

		// Sanitize MIME types
		if ( isset( $input['allowed_mime_types'] ) && is_array( $input['allowed_mime_types'] ) ) {
			$allowed_mimes = array(
				'model/gltf+json',
				'model/gltf-binary',
				'application/octet-stream',
				'application/x-tgif',
				'model/vnd.collada+xml',
				'model/vnd.usdz+zip',
				'model/vnd.pixar.usd'
			);
			$output['allowed_mime_types'] = array_intersect( $input['allowed_mime_types'], $allowed_mimes );
		} else {
			$output['allowed_mime_types'] = array();
		}

		// Sanitize file size
		if ( isset( $input['max_file_size'] ) ) {
			$size = intval( $input['max_file_size'] );
			$output['max_file_size'] = max( 1, min( 100, $size ) );
		}

		// Sanitize checkboxes
		$output['enable_debugging'] = isset( $input['enable_debugging'] ) ? '1' : '0';
		$output['lazy_loading'] = isset( $input['lazy_loading'] ) ? '1' : '0';
		$output['enable_ar_by_default'] = isset( $input['enable_ar_by_default'] ) ? '1' : '0';

		return $output;
	}

	/**
	 * Add settings page to admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_options_page() {
		add_options_page(
			__( '3D Model Viewer Settings', 'wp-3d-model-viewer' ),
			__( '3D Viewer', 'wp-3d-model-viewer' ),
			'manage_options',
			'wp-3d-model-viewer',
			array( $this, 'display_options_page' )
		);
	}

	/**
	 * Display the options page.
	 *
	 * @since    1.0.0
	 */
	public function display_options_page() {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			
			<?php
			// Display success message if settings saved
			if ( isset( $_GET['settings-updated'] ) ) {
				add_settings_error( 'wp_3d_model_viewer_messages', 'wp_3d_model_viewer_message', __( 'Settings saved.', 'wp-3d-model-viewer' ), 'updated' );
			}
			
			// Display any error messages
			settings_errors( 'wp_3d_model_viewer_messages' );
			?>
			
			<form action="options.php" method="post">
				<?php
				// Output security fields for the registered setting
				settings_fields( 'wp_3d_model_viewer_options_group' );
				
				// Output setting sections and their fields
				do_settings_sections( 'wp-3d-model-viewer' );
				
				// Output save settings button
				submit_button( __( 'Save Settings', 'wp-3d-model-viewer' ) );
				?>
			</form>
			
			<div class="wp-3d-model-viewer-help">
				<h2><?php _e( 'Usage Examples', 'wp-3d-model-viewer' ); ?></h2>
				<p><?php _e( 'Use these shortcode examples to display 3D models:', 'wp-3d-model-viewer' ); ?></p>
				
				<h3><?php _e( 'Display a specific 3D model by ID:', 'wp-3d-model-viewer' ); ?></h3>
				<code>[model_viewer id="123"]</code><br>
				<code>[3d_model id="123"]</code><br>
				<code>[3d_model_viewer id="123"]</code>
				
				<h3><?php _e( 'Display a model with custom settings:', 'wp-3d-model-viewer' ); ?></h3>
				<code>[model_viewer id="123" background-color="#ff0000" camera-orbit="45deg 75deg auto"]</code>
				
				<h3><?php _e( 'Display a model from external URL:', 'wp-3d-model-viewer' ); ?></h3>
				<code>[3d_model_viewer src="https://example.com/model.glb" width="800px" height="600px"]</code>
				
				<h3><?php _e( 'Enable AR viewing:', 'wp-3d-model-viewer' ); ?></h3>
				<code>[model_viewer id="123" ar ar-modes="webxr scene-viewer quick-look"]</code>
			</div>
		</div>
		<?php
	}

	/**
	 * Add action links to the plugin page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {
		/*
		*  Documentation : https://codex.wordpress.org/Plugin_API/Filter_Reference/plugin_action_links_(plugin_file_name)
		*/
		$settings_link = array(
			'<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
		);
		return array_merge(  $settings_link, $links );

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page() {
		include_once( 'partials/wp-3d-model-viewer-admin-display.php' );
	}

	/**
	 * Save plugin settings.
	 *
	 * @since    1.0.0
	 */
	public function save_settings() {
		
		// Verify nonce
		if ( ! wp_verify_nonce( $_POST['wp_3d_model_viewer_nonce'], 'wp_3d_model_viewer_save_settings' ) ) {
			wp_die( 'Security check failed' );
		}

		// Check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Insufficient permissions' );
		}

		$settings = array();
		
		// Sanitize and save settings
		$settings['default_width'] = sanitize_text_field( $_POST['default_width'] ?? '100%' );
		$settings['default_height'] = sanitize_text_field( $_POST['default_height'] ?? '400px' );
		$settings['default_background_color'] = sanitize_hex_color( $_POST['default_background_color'] ?? '#ffffff' );
		$settings['enable_ar_by_default'] = isset( $_POST['enable_ar_by_default'] ) ? 1 : 0;
		$settings['enable_auto_rotate_by_default'] = isset( $_POST['enable_auto_rotate_by_default'] ) ? 1 : 0;
		$settings['enable_camera_controls_by_default'] = isset( $_POST['enable_camera_controls_by_default'] ) ? 1 : 0;
		$settings['max_file_size'] = absint( $_POST['max_file_size'] ?? 10485760 );
		$settings['lazy_loading'] = isset( $_POST['lazy_loading'] ) ? 1 : 0;
		$settings['compression_enabled'] = isset( $_POST['compression_enabled'] ) ? 1 : 0;

		// Save allowed file types
		$allowed_types = array();
		if ( isset( $_POST['allowed_file_types'] ) && is_array( $_POST['allowed_file_types'] ) ) {
			foreach ( $_POST['allowed_file_types'] as $type ) {
				$allowed_types[] = sanitize_text_field( $type );
			}
		}
		$settings['allowed_file_types'] = $allowed_types;

		update_option( 'wp_3d_model_viewer_settings', $settings );

		// Redirect with success message
		wp_redirect( add_query_arg( 'settings-updated', 'true', wp_get_referer() ) );
		exit;
	}

	/**
	 * Get plugin settings.
	 *
	 * @since    1.0.0
	 * @return   array    The plugin settings.
	 */
	public function get_settings() {
		$default_settings = array(
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

		return wp_parse_args( get_option( 'wp_3d_model_viewer_settings', array() ), $default_settings );
	}

}
