# WP 3D Model Viewer

[![Latest Stable Version](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/v/stable)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![Total Downloads](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/downloads)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![License](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/license)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![PHP Version Require](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/require/php)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![WordPress tested](https://img.shields.io/badge/WordPress-tested%206.4+-green.svg)](https://wordpress.org/)
[![Build Status](https://img.shields.io/github/actions/workflow/status/Karalumpas/wp-3d-model-viewer/ci.yml?branch=main)](https://github.com/Karalumpas/wp-3d-model-viewer/actions)
[![Code Coverage](https://codecov.io/gh/Karalumpas/wp-3d-model-viewer/branch/main/graph/badge.svg)](https://codecov.io/gh/Karalumpas/wp-3d-model-viewer)

A powerful WordPress plugin for displaying interactive 3D models in the browser with WebGL technology, supporting GLTF, GLB, OBJ, and other popular 3D formats.

![Plugin Screenshot Placeholder](docs/images/main-interface.png)

## Purpose

WP 3D Model Viewer bridges the gap between 3D content creation and web presentation, allowing WordPress site owners to:

- **Showcase Products**: Perfect for e-commerce sites selling 3D-printable items, furniture, jewelry, or any physical products
- **Educational Content**: Display anatomical models, architectural structures, or scientific visualizations
- **Portfolio Presentation**: Artists and designers can showcase their 3D work directly on their websites
- **Interactive Experiences**: Create engaging user experiences with interactive 3D content
- **AR-Ready Content**: Prepare your 3D models for augmented reality experiences on mobile devices

## Features

- 🎯 **Easy Integration** - Simple shortcode and Gutenberg block support
- 📱 **Responsive Design** - Optimized for desktop, tablet, and mobile devices
- 🎮 **Interactive Controls** - Mouse/touch controls for rotation, zoom, and pan
- 🎨 **Customizable Appearance** - Control lighting, background, and viewer dimensions
- 📦 **Multiple Format Support** - GLTF, GLB, OBJ, FBX, and more
- 🔍 **Performance Optimized** - Efficient loading and rendering with WebGL
- 🌐 **Cross-Browser Compatible** - Works on all modern browsers
- 📱 **AR Support** - WebXR integration for augmented reality experiences
- ⚡ **Lazy Loading** - Models load only when needed to improve page performance
- 🎛️ **Admin Controls** - Easy model management through WordPress admin interface
## Screenshots

![Main Interface](docs/screenshots/main-interface.png)
*Main 3D model viewer interface showing interactive controls*

![Admin Settings](docs/screenshots/admin-settings.png)
*WordPress admin panel configuration options*

![Gutenberg Block](docs/screenshots/gutenberg-block.png)
*3D Model Viewer block in the Gutenberg editor*

![Mobile View](docs/screenshots/mobile-view.png)
*Responsive design on mobile devices*

![AR Mode](docs/screenshots/ar-mode.png)
*Augmented Reality mode on compatible devices*

## Installation

### Method 1: WordPress Plugin Directory (Recommended)

1. Navigate to **Plugins > Add New** in your WordPress admin
2. Search for "WP 3D Model Viewer"
3. Click **Install Now** and then **Activate**
4. Go to **Settings > 3D Model Viewer** to configure

### Method 2: Manual Upload

1. Download the latest version from [GitHub Releases](https://github.com/Karalumpas/wp-3d-model-viewer/releases)
2. Upload the ZIP file via **Plugins > Add New > Upload Plugin**
3. Activate the plugin
4. Configure settings in **Settings > 3D Model Viewer**

### Method 3: FTP Installation

1. Download and extract the plugin files
2. Upload the `wp-3d-model-viewer` folder to `/wp-content/plugins/`
3. Activate through the WordPress admin
4. Configure plugin settings

### Method 4: Composer (For Developers)

```bash
composer require karalumpas/wp-3d-model-viewer
```

## Shortcode Usage

The plugin provides multiple shortcode options for displaying 3D models:

### 1. Custom Post Type Models (Recommended)

Create and manage 3D models through the WordPress admin interface using the dedicated 3D Models post type:

```php
[model_viewer id="123"]
```

**Alternative syntax:**
```php
[3d_model id="123"]
```

**Benefits:**
- Visual model management through WordPress admin
- Preset configurations saved per model
- Thumbnail previews in admin list table
- Pre-configured AR settings, camera controls, and styling
- Easy copy-paste shortcode generation
- File management through WordPress Media Library
- Enhanced loading states with poster images
- Progressive enhancement with fallback content

### 2. Direct Model URLs (Advanced)

For direct model embedding with inline configuration:

### Basic Usage

```php
[3d_model_viewer src="https://example.com/model.glb"]
```

### Advanced Usage with All Parameters

```php
[3d_model_viewer 
    src="https://example.com/model.glb"
    width="800px"
    height="600px"
    background_color="#f0f0f0"
    auto_rotate="true"
    camera_controls="true"
    loading="lazy"
    poster="https://example.com/poster.jpg"
    alt="3D model description"
    ar="true"
    ar_modes="webxr scene-viewer quick-look"
    ios_src="https://example.com/model.usdz"
]
```

### Parameter Reference

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `src` | string | required | URL to the 3D model file (GLTF, GLB, etc.) |
| `width` | string | "100%" | Viewer width (px, %, em, rem) |
| `height` | string | "400px" | Viewer height (px, %, em, rem) |
| `background_color` | string | "#ffffff" | Background color (hex, rgb, rgba) |
| `auto_rotate` | boolean | false | Enable automatic rotation |
| `camera_controls` | boolean | true | Enable user camera controls |
| `loading` | string | "auto" | Loading behavior (auto, lazy, eager) |
| `poster` | string | "" | Placeholder image URL |
| `alt` | string | "" | Alt text for accessibility |
| `ar` | boolean | false | Enable AR mode |
| `ar_modes` | string | "webxr scene-viewer quick-look" | AR mode preferences |
| `ios_src` | string | "" | iOS-specific model URL (USDZ format) |

### Usage Examples

#### Basic Product Display
```php
[3d_model_viewer 
    src="/wp-content/uploads/models/product.glb"
    width="100%"
    height="500px"
    alt="Product 3D model"
]
```

#### Educational Model with Auto-Rotation
```php
[3d_model_viewer 
    src="/wp-content/uploads/models/anatomy.glb"
    auto_rotate="true"
    background_color="#f8f9fa"
    width="600px"
    height="400px"
]
```

#### AR-Enabled Model
```php
[3d_model_viewer 
    src="/wp-content/uploads/models/furniture.glb"
    ios_src="/wp-content/uploads/models/furniture.usdz"
    ar="true"
    poster="/wp-content/uploads/images/furniture-poster.jpg"
    alt="Interactive furniture model"
]
```

## Augmented Reality (AR) Support

### Overview

WP 3D Model Viewer includes comprehensive AR support, allowing users to view 3D models in their real environment using their mobile devices.

### Supported AR Technologies

- **WebXR**: Modern web-based AR for compatible browsers
- **AR Quick Look**: iOS Safari native AR support
- **Scene Viewer**: Android Chrome AR support
- **8th Wall**: Advanced web AR (premium feature)

### AR Implementation Notes

#### Device Requirements
- **iOS**: iOS 12+ with Safari browser, ARKit-compatible device
- **Android**: Android 7.0+ with Chrome browser, ARCore support
- **Web**: WebXR-compatible browser (Chrome 81+, Edge 83+)

#### Model Format Requirements
- **iOS**: USDZ format required for AR Quick Look
- **Android/Web**: GLTF/GLB format with proper scaling
- **Recommended**: Provide both GLTF and USDZ versions

#### AR Best Practices

1. **Model Optimization**
   - Keep polygon count under 100,000 triangles
   - Use PBR materials for realistic lighting
   - Optimize textures (max 2048x2048px)
   - Set proper scale (1 unit = 1 meter)

2. **File Size Considerations**
   - Target under 10MB for mobile compatibility
   - Use Draco compression for GLTF files
   - Optimize textures with appropriate compression

3. **User Experience**
   - Provide clear AR activation instructions
   - Include fallback poster images
   - Test on multiple devices and browsers

#### AR Configuration Example

```php
[3d_model_viewer 
    src="/models/chair.glb"
    ios_src="/models/chair.usdz"
    ar="true"
    ar_modes="webxr scene-viewer quick-look"
    poster="/images/chair-poster.jpg"
    alt="Modern office chair - tap to view in AR"
    width="100%"
    height="400px"
]
```

#### Troubleshooting AR Issues

**Model doesn't appear in AR:**
- Verify model scale (should be real-world size)
- Check file format compatibility
- Ensure proper lighting setup

**AR button not showing:**
- Verify device AR compatibility
- Check browser support
- Confirm HTTPS connection

**Performance issues:**
- Reduce model complexity
- Optimize textures
- Use level-of-detail (LOD) models

### Browser Support

| Browser | AR Support | Notes |
|---------|------------|-------|
| Safari iOS | ✅ AR Quick Look | iOS 12+ |
| Chrome Android | ✅ Scene Viewer | ARCore required |
| Chrome Desktop | ✅ WebXR | Experimental |
| Firefox | ⚠️ Limited | WebXR behind flag |
| Edge | ✅ WebXR | Version 83+ |

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser with WebGL support
- HTTPS connection (required for AR features)
- Media Library access for file uploads

## Development

### Local Development Setup

```bash
# Clone the repository
git clone https://github.com/Karalumpas/wp-3d-model-viewer.git
cd wp-3d-model-viewer

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Build assets
npm run build

# Start development mode
npm run dev
```

### Testing

```bash
# Run PHP tests
composer test

# Run PHP code standards check
composer phpcs

# Fix PHP code standards
composer phpcbf

# Run JavaScript tests
npm test

# Run E2E tests
npm run test:e2e
```

## API Reference

### PHP Hooks

```php
// Filter model data before rendering
add_filter('wp_3d_model_viewer_model_data', function($data, $attributes) {
    // Modify model data
    return $data;
}, 10, 2);

// Filter viewer configuration
add_filter('wp_3d_model_viewer_config', function($config, $attributes) {
    // Customize viewer settings
    return $config;
}, 10, 2);
```

### JavaScript Events

```javascript
// Listen for model load events
document.addEventListener('wp3d:model:loaded', function(event) {
    console.log('Model loaded:', event.detail);
});

// Listen for AR mode activation
document.addEventListener('wp3d:ar:activated', function(event) {
    console.log('AR mode activated:', event.detail);
});
```

## Contributing

We welcome contributions! Please see our [Contributing Guidelines](CONTRIBUTING.md) and [Agents.md](Agents.md) for development standards.

## Support

- 📚 [Documentation](https://github.com/Karalumpas/wp-3d-model-viewer/wiki)
- 🐛 [Bug Reports](https://github.com/Karalumpas/wp-3d-model-viewer/issues)
- 💬 [Discussions](https://github.com/Karalumpas/wp-3d-model-viewer/discussions)
- 📧 [Email Support](mailto:support@karalumpas.com)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a detailed list of changes and version history.

## Credits

- Built with [Three.js](https://threejs.org/)
- Model loading via [@google/model-viewer](https://modelviewer.dev/)
- Icons by [Feather Icons](https://feathericons.com/)
- Testing with [WordPress Plugin Boilerplate](https://github.com/DevinVinson/WordPress-Plugin-Boilerplate)

- 📖 [Documentation](https://github.com/Karalumpas/wp-3d-model-viewer/wiki)
- 🐛 [Bug Reports](https://github.com/Karalumpas/wp-3d-model-viewer/issues)
- 💬 [Discussions](https://github.com/Karalumpas/wp-3d-model-viewer/discussions)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Credits

- Built with ❤️ by [Karalumpas](https://github.com/Karalumpas)
- Powered by modern web technologies

---

**Made with WordPress in mind** 🚀