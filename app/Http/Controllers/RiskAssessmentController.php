<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RiskAssessmentController extends Controller
{
    public function index()
    {
        return view('insurance.risk-assessment.index');
    }
    public function create()
    {
        return view('insurance.risk-assessment.create');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function show($id)
    {
        return view('insurance.risk-assessment.show');
    }
}
