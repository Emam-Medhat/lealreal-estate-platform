<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\UserActivityLog;

class SettingsController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('settings.show', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'language' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:3',
        ]);

        $user->update($validated);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_settings',
            'details' => 'Updated general settings',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('settings.show')->with('success', 'Settings updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'changed_password',
            'details' => 'Changed account password',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('settings.show')->with('success', 'Password updated successfully.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
        ]);

        $user->update($validated);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_notifications',
            'details' => 'Updated notification settings',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('settings.show')->with('success', 'Notification settings updated successfully.');
    }
}