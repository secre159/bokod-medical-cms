<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class SystemAssetsService
{
    /**
     * Get the application logo URL
     */
    public static function getLogoUrl($format = 'png')
    {
        $logoPath = "images/logo.{$format}";
        
        // First check if logo exists in public directory
        if (file_exists(public_path($logoPath))) {
            return asset($logoPath);
        }
        
        // Fallback to SVG if PNG doesn't exist
        if ($format === 'png') {
            return self::getLogoUrl('svg');
        }
        
        // Return null if no logo found
        return null;
    }
    
    /**
     * Get the favicon URL
     */
    public static function getFaviconUrl()
    {
        $faviconPath = 'favicon.ico';
        
        if (file_exists(public_path($faviconPath))) {
            return asset($faviconPath);
        }
        
        return null;
    }
    
    /**
     * Update the application logo
     */
    public static function updateLogo(UploadedFile $file, $format = 'png')
    {
        try {
            // Validate file type
            $allowedTypes = ['image/png', 'image/svg+xml'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only PNG and SVG are allowed for logos.');
            }
            
            // Determine file extension
            $extension = $file->getMimeType() === 'image/svg+xml' ? 'svg' : 'png';
            
            // Define the destination path
            $destinationPath = public_path("images/logo.{$extension}");
            
            // Ensure the images directory exists
            $imagesDir = public_path('images');
            if (!is_dir($imagesDir)) {
                mkdir($imagesDir, 0755, true);
            }
            
            // Move the uploaded file to the public directory
            if (!$file->move(public_path('images'), "logo.{$extension}")) {
                throw new \Exception('Failed to move uploaded logo file.');
            }
            
            return "images/logo.{$extension}";
            
        } catch (\Exception $e) {
            \Log::error('Logo update failed: ' . $e->getMessage());
            throw new \Exception('Failed to update logo: ' . $e->getMessage());
        }
    }
    
    /**
     * Update the favicon
     */
    public static function updateFavicon(UploadedFile $file)
    {
        try {
            // Validate file type
            $allowedTypes = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico', 'image/icon'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                throw new \Exception('Invalid file type. Only ICO files are allowed for favicon.');
            }
            
            // Move the uploaded file to the public directory
            if (!$file->move(public_path(), 'favicon.ico')) {
                throw new \Exception('Failed to move uploaded favicon file.');
            }
            
            return 'favicon.ico';
            
        } catch (\Exception $e) {
            \Log::error('Favicon update failed: ' . $e->getMessage());
            throw new \Exception('Failed to update favicon: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all available system assets
     */
    public static function getSystemAssets()
    {
        return [
            'logo_png' => self::getLogoUrl('png'),
            'logo_svg' => self::getLogoUrl('svg'),
            'favicon' => self::getFaviconUrl(),
        ];
    }
}