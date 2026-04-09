<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking notary_requests table structure...\n";

// Check if table exists
if (Schema::hasTable('notary_requests')) {
    echo "✅ Table 'notary_requests' exists\n";
    
    // Get column listing
    $columns = Schema::getColumnListing('notary_requests');
    echo "📋 Columns: " . implode(', ', $columns) . "\n";
    
    // Show table structure
    $structure = DB::select('DESCRIBE notary_requests');
    echo "\n📊 Table Structure:\n";
    foreach ($structure as $column) {
        echo "  - {$column->Field} ({$column->Type}) {$column->Null} {$column->Key}\n";
    }
} else {
    echo "❌ Table 'notary_requests' does not exist\n";
}

echo "\n✅ Check completed!\n";
