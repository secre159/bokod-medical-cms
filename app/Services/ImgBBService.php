<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImgBBService
{
    private $apiKey;
    private $apiUrl = 'https://api.imgbb.com/1/upload';

    public function __construct()
    {
        $this->apiKey = env('IMGBB_API_KEY');
    }

    /**
     * Upload image to ImgBB
     */
    public function uploadImage(UploadedFile $file, $name = null)
    {
        if (!$this->apiKey) {
            throw new \Exception('ImgBB API key not configured');
        }

        try {
            // Convert image to base64
            $imageData = base64_encode(file_get_contents($file->getRealPath()));
            
            $response = Http::asForm()->post($this->apiUrl, [
                'key' => $this->apiKey,
                'image' => $imageData,
                'name' => $name ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success']) {
                    Log::info('ImgBB upload successful', [
                        'filename' => $file->getClientOriginalName(),
                        'url' => $data['data']['url']
                    ]);
                    
                    return [
                        'success' => true,
                        'url' => $data['data']['url'],
                        'delete_url' => $data['data']['delete_url'] ?? null,
                        'display_url' => $data['data']['display_url'],
                        'image_id' => $data['data']['id'],
                        'size' => $data['data']['size'],
                    ];
                } else {
                    throw new \Exception('ImgBB API returned error: ' . ($data['error']['message'] ?? 'Unknown error'));
                }
            } else {
                throw new \Exception('ImgBB API request failed: ' . $response->status() . ' - ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('ImgBB upload failed', [
                'error' => $e->getMessage(),
                'filename' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    /**
     * Check if ImgBB is configured
     */
    public function isConfigured()
    {
        return !empty($this->apiKey);
    }

    /**
     * Test ImgBB connection
     */
    public function testConnection()
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'ImgBB API key not configured'
            ];
        }

        try {
            // Create a simple test image (1x1 pixel PNG)
            $testImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8/5+hHgAHggJ/PchI7wAAAABJRU5ErkJggg==');
            
            $response = Http::asForm()->post($this->apiUrl, [
                'key' => $this->apiKey,
                'image' => base64_encode($testImage),
                'name' => 'test_connection_' . time(),
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => $data['success'] ?? false,
                    'message' => $data['success'] ? 'ImgBB connection successful' : 'ImgBB API error',
                    'test_url' => $data['data']['url'] ?? null
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'ImgBB API request failed: ' . $response->status()
                ];
            }
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'ImgBB connection test failed: ' . $e->getMessage()
            ];
        }
    }
}