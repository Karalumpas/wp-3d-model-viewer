(() => {
  // admin/js/src/cpt.js
  var loadModelViewerLibrary = () => {
    return new Promise((resolve, reject) => {
      if (window.customElements && window.customElements.get("model-viewer")) {
        resolve();
        return;
      }
      const script = document.createElement("script");
      script.type = "module";
      script.src = "https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js";
      script.onload = resolve;
      script.onerror = reject;
      document.head.appendChild(script);
    });
  };
  var initAdmin3DPreview = () => {
    const preview = document.getElementById("wp3d-admin-preview");
    const captureBtn = document.getElementById("wp3d-capture-position");
    const resetBtn = document.getElementById("wp3d-reset-position");
    const statusIndicator = document.getElementById("wp3d-status-indicator");
    const statusText = document.getElementById("wp3d-position-text");
    const cameraOrbitInput = document.getElementById("wp3d_camera_orbit");
    const cameraTargetInput = document.getElementById("wp3d_camera_target");
    if (!preview || !captureBtn || !resetBtn) {
      return;
    }
    let isModelLoaded = false;
    let originalOrbit = "0deg 75deg 105%";
    let originalTarget = "auto auto auto";
    if (cameraOrbitInput && cameraOrbitInput.value) {
      originalOrbit = cameraOrbitInput.value;
    }
    if (cameraTargetInput && cameraTargetInput.value) {
      originalTarget = cameraTargetInput.value;
    }
    preview.addEventListener("load", () => {
      isModelLoaded = true;
      statusText.textContent = "Model loaded. Position it and click capture.";
      captureBtn.disabled = false;
    });
    preview.addEventListener("error", () => {
      statusText.textContent = "Error loading model.";
      captureBtn.disabled = true;
      statusIndicator.className = "wp3d-status-indicator error";
    });
    preview.addEventListener("camera-change", () => {
      if (isModelLoaded) {
        statusIndicator.className = "wp3d-status-indicator modified";
        statusText.textContent = "Camera position changed. Click capture to save.";
      }
    });
    captureBtn.addEventListener("click", () => {
      if (!isModelLoaded) {
        alert("Please wait for the model to load before capturing position.");
        return;
      }
      try {
        const orbit = preview.getCameraOrbit();
        const target = preview.getCameraTarget();
        const orbitString = `${orbit.theta.toFixed(3)}rad ${orbit.phi.toFixed(3)}rad ${orbit.radius.toFixed(3)}m`;
        const targetString = `${target.x.toFixed(3)}m ${target.y.toFixed(3)}m ${target.z.toFixed(3)}m`;
        cameraOrbitInput.value = orbitString;
        cameraTargetInput.value = targetString;
        statusIndicator.className = "wp3d-status-indicator captured";
        statusText.textContent = "Position captured successfully!";
        captureBtn.style.background = "#46b450";
        captureBtn.style.borderColor = "#46b450";
        setTimeout(() => {
          captureBtn.style.background = "";
          captureBtn.style.borderColor = "";
        }, 1e3);
      } catch (error) {
        console.error("Error capturing camera position:", error);
        alert("Error capturing camera position. Please try again.");
      }
    });
    resetBtn.addEventListener("click", () => {
      if (!isModelLoaded) {
        alert("Please wait for the model to load before resetting position.");
        return;
      }
      try {
        preview.cameraOrbit = originalOrbit;
        preview.cameraTarget = originalTarget;
        cameraOrbitInput.value = originalOrbit;
        cameraTargetInput.value = originalTarget;
        statusIndicator.className = "wp3d-status-indicator";
        statusText.textContent = "Position reset to default.";
      } catch (error) {
        console.error("Error resetting camera position:", error);
        alert("Error resetting camera position. Please try again.");
      }
    });
    captureBtn.disabled = !isModelLoaded;
  };
  var initMediaUploader = () => {
    const $ = jQuery;
    const mediaUploaders = {};
    $(".wp3d-upload-button").on("click", function(e) {
      e.preventDefault();
      const button = $(this);
      const targetId = button.data("target");
      const title = button.data("title");
      const buttonText = button.data("button");
      if (mediaUploaders[targetId]) {
        mediaUploaders[targetId].open();
        return;
      }
      mediaUploaders[targetId] = wp.media.frames.file_frame = wp.media({
        title,
        button: {
          text: buttonText
        },
        multiple: false
      });
      mediaUploaders[targetId].on("select", function() {
        const attachment = mediaUploaders[targetId].state().get("selection").first().toJSON();
        $("#" + targetId).val(attachment.id);
        button.siblings(".wp3d-remove-button").show();
        updateFilePreview(targetId, attachment);
        if (targetId === "wp3d_model_file") {
          const preview = document.getElementById("wp3d-admin-preview");
          if (preview) {
            preview.src = attachment.url;
            preview.style.display = "block";
            location.reload();
          }
        }
      });
      mediaUploaders[targetId].open();
    });
    $(".wp3d-remove-button").on("click", function(e) {
      e.preventDefault();
      const button = $(this);
      const targetId = button.data("target");
      $("#" + targetId).val("");
      button.hide();
      $("#" + targetId + "_preview").empty();
      if (targetId === "wp3d_model_file") {
        const preview = document.getElementById("wp3d-admin-preview");
        if (preview) {
          preview.style.display = "none";
        }
      }
    });
    function updateFilePreview(targetId, attachment) {
      const preview = $("#" + targetId + "_preview");
      let html = "";
      if (attachment.type === "image") {
        html = '<img src="' + attachment.sizes.thumbnail.url + '" alt="' + attachment.alt + '" style="max-width: 150px; border-radius: 4px;" />';
      } else {
        html = "<p><strong>Selected file:</strong> " + attachment.filename + "</p>";
        html += "<p><small>" + attachment.url + "</small></p>";
      }
      preview.html(html);
    }
    $(".wp3d-upload-button").each(function() {
      const targetId = $(this).data("target");
      const fileId = $("#" + targetId).val();
      if (fileId) {
        $(this).siblings(".wp3d-remove-button").show();
      }
    });
  };
  jQuery(document).ready(function($) {
    loadModelViewerLibrary().then(() => {
      setTimeout(initAdmin3DPreview, 500);
    }).catch((error) => {
      console.error("Failed to load model-viewer library:", error);
    });
    initMediaUploader();
    $(document).on("click", ".wp-3d-model-viewer-shortcode code", function() {
      const text = $(this).text();
      if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
          const $code = $(this);
          const originalBg = $code.css("background-color");
          $code.css("background-color", "#46b450");
          setTimeout(function() {
            $code.css("background-color", originalBg);
          }, 500);
        });
      }
    });
    if ($("body").hasClass("post-type-3d_model")) {
      setTimeout(function() {
        $(".notice.is-dismissible").fadeOut();
      }, 3e3);
    }
  });
  console.log("Enhanced CPT JS loaded with 3D preview functionality");

  // admin/js/src/settings.js
  console.log("Settings JS loaded");

  // admin/js/src/admin.js
  console.log("WP 3D Model Viewer admin JS loaded");
})();
//# sourceMappingURL=wp-3d-model-viewer-admin.js.map
