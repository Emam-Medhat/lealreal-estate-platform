<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\ApplyMortgageRequest;
use App\Models\MortgageApplication;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MortgageController extends Controller
{
    public function index(Request $request)
    {
        $applications = MortgageApplication::with(['user', 'property', 'processor'])
            ->when($request->search, function ($query, $search) {
                $query->where('application_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->loan_type, function ($query, $type) {
                $query->where('loan_type', $type);
            })
            ->latest('created_at')
            ->paginate(20);

        return view('payments.mortgage.index', compact('applications'));
    }

    public function create()
    {
        return view('payments.mortgage.create');
    }

    public function store(ApplyMortgageRequest $request)
    {
        try {
            DB::beginTransaction();

            $application = MortgageApplication::create([
                'user_id' => Auth::id(),
                'application_number' => $this->generateApplicationNumber(),
                'property_id' => $request->property_id,
                'loan_type' => $request->loan_type,
                'loan_amount' => $request->loan_amount,
                'down_payment' => $request->down_payment,
                'down_payment_percentage' => $request->down_payment_percentage,
                'loan_term_years' => $request->loan_term_years,
                'interest_rate' => $request->interest_rate,
                'fixed_rate_period' => $request->fixed_rate_period,
                'amortization_type' => $request->amortization_type,
                'property_value' => $request->property_value,
                'property_address' => $request->property_address,
                'property_type' => $request->property_type,
                'property_use' => $request->property_use,
                'borrower_income' => $request->borrower_income,
                'borrower_employment' => $request->borrower_employment,
                'borrower_credit_score' => $request->borrower_credit_score,
                'borrower_debts' => $request->borrower_debts,
                'borrower_assets' => $request->borrower_assets,
                'co_borrower' => $request->co_borrower,
                'co_borrower_income' => $request->co_borrower_income,
                'co_borrower_credit_score' => $request->co_borrower_credit_score,
                'documents' => $request->documents ?? [],
                'status' => 'submitted',
                'submitted_at' => now(),
                'created_by' => Auth::id(),
            ]);

            // Calculate monthly payment
            $monthlyPayment = $this->calculateMonthlyPayment($application);
            $totalInterest = $this->calculateTotalInterest($application);

            $application->update([
                'monthly_payment' => $monthlyPayment,
                'total_interest' => $totalInterest,
                'total_payment' => $application->loan_amount + $totalInterest,
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'submitted_mortgage_application',
                'details' => "Submitted mortgage application: {$application->application_number} for {$request->loan_amount}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('payments.mortgage.show', $application)
                ->with('success', 'Mortgage application submitted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error submitting application: ' . $e->getMessage());
        }
    }

    public function show(MortgageApplication $application)
    {
        $application->load(['user', 'property', 'processor', 'documents']);
        return view('payments.mortgage.show', compact('application'));
    }

    public function updateStatus(Request $request, MortgageApplication $application): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:submitted,under_review,approved,rejected,conditionally_approved,closed,funded',
            'notes' => 'nullable|string|max:1000',
            'approved_amount' => 'nullable|numeric|min:0',
            'approved_rate' => 'nullable|numeric|min:0|max:100',
            'approved_terms' => 'nullable|integer|min:1|max:40',
        ]);

        try {
            $updateData = [
                'status' => $request->status,
                'notes' => $request->notes,
                'updated_by' => Auth::id(),
            ];

            if ($request->status === 'approved') {
                $updateData['approved_amount'] = $request->approved_amount;
                $updateData['approved_rate'] = $request->approved_rate;
                $updateData['approved_terms'] = $request->approved_terms;
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = Auth::id();
            }

            if ($request->status === 'rejected') {
                $updateData['rejected_at'] = now();
                $updateData['rejected_by'] = Auth::id();
            }

            if ($request->status === 'funded') {
                $updateData['funded_at'] = now();
                $updateData['funded_by'] = Auth::id();
            }

            $application->update($updateData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_mortgage_status',
                'details' => "Updated mortgage {$application->application_number} status to {$request->status}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'status' => $request->status,
                'message' => 'Mortgage application status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating application: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadDocuments(Request $request, MortgageApplication $application): JsonResponse
    {
        $request->validate([
            'documents' => 'required|array|min:1',
            'documents.*' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'document_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            $documents = [];
            foreach ($request->file('documents') as $document) {
                $path = $document->store('mortgage-documents', 'public');
                $documents[] = [
                    'type' => $request->document_type,
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize(),
                    'description' => $request->description,
                    'uploaded_at' => now()->toISOString(),
                ];
            }

            $currentDocuments = $application->documents ?? [];
            $application->update([
                'documents' => array_merge($currentDocuments, $documents),
                'updated_by' => Auth::id(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'uploaded_mortgage_documents',
                'details' => "Uploaded documents for mortgage {$application->application_number}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'documents' => $documents,
                'message' => 'Documents uploaded successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading documents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function calculateAffordability(Request $request): JsonResponse
    {
        $request->validate([
            'annual_income' => 'required|numeric|min:0',
            'monthly_debts' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0|max:100',
            'loan_term' => 'required|integer|min:1|max:40',
            'property_tax' => 'nullable|numeric|min:0',
            'insurance' => 'nullable|numeric|min:0',
            'hoa_fees' => 'nullable|numeric|min:0',
        ]);

        try {
            $monthlyIncome = $request->annual_income / 12;
            $totalMonthlyDebts = $request->monthly_debts;
            
            // Calculate maximum monthly payment (28% DTI rule)
            $maxMonthlyPayment = $monthlyIncome * 0.28 - $totalMonthlyDebts;
            
            // Calculate loan amount
            $monthlyRate = $request->interest_rate / 100 / 12;
            $numPayments = $request->loan_term * 12;
            
            if ($monthlyRate > 0) {
                $maxLoanAmount = $maxMonthlyPayment * (1 - pow(1 + $monthlyRate, -$numPayments)) / $monthlyRate;
            } else {
                $maxLoanAmount = $maxMonthlyPayment * $numPayments;
            }
            
            // Add down payment to get max property price
            $maxPropertyPrice = $maxLoanAmount + $request->down_payment;
            
            // Calculate total monthly payment including taxes, insurance, HOA
            $totalMonthlyPayment = $maxMonthlyPayment + ($request->property_tax ?? 0) + 
                                ($request->insurance ?? 0) + ($request->hoa_fees ?? 0);
            
            $affordability = [
                'max_monthly_payment' => round($maxMonthlyPayment, 2),
                'max_loan_amount' => round($maxLoanAmount, 2),
                'max_property_price' => round($maxPropertyPrice, 2),
                'total_monthly_payment' => round($totalMonthlyPayment, 2),
                'dti_ratio' => round(($totalMonthlyPayment / $monthlyIncome) * 100, 2),
                'is_affordable' => $totalMonthlyPayment <= ($monthlyIncome * 0.43), // 43% total DTI
            ];

            return response()->json([
                'success' => true,
                'affordability' => $affordability
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating affordability: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getApplicationStats(): JsonResponse
    {
        $stats = [
            'total_applications' => MortgageApplication::count(),
            'submitted_applications' => MortgageApplication::where('status', 'submitted')->count(),
            'under_review_applications' => MortgageApplication::where('status', 'under_review')->count(),
            'approved_applications' => MortgageApplication::where('status', 'approved')->count(),
            'rejected_applications' => MortgageApplication::where('status', 'rejected')->count(),
            'funded_applications' => MortgageApplication::where('status', 'funded')->count(),
            'total_loan_amount' => MortgageApplication::sum('loan_amount'),
            'approved_loan_amount' => MortgageApplication::where('status', 'approved')->sum('approved_amount'),
            'average_loan_amount' => MortgageApplication::avg('loan_amount'),
            'by_loan_type' => MortgageApplication::groupBy('loan_type')
                ->selectRaw('loan_type, COUNT(*) as count, AVG(loan_amount) as avg_amount')
                ->get(),
            'by_status' => MortgageApplication::groupBy('status')
                ->selectRaw('status, COUNT(*) as count, SUM(loan_amount) as total_amount')
                ->get(),
            'monthly_stats' => MortgageApplication::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as count, SUM(loan_amount) as total')
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

    public function exportApplications(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:submitted,under_review,approved,rejected,conditionally_approved,closed,funded',
            'loan_type' => 'nullable|in:fixed,variable,adjustable,fha,va,conventional',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $query = MortgageApplication::with(['user', 'property']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->loan_type) {
            $query->where('loan_type', $request->loan_type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $applications = $query->get();

        $filename = "mortgage_applications_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $applications,
            'filename' => $filename,
            'message' => 'Mortgage applications exported successfully'
        ]);
    }

    private function generateApplicationNumber()
    {
        $prefix = 'MORT';
        $year = date('Y');
        $sequence = MortgageApplication::whereYear('created_at', $year)->count() + 1;
        
        return sprintf('%s-%s-%06d', $prefix, $year, $sequence);
    }

    private function calculateMonthlyPayment($application)
    {
        $principal = $application->loan_amount - $application->down_payment;
        $monthlyRate = $application->interest_rate / 100 / 12;
        $numPayments = $application->loan_term_years * 12;
        
        if ($monthlyRate == 0) {
            return $principal / $numPayments;
        }
        
        return $principal * ($monthlyRate * pow(1 + $monthlyRate, $numPayments)) / 
               (pow(1 + $monthlyRate, $numPayments) - 1);
    }

    private function calculateTotalInterest($application)
    {
        $monthlyPayment = $this->calculateMonthlyPayment($application);
        $numPayments = $application->loan_term_years * 12;
        $principal = $application->loan_amount - $application->down_payment;
        
        return ($monthlyPayment * $numPayments) - $principal;
    }
}
