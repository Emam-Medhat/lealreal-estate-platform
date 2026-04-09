<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging Route Registration:\n\n";

// Check if routes are loaded
echo "Checking route files in bootstrap/app.php:\n";
$bootstrapContent = file_get_contents('bootstrap/app.php');
if (strpos($bootstrapContent, 'reports.php') !== false) {
    echo "✓ reports.php found in bootstrap\n";
} else {
    echo "✗ reports.php NOT found in bootstrap\n";
}

// Check if reports.php file exists
echo "\nChecking reports.php file:\n";
if (file_exists('routes/reports.php')) {
    echo "✓ routes/reports.php exists\n";
    
    $reportsContent = file_get_contents('routes/reports.php');
    if (strpos($reportsContent, 'MarketReportController') !== false) {
        echo "✓ MarketReportController found in reports.php\n";
    } else {
        echo "✗ MarketReportController NOT found in reports.php\n";
    }
} else {
    echo "✗ routes/reports.php does NOT exist\n";
}

// Check web.php routes
echo "\nChecking web.php routes:\n";
$webContent = file_get_contents('routes/web.php');
if (strpos($webContent, 'MarketReportController') !== false) {
    echo "✓ MarketReportController found in web.php\n";
} else {
    echo "✗ MarketReportController NOT found in web.php\n";
}

// Try to access the route
echo "\nTesting route access:\n";
try {
    $request = \Illuminate\Http\Request::create('/reports/market', 'GET');
    $response = app('router')->dispatch($request);
    echo "✓ Route dispatched successfully\n";
    echo "Status: " . $response->getStatusCode() . "\n";
} catch (\Exception $e) {
    echo "✗ Route dispatch failed: " . $e->getMessage() . "\n";
}

// Check all registered routes
echo "\nChecking all registered routes:\n";
$routes = app('router')->getRoutes();
$marketRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri(), 'market') !== false) {
        $marketRoutes[] = $route->uri() . ' -> ' . $route->getActionName();
    }
}

if (!empty($marketRoutes)) {
    echo "Found market routes:\n";
    foreach ($marketRoutes as $route) {
        echo "  - $route\n";
    }
} else {
    echo "No market routes found\n";
}
