<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\VirtualLand;
use App\Models\Metaverse\MetaverseShowroom;
use App\Models\Metaverse\MetaverseAvatar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class VirtualWorldController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of virtual worlds.
     */
    public function index(Request $request)
    {
        $query = VirtualWorld::with(['creator', 'properties', 'lands', 'showrooms'])
            ->withCount(['properties', 'lands', 'showrooms', 'avatars']);

        // Filters
        if ($request->filled('world_type')) {
            $query->where('world_type', $request->world_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('access_level')) {
            $query->where('access_level', $request->access_level);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('theme', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $worlds = $query->paginate(12);

        return Inertia::render('Metaverse/Worlds/Index', [
            'worlds' => $worlds,
            'filters' => $request->only(['world_type', 'status', 'access_level', 'is_active', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new virtual world.
     */
    public function create()
    {
        return Inertia::render('Metaverse/Worlds/Create');
    }

    /**
     * Store a newly created virtual world.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:virtual_worlds',
            'description' => 'required|string|max:2000',
            'world_type' => 'required|in:residential,commercial,mixed,gaming,educational,entertainment,social',
            'theme' => 'required|string|max:100',
            'access_level' => 'required|in:public,private,restricted,premium',
            'max_avatars' => 'required|integer|min:10|max:10000',
            'world_settings' => 'nullable|array',
            'environment_settings' => 'nullable|array',
            'physics_settings' => 'nullable|array',
            'graphics_settings' => 'nullable|array',
            'audio_settings' => 'nullable|array',
            'rules_guidelines' => 'nullable|array',
            'monetization_settings' => 'nullable|array',
            'moderation_settings' => 'nullable|array',
            'is_active' => 'boolean',
            'launch_date' => 'nullable|date|after_or_equal:today',
        ]);

        $world = VirtualWorld::create([
            'name' => $request->name,
            'description' => $request->description,
            'world_type' => $request->world_type,
            'theme' => $request->theme,
            'access_level' => $request->access_level,
            'max_avatars' => $request->max_avatars,
            'world_settings' => $request->world_settings ?? [
                'allow_building' => true,
                'allow_customization' => true,
                'enable_chat' => true,
                'enable_voice' => true,
                'enable_transactions' => true,
                'enable_events' => true,
            ],
            'environment_settings' => $request->environment_settings ?? [
                'weather_system' => true,
                'day_night_cycle' => true,
                'seasonal_changes' => false,
                'ambient_sounds' => true,
            ],
            'physics_settings' => $request->physics_settings ?? [
                'gravity' => 9.8,
                'collision_detection' => true,
                'physics_engine' => 'default',
            ],
            'graphics_settings' => $request->graphics_settings ?? [
                'render_quality' => 'high',
                'shadow_quality' => 'medium',
                'texture_quality' => 'high',
                'particle_effects' => true,
            ],
            'audio_settings' => $request->audio_settings ?? [
                'background_music' => true,
                'ambient_sounds' => true,
                'voice_chat' => true,
                'spatial_audio' => true,
            ],
            'rules_guidelines' => $request->rules_guidelines ?? [],
            'monetization_settings' => $request->monetization_settings ?? [
                'allow_transactions' => true,
                'transaction_fee' => 2.5,
                'currency' => 'ETH',
            ],
            'moderation_settings' => $request->moderation_settings ?? [
                'auto_moderation' => true,
                'report_system' => true,
                'banned_words_filter' => true,
            ],
            'status' => 'development',
            'is_active' => $request->boolean('is_active', false),
            'launch_date' => $request->launch_date,
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Handle world images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('metaverse/worlds/' . $world->id, 'public');
                $world->images()->create(['path' => $path]);
            }
        }

        // Handle world map
        if ($request->hasFile('world_map')) {
            $path = $request->file('world_map')->store('metaverse/world-maps/' . $world->id, 'public');
            $world->update(['world_map_path' => $path]);
        }

        return redirect()->route('metaverse.worlds.show', $world)
            ->with('success', 'تم إنشاء العالم الافتراضي بنجاح');
    }

    /**
     * Display the specified virtual world.
     */
    public function show(VirtualWorld $world)
    {
        $world->load([
            'creator',
            'images',
            'properties' => function ($query) {
                $query->where('status', 'active')->with(['owner', 'nft']);
            },
            'lands' => function ($query) {
                $query->where('status', 'active')->with(['owner']);
            },
            'showrooms' => function ($query) {
                $query->where('status', 'active')->where('is_active', true)->with(['owner']);
            },
            'avatars' => function ($query) {
                $query->where('is_online', true)->with(['user']);
            },
            'events' => function ($query) {
                $query->where('start_time', '>', now())->orderBy('start_time');
            },
        ]);

        // Get world statistics
        $stats = [
            'total_properties' => $world->properties_count,
            'total_lands' => $world->lands_count,
            'total_showrooms' => $world->showrooms_count,
            'online_avatars' => $world->avatars_count,
            'total_transactions' => $world->transactions()->count(),
            'average_property_value' => $world->properties()->avg('price') ?? 0,
            'world_capacity_usage' => $this->calculateCapacityUsage($world),
        ];

        // Get nearby worlds
        $nearbyWorlds = VirtualWorld::where('id', '!=', $world->id)
            ->where('world_type', $world->world_type)
            ->where('is_active', true)
            ->with(['creator'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Worlds/Show', [
            'world' => $world,
            'stats' => $stats,
            'nearbyWorlds' => $nearbyWorlds,
        ]);
    }

    /**
     * Show the form for editing the specified virtual world.
     */
    public function edit(VirtualWorld $world)
    {
        $this->authorize('update', $world);

        $world->load(['images']);

        return Inertia::render('Metaverse/Worlds/Edit', [
            'world' => $world,
        ]);
    }

    /**
     * Update the specified virtual world.
     */
    public function update(Request $request, VirtualWorld $world)
    {
        $this->authorize('update', $world);

        $request->validate([
            'name' => 'required|string|max:255|unique:virtual_worlds,name,' . $world->id,
            'description' => 'required|string|max:2000',
            'world_type' => 'required|in:residential,commercial,mixed,gaming,educational,entertainment,social',
            'theme' => 'required|string|max:100',
            'access_level' => 'required|in:public,private,restricted,premium',
            'max_avatars' => 'required|integer|min:10|max:10000',
            'world_settings' => 'nullable|array',
            'environment_settings' => 'nullable|array',
            'physics_settings' => 'nullable|array',
            'graphics_settings' => 'nullable|array',
            'audio_settings' => 'nullable|array',
            'rules_guidelines' => 'nullable|array',
            'monetization_settings' => 'nullable|array',
            'moderation_settings' => 'nullable|array',
            'is_active' => 'boolean',
            'launch_date' => 'nullable|date|after_or_equal:today',
        ]);

        $world->update([
            'name' => $request->name,
            'description' => $request->description,
            'world_type' => $request->world_type,
            'theme' => $request->theme,
            'access_level' => $request->access_level,
            'max_avatars' => $request->max_avatars,
            'world_settings' => $request->world_settings ?? $world->world_settings,
            'environment_settings' => $request->environment_settings ?? $world->environment_settings,
            'physics_settings' => $request->physics_settings ?? $world->physics_settings,
            'graphics_settings' => $request->graphics_settings ?? $world->graphics_settings,
            'audio_settings' => $request->audio_settings ?? $world->audio_settings,
            'rules_guidelines' => $request->rules_guidelines ?? $world->rules_guidelines,
            'monetization_settings' => $request->monetization_settings ?? $world->monetization_settings,
            'moderation_settings' => $request->moderation_settings ?? $world->moderation_settings,
            'is_active' => $request->boolean('is_active'),
            'launch_date' => $request->launch_date,
            'updated_by' => auth()->id(),
        ]);

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('metaverse/worlds/' . $world->id, 'public');
                $world->images()->create(['path' => $path]);
            }
        }

        // Handle new world map
        if ($request->hasFile('new_world_map')) {
            // Delete old map
            if ($world->world_map_path) {
                Storage::disk('public')->delete($world->world_map_path);
            }
            
            $path = $request->file('new_world_map')->store('metaverse/world-maps/' . $world->id, 'public');
            $world->update(['world_map_path' => $path]);
        }

        return redirect()->route('metaverse.worlds.show', $world)
            ->with('success', 'تم تحديث العالم الافتراضي بنجاح');
    }

    /**
     * Remove the specified virtual world.
     */
    public function destroy(VirtualWorld $world)
    {
        $this->authorize('delete', $world);

        // Delete associated files
        foreach ($world->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        if ($world->world_map_path) {
            Storage::disk('public')->delete($world->world_map_path);
        }

        $world->delete();

        return redirect()->route('metaverse.worlds.index')
            ->with('success', 'تم حذف العالم الافتراضي بنجاح');
    }

    /**
     * Enter virtual world.
     */
    public function enter(VirtualWorld $world)
    {
        $this->authorize('enter', $world);

        // Check access level
        if (!$this->checkAccess($world)) {
            return back()->with('error', 'ليس لديك صلاحية لدخول هذا العالم');
        }

        // Check capacity
        if ($world->current_avatar_count >= $world->max_avatars) {
            return back()->with('error', 'العالم ممتلئ حالياً');
        }

        // Check if user has avatar
        $avatar = auth()->user()->metaverseAvatar;
        if (!$avatar) {
            return back()->with('error', 'يجب إنشاء أفاتار أولاً');
        }

        // Create appearance record
        $appearance = $world->appearances()->create([
            'avatar_id' => $avatar->id,
            'user_id' => auth()->id(),
            'entered_at' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update avatar status
        $avatar->update([
            'is_online' => true,
            'current_world_id' => $world->id,
            'current_location' => $world->name,
            'last_active_at' => now(),
        ]);

        // Increment world avatar count
        $world->increment('current_avatar_count');

        return Inertia::render('Metaverse/Worlds/VirtualSpace', [
            'world' => $world->load(['properties', 'lands', 'showrooms']),
            'avatar' => $avatar,
            'appearance' => $appearance,
        ]);
    }

    /**
     * Exit virtual world.
     */
    public function exit(VirtualWorld $world)
    {
        $avatar = auth()->user()->metaverseAvatar;
        
        if (!$avatar) {
            return redirect()->route('metaverse.worlds.show', $world);
        }

        // Update appearance record
        $appearance = $world->appearances()
            ->where('avatar_id', $avatar->id)
            ->whereNull('exited_at')
            ->latest()
            ->first();

        if ($appearance) {
            $appearance->update([
                'exited_at' => now(),
                'duration' => now()->diffInSeconds($appearance->entered_at),
            ]);
        }

        // Update avatar status
        $avatar->update([
            'is_online' => false,
            'current_world_id' => null,
            'current_location' => null,
            'last_active_at' => now(),
        ]);

        // Decrement world avatar count
        $world->decrement('current_avatar_count');

        return redirect()->route('metaverse.worlds.show', $world);
    }

    /**
     * Get world analytics.
     */
    public function analytics(VirtualWorld $world)
    {
        $this->authorize('view', $world);

        $analytics = [
            'visitor_trends' => $world->appearances()
                ->selectRaw('DATE(entered_at) as date, COUNT(*) as visitors')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
            
            'peak_hours' => $world->appearances()
                ->selectRaw('HOUR(entered_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->limit(24)
                ->get(),
            
            'visitor_demographics' => [
                'by_country' => $world->appearances()
                    ->join('users', 'world_appearances.user_id', '=', 'users.id')
                    ->selectRaw('users.country, COUNT(*) as count')
                    ->groupBy('users.country')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            
            'engagement_metrics' => [
                'avg_visit_duration' => $world->appearances()->avg('duration') ?? 0,
                'return_visitor_rate' => $this->calculateReturnVisitorRate($world),
                'bounce_rate' => $this->calculateBounceRate($world),
            ],
            
            'economic_metrics' => [
                'total_transactions' => $world->transactions()->count(),
                'total_volume' => $world->transactions()->sum('amount'),
                'transaction_types' => $world->transactions()
                    ->selectRaw('type, COUNT(*) as count, SUM(amount) as volume')
                    ->groupBy('type')
                    ->get(),
            ],
            
            'property_metrics' => [
                'total_properties' => $world->properties()->count(),
                'average_property_value' => $world->properties()->avg('price') ?? 0,
                'property_types' => $world->properties()
                    ->selectRaw('property_type, COUNT(*) as count')
                    ->groupBy('property_type')
                    ->get(),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Get world map data.
     */
    public function map(VirtualWorld $world)
    {
        $mapData = [
            'world_info' => [
                'name' => $world->name,
                'dimensions' => $world->dimensions ?? ['width' => 1000, 'height' => 1000],
                'theme' => $world->theme,
            ],
            'properties' => $world->properties()
                ->where('status', 'active')
                ->select(['id', 'title', 'location_coordinates', 'property_type', 'price'])
                ->get(),
            'lands' => $world->lands()
                ->where('status', 'active')
                ->select(['id', 'title', 'coordinates', 'land_type', 'price'])
                ->get(),
            'showrooms' => $world->showrooms()
                ->where('status', 'active')
                ->where('is_active', true)
                ->select(['id', 'title', 'location_coordinates', 'showroom_type'])
                ->get(),
            'landmarks' => $world->landmarks ?? [],
            'zones' => $world->zones ?? [],
        ];

        return response()->json($mapData);
    }

    /**
     * Launch world.
     */
    public function launch(VirtualWorld $world)
    {
        $this->authorize('launch', $world);

        if ($world->status === 'active') {
            return back()->with('error', 'العorld نشط بالفعل');
        }

        $world->update([
            'status' => 'active',
            'launched_at' => now(),
            'is_active' => true,
        ]);

        return back()->with('success', 'تم إطلاق العالم بنجاح');
    }

    /**
     * Suspend world.
     */
    public function suspend(VirtualWorld $world)
    {
        $this->authorize('suspend', $world);

        $world->update([
            'status' => 'suspended',
            'is_active' => false,
            'suspended_at' => now(),
        ]);

        // Kick all online avatars
        $world->appearances()
            ->whereNull('exited_at')
            ->update([
                'exited_at' => now(),
                'duration' => \DB::raw('TIMESTAMPDIFF(SECOND, entered_at, NOW())'),
            ]);

        // Update avatar statuses
        $world->avatars()->update([
            'is_online' => false,
            'current_world_id' => null,
            'current_location' => null,
        ]);

        $world->update(['current_avatar_count' => 0]);

        return back()->with('success', 'تم إيقاف العالم بنجاح');
    }

    /**
     * Check user access to world.
     */
    private function checkAccess(VirtualWorld $world): bool
    {
        switch ($world->access_level) {
            case 'public':
                return true;
            case 'private':
                return $world->creator_id === auth()->id();
            case 'restricted':
                return $world->creator_id === auth()->id() || 
                       $world->allowedUsers()->where('user_id', auth()->id())->exists();
            case 'premium':
                return auth()->user()->hasPremiumAccess();
            default:
                return false;
        }
    }

    /**
     * Calculate capacity usage.
     */
    private function calculateCapacityUsage(VirtualWorld $world): float
    {
        return $world->max_avatars > 0 ? ($world->current_avatar_count / $world->max_avatars) * 100 : 0;
    }

    /**
     * Calculate return visitor rate.
     */
    private function calculateReturnVisitorRate(VirtualWorld $world): float
    {
        $totalVisitors = $world->appearances()->distinct('user_id')->count('user_id');
        $returnVisitors = $world->appearances()
            ->selectRaw('user_id, COUNT(*) as visit_count')
            ->groupBy('user_id')
            ->having('visit_count', '>', 1)
            ->count();
            
        return $totalVisitors > 0 ? ($returnVisitors / $totalVisitors) * 100 : 0;
    }

    /**
     * Calculate bounce rate.
     */
    private function calculateBounceRate(VirtualWorld $world): float
    {
        $totalVisits = $world->appearances()->count();
        $bouncedVisits = $world->appearances()->where('duration', '<', 60)->count();
        
        return $totalVisits > 0 ? ($bouncedVisits / $totalVisits) * 100 : 0;
    }
}
