# Development Agents & Guidelines

This document outlines the coding standards, development workflow, and guidelines for contributors and automated agents working on the WP 3D Model Viewer project.

## Table of Contents

- [Coding Standards](#coding-standards)
- [Branch Workflow](#branch-workflow)
- [Commit Style](#commit-style)
- [Code Review Process](#code-review-process)
- [Testing Requirements](#testing-requirements)
- [Documentation Standards](#documentation-standards)
- [Security Guidelines](#security-guidelines)
- [Performance Standards](#performance-standards)

## Coding Standards

### PHP Standards

#### WordPress Coding Standards
- Follow [WordPress PHP Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use WPCS (WordPress Coding Standards) with PHP_CodeSniffer
- All code must pass `composer phpcs` without warnings

#### PSR Standards
- PSR-4 autoloading for namespaced classes
- PSR-12 coding style for modern PHP features
- PSR-3 logging interfaces where applicable

#### Code Organization
```php
<?php
/**
 * Class description
 *
 * @package Karalumpas\WP3DModelViewer
 * @since   1.0.0
 */

namespace Karalumpas\WP3DModelViewer\Core;

use Karalumpas\WP3DModelViewer\Interfaces\ViewerInterface;
use Karalumpas\WP3DModelViewer\Traits\SingletonTrait;

/**
 * Main viewer class
 */
class ModelViewer implements ViewerInterface {
    use SingletonTrait;

    /**
     * Class constructor
     *
     * @since 1.0.0
     */
    public function __construct() {
        $this->init_hooks();
    }

    /**
     * Initialize WordPress hooks
     *
     * @since 1.0.0
     * @return void
     */
    private function init_hooks(): void {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        add_shortcode( '3d_model_viewer', [ $this, 'render_shortcode' ] );
    }
}
```

#### Naming Conventions
- **Classes**: PascalCase (`ModelViewer`, `ShortcodeHandler`)
- **Methods/Functions**: snake_case (`render_shortcode`, `enqueue_scripts`)
- **Variables**: snake_case (`$model_data`, `$viewer_config`)
- **Constants**: SCREAMING_SNAKE_CASE (`WP_3D_MODEL_VIEWER_VERSION`)
- **Files**: kebab-case (`model-viewer.php`, `shortcode-handler.php`)

### JavaScript/TypeScript Standards

#### ES6+ Standards
- Use modern JavaScript (ES2020+)
- Prefer const/let over var
- Use arrow functions for callbacks
- Destructuring for object/array assignments

#### Code Style
```javascript
/**
 * Model viewer class for handling 3D model display
 */
class ModelViewer {
    /**
     * Initialize the viewer
     * @param {Object} config - Viewer configuration
     * @param {string} config.containerId - Container element ID
     * @param {string} config.modelUrl - URL to 3D model
     */
    constructor({ containerId, modelUrl, ...options }) {
        this.container = document.getElementById(containerId);
        this.modelUrl = modelUrl;
        this.options = { ...this.defaultOptions, ...options };
        
        this.init();
    }

    /**
     * Initialize viewer components
     * @private
     */
    async init() {
        try {
            await this.loadModel();
            this.setupControls();
            this.bindEvents();
        } catch (error) {
            console.error('Failed to initialize model viewer:', error);
            this.showError(error.message);
        }
    }
}
```

#### TypeScript (when applicable)
- Strict type checking enabled
- Explicit return types for public methods
- Interface definitions for complex objects
- Generic types for reusable components

### CSS/SCSS Standards

#### Methodology
- BEM (Block Element Modifier) naming convention
- Component-based architecture
- CSS Custom Properties for theming

#### Structure
```scss
// Block
.wp3d-viewer {
    position: relative;
    width: 100%;
    background: var(--wp3d-bg-color, #ffffff);

    // Element
    &__canvas {
        display: block;
        width: 100%;
        height: 100%;
    }

    // Element
    &__controls {
        position: absolute;
        bottom: 1rem;
        right: 1rem;
        display: flex;
        gap: 0.5rem;
    }

    // Modifier
    &--fullscreen {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 9999;
    }
}
```

## Branch Workflow

### Branch Structure

```
main                 (Production-ready code)
├── dev             (Development integration)
├── feature/*       (Feature development)
├── bugfix/*        (Bug fixes)
├── hotfix/*        (Critical production fixes)
└── release/*       (Release preparation)
```

### Branch Types

#### Main Branch
- **Purpose**: Production-ready code only
- **Protection**: Requires PR approval and passing CI
- **Merges**: Only from `dev` via release PRs or `hotfix/*` branches
- **Deployment**: Automatic deployment to production

#### Development Branch (`dev`)
- **Purpose**: Integration branch for ongoing development
- **Source**: Feature and bugfix branches merge here
- **Testing**: All features tested together
- **Stability**: Should always be in a working state

#### Feature Branches (`feature/`)
```bash
# Naming convention
feature/add-ar-support
feature/improve-loading-performance
feature/gutenberg-block-editor
```
- **Lifetime**: Created from `dev`, merged back to `dev`
- **Scope**: Single feature or enhancement
- **Testing**: Must include tests for new functionality

#### Bugfix Branches (`bugfix/`)
```bash
# Naming convention
bugfix/fix-mobile-controls
bugfix/resolve-texture-loading
bugfix/correct-ar-scaling
```
- **Source**: Created from `dev` or `main` (depending on urgency)
- **Scope**: Single bug fix
- **Testing**: Must include regression tests

#### Hotfix Branches (`hotfix/`)
```bash
# Naming convention
hotfix/security-patch-1.2.1
hotfix/critical-crash-fix
```
- **Source**: Created from `main`
- **Target**: Merged to both `main` and `dev`
- **Scope**: Critical production issues only

#### Release Branches (`release/`)
```bash
# Naming convention
release/1.3.0
release/2.0.0-beta.1
```
- **Purpose**: Prepare releases, final testing
- **Source**: Created from `dev`
- **Scope**: Version bumps, changelog updates, final bug fixes

### Workflow Process

#### Feature Development
1. Create feature branch from `dev`
2. Implement feature with tests
3. Submit PR to `dev`
4. Code review and approval
5. Merge to `dev`

#### Release Process
1. Create `release/x.y.z` from `dev`
2. Final testing and bug fixes
3. Update version numbers and changelog
4. Submit PR to `main`
5. Merge and tag release
6. Merge back to `dev`

#### Hotfix Process
1. Create `hotfix/description` from `main`
2. Fix critical issue
3. Submit PR to `main`
4. Emergency review and merge
5. Merge back to `dev`

## Commit Style

### Conventional Commits

Follow [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>[optional scope]: <description>

[optional body]

[optional footer(s)]
```

### Commit Types

- **feat**: New feature for the user
- **fix**: Bug fix for the user
- **docs**: Documentation changes
- **style**: Code style changes (formatting, missing semicolons, etc.)
- **refactor**: Code refactoring without behavior changes
- **perf**: Performance improvements
- **test**: Adding or updating tests
- **build**: Build system or dependency changes
- **ci**: CI/CD configuration changes
- **chore**: Maintenance tasks, package updates

### Commit Examples

```bash
# Feature commits
feat(viewer): add AR support for iOS devices
feat(admin): implement model upload interface
feat(shortcode): add auto-rotation parameter

# Bug fix commits
fix(controls): resolve touch controls on mobile devices
fix(loading): handle network timeout errors gracefully
fix(ar): correct model scaling in AR mode

# Documentation commits
docs(readme): update installation instructions
docs(api): add shortcode parameter reference
docs(contributing): update development setup guide

# Performance commits
perf(loading): implement lazy loading for 3D models
perf(rendering): optimize shader compilation

# Breaking changes
feat(api)!: redesign shortcode attribute structure

BREAKING CHANGE: Shortcode attributes have been renamed for consistency.
- `model_url` is now `src`
- `viewer_width` is now `width`
- `viewer_height` is now `height`
```

### Commit Guidelines

#### Message Structure
- **Subject line**: 50 characters or less
- **Body**: Wrap at 72 characters
- **Footer**: Reference issues and breaking changes

#### Subject Line Rules
- Use imperative mood ("add feature" not "added feature")
- Don't capitalize first letter
- No period at the end
- Be specific and descriptive

#### Body Content
- Explain the "what" and "why", not the "how"
- Separate subject from body with blank line
- Use bullet points for multiple changes

#### Footer Content
- Reference related issues: `Fixes #123`, `Closes #456`
- Document breaking changes: `BREAKING CHANGE: ...`
- Credit co-authors: `Co-authored-by: Name <email@example.com>`

### Commit Frequency

#### Atomic Commits
- One logical change per commit
- Each commit should be buildable and testable
- Related changes should be grouped together

#### Work-in-Progress
```bash
# Use WIP prefix for incomplete work
git commit -m "WIP: implement AR controls structure"

# Squash WIP commits before final PR
git rebase -i HEAD~n
```

## Code Review Process

### Review Requirements

#### Automated Checks
- All CI/CD checks must pass
- Code coverage must not decrease
- Security scans must pass
- Performance benchmarks within acceptable range

#### Human Review
- At least one approval from code owner
- Additional approval for breaking changes
- Security review for authentication/authorization changes

### Review Guidelines

#### For Reviewers
- **Functionality**: Does the code work as intended?
- **Readability**: Is the code clear and well-documented?
- **Performance**: Are there any performance concerns?
- **Security**: Are there any security vulnerabilities?
- **Testing**: Are there adequate tests?
- **Standards**: Does it follow our coding standards?

#### Review Checklist
- [ ] Code follows established patterns and conventions
- [ ] New features include appropriate tests
- [ ] Documentation is updated for user-facing changes
- [ ] No sensitive data (API keys, passwords) in code
- [ ] Error handling is appropriate and user-friendly
- [ ] Performance impact is acceptable
- [ ] Accessibility standards are maintained

### Review Comments

#### Constructive Feedback
```
// Good
"Consider extracting this logic into a separate method for better readability"

// Better
"This block could be extracted into a `validateModelFormat()` method to improve 
readability and make it easier to test. What do you think?"

// Best
"This validation logic might be clearer as a separate method. It would also make 
it easier to unit test. Here's a suggested approach: [example code]"
```

#### Categories
- **Must Fix**: Blocking issues (bugs, security, standards violations)
- **Should Fix**: Improvements that should be addressed
- **Consider**: Suggestions for consideration
- **Nitpick**: Minor style or preference items

## Testing Requirements

### Test Coverage
- **Minimum**: 80% line coverage
- **Target**: 90% line coverage
- **Critical paths**: 100% coverage

### Test Types

#### Unit Tests (PHP)
```php
use PHPUnit\Framework\TestCase;
use Karalumpas\WP3DModelViewer\Core\ModelValidator;

class ModelValidatorTest extends TestCase {
    private ModelValidator $validator;

    protected function setUp(): void {
        $this->validator = new ModelValidator();
    }

    /**
     * @test
     */
    public function it_validates_gltf_files(): void {
        $result = $this->validator->validate('/path/to/model.gltf');
        
        $this->assertTrue($result->isValid());
        $this->assertEquals('gltf', $result->getFormat());
    }

    /**
     * @test
     */
    public function it_rejects_invalid_file_formats(): void {
        $this->expectException(InvalidModelFormatException::class);
        
        $this->validator->validate('/path/to/document.pdf');
    }
}
```

#### Integration Tests (JavaScript)
```javascript
describe('ModelViewer Integration', () => {
    let viewer;
    let container;

    beforeEach(() => {
        container = document.createElement('div');
        container.id = 'test-container';
        document.body.appendChild(container);
    });

    afterEach(() => {
        if (viewer) {
            viewer.destroy();
        }
        document.body.removeChild(container);
    });

    it('should load and render a valid GLTF model', async () => {
        viewer = new ModelViewer({
            containerId: 'test-container',
            modelUrl: '/fixtures/test-model.gltf'
        });

        await viewer.ready();

        expect(viewer.isLoaded()).toBe(true);
        expect(container.querySelector('canvas')).toBeTruthy();
    });
});
```

#### E2E Tests
```javascript
// tests/e2e/shortcode.spec.js
const { test, expect } = require('@playwright/test');

test('shortcode renders 3D model correctly', async ({ page }) => {
    await page.goto('/test-page-with-shortcode/');
    
    // Wait for model to load
    await page.waitForSelector('.wp3d-viewer canvas');
    
    // Verify viewer is present
    const viewer = page.locator('.wp3d-viewer');
    await expect(viewer).toBeVisible();
    
    // Test interactions
    await viewer.click();
    await page.mouse.move(100, 100);
    
    // Verify model rotated
    const canvas = page.locator('.wp3d-viewer canvas');
    await expect(canvas).toHaveAttribute('data-interacted', 'true');
});
```

### Test Organization

#### File Structure
```
tests/
├── unit/
│   ├── php/
│   │   ├── Core/
│   │   ├── Admin/
│   │   └── Frontend/
│   └── js/
│       ├── components/
│       └── utils/
├── integration/
│   ├── api/
│   ├── database/
│   └── frontend/
└── e2e/
    ├── admin/
    ├── frontend/
    └── fixtures/
```

## Documentation Standards

### Code Documentation

#### PHP DocBlocks
```php
/**
 * Render a 3D model viewer shortcode
 *
 * @since 1.0.0
 * 
 * @param array  $atts {
 *     Shortcode attributes.
 *     
 *     @type string $src              Model file URL. Required.
 *     @type string $width            Viewer width. Default '100%'.
 *     @type string $height           Viewer height. Default '400px'.
 *     @type bool   $auto_rotate      Enable auto rotation. Default false.
 *     @type bool   $camera_controls  Enable camera controls. Default true.
 * }
 * @param string $content Shortcode content (not used).
 * @param string $tag     Shortcode tag name.
 * 
 * @return string HTML output for the 3D model viewer.
 * 
 * @throws InvalidArgumentException When required src attribute is missing.
 * 
 * @example
 * [3d_model_viewer src="/path/to/model.glb" width="800px" height="600px"]
 */
public function render_shortcode( array $atts, string $content = '', string $tag = '' ): string {
    // Implementation
}
```

#### JavaScript JSDoc
```javascript
/**
 * Initialize 3D model viewer
 * @class
 * @param {Object} config - Configuration options
 * @param {string} config.containerId - DOM element ID for viewer container
 * @param {string} config.modelUrl - URL to 3D model file
 * @param {boolean} [config.autoRotate=false] - Enable automatic rotation
 * @param {Object} [config.lighting] - Lighting configuration
 * @param {string} [config.lighting.type='default'] - Lighting type
 * @param {number} [config.lighting.intensity=1.0] - Light intensity
 * 
 * @example
 * const viewer = new ModelViewer({
 *   containerId: 'model-container',
 *   modelUrl: '/path/to/model.glb',
 *   autoRotate: true,
 *   lighting: {
 *     type: 'studio',
 *     intensity: 1.2
 *   }
 * });
 */
class ModelViewer {
    /**
     * Load and display 3D model
     * @async
     * @returns {Promise<void>} Resolves when model is loaded
     * @throws {Error} When model fails to load
     */
    async loadModel() {
        // Implementation
    }
}
```

### User Documentation

#### README Sections
- Clear purpose and features
- Installation instructions
- Usage examples
- Configuration options
- Troubleshooting guide
- API reference

#### Wiki Pages
- Detailed tutorials
- Advanced configuration
- Developer guides
- FAQ and troubleshooting
- Video demonstrations

## Security Guidelines

### Input Validation
- Sanitize all user inputs
- Validate file uploads
- Check file types and sizes
- Prevent path traversal attacks

### Output Escaping
```php
// Always escape output
echo esc_html( $user_input );
echo esc_url( $model_url );
echo esc_attr( $css_class );

// Use wp_kses for HTML content
echo wp_kses( $html_content, $allowed_html );
```

### Nonce Verification
```php
// Add nonces to forms
wp_nonce_field( 'wp3d_save_settings', 'wp3d_settings_nonce' );

// Verify nonces
if ( ! wp_verify_nonce( $_POST['wp3d_settings_nonce'], 'wp3d_save_settings' ) ) {
    wp_die( 'Security check failed' );
}
```

### Capability Checks
```php
// Check user capabilities
if ( ! current_user_can( 'manage_options' ) ) {
    wp_die( 'Insufficient permissions' );
}

// Use appropriate capabilities
if ( current_user_can( 'upload_files' ) ) {
    // Allow file upload
}
```

## Performance Standards

### PHP Performance
- Use WordPress caching APIs
- Minimize database queries
- Implement object caching
- Profile with Query Monitor

### JavaScript Performance
- Lazy load 3D models
- Use Web Workers for heavy processing
- Implement efficient rendering loops
- Monitor memory usage

### Asset Optimization
- Minify CSS/JS in production
- Optimize images and textures
- Use appropriate compression
- Implement caching headers

### Benchmarks
- Page load time < 3 seconds
- First contentful paint < 1.5 seconds
- 3D model load time < 5 seconds
- Memory usage < 100MB per viewer

---

This document serves as the foundation for all development work on the WP 3D Model Viewer project. All contributors, whether human or automated agents, should follow these guidelines to ensure code quality, maintainability, and project success.

For questions or clarifications about these guidelines, please open an issue or discussion on the project repository.
