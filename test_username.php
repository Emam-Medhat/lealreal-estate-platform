<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing username field..." . PHP_EOL;

try {
    $userRepository = new App\Repositories\Eloquent\UserRepository(new App\Models\User());
    $users = $userRepository->getFilteredUsers([], 20);
    
    if ($users->count() > 0) {
        $firstUser = $users->first();
        echo "First user:" . PHP_EOL;
        echo "Username: " . ($firstUser->username ?? 'NULL') . PHP_EOL;
        echo "First Name: " . ($firstUser->first_name ?? 'NULL') . PHP_EOL;
        echo "Last Name: " . ($firstUser->last_name ?? 'NULL') . PHP_EOL;
        echo "Combined: " . ($firstUser->first_name . ' ' . $firstUser->last_name) . PHP_EOL;
        echo "Full expression: " . ($firstUser->username ?? ($firstUser->first_name . ' ' . $firstUser->last_name)) . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}
