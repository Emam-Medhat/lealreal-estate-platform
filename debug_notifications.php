<?php

use App\Models\User;
use App\Notifications\NewUserRegisteredNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "--- Notification Debug Tool ---\n";

// 1. Find all potential admins
$admins = User::where(function ($query) {
    $query->where('user_type', 'admin')
        ->orWhere('user_type', 'super_admin')
        ->orWhere('account_status', 'active')->where('user_type', 'admin');
})->get();

echo "Found " . $admins->count() . " admin(s).\n";
foreach ($admins as $admin) {
    echo "- ID: {$admin->id}, Name: {$admin->name}, Email: {$admin->email}, Type: {$admin->user_type}\n";
}

if ($admins->isEmpty()) {
    echo "ERROR: No admins found. Notifications cannot be sent.\n";
    exit;
}

// 2. Create a dummy user for testing or use an existing one
$testUser = User::latest()->first();
echo "Using latest user for testing: ID: {$testUser->id}, Name: {$testUser->name}\n";

// 3. Trigger notification
echo "Attempting to send notification...\n";
try {
    Notification::send($admins, new NewUserRegisteredNotification($testUser));
    echo "SUCCESS: Notification dispatch call completed.\n";
} catch (\Exception $e) {
    file_put_contents('debug_error.log', $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "FAILED: Check debug_error.log for details.\n";
}

// 4. Verify database
foreach ($admins as $admin) {
    $notifCount = $admin->notifications()->count();
    $unreadCount = $admin->unreadNotifications()->count();
    echo "Admin ID {$admin->id} now has {$notifCount} total notifications ({$unreadCount} unread).\n";

    if ($notifCount > 0) {
        $lastNotif = $admin->notifications()->latest()->first();
        echo "Last notification data: " . json_encode($lastNotif->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

echo "Check laravel.log for any background broadcast errors.\n";
