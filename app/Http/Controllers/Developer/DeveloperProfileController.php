<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Developer\UpdateProfileRequest;
use App\Models\Developer;
use App\Models\DeveloperProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DeveloperProfileController extends Controller
{
    public function show(Developer $developer)
    {
        $profile = $developer->profile ?? $developer->profile()->create([
            'description' => '',
            'mission' => '',
            'vision' => '',
            'values' => [],
            'specializations' => [],
            'services' => [],
            'awards' => [],
            'certifications' => [],
            'social_links' => [],
            'contact_info' => [],
            'business_hours' => [],
        ]);

        return view('developer.profile.show', compact('developer', 'profile'));
    }

    public function edit(Developer $developer)
    {
        $profile = $developer->profile ?? $developer->profile()->create([
            'description' => '',
            'mission' => '',
            'vision' => '',
            'values' => [],
            'specializations' => [],
            'services' => [],
            'awards' => [],
            'certifications' => [],
            'social_links' => [],
            'contact_info' => [],
            'business_hours' => [],
        ]);

        return view('developer.profile.edit', compact('developer', 'profile'));
    }

    public function update(UpdateProfileRequest $request, Developer $developer)
    {
        $profile = $developer->profile ?? $developer->profile()->create();

        $profileData = [
            'description' => $request->description,
            'mission' => $request->mission,
            'vision' => $request->vision,
            'values' => $request->values ?? [],
            'specializations' => $request->specializations ?? [],
            'services' => $request->services ?? [],
            'awards' => $request->awards ?? [],
            'certifications' => $request->certifications ?? [],
            'social_links' => $request->social_links ?? [],
            'contact_info' => $request->contact_info ?? [],
            'business_hours' => $request->business_hours ?? [],
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            $profileData['logo'] = $request->file('logo')->store('developer-logos', 'public');
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            if ($profile->cover_image) {
                Storage::disk('public')->delete($profile->cover_image);
            }
            $profileData['cover_image'] = $request->file('cover_image')->store('developer-covers', 'public');
        }

        $profile->update($profileData);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_profile',
            'details' => "Updated profile for developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('developer.profile.show', $developer)
            ->with('success', 'Profile updated successfully.');
    }

    public function updateLogo(Request $request, Developer $developer): JsonResponse
    {
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $profile = $developer->profile ?? $developer->profile()->create();

        if ($profile->logo) {
            Storage::disk('public')->delete($profile->logo);
        }

        $logoPath = $request->file('logo')->store('developer-logos', 'public');
        $profile->update(['logo' => $logoPath]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_logo',
            'details' => "Updated logo for developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'logo_url' => asset("storage/{$logoPath}"),
            'message' => 'Logo updated successfully'
        ]);
    }

    public function updateCoverImage(Request $request, Developer $developer): JsonResponse
    {
        $request->validate([
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $profile = $developer->profile ?? $developer->profile()->create();

        if ($profile->cover_image) {
            Storage::disk('public')->delete($profile->cover_image);
        }

        $coverPath = $request->file('cover_image')->store('developer-covers', 'public');
        $profile->update(['cover_image' => $coverPath]);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_developer_cover',
            'details' => "Updated cover image for developer: {$developer->company_name}",
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'cover_url' => asset("storage/{$coverPath}"),
            'message' => 'Cover image updated successfully'
        ]);
    }

    public function getProfileStats(Developer $developer): JsonResponse
    {
        $profile = $developer->profile;
        
        $stats = [
            'profile_completion' => $profile ? $this->calculateProfileCompletion($profile) : 0,
            'has_logo' => $profile ? !is_null($profile->logo) : false,
            'has_cover' => $profile ? !is_null($profile->cover_image) : false,
            'specializations_count' => $profile ? count($profile->specializations ?? []) : 0,
            'services_count' => $profile ? count($profile->services ?? []) : 0,
            'awards_count' => $profile ? count($profile->awards ?? []) : 0,
            'certifications_count' => $profile ? count($profile->certifications ?? []) : 0,
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function calculateProfileCompletion(DeveloperProfile $profile): float
    {
        $fields = [
            'description',
            'mission',
            'vision',
            'specializations',
            'services',
            'social_links',
            'contact_info',
            'business_hours',
        ];

        $completed = 0;
        $total = count($fields) + 2; // +2 for logo and cover image

        foreach ($fields as $field) {
            $value = $profile->$field;
            if (is_array($value)) {
                if (!empty($value)) $completed++;
            } elseif (!empty($value)) {
                $completed++;
            }
        }

        if ($profile->logo) $completed++;
        if ($profile->cover_image) $completed++;

        return round(($completed / $total) * 100, 2);
    }
}
