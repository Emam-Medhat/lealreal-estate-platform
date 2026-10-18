<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Http\Controllers\Agent\AgentLeadController;
use App\Models\Agent;
use App\Models\AgentLead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgentLeadStatsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_lead_stats_executes_single_query_and_calculates_correctly()
    {
        /** @var User $user */
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe_' . uniqid(),
            'phone' => '1234567890',
        ]);

        /** @var Agent $agent */
        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent_' . uniqid() . '@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC-' . rand(1000, 9999),
            'hire_date' => now(),
        ]);

        // Create 2 new leads created this month
        AgentLead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Lead',
            'last_name' => 'One',
            'email' => 'lead1@example.com',
            'lead_status' => 'new',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        AgentLead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Lead',
            'last_name' => 'Two',
            'email' => 'lead2@example.com',
            'lead_status' => 'new',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        // Create 1 contacted lead created this month
        AgentLead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Lead',
            'last_name' => 'Three',
            'email' => 'lead3@example.com',
            'lead_status' => 'contacted',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        // Create 1 qualified lead created last month
        $leadFour = AgentLead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Lead',
            'last_name' => 'Four',
            'email' => 'lead4@example.com',
            'lead_status' => 'qualified',
            'created_by' => $user->id,
        ]);
        $leadFour->created_at = now()->subMonth();
        $leadFour->save(['timestamps' => false]);

        // Create 1 converted lead created this month
        AgentLead::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'first_name' => 'Lead',
            'last_name' => 'Five',
            'email' => 'lead5@example.com',
            'lead_status' => 'converted',
            'created_by' => $user->id,
            'created_at' => now(),
        ]);

        $controller = app(AgentLeadController::class);
        $this->actingAs($user);

        // Warm up relationship query Auth::user()->agent
        $agentModel = $user->agent;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $controller->getLeadStats();

        $queryLog = DB::getQueryLog();
        DB::disableQueryLog();

        // Exactly 1 query for leads aggregation
        $this->assertCount(1, $queryLog, 'Expected exactly 1 database query for getLeadStats execution.');

        $data = $response->getData(true);
        $this::assertTrue($data['success']);

        $stats = $data['stats'];
        $this->assertEquals(5, $stats['total_leads']);
        $this->assertEquals(2, $stats['new_leads']);
        $this->assertEquals(1, $stats['contacted_leads']);
        $this->assertEquals(1, $stats['qualified_leads']);
        $this->assertEquals(1, $stats['converted_leads']);
        $this->assertEquals(0, $stats['lost_leads']);
        $this->assertEquals(20.0, $stats['conversion_rate']); // (1 / 5) * 100
        $this->assertEquals(4, $stats['this_month_leads']); // 2 new + 1 contacted + 1 converted
    }
}
