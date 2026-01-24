<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceInspectionController extends Controller
{
    public function index()
    {
        return view('insurance.inspections.index');
    }
    public function create()
    {
        return view('insurance.inspections.create');
    }
    public function store(Request $request)
    {
        return back();
    }
    public function show($inspection)
    {
        return view('insurance.inspections.show');
    }
    public function edit($inspection)
    {
        return view('insurance.inspections.edit');
    }
    public function update(Request $request, $inspection)
    {
        return back();
    }
    public function destroy($inspection)
    {
        return back();
    }
    public function conduct($inspection)
    {
        return back();
    }
    public function complete($inspection)
    {
        return back();
    }
    public function schedule(Request $request, $inspection)
    {
        return back();
    }
    public function addPhotos(Request $request, $inspection)
    {
        return back();
    }
    public function addFindings(Request $request, $inspection)
    {
        return back();
    }
    public function report($inspection)
    {
        return view('insurance.inspections.report');
    }
    public function calendar()
    {
        return view('insurance.inspections.calendar');
    }
    public function export()
    {
        return back();
    }
}
