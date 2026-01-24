<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceQuoteController extends Controller
{
    public function index()
    {
        return view('insurance.quotes.index');
    }
    public function create()
    {
        return view('insurance.quotes.create');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function show($id)
    {
        return view('insurance.quotes.show');
    }
    public function edit($id)
    {
        return view('insurance.quotes.edit');
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
