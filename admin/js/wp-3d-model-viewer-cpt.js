/**
 * Admin JavaScript for 3D Model CPT functionality
 *
 * @link       https://github.com/Karalumpas/wp-3d-model-viewer
 * @since      1.0.0
 *
 * @package    WP_3D_Model_Viewer
 * @subpackage WP_3D_Model_Viewer/admin/js
 */

(function( $ ) {
	'use strict';

    $(document).ready(function() {
		
		// Media uploader functionality
		var mediaUploaders = {};

		$('.wp3d-upload-button').on('click', function(e) {
			e.preventDefault();
			
			var button = $(this);
			var targetId = button.data('target');
			var title = button.data('title');
			var buttonText = button.data('button');
			
			// If the media frame already exists, reopen it
			if (mediaUploaders[targetId]) {
				mediaUploaders[targetId].open();
				return;
			}
			
			// Create the media frame
			mediaUploaders[targetId] = wp.media.frames.file_frame = wp.media({
				title: title,
				button: {
					text: buttonText
				},
				multiple: false
			});
			
			// When an image is selected, run a callback
			mediaUploaders[targetId].on('select', function() {
				var attachment = mediaUploaders[targetId].state().get('selection').first().toJSON();
				
				// Set the attachment ID to the hidden field
				$('#' + targetId).val(attachment.id);
				
				// Show the remove button
				button.siblings('.wp3d-remove-button').show();
				
				// Update the preview
				updateFilePreview(targetId, attachment);
			});
			
			// Finally, open the modal
			mediaUploaders[targetId].open();
		});

		// Remove file functionality
		$('.wp3d-remove-button').on('click', function(e) {
			e.preventDefault();
			
			var button = $(this);
			var targetId = button.data('target');
			
			// Clear the hidden field
			$('#' + targetId).val('');
			
			// Hide the remove button
			button.hide();
			
			// Clear the preview
			$('#' + targetId + '_preview').empty();
		});

		/**
		 * Update file preview based on attachment type
		 */
		function updateFilePreview(targetId, attachment) {
			var preview = $('#' + targetId + '_preview');
			var html = '';
			
			if (attachment.type === 'image') {
				// Show image thumbnail
				html = '<img src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.alt + '" style="max-width: 150px; border-radius: 4px;" />';
			} else {
				// Show file info for non-images
				html = '<p><strong>Selected file:</strong> ' + attachment.filename + '</p>';
				html += '<p><small>' + attachment.url + '</small></p>';
			}
			
			preview.html(html);
		}

		// Initialize existing file previews
		$('.wp3d-upload-button').each(function() {
			var targetId = $(this).data('target');
			var fileId = $('#' + targetId).val();
			
			if (fileId) {
				$(this).siblings('.wp3d-remove-button').show();
			}
		});

		// Copy shortcode functionality for list table
		$(document).on('click', '.wp-3d-model-viewer-shortcode code', function() {
			var text = $(this).text();
			
			if (navigator.clipboard) {
				navigator.clipboard.writeText(text).then(function() {
					// Visual feedback
					var $code = $(this);
					var originalBg = $code.css('background-color');
					$code.css('background-color', '#46b450');
					setTimeout(function() {
						$code.css('background-color', originalBg);
					}, 500);
				});
			}
		});

        // Auto-hide WordPress notices for better UX in 3D model admin
		if ($('body').hasClass('post-type-3d_model')) {
			setTimeout(function() {
				$('.notice.is-dismissible').fadeOut();
			}, 3000);
		}

        // Live preview: reflect metabox changes without saving
        if ($('body').hasClass('post-type-3d_model')) {
            // Try to locate or create a preview container with a model-viewer
            let $preview = $('#wp3d-live-preview');
            if ($preview.length === 0) {
                $preview = $('<div id="wp3d-live-preview" class="wp3d-live-preview" style="margin-top:16px;"></div>');
                // Insert after the model settings metabox
                const $settingsBox = $('#wp3d-model-settings').closest('.postbox');
                if ($settingsBox.length) {
                    $settingsBox.after($preview);
                } else {
                    $('.wrap h1').after($preview);
                }
            }

            // Build or update preview element
            function ensurePreviewElement() {
                let $viewer = $preview.find('model-viewer');
                if ($viewer.length === 0) {
                    $viewer = $('<model-viewer class="wp3d-viewer" style="width:100%; height:400px; background-color:#ffffff;" camera-controls></model-viewer>');
                    $preview.empty().append('<h2>Live preview</h2>').append($viewer);
                }
                return $viewer;
            }

            function getSelectedModelUrl() {
                const id = $('#wp3d_model_file').val();
                if (!id) return '';
                // Try pulling URL from existing preview markup if present
                const urlEl = $('#wp3d_model_file_preview small');
                return urlEl.length ? urlEl.text() : '';
            }

            function getPosterUrl() {
                const id = $('#wp3d_poster_image').val();
                if (!id) return '';
                const img = $('#wp3d_poster_image_preview img');
                return img.length ? img.attr('src') : '';
            }

            function refreshPreview() {
                const $viewer = ensurePreviewElement();

                const modelUrl = getSelectedModelUrl();
                const bg = $('#wp3d_bg_color').val() || '#ffffff';
                const startOrbit = ($('#wp3d_start_rotation').val() || '').trim();
                const zoom = parseFloat($('#wp3d_zoom_level').val() || '1');
                const arEnabled = $('#wp3d_ar_enabled').is(':checked');
                const autoRotate = $('#wp3d_auto_rotate').is(':checked');
                const cameraControls = $('#wp3d_camera_controls').is(':checked');
                const posterUrl = getPosterUrl();

                if (modelUrl) $viewer.attr('src', modelUrl); else $viewer.removeAttr('src');
                $viewer.css('background-color', bg);

                if (startOrbit) $viewer.attr('camera-orbit', startOrbit); else $viewer.removeAttr('camera-orbit');
                // zoom -> approximate with field-of-view equivalents is not directly supported; leave for now

                if (autoRotate) $viewer.attr('auto-rotate', ''); else $viewer.removeAttr('auto-rotate');
                if (cameraControls) $viewer.attr('camera-controls', ''); else $viewer.removeAttr('camera-controls');

                if (arEnabled) {
                    $viewer.attr('ar', '');
                    $viewer.attr('ar-modes', 'webxr scene-viewer quick-look');
                } else {
                    $viewer.removeAttr('ar');
                    $viewer.removeAttr('ar-modes');
                }

                if (posterUrl) $viewer.attr('poster', posterUrl); else $viewer.removeAttr('poster');

                // Force re-render by toggling reveal
                $viewer.attr('reveal', 'interaction');
                // If model-viewer is defined, request an update
                try { if ($viewer[0] && $viewer[0].updateFraming) { $viewer[0].updateFraming(); } } catch(e) {}
            }

            // Bind inputs to refresh
            $(document).on('input change', '#wp3d_bg_color, #wp3d_start_rotation, #wp3d_zoom_level, #wp3d_ar_enabled, #wp3d_auto_rotate, #wp3d_camera_controls', refreshPreview);

            // Also refresh after media selections
            $(document).on('click', '.wp3d-upload-button, .wp3d-remove-button', function() {
                // Let media modal finish selection
                setTimeout(refreshPreview, 50);
            });

            // Initialize after model-viewer custom element is ready
            if (window.customElements && window.customElements.whenDefined) {
                window.customElements.whenDefined('model-viewer').then(refreshPreview);
            } else {
                refreshPreview();
            }
        }

	});

})( jQuery );
