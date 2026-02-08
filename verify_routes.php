<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$router = app('router');
$routes = $router->getRoutes();

$targets = [
    'reports.property.index',
    'reports.commission.index',
    'reports.financial.income-statement'
];

echo "Verifying routes:\n";
foreach ($targets as $target) {
    if ($routes->hasNamedRoute($target)) {
        echo "✓ Route [$target] exists.\n";
    } else {
        echo "✗ Route [$target] MISSING.\n";
    }
}
