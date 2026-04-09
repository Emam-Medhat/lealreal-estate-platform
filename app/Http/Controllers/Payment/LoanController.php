<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $loans = Loan::with(['borrower', 'lender', 'payments'])
            ->when($request->search, function ($query, $search) {
                $query->where('loan_number', 'like', "%{$search}%")
                    ->orWhereHas('borrower', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.loans.index', compact('loans'));
    }

    public function create()
    {
        return view('payments.loans.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'borrower_id' => 'required|exists:users,id',
            'lender_id' => 'nullable|exists:users,id',
            'type' => 'required|in:personal,business,mortgage,auto,student,other',
            'purpose' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'interest_type' => 'required|in:fixed,variable',
            'loan_term_months' => 'required|integer|min:1|max:600',
            'payment_frequency' => 'required|in:weekly,bi_weekly,monthly,quarterly',
            'collateral_type' => 'nullable|string|max:100',
            'collateral_value' => 'nullable|numeric|min:0',
            'collateral_description' => 'nullable|string|max:1000',
            'guarantor' => 'nullable|array',
            'guarantor.name' => 'required_with:guarantor|string|max:255',
            'guarantor.email' => 'required_with:guarantor|email|max:255',
            'guarantor.phone' => 'required_with:guarantor|string|max:20',
            'fees' => 'nullable|array',
            'fees.origination_fee' => 'nullable|numeric|min:0',
            'fees.late_fee' => 'nullable|numeric|min:0',
            'fees.prepayment_penalty' => 'nullable|numeric|min:0',
            'terms' => 'required|string|max:2000',
            'disbursement_method' => 'required|string|max:100',
            'disbursement_schedule' => 'nullable|array',
            'documents' => 'nullable|array',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $loan = Loan::create([
                'borrower_id' => $request->borrower_id,
                'lender_id' => $request->lender_id ?? Auth::id(),
                'loan_number' => $this->generateLoanNumber(),
                'type' => $request->type,
                'purpose' => $request->purpose,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'interest_rate' => $request->interest_rate,
                'interest_type' => $request->interest_type,
                'loan_term_months' => $request->loan_term_months,
                'payment_frequency' => $request->payment_frequency,
                'collateral_type' => $request->collateral_type,
                'collateral_value' => $request->collateral_value,
                'collateral_description' => $request->collateral_description,
                'guarantor' => $request->guarantor,
                'fees' => $request->fees ?? [],
                'terms' => $request->terms,
                'disbursement_method' => $request->disbursement_method,
                'disbursement_schedule' => $request->disbursement_schedule ?? [],
                'documents' => $request->documents ?? [],
                'notes' => $request->notes,
                'status' => 'pending_approval',
                'created_by' => Auth::id(),
            ]);

            // Calculate loan details
            $this->calculateLoanDetails($loan);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_loan',
                'details' => "Created loan: {$loan->loan_number} for {$request->amount} {$request->currency}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('payments.loans.show', $loan)
                ->with('success', 'Loan created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating loan: ' . $e->getMessage());
        }
    }

    public function show(Loan $loan)
    {
        $loan->load(['borrower', 'lender', 'payments', 'documents']);
        return view('payments.loans.show', compact('loan'));
    }

    public function updateStatus(Request $request, Loan $loan): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:pending_approval,approved,disbursed,active,paid_off,defaulted,restructured',
            'notes' => 'nullable|string|max:500',
            'approved_amount' => 'nullable|numeric|min:0',
            'approved_rate' => 'nullable|numeric|min:0|max:100',
            'approved_terms' => 'nullable|integer|min:1|max:600',
        ]);

        try {
            $updateData = [
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ];

            if ($request->status === 'approved') {
                $updateData['approved_amount'] = $request->approved_amount ?? $loan->amount;
                $updateData['approved_rate'] = $request->approved_rate ?? $loan->interest_rate;
                $updateData['approved_terms'] = $request->approved_terms ?? $loan->loan_term_months;
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = Auth::id();
                
                // Recalculate with approved terms
                $loan->amount = $updateData['approved_amount'];
                $loan->interest_rate = $updateData['approved_rate'];
                $loan->loan_term_months = $updateData['approved_terms'];
                $this->calculateLoanDetails($loan);
            }

            if ($request->status === 'disbursed') {
                $updateData['disbursed_at'] = now();
                $updateData['disbursed_by'] = Auth::id();
            }

            if ($request->status === 'active') {
                $updateData['started_at'] = now();
            }

            if ($request->status === 'paid_off') {
                $updateData['paid_off_at'] = now();
            }

            $loan->update($updateData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_loan_status',
                'details' => "Updated loan {$loan->loan_number} status to {$request->status}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $request->status,
                'message' => 'Loan status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function disburse(Request $request, Loan $loan): JsonResponse
    {
        $request->validate([
            'disbursement_amount' => 'required|numeric|min:0.01|max:' . $loan->approved_amount,
            'disbursement_method' => 'required|string|max:100',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($loan->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved loans can be disbursed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create disbursement record
            $disbursement = $loan->disbursements()->create([
                'amount' => $request->disbursement_amount,
                'method' => $request->disbursement_method,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'disbursed_by' => Auth::id(),
                'disbursed_at' => now(),
            ]);

            // Update loan status and disbursed amount
            $totalDisbursed = $loan->disbursements()->sum('amount');
            $loan->update([
                'disbursed_amount' => $totalDisbursed,
                'status' => $totalDisbursed >= $loan->approved_amount ? 'disbursed' : 'partially_disbursed',
                'disbursed_at' => $totalDisbursed >= $loan->approved_amount ? now() : null,
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'disbursed_loan',
                'details' => "Disbursed {$request->disbursement_amount} for loan {$loan->loan_number}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'disbursement' => $disbursement,
                'message' => 'Loan disbursed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error disbursing loan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateSchedule(Request $request, Loan $loan): JsonResponse
    {
        try {
            $schedule = $this->generatePaymentSchedule($loan);
            
            return response()->json([
                'success' => true,
                'schedule' => $schedule,
                'message' => 'Payment schedule calculated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLoanStats(): JsonResponse
    {
        $stats = [
            'total_loans' => Loan::count(),
            'pending_loans' => Loan::where('status', 'pending_approval')->count(),
            'approved_loans' => Loan::where('status', 'approved')->count(),
            'disbursed_loans' => Loan::where('status', 'disbursed')->count(),
            'active_loans' => Loan::where('status', 'active')->count(),
            'paid_off_loans' => Loan::where('status', 'paid_off')->count(),
            'defaulted_loans' => Loan::where('status', 'defaulted')->count(),
            'total_loan_amount' => Loan::sum('amount'),
            'total_disbursed' => Loan::sum('disbursed_amount'),
            'total_outstanding' => Loan::sum('outstanding_balance'),
            'by_type' => Loan::groupBy('type')
                ->selectRaw('type, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'by_status' => Loan::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(amount) as total')
                ->get(),
            'monthly_stats' => Loan::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(amount) as total')
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportLoans(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending_approval,approved,disbursed,active,paid_off,defaulted,restructured',
            'type' => 'nullable|in:personal,business,mortgage,auto,student,other',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = Loan::with(['borrower', 'lender', 'payments']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $loans = $query->get();

        $filename = "loans_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $loans,
            'filename' => $filename,
            'message' => 'Loans exported successfully'
        ]);
    }

    private function generateLoanNumber()
    {
        $prefix = 'LOAN';
        $year = date('Y');
        $sequence = Loan::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }

    private function calculateLoanDetails($loan)
    {
        $principal = $loan->amount;
        $annualRate = $loan->interest_rate;
        $monthlyRate = $annualRate / 100 / 12;
        $numPayments = $loan->loan_term_months;
        
        // Calculate monthly payment
        if ($monthlyRate == 0) {
            $monthlyPayment = $principal / $numPayments;
        } else {
            $monthlyPayment = $principal * ($monthlyRate * pow(1 + $monthlyRate, $numPayments)) / 
                           (pow(1 + $monthlyRate, $numPayments) - 1);
        }
        
        // Calculate total payment and interest
        $totalPayment = $monthlyPayment * $numPayments;
        $totalInterest = $totalPayment - $principal;
        
        // Calculate fees
        $originationFee = ($loan->fees['origination_fee'] ?? 0) * $principal / 100;
        $totalFees = $originationFee + ($loan->fees['late_fee'] ?? 0) + ($loan->fees['prepayment_penalty'] ?? 0);
        
        $loan->update([
            'monthly_payment' => $monthlyPayment,
            'total_payment' => $totalPayment,
            'total_interest' => $totalInterest,
            'total_fees' => $totalFees,
            'outstanding_balance' => $principal,
        ]);
    }

    private function generatePaymentSchedule($loan)
    {
        $schedule = [];
        $balance = $loan->amount;
        $monthlyRate = $loan->interest_rate / 100 / 12;
        $payment = $loan->monthly_payment;
        $numPayments = $loan->loan_term_months;
        
        for ($i = 1; $i <= $numPayments; $i++) {
            if ($balance <= 0) break;
            
            $interest = $balance * $monthlyRate;
            $principal = min($payment - $interest, $balance);
            $balance -= $principal;
            
            $schedule[] = [
                'payment_number' => $i,
                'payment_date' => now()->addMonths($i)->format('Y-m-d'),
                'payment_amount' => round($payment, 2),
                'principal' => round($principal, 2),
                'interest' => round($interest, 2),
                'balance' => round($balance, 2),
                'cumulative_interest' => round($payment * $i - ($loan->amount - $balance), 2),
            ];
        }
        
        return $schedule;
    }
}
