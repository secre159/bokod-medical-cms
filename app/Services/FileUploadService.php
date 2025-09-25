<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Message;
use Exception;

class FileUploadService
{
    /**
     * Get the appropriate storage disk
     */
    private function getStorageDisk()
    {
        // Use Cloudinary if configured, otherwise use local public disk
        $defaultDisk = config('filesystems.default');
        
        if ($defaultDisk === 'cloudinary' && config('filesystems.disks.cloudinary.cloud_name')) {
            return Storage::disk('cloudinary');
        }
        
        return Storage::disk('public');
    }
    
    /**
     * Upload a file and return file information
     */
    public function uploadFile(UploadedFile $file, $conversationId)
    {
        try {
            // Validate file
            $this->validateFile($file);
            
            // Generate unique filename
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFilename = Str::slug($filename) . '_' . time() . '.' . $extension;
            
            // Create directory path based on conversation
            $directoryPath = 'attachments/' . date('Y/m') . '/' . $conversationId;
            
            // Get the appropriate storage disk
            $disk = $this->getStorageDisk();
            
            // Store file
            $filePath = $file->storeAs($directoryPath, $uniqueFilename, $disk->getName());
            
            if (!$filePath) {
                throw new Exception('Failed to store file');
            }
            
            // Determine file type
            $fileType = Message::getFileTypeFromExtension($extension);
            
            return [
                'file_name' => $originalName,
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
            ];
            
        } catch (Exception $e) {
            throw new Exception('File upload failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Validate uploaded file
     */
    private function validateFile(UploadedFile $file)
    {
        // Check file size (10MB max)
        if ($file->getSize() > Message::MAX_FILE_SIZE) {
            throw new Exception('File size exceeds maximum allowed size of 10MB');
        }
        
        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, Message::ALLOWED_EXTENSIONS)) {
            throw new Exception('File type not allowed. Allowed types: ' . implode(', ', Message::ALLOWED_EXTENSIONS));
        }
        
        // Basic security checks
        if (empty($extension)) {
            throw new Exception('File must have a valid extension');
        }
        
        // Check MIME type for images
        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $allowedMimes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                throw new Exception('Invalid image file');
            }
        }
    }
    
    /**
     * Delete a file from storage
     */
    public function deleteFile($filePath)
    {
        try {
            $disk = $this->getStorageDisk();
            if ($filePath && $disk->exists($filePath)) {
                return $disk->delete($filePath);
            }
            return true;
        } catch (Exception $e) {
            // Log error but don't throw - file might already be deleted
            \Log::error('Failed to delete file: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get file preview URL for images
     */
    public function getPreviewUrl($filePath, $fileType)
    {
        if ($fileType === Message::FILE_TYPE_IMAGE) {
            $disk = $this->getStorageDisk();
            
            if ($disk->exists($filePath)) {
                // For Cloudinary, return the URL directly
                if (config('filesystems.default') === 'cloudinary') {
                    return $disk->url($filePath);
                }
                // For local storage, return the asset URL
                else {
                    return asset('storage/' . $filePath);
                }
            }
        }
        
        return null;
    }
    
    /**
     * Generate thumbnail for images (optional feature)
     */
    public function generateThumbnail($filePath, $width = 200, $height = 200)
    {
        // This is a placeholder for thumbnail generation
        // You can implement using Intervention Image or similar
        return null;
    }
    
    /**
     * Scan file for viruses (placeholder)
     */
    private function scanForViruses(UploadedFile $file)
    {
        // Placeholder for virus scanning
        // In production, you might want to integrate with ClamAV or similar
        return true;
    }
    
    /**
     * Get human readable file size
     */
    public static function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    /**
     * Get allowed file extensions as string
     */
    public static function getAllowedExtensionsString()
    {
        return implode(', ', Message::ALLOWED_EXTENSIONS);
    }
    
    /**
     * Get max file size in human readable format
     */
    public static function getMaxFileSizeFormatted()
    {
        return self::formatFileSize(Message::MAX_FILE_SIZE);
    }
}