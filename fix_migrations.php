<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Disable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// List of tables to drop
$tables = [
    'project_tasks',
    'project_phases', 
    'projects',
    'blog_post_tag',
    'menu_items',
    'blog_posts',
    'maintenance_schedules',
    'maintenance_requests',
    'appraisals',
    'inspections',
    'inspection_reports',
    'ad_placement_advertisement',
    'ad_conversions',
    'advertisements'
];

// Drop each table if it exists
foreach ($tables as $table) {
    try {
        Schema::dropIfExists($table);
        echo "Dropped table: $table\n";
    } catch (Exception $e) {
        echo "Error dropping $table: " . $e->getMessage() . "\n";
    }
}

// Re-enable foreign key checks
DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Migration cleanup completed!\n";
