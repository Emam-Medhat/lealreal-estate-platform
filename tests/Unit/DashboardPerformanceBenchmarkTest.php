<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserComparison;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Models\Property;
use App\Models\Agent;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();
        Cache::flush();
    }

    public function test_get_user_dashboard_data_performance()
    {
        // 1. Setup Data
        // Wrap User creation in withoutEvents to bypass missing UserAnalytics model in UserObserver
        $user = User::withoutEvents(function () {
            return User::factory()->create([
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'johndoe',
                'phone' => '123456789',
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'John Doe',
            'email' => $user->email,
            'phone' => '123456789',
            'hire_date' => now(),
            'license_number' => 'ABC12345',
        ]);

        // Create some activity logs
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

        // Create some properties and favorites
        for ($i = 0; $i < 3; $i++) {
            $property = Property::create([
                'title' => "Property $i",
                'description' => "Description $i",
                'price' => 100000 + ($i * 10000),
                'area' => 100 + ($i * 10),
                'property_code' => "PROP-$i-" . time(),
                'address' => "Address $i",
                'city' => "City $i",
                'state' => "State $i",
                'country' => "Country $i",
                'area_unit' => 'sqm',
                'property_type' => 'house',
                'listing_type' => 'sale',
                'agent_id' => $agent->id,
            ]);

            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_id' => $property->id,
                'favoritable_type' => Property::class,
            ]);
        }

        // Create notifications
        for ($i = 0; $i < 5; $i++) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => "Notification $i",
                'message' => "Message $i",
                'is_read' => false,
            ]);
        }

        // Create wallet
        UserWallet::create([
            'user_id' => $user->id,
            'balance' => 1000,
        ]);

        // 2. Measure Query Count
        DB::flushQueryLog();
        DB::enableQueryLog();

        $data = $this->dashboardService->getUserDashboardData($user);

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        echo "\nTotal Queries: $queryCount\n";

        // 3. Assertions
        $this->assertIsArray($data);
        $this->assertNotEmpty($data);

        // Optimization check: Query count should be 12 (reduced from 20)
        $this->assertLessThanOrEqual(12, $queryCount, "Query count is too high!");
    }
}
