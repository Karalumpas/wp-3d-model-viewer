# WP 3D Model Viewer - CSS Architecture

## Overview
Modern CSS architecture using PostCSS build system with modular design and CSS custom properties for maintainable styling.

## Build System
- **PostCSS**: Modern CSS processing with autoprefixer and cssnano
- **Development**: Source maps, unminified output, CSS imports
- **Production**: Minified, optimized, no source maps
- **Watch Mode**: Automatic compilation during development

## File Structure
```
admin/css/
├── src/                          # Source files (development)
│   ├── admin.css                 # Main entry point
│   ├── _variables.css            # CSS custom properties
│   ├── _global.css              # Base styles and utilities
│   ├── _settings.css            # Settings page styles
│   ├── _cpt.css                 # Custom Post Type styles
│   └── _responsive.css          # Responsive utilities
├── wp-3d-model-viewer-admin.css     # Development build
└── wp-3d-model-viewer-admin.min.css # Production build
```

## Design Tokens (_variables.css)
Comprehensive CSS custom properties system:
- **Colors**: Primary, semantic colors, WordPress admin integration
- **Spacing**: 8px base scale (xs to 4xl)
- **Typography**: Font families, sizes, weights, line heights
- **Layout**: Grid gaps, column widths, breakpoints
- **Shadows**: Elevation system (sm to xl)
- **Transitions**: Consistent timing and easing
- **Z-index**: Layering scale for modals, tooltips, overlays

## Component Architecture

### Global Styles (_global.css)
- CSS reset and base styles
- Form elements and inputs
- Button system with variants
- Loading states and animations
- Utility classes
- WordPress admin integration

### Settings Page (_settings.css)
- Settings sections and fieldsets
- File type configuration grid
- Help and documentation styles
- Advanced settings toggles
- Responsive settings layout

### Custom Post Type (_cpt.css)
- Post list table columns
- Model thumbnails and previews
- Metabox styling
- File upload interface
- Shortcode displays
- Toggle switches and controls
- Drag & drop enhancements

### Responsive Design (_responsive.css)
- Mobile-first approach
- Breakpoint utilities
- Container queries (modern browsers)
- Responsive spacing and typography
- Show/hide helpers

## Build Commands

### Development
```bash
npm run build:css:dev      # Build with source maps
npm run watch:css          # Watch mode for development
npm run lint:css           # CSS linting
```

### Production
```bash
npm run build:css:prod     # Minified production build
npm run build              # Full production build (CSS + JS)
```

### Utilities
```bash
npm run clean              # Clean build artifacts
npm run dev                # Development server
```

## CSS Custom Properties Usage

### Design Tokens
```css
.my-component {
  color: var(--wp3d-primary);
  padding: var(--wp3d-space-lg);
  border-radius: var(--wp3d-radius);
  transition: var(--wp3d-transition);
}
```

### WordPress Admin Color Schemes
The system automatically adapts to WordPress admin color schemes:
- Blue (default)
- Coffee
- Ectoplasm
- Midnight
- Ocean
- Sunrise

### Accessibility Features
- High contrast mode support
- Reduced motion preferences
- Keyboard navigation styles
- Screen reader friendly markup
- WCAG 2.1 AA compliance

## Browser Support
- **Modern Browsers**: Full feature support with CSS Grid, custom properties
- **Legacy Browsers**: Graceful fallbacks for IE11+
- **Feature Detection**: Progressive enhancement with @supports
- **Mobile**: iOS Safari 12+, Android Chrome 70+

## Performance
- **Minification**: ~37% size reduction in production
- **Tree Shaking**: Unused CSS removed by cssnano
- **Critical CSS**: Inline critical styles for above-fold content
- **Caching**: Versioned assets for cache busting

## Naming Conventions
- **Prefix**: `wp3d-` for all custom classes
- **BEM**: Block Element Modifier for components
- **Utility**: Descriptive utility classes (wp3d-text-center)
- **State**: State-based classes (is-active, has-error)

## Development Workflow
1. Edit source files in `admin/css/src/`
2. Run `npm run watch:css` for automatic compilation
3. Use browser dev tools with source maps
4. Run `npm run lint:css` before committing
5. Build production version with `npm run build:css:prod`

## Integration with PHP
The plugin automatically enqueues the appropriate CSS file:
- Development: `wp-3d-model-viewer-admin.css` (with source maps)
- Production: `wp-3d-model-viewer-admin.min.css` (minified)

```php
wp_enqueue_style(
    'wp-3d-model-viewer-admin',
    $css_file_url,
    [],
    filemtime($css_file_path)
);
```
