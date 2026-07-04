<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_property_statistics_performance_and_correctness()
    {
        // Create an agent first as it is required for property
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'phone' => '1234567890',
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '0987654321',
            'hire_date' => now(),
            'license_number' => 'LIC-1234567',
        ]);

        // Create properties with different statuses
        // Note: Memory says status should be 'active' instead of 'available'
        // But the current code uses 'available'. I will test with 'active' as per schema info in memory.

        $commonData = [
            'agent_id' => $agent->id,
            'area' => 100,
            'property_code' => 'P1',
            'title' => 'Prop',
            'description' => 'Desc',
            'address' => 'Addr',
            'city' => 'City',
            'state' => 'State',
            'country' => 'Country',
            'area_unit' => 'sqm',
            'property_type' => 'house',
            'listing_type' => 'sale',
        ];

        Property::create(array_merge($commonData, ['property_code' => 'P1', 'status' => 'active', 'price' => 100000]));
        Property::create(array_merge($commonData, ['property_code' => 'P2', 'status' => 'active', 'price' => 100000]));
        Property::create(array_merge($commonData, ['property_code' => 'P3', 'status' => 'sold', 'price' => 200000]));
        Property::create(array_merge($commonData, ['property_code' => 'P4', 'status' => 'rented', 'price' => 50000]));

        $service = new PropertyService();

        DB::enableQueryLog();
        $stats = $service->getPropertyStatistics();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        dump($stats);

        // Expected counts: total=4, active=2, sold=1, rented=1
        $this->assertEquals(4, $stats['total_properties']);

        // If current code uses 'available' but DB has 'active', this might fail or return 0
        // We'll see what the current code returns.

        dump("Query count for getPropertyStatistics: " . $queryCount);
    }
}
