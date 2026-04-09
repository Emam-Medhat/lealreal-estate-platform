<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Http\Requests\Agent\UpdateAgentProfileRequest;
use App\Models\Agent;
use App\Models\AgentProfile;
use App\Models\UserActivityLog;
use App\Services\AgentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AgentProfileController extends Controller
{
    protected $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function show()
    {
        $agent = Auth::user()->agent;
        $agent = $this->agentService->getAgentProfile($agent);

        return view('agent.profile.show', compact('agent'));
    }

    public function edit()
    {
        $agent = Auth::user()->agent;
        $agent = $this->agentService->getAgentProfile($agent);
        
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

        $this->agentService->updateAgentProfile(
            $agent,
            $profileData,
            $request->file('profile_photo'),
            $request->file('cover_image')
        );

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
        
        $photoUrl = $this->agentService->uploadAgentPhoto($agent, $request->file('photo'));

        return response()->json([
            'success' => true,
            'photo_url' => $photoUrl,
            'message' => 'Photo uploaded successfully'
        ]);
    }

    public function removePhoto(): JsonResponse
    {
        $agent = Auth::user()->agent;
        $this->agentService->removeAgentPhoto($agent);

        return response()->json([
            'success' => true,
            'message' => 'Photo removed successfully'
        ]);
    }

    public function getProfileStats(): JsonResponse
    {
        $agent = Auth::user()->agent;
        $stats = $this->agentService->getAgentStats($agent);
        $stats['profile_completion'] = $this->calculateProfileCompletion($agent);

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
        $data = $this->agentService->getPublicAgentProfile($agent);
        return view('agent.profile.public', $data);
    }
}
