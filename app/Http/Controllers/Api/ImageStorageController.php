<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\CloudinaryService;

class ImageStorageController extends Controller
{
    protected $cloudinaryService;
    
    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }
    /**
     * Store an image in Cloudinary and return permanent URL
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB
                'type' => 'sometimes|string|in:profile,medicine,general',
                'category' => 'sometimes|string|max:50'
            ]);

            $file = $request->file('image');
            $type = $request->get('type', 'general');
            $category = $request->get('category', $type); // Allow category or type
            
            // Upload to Cloudinary
            $uploadResult = $this->cloudinaryService->uploadImage(
                $file->getRealPath(),
                "bokod_cms/{$category}"
            );
            
            if (!$uploadResult['success']) {
                throw new \Exception('Cloudinary upload failed: ' . $uploadResult['error']);
            }
            
            Log::info('Image stored via Cloudinary API', [
                'public_id' => $uploadResult['public_id'],
                'size' => $uploadResult['bytes'],
                'category' => $category,
                'url' => $uploadResult['url']
            ]);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $uploadResult['url'],
                    'public_id' => $uploadResult['public_id'],
                    'filename' => basename($uploadResult['url']),
                    'size' => $uploadResult['bytes'],
                    'width' => $uploadResult['width'],
                    'height' => $uploadResult['height'],
                    'format' => $uploadResult['format'],
                    'type' => $category,
                    'provider' => 'cloudinary',
                    'uploaded_at' => now()->toISOString()
                ],
                'message' => 'Image stored successfully in Cloudinary'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Cloudinary API upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to store image: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get image info
     */
    public function show($filename)
    {
        try {
            $path = "api-images/profile/{$filename}";
            
            if (!Storage::disk('public')->exists($path)) {
                $path = "api-images/general/{$filename}";
                if (!Storage::disk('public')->exists($path)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Image not found'
                    ], 404);
                }
            }
            
            $url = asset('storage/' . $path);
            $size = Storage::disk('public')->size($path);
            $lastModified = Storage::disk('public')->lastModified($path);
            
            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename,
                'size' => $size,
                'last_modified' => date('Y-m-d H:i:s', $lastModified)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get image info'
            ], 500);
        }
    }
    
    /**
     * Delete an image
     */
    public function destroy($filename)
    {
        try {
            $paths = [
                "api-images/profile/{$filename}",
                "api-images/medicine/{$filename}",
                "api-images/general/{$filename}"
            ];
            
            $deleted = false;
            foreach ($paths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    $deleted = true;
                    break;
                }
            }
            
            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found'
                ], 404);
            }
            
            Log::info('Image deleted via API', ['filename' => $filename]);
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image deletion API failed', [
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete image'
            ], 500);
        }
    }
    
    /**
     * List all images
     */
    public function index(Request $request)
    {
        try {
            $type = $request->get('type', 'all');
            $images = [];
            
            if ($type === 'all' || $type === 'profile') {
                $profileImages = Storage::disk('public')->files('api-images/profile');
                foreach ($profileImages as $path) {
                    $filename = basename($path);
                    $images[] = [
                        'filename' => $filename,
                        'type' => 'profile',
                        'url' => asset('storage/' . $path),
                        'size' => Storage::disk('public')->size($path)
                    ];
                }
            }
            
            if ($type === 'all' || $type === 'general') {
                $generalImages = Storage::disk('public')->files('api-images/general');
                foreach ($generalImages as $path) {
                    $filename = basename($path);
                    $images[] = [
                        'filename' => $filename,
                        'type' => 'general',
                        'url' => asset('storage/' . $path),
                        'size' => Storage::disk('public')->size($path)
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'images' => $images,
                'total' => count($images)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list images'
            ], 500);
        }
    }
}