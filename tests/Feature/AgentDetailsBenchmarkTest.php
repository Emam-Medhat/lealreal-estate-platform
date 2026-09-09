<?php

namespace Tests\Feature;

use App\Models\Agent;
use App\Models\AgentReview;
use App\Models\Property;
use App\Models\User;
use App\Repositories\Contracts\AgentRepositoryInterface;
use App\Services\AgentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AgentDetailsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_agent_details_returns_correct_stats_and_executes_minimal_queries(): void
    {
        $user = User::create([
            'first_name' => 'Agent',
            'last_name' => 'User',
            'username' => 'agentuser',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Agent',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'LIC-123456',
        ]);

        // Create properties with different statuses
        Property::create([
            'agent_id' => $agent->id,
            'title' => 'Property 1',
            'description' => 'Desc 1',
            'address' => 'Addr 1',
            'city' => 'City 1',
            'state' => 'State 1',
            'country' => 'Country 1',
            'area' => 100,
            'area_unit' => 'sqft',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'property_code' => 'PROP1',
            'status' => 'sold',
            'price' => 100000,
        ]);

        Property::create([
            'agent_id' => $agent->id,
            'title' => 'Property 2',
            'description' => 'Desc 2',
            'address' => 'Addr 2',
            'city' => 'City 2',
            'state' => 'State 2',
            'country' => 'Country 2',
            'area' => 120,
            'area_unit' => 'sqft',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'property_code' => 'PROP2',
            'status' => 'active',
            'price' => 200000,
        ]);

        // Create agent reviews using un-guarded / raw attributes
        $review1 = new AgentReview;
        $review1->agent_id = $agent->id;
        $review1->user_id = $user->id;
        $review1->rating = 4;
        $review1->title = 'Great service';
        $review1->content = 'Great content';
        $review1->save();

        $review2 = new AgentReview;
        $review2->agent_id = $agent->id;
        $review2->user_id = $user->id;
        $review2->rating = 5;
        $review2->title = 'Excellent service';
        $review2->content = 'Excellent content';
        $review2->save();

        $agentRepository = $this->app->make(AgentRepositoryInterface::class);
        $agentService = new AgentService($agentRepository);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $result = $agentService->getAgentDetails($agent);
        $queries = DB::getQueryLog();

        $stats = $result['stats'];

        // Assert stats accuracy
        $this->assertEquals(2, $stats['total_properties']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(2, $stats['total_reviews']);
        $this->assertEquals(4.5, $stats['average_rating']);

        // Assert query count efficiency (8 eager load queries + 2 stat queries)
        $this->assertEquals(10, count($queries));
    }
}
