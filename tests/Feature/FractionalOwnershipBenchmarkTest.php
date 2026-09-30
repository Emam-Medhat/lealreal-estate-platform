<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Services\FractionalOwnershipService;
use App\Models\PropertyAsset;
use App\Models\FractionalOwnership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class FractionalOwnershipBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_distribute_dividends_updates_shareholders_in_single_query_and_calculates_correctly(): void
    {
        // 1. Create Property and PropertyAsset
        $agent = User::create([
            'first_name' => 'Agent',
            'last_name' => 'Owner',
            'username' => 'agent_' . uniqid(),
            'email' => 'agent_' . uniqid() . '@example.com',
            'phone' => '123456780',
            'password' => bcrypt('password'),
        ]);

        $property = \App\Models\Property::create([
            'area' => 150.0,
            'property_code' => 'PROP-' . uniqid(),
            'title' => 'Luxury Apartment Complex',
            'description' => 'Tokenized luxury property',
            'address' => '123 Main St',
            'city' => 'Metropolis',
            'state' => 'NY',
            'country' => 'USA',
            'area_unit' => 'sqm',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'agent_id' => $agent->id,
            'price' => 100000.00,
            'status' => 'active',
        ]);

        $asset = PropertyAsset::create([
            'property_id' => $property->id,
            'token_id' => 'TOK-1001',
            'title' => 'Luxury Apartment Complex',
            'description' => 'Tokenized luxury property',
            'total_shares' => 1000,
            'available_shares' => 0,
            'share_price' => 100.00,
            'status' => 'sold',
        ]);

        // 2. Create multiple users and fractional ownership records
        $shareholdersCount = 5;
        $users = [];
        for ($i = 0; $i < $shareholdersCount; $i++) {
            $user = User::create([
                'first_name' => 'Investor',
                'last_name' => (string) ($i + 1),
                'username' => 'investor_' . ($i + 1) . '_' . uniqid(),
                'email' => 'investor' . ($i + 1) . '_' . uniqid() . '@example.com',
                'phone' => '123456789' . $i,
                'password' => bcrypt('password'),
            ]);

            FractionalOwnership::create([
                'property_asset_id' => $asset->id,
                'user_id' => $user->id,
                'shares_owned' => 200, // Each user owns 20% (200 / 1000)
                'percentage' => 20.0,
                'purchase_price' => 20000.00,
                'price_per_share' => 100.00,
                'dividends_earned' => 0.00,
                'purchased_at' => now(),
            ]);

            $users[] = $user;
        }

        $service = app(FractionalOwnershipService::class);
        $totalIncome = 10000.00;

        // Measure query count
        DB::enableQueryLog();
        $result = $service->distributeDividends($asset, $totalIncome);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Check calculation correctness
        $this->assertEquals(10000.00, $result['total_distributed']);
        $this->assertEquals(5, $result['shareholders_paid']);
        $this->assertCount(5, $result['breakdown']);

        foreach ($result['breakdown'] as $item) {
            $this->assertEquals(2000.00, $item['dividend']);
        }

        // Verify database records updated
        $ownerships = FractionalOwnership::where('property_asset_id', $asset->id)->get();
        foreach ($ownerships as $ownership) {
            $this->assertEquals(2000.00, (float) $ownership->dividends_earned);
        }

        // Filter update statements on fractional_ownerships
        $updateQueries = array_filter($queries, function ($q) {
            return str_contains(strtolower($q['query']), 'update fractional_ownerships');
        });

        // Optimization check: exactly 1 update query was executed instead of N queries
        $this->assertCount(1, $updateQueries, 'Dividends should be updated in a single batched UPDATE query.');
    }
}
