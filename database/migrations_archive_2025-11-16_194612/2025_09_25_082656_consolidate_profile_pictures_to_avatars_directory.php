<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Log::info('Starting profile pictures consolidation migration');
        
        // Move files from profile-pictures to avatars directory
        if (Storage::disk('public')->exists('profile-pictures')) {
            $files = Storage::disk('public')->files('profile-pictures');
            
            foreach ($files as $file) {
                $filename = basename($file);
                $newPath = 'avatars/' . $filename;
                
                // Copy file to new location
                if (Storage::disk('public')->exists($file)) {
                    $content = Storage::disk('public')->get($file);
                    Storage::disk('public')->put($newPath, $content);
                    Log::info("Moved profile picture: {$file} -> {$newPath}");
                }
            }
        }
        
        // Update database records to use avatars directory
        DB::table('users')
            ->where('profile_picture', 'LIKE', 'profile-pictures/%')
            ->update([
                'avatar' => DB::raw("REPLACE(profile_picture, 'profile-pictures/', 'avatars/')"),
                'profile_picture' => DB::raw("REPLACE(profile_picture, 'profile-pictures/', 'avatars/')")
            ]);
            
        // Ensure users with existing avatars also have profile_picture set
        DB::table('users')
            ->whereNotNull('avatar')
            ->whereNull('profile_picture')
            ->update([
                'profile_picture' => DB::raw('avatar')
            ]);
            
        // Ensure users with existing profile_picture also have avatar set
        DB::table('users')
            ->whereNotNull('profile_picture')
            ->whereNull('avatar')
            ->update([
                'avatar' => DB::raw('profile_picture')
            ]);
            
        Log::info('Profile pictures consolidation migration completed');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Log::info('Reversing profile pictures consolidation migration');
        
        // Move files back from avatars to profile-pictures directory
        if (Storage::disk('public')->exists('avatars')) {
            $files = Storage::disk('public')->files('avatars');
            
            foreach ($files as $file) {
                if (strpos(basename($file), 'profile_') === 0) {
                    $filename = basename($file);
                    $oldPath = 'profile-pictures/' . $filename;
                    
                    // Copy file back to old location
                    if (Storage::disk('public')->exists($file)) {
                        $content = Storage::disk('public')->get($file);
                        Storage::disk('public')->put($oldPath, $content);
                        Log::info("Moved profile picture back: {$file} -> {$oldPath}");
                    }
                }
            }
        }
        
        // Update database records back to profile-pictures directory
        DB::table('users')
            ->where('profile_picture', 'LIKE', 'avatars/%')
            ->where('profile_picture', 'LIKE', '%profile_%')
            ->update([
                'profile_picture' => DB::raw("REPLACE(profile_picture, 'avatars/', 'profile-pictures/')")
            ]);
            
        Log::info('Profile pictures consolidation migration rollback completed');
    }
};
