<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
// use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Services\ImgBBService;

class ProfilePictureService
{
    const STORAGE_PATH = 'avatars';
    const DEFAULT_SIZE = 200;
    const THUMBNAIL_SIZE = 50;
    
    /**
     * Get the profile picture URL for a user with fallback to initials
     */
    public static function getProfilePictureUrl($user, $size = 'default')
    {
        if (!$user) {
            return self::getDefaultAvatarUrl('??', $size);
        }

        // Check for existing profile picture
        $imagePath = self::getUserImagePath($user);
        
        if ($imagePath) {
            try {
                // Check if we're using cloud storage
                $disk = self::getStorageDisk();
                
                // Check if imagePath is already a full URL (ImgBB/external service)
                if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
                    \Log::info('ProfilePictureService: Using external URL', [
                        'image_path' => $imagePath
                    ]);
                    return $imagePath;
                }
                
                if ($disk->exists($imagePath)) {
                    // Always get URL from the disk object (handles Cloudinary or local automatically)
                    $url = $disk->url($imagePath);
                    
                    // Debug: Log what URL is generated
                    \Log::info('ProfilePictureService: Generated URL', [
                        'image_path' => $imagePath,
                        'generated_url' => $url,
                        'disk_class' => get_class($disk),
                        'url_empty' => empty($url)
                    ]);
                    
                    // If URL is empty (Cloudinary issue), construct manually
                    if (empty($url) && (config('filesystems.default') === 'cloudinary' || config('filesystems.fallback_disk') === 'cloudinary')) {
                        $cloudName = env('CLOUDINARY_CLOUD_NAME');
                        $manualUrl = "https://res.cloudinary.com/{$cloudName}/image/upload/{$imagePath}";
                        \Log::info('ProfilePictureService: Constructing manual Cloudinary URL', [
                            'image_path' => $imagePath,
                            'manual_url' => $manualUrl
                        ]);
                        return $manualUrl;
                    }
                    
                    // If still empty, fall back to local storage URL
                    if (empty($url)) {
                        return asset('storage/' . $imagePath);
                    }
                    
                    return $url;
                }
            } catch (\Exception $e) {
                \Log::warning('Error accessing storage disk, falling back to default avatar', [
                    'error' => $e->getMessage(),
                    'user_id' => $user->id ?? 'unknown'
                ]);
                // Fall through to default avatar
            }
        }
        
        // Fallback to initials avatar
        return self::getDefaultAvatarUrl(self::getUserInitials($user), $size);
    }
    
    /**
     * Get the appropriate storage disk
     */
    private static function getStorageDisk()
    {
        // Use fallback_disk if configured, otherwise check default disk
        $fallbackDisk = config('filesystems.fallback_disk');
        if ($fallbackDisk) {
            try {
                $disk = Storage::disk($fallbackDisk);
                // Test if disk is accessible
                $disk->url('test');
                return $disk;
            } catch (\Exception $e) {
                \Log::warning('Fallback disk not accessible in ProfilePictureService, using public disk', [
                    'fallback_disk' => $fallbackDisk,
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Check if default disk is cloudinary
        $defaultDisk = config('filesystems.default');
        if ($defaultDisk === 'cloudinary') {
            try {
                $disk = Storage::disk('cloudinary');
                // Test if disk is accessible
                $disk->url('test');
                return $disk;
            } catch (\Exception $e) {
                \Log::warning('Cloudinary not accessible in ProfilePictureService, falling back to public disk', [
                    'error' => $e->getMessage()
                ]);
            }
        }
        
        // Fallback to public disk
        return Storage::disk('public');
    }
    
    /**
     * Upload to storage disk (Cloudinary or local fallback)
     */
    private static function uploadToStorageDisk(UploadedFile $file, $filename, User $user)
    {
        // Get the appropriate storage disk
        $disk = self::getStorageDisk();
        
        // Create directory if it doesn't exist (only for local storage)
        if ($disk === Storage::disk('public') && !$disk->exists(self::STORAGE_PATH)) {
            $disk->makeDirectory(self::STORAGE_PATH);
        }
        
        // Try to use Intervention Image if available, otherwise store directly
        if (class_exists('\Intervention\Image\Facades\Image')) {
            $path = self::STORAGE_PATH . '/' . $filename;
            self::processWithIntervention($file, $path, $disk);
            return $path;
        } else {
            // Store file directly
            $storedPath = $disk->putFileAs(
                self::STORAGE_PATH,
                $file,
                $filename
            );
            
            if (!$storedPath) {
                throw new \Exception('Failed to store file');
            }
            
            return $storedPath;
        }
    }
    
    /**
     * Get user image path from various possible fields
     */
    private static function getUserImagePath($user)
    {
        // Priority: avatar -> profile_picture -> patient.profile_picture
        if (!empty($user->avatar)) {
            return $user->avatar;
        }
        
        if (!empty($user->profile_picture)) {
            return $user->profile_picture;
        }
        
        // For patient users, check patient record
        if ($user->role === 'patient' && $user->patient && !empty($user->patient->profile_picture)) {
            return $user->patient->profile_picture;
        }
        
        return null;
    }
    
    /**
     * Get user initials for default avatar
     */
    private static function getUserInitials($user)
    {
        if (!$user->name) {
            return '??';
        }
        
        $names = explode(' ', trim($user->name));
        if (count($names) >= 2) {
            return strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1));
        }
        
        return strtoupper(substr($names[0], 0, 2));
    }
    
    /**
     * Generate a default avatar URL with initials
     */
    private static function getDefaultAvatarUrl($initials, $size = 'default')
    {
        $pixelSize = $size === 'thumbnail' ? self::THUMBNAIL_SIZE : self::DEFAULT_SIZE;
        $fontSize = $size === 'thumbnail' ? 20 : 80;
        
        // Generate a color based on the initials
        $colors = [
            '#667eea', '#764ba2', '#f093fb', '#f5576c', '#4facfe', '#00f2fe',
            '#43e97b', '#38f9d7', '#ffecd2', '#fcb69f', '#a8edea', '#fed6e3'
        ];
        
        $colorIndex = array_sum(array_map('ord', str_split($initials))) % count($colors);
        $backgroundColor = $colors[$colorIndex];
        
        // Return a data URL for the SVG
        $svg = '
        <svg width="' . $pixelSize . '" height="' . $pixelSize . '" xmlns="http://www.w3.org/2000/svg">
            <circle cx="' . ($pixelSize/2) . '" cy="' . ($pixelSize/2) . '" r="' . ($pixelSize/2) . '" fill="' . $backgroundColor . '"/>
            <text x="50%" y="50%" text-anchor="middle" dy="0.35em" fill="white" font-family="Arial, sans-serif" font-size="' . $fontSize . '" font-weight="bold">' . htmlspecialchars($initials) . '</text>
        </svg>';
        
        return 'data:image/svg+xml;base64,' . base64_encode(trim($svg));
    }
    
    /**
     * Upload and process profile picture
     */
    public static function uploadProfilePicture(UploadedFile $file, User $user)
    {
        try {
            // Delete old profile picture if exists
            self::deleteUserProfilePicture($user);
            
            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
            }
            
            // Check file size (max 5MB)
            if ($file->getSize() > 5 * 1024 * 1024) {
                throw new \Exception('File size too large. Maximum size is 5MB.');
            }
            
            // Generate filename
            $filename = $user->id . '_' . time() . '.jpg';
            $path = self::STORAGE_PATH . '/' . $filename;
            
            // Try ImgBB first if configured
            $imgbbService = new ImgBBService();
            if ($imgbbService->isConfigured()) {
                try {
                    \Log::info('Attempting ImgBB upload for user: ' . $user->id);
                    $imgbbResult = $imgbbService->uploadImage($file, 'user_' . $user->id . '_' . time());
                    
                    if ($imgbbResult['success']) {
                        $path = $imgbbResult['url']; // Store the full URL as path
                        \Log::info('ImgBB upload successful', [
                            'user_id' => $user->id,
                            'url' => $path
                        ]);
                    } else {
                        throw new \Exception('ImgBB upload failed');
                    }
                } catch (\Exception $imgbbError) {
                    \Log::warning('ImgBB upload failed, falling back to storage disk', [
                        'user_id' => $user->id,
                        'error' => $imgbbError->getMessage()
                    ]);
                    
                    // Fallback to storage disk method
                    $path = self::uploadToStorageDisk($file, $filename, $user);
                }
            } else {
                // No ImgBB configured, use storage disk
                $path = self::uploadToStorageDisk($file, $filename, $user);
            }
            
            // Update user record with avatar field
            $user->update(['avatar' => $path, 'profile_picture' => $path]);
            
            // Also update patient record if user is a patient
            if ($user->role === 'patient' && $user->patient) {
                $user->patient->update(['profile_picture' => $path]);
            }
            
            return $path;
            
        } catch (\Exception $e) {
            \Log::error('Profile picture upload failed: ' . $e->getMessage());
            throw new \Exception('Failed to upload profile picture: ' . $e->getMessage());
        }
    }
    
    /**
     * Process image with Intervention Image if available
     */
    private static function processWithIntervention(UploadedFile $file, $path, $disk = null)
    {
        $Image = \Intervention\Image\Facades\Image::class;
        
        // Use provided disk or get default
        if (!$disk) {
            $disk = self::getStorageDisk();
        }
        
        // Process and store the image
        $image = $Image::make($file->getRealPath());
        
        // Resize and optimize
        $image->fit(self::DEFAULT_SIZE, self::DEFAULT_SIZE, function ($constraint) {
            $constraint->upsize();
        });
        
        // Convert to JPEG for consistent format and smaller file size
        $image->encode('jpg', 85);
        
        // Store the processed image
        $disk->put($path, $image->stream());
    }
    
    /**
     * Delete user's profile picture
     */
    public static function deleteUserProfilePicture(User $user)
    {
        $imagePath = self::getUserImagePath($user);
        $disk = self::getStorageDisk();
        
        if ($imagePath && $disk->exists($imagePath)) {
            $disk->delete($imagePath);
        }
        
        // Clear from user record (set both to null for consistency)
        $user->update(['avatar' => null, 'profile_picture' => null]);
        
        // Clear from patient record if exists
        if ($user->role === 'patient' && $user->patient) {
            $user->patient->update(['profile_picture' => null]);
        }
    }
    
    /**
     * Get initials avatar HTML for direct use in templates
     */
    public static function getAvatarHtml($user, $size = 'default', $cssClasses = '')
    {
        $url = self::getProfilePictureUrl($user, $size);
        $initials = self::getUserInitials($user);
        
        if (strpos($url, 'data:image/svg+xml') === 0) {
            // It's an SVG initials avatar
            return '<div class="avatar-initials ' . $cssClasses . '" style="display: inline-block;">' . 
                   '<img src="' . $url . '" alt="' . htmlspecialchars($user->name ?? 'User') . '" style="border-radius: 50%;" />' . 
                   '</div>';
        } else {
            // It's a real uploaded image
            return '<div class="avatar-image ' . $cssClasses . '" style="display: inline-block;">' . 
                   '<img src="' . $url . '" alt="' . htmlspecialchars($user->name ?? 'User') . '" style="border-radius: 50%; object-fit: cover;" />' . 
                   '</div>';
        }
    }
    
    /**
     * Clean up orphaned profile pictures
     */
    public static function cleanupOrphanedPictures()
    {
        $disk = self::getStorageDisk();
        $allFiles = $disk->files(self::STORAGE_PATH);
        $activeFiles = User::whereNotNull('profile_picture')->pluck('profile_picture')->toArray();
        
        $orphaned = array_diff($allFiles, $activeFiles);
        
        foreach ($orphaned as $file) {
            $disk->delete($file);
        }
        
        return count($orphaned);
    }
}