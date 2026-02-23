<?php

use App\Models\User;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// 1. Get or Create User
$user = User::first();
if (!$user) {
    echo "Creating dummy user...\n";
    $user = User::create([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => Hash::make('password'),
        'role' => 'admin',
    ]);
}
echo "Using User ID: {$user->id}\n";

// 2. Create Lead Source
$source = LeadSource::first();
if (!$source) {
    echo "Creating dummy lead source...\n";
    $source = LeadSource::create([
        'name' => 'Website',
        'description' => 'Direct website registration',
        'weight' => 10,
        'is_active' => true,
    ]);
}
echo "Using Lead Source ID: {$source->id}\n";

// 3. Create Lead Status
$status = LeadStatus::first();
if (!$status) {
    // Check if LeadStatus model exists and table exists
    // Assuming LeadStatus model exists based on previous search
    try {
        echo "Creating dummy lead status...\n";
        $status = LeadStatus::create([
            'name' => 'New',
            'color' => '#3498db',
            'is_default' => true,
            'order' => 1,
        ]);
    } catch (\Exception $e) {
        echo "LeadStatus creation failed (might not exist or different schema): " . $e->getMessage() . "\n";
        // Fallback: maybe just use ID 1 if table seeded or let it be null if nullable
    }
}
$statusId = $status ? $status->id : 1;
echo "Using Lead Status ID: {$statusId}\n";

// 4. Create Leads
echo "Creating 5 dummy leads...\n";
for ($i = 1; $i <= 5; $i++) {
    try {
        Lead::create([
            'first_name' => "Lead{$i}",
            'last_name' => "Test",
            'email' => "lead{$i}@example.com",
            'phone' => "123456789{$i}",
            'source_id' => $source->id, // Assuming source_id column exists
            'status_id' => $statusId,   // Assuming status_id column exists
            'created_by' => $user->id,
            'priority' => 1, // Required field seen in describe
            'is_active' => 1, // Required
            'is_converted' => 0, // Required
        ]);
        echo "Created Lead {$i}\n";
    } catch (\Exception $e) {
        echo "Failed to create Lead {$i}: " . $e->getMessage() . "\n";
        // Fallback: try with lead_source string if source_id fails
        try {
            Lead::create([
                'first_name' => "Lead{$i}",
                'last_name' => "Test",
                'email' => "lead{$i}@example.com",
                'phone' => "123456789{$i}",
                'lead_source' => 'Website', // Try string column
                'status_id' => $statusId,
                'created_by' => $user->id,
                'priority' => 1,
                'is_active' => 1,
                'is_converted' => 0,
            ]);
            echo "Created Lead {$i} (fallback)\n";
        } catch (\Exception $e2) {
            echo "Failed fallback: " . $e2->getMessage() . "\n";
        }
    }
}

echo "Done.\n";
