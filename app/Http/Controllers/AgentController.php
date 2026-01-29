<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agent\StoreAgentRequest;
use App\Http\Requests\Agent\UpdateAgentRequest;
use App\Models\Agent;
use App\Models\AgentProfile;
use App\Models\User;
use App\Models\UserActivityLog;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\AgentService;

class AgentController extends Controller
{
    protected $agentService;

    public function __construct(AgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    public function directory(Request $request)
    {
        $filters = $request->only(['search', 'specialization', 'location', 'rating']);
        $agents = $this->agentService->getAgentsPaginated($filters, 12, true);
        $specializations = $this->agentService->getAgentSpecializations();

        return view('agents.directory', compact('agents', 'specializations'));
    }

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'status', 'specialization', 'company_id']);
        $agents = $this->agentService->getAgentsPaginated($filters, 20, false);

        return view('agents.index', compact('agents'));
    }

    public function show(Agent $agent)
    {
        $data = $this->agentService->getAgentDetails($agent);
        return view('agents.show', $data);
    }

    public function create()
    {
        $companies = Company::where('status', 'active')->get(['id', 'name']);
        return view('agents.create', compact('companies'));
    }

    public function store(StoreAgentRequest $request)
    {
        // Debug: Log request data
        \Log::info('Agent creation attempt', [
            'request_data' => $request->all(),
            'files' => $request->hasFile('profile_photo') ? 'has_photo' : 'no_photo'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Create or find user
            $nameParts = explode(' ', $request->name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';
            
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'username' => strtolower(str_replace(' ', '', $request->name)) . '_' . time(),
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'full_name' => $request->name,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->password ?? 'password'),
                    'email_verified_at' => now(),
                    'user_type' => 'agent',
                    'account_status' => 'active',
                ]
            );

            // Create agent record
            $agent = Agent::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'company_id' => $request->company_id,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'status' => $request->status ?? 'active',
                'commission_rate' => $request->commission_rate,
                'hire_date' => now(),
                'created_by' => Auth::id(),
            ]);

            $this->agentService->invalidateCache($agent->id);

            // Create agent profile
            $profileData = [
                'about_me' => $request->bio,
                'specializations' => $request->specializations ?? [],
                'languages' => $request->languages ?? [],
                'service_areas' => $request->service_areas ?? [],
                'achievements' => $request->achievements ?? [],
                'education' => $request->education ?? [],
                'certifications' => $request->certifications ?? [],
                'social_media' => $request->social_links ?? [],
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'working_hours' => $request->working_hours,
                'phone' => $request->phone,
                'email' => $request->email,
            ];

            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $path = $photo->store('agent-photos', 'public');
                $profileData['photo'] = $path;
            }

            $agent->profile()->create($profileData);

            // Create activity log using available fields
            \DB::table('user_activities')->insert([
                'user_id' => Auth::id(),
                'session_id' => session()->getId() ?? 'web_' . Str::random(40),
                'activity_type' => 'agent_management',
                'activity_category' => 'agent_creation',
                'activity_description' => "Created agent: {$user->full_name}",
                'ip_address' => $request->ip(),
                'method' => 'POST',
                'url' => $request->path(),
                'full_url' => $request->fullUrl(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();

            // Send notification to admin users and current user
            try {
                // Get admin users
                $adminUsers = \App\Models\User::where('role', 'admin')->orWhere('role', 'manager')->get();
                
                // Add current user to notifications
                $allUsers = $adminUsers->push(auth()->user());
                
                foreach ($allUsers as $user) {
                    $user->notify(new \App\Notifications\AgentCreated($agent));
                }
            } catch (\Exception $e) {
                // Continue even if notification fails
            }

            // Check if request is AJAX
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Agent created successfully.',
                    'agent' => [
                        'id' => $agent->id,
                        'name' => $agent->name,
                        'email' => $agent->email,
                        'phone' => $agent->phone,
                        'license_number' => $agent->license_number,
                    ]
                ]);
            }

            return redirect()->route('agents.show', $agent)
                ->with('success', 'Agent created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            // Debug: Log error details
            \Log::error('Agent creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create agent: ' . $e->getMessage());
        }
    }

    public function edit(Agent $agent)
    {
        $this->authorize('update', $agent);
        
        $agent->load(['profile', 'user']);
        $companies = Company::where('status', 'active')->get(['id', 'name']);
        
        return view('agents.edit', compact('agent', 'companies'));
    }

    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        $this->authorize('update', $agent);
        
        DB::beginTransaction();
        
        try {
            // Update user
            $agent->user->update([
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            // Update agent
            $agent->update([
                'company_id' => $request->company_id,
                'license_number' => $request->license_number,
                'experience_years' => $request->experience_years,
                'status' => $request->status,
                'commission_rate' => $request->commission_rate,
            ]);

            $this->agentService->invalidateCache($agent->id);

            // Update profile
            $profileData = [
                'about_me' => $request->bio,
                'specializations' => $request->specializations ?? [],
                'languages' => $request->languages ?? [],
                'service_areas' => $request->service_areas ?? [],
                'achievements' => $request->achievements ?? [],
                'education' => $request->education ?? [],
                'certifications' => $request->certifications ?? [],
                'social_media' => $request->social_links ?? [],
                'office_address' => $request->office_address,
                'office_phone' => $request->office_phone,
                'working_hours' => $request->working_hours,
            ];

            if ($request->hasFile('profile_photo')) {
                if ($agent->profile && $agent->profile->photo) {
                    Storage::disk('public')->delete($agent->profile->photo);
                }
                
                $photo = $request->file('profile_photo');
                $path = $photo->store('agent-photos', 'public');
                $profileData['photo'] = $path;
            }

            $agent->profile()->updateOrCreate(['agent_id' => $agent->id], $profileData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'updated_agent',
                'details' => "Updated agent: {$agent->user->name}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('agents.show', $agent)
                ->with('success', 'Agent updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update agent: ' . $e->getMessage());
        }
    }

    public function destroy(Agent $agent)
    {
        $this->authorize('delete', $agent);
        
        $agentName = $agent->user->name;
        
        if ($agent->profile && $agent->profile->photo) {
            Storage::disk('public')->delete($agent->profile->photo);
        }
        
        $agent->delete();
        $this->agentService->invalidateCache($agent->id);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'deleted_agent',
            'details' => "Deleted agent: {$agentName}",
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('agents.index')
            ->with('success', 'Agent deleted successfully.');
    }

    public function toggleStatus(Agent $agent): JsonResponse
    {
        $this->authorize('update', $agent);
        
        $newStatus = $agent->status === 'active' ? 'inactive' : 'active';
        $agent->update(['status' => $newStatus]);
        $this->agentService->invalidateCache($agent->id);

        UserActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggled_agent_status',
            'details' => "Toggled agent {$agent->user->name} status to {$newStatus}",
            'ip_address' => request()->ip(),
        ]);

        return response()->json([
            'success' => true,
            'status' => $newStatus,
            'message' => "Agent status changed to {$newStatus}"
        ]);
    }

    public function getAgents(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'company_id']);
        $agents = $this->agentService->getFilteredAgents($filters);

        return response()->json([
            'success' => true,
            'agents' => $agents
        ]);
    }

    public function getAgentStats(Agent $agent): JsonResponse
    {
        $this->authorize('view', $agent);
        
        $stats = $this->agentService->getAgentStatistics($agent);

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $this->authorize('bulkDelete', Agent::class);
        
        $agentIds = $request->input('agents', []);
        
        DB::beginTransaction();
        
        try {
            $agents = Agent::whereIn('id', $agentIds)->get();
            
            foreach ($agents as $agent) {
                if ($agent->profile && $agent->profile->profile_photo) {
                    Storage::disk('public')->delete($agent->profile->profile_photo);
                }
                $agent->delete();
                $this->agentService->invalidateCache($agent->id);
            }

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'bulk_deleted_agents',
                'details' => "Bulk deleted " . count($agentIds) . " agents",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('agents.index')
                ->with('success', count($agentIds) . ' agents deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->with('error', 'Failed to delete agents: ' . $e->getMessage());
        }
    }

    public function publicProfile(Agent $agent)
    {
        $data = $this->agentService->getPublicAgentProfile($agent);
        return view('agents.public-profile', $data);
    }
}
