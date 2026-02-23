<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DocumentTemplate;

echo "Testing DocumentTemplate update...\n";

// Find template with ID 5
$template = DocumentTemplate::find(5);

if (!$template) {
    echo "Template with ID 5 not found\n";
    exit;
}

echo "Current template data:\n";
echo "ID: {$template->id}\n";
echo "Name: {$template->name}\n";
echo "Description: " . ($template->description ?? 'No description') . "\n";
echo "Category: {$template->category}\n";
echo "Is Active: " . ($template->is_active ? 'Yes' : 'No') . "\n";

// Simulate update data
$updateData = [
    'name' => 'Updated Template Name',
    'description' => 'Updated description',
    'content' => 'Updated content with variables like {{name}} and {{date}}',
    'variables' => [],
    'category' => 'financial',
    'is_active' => true,
];

echo "\nAttempting update with data:\n";
print_r($updateData);

try {
    $result = $template->update($updateData);
    echo "\nUpdate result: " . ($result ? 'Success' : 'Failed') . "\n";
    
    // Reload to verify
    $template->refresh();
    echo "\nUpdated template data:\n";
    echo "Name: {$template->name}\n";
    echo "Description: " . ($template->description ?? 'No description') . "\n";
    echo "Category: {$template->category}\n";
    
} catch (Exception $e) {
    echo "\nUpdate failed with error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
