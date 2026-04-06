<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Agent;
use App\Models\User;
use App\Services\AgentPerformanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AgentPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure necessary tables exist for the test in SQLite memory
        Schema::dropIfExists('agent_leads');
        Schema::create('agent_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id');
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('agent_appointments');
        Schema::create('agent_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id');
            $table->date('appointment_date');
            $table->string('status')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::dropIfExists('agent_commissions');
        Schema::create('agent_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id');
            $table->date('commission_date');
            $table->decimal('amount', 15, 2);
            $table->string('payment_status');
            $table->softDeletes();
            $table->timestamps();
        });

        // Ensure properties table exists (it should from migrations, but we need sold_date and price)
        if (!Schema::hasColumn('properties', 'sold_date')) {
            Schema::table('properties', function (Blueprint $table) {
                $table->date('sold_date')->nullable();
            });
        }
    }

    public function test_get_metrics_performance_and_recursion()
    {
        // Disable events to avoid UserObserver issues
        $user = User::withoutEvents(function () {
            return User::create([
                'uuid' => (string) Str::uuid(),
                'first_name' => 'Test',
                'last_name' => 'Agent',
                'username' => 'testagent',
                'email' => 'agent@example.com',
                'password' => bcrypt('password'),
                'phone' => '123456789',
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '123456789',
            'hire_date' => now(),
            'license_number' => 'LIC123',
            'is_active' => true,
        ]);

        $service = new AgentPerformanceService();

        echo "Attempting to call getMetrics...\n";

        try {
            DB::enableQueryLog();
            $metrics = $service->getMetrics($agent->id);
            echo "Queries executed: " . count(DB::getQueryLog()) . "\n";
            $this->assertIsArray($metrics);
            $this->assertEquals($agent->id, $metrics['agent_id']);
            $this->assertArrayHasKey('performance_score', $metrics);
            $this->assertArrayHasKey('trend', $metrics);
        } catch (\Throwable $e) {
            $this->fail("Caught exception: " . get_class($e) . ": " . $e->getMessage() . "\n" . $e->getTraceAsString());
        }
    }
}
