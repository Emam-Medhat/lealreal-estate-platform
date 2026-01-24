<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateAgentProfileRequest;
use App\Models\Agent;
use App\Models\AgentProfile;
use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentProfileController extends Controller
{
    public function show()
    {
        $agent = Auth::user()->agent;
        $agent->load(['profile', 'certifications', 'licenses', 'reviews' => function ($query) {
            $query->latest()->limit(5);
        }]);

        return view('agent.profile.show', compact('agent'));
    }

    public function edit()
    {
        $agent = Auth::user()->agent;
        $agent->load('profile');
        
        return view('agent.profile.edit', compact('agent'));
    }

    public function update(UpdateAgentProfileRequest $request)
    {
        $agent = Auth::user()->agent;
        
        $profileData = [
            'bio' => $request->bio,
            'experience_years' => $request->experience_years,
            'specializations' => $request->specializations ?? [],
            'languages' => $request->languages ?? [],
            'service_areas' => $request->service_areas ?? [],
            'achievements' => $request->achievements ?? [],
            'education' => $request->education ?? [],
            'certifications' => $request->certifications ?? [],
            'social_links' => $request->social_links ?? [],
            'office_address' => $request->office_address,
            'office_phone' => $request->office_phone,
            'working_hours' => $request->working_hours,
            'commission_structure' => $request->commission_structure,
            'specialties' => $request->specialties ?? [],
            'target_markets' => $request->target_markets ?? [],
            'professional_summary' => $request->professional_summary,
            'career_highlights' => $request->career_highlights ?? [],
            'client_testimonials' => $request->client_testimonials ?? [],
            'awards_recognitions' => $request->awards_recognitions ?? [],
            'professional_associations' => $request->professional_associations ?? [],
            'continuing_education' => $request->continuing_education ?? [],
        ];

        if ($request->hasFile('profile_photo')) {
            if ($agent->profile && $agent->profile->profile_photo) {
                Storage::disk('public')->delete($agent->profile->profile_photo);
            }
            
            $photo = $request->file('profile_photo');
            $path = $photo->store('agent-photos', 'public');
            $profileData['profile_photo'] = $path;
        }

        if ($request->hasFile('cover_image')) {
            if ($agent->profile && $agent->profile->cover_image) {
                Storage::disk('public')->delete($agent->profile->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $path = $cover->store('agent-covers', 'public');
            $profileData['cover_image'] = $path;
        }

        $agent->profile()->updateOrCreate(['agent_id' => $agent->id], $profileData);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'updated_agent_profile',
            'details' => "Updated agent profile information",
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('agent.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $agent = Auth::user()->agent;
        
        if ($request->hasFile('photo')) {
            if ($agent->profile && $agent->profile->profile_photo) {
                Storage::disk('public')->delete($agent->profile->profile_photo);
            }
            
            $photo = $request->file('photo');
            $path = $photo->store('agent-photos', 'public');
            
            $agent->profile()->updateOrCreate(['agent_id' => $agent->id], [
                'profile_photo' => $path
            ]);

            return response()->json([
                'success' => true,
                'photo_url' => asset('storage/' . $path),
                'message' => 'Photo uploaded successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No photo uploaded'
        ]);
    }

    public function removePhoto(): JsonResponse
    {
        $agent = Auth::user()->agent;
        
        if ($agent->profile && $agent->profile->profile_photo) {
            Storage::disk('public')->delete($agent->profile->profile_photo);
            $agent->profile->update(['profile_photo' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo removed successfully'
        ]);
    }

    public function getProfileStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        $agent->load(['properties', 'reviews', 'commissions']);

        $stats = [
            'total_properties' => $agent->properties()->count(),
            'active_properties' => $agent->properties()->where('status', 'active')->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'total_reviews' => $agent->reviews()->count(),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
            'total_commissions' => $agent->commissions()->sum('amount'),
            'this_month_commissions' => $agent->commissions()
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'profile_completion' => $this->calculateProfileCompletion($agent),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    private function calculateProfileCompletion(Agent $agent): int
    {
        if (!$agent->profile) {
            return 0;
        }

        $fields = [
            'bio', 'experience_years', 'specializations', 'languages',
            'service_areas', 'office_address', 'office_phone', 'working_hours',
            'profile_photo', 'cover_image', 'professional_summary'
        ];

        $completed = 0;
        $total = count($fields);

        foreach ($fields as $field) {
            if ($agent->profile->$field && (!is_array($agent->profile->$field) || !empty($agent->profile->$field))) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    public function publicProfile(Agent $agent)
    {
        $agent->load([
            'profile', 
            'properties' => function ($query) {
                $query->where('status', 'active')->latest()->limit(6);
            },
            'reviews' => function ($query) {
                $query->latest()->limit(10);
            },
            'certifications',
            'licenses'
        ]);

        $stats = [
            'total_properties' => $agent->properties()->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
            'total_reviews' => $agent->reviews()->count(),
            'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
        ];

        return view('agent.profile.public', compact('agent', 'stats'));
    }
}
