<?php

namespace Tests\Unit;

use App\Models\AdCampaign;
use App\Models\AdPlacement;
use App\Models\Advertisement;
use App\Models\User;
use App\Repositories\Eloquent\AdAnalyticsRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdAnalyticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculate_revenue_efficiency()
    {
        // Setup minimal DB structure & sample data
        $placement = AdPlacement::create([
            'name' => 'Header Banner',
            'position' => 'header',
            'width' => 728,
            'height' => 90,
            'pricing_model' => 'cpm',
            'base_price' => 10.0,
            'min_bid' => 1.0,
            'is_active' => true,
        ]);

        $user = User::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password'),
        ]);

        $campaign = AdCampaign::create([
            'user_id' => $user->id,
            'name' => 'Test Campaign',
            'objective' => 'awareness',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
        ]);

        $ad = Advertisement::create([
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'title' => 'Test Ad',
            'description' => 'Test Description',
            'target_url' => 'https://example.com',
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'daily_budget' => 50,
            'status' => 'active',
            'type' => 'banner',
        ]);

        DB::table('ad_placement_advertisement')->insert([
            'ad_placement_id' => $placement->id,
            'advertisement_id' => $ad->id,
        ]);

        DB::table('ad_impressions')->insert([
            'impression_id' => (string) Str::uuid(),
            'advertisement_id' => $ad->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0',
            'page_url' => 'https://example.com/page',
            'device_type' => 'desktop',
            'viewed_at' => now(),
        ]);

        $repo = new AdAnalyticsRepository;

        DB::enableQueryLog();
        $revenue = $repo->calculateRevenue();
        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $this->assertEquals(0.01, $revenue); // 1 impression / 1000 * 10 = 0.01
        $this->assertLessThanOrEqual(2, $queryCount); // Reduced from 6 queries down to 2
    }
}
