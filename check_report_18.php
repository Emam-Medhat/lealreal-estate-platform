<?php
$report = \App\Models\Report::find(18);
if ($report) {
    echo "Report 18 found. Type: " . $report->type . "\n";
    $customReport = \App\Models\CustomReport::where('report_id', 18)->first();
    if ($customReport) {
        echo "CustomReport found. ID: " . $customReport->id . "\n";
    } else {
        echo "CustomReport NOT found for report_id 18\n";
    }
} else {
    echo "Report 18 NOT found\n";
}

$customReportById = \App\Models\CustomReport::find(18);
if ($customReportById) {
    echo "CustomReport with ID 18 found. Report ID: " . $customReportById->report_id . "\n";
} else {
    echo "CustomReport with ID 18 NOT found\n";
}
