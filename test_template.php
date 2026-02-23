<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DocumentTemplate;

echo "Checking DocumentTemplate with ID 5...\n";

$template = DocumentTemplate::find(5);

if ($template) {
    echo "Template found: " . $template->name . "\n";
    echo "Description: " . ($template->description ?? 'No description') . "\n";
    echo "Category: " . $template->category . "\n";
    echo "Is Active: " . ($template->is_active ? 'Yes' : 'No') . "\n";
    echo "Content length: " . strlen($template->content) . " characters\n";
} else {
    echo "Template with ID 5 not found\n";
}

echo "\nAll templates:\n";
$templates = DocumentTemplate::all();
foreach ($templates as $t) {
    echo "ID: {$t->id}, Name: {$t->name}, Category: {$t->category}\n";
}
