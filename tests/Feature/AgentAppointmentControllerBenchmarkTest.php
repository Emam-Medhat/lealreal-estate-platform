<?php

namespace Tests\Feature;

use App\Http\Controllers\Agent\AgentAppointmentController;
use App\Models\Agent;
use App\Models\AgentAppointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgentAppointmentControllerBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_appointment_stats_returns_accurate_stats_and_runs_single_query(): void
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
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC123',
        ]);

        AgentAppointment::create([
            'agent_id' => $agent->id,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Meeting 1',
            'status' => 'scheduled',
            'appointment_date' => today()->format('Y-m-d'),
            'start_datetime' => now(),
            'end_datetime' => now()->addHour(),
        ]);

        AgentAppointment::create([
            'agent_id' => $agent->id,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Meeting 2',
            'status' => 'completed',
            'appointment_date' => now()->subDay()->format('Y-m-d'),
            'start_datetime' => now()->subDay(),
            'end_datetime' => now()->subDay()->addHour(),
        ]);

        AgentAppointment::create([
            'agent_id' => $agent->id,
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'title' => 'Meeting 3',
            'status' => 'cancelled',
            'appointment_date' => now()->format('Y-m-d'),
            'start_datetime' => now(),
            'end_datetime' => now()->addHour(),
        ]);

        $this->actingAs($user);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new AgentAppointmentController();
        $response = $controller->getAppointmentStats();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $data = $response->getData(true);

        $this->assertTrue($data['success']);
        $stats = $data['stats'];

        $this->assertEquals(3, $stats['total_appointments']);
        $this->assertEquals(1, $stats['scheduled_appointments']);
        $this->assertEquals(1, $stats['completed_appointments']);
        $this->assertEquals(1, $stats['cancelled_appointments']);
        $this->assertEquals(33.33, $stats['completion_rate']);

        // Assert maximum 2 database queries executed total (1 for user->agent relation lookup, 1 for stats aggregate query)
        $this->assertLessThanOrEqual(2, count($queries), 'Expected 2 or fewer queries, got ' . count($queries));
    }
}
