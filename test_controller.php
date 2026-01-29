<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $controller = app('App\Http\Controllers\Reports\MarketReportController');
    $request = request();
    $result = $controller->index($request);
    echo "Controller method works successfully";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
    echo "\nFile: " . $e->getFile();
    echo "\nLine: " . $e->getLine();
}
