<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Backup Method Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration determines how database backups are created and restored.
    | The system will auto-detect the environment but you can override it here.
    |
    | Supported methods: 'auto', 'mysql', 'php'
    | - auto: Automatically detect based on environment
    | - mysql: Use mysqldump/mysql command line tools (localhost/VPS)
    | - php: Use PHP-based backup/restore (shared hosting)
    |
    */

    'method' => env('BACKUP_METHOD', 'auto'),

    /*
    |--------------------------------------------------------------------------
    | Hosted Environment Indicators
    |--------------------------------------------------------------------------
    |
    | These settings help the system detect if it's running in a hosted
    | environment where command-line MySQL tools may not be available.
    |
    */

    'hosted_indicators' => [
        // Domain patterns that suggest hosted environment
        'domain_patterns' => [
            '*.hostinger.com',
            '*.cpanel.com',
            '*.shared.com',
            '*.bluehost.com',
            '*.godaddy.com',
            '*.siteground.com',
            '*.namecheap.com',
        ],
        
        // Server software that suggests shared hosting
        'server_software' => [
            'LiteSpeed',
            'cPanel',
            'Plesk',
        ],
        
        // Environment variables that indicate hosting
        'env_variables' => [
            'CPANEL_USER',
            'SHARED_HOST',
            'HOSTING_PROVIDER',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Backup Storage Configuration
    |--------------------------------------------------------------------------
    |
    | Configure where backups are stored and how they're managed.
    |
    */

    'storage' => [
        'disk' => 'local',
        'path' => 'backups',
        'max_backups' => 10, // Keep only the latest 10 backups
        'compress' => false, // Whether to compress backup files
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety Settings
    |--------------------------------------------------------------------------
    |
    | Safety features for backup and restore operations.
    |
    */

    'safety' => [
        'auto_safety_backup' => true,
        'verify_backup_integrity' => true,
        'max_restore_time' => 300, // 5 minutes
        'require_confirmation' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Hosting Provider Specific Settings
    |--------------------------------------------------------------------------
    |
    | Special configurations for popular hosting providers.
    |
    */

    'hosting_providers' => [
        'cpanel' => [
            'mysql_path' => '/usr/bin/mysql',
            'mysqldump_path' => '/usr/bin/mysqldump',
            'force_php_method' => false,
        ],
        
        'shared_hosting' => [
            'mysql_path' => null,
            'mysqldump_path' => null,
            'force_php_method' => true,
        ],
        
        'vps' => [
            'mysql_path' => '/usr/bin/mysql',
            'mysqldump_path' => '/usr/bin/mysqldump',
            'force_php_method' => false,
        ],
    ],

];