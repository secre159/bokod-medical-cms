<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CleanupProfilePictures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profile:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned profile pictures and old unused files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting profile picture cleanup...');
        $isDryRun = $this->option('dry-run');
        
        // Get all profile picture files from storage
        $storageFiles = collect(Storage::disk('public')->files('profile-pictures'));
        
        // Get all profile pictures referenced in database
        $dbPictures = DB::table('users')
            ->whereNotNull('profile_picture')
            ->pluck('profile_picture')
            ->filter()
            ->toArray();
        
        // Find orphaned files (files not referenced in database)
        $orphanedFiles = $storageFiles->reject(function ($file) use ($dbPictures) {
            return in_array($file, $dbPictures);
        });
        
        $totalSize = 0;
        $fileCount = 0;
        
        foreach ($orphanedFiles as $file) {
            $size = Storage::disk('public')->size($file);
            $totalSize += $size;
            $fileCount++;
            
            if ($isDryRun) {
                $this->line("Would delete: {$file} (" . $this->formatBytes($size) . ")");
            } else {
                Storage::disk('public')->delete($file);
                $this->line("Deleted: {$file} (" . $this->formatBytes($size) . ")");
            }
        }
        
        $action = $isDryRun ? 'Would clean up' : 'Cleaned up';
        $this->info("{$action} {$fileCount} orphaned files, freeing " . $this->formatBytes($totalSize) . " of storage.");
        
        // Show current storage usage
        $currentFiles = Storage::disk('public')->files('profile-pictures');
        $currentSize = array_sum(array_map(function ($file) {
            return Storage::disk('public')->size($file);
        }, $currentFiles));
        
        $this->info("Current profile pictures storage: " . count($currentFiles) . " files, " . $this->formatBytes($currentSize));
        
        if ($isDryRun) {
            $this->warn('This was a dry run. Use --no-dry-run to actually delete files.');
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
