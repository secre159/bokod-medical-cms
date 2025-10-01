<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use App\Services\CloudinaryService;
use App\Services\OwnImageApiService;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create Laravel app instance (minimal setup)
$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

echo "Testing Cloudinary Connection...\n";
echo "================================\n";

try {
    // Test CloudinaryService directly
    $cloudinary = new CloudinaryService();
    $result = $cloudinary->listImages('bokod_cms/profiles', 1);
    
    if ($result['success']) {
        echo "✅ Cloudinary connection successful!\n";
        echo "Cloud Name: " . $_ENV['CLOUDINARY_CLOUD_NAME'] . "\n";
        echo "Total images in profiles folder: " . ($result['total_count'] ?? 0) . "\n";
        
        if (!empty($result['images'])) {
            echo "\nSample image:\n";
            $image = $result['images'][0];
            echo "- Public ID: " . $image['public_id'] . "\n";
            echo "- URL: " . $image['secure_url'] . "\n";
            echo "- Format: " . $image['format'] . "\n";
        }
    } else {
        echo "❌ Cloudinary connection failed!\n";
        echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Test failed with exception:\n";
    echo $e->getMessage() . "\n";
}

echo "\nTest completed.\n";