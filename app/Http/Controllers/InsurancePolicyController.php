<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePolicyRequest;
use App\Models\InsurancePolicy;
use App\Models\InsuranceProvider;
use App\Models\InsuranceCoverage;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use PDF;

class InsurancePolicyController extends Controller
{
    /**
     * Display a listing of insurance policies.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $provider = $request->get('provider');
        $type = $request->get('type');
        $property = $request->get('property');

        $policies = InsurancePolicy::with(['provider', 'property', 'coverages'])
            ->when($search, function($query, $search) {
                return $query->where('policy_number', 'like', "%{$search}%")
                    ->orWhere('title', 'like', "%{$search}%");
            })
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($provider, function($query, $provider) {
                return $query->where('insurance_provider_id', $provider);
            })
            ->when($type, function($query, $type) {
                return $query->where('policy_type', $type);
            })
            ->when($property, function($query, $property) {
                return $query->where('property_id', $property);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $providers = InsuranceProvider::where('active', true)->pluck('name', 'id');
        $properties = []; // Get from properties table

        return view('insurance.policies.index', compact('policies', 'providers', 'properties'));
    }

    /**
     * Show the form for creating a new insurance policy.
     */
    public function create()
    {
        $providers = InsuranceProvider::where('active', true)->get();
        $properties = []; // Get from properties table
        $coverageTypes = []; // Get coverage types

        return view('insurance.policies.create', compact('providers', 'properties', 'coverageTypes'));
    }

    /**
     * Store a newly created insurance policy.
     */
    public function store(CreatePolicyRequest $request)
    {
        $validated = $request->validated();
        
        $policy = InsurancePolicy::create([
            'policy_number' => $this->generatePolicyNumber(),
            'title' => $validated['title'],
            'title_ar' => $validated['title_ar'] ?? null,
            'description' => $validated['description'] ?? null,
            'description_ar' => $validated['description_ar'] ?? null,
            'insurance_provider_id' => $validated['insurance_provider_id'],
            'property_id' => $validated['property_id'],
            'policy_type' => $validated['policy_type'],
            'status' => 'draft',
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'premium_amount' => $validated['premium_amount'],
            'coverage_amount' => $validated['coverage_amount'],
            'deductible' => $validated['deductible'] ?? 0,
            'payment_frequency' => $validated['payment_frequency'] ?? 'monthly',
            'payment_method' => $validated['payment_method'] ?? 'bank_transfer',
            'created_by' => auth()->id(),
        ]);

        // Add coverages
        if (isset($validated['coverages'])) {
            foreach ($validated['coverages'] as $coverage) {
                InsuranceCoverage::create([
                    'insurance_policy_id' => $policy->id,
                    'coverage_type' => $coverage['type'],
                    'coverage_amount' => $coverage['amount'],
                    'premium' => $coverage['premium'] ?? 0,
                    'deductible' => $coverage['deductible'] ?? 0,
                ]);
            }
        }

        return redirect()->route('insurance.policies.show', $policy)
            ->with('success', 'تم إنشاء بوليصة التأمين بنجاح');
    }

    /**
     * Display the specified insurance policy.
     */
    public function show(InsurancePolicy $policy)
    {
        $policy->load(['provider', 'property', 'coverages', 'claims', 'payments', 'renewals']);
        
        return view('insurance.policies.show', compact('policy'));
    }

    /**
     * Show the form for editing the specified insurance policy.
     */
    public function edit(InsurancePolicy $policy)
    {
        $providers = InsuranceProvider::where('active', true)->get();
        $properties = []; // Get from properties table
        
        return view('insurance.policies.edit', compact('policy', 'providers', 'properties'));
    }

    /**
     * Update the specified insurance policy.
     */
    public function update(Request $request, InsurancePolicy $policy)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'insurance_provider_id' => 'required|exists:insurance_providers,id',
            'property_id' => 'required|exists:properties,id',
            'policy_type' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'premium_amount' => 'required|numeric|min:0',
            'coverage_amount' => 'required|numeric|min:0',
            'deductible' => 'nullable|numeric|min:0',
            'payment_frequency' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|max:255',
        ]);

        $policy->update($validated);

        return redirect()->route('insurance.policies.show', $policy)
            ->with('success', 'تم تحديث بوليصة التأمين بنجاح');
    }

    /**
     * Remove the specified insurance policy.
     */
    public function destroy(InsurancePolicy $policy)
    {
        $policy->delete();

        return redirect()->route('insurance.policies.index')
            ->with('success', 'تم حذف بوليصة التأمين بنجاح');
    }

    /**
     * Activate the insurance policy.
     */
    public function activate(InsurancePolicy $policy)
    {
        $policy->update([
            'status' => 'active',
            'activated_at' => now(),
        ]);

        return back()->with('success', 'تم تفعيل بوليصة التأمين بنجاح');
    }

    /**
     * Suspend the insurance policy.
     */
    public function suspend(InsurancePolicy $policy)
    {
        $policy->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        return back()->with('success', 'تم تعليق بوليصة التأمين بنجاح');
    }

    /**
     * Cancel the insurance policy.
     */
    public function cancel(InsurancePolicy $policy, Request $request)
    {
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
            'cancellation_date' => 'required|date',
            'refund_amount' => 'nullable|numeric|min:0',
        ]);

        $policy->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'],
            'cancellation_date' => $validated['cancellation_date'],
            'refund_amount' => $validated['refund_amount'] ?? 0,
        ]);

        return back()->with('success', 'تم إلغاء بوليصة التأمين بنجاح');
    }

    /**
     * Renew the insurance policy.
     */
    public function renew(InsurancePolicy $policy, Request $request)
    {
        $validated = $request->validate([
            'renewal_date' => 'required|date',
            'new_premium_amount' => 'required|numeric|min:0',
            'new_coverage_amount' => 'required|numeric|min:0',
            'renewal_terms' => 'nullable|string',
        ]);

        // Create renewal record
        $policy->renewals()->create([
            'renewal_number' => $this->generateRenewalNumber(),
            'renewal_date' => $validated['renewal_date'],
            'old_premium_amount' => $policy->premium_amount,
            'new_premium_amount' => $validated['new_premium_amount'],
            'old_coverage_amount' => $policy->coverage_amount,
            'new_coverage_amount' => $validated['new_coverage_amount'],
            'renewal_terms' => $validated['renewal_terms'],
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم إنشاء طلب التجديد بنجاح');
    }

    /**
     * Download the insurance policy.
     */
    public function download(InsurancePolicy $policy)
    {
        $pdf = PDF::loadView('insurance.policies.pdf', compact('policy'));
        
        return $pdf->download('policy_' . $policy->policy_number . '.pdf');
    }

    /**
     * Send reminder for the insurance policy.
     */
    public function sendReminder(InsurancePolicy $policy)
    {
        // Send reminder logic
        
        return back()->with('success', 'تم إرسال التذكير بنجاح');
    }

    /**
     * Get policy coverage details.
     */
    public function coverage(InsurancePolicy $policy)
    {
        $policy->load(['coverages']);
        
        return view('insurance.policies.coverage', compact('policy'));
    }

    /**
     * Get policy claims.
     */
    public function claims(InsurancePolicy $policy)
    {
        $policy->load(['claims']);
        
        return view('insurance.policies.claims', compact('policy'));
    }

    /**
     * Get policy payments.
     */
    public function payments(InsurancePolicy $policy)
    {
        $policy->load(['payments']);
        
        return view('insurance.policies.payments', compact('policy'));
    }

    /**
     * Export policies.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Export logic
        
        return response()->download('policies_export.' . $format);
    }

    /**
     * Generate unique policy number.
     */
    private function generatePolicyNumber(): string
    {
        $prefix = 'POL';
        $year = date('Y');
        $sequence = InsurancePolicy::whereYear('created_at', $year)->count() + 1;
        
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate unique renewal number.
     */
    private function generateRenewalNumber(): string
    {
        $prefix = 'REN';
        $year = date('Y');
        $sequence = InsurancePolicy::whereYear('created_at', $year)->count() + 1;
        
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
