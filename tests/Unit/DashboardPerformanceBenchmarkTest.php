<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Agent;
use App\Models\Property;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserComparison;
use App\Models\UserWallet;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected $dashboardService;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService();

        // Setup basic user and data - bypassing UserObserver and satisfying NOT NULL constraints
        $this->user = User::withoutEvents(function () {
            return User::create([
                'first_name' => 'Test',
                'last_name' => 'User',
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
                'uuid' => (string) Str::uuid(),
            ]);
        });

        // Create Agent for the user
        $agent = Agent::create([
            'user_id' => $this->user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'hire_date' => now(),
            'license_number' => 'ABC-123',
            'is_active' => true,
        ]);

        // Create some properties for the agent
        for ($i = 0; $i < 3; $i++) {
            Property::create([
                'agent_id' => $agent->id,
                'title' => "Property $i",
                'description' => "Description $i",
                'price' => 100000 + ($i * 10000),
                'area' => 100 + ($i * 10),
                'area_unit' => 'sqm',
                'property_type' => 'apartment',
                'listing_type' => 'sale',
                'status' => 'active',
                'address' => "Address $i",
                'city' => 'Riyadh',
                'state' => 'Riyadh',
                'country' => 'Saudi Arabia',
                'property_code' => "PROP-$i",
            ]);
        }

        // Create Activity Logs
        UserActivityLog::create([
            'user_id' => $this->user->id,
            'action' => 'property_view',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        UserActivityLog::create([
            'user_id' => $this->user->id,
            'action' => 'search',
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);
        // Set yesterday's search
        DB::table('user_activity_logs')->insert([
            'user_id' => $this->user->id,
            'action' => 'search',
            'created_at' => Carbon::now()->subDays(2)->toDateTimeString(),
            'updated_at' => Carbon::now()->subDays(2)->toDateTimeString(),
        ]);

        // Create Favorites
        $property = Property::first();
        UserFavorite::create([
            'user_id' => $this->user->id,
            'favoritable_type' => Property::class,
            'favoritable_id' => $property->id,
            'created_at' => Carbon::now()->toDateTimeString(),
        ]);

        // Create Notifications
        UserNotification::create([
            'user_id' => $this->user->id,
            'type' => 'info',
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'is_read' => false,
        ]);

        // Create Comparisons
        $comparison = UserComparison::create([
            'user_id' => $this->user->id,
            'name' => 'My Comparison',
            'items' => [$property->id],
            'is_active' => true,
        ]);

        // Create Wallet
        UserWallet::create([
            'user_id' => $this->user->id,
            'balance' => 1000,
            'currency' => 'USD',
        ]);
    }

    /** @test */
    public function it_measures_query_count_for_user_dashboard_data()
    {
        Cache::flush();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $startTime = microtime(true);
        $data = $this->dashboardService->getUserDashboardData($this->user);
        $endTime = microtime(true);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        echo "\nUser Dashboard Data Performance:\n";
        echo "Query Count: $queryCount\n";
        echo "Execution Time: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";

        // Verification of data
        $this->assertEquals(1, $data['properties_viewed_today']);
        $this->assertEquals(1, $data['searches_today'], "Searches today: " . $data['searches_today']);
        $this->assertEquals(2, $data['searches']);
        $this->assertEquals(1, $data['saved_properties']);
        $this->assertEquals(1, $data['unread_notifications']);
        $this->assertEquals(1, $data['comparisons_count']);

        $this->assertLessThan(15, $queryCount, "Query count should be reduced after optimization");
    }
}
