# WordPress 3D Model Viewer Plugin - Settings API Implementation

## Overview
This plugin now includes a comprehensive WordPress Settings API integration that provides administrators with a user-friendly settings page under **Settings → 3D Viewer**.

## Settings Page Features

### General Settings
- **Default Background Color**: Color picker for setting the default background color of 3D model viewers
- **Default Zoom Level**: Range slider (0.1x to 5.0x) with real-time preview
- **Default Width**: Text field for setting default viewer width (supports %, px, em units)
- **Default Height**: Text field for setting default viewer height (supports px, vh, em units)

### File Upload Settings
- **Allowed MIME Types**: Checkbox selections for supported 3D model formats:
  - GLTF (.gltf) - GL Transmission Format (JSON variant)
  - GLB (.glb) - GL Transmission Format (Binary variant)  
  - OBJ (.obj) - Wavefront OBJ (requires MTL file)
  - FBX (.fbx) - Autodesk FBX format
  - DAE (.dae) - COLLADA Digital Asset Exchange
  - USDZ (.usdz) - USD (iOS AR format)
  - USD (.usd) - Universal Scene Description

- **Maximum File Size**: Number input (1-100 MB) for upload size limits

### Advanced Settings
- **Enable Debugging**: Checkbox to enable detailed logging and debugging information
- **Enable Lazy Loading**: Checkbox to load models only when they come into view (recommended for performance)
- **Enable AR by Default**: Checkbox to enable augmented reality viewing on compatible devices

## Implementation Details

### WordPress Standards Compliance
- **Settings API**: Proper use of `register_setting()`, `add_settings_section()`, and `add_settings_field()`
- **Security**: All inputs are sanitized and validated using WordPress functions
- **Internationalization**: All strings are wrapped with `__()` and `_e()` for translation support
- **Nonces**: Form security handled automatically by WordPress Settings API

### Key Files Modified

#### `admin/class-wp-3d-model-viewer-admin.php`
- Added `admin_init()` method for Settings API registration
- Added `add_options_page()` method to create admin menu entry
- Added `display_options_page()` method for settings page HTML output
- Added section callbacks: `general_section_callback()`, `mime_section_callback()`, `advanced_section_callback()`
- Added field callbacks for all form elements with proper sanitization
- Added `validate_options()` method for comprehensive input validation

#### `includes/class-wp-3d-model-viewer.php`
- Added `admin_init` and `admin_menu` hook registrations in `define_admin_hooks()`

#### `admin/css/wp-3d-model-viewer-admin.css`
- Enhanced styling for settings page components
- Responsive design for mobile compatibility
- Professional styling for form elements and help sections

## Usage Examples Section
The settings page includes a helpful "Usage Examples" section that shows administrators how to use the various shortcode formats:

```php
// Display a specific 3D model by ID
[model_viewer id="123"]
[3d_model id="123"] 
[3d_model_viewer id="123"]

// Display with custom settings
[model_viewer id="123" background-color="#ff0000" camera-orbit="45deg 75deg auto"]

// Display from external URL
[3d_model_viewer src="https://example.com/model.glb" width="800px" height="600px"]

// Enable AR viewing
[model_viewer id="123" ar ar-modes="webxr scene-viewer quick-look"]
```

## Settings Storage
All settings are stored in the `wp_3d_model_viewer_options` option in the WordPress database as a serialized array. The settings include:

- `default_background_color` (hex color)
- `default_zoom_level` (float 0.1-5.0)
- `default_width` (string with units)
- `default_height` (string with units)  
- `allowed_mime_types` (array of MIME type strings)
- `max_file_size` (integer 1-100)
- `enable_debugging` (0 or 1)
- `lazy_loading` (0 or 1)
- `enable_ar_by_default` (0 or 1)

## Security Features
- All settings are validated and sanitized
- Hex color validation for color picker inputs
- Numeric range validation for zoom and file size
- MIME type whitelist validation
- Checkbox values properly handled as 0/1

## Accessibility
- Proper form labels and descriptions
- Semantic HTML structure
- Screen reader friendly field descriptions
- Keyboard navigation support

This implementation provides a professional, user-friendly settings interface that follows WordPress best practices and coding standards.
