/**
 * WP 3D Model Viewer - CPT Admin JavaScript
 * Handles custom post type admin functionality including interactive 3D preview
 * 
 * @package WP_3D_Model_Viewer
 * @version 2.0.0
 */

// Load model-viewer library for admin preview
const loadModelViewerLibrary = () => {
    return new Promise((resolve, reject) => {
        if (window.customElements && window.customElements.get('model-viewer')) {
            resolve();
            return;
        }

        const script = document.createElement('script');
        script.type = 'module';
        script.src = 'https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

// Initialize admin 3D preview functionality
const initAdmin3DPreview = () => {
    const preview = document.getElementById('wp3d-admin-preview');
    const captureBtn = document.getElementById('wp3d-capture-position');
    const resetBtn = document.getElementById('wp3d-reset-position');
    const statusIndicator = document.getElementById('wp3d-status-indicator');
    const statusText = document.getElementById('wp3d-position-text');
    const cameraOrbitInput = document.getElementById('wp3d_camera_orbit');
    const cameraTargetInput = document.getElementById('wp3d_camera_target');

    if (!preview || !captureBtn || !resetBtn) {
        return;
    }

    let isModelLoaded = false;
    let originalOrbit = '0deg 75deg 105%';
    let originalTarget = 'auto auto auto';

    // Store original values
    if (cameraOrbitInput && cameraOrbitInput.value) {
        originalOrbit = cameraOrbitInput.value;
    }
    if (cameraTargetInput && cameraTargetInput.value) {
        originalTarget = cameraTargetInput.value;
    }

    // Wait for model to load
    preview.addEventListener('load', () => {
        isModelLoaded = true;
        statusText.textContent = 'Model loaded. Position it and click capture.';
        captureBtn.disabled = false;
    });

    preview.addEventListener('error', () => {
        statusText.textContent = 'Error loading model.';
        captureBtn.disabled = true;
        statusIndicator.className = 'wp3d-status-indicator error';
    });

    // Track camera changes
    preview.addEventListener('camera-change', () => {
        if (isModelLoaded) {
            statusIndicator.className = 'wp3d-status-indicator modified';
            statusText.textContent = 'Camera position changed. Click capture to save.';
        }
    });

    // Capture current position
    captureBtn.addEventListener('click', () => {
        if (!isModelLoaded) {
            alert('Please wait for the model to load before capturing position.');
            return;
        }

        try {
            const orbit = preview.getCameraOrbit();
            const target = preview.getCameraTarget();

            // Format orbit: theta phi radius
            const orbitString = `${orbit.theta.toFixed(3)}rad ${orbit.phi.toFixed(3)}rad ${orbit.radius.toFixed(3)}m`;
            
            // Format target: x y z
            const targetString = `${target.x.toFixed(3)}m ${target.y.toFixed(3)}m ${target.z.toFixed(3)}m`;

            // Update form fields
            cameraOrbitInput.value = orbitString;
            cameraTargetInput.value = targetString;

            // Update status
            statusIndicator.className = 'wp3d-status-indicator captured';
            statusText.textContent = 'Position captured successfully!';

            // Show success feedback
            captureBtn.style.background = '#46b450';
            captureBtn.style.borderColor = '#46b450';
            setTimeout(() => {
                captureBtn.style.background = '';
                captureBtn.style.borderColor = '';
            }, 1000);

        } catch (error) {
            console.error('Error capturing camera position:', error);
            alert('Error capturing camera position. Please try again.');
        }
    });

    // Reset to default position
    resetBtn.addEventListener('click', () => {
        if (!isModelLoaded) {
            alert('Please wait for the model to load before resetting position.');
            return;
        }

        try {
            // Reset to original position
            preview.cameraOrbit = originalOrbit;
            preview.cameraTarget = originalTarget;

            // Update form fields
            cameraOrbitInput.value = originalOrbit;
            cameraTargetInput.value = originalTarget;

            // Update status
            statusIndicator.className = 'wp3d-status-indicator';
            statusText.textContent = 'Position reset to default.';

        } catch (error) {
            console.error('Error resetting camera position:', error);
            alert('Error resetting camera position. Please try again.');
        }
    });

    // Initial state
    captureBtn.disabled = !isModelLoaded;
};

// Enhanced media uploader functionality
const initMediaUploader = () => {
    const $ = jQuery;
    const mediaUploaders = {};

    $('.wp3d-upload-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const targetId = button.data('target');
        const title = button.data('title');
        const buttonText = button.data('button');
        
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
            const attachment = mediaUploaders[targetId].state().get('selection').first().toJSON();
            
            // Set the attachment ID to the hidden field
            $('#' + targetId).val(attachment.id);
            
            // Show the remove button
            button.siblings('.wp3d-remove-button').show();
            
            // Update the preview
            updateFilePreview(targetId, attachment);

            // If this is the model file and we have a preview, update it
            if (targetId === 'wp3d_model_file') {
                const preview = document.getElementById('wp3d-admin-preview');
                if (preview) {
                    preview.src = attachment.url;
                    preview.style.display = 'block';
                    // Refresh the preview section
                    location.reload();
                }
            }
        });
        
        // Finally, open the modal
        mediaUploaders[targetId].open();
    });

    // Remove file functionality
    $('.wp3d-remove-button').on('click', function(e) {
        e.preventDefault();
        
        const button = $(this);
        const targetId = button.data('target');
        
        // Clear the hidden field
        $('#' + targetId).val('');
        
        // Hide the remove button
        button.hide();
        
        // Clear the preview
        $('#' + targetId + '_preview').empty();

        // If this is the model file, hide the preview
        if (targetId === 'wp3d_model_file') {
            const preview = document.getElementById('wp3d-admin-preview');
            if (preview) {
                preview.style.display = 'none';
            }
        }
    });

    /**
     * Update file preview based on attachment type
     */
    function updateFilePreview(targetId, attachment) {
        const preview = $('#' + targetId + '_preview');
        let html = '';
        
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
        const targetId = $(this).data('target');
        const fileId = $('#' + targetId).val();
        
        if (fileId) {
            $(this).siblings('.wp3d-remove-button').show();
        }
    });
};

// Initialize everything when DOM is ready
jQuery(document).ready(function($) {
    // Load model-viewer library and initialize preview
    loadModelViewerLibrary()
        .then(() => {
            // Wait a bit for the library to fully initialize
            setTimeout(initAdmin3DPreview, 500);
        })
        .catch(error => {
            console.error('Failed to load model-viewer library:', error);
        });

    // Initialize media uploader
    initMediaUploader();

    // Copy shortcode functionality for list table
    $(document).on('click', '.wp-3d-model-viewer-shortcode code', function() {
        const text = $(this).text();
        
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                // Visual feedback
                const $code = $(this);
                const originalBg = $code.css('background-color');
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

console.log('Enhanced CPT JS loaded with 3D preview functionality');