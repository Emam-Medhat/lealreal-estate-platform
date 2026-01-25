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

class AgentController extends Controller
{
    public function directory(Request $request)
    {
        $agents = Agent::with(['profile', 'user', 'company'])
            ->where('status', 'active')
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('profile', function ($q) use ($search) {
                    $q->where('license_number', 'like', "%{$search}%")
                      ->orWhere('bio', 'like', "%{$search}%");
                });
            })
            ->when($request->specialization, function ($query, $specialization) {
                $query->whereHas('profile', function ($q) use ($specialization) {
                    $q->whereJsonContains('specializations', $specialization);
                });
            })
            ->when($request->location, function ($query, $location) {
                $query->whereHas('profile', function ($q) use ($location) {
                    $q->whereJsonContains('service_areas', $location);
                });
            })
            ->when($request->rating, function ($query, $rating) {
                $query->whereHas('reviews', function ($q) use ($rating) {
                    $q->havingRaw('AVG(rating) >= ?', [$rating]);
                });
            })
            ->orderByRaw('(
                SELECT AVG(rating) FROM agent_reviews WHERE agent_id = agents.id
            ) DESC')
            ->paginate(12);

        // Get available specializations for filters
        $specializations = AgentProfile::whereNotNull('specializations')
            ->get()
            ->flatMap(function ($profile) {
                return $profile->specializations ?? [];
            })
            ->unique()
            ->sort()
            ->values();

        return view('agents.directory', compact('agents', 'specializations'));
    }

    public function index(Request $request)
    {
        $agents = Agent::with(['profile', 'user', 'company'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('profile', function ($q) use ($search) {
                    $q->where('license_number', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->specialization, function ($query, $specialization) {
                $query->whereHas('profile', function ($q) use ($specialization) {
                    $q->whereJsonContains('specializations', $specialization);
                });
            })
            ->when($request->company_id, function ($query, $companyId) {
                $query->where('company_id', $companyId);
            })
            ->latest()
            ->paginate(20);

        return view('agents.index', compact('agents'));
    }

    public function show(Agent $agent)
    {
        $agent->load(['profile', 'user', 'company', 'properties' => function ($query) {
            $query->latest()->limit(10);
        }, 'reviews' => function ($query) {
            $query->latest()->limit(5);
        }]);

        // Calculate agent statistics
        $stats = [
            'total_properties' => $agent->properties()->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
            'total_reviews' => $agent->reviews()->count(),
            'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
        ];

        return view('agents.show', compact('agent', 'stats'));
    }

    public function create()
    {
        $companies = Company::where('status', 'active')->get(['id', 'name']);
        return view('agents.create', compact('companies'));
    }

    public function store(StoreAgentRequest $request)
    {
        DB::beginTransaction();
        
        try {
            // Create or find user
            $user = User::firstOrCreate(
                ['email' => $request->email],
                [
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->password ?? 'password'),
                    'email_verified_at' => now(),
                ]
            );

            // Create agent record
            $agent = Agent::create([
                'user_id' => $user->id,
                'company_id' => $request->company_id,
                'license_number' => $request->license_number,
                'status' => $request->status ?? 'active',
                'commission_rate' => $request->commission_rate,
                'created_by' => Auth::id(),
            ]);

            // Create agent profile
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
            ];

            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $path = $photo->store('agent-photos', 'public');
                $profileData['profile_photo'] = $path;
            }

            $agent->profile()->create($profileData);

            UserActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'created_agent',
                'details' => "Created agent: {$user->name}",
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            return redirect()->route('agents.show', $agent)
                ->with('success', 'Agent created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
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
                'status' => $request->status,
                'commission_rate' => $request->commission_rate,
            ]);

            // Update profile
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
            ];

            if ($request->hasFile('profile_photo')) {
                if ($agent->profile && $agent->profile->profile_photo) {
                    Storage::disk('public')->delete($agent->profile->profile_photo);
                }
                
                $photo = $request->file('profile_photo');
                $path = $photo->store('agent-photos', 'public');
                $profileData['profile_photo'] = $path;
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
        
        if ($agent->profile && $agent->profile->profile_photo) {
            Storage::disk('public')->delete($agent->profile->profile_photo);
        }
        
        $agent->delete();

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
        $agents = Agent::with(['user', 'company'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($request->company_id, function ($query, $companyId) {
                $query->where('company_id', $companyId);
            })
            ->where('status', 'active')
            ->get(['id', 'user_id', 'company_id', 'license_number']);

        return response()->json([
            'success' => true,
            'agents' => $agents
        ]);
    }

    public function getAgentStats(Agent $agent): JsonResponse
    {
        $this->authorize('view', $agent);
        
        $stats = [
            'total_properties' => $agent->properties()->count(),
            'active_listings' => $agent->properties()->where('status', 'published')->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'total_reviews' => $agent->reviews()->count(),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
            'total_commissions' => $agent->commissions()->sum('amount'),
            'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
            'member_since' => $agent->created_at->format('M d, Y'),
        ];

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
        $agent->load(['profile', 'company', 'properties' => function ($query) {
            $query->where('status', 'published')->latest()->limit(9);
        }, 'reviews' => function ($query) {
            $query->latest()->limit(10);
        }]);

        $stats = [
            'total_properties' => $agent->properties()->count(),
            'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
            'average_rating' => $agent->reviews()->avg('rating') ?? 0,
            'total_reviews' => $agent->reviews()->count(),
            'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
        ];

        return view('agents.public-profile', compact('agent', 'stats'));
    }
}
