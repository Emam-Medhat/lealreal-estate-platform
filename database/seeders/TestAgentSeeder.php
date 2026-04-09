<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Agent;

class TestAgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an agent record for the first user if it doesn't exist
        $user = User::first();
        if ($user && !Agent::where('user_id', $user->id)->exists()) {
            Agent::create([
                'user_id' => $user->id,
                'name' => $user->name ?? 'Test Agent',
                'email' => $user->email,
                'phone' => $user->phone ?? '000-000-0000',
                'license_number' => 'TEST-' . $user->id,
                'hire_date' => now(),
                'status' => 'active',
                'commission_rate' => 3.5,
            ]);
            
            $this->command->info('Test agent created successfully!');
        } else {
            $this->command->info('Agent already exists or no user found.');
        }
    }
}
