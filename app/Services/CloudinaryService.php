<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;
use Exception;

class CloudinaryService
{
    private $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ]
        ]);
    }

    /**
     * Upload image to Cloudinary
     */
    public function uploadImage($imageFile, $folder = 'profiles')
    {
        try {
            $result = $this->cloudinary->uploadApi()->upload($imageFile, [
                'folder' => $folder,
                'resource_type' => 'image',
                'format' => 'webp', // Optimize format
                'quality' => 'auto:good',
                'fetch_format' => 'auto'
            ]);

            return [
                'success' => true,
                'url' => $result['secure_url'],
                'public_id' => $result['public_id'],
                'format' => $result['format'],
                'width' => $result['width'],
                'height' => $result['height'],
                'bytes' => $result['bytes']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete image from Cloudinary
     */
    public function deleteImage($publicId)
    {
        try {
            $result = $this->cloudinary->uploadApi()->destroy($publicId);
            
            return [
                'success' => $result['result'] === 'ok',
                'result' => $result['result']
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get optimized image URL with transformations
     */
    public function getOptimizedUrl($publicId, $width = 150, $height = 150)
    {
        return $this->cloudinary->image($publicId)
            ->resize(Resize::fill($width, $height))
            ->delivery('q_auto:good')
            ->delivery('f_auto')
            ->toUrl();
    }

    /**
     * List all images in folder
     */
    public function listImages($folder = 'profiles', $maxResults = 100)
    {
        try {
            $result = $this->cloudinary->searchApi()->expression("folder:$folder")
                ->maxResults($maxResults)
                ->execute();

            return [
                'success' => true,
                'images' => $result['resources'] ?? [],
                'total_count' => $result['total_count'] ?? 0
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}