<?php

require_once 'vendor/autoload.php';

// Create Laravel application instance
$app = new Illuminate\Foundation\Application(
    realpath(__DIR__.'/')
);

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    Illuminate\Foundation\Exceptions\Handler::class
);

// Boot the application
$app->boot();

use App\Models\DefiPool;

$pool = DefiPool::find(1);
if ($pool) {
    echo "Pool found: " . $pool->name . "\n";
    echo "Active positions count: " . $pool->activePositions()->count() . "\n";
    echo "Testing withCount method...\n";
    
    $poolWithCount = DefiPool::withCount('activePositions')->find(1);
    if ($poolWithCount) {
        echo "WithCount works! Active positions: " . $poolWithCount->active_positions_count . "\n";
    }
} else {
    echo "Pool not found\n";
}
