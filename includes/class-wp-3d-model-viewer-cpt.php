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
		
		// Add 3D Model Preview metabox at the top
		add_meta_box(
			'wp3d-model-preview',
			'3D Model Preview',
			array( $this, 'model_preview_metabox_callback' ),
			$this->post_type,
			'normal',
			'high'
		);

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
	 * Model preview metabox callback.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Post object.
	 */
	public function model_preview_metabox_callback( $post ) {
		
		// Get current values
		$model_file = get_post_meta( $post->ID, '_wp3d_model_file', true );
		$poster_image = get_post_meta( $post->ID, '_wp3d_poster_image', true );
		$bg_color = get_post_meta( $post->ID, '_wp3d_bg_color', true ) ?: '#ffffff';
		$start_rotation = get_post_meta( $post->ID, '_wp3d_start_rotation', true ) ?: '0deg 75deg 105%';
		$auto_rotate = get_post_meta( $post->ID, '_wp3d_auto_rotate', true );
		$camera_controls = get_post_meta( $post->ID, '_wp3d_camera_controls', true );
		$ar_enabled = get_post_meta( $post->ID, '_wp3d_ar_enabled', true );

		?>
		<div class="wp3d-preview-container">
			<?php if ( $model_file ) : ?>
				<?php $model_url = wp_get_attachment_url( $model_file ); ?>
				
				<!-- Interactive 3D Model Viewer -->
				<div class="wp3d-viewer-section" style="background: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; overflow: hidden;">
					<div id="wp3d-admin-preview-container" style="position: relative;">
						<model-viewer 
							id="wp3d-main-preview"
							src="<?php echo esc_url( $model_url ); ?>"
							style="width: 100%; height: 450px; background-color: <?php echo esc_attr( $bg_color ); ?>;"
							camera-controls="<?php echo $camera_controls ? 'true' : 'false'; ?>"
							<?php if ( $auto_rotate ) : ?>auto-rotate<?php endif; ?>
							<?php if ( $start_rotation !== '0deg 75deg 105%' ) : ?>camera-orbit="<?php echo esc_attr( $start_rotation ); ?>"<?php endif; ?>
							loading="eager"
							class="wp3d-admin-preview"
							<?php if ( $poster_image ) : ?>
							poster="<?php echo esc_url( wp_get_attachment_url( $poster_image ) ); ?>"
							<?php endif; ?>
							alt="<?php echo esc_attr( $post->post_title ?: '3D Model Preview' ); ?>">
							
							<!-- Loading placeholder -->
							<div slot="poster" class="wp3d-loading-state">
								<div class="wp3d-spinner"></div>
								<p>Loading 3D Model...</p>
							</div>
							
							<!-- Error fallback -->
							<div slot="fallback" class="wp3d-error-state">
								<div class="dashicons dashicons-warning"></div>
								<p><strong>Unable to load 3D model</strong></p>
								<p>Please check the file format and try again.</p>
							</div>
						</model-viewer>
						
						<!-- Overlay Controls -->
						<div class="wp3d-overlay-controls">
							<button type="button" class="wp3d-control-btn" onclick="wp3dRefreshPreview()" title="Refresh Preview">
								<span class="dashicons dashicons-update"></span>
							</button>
							<button type="button" class="wp3d-control-btn" onclick="wp3dResetCamera()" title="Reset Camera">
								<span class="dashicons dashicons-location"></span>
							</button>
						</div>
					</div>
					
					<!-- Integrated Settings Panel -->
					<div class="wp3d-settings-panel">
						<div class="wp3d-settings-grid">
							
							<!-- Background Color -->
							<div class="wp3d-setting-group">
								<label for="wp3d_bg_color_preview">Background Color</label>
								<div class="wp3d-color-input">
									<input type="color" 
										   id="wp3d_bg_color_preview" 
										   name="wp3d_bg_color" 
										   value="<?php echo esc_attr( $bg_color ); ?>" 
										   onchange="updatePreviewBackground(this.value)" />
									<span class="wp3d-color-value"><?php echo esc_html( $bg_color ); ?></span>
								</div>
							</div>
							
							<!-- Camera Rotation -->
							<div class="wp3d-setting-group">
								<label for="wp3d_start_rotation_preview">Camera Position</label>
								<input type="text" 
									   id="wp3d_start_rotation_preview" 
									   name="wp3d_start_rotation" 
									   value="<?php echo esc_attr( $start_rotation ); ?>" 
									   placeholder="0deg 75deg 105%" 
									   onchange="updatePreviewCamera(this.value)" />
								<small>Format: theta phi radius</small>
							</div>
							
							<!-- Viewer Controls -->
							<div class="wp3d-setting-group wp3d-checkboxes">
								<label>Viewer Options</label>
								<div class="wp3d-checkbox-row">
									<label class="wp3d-checkbox-label">
										<input type="checkbox" 
											   id="wp3d_auto_rotate_preview" 
											   name="wp3d_auto_rotate" 
											   value="1" 
											   <?php checked( $auto_rotate, '1' ); ?>
											   onchange="updatePreviewAutoRotate(this.checked)" />
										<span class="checkmark"></span>
										Auto-rotate
									</label>
								</div>
								<div class="wp3d-checkbox-row">
									<label class="wp3d-checkbox-label">
										<input type="checkbox" 
											   id="wp3d_camera_controls_preview" 
											   name="wp3d_camera_controls" 
											   value="1" 
											   <?php checked( $camera_controls, '1' ); ?>
											   onchange="updatePreviewControls(this.checked)" />
										<span class="checkmark"></span>
										Camera controls
									</label>
								</div>
								<div class="wp3d-checkbox-row">
									<label class="wp3d-checkbox-label">
										<input type="checkbox" 
											   id="wp3d_ar_enabled_preview" 
											   name="wp3d_ar_enabled" 
											   value="1" 
											   <?php checked( $ar_enabled, '1' ); ?>
											   onchange="updatePreviewAR(this.checked)" />
										<span class="checkmark"></span>
										Enable AR
									</label>
								</div>
							</div>
							
							<!-- Quick Actions -->
							<div class="wp3d-setting-group">
								<label>Quick Actions</label>
								<div class="wp3d-quick-actions">
									<button type="button" class="button button-small" onclick="resetPreviewToDefaults()">
										Reset to Defaults
									</button>
									<button type="button" class="button button-small" onclick="copyCurrentSettings()">
										Copy Settings
									</button>
								</div>
							</div>
							
						</div>
						
						<!-- Instructions -->
						<div class="wp3d-instructions">
							<small>
								<strong>Interaction:</strong> Drag to rotate • Scroll to zoom • Double-click to reset view
							</small>
						</div>
					</div>
				</div>
			<?php else : ?>
				<!-- Empty State -->
				<div class="wp3d-empty-state">
					<div class="wp3d-empty-content">
						<span class="dashicons dashicons-format-gallery"></span>
						<h3>No 3D Model Uploaded</h3>
						<p>Upload a GLB or GLTF file in the "3D Model File" section below to see the interactive preview here.</p>
						<div class="wp3d-supported-formats">
							<small><strong>Supported formats:</strong> .glb, .gltf</small>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
		
		<style>
			/* Modern Preview Container Styles */
			.wp3d-preview-container {
				margin: 0;
			}
			
			.wp3d-viewer-section {
				position: relative;
				box-shadow: 0 2px 12px rgba(0,0,0,0.1);
			}
			
			#wp3d-admin-preview-container {
				position: relative;
			}
			
			.wp3d-admin-preview {
				border-radius: 8px 8px 0 0;
				transition: all 0.3s ease;
			}
			
			/* Overlay Controls */
			.wp3d-overlay-controls {
				position: absolute;
				top: 15px;
				right: 15px;
				display: flex;
				gap: 8px;
				z-index: 10;
			}
			
			.wp3d-control-btn {
				background: rgba(255,255,255,0.9);
				border: 1px solid rgba(0,0,0,0.1);
				border-radius: 6px;
				width: 36px;
				height: 36px;
				display: flex;
				align-items: center;
				justify-content: center;
				cursor: pointer;
				transition: all 0.2s ease;
				backdrop-filter: blur(4px);
			}
			
			.wp3d-control-btn:hover {
				background: rgba(255,255,255,1);
				box-shadow: 0 2px 8px rgba(0,0,0,0.15);
				transform: translateY(-1px);
			}
			
			.wp3d-control-btn .dashicons {
				color: #555;
				font-size: 16px;
			}
			
			/* Settings Panel */
			.wp3d-settings-panel {
				background: #fff;
				padding: 20px;
				border-top: 1px solid #eee;
			}
			
			.wp3d-settings-grid {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
				gap: 20px;
				margin-bottom: 15px;
			}
			
			.wp3d-setting-group {
				display: flex;
				flex-direction: column;
				gap: 8px;
			}
			
			.wp3d-setting-group label {
				font-weight: 600;
				color: #333;
				font-size: 13px;
				text-transform: uppercase;
				letter-spacing: 0.5px;
			}
			
			.wp3d-color-input {
				display: flex;
				align-items: center;
				gap: 10px;
			}
			
			.wp3d-color-input input[type="color"] {
				width: 40px;
				height: 32px;
				border: 2px solid #ddd;
				border-radius: 6px;
				cursor: pointer;
			}
			
			.wp3d-color-value {
				font-family: monospace;
				background: #f5f5f5;
				padding: 4px 8px;
				border-radius: 4px;
				font-size: 11px;
			}
			
			.wp3d-setting-group input[type="text"] {
				padding: 8px 12px;
				border: 1px solid #ddd;
				border-radius: 4px;
				font-size: 13px;
			}
			
			.wp3d-setting-group small {
				color: #666;
				font-size: 11px;
			}
			
			/* Custom Checkboxes */
			.wp3d-checkboxes {
				gap: 4px;
			}
			
			.wp3d-checkbox-row {
				margin: 2px 0;
			}
			
			.wp3d-checkbox-label {
				display: flex;
				align-items: center;
				gap: 8px;
				cursor: pointer;
				font-size: 13px;
				font-weight: normal !important;
				text-transform: none !important;
				letter-spacing: normal !important;
			}
			
			.wp3d-checkbox-label input[type="checkbox"] {
				margin: 0;
			}
			
			/* Quick Actions */
			.wp3d-quick-actions {
				display: flex;
				gap: 8px;
				flex-wrap: wrap;
			}
			
			.wp3d-quick-actions .button {
				padding: 4px 12px;
				font-size: 11px;
			}
			
			/* Instructions */
			.wp3d-instructions {
				text-align: center;
				padding-top: 15px;
				border-top: 1px solid #f0f0f0;
				color: #666;
			}
			
			/* Loading and Error States */
			.wp3d-loading-state, .wp3d-error-state {
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: center;
				height: 100%;
				background: #f8f9fa;
				color: #666;
			}
			
			.wp3d-spinner {
				border: 3px solid #f3f3f3;
				border-radius: 50%;
				border-top: 3px solid #0073aa;
				width: 30px;
				height: 30px;
				animation: wp3d-spin 1s linear infinite;
				margin-bottom: 10px;
			}
			
			@keyframes wp3d-spin {
				0% { transform: rotate(0deg); }
				100% { transform: rotate(360deg); }
			}
			
			.wp3d-error-state .dashicons {
				font-size: 32px;
				color: #d63638;
				margin-bottom: 10px;
			}
			
			/* Empty State */
			.wp3d-empty-state {
				background: #f8f9fa;
				border: 2px dashed #ddd;
				border-radius: 8px;
				padding: 60px 20px;
				text-align: center;
			}
			
			.wp3d-empty-content .dashicons {
				font-size: 48px;
				color: #ccc;
				margin-bottom: 15px;
			}
			
			.wp3d-empty-content h3 {
				color: #333;
				margin: 0 0 10px 0;
				font-size: 18px;
			}
			
			.wp3d-empty-content p {
				color: #666;
				margin: 0 0 15px 0;
				line-height: 1.5;
			}
			
			.wp3d-supported-formats {
				background: #fff;
				border: 1px solid #ddd;
				border-radius: 4px;
				padding: 8px 12px;
				display: inline-block;
			}
			
			/* Responsive */
			@media (max-width: 768px) {
				.wp3d-settings-grid {
					grid-template-columns: 1fr;
					gap: 15px;
				}
				
				.wp3d-overlay-controls {
					top: 10px;
					right: 10px;
				}
				
				.wp3d-control-btn {
					width: 32px;
					height: 32px;
				}
			}
		</style>
		<?php
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
	/**
	 * Model settings metabox callback.
	 *
	 * @since    1.0.0
	 * @param    WP_Post    $post    Post object.
	 */
	public function model_settings_metabox_callback( $post ) {
		
		// Get current values for advanced settings
		$zoom_level = get_post_meta( $post->ID, '_wp3d_zoom_level', true );
		$loading_behavior = get_post_meta( $post->ID, '_wp3d_loading', true );
		$ar_modes = get_post_meta( $post->ID, '_wp3d_ar_modes', true );
		$ios_src = get_post_meta( $post->ID, '_wp3d_ios_src', true );

		// Set defaults
		if ( empty( $zoom_level ) ) $zoom_level = '1';
		if ( empty( $loading_behavior ) ) $loading_behavior = 'auto';
		if ( empty( $ar_modes ) ) $ar_modes = 'webxr scene-viewer quick-look';

		?>
		<div class="wp3d-advanced-settings">
			<p class="description" style="margin-bottom: 20px; padding: 10px; background: #f0f8ff; border-left: 4px solid #0073aa;">
				<strong>💡 Quick Settings:</strong> Use the preview panel above for common settings like background color, rotation, and controls. These are advanced settings for fine-tuning.
			</p>
			
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="wp3d_zoom_level"><?php _e( 'Initial Zoom Level', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="range" id="wp3d_zoom_level" name="wp3d_zoom_level" value="<?php echo esc_attr( $zoom_level ); ?>" min="0.1" max="5" step="0.1" style="width: 200px;" />
						<span class="wp3d-zoom-value"><?php echo esc_html( $zoom_level ); ?>x</span>
						<p class="description"><?php _e( 'Fine-tune the initial zoom level for the 3D model viewer', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wp3d_loading"><?php _e( 'Loading Behavior', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<select id="wp3d_loading" name="wp3d_loading">
							<option value="auto" <?php selected( $loading_behavior, 'auto' ); ?>><?php _e( 'Auto (default)', 'wp-3d-model-viewer' ); ?></option>
							<option value="lazy" <?php selected( $loading_behavior, 'lazy' ); ?>><?php _e( 'Lazy loading', 'wp-3d-model-viewer' ); ?></option>
							<option value="eager" <?php selected( $loading_behavior, 'eager' ); ?>><?php _e( 'Eager loading', 'wp-3d-model-viewer' ); ?></option>
						</select>
						<p class="description"><?php _e( 'Controls when the 3D model starts loading', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wp3d_ar_modes"><?php _e( 'AR Modes', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="text" id="wp3d_ar_modes" name="wp3d_ar_modes" value="<?php echo esc_attr( $ar_modes ); ?>" class="regular-text" />
						<p class="description"><?php _e( 'AR modes for different platforms. Default: webxr scene-viewer quick-look', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="wp3d_ios_src"><?php _e( 'iOS-Specific Model', 'wp-3d-model-viewer' ); ?></label>
					</th>
					<td>
						<input type="url" id="wp3d_ios_src" name="wp3d_ios_src" value="<?php echo esc_attr( $ios_src ); ?>" class="regular-text" placeholder="https://example.com/model-ios.usdz" />
						<p class="description"><?php _e( 'Optional: Different model file for iOS AR Quick Look (USDZ format recommended)', 'wp-3d-model-viewer' ); ?></p>
					</td>
				</tr>
			</table>

			<div class="wp3d-settings-note" style="margin-top: 20px; padding: 15px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px;">
				<h4 style="margin-top: 0;"><?php _e( 'Settings Priority', 'wp-3d-model-viewer' ); ?></h4>
				<ul style="margin-bottom: 0;">
					<li><strong><?php _e( 'Preview Panel:', 'wp-3d-model-viewer' ); ?></strong> <?php _e( 'Background color, camera position, auto-rotate, controls, AR toggle', 'wp-3d-model-viewer' ); ?></li>
					<li><strong><?php _e( 'Advanced Settings:', 'wp-3d-model-viewer' ); ?></strong> <?php _e( 'Zoom level, loading behavior, AR modes, iOS-specific files', 'wp-3d-model-viewer' ); ?></li>
				</ul>
			</div>
		</div>

		<script>
		jQuery(document).ready(function($) {
			// Update zoom level display
			$('#wp3d_zoom_level').on('input', function() {
				$('.wp3d-zoom-value').text($(this).val() + 'x');
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
		$start_rotation = isset( $_POST['wp3d_start_rotation'] ) ? sanitize_text_field( $_POST['wp3d_start_rotation'] ) : '0deg 0deg 0deg';
		$zoom_level = isset( $_POST['wp3d_zoom_level'] ) ? floatval( $_POST['wp3d_zoom_level'] ) : 1;
		$ar_enabled = isset( $_POST['wp3d_ar_enabled'] ) ? '1' : '0';
		$auto_rotate = isset( $_POST['wp3d_auto_rotate'] ) ? '1' : '0';
		$camera_controls = isset( $_POST['wp3d_camera_controls'] ) ? '1' : '0';

		update_post_meta( $post_id, '_wp3d_bg_color', $bg_color );
		update_post_meta( $post_id, '_wp3d_start_rotation', $start_rotation );
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
			wp_enqueue_script( 'wp3d-admin-cpt', plugin_dir_url( __FILE__ ) . '../admin/js/wp-3d-model-viewer-cpt.js', array( 'jquery' ), '1.0.0', true );
		}
	}

}
