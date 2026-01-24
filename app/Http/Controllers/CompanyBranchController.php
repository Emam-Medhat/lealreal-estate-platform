<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyBranch;
use Illuminate\Http\Request;

class CompanyBranchController extends Controller
{
    public function index(Company $company)
    {
        $branches = $company->branches()->paginate(15);
        return view('companies.branches.index', compact('company', 'branches'));
    }

    public function create(Company $company)
    {
        return view('companies.branches.create', compact('company'));
    }

    public function store(Request $request, Company $company)
    {
        // $company->branches()->create($request->all());
        return redirect()->route('companies.branches.index', $company);
    }

    public function show(Company $company, CompanyBranch $branch)
    {
        return view('companies.branches.show', compact('company', 'branch'));
    }

    public function edit(Company $company, CompanyBranch $branch)
    {
        return view('companies.branches.edit', compact('company', 'branch'));
    }

    public function update(Request $request, Company $company, CompanyBranch $branch)
    {
        // $branch->update($request->all());
        return redirect()->route('companies.branches.index', $company);
    }

    public function destroy(Company $company, CompanyBranch $branch)
    {
        $branch->delete();
        return back();
    }
}
