<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $user->load('profile');
        
        return view('user.profile', compact('user'));
    }

    public function edit()
    {
        $user = Auth::user();
        $user->load('profile');
        
        return view('user.edit-profile', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        
        // Update user basic info
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        // Update or create profile
        $profileData = [
            'bio' => $request->bio,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'website' => $request->website,
            'linkedin' => $request->linkedin,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'instagram' => $request->instagram,
        ];

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            $profileData['avatar'] = $path;
        }

        $user->profile()->updateOrCreate(['user_id' => $user->id], $profileData);

        UserActivityLog::create([
            'user_id' => $user->id,
            'action' => 'updated_profile',
            'details' => 'Updated personal profile information',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('user.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }
            
            $avatar = $request->file('avatar');
            $path = $avatar->store('avatars', 'public');
            
            $user->profile()->updateOrCreate(['user_id' => $user->id], ['avatar' => $path]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'uploaded_avatar',
                'details' => 'Updated profile avatar',
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'avatar_url' => Storage::url($path),
                'message' => 'Avatar uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No avatar file provided'
        ], 400);
    }

    public function removeAvatar(): JsonResponse
    {
        $user = Auth::user();
        
        if ($user->profile && $user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
            $user->profile->update(['avatar' => null]);

            UserActivityLog::create([
                'user_id' => $user->id,
                'action' => 'removed_avatar',
                'details' => 'Removed profile avatar',
                'ip_address' => request()->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Avatar removed successfully'
        ]);
    }

    public function getProfileStats(): JsonResponse
    {
        $user = Auth::user();
        
        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($user),
            'member_since' => $user->created_at->format('M d, Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
            'total_logins' => $user->login_count ?? 0,
        ];

        return response()->json($stats);
    }

    private function calculateProfileCompletion(User $user): int
    {
        $profile = $user->profile;
        $totalFields = 15; // Total number of profile fields
        $completedFields = 0;

        // Basic user fields
        if ($user->name) $completedFields++;
        if ($user->phone) $completedFields++;
        if ($user->email_verified_at) $completedFields++;

        // Profile fields
        if ($profile) {
            if ($profile->bio) $completedFields++;
            if ($profile->date_of_birth) $completedFields++;
            if ($profile->gender) $completedFields++;
            if ($profile->address) $completedFields++;
            if ($profile->city) $completedFields++;
            if ($profile->state) $completedFields++;
            if ($profile->country) $completedFields++;
            if ($profile->postal_code) $completedFields++;
            if ($profile->website) $completedFields++;
            if ($profile->linkedin) $completedFields++;
            if ($profile->facebook) $completedFields++;
            if ($profile->twitter) $completedFields++;
            if ($profile->instagram) $completedFields++;
            if ($profile->avatar) $completedFields++;
        }

        return round(($completedFields / $totalFields) * 100);
    }

    public function publicProfile(User $user)
    {
        $user->load(['profile', 'properties' => function ($query) {
            $query->where('status', 'published')->latest()->limit(6);
        }]);

        return view('user.public-profile', compact('user'));
    }
}
