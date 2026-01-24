<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\StoreFinancingRequest;
use App\Http\Requests\Developer\UpdateFinancingRequest;
use App\Models\Developer;
use App\Models\DeveloperProject;
use App\Models\DeveloperFinancing;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperFinancingController extends Controller
{
    public function index(Request $request)
    {
        $developer = Auth::user()->developer;
        
        $financings = $developer->financings()
            ->with(['project'])
            ->when($request->search, function ($query, $search) {
                $query->where('loan_number', 'like', "%{$search}%")
                    ->orWhere('lender_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->project_id, function ($query, $projectId) {
                $query->where('project_id', $projectId);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        $projects = $developer->projects()->pluck('name', 'id');

        return view('developer.financings.index', compact('financings', 'projects'));
    }

    public function create()
    {
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.financings.create', compact('developer', 'projects'));
    }

    public function store(StoreFinancingRequest $request)
    {
        $developer = Auth::user()->developer;
        
        $financing = DeveloperFinancing::create([
            'developer_id' => $developer->id,
            'project_id' => $request->project_id,
            'loan_number' => $request->loan_number,
            'lender_name' => $request->lender_name,
            'lender_type' => $request->lender_type,
            'description' => $request->description,
            'financing_type' => $request->financing_type,
            'loan_amount' => $request->loan_amount,
            'interest_rate' => $request->interest_rate,
            'loan_term_years' => $request->loan_term_years,
            'application_date' => $request->application_date,
            'approval_date' => $request->approval_date,
            'disbursement_date' => $request->disbursement_date,
            'first_payment_date' => $request->first_payment_date,
            'maturity_date' => $request->maturity_date,
            'status' => $request->status ?? 'pending',
            'collateral_details' => $request->collateral_details ?? [],
            'guarantees' => $request->guarantees ?? [],
            'payment_schedule' => $request->payment_schedule ?? [],
            'fees_and_charges' => $request->fees_and_charges ?? [],
            'conditions' => $request->conditions ?? [],
            'covenants' => $request->covenants ?? [],
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        // Handle loan agreement document
        if ($request->hasFile('loan_agreement')) {
            $agreementPath = $request->file('loan_agreement')->store('loan-agreements', 'public');
            $financing->update(['loan_agreement' => $agreementPath]);
        }

        // Handle supporting documents
        if ($request->hasFile('supporting_documents')) {
            $documents = [];
            foreach ($request->file('supporting_documents') as $document) {
                $path = $document->store('financing-documents', 'public');
                $documents[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $financing->update(['supporting_documents' => $documents]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'created_developer_financing',
            'details' => "Created financing: {$financing->loan_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.financings.show', $financing)
            ->with('success', 'Financing created successfully.');
    }

    public function show(DeveloperFinancing $financing)
    {
        $this->authorize('view', $financing);
        
        $financing->load(['project', 'creator', 'updater']);
        
        return view('developer.financings.show', compact('financing'));
    }

    public function edit(DeveloperFinancing $financing)
    {
        $this->authorize('update', $financing);
        
        $developer = Auth::user()->developer;
        $projects = $developer->projects()->pluck('name', 'id');
        
        return view('developer.financings.edit', compact('financing', 'projects'));
    }

    public function update(UpdateFinancingRequest $request, DeveloperFinancing $financing)
    {
        $this->authorize('update', $financing);
        
        $financing->update([
            'project_id' => $request->project_id,
            'loan_number' => $request->loan_number,
            'lender_name' => $request->lender_name,
            'lender_type' => $request->lender_type,
            'description' => $request->description,
            'financing_type' => $request->financing_type,
            'loan_amount' => $request->loan_amount,
            'interest_rate' => $request->interest_rate,
            'loan_term_years' => $request->loan_term_years,
            'application_date' => $request->application_date,
            'approval_date' => $request->approval_date,
            'disbursement_date' => $request->disbursement_date,
            'first_payment_date' => $request->first_payment_date,
            'maturity_date' => $request->maturity_date,
            'status' => $request->status,
            'collateral_details' => $request->collateral_details ?? [],
            'guarantees' => $request->guarantees ?? [],
            'payment_schedule' => $request->payment_schedule ?? [],
            'fees_and_charges' => $request->fees_and_charges ?? [],
            'conditions' => $request->conditions ?? [],
            'covenants' => $request->covenants ?? [],
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'notes' => $request->notes,
            'updated_by' => Auth::id(),
        ]);

        // Handle loan agreement update
        if ($request->hasFile('loan_agreement')) {
            if ($financing->loan_agreement) {
                Storage::disk('public')->delete($financing->loan_agreement);
            }
            $agreementPath = $request->file('loan_agreement')->store('loan-agreements', 'public');
            $financing->update(['loan_agreement' => $agreementPath]);
        }

        // Handle new supporting documents
        if ($request->hasFile('supporting_documents')) {
            $existingDocuments = $financing->supporting_documents ?? [];
            foreach ($request->file('supporting_documents') as $document) {
                $path = $document->store('financing-documents', 'public');
                $existingDocuments[] = [
                    'path' => $path,
                    'name' => $document->getClientOriginalName(),
                    'type' => $document->getClientOriginalExtension(),
                    'uploaded_at' => now()->toISOString(),
                ];
            }
            $financing->update(['supporting_documents' => $existingDocuments]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_financing',
            'details' => "Updated financing: {$financing->loan_number}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.financings.show', $financing)
            ->with('success', 'Financing updated successfully.');
    }

    public function destroy(DeveloperFinancing $financing)
    {
        $this->authorize('delete', $financing);
        
        $loanNumber = $financing->loan_number;
        
        // Delete loan agreement
        if ($financing->loan_agreement) {
            Storage::disk('public')->delete($financing->loan_agreement);
        }
        
        // Delete supporting documents
        if ($financing->supporting_documents) {
            foreach ($financing->supporting_documents as $document) {
                Storage::disk('public')->delete($document['path']);
            }
        }
        
        $financing->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_developer_financing',
            'details' => "Deleted financing: {$loanNumber}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('developer.financings.index')
            ->with('success', 'Financing deleted successfully.');
    }

    public function updateStatus(Request $request, DeveloperFinancing $financing): JsonResponse
    {
        $this->authorize('update', $financing);
        
        $request->validate([
            'status' => 'required|in:pending,approved,disbursed,active,paid_off,defaulted,cancelled',
        ]);

        $financing->update(['status' => $request->status]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_financing_status',
            'details' => "Updated financing '{$financing->loan_number}' status to {$request->status}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $request->status,
            'message' => 'Financing status updated successfully'
        ]);
    }

    public function calculatePaymentSchedule(Request $request, DeveloperFinancing $financing): JsonResponse
    {
        $this->authorize('view', $financing);
        
        $request->validate([
            'payment_type' => 'required|in:monthly,quarterly,annually',
            'start_date' => 'required|date',
        ]);

        $loanAmount = $financing->loan_amount;
        $interestRate = $financing->interest_rate / 100;
        $loanTermYears = $financing->loan_term_years;
        $paymentType = $request->payment_type;
        $startDate = $request->start_date;

        $paymentsPerYear = match($paymentType) {
            'monthly' => 12,
            'quarterly' => 4,
            'annually' => 1,
        };

        $totalPayments = $loanTermYears * $paymentsPerYear;
        $periodicRate = $interestRate / $paymentsPerYear;
        
        // Calculate payment using amortization formula
        $paymentAmount = $loanAmount * ($periodicRate * pow(1 + $periodicRate, $totalPayments)) / 
                        (pow(1 + $periodicRate, $totalPayments) - 1);

        $schedule = [];
        $currentDate = new \DateTime($startDate);
        $remainingBalance = $loanAmount;

        for ($i = 1; $i <= $totalPayments; $i++) {
            $interestPayment = $remainingBalance * $periodicRate;
            $principalPayment = $paymentAmount - $interestPayment;
            $remainingBalance -= $principalPayment;

            $schedule[] = [
                'payment_number' => $i,
                'payment_date' => $currentDate->format('Y-m-d'),
                'payment_amount' => round($paymentAmount, 2),
                'principal_payment' => round($principalPayment, 2),
                'interest_payment' => round($interestPayment, 2),
                'remaining_balance' => round(max(0, $remainingBalance), 2),
            ];

            // Move to next payment date
            $interval = match($paymentType) {
                'monthly' => 'P1M',
                'quarterly' => 'P3M',
                'annually' => 'P1Y',
            };
            $currentDate->add(new \DateInterval($interval));
        }

        return response()->json([
            'success' => true,
            'schedule' => $schedule,
            'summary' => [
                'total_payments' => $totalPayments,
                'payment_amount' => round($paymentAmount, 2),
                'total_interest' => round($paymentAmount * $totalPayments - $loanAmount, 2),
                'total_payment' => round($paymentAmount * $totalPayments, 2),
            ],
            'message' => 'Payment schedule calculated successfully'
        ]);
    }

    public function getProjectFinancings(DeveloperProject $project): JsonResponse
    {
        $this->authorize('view', $project);
        
        $financings = $project->financings()
            ->latest()
            ->get(['id', 'loan_number', 'lender_name', 'loan_amount', 'status', 'approval_date']);

        return response()->json([
            'success' => true,
            'financings' => $financings
        ]);
    }

    public function getFinancingStats(): JsonResponse
    {
        $developer = Auth::user()->developer;
        
        $stats = [
            'total_financings' => $developer->financings()->count(),
            'pending_financings' => $developer->financings()->where('status', 'pending')->count(),
            'approved_financings' => $developer->financings()->where('status', 'approved')->count(),
            'disbursed_financings' => $developer->financings()->where('status', 'disbursed')->count(),
            'active_financings' => $developer->financings()->where('status', 'active')->count(),
            'paid_off_financings' => $developer->financings()->where('status', 'paid_off')->count(),
            'defaulted_financings' => $developer->financings()->where('status', 'defaulted')->count(),
            'by_type' => $developer->financings()
                ->groupBy('financing_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'by_lender_type' => $developer->financings()
                ->groupBy('lender_type')
                ->map(function ($group) {
                    return $group->count();
                }),
            'total_loan_amount' => $developer->financings()->sum('loan_amount'),
            'average_interest_rate' => $developer->financings()->avg('interest_rate'),
            'average_loan_term' => $developer->financings()->avg('loan_term_years'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function exportFinancings(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|in:json,csv,xlsx',
            'status' => 'nullable|in:pending,approved,disbursed,active,paid_off,defaulted,cancelled',
            'project_id' => 'nullable|exists:developer_projects,id',
        ]);

        $developer = Auth::user()->developer;
        
        $query = $developer->financings()->with(['project']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }

        $financings = $query->get();

        $filename = "developer_financings_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $financings,
            'filename' => $filename,
            'message' => 'Financings exported successfully'
        ]);
    }
}
