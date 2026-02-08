<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    protected $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
        // Assuming policies are in place, authorizeResource can be used
        // $this->authorizeResource(Company::class, 'company');
    }

    public function index(Request $request)
    {
        $companies = $this->companyService->getPaginatedCompanies($request->all());

        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['profile', 'branches', 'members', 'properties' => function ($query) {
            $query->with(['media', 'propertyType', 'agent:id,name', 'location', 'price'])
                  ->latest()
                  ->limit(10);
        }]);

        return view('companies.show', compact('company'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('company-logos', 'public');
                $data['logo'] = $path;
            }

            $company = $this->companyService->createCompany($data);

            return redirect()->route('companies.show', $company)
                ->with('success', 'Company created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create company: ' . $e->getMessage());
        }
    }

    public function edit(Company $company)
    {
        $this->authorize('update', $company);
        
        $company->load('profile');
        return view('companies.edit', compact('company'));
    }

    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $this->authorize('update', $company);
        
        try {
            $data = $request->validated();

            if ($request->hasFile('logo')) {
                if ($company->profile && $company->profile->logo) {
                    Storage::disk('public')->delete($company->profile->logo);
                }
                
                $path = $request->file('logo')->store('company-logos', 'public');
                $data['logo'] = $path;
            }

            $this->companyService->updateCompany($company->id, $data);

            return redirect()->route('companies.show', $company)
                ->with('success', 'Company updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);

        try {
            $this->companyService->deleteCompany($company->id);

            return redirect()->route('companies.index')
                ->with('success', 'Company deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete company');
        }
    }

    public function getCompanies(Request $request): JsonResponse
    {
        $companies = $this->companyService->getActiveCompanies($request->all());

        return response()->json([
            'success' => true,
            'companies' => $companies
        ]);
    }

    public function members(Company $company)
    {
        $this->authorize('view', $company);
        
        $members = $company->members()->with('user')->paginate(20);
        return view('companies.members', compact('company', 'members'));
    }

    public function addMember(Request $request, Company $company)
    {
        $this->authorize('update', $company);
        
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'role' => 'required|string|in:admin,manager,agent,staff',
        ]);

        $user = User::where('email', $request->email)->first();

        try {
            $this->companyService->addMember($company->id, $user->id, $request->role);
            return redirect()->back()->with('success', 'Member added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeMember(Company $company, User $user)
    {
        $this->authorize('update', $company);
        
        try {
            $this->companyService->removeMember($company->id, $user->id);
            return redirect()->back()->with('success', 'Member removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
