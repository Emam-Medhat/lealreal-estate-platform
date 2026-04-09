<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyMember;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CompanyMemberController extends Controller
{
    public function index(Company $company)
    {
        $members = $company->members()->with('user')->paginate(15);
        return view('companies.members.index', compact('company', 'members'));
    }

    public function create(Company $company)
    {
        return view('companies.members.create', compact('company'));
    }

    public function store(Request $request, Company $company)
    {
        // Validation logic here
        // $company->members()->create(...)
        return redirect()->back()->with('success', 'Member invited.');
    }

    public function show(Company $company, CompanyMember $member)
    {
        return view('companies.members.show', compact('company', 'member'));
    }

    public function edit(Company $company, CompanyMember $member)
    {
        return view('companies.members.edit', compact('company', 'member'));
    }

    public function update(Request $request, Company $company, CompanyMember $member)
    {
        // Update logic
        return redirect()->route('companies.members.index', $company);
    }

    public function destroy(Company $company, CompanyMember $member)
    {
        $member->delete();
        return redirect()->back()->with('success', 'Member removed.');
    }

    public function resendInvitation(Company $company, CompanyMember $member)
    {
        // Resend logic
        return back()->with('success', 'Invitation resent.');
    }

    // API methods...
    public function apiIndex(Company $company)
    {
        return response()->json($company->members);
    }
}
