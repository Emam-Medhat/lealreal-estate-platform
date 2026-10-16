<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Lead;
use App\Models\User;
use App\Services\AgentCrmService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AgentCrmServiceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_crm_stats_executes_single_query_and_calculates_correct_metrics()
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'agentjohn',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'hire_date' => now()->subYear(),
            'license_number' => 'LIC-123456',
        ]);

        // Create sample leads for the agent
        // 1. Hot lead created this month
        Lead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'created_by' => $user->id,
            'first_name' => 'Hot',
            'last_name' => 'Lead',
            'email' => 'hot@example.com',
            'lead_status' => 'new',
            'lead_type' => 'buyer',
            'temperature' => 85,
            'created_at' => now(),
        ]);

        // 2. Warm converted lead created this month
        Lead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'created_by' => $user->id,
            'first_name' => 'Warm',
            'last_name' => 'Lead',
            'email' => 'warm@example.com',
            'lead_status' => 'converted',
            'lead_type' => 'buyer',
            'temperature' => 60,
            'created_at' => now(),
        ]);

        // 3. Cold lead created last month
        $coldLead = Lead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'created_by' => $user->id,
            'first_name' => 'Cold',
            'last_name' => 'Lead',
            'email' => 'cold@example.com',
            'lead_status' => 'new',
            'lead_type' => 'buyer',
            'temperature' => 20,
        ]);
        DB::table('leads')->where('id', $coldLead->id)->update(['created_at' => now()->subMonth()]);

        $service = new AgentCrmService();

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $service->getCrmStats($agent);

        $queryCount = count(DB::getQueryLog());

        // Assert query count is exactly 1
        $this->assertEquals(1, $queryCount, "Expected exactly 1 database query for getCrmStats, but got {$queryCount}.");

        // Assert calculation accuracy
        $this->assertEquals(3, $stats['total_leads']);
        $this->assertEquals(2, $stats['new_leads_this_month']);
        $this->assertEquals(1, $stats['hot_leads']);
        $this->assertEquals(1, $stats['warm_leads']);
        $this->assertEquals(1, $stats['cold_leads']);
        $this->assertEquals(1, $stats['converted_leads']);
    }
}
