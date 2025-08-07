(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
		
		// Initialize admin functionality
		WP3DModelViewerAdmin.init();
		
	});

	/**
	 * WP 3D Model Viewer Admin JavaScript
	 */
	const WP3DModelViewerAdmin = {
		
		/**
		 * Initialize admin functionality
		 */
		init: function() {
			this.bindEvents();
			this.initFormValidation();
			this.initColorPickers();
			this.initModelViewer();
			this.initPreviewControls();
		},
			this.initFileTypeToggle();
		},

		/**
		 * Bind event handlers
		 */
		bindEvents: function() {
			// Settings form submission
			$('#wp3d-settings-form').on('submit', this.validateForm);
			
			// File size input formatting
			$('#max_file_size').on('input', this.formatFileSize);
			
			// Dimension input validation
			$('#default_width, #default_height').on('blur', this.validateDimensions);
			
			// AR settings toggle
			$('#enable_ar_by_default').on('change', this.toggleARSettings);
		},

		/**
		 * Initialize form validation
		 */
		initFormValidation: function() {
			// Add validation classes
			$('.form-table input[required]').addClass('wp3d-required');
			
			// Real-time validation
			$('.wp3d-required').on('blur', function() {
				WP3DModelViewerAdmin.validateField($(this));
			});
		},

		/**
		 * Initialize color pickers
		 */
		initColorPickers: function() {
			// Add color picker if available
			if ($.fn.wpColorPicker) {
				$('#default_background_color').wpColorPicker({
					defaultColor: '#ffffff',
					change: function(event, ui) {
						// Color changed
						console.log('Background color changed to:', ui.color.toString());
					}
				});
			}
		},

		/**
		 * Initialize file type toggle functionality
		 */
		initFileTypeToggle: function() {
			// Select all/none buttons for file types
			const $fileTypes = $('input[name="allowed_file_types[]"]');
			const $selectAll = $('<button type="button" class="button button-small">Select All</button>');
			const $selectNone = $('<button type="button" class="button button-small">Select None</button>');
			
			// Add buttons after file types
			$fileTypes.last().closest('fieldset').append(
				'<p class="wp3d-file-type-controls"></p>'
			).find('.wp3d-file-type-controls').append($selectAll, ' ', $selectNone);
			
			// Select all handler
			$selectAll.on('click', function() {
				$fileTypes.prop('checked', true);
			});
			
			// Select none handler
			$selectNone.on('click', function() {
				$fileTypes.prop('checked', false);
			});
		},

		/**
		 * Validate form before submission
		 */
		validateForm: function(event) {
			let isValid = true;
			const errors = [];

			// Validate required fields
			$('.wp3d-required').each(function() {
				if (!WP3DModelViewerAdmin.validateField($(this))) {
					isValid = false;
				}
			});

			// Validate at least one file type is selected
			if ($('input[name="allowed_file_types[]"]:checked').length === 0) {
				errors.push('Please select at least one allowed file type.');
				isValid = false;
			}

			// Validate file size
			const maxFileSize = parseInt($('#max_file_size').val());
			if (maxFileSize < 1048576 || maxFileSize > 104857600) {
				errors.push('File size must be between 1MB and 100MB.');
				isValid = false;
			}

			// Show errors if any
			if (!isValid) {
				event.preventDefault();
				WP3DModelViewerAdmin.showErrors(errors);
			}

			return isValid;
		},

		/**
		 * Validate individual field
		 */
		validateField: function($field) {
			const value = $field.val().trim();
			const fieldName = $field.attr('name');
			let isValid = true;

			// Remove previous error styling
			$field.removeClass('wp3d-error');

			// Required field validation
			if ($field.hasClass('wp3d-required') && !value) {
				isValid = false;
			}

			// Specific field validations
			switch (fieldName) {
				case 'default_width':
				case 'default_height':
					if (value && !this.isValidDimension(value)) {
						isValid = false;
					}
					break;
			}

			// Add error styling if invalid
			if (!isValid) {
				$field.addClass('wp3d-error');
			}

			return isValid;
		},

		/**
		 * Check if dimension value is valid
		 */
		isValidDimension: function(value) {
			// Allow percentages, pixels, em, rem, vh, vw
			return /^(\d+(?:\.\d+)?)(px|%|em|rem|vh|vw)$/.test(value) || /^\d+$/.test(value);
		},

		/**
		 * Format file size input
		 */
		formatFileSize: function() {
			const $input = $(this);
			const value = parseInt($input.val());
			const $display = $input.siblings('.file-size-display');
			
			if (value) {
				const formatted = WP3DModelViewerAdmin.formatBytes(value);
				if ($display.length) {
					$display.text(formatted);
				} else {
					$input.after('<span class="file-size-display description"> (' + formatted + ')</span>');
				}
			}
		},

		/**
		 * Format bytes to human readable
		 */
		formatBytes: function(bytes) {
			if (bytes === 0) return '0 Bytes';
			const k = 1024;
			const sizes = ['Bytes', 'KB', 'MB', 'GB'];
			const i = Math.floor(Math.log(bytes) / Math.log(k));
			return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
		},

		/**
		 * Validate dimension inputs
		 */
		validateDimensions: function() {
			const $input = $(this);
			const value = $input.val().trim();
			
			if (value && !WP3DModelViewerAdmin.isValidDimension(value)) {
				$input.addClass('wp3d-error');
				WP3DModelViewerAdmin.showFieldError($input, 'Invalid dimension format. Use values like: 100%, 400px, 20em, 50vh');
			} else {
				$input.removeClass('wp3d-error');
				WP3DModelViewerAdmin.hideFieldError($input);
			}
		},

		/**
		 * Toggle AR-related settings
		 */
		toggleARSettings: function() {
			const $checkbox = $(this);
			const $arSettings = $('.wp3d-ar-settings');
			
			if ($checkbox.is(':checked')) {
				$arSettings.slideDown();
			} else {
				$arSettings.slideUp();
			}
		},

		/**
		 * Show validation errors
		 */
		showErrors: function(errors) {
			// Remove existing error notices
			$('.wp3d-error-notice').remove();
			
			if (errors.length > 0) {
				const $notice = $('<div class="notice notice-error wp3d-error-notice"><ul></ul></div>');
				const $list = $notice.find('ul');
				
				errors.forEach(function(error) {
					$list.append('<li>' + error + '</li>');
				});
				
				$('.wrap h1').after($notice);
				
				// Scroll to top
				$('html, body').animate({
					scrollTop: $('.wrap').offset().top
				}, 500);
			}
		},

		/**
		 * Show field-specific error
		 */
		showFieldError: function($field, message) {
			const $error = $field.siblings('.wp3d-field-error');
			if ($error.length) {
				$error.text(message);
			} else {
				$field.after('<span class="wp3d-field-error description" style="color: #dc3232;">' + message + '</span>');
			}
		},

		/**
		 * Hide field-specific error
		 */
		hideFieldError: function($field) {
			$field.siblings('.wp3d-field-error').remove();
		},

		/**
		 * Initialize model-viewer library for admin preview
		 */
		initModelViewer: function() {
			// Load model-viewer script if not already loaded
			if (!window.customElements || !window.customElements.get('model-viewer')) {
				const script = document.createElement('script');
				script.type = 'module';
				script.src = 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js';
				document.head.appendChild(script);
			}
		},

		/**
		 * Initialize preview control functionality
		 */
		initPreviewControls: function() {
			// Listen for file uploads to refresh preview
			$(document).on('change', '#wp3d_model_file', this.updatePreview.bind(this));
			$(document).on('change', '#wp3d_poster_image', this.updatePreviewPoster.bind(this));
		},

		/**
		 * Update the 3D model preview when a new file is selected
		 */
		updatePreview: function() {
			const modelFileId = $('#wp3d_model_file').val();
			const $previewContainer = $('#wp3d-admin-preview-container');
			const $placeholder = $('.wp3d-admin-preview-placeholder');

			if (modelFileId) {
				// Get the attachment URL via WordPress media API
				wp.media.attachment(modelFileId).fetch().then(function(attachment) {
					const modelUrl = attachment.url;
					const posterImageId = $('#wp3d_poster_image').val();
					
					// Hide placeholder, show preview
					$placeholder.hide();
					$previewContainer.parent().show();

					// Update or create model-viewer element
					let $modelViewer = $previewContainer.find('model-viewer');
					if ($modelViewer.length === 0) {
						$modelViewer = $('<model-viewer>');
						$previewContainer.html($modelViewer);
					}

					// Update model-viewer attributes
					$modelViewer.attr({
						'src': modelUrl,
						'style': 'width: 100%; height: 400px; background-color: #f0f0f0; border-radius: 4px;',
						'camera-controls': '',
						'auto-rotate': '',
						'loading': 'eager',
						'class': 'wp3d-admin-preview'
					});

					// Add poster if available
					if (posterImageId) {
						wp.media.attachment(posterImageId).fetch().then(function(posterAttachment) {
							$modelViewer.attr('poster', posterAttachment.url);
						});
					}

					// Re-add loading and fallback content
					$modelViewer.html(`
						<div slot="poster" style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f0f0f0;">
							<div style="text-align: center;">
								<div class="wp3d-admin-spinner" style="border: 4px solid #f3f3f3; border-radius: 50%; border-top: 4px solid #0073aa; width: 40px; height: 40px; animation: wp3d-spin 2s linear infinite; margin: 0 auto 10px;"></div>
								<p style="margin: 0; color: #666;">Loading 3D Model...</p>
							</div>
						</div>
						<div slot="fallback" style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f0f0f0;">
							<div style="text-align: center; color: #d63638;">
								<p style="margin: 0;"><strong>Unable to load 3D model</strong></p>
								<p style="margin: 5px 0 0; font-size: 12px;">Please check the file format and try again.</p>
							</div>
						</div>
					`);
				});
			} else {
				// No model file, show placeholder
				$previewContainer.parent().hide();
				$placeholder.show();
			}
		},

		/**
		 * Update preview poster image
		 */
		updatePreviewPoster: function() {
			const posterImageId = $('#wp3d_poster_image').val();
			const $modelViewer = $('#wp3d-admin-preview-container model-viewer');

			if ($modelViewer.length && posterImageId) {
				wp.media.attachment(posterImageId).fetch().then(function(attachment) {
					$modelViewer.attr('poster', attachment.url);
				});
			} else if ($modelViewer.length) {
				$modelViewer.removeAttr('poster');
			}
		}
	};

})( jQuery );

/**
 * Global functions for 3D model preview controls
 * These are called from inline onclick handlers in the PHP
 */
function wp3dRefreshPreview() {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		// Force reload by temporarily removing and re-adding src
		const src = modelViewer.src;
		modelViewer.src = '';
		setTimeout(() => {
			modelViewer.src = src + '?refresh=' + Date.now();
		}, 100);
		
		showAdminNotice('Preview refreshed!', 'info');
	}
}

function wp3dResetCamera() {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		modelViewer.setAttribute('camera-orbit', '0deg 75deg 105%');
		jQuery('#wp3d_start_rotation_preview').val('0deg 75deg 105%');
		
		showAdminNotice('Camera reset!', 'info');
	}
}

/**
 * Update preview background color in real-time
 */
function updatePreviewBackground(color) {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		modelViewer.style.backgroundColor = color;
	}
	
	// Update the color value display
	jQuery('.wp3d-color-value').text(color);
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Update preview camera position in real-time
 */
function updatePreviewCamera(rotation) {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer && rotation.trim()) {
		modelViewer.setAttribute('camera-orbit', rotation);
	}
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Update preview auto-rotate in real-time
 */
function updatePreviewAutoRotate(enabled) {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		if (enabled) {
			modelViewer.setAttribute('auto-rotate', '');
		} else {
			modelViewer.removeAttribute('auto-rotate');
		}
	}
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Update preview camera controls in real-time
 */
function updatePreviewControls(enabled) {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		modelViewer.setAttribute('camera-controls', enabled ? 'true' : 'false');
	}
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Update preview AR setting in real-time
 */
function updatePreviewAR(enabled) {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (modelViewer) {
		if (enabled) {
			modelViewer.setAttribute('ar', '');
			modelViewer.setAttribute('ar-modes', 'webxr scene-viewer quick-look');
		} else {
			modelViewer.removeAttribute('ar');
			modelViewer.removeAttribute('ar-modes');
		}
	}
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Reset preview to defaults
 */
function resetPreviewToDefaults() {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (!modelViewer) return;

	// Reset values
	modelViewer.style.backgroundColor = '#ffffff';
	modelViewer.setAttribute('camera-orbit', '0deg 75deg 105%');
	modelViewer.removeAttribute('auto-rotate');
	modelViewer.setAttribute('camera-controls', 'true');
	modelViewer.removeAttribute('ar');

	// Update form fields
	jQuery('#wp3d_bg_color_preview').val('#ffffff');
	jQuery('#wp3d_start_rotation_preview').val('0deg 75deg 105%');
	jQuery('#wp3d_auto_rotate_preview').prop('checked', false);
	jQuery('#wp3d_camera_controls_preview').prop('checked', true);
	jQuery('#wp3d_ar_enabled_preview').prop('checked', false);
	jQuery('.wp3d-color-value').text('#ffffff');

	showAdminNotice('Settings reset to defaults!', 'success');
	
	// Auto-save changes
	debounceAutoSave();
}

/**
 * Copy current settings to clipboard
 */
function copyCurrentSettings() {
	const modelViewer = document.querySelector('#wp3d-main-preview');
	if (!modelViewer) return;

	const settings = {
		backgroundColor: modelViewer.style.backgroundColor || '#ffffff',
		cameraOrbit: modelViewer.getAttribute('camera-orbit') || '0deg 75deg 105%',
		autoRotate: modelViewer.hasAttribute('auto-rotate'),
		cameraControls: modelViewer.getAttribute('camera-controls') !== 'false',
		ar: modelViewer.hasAttribute('ar')
	};

	const settingsText = `3D Model Settings:
Background: ${settings.backgroundColor}
Camera: ${settings.cameraOrbit}
Auto-rotate: ${settings.autoRotate ? 'Yes' : 'No'}
Controls: ${settings.cameraControls ? 'Yes' : 'No'}
AR: ${settings.ar ? 'Yes' : 'No'}`;

	// Try to copy to clipboard
	if (navigator.clipboard) {
		navigator.clipboard.writeText(settingsText).then(() => {
			showAdminNotice('Settings copied to clipboard!', 'success');
		});
	} else {
		// Fallback for older browsers
		const textArea = document.createElement('textarea');
		textArea.value = settingsText;
		document.body.appendChild(textArea);
		textArea.select();
		document.execCommand('copy');
		document.body.removeChild(textArea);
		showAdminNotice('Settings copied to clipboard!', 'success');
	}
}

/**
 * Show admin notice
 */
function showAdminNotice(message, type = 'info') {
	const notice = jQuery(`<div class="notice notice-${type} is-dismissible wp3d-temp-notice"><p>${message}</p></div>`);
	jQuery('.wp-header-end').after(notice);
	
	setTimeout(() => {
		notice.fadeOut(() => notice.remove());
	}, 3000);
}

/**
 * Debounced auto-save function
 */
let autoSaveTimeout;
function debounceAutoSave() {
	clearTimeout(autoSaveTimeout);
	autoSaveTimeout = setTimeout(() => {
		if (typeof wp !== 'undefined' && wp.autosave && wp.autosave.server) {
			wp.autosave.server.triggerSave();
		}
	}, 1000);
}
