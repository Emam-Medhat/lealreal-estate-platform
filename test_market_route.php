<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Market Report Controller:\n";

try {
    $controller = app('App\Http\Controllers\Reports\MarketReportController');
    $request = request();
    
    echo "Calling index method...\n";
    $result = $controller->index($request);
    echo "Controller method executed successfully\n";
    
    echo "Response type: " . get_class($result) . "\n";
    
    if (method_exists($result, 'getContent')) {
        $content = $result->getContent();
        echo "Content length: " . strlen($content) . " bytes\n";
        echo "Content preview: " . substr($content, 0, 200) . "...\n";
    }
    
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}

echo "\nChecking route registration:\n";
$route = app('router')->getRoutes()->match(app('request')->create('/reports/market', 'GET'));
if ($route) {
    echo "Route found: " . $route->getName() . "\n";
    echo "Action: " . $route->getActionName() . "\n";
} else {
    echo "Route NOT found\n";
}
