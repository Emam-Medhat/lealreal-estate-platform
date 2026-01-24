<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Debugging Profile Pictures ===\n";

// Get all users
$users = User::all();

foreach ($users as $user) {
    echo "\nUser ID: {$user->id}\n";
    echo "Name: {$user->full_name}\n";
    echo "Email: {$user->email}\n";
    echo "User Type: {$user->user_type}\n";
    
    // Check avatar field
    echo "Avatar: " . ($user->avatar ?? 'NULL') . "\n";
    echo "Profile Image: " . ($user->profile_image ?? 'NULL') . "\n";
    
    // Check if avatar file exists
    if ($user->avatar) {
        $avatarPath = storage_path('app/public/avatars/' . $user->avatar);
        echo "Avatar file exists: " . (file_exists($avatarPath) ? 'Yes' : 'No') . "\n";
        if (file_exists($avatarPath)) {
            echo "Avatar file size: " . filesize($avatarPath) . " bytes\n";
        }
    }
    
    // Check if profile image file exists
    if ($user->profile_image) {
        $profileImagePath = storage_path('app/public/' . $user->profile_image);
        echo "Profile image file exists: " . (file_exists($profileImagePath) ? 'Yes' : 'No') . "\n";
        if (file_exists($profileImagePath)) {
            echo "Profile image file size: " . filesize($profileImagePath) . " bytes\n";
        }
    }
    
    // Test avatar URL generation
    if ($user->avatar) {
        echo "Avatar URL: " . asset('storage/avatars/' . $user->avatar) . "\n";
    }
    
    if ($user->profile_image) {
        echo "Profile Image URL: " . asset('storage/' . $user->profile_image) . "\n";
    }
    
    echo "--------------------------------\n";
}

// Check avatars directory structure
echo "\n=== Storage Directory Structure ===\n";
$avatarsPath = storage_path('app/public/avatars');
echo "Avatars path: {$avatarsPath}\n";
echo "Directory exists: " . (is_dir($avatarsPath) ? 'Yes' : 'No') . "\n";

if (is_dir($avatarsPath)) {
    $files = scandir($avatarsPath);
    echo "Files in avatars directory:\n";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $fullPath = $avatarsPath . DIRECTORY_SEPARATOR . $file;
            echo "  - {$file} (" . filesize($fullPath) . " bytes)\n";
        }
    }
}

// Check public storage link
echo "\n=== Public Storage Link ===\n";
$publicStoragePath = public_path('storage');
echo "Public storage path: {$publicStoragePath}\n";
echo "Link exists: " . (is_link($publicStoragePath) ? 'Yes' : 'No') . "\n";
if (is_link($publicStoragePath)) {
    echo "Link target: " . readlink($publicStoragePath) . "\n";
}
