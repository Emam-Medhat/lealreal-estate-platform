<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate the repository call
$filters = [];
$perPage = 20;

echo "Testing getFilteredUsers method..." . PHP_EOL;

try {
    $userRepository = new App\Repositories\Eloquent\UserRepository(new App\Models\User());
    $users = $userRepository->getFilteredUsers($filters, $perPage);
    
    echo "Total users found: " . $users->total() . PHP_EOL;
    echo "Current page: " . $users->currentPage() . PHP_EOL;
    echo "Per page: " . $users->perPage() . PHP_EOL;
    
    if ($users->count() > 0) {
        echo PHP_EOL . "First user data:" . PHP_EOL;
        $firstUser = $users->first();
        echo "ID: " . $firstUser->id . PHP_EOL;
        echo "Email: " . $firstUser->email . PHP_EOL;
        echo "First Name: " . ($firstUser->first_name ?? 'NULL') . PHP_EOL;
        echo "Last Name: " . ($firstUser->last_name ?? 'NULL') . PHP_EOL;
        echo "User Type: " . ($firstUser->user_type ?? 'NULL') . PHP_EOL;
        echo "Account Status: " . ($firstUser->account_status ?? 'NULL') . PHP_EOL;
        echo "Status: " . ($firstUser->status ?? 'NULL') . PHP_EOL;
        echo "Created At: " . $firstUser->created_at . PHP_EOL;
        echo "Username: " . ($firstUser->username ?? 'NULL') . PHP_EOL;
    } else {
        echo "No users found!" . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    echo "Trace: " . $e->getTraceAsString() . PHP_EOL;
}
