<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateProfileRequest;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserActivityLog;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class UserProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
        $user = $this->profileService->getUserProfile(Auth::id());
        
        return view('user.profile', compact('user'));
    }

    public function edit()
    {
        $user = $this->profileService->getUserProfile(Auth::id());
        
        return view('user.edit-profile', compact('user'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();
        
        $userData = [
            'first_name' => $request->first_name ?? $user->first_name,
            'last_name' => $request->last_name ?? $user->last_name,
            'phone' => $request->phone,
        ];

        $profileData = $request->only([
            'bio', 'date_of_birth', 'gender', 'address', 'city', 
            'state', 'country', 'postal_code', 'website', 
            'linkedin', 'facebook', 'twitter', 'instagram'
        ]);

        $this->profileService->updateProfile(
            $user, 
            $userData, 
            $profileData, 
            $request->file('avatar'), 
            $request->ip()
        );

        return redirect()->route('user.profile')
            ->with('success', 'Profile updated successfully.');
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();
        
        $success = $this->profileService->updateProfile(
            $user,
            [],
            [],
            $request->file('avatar'),
            $request->ip()
        );

        if ($success) {
            return response()->json([
                'success' => true,
                'avatar_url' => $user->avatar_url,
                'message' => 'Avatar uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Avatar upload failed'
        ], 500);
    }

    public function removeAvatar(): JsonResponse
    {
        $user = Auth::user();
        
        $this->profileService->updateProfile(
            $user,
            ['avatar' => null],
            ['avatar' => null],
            null,
            request()->ip()
        );

        return response()->json([
            'success' => true,
            'message' => 'Avatar removed successfully'
        ]);
    }

    public function getProfileStats(): JsonResponse
    {
        $user = Auth::user();
        
        $stats = [
            'profile_completion' => $this->profileService->calculateCompletion($user),
            'member_since' => $user->created_at->format('M d, Y'),
            'last_login' => $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never',
            'total_logins' => $user->login_count ?? 0,
        ];

        return response()->json($stats);
    }

    public function publicProfile(User $user)
    {
        $user = $this->profileService->getPublicProfile($user);

        return view('user.public-profile', compact('user'));
    }
}

