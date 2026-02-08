<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(Request $request)
    {
        try {
            $companies = $this->companyService->getPaginatedCompanies($request->all());
        } catch (\Exception $e) {
            \Log::error('Failed to fetch companies: ' . $e->getMessage());
            // Return empty result instead of crashing
            $companies = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        }

        return view('admin.companies.index', compact('companies'));
    }

    public function create()
    {
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {
        // Validation
        $validated = $request->validate([
            // Basic Information
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'type' => 'required|string|in:developer,brokerage,property_management,construction,investment,consulting',
            'status' => 'required|string|in:active,pending,suspended,rejected',
            'description' => 'nullable|string',
            
            // Business Details
            'registration_number' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:255',
            'founded_date' => 'nullable|date',
            'employee_count' => 'required|integer|min:1',
            'annual_revenue' => 'nullable|numeric|min:0',
            'subscription_plan' => 'required|string|in:basic,professional,enterprise',
            
            // Address Information
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            
            // Verification & Features
            'is_featured' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'verification_level' => 'required|integer|min:0|max:3',
            'rating' => 'required|numeric|min:0|max:5',
            'total_reviews' => 'required|integer|min:0',
            'subscription_expires_at' => 'nullable|date',
            
            // API Settings
            'api_key' => 'nullable|string|max:255',
            'webhook_url' => 'nullable|url|max:500',
            'logo_url' => 'nullable|image|max:2048',
            'cover_image_url' => 'nullable|image|max:4096',
        ]);

        try {
            // Handle file uploads
            if ($request->hasFile('logo_url')) {
                $validated['logo_url'] = $request->file('logo_url')->store('company-logos', 'public');
            }
            
            if ($request->hasFile('cover_image_url')) {
                $validated['cover_image_url'] = $request->file('cover_image_url')->store('company-covers', 'public');
            }

            // Generate API key if not provided
            if (empty($validated['api_key'])) {
                $validated['api_key'] = Str::random(32);
            }

            $this->companyService->createCompany($validated);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company created successfully');
        } catch (\Exception $e) {
            \Log::error('Company creation failed: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $company = $this->companyService->getCompanyById($id);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Company not found.');
        }

        return view('admin.companies.show', compact('company'));
    }

    public function edit($id)
    {
        try {
            $company = $this->companyService->getCompanyById($id);
        } catch (\Exception $e) {
            return redirect()->route('admin.companies.index')
                ->with('error', 'Company not found.');
        }

        return view('admin.companies.edit', compact('company'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies,email,' . $id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,pending,inactive',
        ]);

        try {
            $this->companyService->updateCompany($id, $request->all());

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update company');
        }
    }

    public function destroy($id)
    {
        try {
            $this->companyService->deleteCompany($id);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete company');
        }
    }
}
