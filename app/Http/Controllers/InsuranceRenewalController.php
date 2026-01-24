<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceRenewalController extends Controller
{
    public function index()
    {
        return view('insurance.renewals.index');
    }
    public function create()
    {
        return view('insurance.renewals.create');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function show($id)
    {
        return view('insurance.renewals.show');
    }
}
