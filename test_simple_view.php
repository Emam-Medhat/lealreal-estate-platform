<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Get funds data
    $funds = \App\Models\InvestmentFund::where('status', 'active')
        ->orderBy('featured', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();
    
    echo "Testing simple view rendering...\n";
    
    // Test each fund's data
    foreach ($funds as $fund) {
        echo "Fund: {$fund->name}\n";
        echo "Min Investment type: " . gettype($fund->min_investment) . "\n";
        echo "Min Investment value: {$fund->min_investment}\n";
        echo "Expected Return type: " . gettype($fund->expected_return) . "\n";
        echo "Expected Return value: {$fund->expected_return}\n";
        echo "Total Funded type: " . gettype($fund->total_funded) . "\n";
        echo "Total Funded value: {$fund->total_funded}\n";
        echo "---\n";
    }
    
    // Test number_format
    $firstFund = $funds->first();
    echo "Testing number_format:\n";
    echo "Min Investment formatted: " . number_format((float)$firstFund->min_investment) . "\n";
    echo "Expected Return formatted: " . number_format((float)$firstFund->expected_return, 2) . "\n";
    
} catch (Exception $e) {
    echo "❌ خطأ: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
