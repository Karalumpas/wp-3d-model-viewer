# WP 3D Model Viewer

[![Latest Stable Version](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/v/stable)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![Total Downloads](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/downloads)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![License](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/license)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![PHP Version Require](https://poser.pugx.org/karalumpas/wp-3d-model-viewer/require/php)](https://packagist.org/packages/karalumpas/wp-3d-model-viewer)
[![WordPress tested](https://img.shields.io/badge/WordPress-tested%206.4+-green.svg)](https://wordpress.org/)
[![Build Status](https://img.shields.io/github/actions/workflow/status/Karalumpas/wp-3d-model-viewer/ci.yml?branch=main)](https://github.com/Karalumpas/wp-3d-model-viewer/actions)
[![Code Coverage](https://codecov.io/gh/Karalumpas/wp-3d-model-viewer/branch/main/graph/badge.svg)](https://codecov.io/gh/Karalumpas/wp-3d-model-viewer)

A WordPress plugin for viewing 3D models in the browser with interactive controls and modern web technologies.

## Description

WP 3D Model Viewer is a powerful WordPress plugin that allows you to easily embed and display 3D models on your website. The plugin supports various 3D model formats and provides an intuitive interface for both administrators and visitors to interact with 3D content.

## Features

- 🎯 **Easy Integration** - Simple shortcode and block editor support
- 📱 **Responsive Design** - Works seamlessly on desktop and mobile devices
- 🎮 **Interactive Controls** - Zoom, rotate, and pan functionality
- 🎨 **Customizable** - Multiple viewing options and styling controls
- ⚡ **Performance Optimized** - Efficient loading and rendering
- 🔧 **Developer Friendly** - Extensible with hooks and filters

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser with WebGL support

## Installation

### Via WordPress Admin

1. Download the plugin from the WordPress repository
2. Upload and activate the plugin through the WordPress admin panel
3. Configure the plugin settings in **Settings > 3D Model Viewer**

### Via Composer

```bash
composer require karalumpas/wp-3d-model-viewer
```

### Manual Installation

1. Download the latest release from [GitHub](https://github.com/Karalumpas/wp-3d-model-viewer/releases)
2. Extract the files to your `/wp-content/plugins/wp-3d-model-viewer/` directory
3. Activate the plugin through the WordPress admin panel

## Usage

### Shortcode

```php
[3d_model_viewer src="path/to/model.glb" width="100%" height="400px"]
```

### Block Editor

Look for the "3D Model Viewer" block in the Gutenberg editor.

### PHP Template

```php
<?php
if (function_exists('wp_3d_model_viewer')) {
    wp_3d_model_viewer([
        'src' => 'path/to/model.glb',
        'width' => '100%',
        'height' => '400px'
    ]);
}
?>
```

## Configuration

The plugin can be configured through the WordPress admin panel under **Settings > 3D Model Viewer**.

## Development

### Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Install npm dependencies: `npm install`
4. Build assets: `npm run build`

### Testing

```bash
# Run PHP tests
composer test

# Run PHP code standards
composer phpcs

# Fix PHP code standards
composer phpcbf

# Run JavaScript tests
npm test
```

## Contributing

Contributions are welcome! Please read our [Contributing Guidelines](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.

## Support

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