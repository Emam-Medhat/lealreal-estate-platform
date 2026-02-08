<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\KycVerification;
use App\Models\UserActivityLog;

class KYCController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $kyc = KycVerification::where('user_id', $user->id)->first();
        
        return view('kyc.show', compact('user', 'kyc'));
    }

    public function submit(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'document_type' => 'required|in:passport,id_card,driving_license',
            'document_number' => 'required|string|max:50',
            'document_front' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'document_back' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'selfie' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'date_of_birth' => 'required|date',
            'nationality' => 'required|string|max:50',
        ]);

        // Check if user already has pending or approved KYC
        $existingKyc = KycVerification::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();
            
        if ($existingKyc) {
            return back()->with('error', 'You already have a KYC verification in process.');
        }

        // Store uploaded files
        $documentFrontPath = $request->file('document_front')->store('kyc/documents', 'public');
        $documentBackPath = $request->file('document_back')->store('kyc/documents', 'public');
        $selfiePath = $request->file('selfie')->store('kyc/selfies', 'public');

        $kyc = KycVerification::create([
            'user_id' => $user->id,
            'document_type' => $request->document_type,
            'document_number' => $request->document_number,
            'document_front_path' => $documentFrontPath,
            'document_back_path' => $documentBackPath,
            'selfie_path' => $selfiePath,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'nationality' => $request->nationality,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'submitted_kyc',
            'details' => 'Submitted KYC verification documents',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('kyc.status')->with('success', 'KYC verification submitted successfully. It will be reviewed within 24-48 hours.');
    }

    public function status()
    {
        $user = Auth::user();
        $kyc = KycVerification::where('user_id', $user->id)->first();
        
        return view('kyc.status', compact('user', 'kyc'));
    }
}