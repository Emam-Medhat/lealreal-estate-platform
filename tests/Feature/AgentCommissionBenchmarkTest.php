<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\Commission;
use App\Models\User;
use App\Services\AgentCommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgentCommissionBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_commission_summary_calculates_correctly_and_optimizes_queries(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Doe Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC-123456',
        ]);

        $lastMonthDate = now()->subMonth()->startOfMonth()->addDays(5);

        $c1 = Commission::create([
            'agent_id' => $agent->id,
            'amount' => 1000.00,
            'percentage' => 2.5,
            'due_date' => $lastMonthDate,
            'type' => 'sale',
            'status' => 'paid',
        ]);
        $c1->created_at = $lastMonthDate;
        $c1->save();

        $c2 = Commission::create([
            'agent_id' => $agent->id,
            'amount' => 2000.00,
            'percentage' => 2.5,
            'due_date' => $lastMonthDate,
            'type' => 'rental',
            'status' => 'paid',
        ]);
        $c2->created_at = $lastMonthDate;
        $c2->save();

        $c3 = Commission::create([
            'agent_id' => $agent->id,
            'amount' => 1500.00,
            'percentage' => 2.5,
            'due_date' => now()->subMonths(3),
            'type' => 'sale',
            'status' => 'paid',
        ]);
        $c3->created_at = now()->subMonths(3);
        $c3->save();

        $service = new AgentCommissionService;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $summary = $service->getCommissionSummary($agent->id, 'month');

        $queryCount = count(DB::getQueryLog());

        $this->assertEquals($agent->id, $summary['agent_id']);
        $this->assertEquals(3000.00, $summary['total_amount']);
        $this->assertEquals(2, $summary['total_count']);
        $this->assertEquals(1500.00, $summary['average_amount']);

        // Check monthly_average (total $4500 / 2 months with commissions = $2250 average across records in window or average of amounts)
        // Note: Commission::avg('amount') across 3 records = (1000 + 2000 + 1500) / 3 = 1500.00
        $this->assertEquals(1500.00, $summary['monthly_average']);

        // Check yearly_projection
        $this->assertArrayHasKey('projected_year_end', $summary['yearly_projection']);
        $this->assertEquals(1500.00, $summary['yearly_projection']['monthly_average']);

        // Query count assertion: Agent::find (1) + Commission get (1) + monthlyAverage (1) + yearlyProjection (1) = 4 queries
        $this->assertLessThanOrEqual(4, $queryCount);
    }
}
