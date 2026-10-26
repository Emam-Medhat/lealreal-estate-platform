<?php

namespace Tests\Feature;

use App\Http\Controllers\AppointmentController;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AppointmentControllerBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_appointment_index_stats_accuracy_and_query_efficiency()
    {
        $user = User::factory()->create();
        $participant = User::factory()->create();

        $start1 = now();
        // Create 2 confirmed appointments today
        Appointment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'participant_id' => $participant->id,
            'title' => 'Confirmed Today 1',
            'start_datetime' => $start1,
            'end_datetime' => $start1->copy()->addMinutes(30),
            'duration' => 30,
            'appointment_type' => 'in-person',
            'status' => 'confirmed',
        ]);

        $start2 = now()->addMinutes(30);
        Appointment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'participant_id' => $participant->id,
            'title' => 'Confirmed Today 2',
            'start_datetime' => $start2,
            'end_datetime' => $start2->copy()->addMinutes(30),
            'duration' => 30,
            'appointment_type' => 'video',
            'status' => 'confirmed',
        ]);

        $start3 = now()->addHours(1);
        // Create 1 pending appointment today
        Appointment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'participant_id' => $participant->id,
            'title' => 'Pending Today',
            'start_datetime' => $start3,
            'end_datetime' => $start3->copy()->addMinutes(30),
            'duration' => 30,
            'appointment_type' => 'voice',
            'status' => 'pending',
        ]);

        $start4 = now()->addDays(1);
        // Create 1 pending appointment tomorrow (not today)
        Appointment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'participant_id' => $participant->id,
            'title' => 'Pending Tomorrow',
            'start_datetime' => $start4,
            'end_datetime' => $start4->copy()->addMinutes(30),
            'duration' => 30,
            'appointment_type' => 'phone',
            'status' => 'pending',
        ]);

        Auth::login($user);

        DB::enableQueryLog();

        $controller = new AppointmentController();
        $request = new Request();
        $response = $controller->index($request);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Retrieve stats passed to the view
        $viewData = $response->getData();
        $this->assertArrayHasKey('stats', $viewData);

        $stats = $viewData['stats'];

        // Assert calculated statistics accuracy
        $this->assertEquals(4, $stats['total']);
        $this->assertEquals(2, $stats['confirmed']);
        $this->assertEquals(2, $stats['pending']);
        $this->assertEquals(3, $stats['today']);

        // Verify only 1 SELECT query containing COUNT(CASE WHEN...) was executed for stats computation
        $statsQueries = array_filter($queries, function ($q) {
            return str_contains($q['query'], 'COUNT(CASE WHEN');
        });
        $this->assertCount(1, $statsQueries);
    }
}
