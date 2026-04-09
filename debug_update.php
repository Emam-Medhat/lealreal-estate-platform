<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing user update..." . PHP_EOL;

try {
    // Get user 9
    $user = App\Models\User::find(9);
    
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
        'first_name' => 'Test Name',
        'phone' => '1234567890',
        'user_type' => 'agent',
        'account_status' => 'active'
    ];
    
    echo "Update data: " . json_encode($updateData) . PHP_EOL;
    
    // Check fillable fields
    echo PHP_EOL . "Fillable fields: " . PHP_EOL;
    echo json_encode($user->getFillable()) . PHP_EOL;
    
    // Try update
    $result = $user->update($updateData);
    
    echo "Update result: " . ($result ? 'SUCCESS' : 'FAILED') . PHP_EOL;
    
    // Refresh and check
    $user->refresh();
    
    echo PHP_EOL . "Updated user data:" . PHP_EOL;
    echo "Name: " . $user->first_name . ' ' . $user->last_name . PHP_EOL;
    echo "Phone: " . ($user->phone ?? 'NULL') . PHP_EOL;
    echo "User Type: " . ($user->user_type ?? 'NULL') . PHP_EOL;
    echo "Account Status: " . ($user->account_status ?? 'NULL') . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
