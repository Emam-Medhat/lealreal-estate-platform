<?php

namespace App\Http\Controllers\Investor;

use App\Http\Controllers\Controller;
use App\Models\Investor;
use App\Models\InvestorProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class InvestorProfileController extends Controller
{
    public function show(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $profile = $investor->profile ?? new InvestorProfile();
        
        return view('investor.profile.show', compact('investor', 'profile'));
    }

    public function edit(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $profile = $investor->profile ?? new InvestorProfile();
        
        return view('investor.profile.edit', compact('investor', 'profile'));
    }

    public function update(Request $request)
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'bio' => 'nullable|string|max:2000',
            'professional_background' => 'nullable|string|max:2000',
            'investment_philosophy' => 'nullable|string|max:2000',
            'risk_appetite' => 'required|in:conservative,moderate,aggressive',
            'investment_horizon' => 'required|in:short_term,medium_term,long_term',
            'preferred_sectors' => 'nullable|array',
            'preferred_sectors.*' => 'string|max:100',
            'excluded_sectors' => 'nullable|array',
            'excluded_sectors.*' => 'string|max:100',
            'min_investment' => 'nullable|numeric|min:0',
            'max_investment' => 'nullable|numeric|min:0',
            'geographic_focus' => 'nullable|array',
            'geographic_focus.*' => 'string|max:100',
            'investment_criteria' => 'nullable|array',
            'investment_criteria.*' => 'string|max:500',
            'achievements' => 'nullable|array',
            'achievements.*' => 'string|max:500',
            'certifications' => 'nullable|array',
            'certifications.*.name' => 'required|string|max:255',
            'certifications.*.issuer' => 'required|string|max:255',
            'certifications.*.date' => 'required|date',
            'education' => 'nullable|array',
            'education.*.degree' => 'required|string|max:255',
            'education.*.institution' => 'required|string|max:255',
            'education.*.year' => 'required|integer|min:1900|max:' . date('Y'),
            'social_links' => 'nullable|array',
            'social_links.*.platform' => 'required|string|max:100',
            'social_links.*.url' => 'required|url|max:500',
            'contact_preferences' => 'nullable|array',
            'contact_preferences.email' => 'nullable|boolean',
            'contact_preferences.phone' => 'nullable|boolean',
            'contact_preferences.newsletter' => 'nullable|boolean',
        ]);

        $profileData = [
            'investor_id' => $investor->id,
            'bio' => $request->bio,
            'professional_background' => $request->professional_background,
            'investment_philosophy' => $request->investment_philosophy,
            'risk_appetite' => $request->risk_appetite,
            'investment_horizon' => $request->investment_horizon,
            'preferred_sectors' => $request->preferred_sectors ?? [],
            'excluded_sectors' => $request->excluded_sectors ?? [],
            'min_investment' => $request->min_investment,
            'max_investment' => $request->max_investment,
            'geographic_focus' => $request->geographic_focus ?? [],
            'investment_criteria' => $request->investment_criteria ?? [],
            'achievements' => $request->achievements ?? [],
            'certifications' => $request->certifications ?? [],
            'education' => $request->education ?? [],
            'social_links' => $request->social_links ?? [],
            'contact_preferences' => $request->contact_preferences ?? [],
            'updated_by' => Auth::id(),
        ];

        $profile = $investor->profile()->updateOrCreate(['investor_id' => $investor->id], $profileData);

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            if ($profile->profile_picture) {
                Storage::disk('public')->delete($profile->profile_picture);
            }
            $picturePath = $request->file('profile_picture')->store('investor-profiles', 'public');
            $profile->update(['profile_picture' => $picturePath]);
        }

        // Handle resume upload
        if ($request->hasFile('resume')) {
            if ($profile->resume) {
                Storage::disk('public')->delete($profile->resume);
            }
            $resumePath = $request->file('resume')->store('investor-resumes', 'public');
            $profile->update(['resume' => $resumePath]);
        }

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_investor_profile',
            'details' => "Updated investor profile for {$investor->first_name} {$investor->last_name}",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('investor.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePicture(Request $request): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $profile = $investor->profile ?? new InvestorProfile();

        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($profile->profile_picture) {
            Storage::disk('public')->delete($profile->profile_picture);
        }

        $picturePath = $request->file('profile_picture')->store('investor-profiles', 'public');
        $profile->update(['profile_picture' => $picturePath]);

        return response()->json([
            'success' => true,
            'profile_picture' => asset('storage/' . $picturePath),
            'message' => 'Profile picture updated successfully'
        ]);
    }

    public function deleteProfilePicture(Request $request): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $profile = $investor->profile ?? new InvestorProfile();

        if ($profile->profile_picture) {
            Storage::disk('public')->delete($profile->profile_picture);
            $profile->update(['profile_picture' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile picture deleted successfully'
        ]);
    }

    public function getProfileCompletion(): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $profile = $investor->profile ?? new InvestorProfile();

        $fields = [
            'bio' => !empty($profile->bio),
            'professional_background' => !empty($profile->professional_background),
            'investment_philosophy' => !empty($profile->investment_philosophy),
            'risk_appetite' => !empty($profile->risk_appetite),
            'investment_horizon' => !empty($profile->investment_horizon),
            'preferred_sectors' => !empty($profile->preferred_sectors),
            'geographic_focus' => !empty($profile->geographic_focus),
            'investment_criteria' => !empty($profile->investment_criteria),
            'profile_picture' => !empty($profile->profile_picture),
            'social_links' => !empty($profile->social_links),
        ];

        $completed = count(array_filter($fields));
        $total = count($fields);
        $percentage = $total > 0 ? round(($completed / $total) * 100, 2) : 0;

        return response()->json([
            'success' => true,
            'completion_percentage' => $percentage,
            'completed_fields' => $completed,
            'total_fields' => $total,
            'fields' => $fields,
        ]);
    }

    public function getPublicProfile(Investor $investor): JsonResponse
    {
        $investor->load(['profile', 'portfolios']);

        $publicData = [
            'id' => $investor->id,
            'first_name' => $investor->first_name,
            'last_name' => $investor->last_name,
            'company_name' => $investor->company_name,
            'investor_type' => $investor->investor_type,
            'status' => $investor->status,
            'verification_status' => $investor->verification_status,
            'profile_picture' => $investor->profile->profile_picture ? asset('storage/' . $investor->profile->profile_picture) : null,
            'bio' => $investor->profile->bio ?? null,
            'investment_philosophy' => $investor->profile->investment_philosophy ?? null,
            'preferred_sectors' => $investor->profile->preferred_sectors ?? [],
            'experience_years' => $investor->experience_years,
            'total_invested' => $investor->total_invested,
            'total_returns' => $investor->total_returns,
            'portfolio_count' => $investor->portfolios->count(),
            'created_at' => $investor->created_at->format('Y-m-d'),
        ];

        return response()->json([
            'success' => true,
            'investor' => $publicData
        ]);
    }

    public function exportProfile(Request $request): JsonResponse
    {
        $investor = Investor::where('user_id', Auth::id())->firstOrFail();
        $profile = $investor->profile ?? new InvestorProfile();

        $exportData = [
            'personal_info' => [
                'first_name' => $investor->first_name,
                'last_name' => $investor->last_name,
                'email' => $investor->email,
                'phone' => $investor->phone,
                'company_name' => $investor->company_name,
            ],
            'profile' => [
                'bio' => $profile->bio,
                'professional_background' => $profile->professional_background,
                'investment_philosophy' => $profile->investment_philosophy,
                'risk_appetite' => $profile->risk_appetite,
                'investment_horizon' => $profile->investment_horizon,
                'preferred_sectors' => $profile->preferred_sectors,
                'excluded_sectors' => $profile->excluded_sectors,
                'min_investment' => $profile->min_investment,
                'max_investment' => $profile->max_investment,
                'geographic_focus' => $profile->geographic_focus,
                'investment_criteria' => $profile->investment_criteria,
                'achievements' => $profile->achievements,
                'certifications' => $profile->certifications,
                'education' => $profile->education,
                'social_links' => $profile->social_links,
            ],
            'investment_stats' => [
                'total_invested' => $investor->total_invested,
                'total_returns' => $investor->total_returns,
                'portfolio_count' => $investor->portfolios->count(),
                'experience_years' => $investor->experience_years,
            ],
        ];

        $filename = "investor_profile_export_" . now()->format('Y-m-d_H-i-s');

        return response()->json([
            'success' => true,
            'data' => $exportData,
            'filename' => $filename,
            'message' => 'Profile exported successfully'
        ]);
    }
}
