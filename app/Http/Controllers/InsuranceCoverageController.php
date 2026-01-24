<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceCoverageController extends Controller
{
    public function index()
    {
        return view('insurance.coverage.index');
    }
    public function create()
    {
        return view('insurance.coverage.create');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function show($id)
    {
        return view('insurance.coverage.show');
    }
    public function edit($id)
    {
        return view('insurance.coverage.edit');
    }
    public function update(Request $request, $id)
    {
        return redirect()->back();
    }
    public function destroy($id)
    {
        return redirect()->back();
    }
}
