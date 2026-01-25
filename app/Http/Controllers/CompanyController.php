<?php

namespace App\Http\Controllers;

use App\Http\Requests\Company\StoreCompanyRequest;
use App\Http\Requests\Company\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\CompanyProfile;
use App\Models\User;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    public function index(Request $request)
    {
        $companies = Company::with('profile')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->latest()
            ->paginate(20);

        return view('companies.index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['profile', 'branches', 'members', 'properties' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return view('companies.show', compact('company'));
    }

    public function create()
    {
        return view('companies.create');
    }

    public function store(StoreCompanyRequest $request)
    {
        DB::beginTransaction();
        
        try {
            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'type' => $request->type,
                'registration_number' => $request->registration_number,
                'tax_id' => $request->tax_id,
                'status' => $request->status ?? 'pending',
                'created_by' => Auth::id(),
            ]);

            $profileData = [
                'description' => $request->description,
                'founded_date' => $request->founded_date,
                'employee_count' => $request->employee_count,
                'annual_revenue' => $request->annual_revenue,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'services' => $request->services,
                'specializations' => $request->specializations,
                'certifications' => $request->certifications,
                'awards' => $request->awards,
            ];

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $path = $logo->store('company-logos', 'public');
                $profileData['logo'] = $path;
            }

            $company->profile()->create($profileData);

            // Add creator as company admin
            $company->members()->create([
                'user_id' => Auth::id(),
                'role' => 'admin',
                'status' => 'active',
                'joined_at' => now(),
            ]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_company',
                'details' => "Created company: {$company->name}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('companies.show', $company)
                ->with('success', 'Company created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
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
        
        DB::beginTransaction();
        
        try {
            $company->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'website' => $request->website,
                'type' => $request->type,
                'registration_number' => $request->registration_number,
                'tax_id' => $request->tax_id,
                'status' => $request->status,
            ]);

            $profileData = [
                'description' => $request->description,
                'founded_date' => $request->founded_date,
                'employee_count' => $request->employee_count,
                'annual_revenue' => $request->annual_revenue,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'postal_code' => $request->postal_code,
                'services' => $request->services,
                'specializations' => $request->specializations,
                'certifications' => $request->certifications,
                'awards' => $request->awards,
            ];

            if ($request->hasFile('logo')) {
                if ($company->profile && $company->profile->logo) {
                    Storage::disk('public')->delete($company->profile->logo);
                }
                
                $logo = $request->file('logo');
                $path = $logo->store('company-logos', 'public');
                $profileData['logo'] = $path;
            }

            $company->profile()->updateOrCreate(['company_id' => $company->id], $profileData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_company',
                'details' => "Updated company: {$company->name}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('companies.show', $company)
                ->with('success', 'Company updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update company: ' . $e->getMessage());
        }
    }

    public function destroy(Company $company)
    {
        $this->authorize('delete', $company);
        
        $companyName = $company->name;
        
        if ($company->profile && $company->profile->logo) {
            Storage::disk('public')->delete($company->profile->logo);
        }
        
        $company->delete();

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_company',
            'details' => "Deleted company: {$companyName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }

    public function toggleStatus(Company $company): JsonResponse
    {
        $this->authorize('update', $company);
        
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $company->update(['status' => $newStatus]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_company_status',
            'details' => "Toggled company {$company->name} status to {$newStatus}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Company status changed to {$newStatus}"
        ]);
    }

    public function getCompanies(Request $request): JsonResponse
    {
        $companies = Company::with('profile')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->type, function ($query, $type) {
                $query->where('type', $type);
            })
            ->where('status', 'active')
            ->get(['id', 'name', 'type', 'email']);

        return response()->json([
            'success' => true,
            'companies' => $companies
        ]);
    }

    public function getCompanyStats(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $stats = [
            'total_properties' => $company->properties()->count(),
            'total_members' => $company->members()->count(),
            'total_branches' => $company->branches()->count(),
            'active_listings' => $company->properties()->where('status', 'published')->count(),
            'total_revenue' => $company->transactions()->sum('amount'),
            'member_since' => $company->created_at->format('M d, Y'),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $this->authorize('bulkDelete', Company::class);
        
        $companyIds = $request->input('companies', []);
        
        DB::beginTransaction();
        
        try {
            $companies = Company::whereIn('id', $companyIds)->get();
            
            foreach ($companies as $company) {
                if ($company->profile && $company->profile->logo) {
                    Storage::disk('public')->delete($company->profile->logo);
                }
                $company->delete();
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'bulk_deleted_companies',
                'details' => "Bulk deleted " . count($companyIds) . " companies",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Selected companies deleted successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete companies: ' . $e->getMessage()
            ], 500);
        }
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

        if ($company->members()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'User is already a member of this company.');
        }

        $company->members()->create([
            'user_id' => $user->id,
            'role' => $request->role,
            'status' => 'active',
            'joined_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Member added successfully.');
    }

    public function removeMember(Company $company, User $user)
    {
        $this->authorize('update', $company);
        
        if ($user->id === $company->created_by) {
            return redirect()->back()->with('error', 'The company owner cannot be removed.');
        }

        $company->members()->where('user_id', $user->id)->delete();

        return redirect()->back()->with('success', 'Member removed successfully.');
    }
}
