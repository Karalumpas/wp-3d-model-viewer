<?php

/**
 * Custom Post Type functionality for 3D Models
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 */

/**
 * Custom Post Type functionality for 3D Models.
 *
 * Registers the 3D Model custom post type, handles metaboxes,
 * and manages the admin list table customizations.
 *
 * @since      1.0.0
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/includes
 * @author     Karalumpas <your-email@example.com>
 */
class WP_3D_Model_Viewer_CPT {

	/**
	 * The post type slug.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $post_type    The post type slug.
	 */
	private $post_type = '3d_model';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		// Constructor intentionally left empty
	}

	/**
	 * Register the custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_post_type() {
		
		$labels = array(
			'name'                  => _x( '3D Models', 'Post type general name', 'wp-3d-model-viewer' ),
			'singular_name'         => _x( '3D Model', 'Post type singular name', 'wp-3d-model-viewer' ),
			'menu_name'             => _x( '3D Models', 'Admin Menu text', 'wp-3d-model-viewer' ),
			'name_admin_bar'        => _x( '3D Model', 'Add New on Toolbar', 'wp-3d-model-viewer' ),
			'add_new'               => __( 'Add New', 'wp-3d-model-viewer' ),
			'add_new_item'          => __( 'Add New 3D Model', 'wp-3d-model-viewer' ),
			'new_item'              => __( 'New 3D Model', 'wp-3d-model-viewer' ),
			'edit_item'             => __( 'Edit 3D Model', 'wp-3d-model-viewer' ),
			'view_item'             => __( 'View 3D Model', 'wp-3d-model-viewer' ),
			'all_items'             => __( 'All 3D Models', 'wp-3d-model-viewer' ),
			'search_items'          => __( 'Search 3D Models', 'wp-3d-model-viewer' ),
			'parent_item_colon'     => __( 'Parent 3D Models:', 'wp-3d-model-viewer' ),
			'not_found'             => __( 'No 3D models found.', 'wp-3d-model-viewer' ),
			'not_found_in_trash'    => __( 'No 3D models found in Trash.', 'wp-3d-model-viewer' ),
			'featured_image'        => _x( 'Model Thumbnail', 'Overrides the "Featured Image" phrase', 'wp-3d-model-viewer' ),
			'set_featured_image'    => _x( 'Set model thumbnail', 'Overrides the "Set featured image" phrase', 'wp-3d-model-viewer' ),
			'remove_featured_image' => _x( 'Remove model thumbnail', 'Overrides the "Remove featured image" phrase', 'wp-3d-model-viewer' ),
			'use_featured_image'    => _x( 'Use as model thumbnail', 'Overrides the "Use as featured image" phrase', 'wp-3d-model-viewer' ),
			'archives'              => _x( '3D Model archives', 'The post type archive label', 'wp-3d-model-viewer' ),
			'insert_into_item'      => _x( 'Insert into 3D model', 'Overrides the "Insert into post"/"Insert into page" phrase', 'wp-3d-model-viewer' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this 3D model', 'Overrides the "Uploaded to this post"/"Uploaded to this page" phrase', 'wp-3d-model-viewer' ),
			'filter_items_list'     => _x( 'Filter 3D models list', 'Screen reader text for the filter links', 'wp-3d-model-viewer' ),
			'items_list_navigation' => _x( '3D Models list navigation', 'Screen reader text for the pagination', 'wp-3d-model-viewer' ),
			'items_list'            => _x( '3D Models list', 'Screen reader text for the items list', 'wp-3d-model-viewer' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => '3d-model' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-format-gallery',
			'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'custom-fields' ),
			'show_in_rest'       => true,
			'rest_base'          => '3d-models',
		);

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Add custom columns to the admin list table.
	 *
	 * @since    1.0.0
	 * @param    array    $columns    Existing columns.
	 * @return   array                Modified columns.
	 */
	public function add_admin_columns( $columns ) {
		
		// Insert thumbnail and shortcode columns after title
		$new_columns = array();
		foreach ( $columns as $key => $value ) {
			$new_columns[ $key ] = $value;
			
			if ( $key === 'title' ) {
				$new_columns['thumbnail'] = __( 'Thumbnail', 'wp-3d-model-viewer' );
				$new_columns['shortcode'] = __( 'Shortcode', 'wp-3d-model-viewer' );
			}
		}
		
		return $new_columns;
	}

	/**
	 * Populate custom columns in the admin list table.
	 *
	 * @since    1.0.0
	 * @param    string    $column     Column name.
	 * @param    int       $post_id    Post ID.
	 */
	public function populate_admin_columns( $column, $post_id ) {
		
		switch ( $column ) {
			case 'thumbnail':
				$this->display_thumbnail_column( $post_id );
				break;
				
			case 'shortcode':
				$this->display_shortcode_column( $post_id );
				break;
		}
	}

	/**
	 * Display thumbnail column content.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Post ID.
	 */
	private function display_thumbnail_column( $post_id ) {
		
		// Try to get featured image first
		if ( has_post_thumbnail( $post_id ) ) {
			echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'border-radius: 4px;' ) );
		} else {
			// Fallback to model file thumbnail if available
			$model_file = get_post_meta( $post_id, '_wp3d_model_file', true );
			$poster_image = get_post_meta( $post_id, '_wp3d_poster_image', true );
			
			if ( $poster_image ) {
				$image = wp_get_attachment_image( $poster_image, array( 60, 60 ), false, array( 'style' => 'border-radius: 4px;' ) );
				echo $image ? $image : '<span class="dashicons dashicons-format-gallery" style="font-size: 40px; color: #ccc;"></span>';
			} else {
				echo '<span class="dashicons dashicons-format-gallery" style="font-size: 40px; color: #ccc;"></span>';
			}
		}
	}

	/**
	 * Display shortcode column content.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Post ID.
	 */
	private function display_shortcode_column( $post_id ) {
		
		$shortcode = sprintf( '[3d_model id="%d"]', $post_id );
		
		echo '<code style="background: #f1f1f1; padding: 4px 8px; border-radius: 3px; font-size: 12px;">' . esc_html( $shortcode ) . '</code>';
		echo '<br><small style="color: #666;">Click to copy</small>';
		
		// Add inline JavaScript for copy functionality
		echo '<script>
			jQuery(document).ready(function($) {
				$("code").click(function() {
					var text = $(this).text();
					if (navigator.clipboard) {
						navigator.clipboard.writeText(text).then(function() {
							console.log("Shortcode copied to clipboard");
						});
					}
				});
			});
		</script>';
	}

	/**
	 * Make custom columns sortable.
	 *
	 * @since    1.0.0
	 * @param    array    $columns    Sortable columns.
	 * @return   array                Modified sortable columns.
	 */
	public function make_columns_sortable( $columns ) {
		// For now, we don't need sorting on thumbnail or shortcode columns
		return $columns;
	}

	/**
	 * Add metaboxes for 3D model settings.
	 *
	 * @since    1.0.0
	 */
	public function add_metaboxes() {
		
		add_meta_box(
			'wp3d-model-file',
			__( '3D Model File', 'wp-3d-model-viewer' ),
			array( $this, 'model_file_metabox_callback' ),
			$this->post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'wp3d-model-settings',
			__( '3D Model Settings', 'wp-3d-model-viewer' ),
			array( $this, 'model_settings_metabox_callback' ),
			$this->post_type,
			'normal',
			'high'
		);

		add_meta_box(
			'wp3d-shortcode-usage',
			__( 'Shortcode Usage', 'wp-3d-model-viewer' ),
			array( $this, 'shortcode_usage_metabox_callback' ),
			$this->post_type,
			'side',
			'high'
		);
	}

	/**
	 * Model file metabox callback.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Post object.
	 */
	public function model_file_metabox_callback( $post ) {
		
		// Add nonce for security
		wp_nonce_field( 'wp3d_save_model_data', 'wp3d_model_nonce' );

		// Get current values
		$model_file = get_post_meta( $post->ID, '_wp3d_model_file', true );
		$ios_file = get_post_meta( $post->ID, '_wp3d_ios_file', true );
		$poster_image = get_post_meta( $post->ID, '_wp3d_poster_image', true );

		?>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="wp3d_model_file"><?php _e( '3D Model File', 'wp-3d-model-viewer' ); ?></label>
				</th>
				<td>
					<input type="hidden" id="wp3d_model_file" name="wp3d_model_file" value="<?php echo esc_attr( $model_file ); ?>" />
					<input type="button" class="button wp3d-upload-button" data-target="wp3d_model_file" data-title="<?php esc_attr_e( 'Select 3D Model File', 'wp-3d-model-viewer' ); ?>" data-button="<?php esc_attr_e( 'Use this file', 'wp-3d-model-viewer' ); ?>" value="<?php esc_attr_e( 'Select Model File', 'wp-3d-model-viewer' ); ?>" />
					<input type="button" class="button wp3d-remove-button" data-target="wp3d_model_file" value="<?php esc_attr_e( 'Remove', 'wp-3d-model-viewer' ); ?>" style="<?php echo $model_file ? '' : 'display:none;'; ?>" />
					<div class="wp3d-file-preview" id="wp3d_model_file_preview" style="margin-top: 10px;">
						<?php if ( $model_file ) : ?>
							<?php $file_url = wp_get_attachment_url( $model_file ); ?>
							<p><strong><?php _e( 'Selected file:', 'wp-3d-model-viewer' ); ?></strong> <?php echo esc_html( basename( $file_url ) ); ?></p>
							<p><small><?php echo esc_url( $file_url ); ?></small></p>
						<?php endif; ?>
					</div>
					<p class="description"><?php _e( 'Select the main 3D model file (GLTF, GLB, OBJ, etc.)', 'wp-3d-model-viewer' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="wp3d_ios_file"><?php _e( 'iOS AR File (USDZ)', 'wp-3d-model-viewer' ); ?></label>
				</th>
				<td>
					<input type="hidden" id="wp3d_ios_file" name="wp3d_ios_file" value="<?php echo esc_attr( $ios_file ); ?>" />
					<input type="button" class="button wp3d-upload-button" data-target="wp3d_ios_file" data-title="<?php esc_attr_e( 'Select iOS USDZ File', 'wp-3d-model-viewer' ); ?>" data-button="<?php esc_attr_e( 'Use this file', 'wp-3d-model-viewer' ); ?>" value="<?php esc_attr_e( 'Select iOS File', 'wp-3d-model-viewer' ); ?>" />
					<input type="button" class="button wp3d-remove-button" data-target="wp3d_ios_file" value="<?php esc_attr_e( 'Remove', 'wp-3d-model-viewer' ); ?>" style="<?php echo $ios_file ? '' : 'display:none;'; ?>" />
					<div class="wp3d-file-preview" id="wp3d_ios_file_preview" style="margin-top: 10px;">
						<?php if ( $ios_file ) : ?>
							<?php $file_url = wp_get_attachment_url( $ios_file ); ?>
							<p><strong><?php _e( 'Selected file:', 'wp-3d-model-viewer' ); ?></strong> <?php echo esc_html( basename( $file_url ) ); ?></p>
							<p><small><?php echo esc_url( $file_url ); ?></small></p>
						<?php endif; ?>
					</div>
					<p class="description"><?php _e( 'Optional: USDZ file for iOS AR Quick Look support', 'wp-3d-model-viewer' ); ?></p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="wp3d_poster_image"><?php _e( 'Poster Image', 'wp-3d-model-viewer' ); ?></label>
				</th>
				<td>
					<input type="hidden" id="wp3d_poster_image" name="wp3d_poster_image" value="<?php echo esc_attr( $poster_image ); ?>" />
					<input type="button" class="button wp3d-upload-button" data-target="wp3d_poster_image" data-title="<?php esc_attr_e( 'Select Poster Image', 'wp-3d-model-viewer' ); ?>" data-button="<?php esc_attr_e( 'Use this image', 'wp-3d-model-viewer' ); ?>" value="<?php esc_attr_e( 'Select Poster Image', 'wp-3d-model-viewer' ); ?>" />
					<input type="button" class="button wp3d-remove-button" data-target="wp3d_poster_image" value="<?php esc_attr_e( 'Remove', 'wp-3d-model-viewer' ); ?>" style="<?php echo $poster_image ? '' : 'display:none;'; ?>" />
					<div class="wp3d-file-preview" id="wp3d_poster_image_preview" style="margin-top: 10px;">
						<?php if ( $poster_image ) : ?>
							<?php echo wp_get_attachment_image( $poster_image, array( 150, 150 ) ); ?>
						<?php endif; ?>
					</div>
					<p class="description"><?php _e( 'Optional: Image to show while the 3D model is loading', 'wp-3d-model-viewer' ); ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Model settings metabox callback.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Post object.
	 */
	public function model_settings_metabox_callback( $post ) {
		
		// Get current values
		$bg_color = get_post_meta( $post->ID, '_wp3d_bg_color', true );
		$start_rotation = get_post_meta( $post->ID, '_wp3d_start_rotation', true );
		$camera_orbit = get_post_meta( $post->ID, '_wp3d_camera_orbit', true );
		$camera_target = get_post_meta( $post->ID, '_wp3d_camera_target', true );
		$zoom_level = get_post_meta( $post->ID, '_wp3d_zoom_level', true );
		$ar_enabled = get_post_meta( $post->ID, '_wp3d_ar_enabled', true );
		$auto_rotate = get_post_meta( $post->ID, '_wp3d_auto_rotate', true );
		$camera_controls = get_post_meta( $post->ID, '_wp3d_camera_controls', true );
		$model_file = get_post_meta( $post->ID, '_wp3d_model_file', true );

		// Set defaults
		if ( empty( $bg_color ) ) $bg_color = '#ffffff';
		if ( empty( $start_rotation ) ) $start_rotation = '0deg 75deg 105%';
		if ( empty( $camera_orbit ) ) $camera_orbit = '0deg 75deg 105%';
		if ( empty( $camera_target ) ) $camera_target = 'auto auto auto';
		if ( empty( $zoom_level ) ) $zoom_level = '1';
		if ( $camera_controls === '' ) $camera_controls = '1';

		?>
		<div class="wp3d-admin-settings">
			<!-- Interactive 3D Model Preview Section -->
			<?php if ( $model_file ) : ?>
				<div class="wp3d-preview-section">
					<h3><?php _e( 'Interactive Model Preview', 'wp-3d-model-viewer' ); ?></h3>
					<p class="description"><?php _e( 'Use the preview below to position your 3D model. Rotate, zoom, and pan to find the perfect view, then click "Capture Current Position" to save it.', 'wp-3d-model-viewer' ); ?></p>
					
					<div class="wp3d-preview-container">
						<model-viewer 
							id="wp3d-admin-preview"
							src="<?php echo esc_url( wp_get_attachment_url( $model_file ) ); ?>"
							camera-controls
							auto-rotate-delay="3000"
							camera-orbit="<?php echo esc_attr( $camera_orbit ); ?>"
							camera-target="<?php echo esc_attr( $camera_target ); ?>"
							style="width: 100%; height: 400px; background-color: <?php echo esc_attr( $bg_color ); ?>; border-radius: 8px; border: 1px solid #ddd;"
							loading="eager">
							<div slot="progress-bar" style="display: none;"></div>
						</model-viewer>
						
						<div class="wp3d-preview-controls">
							<button type="button" id="wp3d-capture-position" class="button button-primary">
								<span class="dashicons dashicons-camera"></span>
								<?php _e( 'Capture Current Position', 'wp-3d-model-viewer' ); ?>
							</button>
							<button type="button" id="wp3d-reset-position" class="button">
								<span class="dashicons dashicons-image-rotate"></span>
								<?php _e( 'Reset to Default', 'wp-3d-model-viewer' ); ?>
							</button>
							<div class="wp3d-position-status">
								<span class="wp3d-status-indicator" id="wp3d-status-indicator"></span>
								<span id="wp3d-position-text"><?php _e( 'Position the model and click capture', 'wp-3d-model-viewer' ); ?></span>
							</div>
						</div>
					</div>
				</div>
				<hr>
			<?php else : ?>
				<div class="wp3d-preview-section">
					<h3><?php _e( 'Interactive Model Preview', 'wp-3d-model-viewer' ); ?></h3>
					<div class="wp3d-no-model-notice">
						<p><span class="dashicons dashicons-info"></span> <?php _e( 'Upload a 3D model file above to see the interactive preview and position controls.', 'wp-3d-model-viewer' ); ?></p>
					</div>
				</div>
				<hr>
			<?php endif; ?>

			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="wp3d_bg_color"><?php _e( 'Background Color', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="color" id="wp3d_bg_color" name="wp3d_bg_color" value="<?php echo esc_attr( $bg_color ); ?>" />
						<p class="description"><?php _e( 'Background color for the 3D model viewer', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wp3d_camera_orbit"><?php _e( 'Camera Position', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="text" id="wp3d_camera_orbit" name="wp3d_camera_orbit" value="<?php echo esc_attr( $camera_orbit ); ?>" class="regular-text" readonly />
						<input type="hidden" id="wp3d_camera_target" name="wp3d_camera_target" value="<?php echo esc_attr( $camera_target ); ?>" />
						<input type="hidden" id="wp3d_start_rotation" name="wp3d_start_rotation" value="<?php echo esc_attr( $start_rotation ); ?>" />
						<p class="description"><?php _e( 'Camera orbit position (automatically captured from preview above)', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wp3d_zoom_level"><?php _e( 'Field of View', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="range" id="wp3d_zoom_level" name="wp3d_zoom_level" value="<?php echo esc_attr( $zoom_level ); ?>" min="10" max="120" step="1" />
						<span class="wp3d-zoom-value"><?php echo esc_html( $zoom_level ); ?>°</span>
						<p class="description"><?php _e( 'Field of view angle in degrees (10° = zoomed in, 120° = zoomed out)', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e( 'Viewer Controls', 'wp-3d-model-viewer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Viewer Controls', 'wp-3d-model-viewer' ); ?></span></legend>
							
							<label for="wp3d_camera_controls">
								<input type="checkbox" id="wp3d_camera_controls" name="wp3d_camera_controls" value="1" <?php checked( $camera_controls, '1' ); ?> />
								<?php _e( 'Enable camera controls (zoom, pan, rotate)', 'wp-3d-model-viewer' ); ?>
							</label>
							<br><br>

							<label for="wp3d_auto_rotate">
								<input type="checkbox" id="wp3d_auto_rotate" name="wp3d_auto_rotate" value="1" <?php checked( $auto_rotate, '1' ); ?> />
								<?php _e( 'Enable auto-rotation', 'wp-3d-model-viewer' ); ?>
							</label>
							<br><br>

							<label for="wp3d_ar_enabled">
								<input type="checkbox" id="wp3d_ar_enabled" name="wp3d_ar_enabled" value="1" <?php checked( $ar_enabled, '1' ); ?> />
								<?php _e( 'Enable Augmented Reality (AR) support', 'wp-3d-model-viewer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</table>
		</div>

		<style>
		.wp3d-admin-settings .wp3d-preview-section {
			margin-bottom: 20px;
		}
		
		.wp3d-preview-container {
			background: #f9f9f9;
			padding: 20px;
			border-radius: 8px;
			border: 1px solid #ddd;
		}
		
		.wp3d-preview-controls {
			margin-top: 15px;
			display: flex;
			align-items: center;
			gap: 10px;
			flex-wrap: wrap;
		}
		
		.wp3d-preview-controls .button .dashicons {
			font-size: 16px;
			width: 16px;
			height: 16px;
			margin-right: 5px;
		}
		
		.wp3d-position-status {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-left: auto;
			font-size: 14px;
		}
		
		.wp3d-status-indicator {
			width: 12px;
			height: 12px;
			border-radius: 50%;
			background: #ddd;
			transition: background-color 0.3s ease;
		}
		
		.wp3d-status-indicator.captured {
			background: #46b450;
		}
		
		.wp3d-status-indicator.modified {
			background: #ffb900;
		}
		
		#wp3d-admin-preview {
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		
		@media (max-width: 782px) {
			.wp3d-preview-controls {
				flex-direction: column;
				align-items: stretch;
			}
			
			.wp3d-position-status {
				margin-left: 0;
				justify-content: center;
			}
		}
		</style>

		<script>
		jQuery(document).ready(function($) {
			// Update zoom level display
			$('#wp3d_zoom_level').on('input', function() {
				$('.wp3d-zoom-value').text($(this).val() + '°');
				
				// Update the preview model's field-of-view
				const preview = document.getElementById('wp3d-admin-preview');
				if (preview) {
					preview.setAttribute('field-of-view', $(this).val() + 'deg');
				}
			});
			
			// Update background color in real-time
			$('#wp3d_bg_color').on('input', function() {
				const preview = document.getElementById('wp3d-admin-preview');
				if (preview) {
					preview.style.backgroundColor = $(this).val();
				}
			});
		});
		</script>
		<?php
	}

	/**
	 * Shortcode usage metabox callback.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Post object.
	 */
	public function shortcode_usage_metabox_callback( $post ) {
		
		$shortcode = sprintf( '[3d_model id="%d"]', $post->ID );
		
		?>
		<div class="wp3d-shortcode-usage">
			<p><strong><?php _e( 'Use this shortcode to display the 3D model:', 'wp-3d-model-viewer' ); ?></strong></p>
			<code class="wp3d-shortcode-copy" style="display: block; background: #f1f1f1; padding: 10px; border-radius: 3px; cursor: pointer; user-select: all;"><?php echo esc_html( $shortcode ); ?></code>
			<p><small><?php _e( 'Click the shortcode above to copy it to your clipboard.', 'wp-3d-model-viewer' ); ?></small></p>
			
			<hr style="margin: 20px 0;">
			
			<h4><?php _e( 'Alternative Usage', 'wp-3d-model-viewer' ); ?></h4>
			<p><?php _e( 'You can also use the generic shortcode with custom parameters:', 'wp-3d-model-viewer' ); ?></p>
			<code style="display: block; background: #f9f9f9; padding: 8px; font-size: 11px; border-radius: 3px;">[3d_model_viewer src="..." width="100%" height="400px"]</code>
		</div>

		<script>
		jQuery(document).ready(function($) {
			$('.wp3d-shortcode-copy').click(function() {
				var text = $(this).text();
				if (navigator.clipboard) {
					navigator.clipboard.writeText(text).then(function() {
						// Visual feedback
						var $this = $('.wp3d-shortcode-copy');
						var original = $this.css('background');
						$this.css('background', '#46b450');
						setTimeout(function() {
							$this.css('background', original);
						}, 500);
					});
				} else {
					// Fallback for older browsers
					$(this).select();
					document.execCommand('copy');
				}
			});
		});
		</script>
		<?php
	}

	/**
	 * Save metabox data.
	 *
	 * @since    1.0.0
	 * @param    int    $post_id    Post ID.
	 */
	public function save_metabox_data( $post_id ) {
		
		// Check if our nonce is set and verify it
		if ( ! isset( $_POST['wp3d_model_nonce'] ) || ! wp_verify_nonce( $_POST['wp3d_model_nonce'], 'wp3d_save_model_data' ) ) {
			return;
		}

		// Check if this is an autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if this is the correct post type
		if ( get_post_type( $post_id ) !== $this->post_type ) {
			return;
		}

		// Save model files
		$model_file = isset( $_POST['wp3d_model_file'] ) ? absint( $_POST['wp3d_model_file'] ) : '';
		$ios_file = isset( $_POST['wp3d_ios_file'] ) ? absint( $_POST['wp3d_ios_file'] ) : '';
		$poster_image = isset( $_POST['wp3d_poster_image'] ) ? absint( $_POST['wp3d_poster_image'] ) : '';

		update_post_meta( $post_id, '_wp3d_model_file', $model_file );
		update_post_meta( $post_id, '_wp3d_ios_file', $ios_file );
		update_post_meta( $post_id, '_wp3d_poster_image', $poster_image );

		// Save settings
		$bg_color = isset( $_POST['wp3d_bg_color'] ) ? sanitize_hex_color( $_POST['wp3d_bg_color'] ) : '#ffffff';
		$start_rotation = isset( $_POST['wp3d_start_rotation'] ) ? sanitize_text_field( $_POST['wp3d_start_rotation'] ) : '0deg 75deg 105%';
		$camera_orbit = isset( $_POST['wp3d_camera_orbit'] ) ? sanitize_text_field( $_POST['wp3d_camera_orbit'] ) : '0deg 75deg 105%';
		$camera_target = isset( $_POST['wp3d_camera_target'] ) ? sanitize_text_field( $_POST['wp3d_camera_target'] ) : 'auto auto auto';
		$zoom_level = isset( $_POST['wp3d_zoom_level'] ) ? intval( $_POST['wp3d_zoom_level'] ) : 75;
		$ar_enabled = isset( $_POST['wp3d_ar_enabled'] ) ? '1' : '0';
		$auto_rotate = isset( $_POST['wp3d_auto_rotate'] ) ? '1' : '0';
		$camera_controls = isset( $_POST['wp3d_camera_controls'] ) ? '1' : '0';

		// Ensure zoom level is within valid range
		$zoom_level = max( 10, min( 120, $zoom_level ) );

		update_post_meta( $post_id, '_wp3d_bg_color', $bg_color );
		update_post_meta( $post_id, '_wp3d_start_rotation', $start_rotation );
		update_post_meta( $post_id, '_wp3d_camera_orbit', $camera_orbit );
		update_post_meta( $post_id, '_wp3d_camera_target', $camera_target );
		update_post_meta( $post_id, '_wp3d_zoom_level', $zoom_level );
		update_post_meta( $post_id, '_wp3d_ar_enabled', $ar_enabled );
		update_post_meta( $post_id, '_wp3d_auto_rotate', $auto_rotate );
		update_post_meta( $post_id, '_wp3d_camera_controls', $camera_controls );
	}

	/**
	 * Enqueue admin scripts for media uploader.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_admin_scripts() {
		
		global $post_type;
		
		if ( $post_type === $this->post_type ) {
			wp_enqueue_media();
			// Enqueue model-viewer in admin for live preview
			wp_enqueue_script(
				'model-viewer',
				'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js',
				array(),
				'3.5.0',
				true
			);

			// Ensure script tag has type="module" and async
			add_filter( 'script_loader_tag', array( $this, 'add_module_async_attributes' ), 10, 3 );

			// Enqueue CPT admin script after model-viewer
			wp_enqueue_script( 'wp3d-admin-cpt', plugin_dir_url( __FILE__ ) . '../admin/js/wp-3d-model-viewer-cpt.js', array( 'jquery', 'model-viewer' ), '1.0.1', true );
		}
	}

	/**
	 * Add type="module" and async to model-viewer script tag in admin.
	 */
	public function add_module_async_attributes( $tag, $handle, $src ) {
		if ( 'model-viewer' === $handle ) {
			$tag = str_replace( '<script ', '<script type="module" async ', $tag );
		}
		return $tag;
	}

}
