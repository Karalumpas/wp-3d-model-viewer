# WP 3D Model Viewer - Troubleshooting Guide

## 🚨 3D Model Not Loading on Frontend

If your 3D model isn't displaying when you use the shortcode `[3d_model id="X"]`, follow these steps:

### **Step 1: Verify Basic Setup**

1. **Check Plugin Activation**: Ensure the plugin is active in `Plugins` → `Installed Plugins`
2. **Verify Model Upload**: Go to `3D Models` → Edit your model → Check that a GLB/GLTF file is uploaded
3. **Test Shortcode**: Make sure you're using `[3d_model id="123"]` (replace 123 with your actual model ID)

### **Step 2: Check Browser Console**

1. **Open Developer Tools**: Right-click → "Inspect Element" → "Console" tab
2. **Look for Errors**: Check for these common issues:
   ```
   - Failed to load resource: model-viewer.min.js
   - TypeError: Cannot read property 'src' of null
   - CORS error when loading .glb file
   - Mixed content error (HTTP vs HTTPS)
   ```

### **Step 3: Quick Fixes**

#### **✅ Model-Viewer Script Not Loading**
```html
<!-- If you see this in console: "model-viewer is not defined" -->
```
**Solution**: The model-viewer library isn't loading properly.

**Fix Options**:
1. **Clear Cache**: Clear all caching plugins and browser cache
2. **Check Network**: Ensure `https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js` is accessible
3. **Manual Load**: Add this to your theme's footer temporarily:
```html
<script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
```

#### **✅ CORS/File Access Issues**
```html
<!-- If you see: "Access to fetch at 'model.glb' from origin blocked by CORS" -->
```
**Solution**: File permissions or server configuration issue.

**Fix Options**:
1. **Re-upload Model**: Delete and re-upload your GLB/GLTF file
2. **Check File URL**: Ensure the file URL is accessible directly in browser
3. **Server Config**: Add to `.htaccess`:
```apache
<IfModule mod_headers.c>
    <FilesMatch "\.(glb|gltf)$">
        Header set Access-Control-Allow-Origin "*"
    </FilesMatch>
</IfModule>
```

#### **✅ Mixed Content (HTTP/HTTPS) Issues**
```html
<!-- If you see: "Mixed Content: The page at 'https://' was loaded over HTTPS" -->
```
**Solution**: Ensure all resources use HTTPS.

**Fix**: Update WordPress URLs in `Settings` → `General` to use HTTPS

### **Step 4: Debug Mode**

Enable WordPress debug mode to see detailed error information:

1. **Edit wp-config.php**:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);
```

2. **Check Debug Output**: Look for HTML comments in page source:
```html
<!-- WP 3D Model Viewer Debug Info:
Model ID: 123
Model URL: https://yoursite.com/wp-content/uploads/model.glb
Camera Orbit: 0deg 75deg 105%
... -->
```

### **Step 5: Test with Direct URL**

Create a test shortcode with direct model URL:
```
[3d_model_viewer src="https://modelviewer.dev/shared-assets/models/Astronaut.glb" width="100%" height="400px"]
```

If this works but your uploaded model doesn't, the issue is with your uploaded file.

### **Step 6: Common File Issues**

#### **✅ File Format Problems**
- **Use GLB format** (recommended over GLTF)
- **File size** should be under 50MB for best performance
- **Test your model** at https://modelviewer.dev/ first

#### **✅ File Upload Issues**
1. **Check WordPress Upload Limits**:
   - Go to `Media` → `Add New` → Check "Maximum upload file size"
   - If too small, increase in hosting control panel or wp-config.php

2. **Allowed File Types**:
   Add to functions.php:
   ```php
   function add_3d_model_upload_mimes($mimes) {
       $mimes['glb'] = 'model/gltf-binary';
       $mimes['gltf'] = 'model/gltf+json';
       return $mimes;
   }
   add_filter('upload_mimes', 'add_3d_model_upload_mimes');
   ```

### **Step 7: Theme Compatibility**

Some themes may conflict with model-viewer. Test with:

1. **Default Theme**: Temporarily switch to Twenty Twenty-Three
2. **Plugin Conflicts**: Deactivate other plugins temporarily
3. **JavaScript Conflicts**: Look for console errors from other scripts

### **Step 8: Server Requirements**

Ensure your server supports:
- ✅ **PHP 7.4+**
- ✅ **WordPress 5.0+**
- ✅ **Modern browsers** (Chrome 67+, Firefox 60+, Safari 12+)
- ✅ **HTTPS** (required for AR features)

### **Step 9: Advanced Debugging**

#### **Check Generated HTML**
View page source and look for:
```html
<model-viewer src="..." camera-controls>
  <!-- Should contain model-viewer element -->
</model-viewer>
```

#### **Check Script Loading Order**
In browser developer tools → Network tab:
1. ✅ `model-viewer.min.js` loads first
2. ✅ `wp-3d-model-viewer-public.js` loads after
3. ✅ Your model file (.glb) loads when needed

### **Step 10: Getting Help**

If none of these steps work:

1. **Copy Error Messages**: From browser console
2. **Check WordPress Error Log**: In `/wp-content/debug.log`
3. **Test Environment**: Try on a staging site
4. **Provide Debug Info**: Model ID, WordPress version, theme name, error messages

### **Quick Reference: Working Shortcode Examples**

```html
<!-- Basic CPT Model -->
[3d_model id="123"]

<!-- Direct URL Model -->
[3d_model_viewer src="https://example.com/model.glb"]

<!-- Full-Featured Model -->
[3d_model_viewer 
    src="https://example.com/model.glb" 
    width="100%" 
    height="500px" 
    background_color="#ffffff" 
    auto_rotate="true" 
    camera_controls="true" 
    ar="false"]
```

### **Performance Tips**

1. **Optimize Models**: Keep under 5MB if possible
2. **Use GLB**: Better compression than GLTF
3. **Enable Lazy Loading**: Models load when scrolled into view
4. **Add Poster Images**: Shows while model loads

---

## ✅ **Most Common Solution**

**90% of loading issues are resolved by**:
1. Clearing all caches (plugin + browser)
2. Re-uploading the 3D model file
3. Checking browser console for specific error messages

Still having issues? The problem is likely browser/server-specific rather than plugin-specific.
