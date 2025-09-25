<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for Cloudinary, a cloud service
    | that offers a solution to a web application's entire image management
    | pipeline.
    |
    | To get started, you need to add your credentials in your .env file.
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key' => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure' => env('CLOUDINARY_SECURE_URL', true),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Upload Presets
    |--------------------------------------------------------------------------
    |
    | Upload presets allow you to define the default behavior for your uploads.
    | They can be defined on your Cloudinary console and referenced here.
    |
    */

    'upload_preset' => [
        'default' => env('CLOUDINARY_UPLOAD_PRESET'),
        'profile_pictures' => env('CLOUDINARY_PROFILE_PRESET', 'profile_pictures'),
        'system_assets' => env('CLOUDINARY_SYSTEM_PRESET', 'system_assets'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Settings
    |--------------------------------------------------------------------------
    |
    | Default settings for uploads
    |
    */

    'defaults' => [
        'folder' => env('CLOUDINARY_DEFAULT_FOLDER', 'bokod_cms'),
        'resource_type' => 'auto',
        'quality' => 'auto:good',
        'fetch_format' => 'auto',
    ],

    /*
    |--------------------------------------------------------------------------
    | Archive Settings
    |--------------------------------------------------------------------------
    |
    | Configure archiving behavior
    |
    */

    'archive' => [
        'create_archive' => env('CLOUDINARY_CREATE_ARCHIVE', false),
        'keep_derived' => env('CLOUDINARY_KEEP_DERIVED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification URL
    |--------------------------------------------------------------------------
    |
    | URL to receive upload notifications
    |
    */

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Folders Configuration
    |--------------------------------------------------------------------------
    |
    | Configure specific folders for different types of uploads
    |
    */

    'folders' => [
        'profile_pictures' => 'profile_pictures',
        'system_assets' => 'system',
        'settings' => 'settings',
        'avatars' => 'avatars',
    ],
];