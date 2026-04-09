<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LATEST LOGS ===" . PHP_EOL;

// Get the latest log file
$logFile = storage_path('logs/laravel.log');

if (file_exists($logFile)) {
    // Read last 50 lines of the log file
    $lines = file($logFile);
    $lastLines = array_slice($lines, -50);
    
    foreach ($lastLines as $line) {
        echo $line;
    }
} else {
    echo "No log file found at: " . $logFile . PHP_EOL;
}

echo PHP_EOL . "=== END LOGS ===" . PHP_EOL;
