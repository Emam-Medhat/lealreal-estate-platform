<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Agent;
use App\Models\Company;
use App\Services\AgentPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class AgentPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $agentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agentService = new AgentPerformanceService();
    }

    public function test_get_metrics_query_count_and_recursion()
    {
        $user = User::withoutEvents(fn() => User::factory()->create());
        $company = Company::create([
            'name' => 'Test Company',
            'status' => 'active',
            'created_by' => $user->id
        ]);
        $agent = Agent::create([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'name' => 'Test Agent',
            'email' => 'agent@test.com',
            'phone' => '123456789',
            'is_active' => true,
            'is_verified' => true,
            'license_number' => 'LIC-123456',
            'hire_date' => now(),
        ]);

        // We expect this to fail due to missing relationships or infinite recursion
        // But for now, let's see how many queries it takes if it doesn't crash

        DB::enableQueryLog();

        try {
            $metrics = $this->agentService->getMetrics($agent->id);
            $queryCount = count(DB::getQueryLog());
            $this->assertIsArray($metrics);
            echo "\nQuery count for getMetrics: " . $queryCount . "\n";
        } catch (\Error $e) {
            echo "\nCaught error in getMetrics: " . $e->getMessage() . "\n";
            $this->assertStringContainsString('performanceMetrics', $e->getMessage());
        } catch (\Exception $e) {
            echo "\nCaught exception in getMetrics: " . $e->getMessage() . "\n";
        }
    }

    public function test_get_ranking_n_plus_one_problem()
    {
        $user0 = User::withoutEvents(fn() => User::factory()->create());
        $company = Company::create([
            'name' => 'Test Company',
            'status' => 'active',
            'created_by' => $user0->id
        ]);

        // Create 5 agents
        $agents = [];
        foreach (range(1, 5) as $i) {
            $user = User::withoutEvents(fn() => User::factory()->create());
            $agents[] = Agent::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'name' => "Agent $i",
                'email' => "agent$i@test.com",
                'phone' => "12345678$i",
                'is_active' => true,
                'is_verified' => true,
                'license_number' => "LIC-$i",
                'hire_date' => now(),
            ]);
        }

        DB::flushQueryLog();
        DB::enableQueryLog();

        try {
            $ranking = $this->agentService->getRanking($agents[0]->id);
            $queryCount = count(DB::getQueryLog());
            echo "\nQuery count for getRanking (5 agents): " . $queryCount . "\n";
            $this->assertIsArray($ranking);
        } catch (\Error $e) {
            echo "\nCaught error in getRanking: " . $e->getMessage() . "\n";
        } catch (\Exception $e) {
            echo "\nCaught exception in getRanking: " . $e->getMessage() . "\n";
        }
    }
}
