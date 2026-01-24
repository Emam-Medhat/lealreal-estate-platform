<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitClaimRequest;
use App\Models\InsuranceClaim;
use App\Models\InsurancePolicy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class InsuranceClaimController extends Controller
{
    /**
     * Display a listing of insurance claims.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $policy = $request->get('policy');
        $provider = $request->get('provider');

        $claims = InsuranceClaim::with(['policy', 'policy.provider', 'documents'])
            ->when($search, function($query, $search) {
                return $query->where('claim_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($policy, function($query, $policy) {
                return $query->where('insurance_policy_id', $policy);
            })
            ->when($provider, function($query, $provider) {
                return $query->whereHas('policy', function($q) use ($provider) {
                    $q->where('insurance_provider_id', $provider);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('insurance.claims.index', compact('claims'));
    }

    /**
     * Show the form for creating a new insurance claim.
     */
    public function create()
    {
        $policies = InsurancePolicy::where('status', 'active')->get();
        
        return view('insurance.claims.create', compact('policies'));
    }

    /**
     * Store a newly created insurance claim.
     */
    public function store(SubmitClaimRequest $request)
    {
        $validated = $request->validated();
        
        $claim = InsuranceClaim::create([
            'claim_number' => $this->generateClaimNumber(),
            'insurance_policy_id' => $validated['insurance_policy_id'],
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'],
            'description_ar' => $validated['description_ar'] ?? null,
            'claim_type' => $validated['claim_type'],
            'incident_date' => $validated['incident_date'],
            'incident_location' => $validated['incident_location'] ?? null,
            'incident_description' => $validated['incident_description'] ?? null,
            'claimed_amount' => $validated['claimed_amount'],
            'estimated_damage' => $validated['estimated_damage'] ?? null,
            'damage_type' => $validated['damage_type'] ?? null,
            'cause_of_loss' => $validated['cause_of_loss'] ?? null,
            'witnesses' => $validated['witnesses'] ?? null,
            'police_report' => $validated['police_report'] ?? null,
            'police_report_number' => $validated['police_report_number'] ?? null,
            'emergency_services' => $validated['emergency_services'] ?? null,
            'medical_attention' => $validated['medical_attention'] ?? null,
            'injuries' => $validated['injuries'] ?? null,
            'property_damage' => $validated['property_damage'] ?? null,
            'other_losses' => $validated['other_losses'] ?? null,
            'immediate_actions' => $validated['immediate_actions'] ?? null,
            'preventive_measures' => $validated['preventive_measures'] ?? null,
            'status' => 'pending',
            'priority' => $validated['priority'] ?? 'medium',
            'assigned_to' => $validated['assigned_to'] ?? null,
            'estimated_settlement_date' => $validated['estimated_settlement_date'] ?? null,
            'created_by' => auth()->id(),
        ]);

        // Handle document uploads
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('claims/documents', 'public');
                $claim->documents()->create([
                    'file_name' => $document->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $document->getClientMimeType(),
                    'file_size' => $document->getSize(),
                    'uploaded_by' => auth()->id(),
                ]);
            }
        }

        return redirect()->route('insurance.claims.show', $claim)
            ->with('success', 'تم إنشاء المطالبة بنجاح');
    }

    /**
     * Display the specified insurance claim.
     */
    public function show(InsuranceClaim $claim)
    {
        $claim->load(['policy', 'policy.provider', 'documents', 'notes', 'timeline']);
        
        return view('insurance.claims.show', compact('claim'));
    }

    /**
     * Show the form for editing the specified insurance claim.
     */
    public function edit(InsuranceClaim $claim)
    {
        $claim->load(['policy']);
        $policies = InsurancePolicy::where('status', 'active')->get();
        
        return view('insurance.claims.edit', compact('claim', 'policies'));
    }

    /**
     * Update the specified insurance claim.
     */
    public function update(Request $request, InsuranceClaim $claim)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'claim_type' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_location' => 'nullable|string|max:500',
            'incident_description' => 'nullable|string',
            'claimed_amount' => 'required|numeric|min:0',
            'estimated_damage' => 'nullable|numeric|min:0',
            'damage_type' => 'nullable|string|max:255',
            'cause_of_loss' => 'nullable|string|max:500',
            'witnesses' => 'nullable|array',
            'police_report' => 'nullable|boolean',
            'police_report_number' => 'nullable|string|max:255',
            'emergency_services' => 'nullable|boolean',
            'medical_attention' => 'nullable|boolean',
            'injuries' => 'nullable|string',
            'property_damage' => 'nullable|string',
            'other_losses' => 'nullable|string',
            'immediate_actions' => 'nullable|string',
            'preventive_measures' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_settlement_date' => 'nullable|date',
        ]);

        $claim->update($validated);

        return redirect()->route('insurance.claims.show', $claim)
            ->with('success', 'تم تحديث المطالبة بنجاح');
    }

    /**
     * Remove the specified insurance claim.
     */
    public function destroy(InsuranceClaim $claim)
    {
        $claim->delete();

        return redirect()->route('insurance.claims.index')
            ->with('success', 'تم حذف المطالبة بنجاح');
    }

    /**
     * Submit claim for processing.
     */
    public function submit(InsuranceClaim $claim)
    {
        $claim->update([
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        return back()->with('success', 'تم تقديم المطالبة للمعالجة');
    }

    /**
     * Approve claim.
     */
    public function approve(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'approved_amount' => 'required|numeric|min:0',
            'approval_notes' => 'nullable|string|max:1000',
            'settlement_date' => 'nullable|date|after:today',
        ]);

        $claim->update([
            'status' => 'approved',
            'approved_amount' => $validated['approved_amount'],
            'approval_notes' => $validated['approval_notes'],
            'settlement_date' => $validated['settlement_date'],
            'approved_at' => now(),
            'approved_by' => auth()->id(),
        ]);

        return back()->with('success', 'تمت الموافقة على المطالبة بنجاح');
    }

    /**
     * Reject claim.
     */
    public function reject(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
            'rejection_details' => 'nullable|string|max:2000',
        ]);

        $claim->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'rejection_details' => $validated['rejection_details'],
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض المطالبة');
    }

    /**
     * Process claim.
     */
    public function process(InsuranceClaim $claim)
    {
        $claim->update([
            'status' => 'processing',
            'processing_started_at' => now(),
        ]);

        return back()->with('success', 'بدأت معالجة المطالبة');
    }

    /**
     * Settle claim.
     */
    public function settle(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'settlement_amount' => 'required|numeric|min:0',
            'settlement_method' => 'required|in:cash,bank_transfer,check,other',
            'settlement_reference' => 'nullable|string|max:255',
            'settlement_notes' => 'nullable|string|max:1000',
        ]);

        $claim->update([
            'status' => 'settled',
            'settlement_amount' => $validated['settlement_amount'],
            'settlement_method' => $validated['settlement_method'],
            'settlement_reference' => $validated['settlement_reference'],
            'settlement_notes' => $validated['settlement_notes'],
            'settled_at' => now(),
            'settled_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تسوية المطالبة بنجاح');
    }

    /**
     * Deny claim.
     */
    public function deny(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'denial_reason' => 'required|string|max:1000',
            'denial_details' => 'nullable|string|max:2000',
        ]);

        $claim->update([
            'status' => 'denied',
            'denial_reason' => $validated['denial_reason'],
            'denial_details' => $validated['denial_details'],
            'denied_at' => now(),
            'denied_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم رفض المطالبة');
    }

    /**
     * Reopen claim.
     */
    public function reopen(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'reopen_reason' => 'required|string|max:1000',
        ]);

        $claim->update([
            'status' => 'reopened',
            'reopen_reason' => $validated['reopen_reason'],
            'reopened_at' => now(),
            'reopened_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إعادة فتح المطالبة');
    }

    /**
     * Add document to claim.
     */
    public function addDocument(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'document_type' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $path = $validated['document']->store('claims/documents', 'public');
        
        $claim->documents()->create([
            'file_name' => $validated['document']->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $validated['document']->getClientMimeType(),
            'file_size' => $validated['document']->getSize(),
            'document_type' => $validated['document_type'],
            'description' => $validated['description'],
            'uploaded_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إضافة المستند بنجاح');
    }

    /**
     * Add note to claim.
     */
    public function addNote(InsuranceClaim $claim, Request $request)
    {
        $validated = $request->validate([
            'note' => 'required|string|max:2000',
            'note_type' => 'nullable|in:internal,external,system',
        ]);

        $claim->notes()->create([
            'note' => $validated['note'],
            'note_type' => $validated['note_type'] ?? 'internal',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    /**
     * Get claim timeline.
     */
    public function timeline(InsuranceClaim $claim)
    {
        $timeline = $claim->timeline()->with('user')->orderBy('created_at', 'desc')->get();
        
        return view('insurance.claims.timeline', compact('claim', 'timeline'));
    }

    /**
     * Generate claim report.
     */
    public function report(InsuranceClaim $claim)
    {
        // Generate PDF report
        
        return response()->download('claim_report_' . $claim->claim_number . '.pdf');
    }

    /**
     * Generate unique claim number.
     */
    private function generateClaimNumber(): string
    {
        $prefix = 'CLM';
        $year = date('Y');
        $sequence = InsuranceClaim::whereYear('created_at', $year)->count() + 1;
        
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
