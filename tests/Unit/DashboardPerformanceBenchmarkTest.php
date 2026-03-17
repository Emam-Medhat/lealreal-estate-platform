<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use App\Models\Agent;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    private $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();
    }

    public function test_get_user_dashboard_data_query_count()
    {
        // Seed data for the test
        $user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'johndoe' . rand(1, 1000),
                'phone' => '123456789' . rand(1, 1000),
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => $user->full_name,
            'email' => $user->email,
            'phone' => $user->phone ?? '123456789',
            'hire_date' => now(),
            'license_number' => 'LIC' . rand(1, 1000),
            'status' => 'active',
        ]);

        // Activity logs
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'property_view',
            'created_at' => now(),
        ]);
        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'search',
            'created_at' => now(),
        ]);

        // Favorites with Properties
        for ($i = 0; $i < 3; $i++) {
            $property = Property::create([
                'agent_id' => $agent->id,
                'title' => "Property $i",
                'price' => 100000 + ($i * 10000),
                'area' => 120,
                'property_code' => "PROP-$i-" . rand(1, 1000),
                'status' => 'active',
                'description' => 'Test property',
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'address' => 'Test Address',
                'city' => 'Test City',
                'state' => 'Test State',
                'country' => 'Test Country',
                'registration_number' => 'REG' . rand(1, 1000),
                'zoning' => 'Residential',
                'zoning_type' => 'R1',
                'land_use' => 'Residential',
                'ownership_type' => 'Freehold',
            ]);

            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_type' => Property::class,
                'favoritable_id' => $property->id,
            ]);
        }

        // Notifications
        for ($i = 0; $i < 3; $i++) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Test Notification',
                'message' => "Notification $i",
                'is_read' => false,
            ]);
        }

        // Wallet
        UserWallet::create([
            'user_id' => $user->id,
            'balance' => 500.00,
            'currency' => 'USD',
        ]);

        // Measure queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $data = $this->dashboardService->getUserDashboardData($user);

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);

        echo "\nInitial Dashboard Query Count: $queryCount\n";

        $this->assertNotNull($data);
        $this->assertGreaterThan(0, $queryCount);
    }
}
