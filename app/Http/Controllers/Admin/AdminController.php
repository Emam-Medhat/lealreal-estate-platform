<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Services\Admin\AdminDashboardService;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\PropertyRepositoryInterface;
use App\Repositories\Contracts\CompanyRepositoryInterface;

class AdminController extends Controller
{
    protected $dashboardService;
    protected $userRepository;
    protected $propertyRepository;
    protected $companyRepository;

    public function __construct(
        AdminDashboardService $dashboardService,
        UserRepositoryInterface $userRepository,
        PropertyRepositoryInterface $propertyRepository,
        CompanyRepositoryInterface $companyRepository
    ) {
        $this->middleware('auth');
        $this->middleware('admin');
        $this->dashboardService = $dashboardService;
        $this->userRepository = $userRepository;
        $this->propertyRepository = $propertyRepository;
        $this->companyRepository = $companyRepository;
    }

    public function dashboard()
    {
        $stats = $this->dashboardService->getDashboardStats();
        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = $this->userRepository->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function properties()
    {
        $properties = $this->propertyRepository->paginate(10, ['*'], ['agent']);
        return view('admin.properties.index', compact('properties'));
    }

    public function companies()
    {
        $companies = $this->companyRepository->paginate(10);
        return view('admin.companies.index', compact('companies'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => ['required', Rule::in(['user', 'agent', 'company', 'developer', 'investor', 'admin', 'super_admin'])],
            'phone' => 'nullable|string|max:20|unique:users',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'account_status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'banned', 'pending_verification'])],
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
            'account_status' => $request->account_status,
            'email_verified_at' => now(),
        ];

        if ($request->user_type === 'agent') {
            $userData['is_agent'] = true;
            $userData['agent_license_number'] = $request->agent_license_number ?? null;
            $userData['agent_company'] = $request->agent_company ?? null;
            $userData['agent_bio'] = $request->agent_bio ?? null;
        }

        $this->userRepository->create($userData);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'user_type' => ['required', Rule::in(['user', 'agent', 'company', 'developer', 'investor', 'admin', 'super_admin'])],
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'account_status' => ['required', Rule::in(['active', 'inactive', 'suspended', 'banned', 'pending_verification'])],
        ]);

        $userData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'username' => $request->username,
            'user_type' => $request->user_type,
            'phone' => $request->phone,
            'country' => $request->country,
            'city' => $request->city,
            'account_status' => $request->account_status,
        ];

        if ($request->user_type === 'agent') {
            $userData['is_agent'] = true;
            $userData['agent_license_number'] = $request->agent_license_number ?? null;
            $userData['agent_company'] = $request->agent_company ?? null;
            $userData['agent_bio'] = $request->agent_bio ?? null;
        } else {
            $userData['is_agent'] = false;
            $userData['agent_license_number'] = null;
            $userData['agent_company'] = null;
            $userData['agent_bio'] = null;
        }

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        $this->userRepository->update($user->id, $userData);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users')
                ->with('error', 'You cannot delete your own account.');
        }

        $this->userRepository->deleteById($user->id);
        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        $newStatus = $user->account_status === 'active' ? 'inactive' : 'active';
        $this->userRepository->update($user->id, ['account_status' => $newStatus]);

        return redirect()->back()
            ->with('success', "User status changed to {$newStatus}.");
    }

    public function createCompany()
    {
        return view('admin.companies.create');
    }

    public function storeCompany(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'phone' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'registration_number' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $companyData = $request->except('logo');

        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('companies/logos', 'public');
            $companyData['logo'] = $logoPath;
        }

        $this->companyRepository->create($companyData);

        return redirect()->route('admin.companies')
            ->with('success', 'Company created successfully.');
    }
}
