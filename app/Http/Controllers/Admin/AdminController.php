<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_properties' => Property::count(),
            'new_properties_today' => Property::whereDate('created_at', today())->count(),
            'total_agents' => User::where('user_type', 'agent')->count(),
            'total_companies' => Company::count(),
            'new_companies_today' => Company::whereDate('created_at', today())->count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'sold_properties' => Property::where('status', 'sold')->count(),
            'total_revenue' => 0,
            'revenue_today' => 0,
            'recent_users' => User::latest()->take(5)->get(),
            'recent_properties' => Property::with('agent')->latest()->take(5)->get(),
            'recent_activity' => [
                ['icon' => 'users', 'message' => 'System initialized', 'time' => now()->diffForHumans()],
            ],
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function users()
    {
        $users = User::latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function properties()
    {
        $properties = Property::with('agent')->latest()->paginate(10);
        return view('admin.properties.index', compact('properties'));
    }

    public function companies()
    {
        $companies = Company::latest()->paginate(10);
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

        $user = User::create([
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
        ]);

        // Set agent-specific fields if user type is agent
        if ($request->user_type === 'agent') {
            $user->update([
                'is_agent' => true,
                'agent_license_number' => $request->agent_license_number ?? null,
                'agent_company' => $request->agent_company ?? null,
                'agent_bio' => $request->agent_bio ?? null,
            ]);
        }

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

        $user->update([
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
        ]);

        // Update agent-specific fields
        if ($request->user_type === 'agent') {
            $user->update([
                'is_agent' => true,
                'agent_license_number' => $request->agent_license_number ?? null,
                'agent_company' => $request->agent_company ?? null,
                'agent_bio' => $request->agent_bio ?? null,
            ]);
        } else {
            $user->update([
                'is_agent' => false,
                'agent_license_number' => null,
                'agent_company' => null,
                'agent_bio' => null,
            ]);
        }

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

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

        $user->delete();
        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleUserStatus(User $user)
    {
        $newStatus = $user->account_status === 'active' ? 'inactive' : 'active';
        $user->update(['account_status' => $newStatus]);

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

        Company::create($companyData);

        return redirect()->route('admin.companies')
            ->with('success', 'Company created successfully.');
    }
}
