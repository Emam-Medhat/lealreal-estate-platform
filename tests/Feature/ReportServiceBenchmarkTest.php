<?php

namespace Tests\Feature;

use App\Services\ReportService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ReportServiceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure payments table has paid_at column if not created by migrations
        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
                $table->id();
                $table->string('transaction_id')->unique();
                $table->decimal('amount', 15, 2);
                $table->string('payment_method')->default('bank_transfer');
                $table->string('type')->default('payment');
                $table->string('status')->default('completed');
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        } elseif (! Schema::hasColumn('payments', 'paid_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->timestamp('paid_at')->nullable();
            });
        }

        // Ensure expenses table exists for testing cash flow outflows
        if (! Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->id();
                $table->decimal('amount', 15, 2);
                $table->date('date')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_cash_flow_report_accuracy_and_query_reduction()
    {
        // 2025 Data
        DB::table('payments')->insert([
            ['transaction_id' => 'TX1', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 1000.00, 'status' => 'completed', 'paid_at' => '2025-01-15 10:00:00'],
            ['transaction_id' => 'TX2', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 1500.00, 'status' => 'completed', 'paid_at' => '2025-01-20 12:00:00'],
            ['transaction_id' => 'TX3', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 2000.00, 'status' => 'completed', 'paid_at' => '2025-02-10 09:00:00'],
            ['transaction_id' => 'TX4', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 500.00, 'status' => 'pending', 'paid_at' => '2025-02-11 09:00:00'], // non-completed payment ignored
            ['transaction_id' => 'TX5', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 3000.00, 'status' => 'completed', 'paid_at' => '2025-04-05 14:00:00'],
        ]);

        DB::table('expenses')->insert([
            ['amount' => 400.00, 'date' => '2025-01-10'],
            ['amount' => 600.00, 'date' => '2025-02-15'],
            ['amount' => 1000.00, 'date' => '2025-04-12'],
        ]);

        // 2024 Data (for projections)
        DB::table('payments')->insert([
            ['transaction_id' => 'TX6', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 800.00, 'status' => 'completed', 'paid_at' => '2024-01-10 10:00:00'],
            ['transaction_id' => 'TX7', 'payment_method' => 'cash', 'type' => 'payment', 'amount' => 1200.00, 'status' => 'completed', 'paid_at' => '2024-05-15 10:00:00'],
        ]);

        DB::table('expenses')->insert([
            ['amount' => 300.00, 'date' => '2024-01-12'],
            ['amount' => 500.00, 'date' => '2024-05-20'],
        ]);

        /** @var ReportService $reportService */
        $reportService = app(ReportService::class);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $report = $reportService->generateCashFlowReport(2025);

        $queryCount = count(DB::getQueryLog());

        // Assert query efficiency: previously ~82 queries, now 4 bulk queries total
        $this->assertLessThanOrEqual(4, $queryCount, "Expected at most 4 DB queries, executed {$queryCount}");

        // Assert monthly accuracy
        $monthly = $report['monthly_cash_flow'];
        $this->assertEquals(2500.00, $monthly[1]['inflow']); // Jan 2025: 1000 + 1500
        $this->assertEquals(400.00, $monthly[1]['outflow']);
        $this->assertEquals(2100.00, $monthly[1]['net_flow']);

        $this->assertEquals(2000.00, $monthly[2]['inflow']); // Feb 2025
        $this->assertEquals(600.00, $monthly[2]['outflow']);
        $this->assertEquals(1400.00, $monthly[2]['net_flow']);

        $this->assertEquals(3000.00, $monthly[4]['inflow']); // Apr 2025
        $this->assertEquals(1000.00, $monthly[4]['outflow']);

        // Assert quarterly accuracy
        $quarterly = $report['quarterly_cash_flow'];
        $this->assertEquals(4500.00, $quarterly[1]['inflow']); // Q1 (Jan + Feb + Mar): 2500 + 2000 = 4500
        $this->assertEquals(1000.00, $quarterly[1]['outflow']); // Q1: 400 + 600 = 1000
        $this->assertEquals(3500.00, $quarterly[1]['net_flow']);

        $this->assertEquals(3000.00, $quarterly[2]['inflow']); // Q2 (Apr + May + Jun): 3000

        // Assert yearly summary
        $yearly = $report['yearly_summary'];
        $this->assertEquals(2025, $yearly['year']);
        $this->assertEquals(7500.00, $yearly['total_inflow']);
        $this->assertEquals(2000.00, $yearly['total_outflow']);
        $this->assertEquals(5500.00, $yearly['net_cash_flow']);
    }
}
