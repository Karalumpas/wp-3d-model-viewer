<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/admin/partials
 */

// Get plugin settings
$admin = new WP_3D_Model_Viewer_Admin( 'wp-3d-model-viewer', WP_3D_MODEL_VIEWER_VERSION );
$settings = $admin->get_settings();
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <?php if ( isset( $_GET['settings-updated'] ) && $_GET['settings-updated'] ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e( 'Settings saved successfully!', 'wp-3d-model-viewer' ); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo admin_url( 'admin-post.php' ); ?>">
        <?php wp_nonce_field( 'wp_3d_model_viewer_save_settings', 'wp_3d_model_viewer_nonce' ); ?>
        <input type="hidden" name="action" value="wp_3d_model_viewer_save_settings">

        <table class="form-table" role="presentation">
            <tbody>
                
                <!-- Default Dimensions -->
                <tr>
                    <th scope="row">
                        <label for="default_width"><?php _e( 'Default Width', 'wp-3d-model-viewer' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="default_width" 
                               name="default_width" 
                               value="<?php echo esc_attr( $settings['default_width'] ); ?>" 
                               class="regular-text" />
                        <p class="description"><?php _e( 'Default width for 3D model viewers (e.g., 100%, 800px, 50em)', 'wp-3d-model-viewer' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="default_height"><?php _e( 'Default Height', 'wp-3d-model-viewer' ); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="default_height" 
                               name="default_height" 
                               value="<?php echo esc_attr( $settings['default_height'] ); ?>" 
                               class="regular-text" />
                        <p class="description"><?php _e( 'Default height for 3D model viewers (e.g., 400px, 50vh, 30em)', 'wp-3d-model-viewer' ); ?></p>
                    </td>
                </tr>

                <!-- Appearance -->
                <tr>
                    <th scope="row">
                        <label for="default_background_color"><?php _e( 'Default Background Color', 'wp-3d-model-viewer' ); ?></label>
                    </th>
                    <td>
                        <input type="color" 
                               id="default_background_color" 
                               name="default_background_color" 
                               value="<?php echo esc_attr( $settings['default_background_color'] ); ?>" />
                        <p class="description"><?php _e( 'Default background color for 3D model viewers', 'wp-3d-model-viewer' ); ?></p>
                    </td>
                </tr>

                <!-- Default Behavior -->
                <tr>
                    <th scope="row"><?php _e( 'Default Behavior', 'wp-3d-model-viewer' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Default Behavior', 'wp-3d-model-viewer' ); ?></span></legend>
                            
                            <label for="enable_camera_controls_by_default">
                                <input type="checkbox" 
                                       id="enable_camera_controls_by_default" 
                                       name="enable_camera_controls_by_default" 
                                       value="1" 
                                       <?php checked( $settings['enable_camera_controls_by_default'] ); ?> />
                                <?php _e( 'Enable camera controls by default', 'wp-3d-model-viewer' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Allow users to rotate, zoom, and pan the 3D model', 'wp-3d-model-viewer' ); ?></p>
                            <br>

                            <label for="enable_auto_rotate_by_default">
                                <input type="checkbox" 
                                       id="enable_auto_rotate_by_default" 
                                       name="enable_auto_rotate_by_default" 
                                       value="1" 
                                       <?php checked( $settings['enable_auto_rotate_by_default'] ); ?> />
                                <?php _e( 'Enable auto rotation by default', 'wp-3d-model-viewer' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Automatically rotate the 3D model continuously', 'wp-3d-model-viewer' ); ?></p>
                            <br>

                            <label for="enable_ar_by_default">
                                <input type="checkbox" 
                                       id="enable_ar_by_default" 
                                       name="enable_ar_by_default" 
                                       value="1" 
                                       <?php checked( $settings['enable_ar_by_default'] ); ?> />
                                <?php _e( 'Enable AR support by default', 'wp-3d-model-viewer' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Enable augmented reality viewing on compatible devices', 'wp-3d-model-viewer' ); ?></p>
                        </fieldset>
                    </td>
                </tr>

                <!-- File Upload Settings -->
                <tr>
                    <th scope="row">
                        <label for="max_file_size"><?php _e( 'Maximum File Size', 'wp-3d-model-viewer' ); ?></label>
                    </th>
                    <td>
                        <input type="number" 
                               id="max_file_size" 
                               name="max_file_size" 
                               value="<?php echo esc_attr( $settings['max_file_size'] ); ?>" 
                               min="1048576" 
                               max="104857600" 
                               step="1048576" 
                               class="regular-text" />
                        <p class="description"><?php _e( 'Maximum file size for 3D model uploads in bytes (1MB = 1048576 bytes)', 'wp-3d-model-viewer' ); ?></p>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e( 'Allowed File Types', 'wp-3d-model-viewer' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Allowed File Types', 'wp-3d-model-viewer' ); ?></span></legend>
                            
                            <?php
                            $file_types = array(
                                'gltf' => 'GLTF (.gltf)',
                                'glb'  => 'GLB (.glb)',
                                'obj'  => 'OBJ (.obj)',
                                'fbx'  => 'FBX (.fbx)',
                                'dae'  => 'DAE (.dae)',
                                'usdz' => 'USDZ (.usdz - for iOS AR)',
                            );
                            
                            foreach ( $file_types as $ext => $label ) :
                            ?>
                                <label for="allowed_type_<?php echo esc_attr( $ext ); ?>">
                                    <input type="checkbox" 
                                           id="allowed_type_<?php echo esc_attr( $ext ); ?>" 
                                           name="allowed_file_types[]" 
                                           value="<?php echo esc_attr( $ext ); ?>" 
                                           <?php checked( in_array( $ext, $settings['allowed_file_types'] ) ); ?> />
                                    <?php echo esc_html( $label ); ?>
                                </label>
                                <br>
                            <?php endforeach; ?>
                            
                            <p class="description"><?php _e( 'Select which 3D model file formats are allowed for upload', 'wp-3d-model-viewer' ); ?></p>
                        </fieldset>
                    </td>
                </tr>

                <!-- Performance Settings -->
                <tr>
                    <th scope="row"><?php _e( 'Performance Optimization', 'wp-3d-model-viewer' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><span><?php _e( 'Performance Optimization', 'wp-3d-model-viewer' ); ?></span></legend>
                            
                            <label for="lazy_loading">
                                <input type="checkbox" 
                                       id="lazy_loading" 
                                       name="lazy_loading" 
                                       value="1" 
                                       <?php checked( $settings['lazy_loading'] ); ?> />
                                <?php _e( 'Enable lazy loading', 'wp-3d-model-viewer' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Load 3D models only when they come into view', 'wp-3d-model-viewer' ); ?></p>
                            <br>

                            <label for="compression_enabled">
                                <input type="checkbox" 
                                       id="compression_enabled" 
                                       name="compression_enabled" 
                                       value="1" 
                                       <?php checked( $settings['compression_enabled'] ); ?> />
                                <?php _e( 'Enable Draco compression support', 'wp-3d-model-viewer' ); ?>
                            </label>
                            <p class="description"><?php _e( 'Support for compressed GLTF models (reduces file size)', 'wp-3d-model-viewer' ); ?></p>
                        </fieldset>
                    </td>
                </tr>

            </tbody>
        </table>

        <?php submit_button( __( 'Save Settings', 'wp-3d-model-viewer' ) ); ?>
    </form>

    <!-- Documentation Section -->
    <div class="wp3d-documentation" style="margin-top: 40px; padding: 20px; background: #f9f9f9; border-left: 4px solid #0073aa;">
        <h2><?php _e( 'Usage Instructions', 'wp-3d-model-viewer' ); ?></h2>
        
        <h3><?php _e( 'Available Shortcodes', 'wp-3d-model-viewer' ); ?></h3>
        <p><?php _e( 'This plugin provides three shortcode options for displaying 3D models:', 'wp-3d-model-viewer' ); ?></p>
        
        <h4><?php _e( '1. Custom Post Type Models (Recommended)', 'wp-3d-model-viewer' ); ?></h4>
        <code>[model_viewer id="123"]</code>
        <p><?php _e( 'Uses 3D models created in WordPress admin with preset configurations.', 'wp-3d-model-viewer' ); ?></p>
        
        <h4><?php _e( '2. Direct Model URLs', 'wp-3d-model-viewer' ); ?></h4>
        <code>[3d_model_viewer src="path/to/model.glb" width="100%" height="400px"]</code>
        <p><?php _e( 'For direct model embedding with inline configuration.', 'wp-3d-model-viewer' ); ?></p>
        
        <h4><?php _e( '3. Alternative Syntax', 'wp-3d-model-viewer' ); ?></h4>
        <code>[3d_model id="123"]</code>
        <p><?php _e( 'Alternative syntax for CPT models.', 'wp-3d-model-viewer' ); ?></p>
        
        <h3><?php _e( 'Available Parameters', 'wp-3d-model-viewer' ); ?></h3>
        <ul>
            <li><strong>id</strong> - <?php _e( '3D Model post ID (for CPT models)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>src</strong> - <?php _e( 'URL to the 3D model file (required for direct URLs)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>width</strong> - <?php _e( 'Viewer width (default: 100%)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>height</strong> - <?php _e( 'Viewer height (default: 400px)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>background_color</strong> - <?php _e( 'Background color (default: #ffffff)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>auto_rotate</strong> - <?php _e( 'Enable auto rotation (true/false)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>camera_controls</strong> - <?php _e( 'Enable camera controls (true/false)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>ar</strong> - <?php _e( 'Enable AR support (true/false)', 'wp-3d-model-viewer' ); ?></li>
            <li><strong>ios_src</strong> - <?php _e( 'USDZ file URL for iOS AR support', 'wp-3d-model-viewer' ); ?></li>
        </ul>

        <h3><?php _e( 'Enhanced Features', 'wp-3d-model-viewer' ); ?></h3>
        <ul>
            <li><?php _e( 'Google model-viewer library with ES6 modules', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Interactive 3D model preview in admin with rotation capture', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Visual camera position controls - no more guessing rotation values!', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Async loading for better performance', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Progressive loading with poster images', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'WebXR and AR Quick Look support', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Responsive design with fallback content', 'wp-3d-model-viewer' ); ?></li>
        </ul>

        <h3><?php _e( 'NEW: Interactive Camera Position Setup', 'wp-3d-model-viewer' ); ?></h3>
        <p><?php _e( 'When editing a 3D model, you can now:', 'wp-3d-model-viewer' ); ?></p>
        <ul>
            <li><?php _e( 'See a live preview of your 3D model in the admin', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Rotate, zoom, and position the model visually', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Click "Capture Current Position" to save the perfect view', 'wp-3d-model-viewer' ); ?></li>
            <li><?php _e( 'Your visitors will see the model from your chosen angle', 'wp-3d-model-viewer' ); ?></li>
        </ul>

        <h3><?php _e( 'Complete AR Example', 'wp-3d-model-viewer' ); ?></h3>
        <code>[model_viewer id="123"]</code>
        <p><?php _e( 'OR for manual configuration:', 'wp-3d-model-viewer' ); ?></p>
        <code>[3d_model_viewer src="model.glb" ios_src="model.usdz" ar="true" poster="poster.jpg" camera_controls="true"]</code>
    </div>
</div>
