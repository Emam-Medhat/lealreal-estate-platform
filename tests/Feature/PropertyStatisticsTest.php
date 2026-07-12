<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Property;
use App\Models\Agent;
use App\Models\Client;
use App\Models\Company;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyStatisticsTest extends TestCase
{
    use RefreshDatabase;

    private PropertyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PropertyService();
    }

    public function test_get_property_statistics_executes_minimum_queries()
    {
        // 1. Setup data
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'first_name' => 'Test',
            'last_name' => 'User',
            'uuid' => (string) Str::uuid(),
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC-12345',
            'hire_date' => now(),
        ]);

        // Create some properties with different statuses
        // Note: The 'status' enum in migration is: ['draft', 'active', 'inactive', 'sold', 'rented']
        // The Service currently uses 'available' which is NOT in the enum!

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'P1',
            'title' => 'Active Prop',
            'description' => 'Desc',
            'property_type' => 1, // Using integer as per modification migration
            'listing_type' => 'sale',
            'price' => 100000,
            'area' => 100,
            'status' => 'active', // This is what 'available' should probably be
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'P2',
            'title' => 'Sold Prop',
            'description' => 'Desc',
            'property_type' => 1,
            'listing_type' => 'sale',
            'price' => 200000,
            'area' => 150,
            'status' => 'sold',
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country',
        ]);

        Property::create([
            'agent_id' => $user->id,
            'property_code' => 'P3',
            'title' => 'Rented Prop',
            'description' => 'Desc',
            'property_type' => 1,
            'listing_type' => 'rent',
            'price' => 1500,
            'area' => 80,
            'status' => 'rented',
            'address' => 'Addr',
            'city' => 'City',
            'country' => 'Country',
        ]);

        // 2. Measure queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $this->service->getPropertyStatistics();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 3. Assertions
        // Optimized implementation should do exactly 1 query.
        // Also verifying accuracy (considering the 'available' vs 'active' bug fix)

        $this->assertEquals(3, $stats['total_properties']);
        $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);
        $this->assertEquals(301500, $stats['total_value']);

        // We expect the optimized version to have query count == 1
        $this->assertEquals(1, $queryCount, "Should execute exactly 1 query");

        echo "\nOptimized Query Count: $queryCount\n";
    }
}
