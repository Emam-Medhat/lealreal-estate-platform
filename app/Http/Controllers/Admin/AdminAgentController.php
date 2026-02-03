<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class AdminAgentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request): View
    {
        $agents = User::where('role', 'agent')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(20);

        return view('admin.agents.index', compact('agents'));
    }

    public function create(): View
    {
        return view('admin.agents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // Basic Information
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'telegram' => 'nullable|string|max:100',
            'password' => 'required|string|min:8|confirmed',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'website' => 'nullable|url',
            
            // Agent Information
            'agent_license_number' => 'nullable|string|max:100',
            'agent_license_expiry' => 'nullable|date',
            'agent_company' => 'nullable|string|max:255',
            'agent_commission_rate' => 'nullable|numeric|min:0|max:100',
            'average_response_time' => 'nullable|numeric|min:0',
            'client_satisfaction_rate' => 'nullable|numeric|min:0|max:9.99',
            'agent_specializations' => 'nullable|array',
            'agent_specializations.*' => 'string',
            'agent_service_areas' => 'nullable|array',
            'agent_service_areas.*' => 'string',
            'agent_bio' => 'nullable|string',
            
            // Location Information
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'address' => 'nullable|string',
            'postal_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            
            // Social Media
            'facebook_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'youtube_url' => 'nullable|url',
            
            // Account Settings
            'account_status' => 'required|in:pending_verification,active,inactive,suspended',
            'kyc_status' => 'required|in:not_submitted,pending,verified,rejected',
            'language' => 'required|string|max:5',
            'currency' => 'required|string|max:3',
            'wallet_balance' => 'nullable|numeric|min:0',
            'referral_code' => 'nullable|string|max:8|unique:users,referral_code',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $avatarPath;
        }

        // Generate full name
        $validated['full_name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        // Hash password
        $validated['password'] = bcrypt($validated['password']);

        // Set agent-specific fields
        $validated['is_agent'] = true;
        $validated['user_type'] = 'agent';

        // Generate referral code if not provided
        if (empty($validated['referral_code'])) {
            $validated['referral_code'] = $this->generateUniqueReferralCode();
        }

        // Create the agent
        $agent = User::create($validated);

        return redirect()
            ->route('admin.agents.show', $agent)
            ->with('success', 'تم إضافة الوكيل بنجاح');
    }

    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    public function show(User $agent): View
    {
        // Load agent with all necessary data
        $agent->load([
            'properties' => function($query) {
                $query->latest()->limit(10);
            },
            'reviews' => function($query) {
                $query->latest()->limit(5);
            },
            'company',
            'developer'
        ]);

        // Calculate real-time statistics if needed
        if ($agent->is_agent) {
            $agent->properties_count = $agent->properties()->count();
            $agent->reviews_count = $agent->reviews()->count();
            $agent->properties_views_count = $agent->properties()->sum('views_count') ?? 0;
        }

        return view('admin.agents.show', compact('agent'));
    }

    public function edit(User $agent): View
    {
        return view('admin.agents.edit', compact('agent'));
    }

    public function update(Request $request, User $agent)
    {
        // Implementation for updating agent
        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent updated successfully');
    }

    public function destroy(User $agent)
    {
        $agent->delete();
        return redirect()->route('admin.agents.index')
            ->with('success', 'Agent deleted successfully');
    }

    public function toggleStatus(Request $request, User $agent): JsonResponse
    {
        $agent->update(['status' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => 'Agent status updated successfully'
        ]);
    }
}
