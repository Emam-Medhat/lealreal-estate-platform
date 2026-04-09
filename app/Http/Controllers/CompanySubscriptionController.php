<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

class CompanySubscriptionController extends Controller
{
    public function index(Company $company)
    {
        return view('companies.subscription.index', compact('company'));
    }

    public function plans(Company $company)
    {
        return view('companies.subscription.plans', compact('company'));
    }

    public function subscribe(Request $request, Company $company)
    {
        return back()->with('success', 'Subscribed successfully.');
    }

    public function cancel(Request $request, Company $company)
    {
        return back()->with('success', 'Subscription cancelled.');
    }

    public function renew(Request $request, Company $company)
    {
        return back();
    }
    public function upgrade(Request $request, Company $company)
    {
        return back();
    }
    public function history(Company $company)
    {
        return view('companies.subscription.history', compact('company'));
    }
}
