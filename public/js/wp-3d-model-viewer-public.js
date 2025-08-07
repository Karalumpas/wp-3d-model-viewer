(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {
		
		// Initialize public functionality when DOM is ready
		WP3DModelViewer.init();
		
	});

	// Also initialize when model-viewer library is ready
	document.addEventListener('DOMContentLoaded', function() {
		// Double-check initialization in case jQuery isn't available
		if (typeof WP3DModelViewer !== 'undefined') {
			WP3DModelViewer.init();
		}
	});

	/**
	 * WP 3D Model Viewer Public JavaScript
	 */
	const WP3DModelViewer = {
		
		// Configuration
		config: {
			modelViewerScript: 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js',
			intersectionObserverSupported: 'IntersectionObserver' in window,
			loadedViewers: new Set()
		},

		// Track initialization state
		initialized: false,

		/**
		 * Initialize public functionality
		 */
		init: function() {
			// Check if already initialized to prevent double initialization
			if (this.initialized) {
				return;
			}
			this.initialized = true;

			this.loadModelViewerScript()
				.then(() => {
					this.initViewers();
					this.bindEvents();
					this.setupLazyLoading();
				})
				.catch(error => {
					console.error('Failed to load model-viewer script:', error);
					// Fallback: try to initialize without explicit script loading
					setTimeout(() => {
						this.initViewers();
						this.bindEvents();
						this.setupLazyLoading();
					}, 1000);
				});
		},

		/**
		 * Load the model-viewer script
		 */
		loadModelViewerScript: function() {
			return new Promise((resolve, reject) => {
				// Check if already loaded
				if (window.customElements && window.customElements.get('model-viewer')) {
					resolve();
					return;
				}

				// Create script element
				const script = document.createElement('script');
				script.type = 'module';
				script.src = this.config.modelViewerScript;
				script.onload = resolve;
				script.onerror = reject;
				
				document.head.appendChild(script);
			});
		},

		/**
		 * Initialize all model viewers on the page
		 */
		initViewers: function() {
			const viewers = document.querySelectorAll('model-viewer.wp3d-viewer');
			
			viewers.forEach(viewer => {
				this.initViewer(viewer);
			});
		},

		/**
		 * Initialize individual model viewer
		 */
		initViewer: function(viewer) {
			const viewerId = viewer.id;
			
			// Skip if already initialized
			if (this.config.loadedViewers.has(viewerId)) {
				return;
			}

			// Mark as loaded
			this.config.loadedViewers.add(viewerId);

			// Get configuration
			const configScript = document.querySelector(`.wp3d-config[data-for="${viewerId}"]`);
			const config = configScript ? JSON.parse(configScript.textContent) : {};

			// Setup viewer
			this.setupViewer(viewer, config);
			this.addControls(viewer, config);
			this.setupEventListeners(viewer, config);
			this.addProgressTracking(viewer);
		},

		/**
		 * Setup viewer with configuration
		 */
		setupViewer: function(viewer, config) {
			// Add loading class
			viewer.classList.add('wp3d-loading');

			// Set up attributes based on config
			if (config.autoRotate) {
				viewer.setAttribute('auto-rotate', '');
			}

			if (config.cameraControls) {
				viewer.setAttribute('camera-controls', '');
			}

			if (config.ar && this.isARSupported()) {
				viewer.setAttribute('ar', '');
				if (config.arModes) {
					viewer.setAttribute('ar-modes', config.arModes);
				}
				if (config.iosSrc) {
					viewer.setAttribute('ios-src', config.iosSrc);
				}
			}

			// Add fade-in animation
			viewer.classList.add('wp3d-fade-in');
		},

		/**
		 * Add custom controls to viewer
		 */
		addControls: function(viewer, config) {
			const controlsContainer = document.createElement('div');
			controlsContainer.className = 'wp3d-controls';

			// Fullscreen button
			const fullscreenBtn = this.createControlButton('fullscreen', this.getIcon('fullscreen'));
			fullscreenBtn.addEventListener('click', () => this.toggleFullscreen(viewer));
			controlsContainer.appendChild(fullscreenBtn);

			// Reset camera button
			const resetBtn = this.createControlButton('reset', this.getIcon('reset'));
			resetBtn.addEventListener('click', () => this.resetCamera(viewer));
			controlsContainer.appendChild(resetBtn);

			// Auto-rotate toggle
			if (config.autoRotate !== undefined) {
				const rotateBtn = this.createControlButton('rotate', this.getIcon('rotate'));
				rotateBtn.addEventListener('click', () => this.toggleAutoRotate(viewer, rotateBtn));
				controlsContainer.appendChild(rotateBtn);
			}

			// Append controls to viewer container
			if (viewer.parentElement) {
				viewer.parentElement.style.position = 'relative';
				viewer.parentElement.appendChild(controlsContainer);
			}
		},

		/**
		 * Create control button
		 */
		createControlButton: function(type, icon) {
			const button = document.createElement('button');
			button.className = `wp3d-control-btn wp3d-control-${type}`;
			button.innerHTML = icon;
			button.setAttribute('aria-label', this.getButtonLabel(type));
			button.setAttribute('title', this.getButtonLabel(type));
			return button;
		},

		/**
		 * Get button label for accessibility
		 */
		getButtonLabel: function(type) {
			const labels = {
				fullscreen: 'Toggle fullscreen',
				reset: 'Reset camera',
				rotate: 'Toggle auto-rotation'
			};
			return labels[type] || type;
		},

		/**
		 * Get SVG icons
		 */
		getIcon: function(type) {
			const icons = {
				fullscreen: '<svg viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>',
				reset: '<svg viewBox="0 0 24 24"><path d="M17.65 6.35A7.958 7.958 0 0 0 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08A5.99 5.99 0 0 1 12 18c-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"/></svg>',
				rotate: '<svg viewBox="0 0 24 24"><path d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8A5.87 5.87 0 0 1 6 12c0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"/></svg>'
			};
			return icons[type] || '';
		},

		/**
		 * Setup event listeners for viewer
		 */
		setupEventListeners: function(viewer, config) {
			// Model load events
			viewer.addEventListener('load', () => {
				viewer.classList.remove('wp3d-loading');
				viewer.classList.add('wp3d-loaded');
				this.triggerEvent('wp3d:model:loaded', viewer, config);
			});

			viewer.addEventListener('error', (event) => {
				viewer.classList.remove('wp3d-loading');
				viewer.classList.add('wp3d-error');
				this.showError(viewer, 'Failed to load 3D model');
				this.triggerEvent('wp3d:model:error', viewer, { error: event.detail });
			});

			// Progress tracking
			viewer.addEventListener('progress', (event) => {
				this.updateProgress(viewer, event.detail.totalProgress);
			});

			// AR events
			if (config.ar) {
				viewer.addEventListener('ar-status', (event) => {
					this.triggerEvent('wp3d:ar:status', viewer, { status: event.detail.status });
				});
			}

			// Camera change events
			viewer.addEventListener('camera-change', () => {
				this.triggerEvent('wp3d:camera:change', viewer, {
					cameraTarget: viewer.getCameraTarget(),
					cameraOrbit: viewer.getCameraOrbit()
				});
			});
		},

		/**
		 * Add progress tracking
		 */
		addProgressTracking: function(viewer) {
			const progressContainer = document.createElement('div');
			progressContainer.className = 'wp3d-progress';
			progressContainer.innerHTML = '<div class="wp3d-progress-bar"></div>';
			
			if (viewer.parentElement) {
				viewer.parentElement.appendChild(progressContainer);
			}
		},

		/**
		 * Update progress bar
		 */
		updateProgress: function(viewer, progress) {
			const container = viewer.parentElement;
			if (!container) return;

			const progressBar = container.querySelector('.wp3d-progress-bar');
			if (progressBar) {
				progressBar.style.width = (progress * 100) + '%';
				
				// Hide progress bar when complete
				if (progress >= 1) {
					setTimeout(() => {
						const progressContainer = container.querySelector('.wp3d-progress');
						if (progressContainer) {
							progressContainer.style.opacity = '0';
							setTimeout(() => progressContainer.remove(), 300);
						}
					}, 500);
				}
			}
		},

		/**
		 * Setup lazy loading
		 */
		setupLazyLoading: function() {
			if (!this.config.intersectionObserverSupported) {
				return;
			}

			const lazyViewers = document.querySelectorAll('model-viewer[loading="lazy"]');
			
			if (lazyViewers.length === 0) {
				return;
			}

			const observer = new IntersectionObserver((entries) => {
				entries.forEach(entry => {
					if (entry.isIntersecting) {
						const viewer = entry.target;
						viewer.removeAttribute('loading');
						observer.unobserve(viewer);
					}
				});
			}, {
				rootMargin: '50px'
			});

			lazyViewers.forEach(viewer => observer.observe(viewer));
		},

		/**
		 * Bind global events
		 */
		bindEvents: function() {
			// Escape key to exit fullscreen
			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape') {
					this.exitFullscreen();
				}
			});

			// Handle window resize
			window.addEventListener('resize', () => {
				this.handleResize();
			});
		},

		/**
		 * Toggle fullscreen mode
		 */
		toggleFullscreen: function(viewer) {
			if (viewer.classList.contains('wp3d-viewer--fullscreen')) {
				this.exitFullscreen();
			} else {
				this.enterFullscreen(viewer);
			}
		},

		/**
		 * Enter fullscreen mode
		 */
		enterFullscreen: function(viewer) {
			viewer.classList.add('wp3d-viewer--fullscreen');
			document.body.style.overflow = 'hidden';
			this.triggerEvent('wp3d:fullscreen:enter', viewer);
		},

		/**
		 * Exit fullscreen mode
		 */
		exitFullscreen: function() {
			const fullscreenViewer = document.querySelector('.wp3d-viewer--fullscreen');
			if (fullscreenViewer) {
				fullscreenViewer.classList.remove('wp3d-viewer--fullscreen');
				document.body.style.overflow = '';
				this.triggerEvent('wp3d:fullscreen:exit', fullscreenViewer);
			}
		},

		/**
		 * Reset camera position
		 */
		resetCamera: function(viewer) {
			if (viewer.resetTurntableRotation) {
				viewer.resetTurntableRotation();
			}
			if (viewer.jumpCameraToGoal) {
				viewer.jumpCameraToGoal();
			}
			this.triggerEvent('wp3d:camera:reset', viewer);
		},

		/**
		 * Toggle auto rotation
		 */
		toggleAutoRotate: function(viewer, button) {
			const isRotating = viewer.hasAttribute('auto-rotate');
			
			if (isRotating) {
				viewer.removeAttribute('auto-rotate');
				button.classList.remove('active');
			} else {
				viewer.setAttribute('auto-rotate', '');
				button.classList.add('active');
			}
			
			this.triggerEvent('wp3d:autorotate:toggle', viewer, { enabled: !isRotating });
		},

		/**
		 * Show error message
		 */
		showError: function(viewer, message) {
			const errorDiv = document.createElement('div');
			errorDiv.className = 'wp3d-error-overlay';
			errorDiv.innerHTML = `<p>${message}</p>`;
			
			if (viewer.parentElement) {
				viewer.parentElement.appendChild(errorDiv);
			}
		},

		/**
		 * Check if AR is supported
		 */
		isARSupported: function() {
			return 'xr' in navigator && 'requestSession' in navigator.xr;
		},

		/**
		 * Handle window resize
		 */
		handleResize: function() {
			// Update any responsive elements
			const fullscreenViewer = document.querySelector('.wp3d-viewer--fullscreen');
			if (fullscreenViewer) {
				// Trigger resize event for model-viewer
				fullscreenViewer.dispatchEvent(new Event('resize'));
			}
		},

		/**
		 * Trigger custom event
		 */
		triggerEvent: function(eventName, element, detail = {}) {
			const event = new CustomEvent(eventName, {
				detail: { element, ...detail },
				bubbles: true,
				cancelable: true
			});
			
			if (element) {
				element.dispatchEvent(event);
			} else {
				document.dispatchEvent(event);
			}
		}
	};

	// Expose to global scope for external access
	window.WP3DModelViewer = WP3DModelViewer;

})( jQuery );
