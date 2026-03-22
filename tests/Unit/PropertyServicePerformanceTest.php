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

    protected $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService;
    }

    private function createAgent()
    {
        $user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser'.uniqid(),
                'phone' => '1234567890',
            ]);
        });

        return Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent'.uniqid().'@test.com',
            'phone' => '123456789',
            'hire_date' => now(),
            'license_number' => 'LIC'.uniqid(),
        ]);
    }

    private function getPropertyData(array $overrides = [])
    {
        return array_merge([
            'title' => 'Test Property',
            'status' => 'active',
            'price' => 100000,
            'area' => 100,
            'property_code' => 'PROP'.uniqid(),
            'description' => 'Test Description',
            'address' => 'Test Address',
            'city' => 'Test City',
            'state' => 'Test State',
            'country' => 'Test Country',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
        ], $overrides);
    }

    public function test_get_property_statistics_consolidates_queries()
    {
        $agent = $this->createAgent();

        Property::create($this->getPropertyData([
            'agent_id' => $agent->id,
            'status' => 'active',
            'price' => 100000,
        ]));

        Property::create($this->getPropertyData([
            'agent_id' => $agent->id,
            'status' => 'sold',
            'price' => 200000,
        ]));

        Property::create($this->getPropertyData([
            'agent_id' => $agent->id,
            'status' => 'rented',
            'price' => 300000,
        ]));

        // Track queries
        DB::enableQueryLog();
        $initialQueryCount = count(DB::getQueryLog());

        $stats = $this->propertyService->getPropertyStatistics();

        $finalQueryLog = DB::getQueryLog();
        $queriesForStats = count($finalQueryLog) - $initialQueryCount;

        // Verify query count - should be 1
        $this->assertEquals(1, $queriesForStats, 'Should only perform 1 query for statistics');

        // Verify data accuracy
        $this->assertEquals(3, $stats['total_properties']);
        $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(1, $stats['rented_properties']);
        $this->assertEquals(600000.0, $stats['total_value']);
        $this->assertEquals(200000.0, $stats['average_price']);
    }

    public function test_get_available_properties_uses_correct_status()
    {
        $agent = $this->createAgent();

        Property::create($this->getPropertyData([
            'agent_id' => $agent->id,
            'status' => 'active',
        ]));

        Property::create($this->getPropertyData([
            'agent_id' => $agent->id,
            'status' => 'draft',
        ]));

        $available = $this->propertyService->getAvailableProperties();

        $this->assertCount(1, $available);
        $this->assertEquals('active', $available->first()->status);
    }
}
