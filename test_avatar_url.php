<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "=== Testing Avatar URL Generation ===\n";

$user = User::find(2);
echo "User ID: {$user->id}\n";
echo "Avatar field: {$user->avatar}\n";
echo "Avatar URL: {$user->avatar_url}\n";

// Test the URL directly
$filename = $user->avatar;
if ($filename && str_starts_with($filename, 'avatars/')) {
    $cleanFilename = substr($filename, 8);
    echo "Clean filename: {$cleanFilename}\n";
    echo "Direct URL: " . asset('storage/avatars/' . $cleanFilename) . "\n";
}
