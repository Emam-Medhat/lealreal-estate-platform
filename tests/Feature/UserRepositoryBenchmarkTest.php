<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\User;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserRepositoryBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_stats_query_count_and_accuracy(): void
    {
        // Disable UserObserver activity logging for clean counts
        User::unsetEventDispatcher();

        // Truncate users table if seeded or existing
        User::query()->forceDelete();

        // Create sample users with various attributes
        User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe1',
            'phone' => '1234567890',
            'account_status' => 'active',
            'user_type' => 'agent',
            'kyc_status' => 'verified',
            'two_factor_enabled' => true,
            'created_at' => now(),
        ]);

        User::factory()->create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'username' => 'janesmith2',
            'phone' => '0987654321',
            'account_status' => 'inactive',
            'user_type' => 'investor',
            'kyc_status' => 'pending',
            'two_factor_enabled' => false,
            'created_at' => now()->subDays(40),
        ]);

        $repository = new UserRepository(new User());

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $repository->getUserStats();

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertEquals(1, $queryCount, "Expected getUserStats to execute exactly 1 query, but executed {$queryCount}. Queries: " . json_encode($queries));

        // Verify calculations
        $this->assertEquals(2, $stats['total_users']);
        $this->assertEquals(1, $stats['active_users']);
        $this->assertEquals(1, $stats['inactive_users']);
        $this->assertEquals(1, $stats['agent_users']);
        $this->assertEquals(1, $stats['investor_users']);
        $this->assertEquals(1, $stats['kyc_verified_users']);
        $this->assertEquals(1, $stats['two_factor_enabled_users']);
        $this->assertEquals(50.0, $stats['activation_rate']);
        $this->assertEquals(50.0, $stats['kyc_completion_rate']);
        $this->assertEquals(50.0, $stats['two_factor_adoption_rate']);
    }

    public function test_dashboard_stats_query_count_and_accuracy(): void
    {
        $user = User::factory()->create([
            'first_name' => 'Agent',
            'last_name' => 'User',
            'username' => 'agentuser',
            'phone' => '1122334455',
        ]);

        // Create properties for user directly via Eloquent model
        Property::create([
            'title' => 'Active Listing',
            'description' => 'Test Desc',
            'address' => '123 Street',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'Egypt',
            'area_unit' => 'sqm',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'area' => 120,
            'property_code' => 'P101',
            'status' => 'active',
            'price' => 1000000,
            'agent_id' => $user->id,
        ]);

        Property::create([
            'title' => 'Sold Property',
            'description' => 'Test Desc',
            'address' => '456 Street',
            'city' => 'Cairo',
            'state' => 'Cairo',
            'country' => 'Egypt',
            'area_unit' => 'sqm',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'area' => 250,
            'property_code' => 'P102',
            'status' => 'sold',
            'price' => 5000000,
            'agent_id' => $user->id,
        ]);

        $repository = new UserRepository(new User());

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $repository->getDashboardStats($user->id);

        $queryCount = count(DB::getQueryLog());

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        // We expect 2 database queries: 1 findById + 1 property aggregate selectRaw query
        $this->assertLessThanOrEqual(2, $queryCount, "Expected getDashboardStats to execute at most 2 queries, executed {$queryCount}. Queries: " . json_encode($queries));

        // Verify calculations
        $this->assertEquals(2, $stats['total_properties']);
        $this->assertEquals(1, $stats['active_listings']);
        $this->assertEquals(1, $stats['sold_properties']);
        $this->assertEquals(5000000.0, $stats['total_sales_value']);
        $this->assertEquals(250000.0, $stats['commission_earned']);
    }
}
