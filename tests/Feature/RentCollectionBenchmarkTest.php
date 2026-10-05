<?php

namespace Tests\Feature;

use App\Http\Controllers\RentCollectionController;
use App\Models\RentPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RentCollectionBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_monthly_trends_executes_minimal_queries_and_calculates_accurately()
    {
        // Create rent payments across multiple months
        $now = now();

        // Month 1: 2 months ago
        $date1 = $now->copy()->subMonths(2)->startOfMonth()->addDays(5);
        RentPayment::create([
            'amount' => 1000,
            'paid_amount' => 1000,
            'status' => 'paid',
            'payment_date' => $date1,
            'due_date' => $date1,
            'payment_method' => 'bank_transfer',
        ]);

        // Month 2: Current month
        $date2 = $now->copy()->startOfMonth()->addDays(2);
        RentPayment::create([
            'amount' => 1500,
            'paid_amount' => 1500,
            'status' => 'paid',
            'payment_date' => $date2,
            'due_date' => $date2,
            'payment_method' => 'credit_card',
        ]);

        RentPayment::create([
            'amount' => 500,
            'paid_amount' => 0,
            'status' => 'pending',
            'payment_date' => null,
            'due_date' => $date2,
            'payment_method' => 'cash',
        ]);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $controller = new RentCollectionController();

        // Invoke reflection or call method if public/indirectly
        $reflection = new \ReflectionClass(RentCollectionController::class);
        $method = $reflection->getMethod('getMonthlyTrends');
        $method->setAccessible(true);

        $trends = $method->invoke($controller);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // 2 queries (1 for paid, 1 for due) instead of 24 queries
        $this->assertLessThanOrEqual(2, count($queries), 'Monthly trends query count exceeded 2 queries');

        $this->assertCount(12, $trends);

        // Current month is the last entry in trends
        $currentMonthTrend = end($trends);
        $this->assertEquals($now->format('M Y'), $currentMonthTrend['month']);
        $this->assertEquals(1500, $currentMonthTrend['paid']);
        $this->assertEquals(2000, $currentMonthTrend['due']);
        $this->assertEquals(75, $currentMonthTrend['rate']);
    }
}
