<?php
use App\Models\ActivityLog;

try {
    $count = ActivityLog::where('subject_type', 'App\Models\Security')->update(['subject_type' => null, 'subject_id' => null]);
    echo "Updated $count records with invalid subject_type 'App\Models\Security'.\n";
    
    // Check other potentially invalid types
    $types = ActivityLog::distinct('subject_type')->pluck('subject_type');
    foreach ($types as $type) {
        if ($type && !class_exists($type)) {
            echo "Found another invalid type: $type\n";
            ActivityLog::where('subject_type', $type)->update(['subject_type' => null, 'subject_id' => null]);
            echo "Updated records for $type.\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
