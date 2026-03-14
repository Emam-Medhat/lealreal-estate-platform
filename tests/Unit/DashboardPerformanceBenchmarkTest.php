<?php

namespace Tests\Unit;

use App\Models\Agent;
use App\Models\Property;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserWallet;
use App\Services\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class DashboardPerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected DashboardService $dashboardService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dashboardService = new DashboardService;
        Cache::flush();
    }

    public function test_get_user_dashboard_data_performance()
    {
        // 1. Setup Data Manually to avoid factory issues
        $user = User::withoutEvents(function () {
            return User::create([
                'uuid' => Str::uuid(),
                'username' => 'testuser',
                'first_name' => 'Test',
                'last_name' => 'User',
                'full_name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        });

        $agent = Agent::create([
            'user_id' => $user->id,
            'name' => 'Test Agent',
            'email' => 'agent@example.com',
            'phone' => '1234567890',
            'license_number' => 'LIC-123',
            'hire_date' => now(),
            'is_active' => true,
            'is_verified' => true,
        ]);

        for ($i = 0; $i < 10; $i++) {
            Property::create([
                'agent_id' => $agent->id,
                'property_code' => 'PROP-'.Str::random(8),
                'title' => 'Property '.$i,
                'description' => 'Description '.$i,
                'property_type' => 'house',
                'listing_type' => 'sale',
                'price' => 1000000 + ($i * 10000),
                'area' => 200 + $i,
                'address' => 'Address '.$i,
                'city' => 'City',
                'country' => 'Country',
                'status' => 'active',
            ]);
        }

        $today = Carbon::today();
        for ($i = 0; $i < 20; $i++) {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'property_view',
                'created_at' => $today,
            ]);
        }
        for ($i = 0; $i < 10; $i++) {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'search',
                'created_at' => $today,
            ]);
        }
        for ($i = 0; $i < 50; $i++) {
            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'search',
                'created_at' => Carbon::yesterday(),
            ]);
        }

        $properties = Property::limit(10)->get();
        foreach ($properties as $property) {
            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_type' => Property::class,
                'favoritable_id' => $property->id,
                'created_at' => $today,
            ]);
        }

        for ($i = 0; $i < 10; $i++) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Title '.$i, // Added title
                'message' => 'Unread '.$i,
                'is_read' => false,
            ]);
        }
        for ($i = 0; $i < 20; $i++) {
            UserNotification::create([
                'user_id' => $user->id,
                'type' => 'info',
                'title' => 'Title '.$i, // Added title
                'message' => 'Read '.$i,
                'is_read' => true,
            ]);
        }

        UserWallet::create([
            'user_id' => $user->id,
            'balance' => 1000,
        ]);

        // 2. Measure Performance
        Cache::flush();
        DB::flushQueryLog();
        DB::enableQueryLog();

        $start = microtime(true);
        $data = $this->dashboardService->getUserDashboardData($user);
        $end = microtime(true);

        $queryLog = DB::getQueryLog();
        $queryCount = count($queryLog);
        $executionTime = ($end - $start) * 1000;

        echo "\nPerformance Baseline for getUserDashboardData:\n";
        echo "Queries: {$queryCount}\n";
        echo "Execution Time: {$executionTime}ms\n";

        // Basic Assertions
        $this->assertIsArray($data);
        $this->assertEquals(20, $data['properties_viewed_today']);
        $this->assertEquals(60, $data['searches']);
        $this->assertEquals(10, $data['saved_today']);
        $this->assertEquals(30, $data['notifications']);
        $this->assertEquals(10, $data['unread_notifications']);

        $this->assertLessThan(20, $queryCount, 'Too many queries detected!');
    }
}
