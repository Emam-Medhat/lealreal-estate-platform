<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsurancePaymentController extends Controller
{
    public function index()
    {
        return view('insurance.payments.index');
    }
    public function create()
    {
        return view('insurance.payments.create');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function show($id)
    {
        return view('insurance.payments.show');
    }
}
