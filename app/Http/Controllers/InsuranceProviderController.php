<?php

namespace App\Http\Controllers;

use App\Models\InsuranceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InsuranceProviderController extends Controller
{
    /**
     * Display a listing of insurance providers.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $type = $request->get('type');

        $providers = InsuranceProvider::with(['policies', 'claims'])
            ->when($search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('provider_code', 'like', "%{$search}%");
            })
            ->when($status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($type, function($query, $type) {
                return $query->where('provider_type', $type);
            })
            ->orderBy('name', 'asc')
            ->paginate(15);

        return view('insurance.providers.index', compact('providers'));
    }

    /**
     * Show the form for creating a new insurance provider.
     */
    public function create()
    {
        return view('insurance.providers.create');
    }

    /**
     * Store a newly created insurance provider.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'provider_type' => 'required|in:company,broker,agent,underwriter,reinsurer',
            'license_number' => 'required|string|max:255|unique:insurance_providers',
            'license_expiry' => 'required|date|after:today',
            'registration_number' => 'required|string|max:255|unique:insurance_providers',
            'tax_id' => 'required|string|max:255|unique:insurance_providers',
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:insurance_providers',
            'email_support' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:500',
            'address_ar' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'city_ar' => 'nullable|string|max:255',
            'state' => 'required|string|max:255',
            'state_ar' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_title' => 'nullable|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'required|email|max:255',
            'services_offered' => 'nullable|array',
            'coverage_types' => 'nullable|array',
            'specializations' => 'nullable|array',
            'regions_served' => 'nullable|array',
            'min_premium' => 'nullable|numeric|min:0',
            'max_coverage' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|in:monthly,quarterly,semi_annually,annually',
            'claims_processing_days' => 'nullable|integer|min:1',
            'customer_satisfaction' => 'nullable|numeric|min:0|max:5',
            'financial_rating' => 'nullable|numeric|min:0|max:5',
            'rating_agency' => 'nullable|string|max:255',
            'rating_date' => 'nullable|date',
            'accreditations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'awards' => 'nullable|array',
            'years_in_business' => 'nullable|integer|min:0',
            'key_personnel' => 'nullable|array',
            'branch_offices' => 'nullable|array',
            'partners' => 'nullable|array',
            'technology_platforms' => 'nullable|array',
            'api_integrations' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:2000',
            'notes_ar' => 'nullable|string|max:2000',
        ]);

        $provider = InsuranceProvider::create(array_merge($validated, [
            'provider_code' => $this->generateProviderCode(),
            'status' => 'active',
            'created_by' => auth()->id(),
        ]));

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('providers/logos', 'public');
            $provider->update(['logo' => $logoPath]);
        }

        return redirect()->route('insurance.providers.show', $provider)
            ->with('success', 'تم إنشاء شركة التأمين بنجاح');
    }

    /**
     * Display the specified insurance provider.
     */
    public function show(InsuranceProvider $provider)
    {
        $provider->load(['policies', 'claims', 'documents']);
        
        return view('insurance.providers.show', compact('provider'));
    }

    /**
     * Show the form for editing the specified insurance provider.
     */
    public function edit(InsuranceProvider $provider)
    {
        return view('insurance.providers.edit', compact('provider'));
    }

    /**
     * Update the specified insurance provider.
     */
    public function update(Request $request, InsuranceProvider $provider)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'provider_type' => 'required|in:company,broker,agent,underwriter,reinsurer',
            'license_number' => 'required|string|max:255|unique:insurance_providers,license_number,' . $provider->id,
            'license_expiry' => 'required|date|after:today',
            'registration_number' => 'required|string|max:255|unique:insurance_providers,registration_number,' . $provider->id,
            'tax_id' => 'required|string|max:255|unique:insurance_providers,tax_id,' . $provider->id,
            'phone' => 'required|string|max:20',
            'phone_secondary' => 'nullable|string|max:20',
            'email' => 'required|email|max:255|unique:insurance_providers,email,' . $provider->id,
            'email_support' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'required|string|max:500',
            'address_ar' => 'nullable|string|max:500',
            'city' => 'required|string|max:255',
            'city_ar' => 'nullable|string|max:255',
            'state' => 'required|string|max:255',
            'state_ar' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_person_title' => 'nullable|string|max:255',
            'contact_person_phone' => 'required|string|max:20',
            'contact_person_email' => 'required|email|max:255',
            'services_offered' => 'nullable|array',
            'coverage_types' => 'nullable|array',
            'specializations' => 'nullable|array',
            'regions_served' => 'nullable|array',
            'min_premium' => 'nullable|numeric|min:0',
            'max_coverage' => 'nullable|numeric|min:0',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'payment_terms' => 'nullable|in:monthly,quarterly,semi_annually,annually',
            'claims_processing_days' => 'nullable|integer|min:1',
            'customer_satisfaction' => 'nullable|numeric|min:0|max:5',
            'financial_rating' => 'nullable|numeric|min:0|max:5',
            'rating_agency' => 'nullable|string|max:255',
            'rating_date' => 'nullable|date',
            'accreditations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'awards' => 'nullable|array',
            'years_in_business' => 'nullable|integer|min:0',
            'key_personnel' => 'nullable|array',
            'branch_offices' => 'nullable|array',
            'partners' => 'nullable|array',
            'technology_platforms' => 'nullable|array',
            'api_integrations' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string|max:2000',
            'notes_ar' => 'nullable|string|max:2000',
        ]);

        $provider->update($validated);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('providers/logos', 'public');
            $provider->update(['logo' => $logoPath]);
        }

        return redirect()->route('insurance.providers.show', $provider)
            ->with('success', 'تم تحديث شركة التأمين بنجاح');
    }

    /**
     * Remove the specified insurance provider.
     */
    public function destroy(InsuranceProvider $provider)
    {
        $provider->delete();

        return redirect()->route('insurance.providers.index')
            ->with('success', 'تم حذف شركة التأمين بنجاح');
    }

    /**
     * Toggle provider status.
     */
    public function toggleStatus(InsuranceProvider $provider)
    {
        $newStatus = $provider->status === 'active' ? 'inactive' : 'active';
        $provider->update(['status' => $newStatus]);

        return back()->with('success', 'تم تحديث الحالة بنجاح');
    }

    /**
     * Verify provider.
     */
    public function verify(InsuranceProvider $provider)
    {
        $provider->update([
            'verified' => true,
            'verified_at' => now(),
        ]);

        return back()->with('success', 'تم التحقق من شركة التأمين بنجاح');
    }

    /**
     * Rate provider.
     */
    public function rate(InsuranceProvider $provider, Request $request)
    {
        $validated = $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        // Store rating logic

        return back()->with('success', 'تم تقييم شركة التأمين بنجاح');
    }

    /**
     * Get provider policies.
     */
    public function policies(InsuranceProvider $provider)
    {
        $policies = $provider->policies()->with(['property'])->paginate(15);
        
        return view('insurance.providers.policies', compact('provider', 'policies'));
    }

    /**
     * Get provider claims.
     */
    public function claims(InsuranceProvider $provider)
    {
        $claims = $provider->claims()->with(['policy'])->paginate(15);
        
        return view('insurance.providers.claims', compact('provider', 'claims'));
    }

    /**
     * Get provider performance.
     */
    public function performance(InsuranceProvider $provider)
    {
        $performance = [
            'policies_count' => $provider->policies()->count(),
            'active_policies' => $provider->policies()->where('status', 'active')->count(),
            'claims_count' => $provider->claims()->count(),
            'approved_claims' => $provider->claims()->where('status', 'approved')->count(),
            'rejected_claims' => $provider->claims()->where('status', 'rejected')->count(),
            'total_premiums' => $provider->policies()->sum('premium_amount'),
            'total_claims_paid' => $provider->claims()->where('status', 'approved')->sum('claimed_amount'),
            'claims_ratio' => 0,
            'average_processing_time' => 0,
            'customer_satisfaction' => $provider->customer_satisfaction,
            'financial_rating' => $provider->financial_rating,
        ];

        return view('insurance.providers.performance', compact('provider', 'performance'));
    }

    /**
     * Export providers.
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'excel');
        
        // Export logic
        
        return response()->download('providers_export.' . $format);
    }

    /**
     * Generate unique provider code.
     */
    private function generateProviderCode(): string
    {
        $prefix = 'PROV';
        $year = date('Y');
        $sequence = InsuranceProvider::whereYear('created_at', $year)->count() + 1;
        
        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }
}
