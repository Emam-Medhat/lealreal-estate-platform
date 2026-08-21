<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentAppointment;
use App\Models\User;
use App\Services\AgentAppointmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AgentAppointmentStatsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_appointment_stats_returns_correct_counts_and_executes_single_query(): void
    {
        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Agent',
            'username' => 'john_agent',
            'email' => 'john.agent@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Agent',
            'email' => 'john.agent@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC-1234567',
        ]);

        // Today's appointment (upcoming today, pending)
        AgentAppointment::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'title' => 'Today Meeting',
            'start_datetime' => now()->addMinutes(30),
            'end_datetime' => now()->addMinutes(90),
            'status' => 'pending',
        ]);

        // Completed appointment (past)
        AgentAppointment::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'title' => 'Past Meeting',
            'start_datetime' => now()->subDays(2),
            'end_datetime' => now()->subDays(2)->addHour(),
            'status' => 'completed',
        ]);

        // Confirmed upcoming appointment
        AgentAppointment::create([
            'uuid' => (string) Str::uuid(),
            'agent_id' => $agent->id,
            'title' => 'Tomorrow Meeting',
            'start_datetime' => now()->addDays(1),
            'end_datetime' => now()->addDays(1)->addHour(),
            'status' => 'confirmed',
        ]);

        $service = new AgentAppointmentService;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $service->getAppointmentStats($agent);

        $executedQueries = DB::getQueryLog();

        // Verify database query efficiency: exactly 1 query should be executed
        $this->assertCount(1, $executedQueries, 'getAppointmentStats should execute exactly 1 query.');

        // Verify correct stat calculations
        $this->assertEquals(3, $stats['total_appointments']);
        $this->assertEquals(1, $stats['today_appointments']);
        $this->assertEquals(2, $stats['upcoming_appointments']);
        $this->assertEquals(1, $stats['completed_appointments']);
        $this->assertEquals(1, $stats['pending_appointments']);
        $this->assertEquals(1, $stats['confirmed_appointments']);
    }
}
