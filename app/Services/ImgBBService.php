<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class ImgBBService
{
    private $apiKey;
    private $baseUrl = 'https://api.imgbb.com/1/upload';

    public function __construct()
    {
        $this->apiKey = config('services.imgbb.api_key', config('services.imgbb.key'));
    }

    /**
     * Upload an image to ImgBB
     *
     * @param UploadedFile $file
     * @param string|null $name Optional name for the image
     * @return array
     * @throws Exception
     */
    public function uploadImage(UploadedFile $file, ?string $name = null): array
    {
        try {
            // Validate file
            $this->validateFile($file);

            Log::info('Starting ImgBB upload', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            // Convert file to base64
            $base64Image = base64_encode(file_get_contents($file->getRealPath()));

            // Prepare data for ImgBB API (key goes in URL, not body)
            $data = [
                'image' => $base64Image,
                'expiration' => 0, // Never expire
            ];

            if ($name) {
                $data['name'] = $name;
            }

            // Make request to ImgBB API with key in URL and proper headers
            $url = $this->baseUrl . '?key=' . $this->apiKey;
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                    'Accept' => 'application/json',
                    'Referer' => 'https://imgbb.com/',
                ])
                ->asForm()
                ->post($url, $data);

            if (!$response->successful()) {
                throw new Exception('ImgBB API request failed: ' . $response->body());
            }

            $responseData = $response->json();

            if (!isset($responseData['success']) || !$responseData['success']) {
                throw new Exception('ImgBB upload failed: ' . ($responseData['error']['message'] ?? 'Unknown error'));
            }

            $imageData = $responseData['data'];

            Log::info('ImgBB upload successful', [
                'url' => $imageData['url'],
                'display_url' => $imageData['display_url'] ?? $imageData['url'],
                'available_keys' => array_keys($imageData)
            ]);

            return [
                'success' => true,
                'url' => $imageData['url'],
                'display_url' => $imageData['display_url'] ?? $imageData['url'],
                'thumb_url' => isset($imageData['thumb']['url']) ? $imageData['thumb']['url'] : $imageData['url'],
                'medium_url' => isset($imageData['medium']['url']) ? $imageData['medium']['url'] : $imageData['url'],
                'delete_url' => $imageData['delete_url'] ?? null,
                'size' => $imageData['size'] ?? null,
                'width' => $imageData['width'] ?? null,
                'height' => $imageData['height'] ?? null
            ];

        } catch (Exception $e) {
            Log::error('ImgBB upload failed', [
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
     * Upload profile picture specifically
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadProfilePicture(UploadedFile $file, int $userId): array
    {
        $name = "profile_picture_user_{$userId}_" . time();
        return $this->uploadImage($file, $name);
    }

    /**
     * Upload messaging attachment specifically
     *
     * @param UploadedFile $file
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function uploadMessageAttachment(UploadedFile $file, int $conversationId, int $userId): array
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $name = "message_conv{$conversationId}_user{$userId}_{$originalName}_" . time();
        
        return $this->uploadImage($file, $name);
    }

    /**
     * Validate uploaded file
     *
     * @param UploadedFile $file
     * @throws Exception
     */
    private function validateFile(UploadedFile $file): void
    {
        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            throw new Exception('File upload failed: ' . $file->getErrorMessage());
        }

        // Check file size (ImgBB max is 32MB, but we'll limit to 10MB for messaging attachments)
        $maxSize = 10 * 1024 * 1024; // 10MB
        if ($file->getSize() > $maxSize) {
            throw new Exception('File size too large. Maximum allowed size is 10MB.');
        }

        // Check file type
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg', 
            'image/png',
            'image/gif',
            'image/webp'
        ];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP images are allowed.');
        }

        // Check file extension
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            throw new Exception('Invalid file extension. Only jpg, jpeg, png, gif, and webp files are allowed.');
        }
    }

    /**
     * Get optimized image URL for display
     *
     * @param string $originalUrl
     * @param string $size 'thumb'|'medium'|'original'
     * @return string
     */
    public function getOptimizedUrl(string $originalUrl, string $size = 'medium'): string
    {
        // ImgBB URLs can be modified to get different sizes
        // Original: https://i.ibb.co/abc123/image.jpg
        // Medium: https://i.ibb.co/abc123/image.md.jpg
        // Thumb: https://i.ibb.co/abc123/image.th.jpg
        
        if ($size === 'original' || $size === 'full') {
            return $originalUrl;
        }

        $suffix = $size === 'thumb' ? '.th' : '.md';
        
        // Extract the file extension
        $pathinfo = pathinfo($originalUrl);
        $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
        
        // Insert size suffix before extension
        return str_replace($extension, $suffix . $extension, $originalUrl);
    }

    /**
     * Check if ImgBB service is configured
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Test connection to ImgBB API
     *
     * @return array
     */
    public function testConnection(): array
    {
        try {
            // Test with a simple request to see if the API responds
            $response = Http::timeout(5)->get('https://api.imgbb.com/1/upload?key=' . $this->apiKey);
            
            if ($response->status() === 400) {
                // 400 is expected for GET request without image data
                return [
                    'success' => true,
                    'message' => 'API key is valid and service is accessible',
                    'status_code' => $response->status()
                ];
            }
            
            return [
                'success' => true,
                'message' => 'Service is accessible',
                'status_code' => $response->status(),
                'response' => $response->body()
            ];
        } catch (Exception $e) {
            Log::warning('ImgBB connection test failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if ImgBB service is available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get('https://api.imgbb.com/1/upload?key=test');
            return $response->status() !== 500; // Any response other than server error means service is up
        } catch (Exception $e) {
            Log::warning('ImgBB service availability check failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
