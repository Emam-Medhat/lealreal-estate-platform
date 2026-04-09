<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check if user_activities table exists
    $result = \Illuminate\Support\Facades\DB::select("SHOW TABLES LIKE 'user_activities'");
    echo "User activities table: " . (count($result) > 0 ? "EXISTS" : "MISSING") . "\n";
    
    if (count($result) > 0) {
        // Test the UserActivity model
        try {
            $activityCount = \App\Models\UserActivity::count();
            echo "UserActivity model works, records: {$activityCount}\n";
            
            // Test creating a user activity record
            $testActivity = \App\Models\UserActivity::create([
                'user_id' => 1,
                'activity_type' => 'page_view',
                'activity_category' => 'general',
                'activity_description' => 'Test activity',
                'duration' => 1.5,
                'device_type' => 'desktop',
                'browser' => 'Chrome',
                'platform' => 'Windows',
                'is_mobile' => 0,
                'is_tablet' => 0,
                'is_desktop' => 1,
                'is_bot' => 0,
                'method' => 'GET',
                'url' => '/test',
                'full_url' => 'http://test.com/test',
                'response_status' => 200,
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Test Agent',
                'is_authenticated' => 1,
                'is_admin' => 0,
                'is_premium' => 0,
            ]);
            echo "UserActivity creation: SUCCESS\n";
            
        } catch (Exception $e) {
            echo "UserActivity model error: " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
