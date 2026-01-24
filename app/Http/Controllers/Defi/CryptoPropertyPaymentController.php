<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\CryptoPropertyPayment;
use App\Models\Defi\DefiPropertyInvestment;
use App\Models\Defi\PropertyToken;
use App\Models\Defi\FractionalOwnership;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CryptoPropertyPaymentController extends Controller
{
    /**
     * Display a listing of crypto property payments.
     */
    public function index(Request $request)
    {
        $query = CryptoPropertyPayment::with(['user', 'property', 'token', 'investment', 'ownership'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by currency
        if ($request->has('currency') && $request->currency) {
            $query->where('currency', $request->currency);
        }

        // Filter by payment type
        if ($request->has('payment_type') && $request->payment_type) {
            $query->where('payment_type', $request->payment_type);
        }

        // Filter by blockchain
        if ($request->has('blockchain') && $request->blockchain) {
            $query->where('blockchain', $request->blockchain);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get statistics
        $stats = [
            'total_payments' => CryptoPropertyPayment::where('user_id', auth()->id())->count(),
            'completed_payments' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'completed')->count(),
            'total_amount' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'completed')->sum('amount'),
            'total_fees' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'completed')->sum('fee_amount'),
            'total_gas' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'completed')->sum('gas_fee'),
            'pending_payments' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'pending')->count(),
            'failed_payments' => CryptoPropertyPayment::where('user_id', auth()->id())
                ->where('status', 'failed')->count(),
            'currency_distribution' => $this->getCurrencyDistribution(),
            'blockchain_distribution' => $this->getBlockchainDistribution(),
        ];

        return Inertia::render('defi/payments/index', [
            'payments' => $payments,
            'stats' => $stats,
            'filters' => $request->only(['status', 'currency', 'payment_type', 'blockchain', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new crypto property payment.
     */
    public function create()
    {
        // Get available properties for payment
        $properties = MetaverseProperty::where('status', 'active')
            ->where('crypto_payment_enabled', true)
            ->get();

        // Get user's tokens for payment
        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->get();

        // Get user's investments for payment
        $investments = DefiPropertyInvestment::where('user_id', auth()->id())
            ->where('status', 'active')
            ->get();

        return Inertia::render('defi/payments/create', [
            'properties' => $properties,
            'tokens' => $tokens,
            'investments' => $investments,
        ]);
    }

    /**
     * Store a newly created crypto property payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'payment_type' => 'required|in:property_purchase,property_rental,token_purchase,investment_payment,ownership_payment,service_fee,subscription',
            'amount' => 'required|numeric|min:0.000001',
            'currency' => 'required|string|max:10|in:ETH,BTC,USDT,USDC,DAI,MATIC,BNB,SOL,ADA',
            'blockchain' => 'required|string|max:50|in:ethereum,polygon,binance_smart_chain,solana,avalanche,bitcoin,cardano,polkadot',
            'property_id' => 'nullable|exists:metaverse_properties,id',
            'property_token_id' => 'nullable|exists:property_tokens,id',
            'defi_property_investment_id' => 'nullable|exists:defi_property_investments,id',
            'fractional_ownership_id' => 'nullable|exists:fractional_ownerships,id',
            'recipient_address' => 'required|string|max:255',
            'gas_fee' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0|max:10',
            'payment_method' => 'required|string|max:50|in:wallet,exchange,defi_protocol,crypto_card',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        DB::beginTransaction();

        try {
            // Calculate fee amount
            $feeAmount = $request->amount * ($request->fee_percentage / 100);
            $totalAmount = $request->amount + $feeAmount + $request->gas_fee;

            // Create payment
            $payment = CryptoPropertyPayment::create([
                'user_id' => auth()->id(),
                'payment_type' => $request->payment_type,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'blockchain' => $request->blockchain,
                'property_id' => $request->property_id,
                'property_token_id' => $request->property_token_id,
                'defi_property_investment_id' => $request->defi_property_investment_id,
                'fractional_ownership_id' => $request->fractional_ownership_id,
                'sender_address' => auth()->user()->wallet_address,
                'recipient_address' => $request->recipient_address,
                'transaction_hash' => null, // Will be set when processed
                'block_number' => null,
                'gas_fee' => $request->gas_fee,
                'fee_amount' => $feeAmount,
                'fee_percentage' => $request->fee_percentage,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'confirmed_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'retry_count' => 0,
                'max_retries' => 3,
                'description' => $request->description,
                'metadata' => $request->metadata,
                'created_at' => now(),
            ]);

            // Process payment
            $this->processPayment($payment);

            DB::commit();

            return redirect()->route('defi.payments.show', $payment)
                ->with('success', 'تم إنشاء الدفعة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إنشاء الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified crypto property payment.
     */
    public function show(CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الدفعة');
        }

        $payment->load(['user', 'property', 'token', 'investment', 'ownership']);

        // Calculate payment statistics
        $statistics = [
            'current_amount' => $payment->amount,
            'fee_amount' => $payment->fee_amount,
            'gas_fee' => $payment->gas_fee,
            'total_amount' => $payment->total_amount,
            'net_amount' => $payment->amount - $payment->fee_amount - $payment->gas_fee,
            'confirmation_count' => $this->getConfirmationCount($payment),
            'estimated_completion_time' => $this->estimateCompletionTime($payment),
            'can_retry' => $this->canRetryPayment($payment),
            'can_cancel' => $this->canCancelPayment($payment),
            'block_explorer_url' => $this->getBlockExplorerUrl($payment),
            'payment_progress' => $this->calculatePaymentProgress($payment),
        ];

        return Inertia::render('defi/payments/show', [
            'payment' => $payment,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified crypto property payment.
     */
    public function edit(CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment and it's pending
        if ($payment->user_id !== auth()->id() || $payment->status !== 'pending') {
            abort(403, 'لا يمكن تعديل هذه الدفعة');
        }

        return Inertia::render('defi/payments/edit', [
            'payment' => $payment,
        ]);
    }

    /**
     * Update the specified crypto property payment in storage.
     */
    public function update(Request $request, CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment and it's pending
        if ($payment->user_id !== auth()->id() || $payment->status !== 'pending') {
            abort(403, 'لا يمكن تعديل هذه الدفعة');
        }

        $request->validate([
            'recipient_address' => 'required|string|max:255',
            'gas_fee' => 'required|numeric|min:0',
            'fee_percentage' => 'required|numeric|min:0|max:10',
            'description' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
        ]);

        // Recalculate fees
        $feeAmount = $payment->amount * ($request->fee_percentage / 100);
        $totalAmount = $payment->amount + $feeAmount + $request->gas_fee;

        $payment->update([
            'recipient_address' => $request->recipient_address,
            'gas_fee' => $request->gas_fee,
            'fee_amount' => $feeAmount,
            'fee_percentage' => $request->fee_percentage,
            'total_amount' => $totalAmount,
            'description' => $request->description,
            'metadata' => $request->metadata,
            'updated_at' => now(),
        ]);

        return redirect()->route('defi.payments.show', $payment)
            ->with('success', 'تم تحديث الدفعة بنجاحاح');
    }

    /**
     * Remove the specified crypto property payment from storage.
     */
    public function destroy(CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment and it can be cancelled
        if ($payment->user_id !== auth()->id() || !$this->canCancelPayment($payment)) {
            abort(403, 'لا يمكن حذف هذه الدفعة');
        }

        DB::beginTransaction();

        try {
            // Update status to cancelled
            $payment->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('defi.payments.index')
                ->with('success', 'تم إلغاء الدفعة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إلغاء الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Retry failed payment.
     */
    public function retry(Request $request, CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بإعادة محاولة هذه الدفعة');
        }

        if (!$this->canRetryPayment($payment)) {
            return back()->with('error', 'لا يمكن إعادة محاولة هذه الدفعة');
        }

        DB::beginTransaction();

        try {
            // Update retry count
            $payment->increment('retry_count');
            $payment->update([
                'status' => 'pending',
                'transaction_hash' => null,
                'block_number' => null,
                'confirmed_at' => null,
                'completed_at' => null,
                'failed_at' => null,
                'updated_at' => now(),
            ]);

            // Process payment
            $this->processPayment($payment);

            DB::commit();

            return back()->with('success', 'تم إعادة محاولة الدفعة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء إعادة محاولة الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Confirm payment.
     */
    public function confirm(Request $request, CryptoPropertyPayment $payment)
    {
        // Check if user owns the payment
        if ($payment->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بتأكيد هذه الدفعة');
        }

        if ($payment->status !== 'pending') {
            abort(403, 'الدفعة ليست في حالة انتظار');
        }

        DB::beginTransaction();

        try {
            // Update payment status
            $payment->update([
                'status' => 'confirmed',
                'confirmed_at' => now(),
                'updated_at' => now(),
            ]);

            // Complete payment
            $this->completePayment($payment);

            DB::commit();

            return back()->with('success', 'تم تأكيد الدفعة بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تأكيد الدفعة: ' . $e->getMessage());
        }
    }

    /**
     * Get payment analytics.
     */
    public function analytics()
    {
        $userPayments = CryptoPropertyPayment::where('user_id', auth()->id())->get();

        $analytics = [
            'total_payments' => $userPayments->count(),
            'completed_payments' => $userPayments->where('status', 'completed')->count(),
            'failed_payments' => $userPayments->where('status', 'failed')->count(),
            'total_amount' => $userPayments->where('status', 'completed')->sum('amount'),
            'total_fees' => $userPayments->where('status', 'completed')->sum('fee_amount'),
            'total_gas' => $userPayments->where('status', 'completed')->sum('gas_fee'),
            'average_transaction_time' => $this->calculateAverageTransactionTime($userPayments),
            'success_rate' => $this->calculateSuccessRate($userPayments),
            'currency_distribution' => $this->getCurrencyDistribution($userPayments),
            'blockchain_distribution' => $this->getBlockchainDistribution($userPayments),
            'payment_type_distribution' => $this->getPaymentTypeDistribution($userPayments),
            'monthly_spending' => $this->calculateMonthlySpending($userPayments),
            'top_spending_categories' => $this->getTopSpendingCategories($userPayments),
        ];

        return Inertia::render('defi/payments/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get payment history.
     */
    public function history(Request $request)
    {
        $query = CryptoPropertyPayment::with(['property', 'token', 'investment', 'ownership'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['completed', 'failed', 'cancelled']);

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $payments = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('defi/payments/history', [
            'payments' => $payments,
            'filters' => $request->only(['start_date', 'end_date']),
        ]);
    }

    /**
     * Process payment.
     */
    private function processPayment($payment): void
    {
        try {
            // This would integrate with blockchain payment processing
            // For now, simulate payment processing
            
            // Generate transaction hash
            $transactionHash = $this->generateTransactionHash($payment);
            
            // Update payment
            $payment->update([
                'transaction_hash' => $transactionHash,
                'block_number' => rand(1000000, 9999999),
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            // Complete payment
            $this->completePayment($payment);

        } catch (\Exception $e) {
            // Mark payment as failed
            $payment->update([
                'status' => 'failed',
                'failed_at' => now(),
                'error_message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Complete payment.
     */
    private function completePayment($payment): void
    {
        // Update related entities based on payment type
        switch ($payment->payment_type) {
            case 'property_purchase':
                if ($payment->property) {
                    $payment->property->update([
                        'owner_id' => $payment->user_id,
                        'status' => 'sold',
                        'sold_at' => now(),
                    ]);
                }
                break;

            case 'token_purchase':
                if ($payment->token) {
                    // This would handle token purchase logic
                    \Log::info("Token purchase completed for payment {$payment->id}");
                }
                break;

            case 'investment_payment':
                if ($payment->investment) {
                    $payment->investment->update([
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);
                }
                break;

            case 'ownership_payment':
                if ($payment->ownership) {
                    $payment->ownership->update([
                        'status' => 'active',
                        'activated_at' => now(),
                    ]);
                }
                break;
        }

        // Mark payment as completed
        $payment->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Generate transaction hash.
     */
    private function generateTransactionHash($payment): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    /**
     * Get confirmation count.
     */
    private function getConfirmationCount($payment): int
    {
        // This would query the blockchain for confirmation count
        // For now, return a mock count
        if ($payment->status === 'completed') {
            return rand(12, 50);
        }
        
        return 0;
    }

    /**
     * Estimate completion time.
     */
    private function estimateCompletionTime($payment): string
    {
        if ($payment->status === 'completed') {
            return 'مكتمل';
        }

        $blockchain = $payment->blockchain;
        $estimatedMinutes = match ($blockchain) {
            'ethereum' => 15,
            'polygon' => 2,
            'binance_smart_chain' => 3,
            'solana' => 1,
            'avalanche' => 2,
            'bitcoin' => 60,
            'cardano' => 20,
            'polkadot' => 6,
            default => 10,
        };

        return now()->addMinutes($estimatedMinutes)->format('H:i');
    }

    /**
     * Check if payment can be retried.
     */
    private function canRetryPayment($payment): bool
    {
        return $payment->status === 'failed' && 
               $payment->retry_count < $payment->max_retries;
    }

    /**
     * Check if payment can be cancelled.
     */
    private function canCancelPayment($payment): bool
    {
        return in_array($payment->status, ['pending', 'failed']);
    }

    /**
     * Get block explorer URL.
     */
    private function getBlockExplorerUrl($payment): string
    {
        if (!$payment->transaction_hash) {
            return '';
        }

        $explorers = [
            'ethereum' => 'https://etherscan.io/tx/',
            'polygon' => 'https://polygonscan.com/tx/',
            'binance_smart_chain' => 'https://bscscan.com/tx/',
            'solana' => 'https://solscan.io/tx/',
            'avalanche' => 'https://snowtrace.io/tx/',
            'bitcoin' => 'https://blockstream.info/tx/',
            'cardano' => 'https://cardanoscan.io/tx/',
            'polkadot' => 'https://polkascan.io/tx/',
        ];

        $baseUrl = $explorers[$payment->blockchain] ?? '';
        return $baseUrl . $payment->transaction_hash;
    }

    /**
     * Calculate payment progress.
     */
    private function calculatePaymentProgress($payment): array
    {
        $progress = [
            'created' => 100,
            'pending' => $payment->status === 'pending' ? 50 : 100,
            'confirmed' => $payment->status === 'confirmed' ? 75 : ($payment->status === 'completed' ? 100 : 0),
            'completed' => $payment->status === 'completed' ? 100 : 0,
        ];

        return $progress;
    }

    /**
     * Get currency distribution.
     */
    private function getCurrencyDistribution($payments = null): array
    {
        if ($payments === null) {
            $payments = CryptoPropertyPayment::where('user_id', auth()->id())->get();
        }

        return $payments->groupBy('currency')->map->count()->toArray();
    }

    /**
     * Get blockchain distribution.
     */
    private function getBlockchainDistribution($payments = null): array
    {
        if ($payments === null) {
            $payments = CryptoPropertyPayment::where('user_id', auth()->id())->get();
        }

        return $payments->groupBy('blockchain')->map->count()->toArray();
    }

    /**
     * Get payment type distribution.
     */
    private function getPaymentTypeDistribution($payments): array
    {
        return $payments->groupBy('payment_type')->map->count()->toArray();
    }

    /**
     * Calculate average transaction time.
     */
    private function calculateAverageTransactionTime($payments): float
    {
        $completedPayments = $payments->where('status', 'completed');
        
        if ($completedPayments->isEmpty()) {
            return 0;
        }

        $totalTime = $completedPayments->sum(function ($payment) {
            return $payment->created_at->diffInMinutes($payment->completed_at);
        });

        return $totalTime / $completedPayments->count();
    }

    /**
     * Calculate success rate.
     */
    private function calculateSuccessRate($payments): float
    {
        $totalPayments = $payments->count();
        
        if ($totalPayments === 0) {
            return 0;
        }

        $successfulPayments = $payments->whereIn('status', ['completed', 'confirmed'])->count();
        
        return ($successfulPayments / $totalPayments) * 100;
    }

    /**
     * Calculate monthly spending.
     */
    private function calculateMonthlySpending($payments): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthSpending = $payments->where('status', 'completed')
                ->where('created_at', '>=', $date->startOfMonth())
                ->where('created_at', '<=', $date->endOfMonth())
                ->sum('amount');
            
            $monthlyData[$date->format('Y-m')] = $monthSpending;
        }

        return $monthlyData;
    }

    /**
     * Get top spending categories.
     */
    private function getTopSpendingCategories($payments): array
    {
        return $payments->where('status', 'completed')
            ->groupBy('payment_type')
            ->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                    'average_amount' => $group->avg('amount'),
                ];
            })
            ->sortByDesc('total_amount')
            ->take(5)
            ->toArray();
    }
}
