<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Total users: " . App\Models\User::count() . PHP_EOL;
echo "Admin users: " . App\Models\User::where('user_type', 'admin')->count() . PHP_EOL;
echo "Regular users: " . App\Models\User::where('user_type', 'user')->count() . PHP_EOL;

echo PHP_EOL . "Sample users:" . PHP_EOL;
App\Models\User::take(3)->get(['id', 'email', 'user_type', 'account_status'])->each(function($user) {
    echo "ID: {$user->id}, Email: {$user->email}, Type: {$user->user_type}, Status: {$user->account_status}" . PHP_EOL;
});
