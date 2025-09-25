<?php

/**
 * Storage Preservation Script for Render Deployment
 * 
 * This script helps preserve uploaded files during deployment
 * by creating proper backup and restoration mechanisms.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StoragePreserver
{
    const PRESERVED_DIRECTORIES = [
        'avatars',
        'attachments',
        'settings'
    ];
    
    public static function backup()
    {
        echo "📦 Creating storage backup...\n";
        
        foreach (self::PRESERVED_DIRECTORIES as $directory) {
            $sourcePath = storage_path("app/public/{$directory}");
            $backupPath = storage_path("backup/{$directory}");
            
            if (is_dir($sourcePath)) {
                self::copyDirectory($sourcePath, $backupPath);
                echo "✅ Backed up {$directory}\n";
            }
        }
        
        // Backup database references
        $users = DB::table('users')
            ->whereNotNull('avatar')
            ->orWhereNotNull('profile_picture')
            ->select('id', 'avatar', 'profile_picture')
            ->get();
            
        file_put_contents(
            storage_path('backup/user_avatars.json'),
            json_encode($users->toArray(), JSON_PRETTY_PRINT)
        );
        
        echo "✅ Backed up user avatar references\n";
        echo "📦 Storage backup completed!\n";
    }
    
    public static function restore()
    {
        echo "📥 Restoring storage from backup...\n";
        
        foreach (self::PRESERVED_DIRECTORIES as $directory) {
            $backupPath = storage_path("backup/{$directory}");
            $targetPath = storage_path("app/public/{$directory}");
            
            if (is_dir($backupPath)) {
                self::copyDirectory($backupPath, $targetPath);
                echo "✅ Restored {$directory}\n";
            }
        }
        
        echo "📥 Storage restoration completed!\n";
    }
    
    public static function verify()
    {
        echo "🔍 Verifying storage integrity...\n";
        
        $userAvatars = json_decode(
            file_get_contents(storage_path('backup/user_avatars.json')), 
            true
        );
        
        $missingFiles = [];
        
        foreach ($userAvatars as $user) {
            $avatarPath = $user['avatar'] ?? null;
            if ($avatarPath && !Storage::disk('public')->exists($avatarPath)) {
                $missingFiles[] = $avatarPath;
            }
        }
        
        if (empty($missingFiles)) {
            echo "✅ All user profile pictures verified!\n";
        } else {
            echo "⚠️  Missing files:\n";
            foreach ($missingFiles as $file) {
                echo "   - {$file}\n";
            }
        }
        
        return empty($missingFiles);
    }
    
    private static function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $destPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                }
            } else {
                copy($item, $destPath);
            }
        }
    }
}

// Command line interface
if (isset($argv[1])) {
    switch ($argv[1]) {
        case 'backup':
            StoragePreserver::backup();
            break;
        case 'restore':
            StoragePreserver::restore();
            break;
        case 'verify':
            $isValid = StoragePreserver::verify();
            exit($isValid ? 0 : 1);
        default:
            echo "Usage: php preserve-storage.php [backup|restore|verify]\n";
            exit(1);
    }
} else {
    echo "Usage: php preserve-storage.php [backup|restore|verify]\n";
    exit(1);
}