<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing direct DB update..." . PHP_EOL;

try {
    // Get user 9
    $user = \DB::table('users')->where('id', 9)->first();
    
    if (!$user) {
        echo "User 9 not found!" . PHP_EOL;
        exit;
    }
    
    echo "Current user data:" . PHP_EOL;
    echo "ID: " . $user->id . PHP_EOL;
    echo "Name: " . $user->first_name . ' ' . $user->last_name . PHP_EOL;
    echo "Email: " . $user->email . PHP_EOL;
    echo "Phone: " . ($user->phone ?? 'NULL') . PHP_EOL;
    echo "User Type: " . ($user->user_type ?? 'NULL') . PHP_EOL;
    echo "Account Status: " . ($user->account_status ?? 'NULL') . PHP_EOL;
    
    // Test update
    echo PHP_EOL . "Testing update..." . PHP_EOL;
    
    $updateData = [
        'first_name' => 'Test Name Updated',
        'phone' => '9876543210',
        'user_type' => 'agent',
        'account_status' => 'active'
    ];
    
    echo "Update data: " . json_encode($updateData) . PHP_EOL;
    
    // Try direct DB update
    $result = \DB::table('users')->where('id', 9)->update($updateData);
    
    echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    echo "Rows affected: " . $result . PHP_EOL;
    
    // Check updated data
    $updatedUser = \DB::table('users')->where('id', 9)->first();
    
    echo PHP_EOL . "Updated user data:" . PHP_EOL;
    echo "Name: " . $updatedUser->first_name . ' ' . $updatedUser->last_name . PHP_EOL;
    echo "Phone: " . ($updatedUser->phone ?? 'NULL') . PHP_EOL;
    echo "User Type: " . ($updatedUser->user_type ?? 'NULL') . PHP_EOL;
    echo "Account Status: " . ($updatedUser->account_status ?? 'NULL') . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
