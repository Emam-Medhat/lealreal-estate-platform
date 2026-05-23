<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Property;
use App\Models\Agent;
use App\Models\User;
use App\Services\PropertyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class PropertyStatisticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected PropertyService $propertyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->propertyService = new PropertyService();
    }

    public function test_get_property_statistics_executes_single_query()
    {
        // Seed some data
        $agent = User::withoutEvents(fn() => User::factory()->create());
        $agentModel = Agent::create([
            'user_id' => $agent->id,
            'name' => 'Test Agent',
            'email' => 'agent@test.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC123',
            'status' => 'active'
        ]);

        Property::factory()->count(5)->create([
            'agent_id' => $agentModel->id,
            'status' => 'active',
            'price' => 100000,
            'property_type' => 'apartment'
        ]);

        Property::factory()->count(3)->create([
            'agent_id' => $agentModel->id,
            'status' => 'sold',
            'price' => 200000,
            'property_type' => 'villa'
        ]);

        Property::factory()->count(2)->create([
            'agent_id' => $agentModel->id,
            'status' => 'rented',
            'price' => 5000,
            'property_type' => 'house'
        ]);

        DB::enableQueryLog();

        $stats = $this->propertyService->getPropertyStatistics();

        $queryLog = DB::getQueryLog();

        // Before optimization, it was 6 queries. Now it should be 1.
        $this->assertCount(1, $queryLog, "Property statistics should be calculated in exactly 1 query.");

        $this->assertEquals(10, $stats['total_properties']);
        $this->assertEquals(5, $stats['available_properties']);
        $this->assertEquals(3, $stats['sold_properties']);
        $this->assertEquals(2, $stats['rented_properties']);
        $this->assertEquals(1110000, $stats['total_value']); // (5*100k) + (3*200k) + (2*5k) = 500k + 600k + 10k = 1.11M
        $this->assertEquals(111000, $stats['average_price']); // 1.11M / 10 = 111k
        $this->assertEquals(50, $stats['availability_rate']);
    }

    public function test_get_property_statistics_with_type_filter()
    {
        $agent = User::withoutEvents(fn() => User::factory()->create());
        $agentModel = Agent::create([
            'user_id' => $agent->id,
            'name' => 'Test Agent',
            'email' => 'agent2@test.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC124',
            'status' => 'active'
        ]);

        Property::factory()->create([
            'agent_id' => $agentModel->id,
            'status' => 'active',
            'property_type' => 'apartment',
            'price' => 100000
        ]);

        Property::factory()->create([
            'agent_id' => $agentModel->id,
            'status' => 'active',
            'property_type' => 'villa',
            'price' => 200000
        ]);

        $stats = $this->propertyService->getPropertyStatistics(['type' => 'apartment']);

        $this->assertEquals(1, $stats['total_properties']);
        $this->assertEquals(1, $stats['available_properties']);
        $this->assertEquals(100000, $stats['total_value']);
    }
}
