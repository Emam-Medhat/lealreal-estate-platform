<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$router = app('router');
$routes = $router->getRoutes();

$targets = [
    'investor.opportunities.index',
    'investors.opportunities.index',
    'investor.funds.index',
    'investors.funds.index',
    'investor.crowdfunding.index',
    'investors.crowdfunding.index',
    'investor.stats.public',
    'investors.stats.public'
];

echo "Verifying routes:\n";
foreach ($targets as $target) {
    if ($routes->hasNamedRoute($target)) {
        echo "✓ Route [$target] exists.\n";
    } else {
        echo "✗ Route [$target] MISSING.\n";
    }
}
