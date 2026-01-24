<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsuranceDocumentController extends Controller
{
    public function index()
    {
        return view('insurance.documents.index');
    }
    public function store(Request $request)
    {
        return redirect()->back();
    }
    public function download($id)
    {
        return response()->download('test.txt');
    }
    public function destroy($id)
    {
        return redirect()->back();
    }
}
