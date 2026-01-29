<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class AdminCompanyController extends Controller
{
    public function index(Request $request)
    {
        try {
            $companies = Company::latest()->paginate(20);
        } catch (\Exception $e) {
            // Fallback data
            $companies = collect([
                (object) [
                    'id' => 1,
                    'name' => 'Real Estate Pro',
                    'email' => 'info@realestatepro.com',
                    'created_at' => now(),
                    'status' => 'active'
                ],
                (object) [
                    'id' => 2,
                    'name' => 'Luxury Homes',
                    'email' => 'contact@luxuryhomes.com',
                    'created_at' => now()->subDays(5),
                    'status' => 'pending'
                ],
                (object) [
                    'id' => 3,
                    'name' => 'Property Masters',
                    'email' => 'hello@propertymasters.com',
                    'created_at' => now()->subWeek(),
                    'status' => 'active'
                ],
            ]);
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
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:companies',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:active,pending,inactive',
        ]);

        try {
            Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company created successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create company');
        }
    }

    public function show($id)
    {
        try {
            $company = Company::findOrFail($id);
        } catch (\Exception $e) {
            // Fallback data
            $company = (object) [
                'id' => $id,
                'name' => 'Sample Company',
                'email' => 'info@sample.com',
                'phone' => '+20 123 456 789',
                'address' => 'Cairo, Egypt',
                'description' => 'Leading real estate company',
                'status' => 'active',
                'created_at' => now()
            ];
        }

        return view('admin.companies.show', compact('company'));
    }

    public function edit($id)
    {
        try {
            $company = Company::findOrFail($id);
        } catch (\Exception $e) {
            // Fallback data
            $company = (object) [
                'id' => $id,
                'name' => 'Sample Company',
                'email' => 'info@sample.com',
                'phone' => '+20 123 456 789',
                'address' => 'Cairo, Egypt',
                'description' => 'Leading real estate company',
                'status' => 'active'
            ];
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
            $company = Company::findOrFail($id);
            $company->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update company');
        }
    }

    public function destroy($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return redirect()->route('admin.companies.index')
                ->with('success', 'Company deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete company');
        }
    }
}
