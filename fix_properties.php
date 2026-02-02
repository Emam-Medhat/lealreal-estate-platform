<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Property;

echo "=== Updating all properties to active status ===\n";
$updated = Property::where('status', '!=', 'active')->update(['status' => 'active']);
echo "Updated {$updated} properties to active status\n";

echo "\n=== Properties after update ===\n";
$properties = Property::get();

foreach ($properties as $property) {
    echo "ID: {$property->id}, Title: {$property->title}, Status: {$property->status}\n";
}

echo "\n=== Testing search for 'villa' ===\n";
$results = Property::where('title', 'like', '%villa%')
    ->orWhere('description', 'like', '%villa%')
    ->orWhere('property_code', 'like', '%villa%')
    ->get();

echo "Found {$results->count()} properties\n";
foreach ($results as $result) {
    echo "- {$result->title}\n";
}
