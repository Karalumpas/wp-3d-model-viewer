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
		}
	};

})( jQuery );
