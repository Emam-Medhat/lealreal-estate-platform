<?php

namespace Tests\Unit;

use App\Models\Agent;
use App\Models\Property;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PropertyServicePerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
    }

    public function test_get_property_statistics_query_count()
    {
        // Use User::withoutEvents to avoid UserObserver errors
        $user = User::withoutEvents(function () {
            return User::factory()->create();
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '123456789',
            'license_number' => 'LIC-123',
            'is_active' => true,
            'is_verified' => true,
            'hire_date' => now(),
        ]);

        $baseData = [
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'agent_id' => $agent->id,
            'area' => 100,
            'property_type' => 'apartment',
            'listing_type' => 'sale',
        ];

        // 10 * 100,000 = 1,000,000
        for ($i = 0; $i < 10; $i++) {
            Property::create(array_merge($baseData, [
                'title' => "Property available $i",
                'status' => 'available',
                'price' => 100000,
            ]));
        }

        // 5 * 200,000 = 1,000,000
        for ($i = 0; $i < 5; $i++) {
            Property::create(array_merge($baseData, [
                'title' => "Property sold $i",
                'status' => 'sold',
                'price' => 200000,
            ]));
        }

        // 5 * 150,000 = 750,000
        for ($i = 0; $i < 5; $i++) {
            Property::create(array_merge($baseData, [
                'title' => "Property rented $i",
                'status' => 'rented',
                'price' => 150000,
            ]));
        }

        // Total = 1,000,000 + 1,000,000 + 750,000 = 2,750,000
        // Avg = 2,750,000 / 20 = 137,500

        // Start counting queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        // Total, available, sold, rented, sum, avg - all in 1 query
        $this->assertEquals(1, $queryCount, 'Should now take only 1 query');

        $this->assertEquals(20, $stats['total_properties']);
        $this->assertEquals(10, $stats['available_properties']);
        $this->assertEquals(5, $stats['sold_properties']);
        $this->assertEquals(5, $stats['rented_properties']);
        $this->assertEquals(2750000, (float) $stats['total_value']);
        $this->assertEquals(137500, (float) $stats['average_price']);
    }
}
