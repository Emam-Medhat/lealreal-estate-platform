<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\DashboardApiController;
use App\Models\Lead;
use App\Models\Property;
use App\Models\User;
use App\Services\LeadService;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardApiBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_growth_metrics_calculation_and_query_efficiency()
    {
        User::query()->delete();

        $lastMonthDate = now()->firstOfMonth()->subMonth()->addDays(2);
        $thisMonthDate = now()->firstOfMonth()->addDays(2);

        $creator = User::factory()->create(['created_at' => $lastMonthDate]);
        $u2 = User::factory()->create(['created_at' => $lastMonthDate]);
        $u3 = User::factory()->create(['created_at' => $thisMonthDate]);
        $u4 = User::factory()->create(['created_at' => $thisMonthDate]);

        // Seed leads
        Lead::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@example.com',
            'created_by' => $creator->id,
            'created_at' => $lastMonthDate,
        ]);
        Lead::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'jane.smith@example.com',
            'created_by' => $creator->id,
            'created_at' => $thisMonthDate,
        ]);

        $leadService = $this->createMock(LeadService::class);
        $propertyService = $this->createMock(PropertyService::class);
        $controller = new DashboardApiController($leadService, $propertyService);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $request = new \Illuminate\Http\Request();
        $response = $controller->growth($request);

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        $data = $response->getData(true)['data'];

        // Verify calculations:
        // Users: last_month = 2, this_month = 2 -> (2 - 2) / 2 * 100 = 0%
        // Leads: last_month = 1, this_month = 1 -> (1 - 1) / 1 * 100 = 0%
        // Properties: last_month = 0, this_month = 0 -> 0%
        $this->assertEquals(0.0, $data['users_growth']);
        $this->assertEquals(0.0, $data['leads_growth']);
        $this->assertEquals(0.0, $data['properties_growth']);

        // 3 database queries total (1 for each model: Lead, Property, User) instead of 6 queries with whereMonth
        $this->assertLessThanOrEqual(3, $queryCount);
    }
}
