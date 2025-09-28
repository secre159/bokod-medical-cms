<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class LocalProfilePictureService
{
    /**
     * Upload profile picture to local storage
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return array
     */
    public function uploadProfilePicture(UploadedFile $file, int $userId): array
    {
        try {
            // Validate file
            $this->validateFile($file);

            Log::info('Starting local profile picture upload', [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'user_id' => $userId
            ]);

            // Create directory if it doesn't exist
            $directory = 'profile-pictures';
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = "profile_{$userId}_" . time() . '_' . uniqid() . '.' . $extension;
            
            // Store the file
            $path = $file->storeAs($directory, $filename, 'public');
            
            if (!$path) {
                throw new Exception('Failed to store file');
            }

            // Generate URL
            $url = asset('storage/' . $path);

            Log::info('Local profile picture upload successful', [
                'path' => $path,
                'url' => $url,
                'user_id' => $userId
            ]);

            return [
                'success' => true,
                'url' => $url,
                'path' => $path,
                'filename' => $filename,
                'size' => $file->getSize()
            ];

        } catch (Exception $e) {
            Log::error('Local profile picture upload failed', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
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

        // Check file size (5MB max)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file->getSize() > $maxSize) {
            throw new Exception('File size too large. Maximum allowed size is 5MB.');
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
     * Delete profile picture from local storage
     *
     * @param string $url
     * @return bool
     */
    public function deleteProfilePicture(string $url): bool
    {
        try {
            // Extract path from URL
            $path = str_replace(asset('storage/'), '', $url);
            
            if (Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->delete($path);
            }
            
            return true; // Consider it deleted if it doesn't exist
            
        } catch (Exception $e) {
            Log::error('Failed to delete profile picture', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}