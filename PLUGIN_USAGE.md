# WP 3D Model Viewer Plugin - Usage Guide

## Overview
This WordPress plugin uses **Google's model-viewer** library to display 3D models in your WordPress website. It supports GLB, GLTF, and other 3D model formats with AR (Augmented Reality) capabilities.

## Installation
1. Upload `wp-3d-model-viewer.zip` to WordPress via `Plugins` → `Add New` → `Upload Plugin`
2. Activate the plugin
3. You're ready to use 3D models!

## Admin Interface

### **🎯 NEW: Live 3D Model Preview**
When editing a 3D model in WordPress admin, you'll see:

1. **3D Model Preview** (Top metabox) - Live interactive preview of your model
2. **3D Model File** - Upload your GLB/GLTF files  
3. **3D Model Settings** - Configure viewer options
4. **Shortcode Usage** - Copy shortcode for use in posts/pages

### **Interactive Preview Controls**
- **Drag to rotate** the model
- **Scroll to zoom** in and out
- **Refresh Preview** button to reload the model
- **Reset Camera** button to return to default view
- **Auto-rotation** for better viewing

## Usage

### Basic Shortcode
```
[3d_model_viewer src="https://example.com/model.glb"]
```

### Advanced Shortcode with Options
```
[3d_model_viewer 
    src="https://example.com/model.glb" 
    width="800px" 
    height="600px" 
    auto_rotate="true" 
    camera_controls="true" 
    ar="true" 
    background_color="#f0f0f0"
    poster="https://example.com/poster.jpg"
    alt="My 3D Model"]
```

### Using Custom Post Type
```
[3d_model_viewer id="123"]
```

## Shortcode Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `src` | URL | *required* | URL to 3D model file (.glb, .gltf) |
| `id` | Number | - | Custom Post Type ID for stored 3D models |
| `width` | String | `100%` | Viewer width (px, %, em, etc.) |
| `height` | String | `400px` | Viewer height (px, %, em, etc.) |
| `auto_rotate` | Boolean | `false` | Enable automatic rotation |
| `camera_controls` | Boolean | `true` | Enable mouse/touch controls |
| `ar` | Boolean | `false` | Enable AR support |
| `ar_modes` | String | `webxr scene-viewer quick-look` | AR modes |
| `ios_src` | URL | - | iOS-specific model file |
| `background_color` | Color | `#ffffff` | Background color |
| `poster` | URL | - | Loading poster image |
| `alt` | String | - | Alt text for accessibility |
| `loading` | String | `auto` | Loading behavior |
| `class` | String | - | Additional CSS classes |
| `viewer_id` | String | auto-generated | HTML element ID |

## Features

### ✅ Google Model-Viewer Integration
- Latest model-viewer library (v3.5.0)
- Full 3D model support (GLB, GLTF)
- Automatic script loading with module support

### ✅ Admin Preview System  
- **Live preview** in WordPress admin
- **Interactive controls** - rotate, zoom, examine models
- **Auto-refresh** when files are updated
- **Error handling** with clear feedback
- **Responsive design** for all screen sizes

### ✅ AR Support
- WebXR support
- iOS Quick Look
- Android Scene Viewer

### ✅ WordPress Integration
- Custom Post Type for 3D models
- Gutenberg block support
- Shortcode support
- Admin settings page

### ✅ Performance Features
- Lazy loading
- Progressive enhancement
- Responsive design
- Loading states and error handling

## Admin Workflow

### Adding a New 3D Model

1. **Go to WordPress Admin** → **3D Models** → **Add New**
2. **Enter a title** for your 3D model
3. **Upload model file** in the "3D Model File" section
4. **Preview appears automatically** at the top - interact with your model!
5. **Configure settings** like background color, camera controls, AR
6. **Copy the shortcode** and use it in posts/pages

### The Preview System

The **3D Model Preview** metabox shows at the top and provides:
- **Real-time 3D preview** of uploaded models
- **Interactive controls** for testing the model
- **Loading animations** and error handling
- **Refresh/Reset buttons** for preview control
- **Visual instructions** for users

## File Format Support
- **GLB** (recommended) - Binary GLTF format
- **GLTF** - Text-based GLTF format
- **FBX** - Via conversion to GLB/GLTF
- **OBJ** - Via conversion to GLB/GLTF

## Browser Support
- Chrome 67+
- Firefox 60+
- Safari 12+
- Edge 79+
- Mobile browsers with WebGL support

## Troubleshooting

### Model Not Loading in Admin Preview?
1. Check that the model file is properly uploaded
2. Verify file format is GLB or GLTF
3. Use the "Refresh Preview" button
4. Check browser console for errors

### Model Not Loading on Frontend?
1. Check that the model URL is accessible
2. Verify file format is GLB or GLTF
3. Check browser console for errors
4. Ensure HTTPS for AR features

### Performance Issues?
1. Optimize model file size (< 5MB recommended)
2. Use GLB format for better compression
3. Enable lazy loading
4. Use poster images for faster initial load

## Examples

### Simple Product Display
```
[3d_model_viewer src="/wp-content/uploads/product.glb" height="500px" auto_rotate="true"]
```

### AR-Enabled Model
```
[3d_model_viewer 
    src="/wp-content/uploads/chair.glb" 
    ar="true" 
    ar_modes="webxr scene-viewer quick-look"
    poster="/wp-content/uploads/chair-poster.jpg"]
```

### Using Custom Post Type
```
[3d_model_viewer id="15" width="100%" height="600px"]
```

### Custom Styled Viewer
```
[3d_model_viewer 
    src="/wp-content/uploads/sculpture.glb" 
    width="100%" 
    height="400px" 
    background_color="#000000"
    class="custom-viewer"
    camera_controls="true"]
```

## CSS Customization

### Basic Styling
```css
.wp3d-viewer {
    border-radius: 15px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

.wp3d-viewer:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
}
```

### Custom Loading Animation
```css
.wp3d-loading {
    position: relative;
}

.wp3d-loading::after {
    content: "Loading 3D Model...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: rgba(0,0,0,0.8);
    color: white;
    padding: 10px 20px;
    border-radius: 4px;
}
```

## Support
For issues and questions, please check:
1. WordPress debug log
2. Browser console for JavaScript errors
3. Model file accessibility
4. Plugin compatibility

---

**Note**: This plugin requires WordPress 5.0+ and modern browsers with WebGL support.
