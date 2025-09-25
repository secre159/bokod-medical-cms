<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ProfilePictureService;

class CleanupProfilePicturesNew extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'profile:cleanup-pictures {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up orphaned profile pictures that are no longer referenced by users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting profile picture cleanup...');
        
        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No files will be deleted');
        }
        
        try {
            if ($this->option('dry-run')) {
                // Show what would be cleaned up
                $orphanedCount = $this->countOrphanedFiles();
                $this->info("Would delete {$orphanedCount} orphaned profile pictures");
            } else {
                $cleanedCount = ProfilePictureService::cleanupOrphanedPictures();
                $this->info("Cleaned up {$cleanedCount} orphaned profile pictures");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            return 1;
        }
    }
    
    private function countOrphanedFiles()
    {
        $allFiles = \Storage::disk('public')->files(ProfilePictureService::STORAGE_PATH);
        $activeFiles = \App\Models\User::whereNotNull('profile_picture')->pluck('profile_picture')->toArray();
        
        return count(array_diff($allFiles, $activeFiles));
    }
}