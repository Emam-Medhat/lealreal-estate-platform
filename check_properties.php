<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;
use App\Models\PropertyMedia;

echo "=== Checking Properties and Media ===\n";

$properties = Property::with('media')->get();
echo "Total properties: " . $properties->count() . "\n\n";

foreach ($properties as $property) {
    echo "Property: " . $property->title . "\n";
    echo "  - ID: " . $property->id . "\n";
    echo "  - Status: " . $property->status . "\n";
    echo "  - Featured: " . ($property->featured ? 'Yes' : 'No') . "\n";
    echo "  - Media count: " . $property->media->count() . "\n";
    
    foreach ($property->media as $media) {
        echo "    - Media ID: " . $media->id . "\n";
        echo "      File Path: " . $media->file_path . "\n";
        echo "      Media Type: " . $media->media_type . "\n";
        echo "      File Type: " . $media->file_type . "\n";
        
        // Check if file exists
        $fullPath = storage_path('app/public/' . $media->file_path);
        echo "      File exists: " . (file_exists($fullPath) ? 'Yes' : 'No') . "\n";
        if (file_exists($fullPath)) {
            echo "      File size: " . filesize($fullPath) . " bytes\n";
        }
        echo "\n";
    }
    echo "--------------------------------\n";
}

echo "\n=== Checking Storage Directory ===\n";
$storagePath = storage_path('app/public/properties');
echo "Storage path: " . $storagePath . "\n";
echo "Directory exists: " . (is_dir($storagePath) ? 'Yes' : 'No') . "\n";

if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    echo "Files in directory:\n";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "  - " . $file . "\n";
        }
    }
}

echo "\n=== Checking Public Storage Link ===\n";
$publicStoragePath = public_path('storage');
echo "Public storage path: " . $publicStoragePath . "\n";
echo "Link exists: " . (is_link($publicStoragePath) ? 'Yes' : 'No') . "\n";
if (is_link($publicStoragePath)) {
    echo "Link target: " . readlink($publicStoragePath) . "\n";
}
