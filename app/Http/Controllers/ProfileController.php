<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\UserActivityLog;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
        ]);

        $user->update($validated);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_profile',
            'details' => 'Updated profile information',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        $avatarPath = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $avatarPath]);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_avatar',
            'details' => 'Updated profile avatar',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('profile.show')->with('success', 'Avatar updated successfully.');
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();
        
        if ($user->avatar) {
            Storage::delete($user->avatar);
            $user->update(['avatar' => null]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'deleted_avatar',
                'details' => 'Deleted profile avatar',
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Avatar removed successfully.');
    }
}