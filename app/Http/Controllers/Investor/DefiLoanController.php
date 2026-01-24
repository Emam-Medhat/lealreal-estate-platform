<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Investor\InvestRequest;
use App\Models\Investor;
use App\Models\DefiLoan;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DefiLoanController extends Controller
{
    public function index(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $loans = $investor->defiLoans()
            ->with(['borrower'])
            ->when($request->search, function ($query, $search) {
                $query->where('loan_purpose', 'like', "%{$search}%")
                    ->orWhere('collateral_type', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->loan_type, function ($query, $type) {
                $query->where('loan_type', $type);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('investor.defi.loans.index', compact('loans'));
    }

    public function create()
    {
        return view('investor.defi.loans.create');
    }

    public function store(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'loan_amount' => 'required|numeric|min:100|max:1000000',
            'loan_purpose' => 'required|string|max:255',
            'loan_type' => 'required|in:personal,business,crypto,mortgage,education,medical',
            'borrower_address' => 'required|string|max:255',
            'collateral_type' => 'required|in:crypto,real_estate,vehicle,none',
            'collateral_value' => 'nullable|numeric|min:0',
            'collateral_address' => 'nullable|string|max:255',
            'interest_rate' => 'required|numeric|min:0|max:50',
            'loan_term_days' => 'required|integer|min:1|max:1095',
            'repayment_frequency' => 'required|in:daily,weekly,monthly,quarterly',
            'grace_period_days' => 'nullable|integer|min:0|max:365',
            'late_fee_rate' => 'nullable|numeric|min:0|max:20',
            'early_repayment_penalty' => 'nullable|numeric|min:0|max:50',
            'minimum_credit_score' => 'nullable|integer|min:300|max:850',
            'required_documents' => 'nullable|array',
            'required_documents.*' => 'string|max:255',
            'smart_contract_address' => 'nullable|string|max:255',
            'blockchain_network' => 'required|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
            'token_standard' => 'required|in:ERC20,ERC721,ERC1155',
            'notes' => 'nullable|string|max:1000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        $loan = DefiLoan::create([
            'investor_id' => $investor->id,
            'loan_amount' => $request->loan_amount,
            'loan_purpose' => $request->loan_purpose,
            'loan_type' => $request->loan_type,
            'borrower_address' => $request->borrower_address,
            'collateral_type' => $request->collateral_type,
            'collateral_value' => $request->collateral_value,
            'collateral_address' => $request->collateral_address,
            'interest_rate' => $request->interest_rate,
            'loan_term_days' => $request->loan_term_days,
            'repayment_frequency' => $request->repayment_frequency,
            'grace_period_days' => $request->grace_period_days ?? 0,
            'late_fee_rate' => $request->late_fee_rate ?? 0,
            'early_repayment_penalty' => $request->early_repayment_penalty ?? 0,
            'minimum_credit_score' => $request->minimum_credit_score,
            'required_documents' => $request->required_documents ?? [],
            'smart_contract_address' => $request->smart_contract_address,
            'blockchain_network' => $request->blockchain_network,
            'token_standard' => $request->token_standard,
            'status' => 'pending_approval',
            'total_repayments' => $this->calculateTotalRepayments($request),
            'monthly_payment' => $this->calculateMonthlyPayment($request),
            'total_interest' => $this->calculateTotalInterest($request),
            'collateral_ratio' => $this->calculateCollateralRatio($request),
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle documents upload
        if ($request->hasFile('documents')) {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('defi-loan-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $loan->update(['documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_defi_loan',
            'details' => "Created DeFi loan: {$loan->loan_purpose}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.defi.loans.show', $loan)
            ->with('success', 'DeFi loan created successfully.');
    }

    public function show(DefiLoan $loan)
    {
        $this->authorize('view', $loan);
        
        $loan->load(['investor', 'borrower', 'repayments']);
        
        return view('investor.defi.loans.show', compact('loan'));
    }

    public function updateStatus(Request $request, DefiLoan $loan): JsonResponse
    {
        $this->authorize('update', $loan);
        
        $request->validate([
            'status' => 'required|in:pending_approval,approved,funded,active,late,defaulted,completed,cancelled',
        ]);

        $loan->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_defi_loan_status',
            'details' => "Updated DeFi loan '{$loan->loan_purpose}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Loan status updated successfully'
        ]);
    }

    public function recordRepayment(Request $request, DefiLoan $loan): JsonResponse
    {
        $this->authorize('update', $loan);
        
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'transaction_hash' => 'required|string|max:255',
            'block_network' => 'required|string|max:100',
            'gas_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $repayment = $loan->repayments()->create([
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'transaction_hash' => $request->transaction_hash,
            'block_network' => $request->block_network,
            'gas_fee' => $request->gas_fee ?? 0,
            'net_amount' => $request->amount - ($request->gas_fee ?? 0),
            'notes' => $request->notes,
            'created_by' => Auth::id(),
        ]);

        // Update loan status if fully paid
        $totalRepaid = $loan->repayments()->sum('amount');
        if ($totalRepaid >= $loan->total_repayments) {
            $loan->update(['status' => 'completed']);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'recorded_defi_loan_repayment',
            'details' => "Recorded repayment of {$request->amount} for loan: {$loan->loan_purpose}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'repayment' => $repayment,
            'message' => 'Repayment recorded successfully'
        ]);
    }

    public function getLoanStats(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $stats = [
            'total_loans' => $investor->defiLoans()->count(),
            'active_loans' => $investor->defiLoans()->where('status', 'active')->count(),
            'completed_loans' => $investor->defiLoans()->where('status', 'completed')->count(),
            'defaulted_loans' => $investor->defiLoans()->where('status', 'defaulted')->count(),
            'total_loaned' => $investor->defiLoans()->sum('loan_amount'),
            'total_repaid' => $investor->defiLoans()->sum('total_repaid'),
            'total_interest_earned' => $investor->defiLoans()->sum('total_interest'),
            'average_interest_rate' => $investor->defiLoans()->avg('interest_rate'),
            'by_loan_type' => $investor->defiLoans()
                ->groupBy('loan_type')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('loan_amount'),
                        'average_rate' => $group->avg('interest_rate'),
                    ];
                }),
            'by_blockchain' => $investor->defiLoans()
                ->groupBy('blockchain_network')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('loan_amount'),
                    ];
                }),
            'by_status' => $investor->defiLoans()
                ->groupBy('status')
                ->map(function ($group) {
                    return [
                        'count' => $group->count(),
                        'total_amount' => $group->sum('loan_amount'),
                    ];
                }),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getLoanPerformance(DefiLoan $loan): JsonResponse
    {
        $this->authorize('view', $loan);
        
        $performance = [
            'loan_id' => $loan->id,
            'loan_purpose' => $loan->loan_purpose,
            'status' => $loan->status,
            'total_loaned' => $loan->loan_amount,
            'total_repaid' => $loan->total_repaid,
            'remaining_balance' => $loan->total_repayments - $loan->total_repaid,
            'interest_rate' => $loan->interest_rate,
            'total_interest_earned' => $loan->total_interest,
            'repayment_progress' => $loan->total_repayments > 0 ? ($loan->total_repaid / $loan->total_repayments) * 100 : 0,
            'days_active' => $loan->created_at->diffInDays(now()),
            'next_payment_due' => $this->calculateNextPaymentDue($loan),
            'collateral_value' => $loan->collateral_value,
            'collateral_ratio' => $loan->collateral_ratio,
        ];

        return response()->json([
            'success' => true,
            'performance' => $performance
        ]);
    }

    public function exportLoans(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending_approval,approved,funded,active,late,defaulted,completed,cancelled',
            'loan_type' => 'nullable|in:personal,business,crypto,mortgage,education,medical',
            'blockchain_network' => 'nullable|in:ethereum,polygon,bnb_chain,avalanche,arbitrum',
        ]);

        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $query = $investor->defiLoans()->with(['borrower']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->loan_type) {
            $query->where('loan_type', $request->loan_type);
        }

        if ($request->blockchain_network) {
            $query->where('blockchain_network', $request->blockchain_network);
        }

        $loans = $query->get();

        $filename = "defi_loans_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $loans,
            'filename' => $filename,
            'message' => 'DeFi loans exported successfully'
        ]);
    }

    private function calculateTotalRepayments(Request $request): float
    {
        $principal = $request->loan_amount;
        $interestRate = $request->interest_rate / 100;
        $termDays = $request->loan_term_days;
        
        $totalInterest = $principal * $interestRate * ($termDays / 365);
        return $principal + $totalInterest;
    }

    private function calculateMonthlyPayment(Request $request): float
    {
        $totalRepayment = $this->calculateTotalRepayments($request);
        $termDays = $request->loan_term_days;
        
        return $totalRepayment / ($termDays / 30); // Approximate monthly
    }

    private function calculateTotalInterest(Request $request): float
    {
        $principal = $request->loan_amount;
        $interestRate = $request->interest_rate / 100;
        $termDays = $request->loan_term_days;
        
        return $principal * $interestRate * ($termDays / 365);
    }

    private function calculateCollateralRatio(Request $request): float
    {
        if (!$request->collateral_value || $request->collateral_value == 0) {
            return 0;
        }
        
        return ($request->loan_amount / $request->collateral_value) * 100;
    }

    private function calculateNextPaymentDue(DefiLoan $loan): ?string
    {
        if ($loan->status !== 'active') {
            return null;
        }
        
        $lastRepayment = $loan->repayments()->latest('payment_date')->first();
        $frequency = $loan->repayment_frequency;
        
        $nextDue = $lastRepayment ? 
            $lastRepayment->payment_date->addDays($this->getFrequencyDays($frequency)) :
            $loan->created_at->addDays($this->getFrequencyDays($frequency));
        
        return $nextDue->format('Y-m-d');
    }

    private function getFrequencyDays(string $frequency): int
    {
        $frequencies = [
            'daily' => 1,
            'weekly' => 7,
            'monthly' => 30,
            'quarterly' => 90,
        ];
        
        return $frequencies[$frequency] ?? 30;
    }
}
