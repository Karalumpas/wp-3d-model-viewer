<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/public
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wp-3d-model-viewer-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Only load model-viewer on pages that have shortcodes or blocks
		if ( $this->has_3d_content() ) {
			// Enqueue model-viewer library with module attribute
			wp_enqueue_script( 
				'model-viewer', 
				'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js', 
				array(), 
				'3.5.0', 
				false  // Load in head to ensure it's available when model-viewer tags are parsed
			);
			
			// Add module attribute to the model-viewer script
			add_filter( 'script_loader_tag', array( $this, 'add_module_attribute' ), 10, 3 );

			// Enqueue plugin JavaScript
			wp_enqueue_script( 
				$this->plugin_name, 
				plugin_dir_url( __FILE__ ) . 'js/wp-3d-model-viewer-public.js', 
				array( 'jquery', 'model-viewer' ), 
				$this->version, 
				true 
			);
		}
	}

	/**
	 * Add module attribute to model-viewer script.
	 *
	 * @since    1.0.0
	 * @param    string    $tag     The script tag.
	 * @param    string    $handle  The script handle.
	 * @param    string    $src     The script source URL.
	 * @return   string             Modified script tag.
	 */
	public function add_module_attribute( $tag, $handle, $src ) {
		
		if ( 'model-viewer' === $handle ) {
			// Add type="module" attribute for model-viewer (remove async to ensure proper loading)
			$tag = str_replace( '<script ', '<script type="module" ', $tag );
		}
		
		return $tag;
	}

	/**
	 * Check if the current page/post contains 3D model content.
	 *
	 * @since    1.0.0
	 * @return   bool    True if 3D content is found.
	 */
	private function has_3d_content() {
		
		global $post;
		
		// Always load on single 3D model posts
		if ( is_singular( 'wp_3d_model' ) ) {
			return true;
		}
		
		// Check if current post has shortcodes
		if ( $post && has_shortcode( $post->post_content, '3d_model_viewer' ) ) {
			return true;
		}
		
		if ( $post && has_shortcode( $post->post_content, '3d_model' ) ) {
			return true;
		}
		
		if ( $post && has_shortcode( $post->post_content, 'model_viewer' ) ) {
			return true;
		}
		
		// Check for Gutenberg blocks (if available)
		if ( function_exists( 'has_block' ) && $post && has_block( 'wp-3d-model-viewer/model-viewer', $post ) ) {
			return true;
		}
		
		// For now, let's always load to ensure compatibility
		// TODO: Optimize this in future versions
		return true;
	}

	/**
	 * Register the shortcode.
	 *
	 * @since    1.0.0
	 */
	public function register_shortcode() {
		add_shortcode( '3d_model_viewer', array( $this, 'render_shortcode' ) );
		add_shortcode( '3d_model', array( $this, 'render_shortcode' ) );
		add_shortcode( 'model_viewer', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Render the 3D model viewer shortcode.
	 *
	 * @since    1.0.0
	 * @param    array     $atts      Shortcode attributes.
	 * @param    string    $content   Shortcode content.
	 * @param    string    $tag       Shortcode tag.
	 * @return   string               HTML output for the 3D model viewer.
	 */
	public function render_shortcode( $atts, $content = '', $tag = '' ) {
		
		// Default attributes
		$default_atts = array(
			'id'               => '',        // CPT ID for 3D model
			'src'              => '',
			'width'            => '100%',
			'height'           => '400px',
			'background_color' => '#ffffff',
			'auto_rotate'      => 'false',
			'camera_controls'  => 'true',
			'loading'          => 'auto',
			'poster'           => '',
			'alt'              => '',
			'ar'               => 'false',
			'ar_modes'         => 'webxr scene-viewer quick-look',
			'ios_src'          => '',
			'class'            => '',
			'viewer_id'        => '',        // HTML element ID
			
			// New customization attributes
			'show_label'       => 'true',
			'label_text'       => '',
			'label_position'   => 'top-left',
			'label_color'      => 'rgba(0, 115, 170, 0.9)',
			'ar_position'      => 'bottom-left',
			'ar_color'         => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
			'show_border'      => 'true',
			'border_color'     => '#0073aa',
			'border_width'     => '2',
		);

		// Parse attributes
		$atts = shortcode_atts( $default_atts, $atts, $tag );

		// Check if this is a CPT-based shortcode
		if ( ! empty( $atts['id'] ) && is_numeric( $atts['id'] ) ) {
			return $this->render_cpt_model( $atts );
		}

		// Validate required attributes for manual shortcode
		if ( empty( $atts['src'] ) ) {
			return '<div class="wp3d-error">Error: Model source URL is required.</div>';
		}

		// Generate unique ID if not provided
		if ( empty( $atts['viewer_id'] ) ) {
			$atts['viewer_id'] = 'wp3d-viewer-' . uniqid();
		}

		// Sanitize attributes
		$src = esc_url( $atts['src'] );
		$width = $this->sanitize_dimension( $atts['width'] );
		$height = $this->sanitize_dimension( $atts['height'] );
		$background_color = sanitize_hex_color( $atts['background_color'] );
		$auto_rotate = $this->parse_boolean( $atts['auto_rotate'] );
		$camera_controls = $this->parse_boolean( $atts['camera_controls'] );
		$loading = sanitize_text_field( $atts['loading'] );
		$poster = esc_url( $atts['poster'] );
		$alt = sanitize_text_field( $atts['alt'] );
		$ar = $this->parse_boolean( $atts['ar'] );
		$ar_modes = sanitize_text_field( $atts['ar_modes'] );
		$ios_src = esc_url( $atts['ios_src'] );
		$class = sanitize_html_class( $atts['class'] );
		$viewer_id = sanitize_html_class( $atts['viewer_id'] );
		
		// Sanitize customization attributes
		$show_label = $this->parse_boolean( $atts['show_label'] );
		$label_text = sanitize_text_field( $atts['label_text'] );
		$label_position = sanitize_text_field( $atts['label_position'] );
		$label_color = sanitize_text_field( $atts['label_color'] );
		$ar_position = sanitize_text_field( $atts['ar_position'] );
		$ar_color = sanitize_text_field( $atts['ar_color'] );
		$show_border = $this->parse_boolean( $atts['show_border'] );
		$border_color = sanitize_hex_color( $atts['border_color'] ) ?: sanitize_text_field( $atts['border_color'] );
		$border_width = absint( $atts['border_width'] );

		// Build CSS classes
		$css_classes = array( 'wp3d-viewer' );
		if ( ! empty( $class ) ) {
			$css_classes[] = $class;
		}
		
		// Add customization classes for direct shortcode
		if ( $show_border ) {
			$css_classes[] = 'wp3d-has-border';
		}
		if ( $show_label && ! empty( $label_text ) ) {
			$css_classes[] = 'wp3d-has-label';
			$css_classes[] = 'wp3d-label-' . str_replace( '-', '_', $label_position );
		}
		if ( $ar ) {
			$css_classes[] = 'wp3d-has-ar';
			$css_classes[] = 'wp3d-ar-' . str_replace( '-', '_', $ar_position );
		}

		// Build model-viewer attributes
		$model_attributes = array(
			'src="' . $src . '"',
			'style="width: ' . $width . '; height: ' . $height . '; background-color: ' . $background_color . ';"',
			'id="' . $viewer_id . '"',
			'class="' . implode( ' ', $css_classes ) . '"',
		);

		if ( $auto_rotate ) {
			$model_attributes[] = 'auto-rotate';
		}

		if ( $camera_controls ) {
			$model_attributes[] = 'camera-controls';
		}

		if ( ! empty( $poster ) ) {
			$model_attributes[] = 'poster="' . $poster . '"';
		}

		if ( ! empty( $alt ) ) {
			$model_attributes[] = 'alt="' . $alt . '"';
		}

		if ( $ar ) {
			$model_attributes[] = 'ar';
			$model_attributes[] = 'ar-modes="' . $ar_modes . '"';
			
			if ( ! empty( $ios_src ) ) {
				$model_attributes[] = 'ios-src="' . $ios_src . '"';
			}
		}

		if ( $loading !== 'auto' ) {
			$model_attributes[] = 'loading="' . $loading . '"';
		}

		// Build the HTML
		$html = '<model-viewer ' . implode( ' ', $model_attributes ) . '>';
		
		// Add fallback content
		$html .= '<div slot="poster" class="wp3d-poster">';
		if ( ! empty( $poster ) ) {
			$html .= '<img src="' . $poster . '" alt="' . $alt . '">';
		} else {
			$html .= '<div class="wp3d-placeholder">Loading 3D Model...</div>';
		}
		$html .= '</div>';
		
		// Add AR button if enabled
		if ( $ar ) {
			$html .= '<button slot="ar-button" class="wp3d-ar-button wp3d-ar-' . str_replace( '-', '_', $ar_position ) . '" aria-label="View in AR">';
			$html .= '<span class="wp3d-ar-icon">📱</span>';
			$html .= '<span class="wp3d-ar-text">View in AR</span>';
			$html .= '</button>';
		}
		
		$html .= '</model-viewer>';
		
		// Add custom label overlay if enabled
		if ( $show_label && ! empty( $label_text ) ) {
			$html .= '<div class="wp3d-model-label wp3d-label-' . str_replace( '-', '_', $label_position ) . '">';
			$html .= esc_html( $label_text );
			$html .= '</div>';
		}

		// Add custom CSS for styling
		$html .= '<style>';
		
		// Border styling
		if ( $show_border ) {
			$html .= '#' . $viewer_id . ' { border: ' . $border_width . 'px solid ' . $border_color . '; }';
		} else {
			$html .= '#' . $viewer_id . ' { border: none; }';
		}
		
		// Custom label styling
		if ( $show_label && ! empty( $label_text ) ) {
			$position_styles = '';
			switch ( $label_position ) {
				case 'top-left':
					$position_styles = 'top: 10px; left: 10px;';
					break;
				case 'top-right':
					$position_styles = 'top: 10px; right: 10px;';
					break;
				case 'bottom-left':
					$position_styles = 'bottom: 10px; left: 10px;';
					break;
				case 'bottom-right':
					$position_styles = 'bottom: 10px; right: 10px;';
					break;
			}
			
			$html .= '#' . $viewer_id . ' + .wp3d-model-label {';
			$html .= 'position: absolute;';
			$html .= $position_styles;
			$html .= 'background: ' . $label_color . ';';
			$html .= 'color: white;';
			$html .= 'padding: 4px 8px;';
			$html .= 'border-radius: 4px;';
			$html .= 'font-size: 12px;';
			$html .= 'font-weight: bold;';
			$html .= 'z-index: 10;';
			$html .= 'pointer-events: none;';
			$html .= '}';
		}
		
		// Custom AR button styling
		if ( $ar ) {
			$position_styles = '';
			switch ( $ar_position ) {
				case 'top-left':
					$position_styles = 'top: 16px; left: 16px; bottom: auto; right: auto;';
					break;
				case 'top-right':
					$position_styles = 'top: 16px; right: 16px; bottom: auto; left: auto;';
					break;
				case 'bottom-left':
					$position_styles = 'bottom: 16px; left: 16px; top: auto; right: auto;';
					break;
				case 'bottom-right':
					$position_styles = 'bottom: 16px; right: 16px; top: auto; left: auto;';
					break;
			}
			
			$html .= '#' . $viewer_id . ' .wp3d-ar-button {';
			$html .= $position_styles;
			$html .= 'background: ' . $ar_color . ';';
			$html .= '}';
		}
		
		$html .= '</style>';

		// Wrap in container for proper positioning
		$container_html = '<div class="wp3d-model-container" style="position: relative; display: inline-block; width: ' . $width . '; height: ' . $height . ';">';
		$container_html .= $html;
		$container_html .= '</div>';
		
		$html = $container_html;

		// Add data attributes for JavaScript enhancement
		$html .= '<script type="application/json" class="wp3d-config" data-for="' . $viewer_id . '">';
		$html .= json_encode( array(
			'src' => $src,
			'autoRotate' => $auto_rotate,
			'cameraControls' => $camera_controls,
			'ar' => $ar,
			'arModes' => $ar_modes,
			'iosSrc' => $ios_src,
		) );
		$html .= '</script>';

		return $html;
	}

	/**
	 * Render 3D model from Custom Post Type.
	 *
	 * @since    1.0.0
	 * @param    array    $atts    Shortcode attributes.
	 * @return   string            HTML output for the 3D model viewer.
	 */
	private function render_cpt_model( $atts ) {
		
		$post_id = absint( $atts['id'] );
		
		// Check if post exists and is correct type
		$post = get_post( $post_id );
		if ( ! $post || $post->post_type !== 'wp_3d_model' ) {
			return '<div class="wp3d-error">Error: 3D Model not found (ID: ' . $post_id . ').</div>';
		}

		// Get model file
		$model_file_id = get_post_meta( $post_id, '_wp3d_model_file', true );
		if ( ! $model_file_id ) {
			return '<div class="wp3d-error">Error: No model file found for this 3D model.</div>';
		}

		$model_url = wp_get_attachment_url( $model_file_id );
		if ( ! $model_url ) {
			return '<div class="wp3d-error">Error: Model file URL could not be retrieved.</div>';
		}

		// Get model settings from post meta
		$bg_color = get_post_meta( $post_id, '_wp3d_bg_color', true ) ?: '#ffffff';
		$start_rotation = get_post_meta( $post_id, '_wp3d_start_rotation', true ) ?: '0deg 75deg 105%';
		$zoom_level = get_post_meta( $post_id, '_wp3d_zoom_level', true ) ?: '1';
		$ar_enabled = get_post_meta( $post_id, '_wp3d_ar_enabled', true );
		$auto_rotate = get_post_meta( $post_id, '_wp3d_auto_rotate', true );
		$camera_controls = get_post_meta( $post_id, '_wp3d_camera_controls', true );
		
		// Get appearance settings
		$show_label = get_post_meta( $post_id, '_wp3d_show_label', true );
		$label_text = get_post_meta( $post_id, '_wp3d_label_text', true ) ?: '3D Model';
		$label_position = get_post_meta( $post_id, '_wp3d_label_position', true ) ?: 'top-left';
		$label_color = get_post_meta( $post_id, '_wp3d_label_color', true ) ?: 'rgba(0, 115, 170, 0.9)';
		$ar_position = get_post_meta( $post_id, '_wp3d_ar_position', true ) ?: 'bottom-left';
		$ar_color = get_post_meta( $post_id, '_wp3d_ar_color', true ) ?: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
		$show_border = get_post_meta( $post_id, '_wp3d_show_border', true );
		$border_color = get_post_meta( $post_id, '_wp3d_border_color', true ) ?: '#0073aa';
		$border_width = get_post_meta( $post_id, '_wp3d_border_width', true ) ?: '2';
		$border_shadow = get_post_meta( $post_id, '_wp3d_border_shadow', true );
		$shadow_intensity = get_post_meta( $post_id, '_wp3d_shadow_intensity', true ) ?: '3';
		
		// Set defaults for checkboxes if empty
		if ( $show_label === '' ) $show_label = '1';
		if ( $show_border === '' ) $show_border = '1';
		if ( $border_shadow === '' ) $border_shadow = '1';
		
		// Set default for camera_controls if empty (should be enabled by default)
		if ( $camera_controls === '' ) {
			$camera_controls = '1';
		}

		// Get optional files
		$ios_file_id = get_post_meta( $post_id, '_wp3d_ios_file', true );
		$poster_image_id = get_post_meta( $post_id, '_wp3d_poster_image', true );

		$ios_src = '';
		if ( $ios_file_id ) {
			$ios_src = wp_get_attachment_url( $ios_file_id );
		}

		$poster_src = '';
		if ( $poster_image_id ) {
			$poster_src = wp_get_attachment_url( $poster_image_id );
		} elseif ( has_post_thumbnail( $post_id ) ) {
			$poster_src = get_the_post_thumbnail_url( $post_id, 'large' );
		}

		// Override with shortcode attributes if provided
		$width = ! empty( $atts['width'] ) ? $this->sanitize_dimension( $atts['width'] ) : '100%';
		$height = ! empty( $atts['height'] ) ? $this->sanitize_dimension( $atts['height'] ) : '400px';
		$class = ! empty( $atts['class'] ) ? sanitize_html_class( $atts['class'] ) : '';

		// Generate unique viewer ID
		$viewer_id = ! empty( $atts['viewer_id'] ) ? sanitize_html_class( $atts['viewer_id'] ) : 'wp3d-model-' . $post_id . '-' . uniqid();

		// Build CSS classes
		$css_classes = array( 'wp3d-viewer', 'wp3d-cpt-model' );
		if ( ! empty( $class ) ) {
			$css_classes[] = $class;
		}
		
		// Add customization classes
		if ( $show_border && $show_border !== '0' ) {
			$css_classes[] = 'wp3d-has-border';
		}
		if ( $border_shadow && $border_shadow !== '0' ) {
			$css_classes[] = 'wp3d-has-shadow';
		}
		if ( $show_label && $show_label !== '0' && ! empty( $label_text ) ) {
			$css_classes[] = 'wp3d-has-label';
			$css_classes[] = 'wp3d-label-' . str_replace( '-', '_', $label_position );
		}
		if ( $ar_enabled ) {
			$css_classes[] = 'wp3d-has-ar';
			$css_classes[] = 'wp3d-ar-' . str_replace( '-', '_', $ar_position );
		}

		// Build model-viewer attributes array for better organization
		$model_attributes = array();
		
		// Required attributes
		$model_attributes['src'] = esc_url( $model_url );
		$model_attributes['id'] = $viewer_id;
		$model_attributes['class'] = implode( ' ', $css_classes );
		
		// Styling attributes
		$model_attributes['style'] = sprintf(
			'width: %s; height: %s; background-color: %s;',
			$width,
			$height,
			esc_attr( $bg_color )
		);
		
		// Camera and interaction attributes
		if ( $start_rotation && $start_rotation !== '0deg 75deg 105%' && $start_rotation !== '0deg 0deg 0deg' ) {
			$model_attributes['camera-orbit'] = esc_attr( $start_rotation );
		}
		
		if ( $auto_rotate ) {
			$model_attributes['auto-rotate'] = '';
		}
		
		if ( $camera_controls ) {
			$model_attributes['camera-controls'] = '';
		}
		
		// Media attributes
		if ( ! empty( $poster_src ) ) {
			$model_attributes['poster'] = esc_url( $poster_src );
		}
		
		$alt_text = $post->post_title ? esc_attr( $post->post_title ) : '3D Model';
		$model_attributes['alt'] = $alt_text;
		
		// AR attributes
		if ( $ar_enabled ) {
			$model_attributes['ar'] = '';
			$model_attributes['ar-modes'] = 'webxr scene-viewer quick-look';
			
			if ( ! empty( $ios_src ) ) {
				$model_attributes['ios-src'] = esc_url( $ios_src );
			}
		}
		
		// Performance attributes - use eager loading to ensure model shows immediately
		$model_attributes['loading'] = 'eager';
		// Remove reveal=interaction to allow immediate loading
		
		// Convert attributes array to string
		$attributes_string = '';
		foreach ( $model_attributes as $key => $value ) {
			if ( $value === '' ) {
				// Boolean attributes
				$attributes_string .= ' ' . $key;
			} else {
				// Value attributes
				$attributes_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		// Build the HTML with proper model-viewer tag
		$html = '<model-viewer' . $attributes_string . '>';
		
		// Add loading placeholder content
		$html .= '<div slot="poster" class="wp3d-loading-container">';
		if ( ! empty( $poster_src ) ) {
			$html .= '<img src="' . esc_url( $poster_src ) . '" alt="' . $alt_text . '" class="wp3d-poster-image" />';
		} else {
			$html .= '<div class="wp3d-loading-placeholder">';
			$html .= '<div class="wp3d-loading-spinner"></div>';
			$html .= '<p>Loading 3D Model: ' . esc_html( $post->post_title ) . '</p>';
			$html .= '</div>';
		}
		$html .= '</div>';
		
		// Add progress bar for loading
		$html .= '<div slot="progress-bar" class="wp3d-progress-bar">';
		$html .= '<div class="wp3d-progress-fill"></div>';
		$html .= '</div>';
		
		// Add AR button if enabled
		if ( $ar_enabled ) {
			$html .= '<button slot="ar-button" class="wp3d-ar-button wp3d-ar-' . str_replace( '-', '_', $ar_position ) . '" aria-label="View in AR">';
			$html .= '<span class="wp3d-ar-icon">📱</span>';
			$html .= '<span class="wp3d-ar-text">View in AR</span>';
			$html .= '</button>';
		}
		
		// Add error fallback
		$html .= '<div slot="fallback" class="wp3d-error-fallback">';
		$html .= '<p>Unable to load 3D model. Please try refreshing the page.</p>';
		if ( ! empty( $poster_src ) ) {
			$html .= '<img src="' . esc_url( $poster_src ) . '" alt="' . $alt_text . '" />';
		}
		$html .= '</div>';
		
		$html .= '</model-viewer>';
		
		// Add custom label overlay if enabled
		if ( $show_label && $show_label !== '0' && ! empty( $label_text ) ) {
			$html .= '<div class="wp3d-model-label wp3d-label-' . str_replace( '-', '_', $label_position ) . '">';
			$html .= esc_html( $label_text );
			$html .= '</div>';
		}

		// Add custom CSS for styling
		$html .= '<style>';
		
		// Border styling
		if ( $show_border && $show_border !== '0' ) {
			$html .= '#' . $viewer_id . '.wp3d-cpt-model { border: ' . absint( $border_width ) . 'px solid ' . esc_attr( $border_color ) . '; }';
		} else {
			$html .= '#' . $viewer_id . '.wp3d-cpt-model { border: none; }';
		}
		
		// Shadow styling
		if ( $border_shadow && $border_shadow !== '0' ) {
			$shadow_blur = absint( $shadow_intensity ) * 2; // Base blur
			$shadow_spread = absint( $shadow_intensity ); // Spread
			$shadow_offset = absint( $shadow_intensity ); // Offset
			$shadow_opacity = 0.1 + ( absint( $shadow_intensity ) * 0.05 ); // Dynamic opacity
			
			$html .= '#' . $viewer_id . '.wp3d-cpt-model { ';
			$html .= 'box-shadow: 0 ' . $shadow_offset . 'px ' . $shadow_blur . 'px ' . $shadow_spread . 'px rgba(0, 0, 0, ' . $shadow_opacity . '); ';
			$html .= '}';
		} else {
			$html .= '#' . $viewer_id . '.wp3d-cpt-model { box-shadow: none; }';
		}
		
		// Remove default label if custom label is used or hidden
		if ( $show_label === '0' || ! empty( $label_text ) ) {
			$html .= '#' . $viewer_id . '.wp3d-cpt-model::after { display: none; }';
		}
		
		// Custom label styling
		if ( $show_label && $show_label !== '0' && ! empty( $label_text ) ) {
			$position_styles = '';
			switch ( $label_position ) {
				case 'top-left':
					$position_styles = 'top: 10px; left: 10px;';
					break;
				case 'top-right':
					$position_styles = 'top: 10px; right: 10px;';
					break;
				case 'bottom-left':
					$position_styles = 'bottom: 10px; left: 10px;';
					break;
				case 'bottom-right':
					$position_styles = 'bottom: 10px; right: 10px;';
					break;
			}
			
			$html .= '#' . $viewer_id . ' + .wp3d-model-label {';
			$html .= 'position: absolute;';
			$html .= $position_styles;
			$html .= 'background: ' . esc_attr( $label_color ) . ';';
			$html .= 'color: white;';
			$html .= 'padding: 4px 8px;';
			$html .= 'border-radius: 4px;';
			$html .= 'font-size: 12px;';
			$html .= 'font-weight: bold;';
			$html .= 'z-index: 10;';
			$html .= 'pointer-events: none;';
			$html .= '}';
		}
		
		// Custom AR button styling
		if ( $ar_enabled ) {
			$position_styles = '';
			switch ( $ar_position ) {
				case 'top-left':
					$position_styles = 'top: 16px; left: 16px; bottom: auto; right: auto;';
					break;
				case 'top-right':
					$position_styles = 'top: 16px; right: 16px; bottom: auto; left: auto;';
					break;
				case 'bottom-left':
					$position_styles = 'bottom: 16px; left: 16px; top: auto; right: auto;';
					break;
				case 'bottom-right':
					$position_styles = 'bottom: 16px; right: 16px; top: auto; left: auto;';
					break;
			}
			
			$html .= '#' . $viewer_id . ' .wp3d-ar-button {';
			$html .= $position_styles;
			$html .= 'background: ' . esc_attr( $ar_color ) . ';';
			$html .= '}';
		}
		
		$html .= '</style>';

		// Wrap in container for proper positioning
		$container_html = '<div class="wp3d-model-container" style="position: relative; display: inline-block; width: ' . $width . '; height: ' . $height . ';">';
		$container_html .= $html;
		$container_html .= '</div>';
		
		$html = $container_html;

		// Add enhanced data attributes for JavaScript
		$html .= '<script type="application/json" class="wp3d-model-config" data-model-id="' . $viewer_id . '">';
		$html .= json_encode( array(
			'postId' => $post_id,
			'modelUrl' => $model_url,
			'posterUrl' => $poster_src,
			'iosUrl' => $ios_src,
			'title' => $post->post_title,
			'autoRotate' => (bool) $auto_rotate,
			'cameraControls' => (bool) $camera_controls,
			'arEnabled' => (bool) $ar_enabled,
			'cameraOrbit' => $start_rotation,
			'zoomLevel' => floatval( $zoom_level ),
			'backgroundColor' => $bg_color,
			'iosSrc' => $ios_src,
		) );
		$html .= '</script>';

		// Add debug information if WP_DEBUG is enabled
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$html .= '<!-- WP 3D Model Viewer Debug Info:
Model ID: ' . $post_id . '
Model URL: ' . $model_url . '
Camera Orbit: ' . $start_rotation . '
Auto Rotate: ' . ( $auto_rotate ? 'Yes' : 'No' ) . '
Camera Controls: ' . ( $camera_controls ? 'Yes' : 'No' ) . '
AR Enabled: ' . ( $ar_enabled ? 'Yes' : 'No' ) . '
Background: ' . $bg_color . '
-->';
		}

		return $html;
	}

	/**
	 * Register Gutenberg block.
	 *
	 * @since    1.0.0
	 */
	public function register_block() {
		
		// Check if Gutenberg is available
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Register block script
		wp_register_script(
			'wp-3d-model-viewer-block',
			plugin_dir_url( __FILE__ ) . 'js/blocks/wp-3d-model-viewer-block.js',
			array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ),
			$this->version,
			true
		);

		// Register block style
		wp_register_style(
			'wp-3d-model-viewer-block',
			plugin_dir_url( __FILE__ ) . 'css/blocks/wp-3d-model-viewer-block.css',
			array( 'wp-edit-blocks' ),
			$this->version
		);

		// Register the block
		register_block_type( 'wp-3d-model-viewer/model-viewer', array(
			'editor_script'   => 'wp-3d-model-viewer-block',
			'editor_style'    => 'wp-3d-model-viewer-block',
			'render_callback' => array( $this, 'render_block' ),
			'attributes'      => array(
				'src' => array(
					'type'    => 'string',
					'default' => '',
				),
				'width' => array(
					'type'    => 'string',
					'default' => '100%',
				),
				'height' => array(
					'type'    => 'string',
					'default' => '400px',
				),
				'backgroundColorProperty' => array(
					'type'    => 'string',
					'default' => '#ffffff',
				),
				'autoRotate' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'cameraControls' => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'poster' => array(
					'type'    => 'string',
					'default' => '',
				),
				'alt' => array(
					'type'    => 'string',
					'default' => '',
				),
				'ar' => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'iosSrc' => array(
					'type'    => 'string',
					'default' => '',
				),
			),
		) );
	}

	/**
	 * Render Gutenberg block.
	 *
	 * @since    1.0.0
	 * @param    array    $attributes    Block attributes.
	 * @return   string                  HTML output for the block.
	 */
	public function render_block( $attributes ) {
		
		// Convert block attributes to shortcode format
		$shortcode_atts = array(
			'src'              => $attributes['src'] ?? '',
			'width'            => $attributes['width'] ?? '100%',
			'height'           => $attributes['height'] ?? '400px',
			'background_color' => $attributes['backgroundColorProperty'] ?? '#ffffff',
			'auto_rotate'      => $attributes['autoRotate'] ? 'true' : 'false',
			'camera_controls'  => $attributes['cameraControls'] ? 'true' : 'false',
			'poster'           => $attributes['poster'] ?? '',
			'alt'              => $attributes['alt'] ?? '',
			'ar'               => $attributes['ar'] ? 'true' : 'false',
			'ios_src'          => $attributes['iosSrc'] ?? '',
		);

		return $this->render_shortcode( $shortcode_atts );
	}

	/**
	 * Sanitize dimension values (width/height).
	 *
	 * @since    1.0.0
	 * @param    string    $dimension    The dimension value to sanitize.
	 * @return   string                  The sanitized dimension value.
	 */
	private function sanitize_dimension( $dimension ) {
		// Allow percentages, pixels, em, rem, vh, vw
		if ( preg_match( '/^(\d+(?:\.\d+)?)(px|%|em|rem|vh|vw)$/', $dimension ) ) {
			return $dimension;
		}
		
		// If it's just a number, assume pixels
		if ( is_numeric( $dimension ) ) {
			return $dimension . 'px';
		}
		
		// Default fallback
		return '400px';
	}

	/**
	 * Parse boolean values from strings.
	 *
	 * @since    1.0.0
	 * @param    string|bool    $value    The value to parse.
	 * @return   bool                     The boolean value.
	 */
	private function parse_boolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		
		if ( is_string( $value ) ) {
			return in_array( strtolower( $value ), array( 'true', '1', 'yes', 'on' ), true );
		}
		
		return (bool) $value;
	}

}
