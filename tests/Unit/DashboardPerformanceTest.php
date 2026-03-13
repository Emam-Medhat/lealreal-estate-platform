<?php

namespace Tests\Unit;

use App\Models\Property;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService;
        Cache::flush();
    }

    public function test_get_user_dashboard_data_performance()
    {
        // Create user without events to avoid missing UserAnalytics error
        $user = User::withoutEvents(fn () => User::factory()->create());

        // Create some data
        UserActivityLog::create(['user_id' => $user->id, 'action' => 'property_view', 'created_at' => now()]);
        UserActivityLog::create(['user_id' => $user->id, 'action' => 'search', 'created_at' => now()]);
        UserActivityLog::create(['user_id' => $user->id, 'action' => 'search', 'created_at' => now()->subDay()]);

        $property = Property::create([
            'title' => 'Test Property',
            'slug' => 'test-property',
            'price' => 100000,
            'status' => 'active',
            'agent_id' => $user->id,
            'description' => 'Test description',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'area' => 100,
            'address' => 'Test address',
            'city' => 'Test city',
            'country' => 'Test country',
        ]);

        UserFavorite::create([
            'user_id' => $user->id,
            'favoritable_type' => Property::class,
            'favoritable_id' => $property->id,
        ]);

        UserNotification::create([
            'user_id' => $user->id,
            'type' => 'info',
            'title' => 'Test',
            'message' => 'Test notification',
            'is_read' => false,
        ]);

        UserWallet::create([
            'user_id' => $user->id,
            'balance' => 1000,
        ]);

        // Clear cache again to ensure we measure query count
        Cache::flush();

        // Measure queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $start = microtime(true);
        $data = $this->dashboardService->getUserDashboardData($user);
        $end = microtime(true);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        $timeTaken = ($end - $start) * 1000;

        echo "\nDashboard Performance:\n";
        echo "Queries: {$queryCount}\n";
        echo 'Time: '.number_format($timeTaken, 2)."ms\n";

        $this->assertIsArray($data);
        // We will update this assertion after optimization
        // Currently expected to be around 14 based on memory
        // Let's see what it actually is in this environment
    }
}
