<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class StorageReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a report of storage usage for profile pictures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗂️  Storage Usage Report');
        $this->line('');
        
        // Profile Pictures Analysis
        $profilePicturesPath = 'profile-pictures';
        $files = Storage::disk('public')->files($profilePicturesPath);
        
        $totalSize = 0;
        $fileCount = count($files);
        
        foreach ($files as $file) {
            $totalSize += Storage::disk('public')->size($file);
        }
        
        // Get user counts
        $totalUsers = DB::table('users')->where('role', 'patient')->count();
        $usersWithPictures = DB::table('users')
            ->where('role', 'patient')
            ->whereNotNull('profile_picture')
            ->count();
        
        // Calculate statistics
        $avgSizePerFile = $fileCount > 0 ? $totalSize / $fileCount : 0;
        $usagePercentage = $totalUsers > 0 ? ($usersWithPictures / $totalUsers) * 100 : 0;
        
        // Configuration limits
        $maxTotalStorage = config('image_processing.storage.max_total_storage_mb', 500) * 1024 * 1024;
        $usagePercentageOfLimit = $maxTotalStorage > 0 ? ($totalSize / $maxTotalStorage) * 100 : 0;
        
        // Display results
        $this->table(['Metric', 'Value'], [
            ['Total Files', number_format($fileCount)],
            ['Total Storage Used', $this->formatBytes($totalSize)],
            ['Average File Size', $this->formatBytes($avgSizePerFile)],
            ['Storage Limit', $maxTotalStorage > 0 ? $this->formatBytes($maxTotalStorage) : 'No limit'],
            ['Usage vs Limit', $maxTotalStorage > 0 ? round($usagePercentageOfLimit, 2) . '%' : 'N/A'],
            ['Total Patients', number_format($totalUsers)],
            ['Patients with Pictures', number_format($usersWithPictures)],
            ['Upload Rate', round($usagePercentage, 2) . '%'],
        ]);
        
        // Warnings
        if ($usagePercentageOfLimit > 80) {
            $this->warn('⚠️  Storage usage is above 80% of the configured limit!');
        }
        
        if ($avgSizePerFile > 100 * 1024) { // 100KB
            $this->warn('⚠️  Average file size is high. Consider optimizing image processing settings.');
        }
        
        // Projections
        $this->line('');
        $this->info('📊 Projections:');
        
        $projectedStorageFor1000Users = $avgSizePerFile * 1000;
        $projectedStorageFor5000Users = $avgSizePerFile * 5000;
        
        $this->line('• Storage needed for 1,000 users: ' . $this->formatBytes($projectedStorageFor1000Users));
        $this->line('• Storage needed for 5,000 users: ' . $this->formatBytes($projectedStorageFor5000Users));
        
        if ($fileCount > 0) {
            $monthlyGrowth = $fileCount; // Assume current files represent recent growth
            $projectedMonthlyStorage = $avgSizePerFile * $monthlyGrowth;
            $this->line('• Estimated monthly growth: ' . $this->formatBytes($projectedMonthlyStorage));
        }
        
        $this->line('');
        $this->info('💡 Tips for optimization:');
        $this->line('• Run "php artisan profile:cleanup --dry-run" to see orphaned files');
        $this->line('• Adjust image quality in config/image_processing.php');
        $this->line('• Consider implementing image CDN for large deployments');
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
