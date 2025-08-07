# WP 3D Model Viewer - Troubleshooting

## Common Installation Issues

### Issue: "Failed to open stream: No such file or directory"

If you're encountering an error like:
```
PHP Warning: require(/path/to/wp-content/plugins/wp-3d-model-viewer/includes/class-wp-3d-model-viewer.php): Failed to open stream: No such file or directory
```

This usually indicates one of the following problems:

#### 1. Incomplete Plugin Upload
The most common cause is that the plugin files were not completely uploaded to your WordPress installation. 

**Solution:**
1. Delete the existing plugin folder from `/wp-content/plugins/`
2. Re-upload the complete plugin package
3. Ensure all files and folders are present:
   - `wp-3d-model-viewer.php` (main plugin file)
   - `includes/` folder with all class files
   - `admin/` folder with admin functionality
   - `public/` folder with frontend functionality

#### 2. Incorrect Folder Name
The error path shows a folder name like `wp-3d-model-viewer-v2.0.0-fixed-1` but the plugin expects to be in a folder named `wp-3d-model-viewer`.

**Solution:**
1. Rename the plugin folder to exactly `wp-3d-model-viewer`
2. OR re-upload the plugin properly through WordPress admin

#### 3. File Permissions
Sometimes file permissions can prevent WordPress from accessing the plugin files.

**Solution:**
1. Set folder permissions to 755: `chmod 755 wp-3d-model-viewer/`
2. Set file permissions to 644: `chmod 644 wp-3d-model-viewer/*.php`
3. Apply recursively to all subfolders

#### 4. Corrupted Files
Files may have been corrupted during upload or extraction.

**Solution:**
1. Download a fresh copy of the plugin
2. Delete the existing plugin folder completely
3. Upload the fresh copy

## Enhanced Error Handling

This version of the plugin includes enhanced error handling that will:
- Check for file existence before requiring them
- Display helpful error messages in the WordPress admin
- Gracefully handle missing dependencies
- Provide better debugging information

## Installation Steps

1. **Download**: Get the latest plugin files
2. **Upload**: Upload to `/wp-content/plugins/wp-3d-model-viewer/`
3. **Verify**: Ensure all files are present (see file list below)
4. **Activate**: Activate the plugin through WordPress admin

## Required Files Structure

```
wp-3d-model-viewer/
├── wp-3d-model-viewer.php
├── includes/
│   ├── class-wp-3d-model-viewer.php
│   ├── class-wp-3d-model-viewer-activator.php
│   ├── class-wp-3d-model-viewer-deactivator.php
│   ├── class-wp-3d-model-viewer-loader.php
│   ├── class-wp-3d-model-viewer-i18n.php
│   └── class-wp-3d-model-viewer-cpt.php
├── admin/
│   ├── class-wp-3d-model-viewer-admin.php
│   └── partials/
│       └── wp-3d-model-viewer-admin-display.php
└── public/
    └── class-wp-3d-model-viewer-public.php
```

## Debug Mode

If you're still having issues, you can use the included `debug-plugin.php` file:
1. Upload `debug-plugin.php` to your plugin folder
2. Access it via your WordPress admin or directly in browser
3. It will show you exactly which files are missing or inaccessible

## Support

If you continue to experience issues after following these steps:
1. Check your web server's error logs
2. Verify PHP version compatibility (requires PHP 7.4+)
3. Ensure WordPress version compatibility (requires WP 5.0+)
4. Contact your hosting provider if file permission issues persist
