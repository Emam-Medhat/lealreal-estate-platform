<?php

use App\Models\User;
use App\Services\GamificationService;
use App\Models\PropertyLeaderboard;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a test user if not exists
$user = User::first();
if (!$user) {
    echo "No users found. Creating one...\n";
    $user = User::factory()->create();
}

echo "Testing with user ID: " . $user->id . "\n";

// Ensure leaderboard entry exists
PropertyLeaderboard::create([
    'user_id' => $user->id,
    'type' => 'global',
    'period' => 'all_time',
    'score' => 100,
    'rank' => 1,
    'calculated_at' => now(),
]);

$service = new GamificationService();

try {
    $rank = $service->getUserRank($user);
    echo "User Rank: " . print_r($rank, true) . "\n";
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
