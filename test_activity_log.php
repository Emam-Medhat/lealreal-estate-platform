<?php
try {
    $service = app(App\Services\Admin\AdminDashboardService::class);
    $stats = $service->getDashboardStats();
    echo "Recent Activity Count: " . $stats['recent_activity']->count() . "\n";
    foreach ($stats['recent_activity'] as $activity) {
        echo "Activity: " . $activity->description . " (" . $activity->created_at . ")\n";
        echo "Subject Type: " . $activity->subject_type . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
