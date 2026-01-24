<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanySettingsController extends Controller
{
    public function index(Company $company)
    {
        return view('companies.settings.index', compact('company'));
    }

    public function profile(Company $company)
    {
        return view('companies.settings.profile', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        // Update settings
        return back()->with('success', 'Settings updated.');
    }

    // Stub methods for other settings pages
    public function notifications(Company $company)
    {
        return view('companies.settings.notifications', compact('company'));
    }
    public function privacy(Company $company)
    {
        return view('companies.settings.privacy', compact('company'));
    }
    public function features(Company $company)
    {
        return view('companies.settings.features', compact('company'));
    }
    public function branding(Company $company)
    {
        return view('companies.settings.branding', compact('company'));
    }
}
