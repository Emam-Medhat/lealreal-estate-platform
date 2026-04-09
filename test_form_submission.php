<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\DocumentTemplateController;
use Illuminate\Http\Request;

echo "Testing DocumentTemplateController update method...\n";

// Find template with ID 5
$template = \App\Models\DocumentTemplate::find(5);

if (!$template) {
    echo "Template with ID 5 not found\n";
    exit;
}

// Create a mock request similar to form submission
$requestData = [
    'name' => 'Test Update Name',
    'description' => 'Test update description',
    'content' => 'Test content with {{name}} and {{date}} variables',
    'category' => 'financial',
    'is_active' => '1',
    'variables' => [
        [
            'name' => 'client_name',
            'description' => 'Client full name',
            'type' => 'text'
        ]
    ]
];

echo "Mock request data:\n";
print_r($requestData);

// Create Request object
$request = Request::create('/document-templates/5', 'PUT', $requestData);

// Create controller instance
$controller = new DocumentTemplateController();

echo "\nTesting validation...\n";

// Test validation rules
$rules = [
    'name' => 'required|string|max:255',
    'description' => 'nullable|string|max:2000',
    'content' => 'required|string',
    'variables' => 'nullable|array',
    'variables.*.name' => 'required|string|max:100',
    'variables.*.description' => 'nullable|string|max:255',
    'variables.*.type' => 'required|in:text,date,number,select,currency,email,phone',
    'variables.*.options' => 'nullable|array',
    'variables.*.options_text' => 'nullable|string',
    'category' => 'required|string|max:100',
    'is_active' => 'boolean',
];

$validator = \Validator::make($requestData, $rules);

if ($validator->fails()) {
    echo "Validation failed:\n";
    print_r($validator->errors()->all());
} else {
    echo "Validation passed!\n";
    
    try {
        // Test the actual update method
        echo "\nTesting controller update method...\n";
        $result = $controller->update($request, $template);
        echo "Update method executed successfully\n";
        echo "Result type: " . get_class($result) . "\n";
        
        // Check if template was actually updated
        $template->refresh();
        echo "Template name after update: {$template->name}\n";
        
    } catch (Exception $e) {
        echo "Update method failed: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
}
