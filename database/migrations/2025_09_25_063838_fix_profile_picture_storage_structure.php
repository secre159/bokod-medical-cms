<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration will standardize profile picture paths
        // and create the necessary storage directory structure
        
        // Ensure the profile_pictures directory exists
        $publicPath = storage_path('app/public');
        $profilePicturesPath = $publicPath . '/profile_pictures';
        
        if (!file_exists($profilePicturesPath)) {
            mkdir($profilePicturesPath, 0755, true);
        }
        
        // Update existing profile picture paths to use new standardized format
        // This will be handled by the ProfilePictureService
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse directory creation
    }
};
