<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use Illuminate\Support\Facades\Hash;

class TempLeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Get or Create User
        $user = User::first();
        if (!$user) {
            $this->command->info("Creating dummy user...");
            $user = User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }
        $this->command->info("Using User ID: {$user->id}");

        // 2. Create Lead Source
        $source = LeadSource::first();
        if (!$source) {
            $this->command->info("Creating dummy lead source...");
            $source = LeadSource::create([
                'name' => 'Website',
                'description' => 'Direct website registration',
                'weight' => 10,
                'is_active' => true,
            ]);
        }
        $this->command->info("Using Lead Source ID: {$source->id}");

        // 3. Create Lead Status
        $status = LeadStatus::first();
        if (!$status) {
            try {
                $this->command->info("Creating dummy lead status...");
                $status = LeadStatus::create([
                    'name' => 'New',
                    'color' => '#3498db',
                    'is_active' => true, // Changed from is_default to is_active based on model
                    'order' => 1,
                ]);
            } catch (\Exception $e) {
                $this->command->error("LeadStatus creation failed: " . $e->getMessage());
            }
        }
        $statusId = $status ? $status->id : 1;
        $this->command->info("Using Lead Status ID: {$statusId}");

        // 4. Create Leads
        $this->command->info("Creating 5 dummy leads...");
        for ($i = 1; $i <= 5; $i++) {
            try {
                Lead::create([
                    'first_name' => "Lead{$i}",
                    'last_name' => "Test",
                    'email' => "lead{$i}@example.com",
                    'phone' => "123456789{$i}",
                    'source_id' => $source->id,
                    'status_id' => $statusId,
                    'created_by' => $user->id,
                    'priority' => 1,
                    'is_active' => 1,
                    'is_converted' => 0,
                ]);
                $this->command->info("Created Lead {$i}");
            } catch (\Exception $e) {
                $this->command->error("Failed to create Lead {$i}: " . $e->getMessage());
            }
        }
    }
}
