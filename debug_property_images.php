<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyMedia;

echo "=== Debugging Property Images ===\n";

// Get properties with media
$properties = Property::with('media')->get();

foreach ($properties as $property) {
    echo "\nProperty ID: {$property->id}\n";
    echo "Title: {$property->title}\n";
    echo "Media count: {$property->media->count()}\n";
    
    if ($property->media->count() > 0) {
        foreach ($property->media as $media) {
            echo "  Media ID: {$media->id}\n";
            echo "  File path: {$media->file_path}\n";
            echo "  Media type: {$media->media_type}\n";
            
            // Test the url attribute
            echo "  URL: {$media->url}\n";
            
            // Check if file exists
            $fullPath = storage_path('app/public/' . $media->file_path);
            echo "  File exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
            
            // Test asset() function
            echo "  Asset URL: " . asset('storage/' . $media->file_path) . "\n";
        }
    } else {
        echo "  No media found\n";
    }
}

// Test creating a dummy property with image
echo "\n=== Testing Image URL Generation ===\n";
$dummyMedia = new PropertyMedia();
$dummyMedia->file_path = 'properties/test-image.jpg';
$dummyMedia->media_type = 'image';

echo "Dummy media URL: {$dummyMedia->url}\n";

// Check storage directory structure 
echo "\n=== Storage Directory Structure ===\n";
$storagePath = storage_path('app/public');
echo "Storage base path: {$storagePath}\n";

if (is_dir($storagePath)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($storagePath));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $relativePath = str_replace($storagePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            echo "Found file: {$relativePath}\n";
        }
    }
}
