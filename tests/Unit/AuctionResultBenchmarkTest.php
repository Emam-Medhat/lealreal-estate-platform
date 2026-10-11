<?php

namespace Tests\Unit;

use App\Models\Auction;
use App\Models\AuctionResult;
use App\Models\User;
use App\Repositories\Eloquent\AuctionResultRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuctionResultBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_stats_calculates_correctly_and_uses_single_query(): void
    {
        // Setup mock user and auction
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'phone' => '1234567890'
        ]);

        $agent = \App\Models\Agent::create([
            'user_id' => $user->id,
            'name' => 'Agent Smith',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'hire_date' => now()->subYears(1),
            'license_number' => 'LIC12345'
        ]);

        $property = \App\Models\Property::create([
            'title' => 'Test Property',
            'description' => 'Test Property Description',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'state' => 'NY',
            'country' => 'USA',
            'area' => 1000,
            'area_unit' => 'sqft',
            'property_code' => 'PROP-12345',
            'property_type' => 'residential',
            'listing_type' => 'sale',
            'price' => 100000,
            'agent_id' => $agent->id,
        ]);

        $auction = Auction::create([
            'title' => 'Sample Auction',
            'description' => 'Sample Auction Description',
            'property_id' => $property->id,
            'seller_id' => $user->id,
            'starting_price' => 1000,
            'status' => 'ended'
        ]);

        // Create results for this month
        AuctionResult::create([
            'auction_id' => $auction->id,
            'winner_id' => $user->id,
            'winning_bid' => 100000.00,
            'final_price' => 100000.00,
            'total_amount' => 100000.00,
            'result_time' => now()->startOfMonth()->addDays(2),
            'status' => 'confirmed'
        ]);

        AuctionResult::create([
            'auction_id' => $auction->id,
            'winner_id' => $user->id,
            'winning_bid' => 200000.00,
            'final_price' => 200000.00,
            'total_amount' => 200000.00,
            'result_time' => now()->startOfMonth()->addDays(5),
            'status' => 'confirmed'
        ]);

        // Create results for last month
        AuctionResult::create([
            'auction_id' => $auction->id,
            'winner_id' => $user->id,
            'winning_bid' => 50000.00,
            'final_price' => 50000.00,
            'total_amount' => 50000.00,
            'result_time' => now()->subMonth()->startOfMonth()->addDays(3),
            'status' => 'confirmed'
        ]);

        $repository = new AuctionResultRepository(new AuctionResult());

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $repository->getStats();

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Verify single query execution
        $this::assertCount(1, $queries, 'Expected getStats to execute exactly 1 database query.');

        // Verify calculations
        $this::assertEquals(3, $stats['total_completed']);
        $this::assertEquals(350000.00, $stats['total_revenue']);
        $this::assertEquals(116666.67, round($stats['average_price'], 2));
        $this::assertEquals(200000.00, $stats['highest_price']);
        $this::assertEquals(50000.00, $stats['lowest_price']);
        $this::assertEquals(2, $stats['this_month']);
        $this::assertEquals(1, $stats['last_month']);
        $this::assertEquals(100.0, $stats['growth_rate']); // (2 - 1) / 1 * 100 = 100%

        // Test calculateGrowthRate reuses getStats without additional queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $growthRate = $repository->calculateGrowthRate();

        $growthQueries = DB::getQueryLog();
        DB::disableQueryLog();

        // Since Cache::remember is used or getStats handles it in 1 query (if uncached)
        $this::assertLessThanOrEqual(1, count($growthQueries));
        $this::assertEquals(100.0, $growthRate);
    }
}
