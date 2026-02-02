<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Test the view rendering
    $controller = new \App\Http\Controllers\Investor\InvestorController();
    $response = $controller->getInvestmentFunds();
    
    echo "✅ View rendering works!\n";
    echo "Response type: " . get_class($response) . "\n";
    
} catch (Exception $e) {
    echo "❌ خطأ في View: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
