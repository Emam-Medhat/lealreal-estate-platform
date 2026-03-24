<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\Agent;
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
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_performance_and_accuracy()
    {
        // Setup: Create a user and an agent
        $user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'Agent',
                'last_name' => 'User',
                'username' => 'agentuser',
                'phone' => '1234567890',
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '123456789',
            'license_number' => 'ABC-123',
            'hire_date' => now(),
        ]);

        // Create properties with different statuses
        // The current implementation in PropertyService uses 'available' but the schema is 'active'
        $properties = [
            ['status' => 'active', 'price' => 100000, 'area' => 100, 'property_code' => 'P1'],
            ['status' => 'active', 'price' => 200000, 'area' => 200, 'property_code' => 'P2'],
            ['status' => 'sold', 'price' => 300000, 'area' => 300, 'property_code' => 'P3'],
            ['status' => 'rented', 'price' => 400000, 'area' => 400, 'property_code' => 'P4'],
            ['status' => 'inactive', 'price' => 500000, 'area' => 500, 'property_code' => 'P5'],
        ];

        foreach ($properties as $p) {
            Property::create(array_merge($p, [
                'agent_id' => $agent->id,
                'title' => 'Property ' . $p['property_code'],
                'description' => 'Description',
                'address' => 'Address',
                'city' => 'City',
                'state' => 'State',
                'country' => 'Country',
                'area_unit' => 'sqm',
                'property_type' => 'apartment',
                'listing_type' => 'sale',
            ]));
        }

        // Measure performance: Record the number of database queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics(['agent_id' => $agent->id]);

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);
        DB::disableQueryLog();

        // Output current behavior for debugging
        echo "\nCurrent query count: $queryCount\n";
        echo "Stats: " . json_encode($stats, JSON_PRETTY_PRINT) . "\n";

        // Currently, it should take 6 queries (total, available, sold, rented, sum, avg)
        // However, the current code has a bug where filters stack, likely resulting in 0 counts for later statuses

        // Assertions for accuracy (this will likely fail on current implementation due to 'available' vs 'active' and stacking)
        $this->assertEquals(5, $stats['total_properties'], 'Total properties count should be 5');

        // The following assertions are expected to FAIL with the current implementation
        // 1. 'available_properties' will be 0 because code looks for 'available' but DB has 'active'
        // 2. 'sold_properties' and 'rented_properties' will be 0 because filters stack:
        //    Query becomes WHERE status='available' AND status='sold' (impossible)

        $this->assertEquals(2, $stats['available_properties'], 'Available properties count should be 2 (active)');
        $this->assertEquals(1, $stats['sold_properties'], 'Sold properties count should be 1');
        $this->assertEquals(1, $stats['rented_properties'], 'Rented properties count should be 1');
        $this->assertEquals(1500000, $stats['total_value'], 'Total value should be 1,500,000');
        $this->assertEquals(300000, $stats['average_price'], 'Average price should be 300,000');

        // Assert performance: We want to reach 1 query
        $this->assertLessThanOrEqual(1, $queryCount, 'Database queries should be consolidated into 1');
    }
}
