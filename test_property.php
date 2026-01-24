<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    echo "Testing database connection...\n";
    \DB::connection()->getPdo();
    echo "Connection OK!\n";
    
    echo "Testing Property model...\n";
    $prop = new \App\Models\Property();
    echo "Property model OK!\n";
    
    echo "Testing table exists...\n";
    if (\Schema::hasTable('properties')) {
        echo "Table exists!\n";
        
        echo "Creating test property...\n";
        $prop = new \App\Models\Property();
        $prop->agent_id = 1;
        $prop->title = 'Test Property';
        $prop->description = 'Test Description';
        $prop->property_type = 'apartment';
        $prop->listing_type = 'sale';
        $prop->price = 100000;
        $prop->currency = 'SAR';
        $prop->area = 100;
        $prop->area_unit = 'sq_m';
        $prop->address = 'Test Address';
        $prop->city = 'Test City';
        $prop->country = 'Test Country';
        $prop->status = 'draft';
        $prop->property_code = 'PROP-TEST';
        $prop->save();
        
        echo "Property created with ID: " . $prop->id . "\n";
        
        echo "Total properties in database: " . \App\Models\Property::count() . "\n";
        
    } else {
        echo "Table missing!\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
