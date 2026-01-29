<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Report with ID 3:\n";
$report = App\Models\Report::find(3);

if ($report) {
    echo "Found Report:\n";
    echo "ID: " . $report->id . "\n";
    echo "Title: " . $report->title . "\n";
    echo "Type: " . $report->type . "\n";
    echo "Status: " . $report->status . "\n";
    
    // Check if it has market report
    $marketReport = $report->marketReport;
    if ($marketReport) {
        echo "Market Report Found: Yes\n";
    } else {
        echo "Market Report Found: No\n";
    }
} else {
    echo "Report with ID 3 not found\n";
}

echo "\nAll Reports:\n";
$reports = App\Models\Report::all();
foreach ($reports as $r) {
    echo "ID: {$r->id}, Type: {$r->type}, Title: {$r->title}\n";
}
