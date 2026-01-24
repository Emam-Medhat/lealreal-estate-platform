<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompanyInvitationController extends Controller
{
    public function index()
    {
        return view('companies.invitations.index');
    }

    public function accept($id)
    {
        return redirect()->route('home')->with('success', 'Invitation accepted.');
    }

    public function decline($id)
    {
        return redirect()->route('home')->with('success', 'Invitation declined.');
    }

    public function store(Request $request)
    {
        return back();
    }
    public function show($id)
    {
        return view('companies.invitations.show');
    }
    public function resend($id)
    {
        return back();
    }
}
