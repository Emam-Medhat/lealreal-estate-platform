<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use App\Services\AgentCrmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgentCrmStatsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_crm_stats_executes_single_query_and_returns_accurate_stats(): void
    {
        // Create user for created_by
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        // Create agent
        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567891',
            'hire_date' => now()->subYear(),
            'license_number' => 'LIC123456',
            'status' => 'active',
        ]);

        // Create leads with various temperatures and statuses
        // 1. Hot lead, converted, created this month
        Lead::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Hot',
            'last_name' => 'Lead',
            'email' => 'hot@example.com',
            'temperature' => 85,
            'lead_status' => 'converted',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        // 2. Warm lead, active, created this month
        Lead::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Warm',
            'last_name' => 'Lead',
            'email' => 'warm@example.com',
            'temperature' => 50,
            'lead_status' => 'new',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        // 3. Cold lead, new, created last month
        Lead::forceCreate([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Cold',
            'last_name' => 'Lead',
            'email' => 'cold@example.com',
            'temperature' => 20,
            'lead_status' => 'new',
            'created_by' => $user->id,
            'created_at' => now()->subMonth(),
            'updated_at' => now()->subMonth(),
        ]);

        $service = new AgentCrmService();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $service->getCrmStats($agent);

        $queryCount = count(DB::getQueryLog());

        // Assert query count is 1
        $this->assertEquals(1, $queryCount, "Expected getCrmStats to perform exactly 1 query, but {$queryCount} were executed.");

        // Assert correct stat values
        $this->assertEquals(3, $stats['total_leads']);
        $this->assertEquals(2, $stats['new_leads_this_month']);
        $this->assertEquals(1, $stats['hot_leads']);
        $this->assertEquals(1, $stats['warm_leads']);
        $this->assertEquals(1, $stats['cold_leads']);
        $this->assertEquals(1, $stats['converted_leads']);
    }
}
