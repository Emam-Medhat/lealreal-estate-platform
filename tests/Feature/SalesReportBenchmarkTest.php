<?php

namespace Tests\Feature;

use App\Http\Controllers\Reports\SalesReportController;
use App\Models\User;
use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Tests\TestCase;

class SalesReportBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_stats_and_monthly_sales_query_count_and_accuracy(): void
    {
        // Populate test sold properties across 3 months
        $now = Carbon::now();
        $month1 = $now->copy()->subMonths(2)->startOfMonth()->addDays(5);
        $month2 = $now->copy()->subMonths(1)->startOfMonth()->addDays(10);
        $month3 = $now->copy()->startOfMonth()->addDays(2);

        // Month 1: 2 properties @ 100000 & 200000 (total = 300000)
        DB::table('properties')->insert([
            'title' => 'Property 1',
            'description' => 'Test Desc 1',
            'price' => 100000,
            'status' => 'sold',
            'area' => 100,
            'property_code' => 'P1',
            'address' => 'Addr 1',
            'city' => 'City 1',
            'state' => 'State 1',
            'country' => 'Country 1',
            'area_unit' => 'sqft',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'agent_id' => 1,
            'created_at' => $month1,
            'updated_at' => $month1,
        ]);

        DB::table('properties')->insert([
            'title' => 'Property 2',
            'description' => 'Test Desc 2',
            'price' => 200000,
            'status' => 'sold',
            'area' => 120,
            'property_code' => 'P2',
            'address' => 'Addr 2',
            'city' => 'City 1',
            'state' => 'State 1',
            'country' => 'Country 1',
            'area_unit' => 'sqft',
            'property_type' => 'apartment',
            'listing_type' => 'sale',
            'agent_id' => 1,
            'created_at' => $month1,
            'updated_at' => $month1,
        ]);

        // Month 2: 1 property @ 300000 (total = 300000)
        DB::table('properties')->insert([
            'title' => 'Property 3',
            'description' => 'Test Desc 3',
            'price' => 300000,
            'status' => 'sold',
            'area' => 150,
            'property_code' => 'P3',
            'address' => 'Addr 3',
            'city' => 'City 2',
            'state' => 'State 2',
            'country' => 'Country 1',
            'area_unit' => 'sqft',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'agent_id' => 1,
            'created_at' => $month2,
            'updated_at' => $month2,
        ]);

        // Month 3: 1 property @ 400000 (total = 400000)
        DB::table('properties')->insert([
            'title' => 'Property 4',
            'description' => 'Test Desc 4',
            'price' => 400000,
            'status' => 'sold',
            'area' => 200,
            'property_code' => 'P4',
            'address' => 'Addr 4',
            'city' => 'City 2',
            'state' => 'State 2',
            'country' => 'Country 1',
            'area_unit' => 'sqft',
            'property_type' => 'villa',
            'listing_type' => 'sale',
            'agent_id' => 1,
            'created_at' => $month3,
            'updated_at' => $month3,
        ]);

        $controller = new SalesReportController();
        $startDate = $now->copy()->subMonths(2)->startOfMonth();
        $endDate = $now->copy()->endOfMonth();

        $reflection = new ReflectionClass($controller);
        $getSalesStatsMethod = $reflection->getMethod('getSalesStats');
        $getSalesStatsMethod->setAccessible(true);

        $getMonthlySalesMethod = $reflection->getMethod('getMonthlySales');
        $getMonthlySalesMethod->setAccessible(true);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $getSalesStatsMethod->invoke($controller, $startDate, $endDate);
        $statsQueries = count(DB::getQueryLog());

        DB::flushQueryLog();

        $monthlySales = $getMonthlySalesMethod->invoke($controller, $startDate, $endDate);
        $monthlyQueries = count(DB::getQueryLog());

        DB::disableQueryLog();

        // Check calculation correctness
        // Total sales = 100000 + 200000 + 300000 + 400000 = 1,000,000
        // Properties sold = 4
        // Total commission = 1000000 * 0.05 = 50,000
        // Average sale price = 1000000 / 4 = 250,000
        $this->assertEquals(1000000, $stats['total_sales']);
        $this->assertEquals(4, $stats['properties_sold']);
        $this->assertEquals(50000, $stats['total_commission']);
        $this->assertEquals(250000, $stats['average_sale_price']);

        // Check query counts: getSalesStats must execute exactly 1 query
        $this->assertEquals(1, $statsQueries, "getSalesStats executed {$statsQueries} queries, expected exactly 1.");

        // Check query counts: getMonthlySales must execute exactly 1 query regardless of month count
        $this->assertEquals(1, $monthlyQueries, "getMonthlySales executed {$monthlyQueries} queries, expected exactly 1.");

        // Verify monthly breakdown accuracy
        $m1Key = $month1->format('Y-m');
        $m2Key = $month2->format('Y-m');
        $m3Key = $month3->format('Y-m');

        $monthlyMap = collect($monthlySales)->keyBy('month');

        $this->assertEquals(300000, $monthlyMap[$m1Key]['sales']);
        $this->assertEquals(2, $monthlyMap[$m1Key]['properties']);

        $this->assertEquals(300000, $monthlyMap[$m2Key]['sales']);
        $this->assertEquals(1, $monthlyMap[$m2Key]['properties']);

        $this->assertEquals(400000, $monthlyMap[$m3Key]['sales']);
        $this->assertEquals(1, $monthlyMap[$m3Key]['properties']);
    }
}
