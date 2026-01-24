<?php

namespace App\Http\Controllers\Defi;

use App\Http\Controllers\Controller;
use App\Models\Defi\DefiPropertyLoan;
use App\Models\Defi\DefiCollateral;
use App\Models\Defi\PropertyToken;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\User;
use App\Http\Requests\Defi\ApplyDefiLoanRequest;
use App\Http\Requests\Defi\ApproveDefiLoanRequest;
use App\Http\Requests\Defi\RepayDefiLoanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DefiPropertyLoanController extends Controller
{
    /**
     * Display a listing of DeFi property loans.
     */
    public function index(Request $request)
    {
        $query = DefiPropertyLoan::with(['user', 'property', 'collateral', 'repayments'])
            ->where('user_id', auth()->id());

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by loan type
        if ($request->has('loan_type') && $request->loan_type) {
            $query->where('loan_type', $request->loan_type);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $loans = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get statistics
        $stats = [
            'total_loans' => DefiPropertyLoan::where('user_id', auth()->id())->count(),
            'active_loans' => DefiPropertyLoan::where('user_id', auth()->id())
                ->where('status', 'active')->count(),
            'total_borrowed' => DefiPropertyLoan::where('user_id', auth()->id())
                ->where('status', '!=', 'rejected')->sum('amount'),
            'total_repaid' => DefiPropertyLoan::where('user_id', auth()->id())
                ->where('status', '!=', 'rejected')->sum('repaid_amount'),
            'total_interest' => DefiPropertyLoan::where('user_id', auth()->id())
                ->where('status', '!=', 'rejected')->sum('total_interest'),
        ];

        return Inertia::render('defi/property-loans/index', [
            'loans' => $loans,
            'stats' => $stats,
            'filters' => $request->only(['status', 'loan_type', 'start_date', 'end_date']),
        ]);
    }

    /**
     * Show the form for creating a new DeFi property loan.
     */
    public function create()
    {
        // Get user's properties for collateral
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->get();

        // Get user's tokens for collateral
        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->get();

        return Inertia::render('defi/property-loans/create', [
            'properties' => $properties,
            'tokens' => $tokens,
        ]);
    }

    /**
     * Store a newly created DeFi property loan in storage.
     */
    public function store(ApplyDefiLoanRequest $request)
    {
        DB::beginTransaction();

        try {
            // Create the loan
            $loan = DefiPropertyLoan::create([
                'user_id' => auth()->id(),
                'property_id' => $request->property_id,
                'loan_type' => $request->loan_type,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'interest_rate' => $request->interest_rate,
                'loan_term' => $request->loan_term,
                'collateral_type' => $request->collateral_type,
                'collateral_value' => $request->collateral_value,
                'collateral_details' => $request->collateral_details,
                'purpose' => $request->purpose,
                'risk_assessment' => $this->assessRisk($request),
                'credit_score' => $this->calculateCreditScore(auth()->user()),
                'status' => 'pending',
                'application_hash' => $this->generateApplicationHash(),
                'smart_contract_address' => null, // Will be set when approved
                'created_at' => now(),
            ]);

            // Create collateral record if provided
            if ($request->collateral_type === 'property' && $request->property_id) {
                DefiCollateral::create([
                    'defi_property_loan_id' => $loan->id,
                    'collateral_type' => 'property',
                    'collateral_id' => $request->property_id,
                    'value' => $request->collateral_value,
                    'status' => 'pending',
                    'locked_at' => null,
                ]);
            } elseif ($request->collateral_type === 'tokens' && $request->token_ids) {
                foreach ($request->token_ids as $tokenId) {
                    DefiCollateral::create([
                        'defi_property_loan_id' => $loan->id,
                        'collateral_type' => 'token',
                        'collateral_id' => $tokenId,
                        'value' => $this->getTokenValue($tokenId),
                        'status' => 'pending',
                        'locked_at' => null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('defi.loans.show', $loan)
                ->with('success', 'تم تقديم طلب القرض بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تقديم طلب القرض: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified DeFi property loan.
     */
    public function show(DefiPropertyLoan $loan)
    {
        // Check if user owns the loan
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذا القرض');
        }

        $loan->load(['user', 'property', 'collateral', 'repayments', 'transactions']);

        // Calculate loan statistics
        $statistics = [
            'remaining_balance' => $loan->amount - $loan->repaid_amount,
            'remaining_interest' => $loan->total_interest - $loan->paid_interest,
            'total_remaining' => ($loan->amount + $loan->total_interest) - ($loan->repaid_amount + $loan->paid_interest),
            'progress_percentage' => $this->calculateProgressPercentage($loan),
            'next_payment_due' => $this->calculateNextPaymentDue($loan),
            'days_overdue' => $this->calculateDaysOverdue($loan),
        ];

        return Inertia::render('defi/property-loans/show', [
            'loan' => $loan,
            'statistics' => $statistics,
        ]);
    }

    /**
     * Show the form for editing the specified DeFi property loan.
     */
    public function edit(DefiPropertyLoan $loan)
    {
        // Check if user owns the loan and it's pending
        if ($loan->user_id !== auth()->id() || $loan->status !== 'pending') {
            abort(403, 'لا يمكن تعديل هذا القرض');
        }

        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->get();

        $tokens = PropertyToken::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->get();

        return Inertia::render('defi/property-loans/edit', [
            'loan' => $loan,
            'properties' => $properties,
            'tokens' => $tokens,
        ]);
    }

    /**
     * Update the specified DeFi property loan in storage.
     */
    public function update(ApplyDefiLoanRequest $request, DefiPropertyLoan $loan)
    {
        // Check if user owns the loan and it's pending
        if ($loan->user_id !== auth()->id() || $loan->status !== 'pending') {
            abort(403, 'لا يمكن تعديل هذا القرض');
        }

        DB::beginTransaction();

        try {
            $loan->update([
                'amount' => $request->amount,
                'currency' => $request->currency,
                'interest_rate' => $request->interest_rate,
                'loan_term' => $request->loan_term,
                'collateral_type' => $request->collateral_type,
                'collateral_value' => $request->collateral_value,
                'collateral_details' => $request->collateral_details,
                'purpose' => $request->purpose,
                'risk_assessment' => $this->assessRisk($request),
                'updated_at' => now(),
            ]);

            // Update collateral
            $loan->collateral()->delete();

            if ($request->collateral_type === 'property' && $request->property_id) {
                DefiCollateral::create([
                    'defi_property_loan_id' => $loan->id,
                    'collateral_type' => 'property',
                    'collateral_id' => $request->property_id,
                    'value' => $request->collateral_value,
                    'status' => 'pending',
                    'locked_at' => null,
                ]);
            } elseif ($request->collateral_type === 'tokens' && $request->token_ids) {
                foreach ($request->token_ids as $tokenId) {
                    DefiCollateral::create([
                        'defi_property_loan_id' => $loan->id,
                        'collateral_type' => 'token',
                        'collateral_id' => $tokenId,
                        'value' => $this->getTokenValue($tokenId),
                        'status' => 'pending',
                        'locked_at' => null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('defi.loans.show', $loan)
                ->with('success', 'تم تحديث طلب القرض بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء تحديث طلب القرض: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified DeFi property loan from storage.
     */
    public function destroy(DefiPropertyLoan $loan)
    {
        // Check if user owns the loan and it's pending
        if ($loan->user_id !== auth()->id() || $loan->status !== 'pending') {
            abort(403, 'لا يمكن حذف هذا القرض');
        }

        DB::beginTransaction();

        try {
            // Delete collateral
            $loan->collateral()->delete();
            
            // Delete loan
            $loan->delete();

            DB::commit();

            return redirect()->route('defi.loans.index')
                ->with('success', 'تم حذف طلب القرض بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء حذف طلب القرض: ' . $e->getMessage());
        }
    }

    /**
     * Approve a loan (admin function).
     */
    public function approve(ApproveDefiLoanRequest $request, DefiPropertyLoan $loan)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('defi_manager')) {
            abort(403, 'غير مصرح لك بالموافقة على القروض');
        }

        DB::beginTransaction();

        try {
            // Deploy smart contract
            $smartContractAddress = $this->deployLoanSmartContract($loan);

            // Lock collateral
            $loan->collateral()->update([
                'status' => 'locked',
                'locked_at' => now(),
            ]);

            // Update loan status
            $loan->update([
                'status' => 'active',
                'smart_contract_address' => $smartContractAddress,
                'approved_at' => now(),
                'approved_by' => auth()->id(),
                'disbursed_at' => now(),
                'next_payment_date' => now()->addMonth(),
            ]);

            // Create transaction record
            $this->createTransaction($loan, 'disbursement');

            DB::commit();

            return back()->with('success', 'تم الموافقة على القرض ونشر العقد الذكي');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء الموافقة على القرض: ' . $e->getMessage());
        }
    }

    /**
     * Reject a loan (admin function).
     */
    public function reject(Request $request, DefiPropertyLoan $loan)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('defi_manager')) {
            abort(403, 'غير مصرح لك برفض القروض');
        }

        $loan->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Unlock collateral
        $loan->collateral()->update([
            'status' => 'released',
            'locked_at' => null,
        ]);

        return back()->with('success', 'تم رفض طلب القرض');
    }

    /**
     * Make a loan repayment.
     */
    public function repay(RepayDefiLoanRequest $request, DefiPropertyLoan $loan)
    {
        // Check if user owns the loan
        if ($loan->user_id !== auth()->id()) {
            abort(403, 'غير مصرح لك بالسداد على هذا القرض');
        }

        if ($loan->status !== 'active') {
            abort(403, 'القرض غير نشط');
        }

        DB::beginTransaction();

        try {
            // Process payment
            $paymentResult = $this->processRepayment($loan, $request->amount);

            if (!$paymentResult['success']) {
                return back()->with('error', $paymentResult['message']);
            }

            // Update loan
            $loan->update([
                'repaid_amount' => $loan->repaid_amount + $request->amount,
                'paid_interest' => $loan->paid_interest + $paymentResult['interest_paid'],
                'last_payment_date' => now(),
                'next_payment_date' => now()->addMonth(),
            ]);

            // Check if loan is fully paid
            if ($loan->repaid_amount >= $loan->amount) {
                $loan->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // Release collateral
                $loan->collateral()->update([
                    'status' => 'released',
                    'locked_at' => null,
                ]);
            }

            // Create transaction record
            $this->createTransaction($loan, 'repayment', $request->amount);

            DB::commit();

            return back()->with('success', 'تم سداد القسط بنجاحاح');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'حدث خطأ أثناء سداد القسط: ' . $e->getMessage());
        }
    }

    /**
     * Get loan analytics.
     */
    public function analytics()
    {
        $userLoans = DefiPropertyLoan::where('user_id', auth()->id())->get();

        $analytics = [
            'total_borrowed' => $userLoans->sum('amount'),
            'total_repaid' => $userLoans->sum('repaid_amount'),
            'total_interest' => $userLoans->sum('total_interest'),
            'active_loans' => $userLoans->where('status', 'active')->count(),
            'completed_loans' => $userLoans->where('status', 'completed')->count(),
            'rejected_loans' => $userLoans->where('status', 'rejected')->count(),
            'average_interest_rate' => $userLoans->avg('interest_rate'),
            'loan_types' => $userLoans->groupBy('loan_type')->map->count(),
            'monthly_borrowing' => $this->calculateMonthlyBorrowing($userLoans),
            'credit_score_trend' => $this->getCreditScoreTrend(),
        ];

        return Inertia::render('defi/property-loans/analytics', [
            'analytics' => $analytics,
        ]);
    }

    /**
     * Get loan marketplace.
     */
    public function marketplace(Request $request)
    {
        $query = DefiPropertyLoan::with(['user', 'property'])
            ->where('status', 'active')
            ->where('is_public', true);

        // Filter by loan type
        if ($request->has('loan_type') && $request->loan_type) {
            $query->where('loan_type', $request->loan_type);
        }

        // Filter by interest rate range
        if ($request->has('min_rate') && $request->min_rate) {
            $query->where('interest_rate', '>=', $request->min_rate);
        }

        if ($request->has('max_rate') && $request->max_rate) {
            $query->where('interest_rate', '<=', $request->max_rate);
        }

        // Filter by amount range
        if ($request->has('min_amount') && $request->min_amount) {
            $query->where('amount', '>=', $request->min_amount);
        }

        if ($request->has('max_amount') && $request->max_amount) {
            $query->where('amount', '<=', $request->max_amount);
        }

        $loans = $query->orderBy('created_at', 'desc')
            ->paginate(12);

        return Inertia::render('defi/property-loans/marketplace', [
            'loans' => $loans,
            'filters' => $request->only(['loan_type', 'min_rate', 'max_rate', 'min_amount', 'max_amount']),
        ]);
    }

    /**
     * Assess loan risk.
     */
    private function assessRisk($request): array
    {
        $riskScore = 0;
        $riskFactors = [];

        // Credit score risk
        $creditScore = $this->calculateCreditScore(auth()->user());
        if ($creditScore < 600) {
            $riskScore += 30;
            $riskFactors[] = 'درجة ائتمان منخفضة';
        } elseif ($creditScore < 700) {
            $riskScore += 15;
            $riskFactors[] = 'درجة ائتمان متوسطة';
        }

        // Loan amount risk
        if ($request->amount > 100000) {
            $riskScore += 20;
            $riskFactors[] = 'مبلغ القرض كبير';
        } elseif ($request->amount > 50000) {
            $riskScore += 10;
            $riskFactors[] = 'مبلغ القرض متوسط';
        }

        // Collateral risk
        $collateralRatio = $request->collateral_value / $request->amount;
        if ($collateralRatio < 1.2) {
            $riskScore += 25;
            $riskFactors[] = 'نسبة الضمانة منخفضة';
        } elseif ($collateralRatio < 1.5) {
            $riskScore += 10;
            $riskFactors[] = 'نسبة الضمانة متوسطة';
        }

        // Interest rate risk
        if ($request->interest_rate > 15) {
            $riskScore += 15;
            $riskFactors[] = 'سعر فائدة مرتفع';
        }

        // Loan term risk
        if ($request->loan_term > 360) {
            $riskScore += 10;
            $riskFactors[] = 'فترة القرض طويلة';
        }

        $riskLevel = 'low';
        if ($riskScore >= 60) {
            $riskLevel = 'high';
        } elseif ($riskScore >= 30) {
            $riskLevel = 'medium';
        }

        return [
            'score' => $riskScore,
            'level' => $riskLevel,
            'factors' => $riskFactors,
            'collateral_ratio' => $collateralRatio,
        ];
    }

    /**
     * Calculate user credit score.
     */
    private function calculateCreditScore($user): int
    {
        $baseScore = 700;

        // Adjust based on loan history
        $completedLoans = DefiPropertyLoan::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        $rejectedLoans = DefiPropertyLoan::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        $baseScore += ($completedLoans * 10) - ($rejectedLoans * 20);

        // Adjust based on repayment history
        $latePayments = DefiPropertyLoan::where('user_id', $user->id)
            ->whereHas('repayments', function ($query) {
                $query->where('status', 'late');
            })
            ->count();

        $baseScore -= ($latePayments * 5);

        // Ensure score is within valid range
        return max(300, min(850, $baseScore));
    }

    /**
     * Generate application hash.
     */
    private function generateApplicationHash(): string
    {
        return '0x' . bin2hex(random_bytes(32));
    }

    /**
     * Deploy loan smart contract.
     */
    private function deployLoanSmartContract($loan): string
    {
        // This would integrate with a smart contract deployment service
        // For now, return a mock address
        return '0x' . bin2hex(random_bytes(20));
    }

    /**
     * Create transaction record.
     */
    private function createTransaction($loan, $type, $amount = null): void
    {
        // This would create a blockchain transaction record
        // For now, just log the transaction
        \Log::info("Transaction created: {$type} for loan {$loan->id}");
    }

    /**
     * Process repayment.
     */
    private function processRepayment($loan, $amount): array
    {
        $remainingPrincipal = $loan->amount - $loan->repaid_amount;
        $remainingInterest = $loan->total_interest - $loan->paid_interest;
        $totalRemaining = $remainingPrincipal + $remainingInterest;

        if ($amount > $totalRemaining) {
            return ['success' => false, 'message' => 'المبلغ يتجاوز المبلغ المتبقي'];
        }

        // Calculate interest portion
        $interestPortion = min($amount, $remainingInterest);
        $principalPortion = $amount - $interestPortion;

        return [
            'success' => true,
            'interest_paid' => $interestPortion,
            'principal_paid' => $principalPortion,
        ];
    }

    /**
     * Calculate progress percentage.
     */
    private function calculateProgressPercentage($loan): float
    {
        $total = $loan->amount + $loan->total_interest;
        $paid = $loan->repaid_amount + $loan->paid_interest;
        
        return $total > 0 ? ($paid / $total) * 100 : 0;
    }

    /**
     * Calculate next payment due.
     */
    private function calculateNextPaymentDue($loan): ?string
    {
        if ($loan->status !== 'active') {
            return null;
        }

        return $loan->next_payment_date->format('Y-m-d');
    }

    /**
     * Calculate days overdue.
     */
    private function calculateDaysOverdue($loan): int
    {
        if ($loan->status !== 'active' || !$loan->next_payment_date) {
            return 0;
        }

        return max(0, now()->diffInDays($loan->next_payment_date));
    }

    /**
     * Get token value.
     */
    private function getTokenValue($tokenId): float
    {
        $token = PropertyToken::find($tokenId);
        return $token ? $token->current_value : 0;
    }

    /**
     * Calculate monthly borrowing.
     */
    private function calculateMonthlyBorrowing($loans): array
    {
        $monthlyData = [];
        
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $monthBorrowing = $loans->where('created_at', '>=', $date->startOfMonth())
                ->where('created_at', '<=', $date->endOfMonth())
                ->sum('amount');
            
            $monthlyData[$date->format('Y-m')] = $monthBorrowing;
        }

        return $monthlyData;
    }

    /**
     * Get credit score trend.
     */
    private function getCreditScoreTrend(): array
    {
        // This would calculate credit score over time
        // For now, return mock data
        return [
            'current' => $this->calculateCreditScore(auth()->user()),
            'last_month' => $this->calculateCreditScore(auth()->user()) - 5,
            'last_quarter' => $this->calculateCreditScore(auth()->user()) - 10,
        ];
    }
}
