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

    public function test_get_crm_stats_executes_single_query_and_returns_correct_data()
    {
        $user = User::factory()->create();
        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Agent',
            'email' => 'john.agent@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC-123456',
            'hire_date' => now()->subYear(),
        ]);

        // Create sample leads with different attributes
        // Lead 1: Hot lead, converted, created this month
        $lead1 = Lead::create([
            'agent_id' => $agent->id,
            'first_name' => 'Hot',
            'last_name' => 'Lead',
            'email' => 'hot.lead@example.com',
            'temperature' => 85,
            'lead_status' => 'converted',
            'created_by' => $user->id,
        ]);

        // Lead 2: Warm lead, created this month
        $lead2 = Lead::create([
            'agent_id' => $agent->id,
            'first_name' => 'Warm',
            'last_name' => 'Lead',
            'email' => 'warm.lead@example.com',
            'temperature' => 50,
            'lead_status' => 'new',
            'created_by' => $user->id,
        ]);

        // Lead 3: Cold lead, created last month
        $lead3 = Lead::create([
            'agent_id' => $agent->id,
            'first_name' => 'Cold',
            'last_name' => 'Lead',
            'email' => 'cold.lead@example.com',
            'temperature' => 20,
            'lead_status' => 'new',
            'created_by' => $user->id,
        ]);

        // Explicitly set created_at for lead3 to last month
        $lead3->created_at = now()->subMonth();
        $lead3->save();

        $service = app(AgentCrmService::class);
        $service->invalidateCache($agent->id);

        $queryCount = 0;
        DB::listen(function () use (&$queryCount) {
            $queryCount++;
        });

        $stats = $service->getCrmStats($agent);

        $this->assertEquals(1, $queryCount, "getCrmStats should execute exactly 1 database query.");

        $this->assertEquals(3, $stats['total_leads']);
        $this->assertEquals(2, $stats['new_leads_this_month']);
        $this->assertEquals(1, $stats['hot_leads']);
        $this->assertEquals(1, $stats['warm_leads']);
        $this->assertEquals(1, $stats['cold_leads']);
        $this->assertEquals(1, $stats['converted_leads']);
    }
}
