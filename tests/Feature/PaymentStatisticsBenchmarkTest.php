<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PaymentStatisticsBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_payment_statistics_executes_single_query_and_returns_accurate_stats()
    {
        // Seed test payments with different statuses and amounts
        Payment::create(['transaction_id' => (string) Str::uuid(), 'type' => 'payment', 'payment_method' => 'credit_card', 'amount' => 100.00, 'status' => 'completed']);
        Payment::create(['transaction_id' => (string) Str::uuid(), 'type' => 'payment', 'payment_method' => 'credit_card', 'amount' => 200.00, 'status' => 'completed']);
        Payment::create(['transaction_id' => (string) Str::uuid(), 'type' => 'payment', 'payment_method' => 'credit_card', 'amount' => 50.00, 'status' => 'failed']);
        Payment::create(['transaction_id' => (string) Str::uuid(), 'type' => 'payment', 'payment_method' => 'credit_card', 'amount' => 30.00, 'status' => 'refunded']);
        Payment::create(['transaction_id' => (string) Str::uuid(), 'type' => 'payment', 'payment_method' => 'credit_card', 'amount' => 20.00, 'status' => 'pending']);

        $service = new PaymentService;

        DB::flushQueryLog();
        DB::enableQueryLog();

        $stats = $service->getPaymentStatistics();

        $queryCount = count(DB::getQueryLog());

        // Verify query efficiency
        $this::assertEquals(1, $queryCount, 'getPaymentStatistics should execute exactly 1 database query.');

        // Verify accuracy of calculated statistics
        $this::assertEquals(5, $stats['total_payments']);
        $this::assertEquals(400.00, $stats['total_amount']);
        $this::assertEquals(300.00, $stats['completed_amount']);
        $this::assertEquals(50.00, $stats['failed_amount']);
        $this::assertEquals(30.00, $stats['refunded_amount']);
        $this::assertEquals(20.00, $stats['pending_amount']);
        $this::assertEquals(75.0, $stats['success_rate']); // (300 / 400) * 100
        $this::assertEquals(12.5, $stats['failure_rate']); // (50 / 400) * 100
    }
}
