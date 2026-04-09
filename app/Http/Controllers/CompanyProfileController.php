<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CompanyProfileController extends Controller
{
    public function show(Company $company)
    {
        $company->load(['profile', 'members' => function ($query) {
            $query->where('status', 'active')->with('user.profile');
        }, 'branches', 'properties' => function ($query) {
            $query->where('status', 'published')->latest()->limit(6);
        }]);

        return view('company.profile', compact('company'));
    }

    public function edit(Company $company)
    {
        $this->authorize('update', $company);
        
        $company->load('profile');
        return view('company.edit-profile', compact('company'));
    }

    public function update(Request $request, Company $company)
    {
        $this->authorize('update', $company);
        
        $request->validate([
            'description' => 'nullable|string|max:2000',
            'founded_date' => 'nullable|date',
            'employee_count' => 'nullable|integer|min:1',
            'annual_revenue' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:500',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'services' => 'nullable|array',
            'specializations' => 'nullable|array',
            'certifications' => 'nullable|array',
            'awards' => 'nullable|array',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        $profileData = [
            'description' => $request->description,
            'founded_date' => $request->founded_date,
            'employee_count' => $request->employee_count,
            'annual_revenue' => $request->annual_revenue,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->postal_code,
            'services' => $request->services,
            'specializations' => $request->specializations,
            'certifications' => $request->certifications,
            'awards' => $request->awards,
        ];

        if ($request->hasFile('logo')) {
            if ($company->profile && $company->profile->logo) {
                Storage::disk('public')->delete($company->profile->logo);
            }
            
            $logo = $request->file('logo');
            $path = $logo->store('company-logos', 'public');
            $profileData['logo'] = $path;
        }

        if ($request->hasFile('cover_image')) {
            if ($company->profile && $company->profile->cover_image) {
                Storage::disk('public')->delete($company->profile->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $path = $cover->store('company-covers', 'public');
            $profileData['cover_image'] = $path;
        }

        $company->profile()->updateOrCreate(['company_id' => $company->id], $profileData);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_company_profile',
            'details' => "Updated profile for company: {$company->name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('company.profile.show', $company)
            ->with('success', 'Company profile updated successfully.');
    }

    public function uploadLogo(Request $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);
        
        $request->validate([
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            if ($company->profile && $company->profile->logo) {
                Storage::disk('public')->delete($company->profile->logo);
            }
            
            $logo = $request->file('logo');
            $path = $logo->store('company-logos', 'public');
            
            $company->profile()->updateOrCreate(['company_id' => $company->id], ['logo' => $path]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'uploaded_company_logo',
                'details' => "Updated logo for company: {$company->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'logo_url' => Storage::url($path),
                'message' => 'Logo uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No logo file provided'
        ], 400);
    }

    public function uploadCoverImage(Request $request, Company $company): JsonResponse
    {
        $this->authorize('update', $company);
        
        $request->validate([
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($company->profile && $company->profile->cover_image) {
                Storage::disk('public')->delete($company->profile->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $path = $cover->store('company-covers', 'public');
            
            $company->profile()->updateOrCreate(['company_id' => $company->id], ['cover_image' => $path]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'uploaded_company_cover',
                'details' => "Updated cover image for company: {$company->name}",
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'success' => true,
                'cover_url' => Storage::url($path),
                'message' => 'Cover image uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No cover image file provided'
        ], 400);
    }

    public function removeLogo(Company $company): JsonResponse
    {
        $this->authorize('update', $company);
        
        if ($company->profile && $company->profile->logo) {
            Storage::disk('public')->delete($company->profile->logo);
            $company->profile->update(['logo' => null]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'removed_company_logo',
                'details' => "Removed logo for company: {$company->name}",
                'ip_address' => request()->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Logo removed successfully'
        ]);
    }

    public function removeCoverImage(Company $company): JsonResponse
    {
        $this->authorize('update', $company);
        
        if ($company->profile && $company->profile->cover_image) {
            Storage::disk('public')->delete($company->profile->cover_image);
            $company->profile->update(['cover_image' => null]);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'removed_company_cover',
                'details' => "Removed cover image for company: {$company->name}",
                'ip_address' => request()->ip(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Cover image removed successfully'
        ]);
    }

    public function getProfileCompletion(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $profile = $company->profile;
        $totalFields = 12;
        $completedFields = 0;

        if ($profile) {
            if ($profile->description) $completedFields++;
            if ($profile->founded_date) $completedFields++;
            if ($profile->employee_count) $completedFields++;
            if ($profile->annual_revenue) $completedFields++;
            if ($profile->address) $completedFields++;
            if ($profile->city) $completedFields++;
            if ($profile->state) $completedFields++;
            if ($profile->country) $completedFields++;
            if ($profile->postal_code) $completedFields++;
            if ($profile->services) $completedFields++;
            if ($profile->specializations) $completedFields++;
            if ($profile->logo) $completedFields++;
        }

        $completionPercentage = round(($completedFields / $totalFields) * 100);

        return response()->json([
            'success' => true,
            'completion_percentage' => $completionPercentage,
            'completed_fields' => $completedFields,
            'total_fields' => $totalFields
        ]);
    }

    public function publicProfile(Company $company)
    {
        $company->load(['profile', 'properties' => function ($query) {
            $query->where('status', 'published')->latest()->limit(9);
        }]);

        return view('company.public-profile', compact('company'));
    }

    public function getProfileStats(Company $company): JsonResponse
    {
        $this->authorize('view', $company);
        
        $profile = $company->profile;
        
        $stats = [
            'profile_completion' => $this->calculateProfileCompletion($company),
            'has_logo' => $profile && $profile->logo ? true : false,
            'has_cover' => $profile && $profile->cover_image ? true : false,
            'has_description' => $profile && $profile->description ? true : false,
            'has_contact_info' => ($company->phone || $company->email) ? true : false,
            'has_address' => $profile && $profile->address ? true : false,
            'total_services' => $profile && $profile->services ? count($profile->services) : 0,
            'total_specializations' => $profile && $profile->specializations ? count($profile->specializations) : 0,
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function calculateProfileCompletion(Company $company): int
    {
        $profile = $company->profile;
        $totalFields = 12;
        $completedFields = 0;

        if ($profile) {
            if ($profile->description) $completedFields++;
            if ($profile->founded_date) $completedFields++;
            if ($profile->employee_count) $completedFields++;
            if ($profile->annual_revenue) $completedFields++;
            if ($profile->address) $completedFields++;
            if ($profile->city) $completedFields++;
            if ($profile->state) $completedFields++;
            if ($profile->country) $completedFields++;
            if ($profile->postal_code) $completedFields++;
            if ($profile->services) $completedFields++;
            if ($profile->specializations) $completedFields++;
            if ($profile->logo) $completedFields++;
        }

        return round(($completedFields / $totalFields) * 100);
    }
}
