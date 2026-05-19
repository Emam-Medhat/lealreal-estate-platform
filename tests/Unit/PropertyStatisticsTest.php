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

    public function test_get_property_statistics_accuracy_and_query_count()
    {
        // Create an agent first as it's required for properties
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);

        // Create some properties with different statuses
        Property::factory()->count(5)->create(['status' => 'active', 'price' => 100000, 'agent_id' => $agent->id]);
        Property::factory()->count(3)->create(['status' => 'sold', 'price' => 200000, 'agent_id' => $agent->id]);
        Property::factory()->count(2)->create(['status' => 'rented', 'price' => 50000, 'agent_id' => $agent->id]);

        $service = new PropertyService();

        DB::enableQueryLog();
        $stats = $service->getPropertyStatistics();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertEquals(10, $stats['total_properties']);
        $this->assertEquals(5, $stats['available_properties'], "Available properties count is wrong");
        $this->assertEquals(3, $stats['sold_properties'], "Sold properties count is wrong");
        $this->assertEquals(2, $stats['rented_properties'], "Rented properties count is wrong");

        // Verify query count - should be exactly 1
        $this->assertEquals(1, $queryCount, "Should execute exactly 1 query");
    }

    public function test_get_property_statistics_with_type_filter()
    {
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);

        Property::factory()->count(2)->create(['property_type' => 'apartment', 'agent_id' => $agent->id]);
        Property::factory()->count(3)->create(['property_type' => 'villa', 'agent_id' => $agent->id]);

        $service = new PropertyService();

        $stats = $service->getPropertyStatistics(['type' => 'apartment']);

        $this->assertEquals(2, $stats['total_properties']);
    }

    public function test_get_available_properties_uses_active_status()
    {
        $user = User::factory()->create();
        $agent = Agent::factory()->create(['user_id' => $user->id]);

        Property::factory()->create(['status' => 'active', 'agent_id' => $agent->id]);
        Property::factory()->create(['status' => 'sold', 'agent_id' => $agent->id]);

        $service = new PropertyService();
        $available = $service->getAvailableProperties();

        $this->assertCount(1, $available);
        $this->assertEquals('active', $available->first()->status);
    }
}
