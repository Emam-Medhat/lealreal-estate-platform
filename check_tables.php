<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = DB::select("SHOW TABLES LIKE '%subscription%'");
    echo "Subscription-related tables:\n";
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        echo "- $tableName\n";
    }
    
    // Check if main subscription table exists
    $subscriptionTableExists = DB::getSchemaBuilder()->hasTable('subscriptions');
    echo "\nMain subscriptions table exists: " . ($subscriptionTableExists ? 'Yes' : 'No') . "\n";
    
    if ($subscriptionTableExists) {
        $columns = DB::select("DESCRIBE subscriptions");
        echo "\nSubscriptions table columns:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field} ({$column->Type})\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
