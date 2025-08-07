# WP 3D Model Viewer - AI Coding Agent Instructions

## Project Overview
WordPress plugin for displaying interactive 3D models using Google's model-viewer library. Supports GLTF/GLB formats with AR capabilities, custom post types, and shortcode integration.

## Architecture Patterns

### WordPress Plugin Boilerplate Structure
- **Entry Point**: `wp-3d-model-viewer.php` - Main plugin file with WordPress headers and bootstrapping
- **Core Orchestrator**: `includes/class-wp-3d-model-viewer.php` - Registers all hooks via the Loader pattern
- **Hook Loader**: `includes/class-wp-3d-model-viewer-loader.php` - Centralizes WordPress hook registration
- **Admin/Public Separation**: Distinct `admin/` and `public/` directories with their own classes

### Multi-Component Hook System
```php
// Hook registration pattern used throughout
$this->loader->add_action( 'hook_name', $instance, 'method_name' );
$this->loader->add_filter( 'filter_name', $instance, 'method_name', $priority, $args );
```

### Custom Post Type Integration
- **Post Type**: `wp_3d_model` (changed from `3d_model` for WordPress compatibility)
- **Metaboxes**: Complex metabox system in `class-wp-3d-model-viewer-cpt.php` with 11+ custom settings
- **Admin Columns**: Custom columns for model preview and file info

## Critical Development Patterns

### JavaScript Module Loading
Model-viewer library MUST be loaded as ES6 module:
```php
// In WP_3D_Model_Viewer_Public::enqueue_scripts()
wp_enqueue_script('model-viewer', 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js');
add_filter('script_loader_tag', array($this, 'add_module_attribute'));
```

### Shortcode System
Three shortcode aliases all use single handler:
```php
add_shortcode('3d_model_viewer', array($this, 'render_shortcode'));
add_shortcode('3d_model', array($this, 'render_shortcode'));
add_shortcode('model_viewer', array($this, 'render_shortcode'));
```

### Dynamic CSS Generation
Settings generate CSS dynamically in `render_shortcode()`:
```php
// Border styling pattern
if ($show_border === '1') {
    $custom_css .= "border: {$border_width}px solid {$border_color};";
}
```

### PHP Compatibility Considerations
- **Avoid `?:` operator** - Use `empty()` checks instead for PHP 7.4 compatibility
- **WordPress naming** - Post types must start with letters, not numbers
- **Hook naming** - Use `manage_{post_type}_posts_columns` pattern

## Key Integration Points

### Model-Viewer Library Dependency
- **CDN**: Google's unpkg.com CDN for model-viewer
- **Version**: Fixed at 3.5.0
- **Loading**: Must load in `<head>` with `type="module"`
- **Fallback**: Progressive enhancement with fallback content

### AR (Augmented Reality) Support
- **iOS**: Requires USDZ format, uses AR Quick Look
- **Android**: Uses Scene Viewer with GLTF/GLB
- **Web**: WebXR for desktop browsers

### WordPress Media Integration
```javascript
// Media uploader pattern in admin/js/wp-3d-model-viewer-cpt.js
wp.media.frames.file_frame = wp.media({
    title: 'Select 3D Model',
    multiple: false
});
```

## CSS Architecture

### BEM-like Component Naming
```css
.wp3d-viewer               /* Main container */
.wp3d-cpt-model           /* CPT-specific styling */
.wp3d-has-label           /* State modifier */
.wp3d-shadow-intensity-3  /* Utility class */
```

### Dynamic Shadow System
```css
.wp3d-shadow-intensity-1 { box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
.wp3d-shadow-intensity-10 { box-shadow: 0 20px 40px rgba(0,0,0,0.4); }
```

### Responsive AR Controls
Position-based classes for AR/label positioning:
```css
.wp3d-ar-top-left { top: 10px; left: 10px; }
.wp3d-label-bottom-right { bottom: 10px; right: 10px; }
```

## Development Workflows

### Plugin Packaging
```powershell
# Create deployment package (Windows)
powershell -command "Compress-Archive -Path '.\*' -DestinationPath '.\wp-3d-model-viewer-v{version}.zip' -Force"
```

### Debugging 3D Models
1. **Enable WP_DEBUG** for detailed error output
2. **Check browser console** for model-viewer errors
3. **Test with known-good model**: `https://modelviewer.dev/shared-assets/models/Astronaut.glb`
4. **CORS issues**: Add headers for .glb/.gltf files

### Admin Preview System
Real-time preview in `model_preview_metabox_callback()` updates via JavaScript:
```javascript
// Live preview updates in admin
viewer.style.backgroundColor = newColor;
viewer.setAttribute('camera-orbit', newOrbit);
```

## File Upload Constraints
- **Formats**: GLB (preferred), GLTF, OBJ, FBX, DAE, USDZ
- **Size**: 50MB recommended maximum
- **MIME Types**: Custom MIME type registration for 3D formats
- **WordPress Upload**: Uses standard WordPress media handling

## Performance Considerations
- **Lazy Loading**: Conditional script loading only on pages with 3D content
- **CDN Dependency**: External model-viewer library
- **File Size**: Large 3D models impact loading performance
- **Progressive Enhancement**: Fallback content for unsupported browsers

## Testing Patterns
- **Cross-browser**: Chrome, Safari, Edge (model-viewer support)
- **Mobile**: AR functionality on iOS/Android devices
- **File Formats**: Test with multiple 3D formats
- **WordPress Versions**: 5.0+ compatibility required
