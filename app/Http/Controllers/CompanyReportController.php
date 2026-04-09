<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanyReportController extends Controller
{
    public function index(Company $company)
    {
        return view('companies.reports.index', compact('company'));
    }

    public function generate(Request $request, Company $company)
    {
        // Dispatch GenerateCompanyReport job
        return back()->with('success', 'Report generation started.');
    }

    public function show(Company $company, $reportId)
    {
        return view('companies.reports.show', compact('company'));
    }

    public function download(Company $company, $reportId)
    {
        // Download logic
    }

    public function apiGenerate(Request $request, Company $company)
    {
        return response()->json(['message' => 'Report generation started']);
    }

    public function apiIndex(Company $company)
    {
        return response()->json([]);
    }
    public function apiShow(Company $company, $report)
    {
        return response()->json([]);
    }
    public function apiDownload(Company $company, $report)
    {
        return response()->json([]);
    }
    public function preview(Company $company, $report)
    {
        return view('companies.reports.preview');
    }
    public function destroy(Company $company, $report)
    {
        return back();
    }

}
