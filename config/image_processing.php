<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Profile Picture Settings
    |--------------------------------------------------------------------------
    |
    | Configure profile picture processing settings to optimize storage usage
    | and maintain consistent image quality across the application.
    |
    */
    
    'profile_pictures' => [
        // Maximum dimensions for profile pictures (maintains aspect ratio)
        'max_width' => 400,
        'max_height' => 400,
        
        // JPEG quality (1-100, 85 is a good balance of quality/size)
        'jpeg_quality' => 85,
        
        // File size limits
        'max_upload_size' => 1024 * 1024, // 1MB in bytes
        'max_validation_size' => 1024, // 1MB for Laravel validation (in kilobytes)
        
        // Allowed file types
        'allowed_mimes' => ['jpeg', 'png', 'jpg', 'gif', 'webp'],
        'allowed_extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
        
        // Storage settings
        'directory' => 'profile-pictures',
        'disk' => 'public',
        
        // Processing settings
        'convert_to_jpeg' => true, // Convert all images to JPEG for consistency
        'filename_pattern' => 'profile_{patient_id}_{timestamp}_{random}.jpg',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Storage Management
    |--------------------------------------------------------------------------
    |
    | Settings for managing storage space and cleaning up old files
    |
    */
    
    'storage' => [
        // Enable automatic cleanup of old profile pictures when uploading new ones
        'auto_cleanup_old' => true,
        
        // Maximum total storage for profile pictures (in MB)
        // Set to 0 to disable limit
        'max_total_storage_mb' => 500, // 500MB total for all profile pictures
        
        // Cleanup settings
        'cleanup' => [
            // Delete orphaned files (files not referenced in database)
            'delete_orphaned' => true,
            
            // Days to keep orphaned files before deletion (0 = delete immediately)
            'orphaned_retention_days' => 7,
        ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Image Processing Library
    |--------------------------------------------------------------------------
    |
    | Configure the image processing library settings
    |
    */
    
    'processing' => [
        // Image processing driver (gd, imagick)
        'driver' => 'gd',
        
        // Enable/disable image optimization
        'optimize' => true,
        
        // Enable/disable progressive JPEG
        'progressive' => true,
    ],
];