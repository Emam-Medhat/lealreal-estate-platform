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
use App\Services\DashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DashboardPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_user_dashboard_data_performance()
    {
        $user = User::withoutEvents(function () {
            return User::factory()->create();
        });

        // Create some data
        UserWallet::create(['user_id' => $user->id, 'balance' => 1000]);

        // Create some properties and favorites
        for ($i = 0; $i < 3; $i++) {
            $property = Property::create([
                'title' => "Property $i",
                'description' => "Description $i",
                'price' => 100000 * ($i + 1),
                'agent_id' => $user->id,
                'property_type' => 'house',
                'listing_type' => 'sale',
                'status' => 'active',
                'address' => '123 Street',
                'city' => 'City',
                'state' => 'State',
                'country' => 'Country',
                'property_code' => 'PROP-' . Str::random(8),
                'slug' => 'property-' . $i . '-' . Str::random(5),
                'area' => 100 + $i,
            ]);

            UserFavorite::create([
                'user_id' => $user->id,
                'favoritable_type' => Property::class,
                'favoritable_id' => $property->id,
            ]);
        }

        // Log queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $service = new DashboardService();
        $service->getUserDashboardData($user);

        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        echo "\nTotal queries for getUserDashboardData with data: " . $queryCount . "\n";

        $this->assertGreaterThan(0, $queryCount);
    }
}
