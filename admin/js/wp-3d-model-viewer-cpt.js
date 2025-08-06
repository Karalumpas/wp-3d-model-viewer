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

	});

})( jQuery );
