<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\Property;
use App\Notifications\NewInquiryNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;

class InquiryController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'property_id' => 'required|exists:properties,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'required|string|max:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $property = Property::findOrFail($request->property_id);

        $inquiry = Inquiry::create([
            'property_id' => $property->id,
            'user_id' => auth()->id(), // Can be null for guests
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'message' => $request->message,
            'status' => 'new',
        ]);

        // Notify the agent responsible for the property
        if ($property->agent) {
            Notification::send($property->agent, new NewInquiryNotification($inquiry));
        }
        
        // Also notify admins
        $admins = \App\Models\User::whereIn('user_type', ['admin', 'super_admin'])->get();
        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewInquiryNotification($inquiry));
        }

        return response()->json(['success' => 'Inquiry sent successfully!'], 201);
    }
}
