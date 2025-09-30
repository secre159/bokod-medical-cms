<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;
use Exception;

class OwnImageApiService
{
    private $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    
    /**
     * Upload profile picture to Cloudinary
     */
    public function uploadProfilePicture(UploadedFile $file, int $userId): array
    {
        try {
            Log::info('Uploading to Cloudinary', [
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'user_id' => $userId
            ]);
            
            // Upload to Cloudinary with user-specific folder
            $uploadResult = $this->cloudinaryService->uploadImage(
                $file->getRealPath(),
                'bokod_cms/profiles'
            );
            
            if (!$uploadResult['success']) {
                throw new Exception('Cloudinary upload failed: ' . $uploadResult['error']);
            }
            
            Log::info('Cloudinary upload successful', [
                'user_id' => $userId,
                'public_id' => $uploadResult['public_id'],
                'url' => $uploadResult['url']
            ]);
            
            return [
                'success' => true,
                'url' => $uploadResult['url'],
                'public_id' => $uploadResult['public_id'],
                'filename' => basename($uploadResult['url']),
                'size' => $uploadResult['bytes'],
                'width' => $uploadResult['width'],
                'height' => $uploadResult['height'],
                'format' => $uploadResult['format'],
                'provider' => 'cloudinary'
            ];
            
        } catch (Exception $e) {
            Log::error('Cloudinary upload failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Delete profile picture from Cloudinary
     */
    public function deleteProfilePicture(string $publicId): bool
    {
        try {
            $deleteResult = $this->cloudinaryService->deleteImage($publicId);
            
            if ($deleteResult['success']) {
                Log::info('Cloudinary image deleted successfully', [
                    'public_id' => $publicId
                ]);
                return true;
            }
            
            Log::warning('Failed to delete from Cloudinary', [
                'public_id' => $publicId,
                'result' => $deleteResult
            ]);
            return false;
            
        } catch (Exception $e) {
            Log::error('Failed to delete from Cloudinary', [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    /**
     * Get optimized image URL from Cloudinary
     */
    public function getOptimizedImageUrl(string $publicId, int $width = 150, int $height = 150): ?string
    {
        try {
            return $this->cloudinaryService->getOptimizedUrl($publicId, $width, $height);
        } catch (Exception $e) {
            Log::warning('Failed to get optimized image URL', [
                'public_id' => $publicId,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    
    /**
     * List profile images from Cloudinary
     */
    public function listProfileImages(): array
    {
        try {
            return $this->cloudinaryService->listImages('bokod_cms/profiles');
        } catch (Exception $e) {
            Log::error('Failed to list profile images', [
                'error' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * Test Cloudinary connection
     */
    public function testConnection(): array
    {
        try {
            // Test by listing images (lightweight operation)
            $result = $this->cloudinaryService->listImages('bokod_cms/profiles', 1);
            
            if ($result['success']) {
                return [
                    'success' => true,
                    'message' => 'Cloudinary connection successful',
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'total_images' => $result['total_count'] ?? 0
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Cloudinary connection failed',
                'error' => $result['error'] ?? 'Unknown error'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Cloudinary connection test failed: ' . $e->getMessage()
            ];
        }
    }
}