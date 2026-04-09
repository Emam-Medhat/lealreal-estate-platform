<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Models\Metaverse\MetaverseShowroom;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\MetaverseAvatar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MetaverseShowroomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of metaverse showrooms.
     */
    public function index(Request $request)
    {
        $query = MetaverseShowroom::with(['virtualWorld', 'owner', 'properties'])
            ->withCount(['visits', 'events']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('showroom_type')) {
            $query->where('showroom_type', $request->showroom_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('access_level')) {
            $query->where('access_level', $request->access_level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location_coordinates', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $showrooms = $query->paginate(12);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();

        return Inertia::render('Metaverse/Showrooms/Index', [
            'showrooms' => $showrooms,
            'virtualWorlds' => $virtualWorlds,
            'filters' => $request->only(['world_id', 'showroom_type', 'status', 'access_level', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new metaverse showroom.
     */
    public function create()
    {
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Showrooms/Create', [
            'virtualWorlds' => $virtualWorlds,
            'properties' => $properties,
        ]);
    }

    /**
     * Store a newly created metaverse showroom.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'virtual_world_id' => 'required|exists:virtual_worlds,id',
            'showroom_type' => 'required|in:residential,commercial,mixed,exhibition,event_space',
            'location_coordinates' => 'required|string|max:255',
            'dimensions' => 'required|array',
            'dimensions.length' => 'required|numeric|min:1',
            'dimensions.width' => 'required|numeric|min:1',
            'dimensions.height' => 'required|numeric|min:1',
            'access_level' => 'required|in:public,private,restricted,premium',
            'capacity' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'theme' => 'nullable|string|max:100',
            'lighting_settings' => 'nullable|array',
            'audio_settings' => 'nullable|array',
            'interactive_elements' => 'nullable|array',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:metaverse_properties,id',
            'is_active' => 'boolean',
        ]);

        $showroom = MetaverseShowroom::create([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'showroom_type' => $request->showroom_type,
            'location_coordinates' => $request->location_coordinates,
            'dimensions' => $request->dimensions,
            'access_level' => $request->access_level,
            'capacity' => $request->capacity,
            'theme' => $request->theme,
            'lighting_settings' => $request->lighting_settings ?? [],
            'audio_settings' => $request->audio_settings ?? [],
            'interactive_elements' => $request->interactive_elements ?? [],
            'status' => 'active',
            'is_active' => $request->boolean('is_active', true),
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Attach properties to showroom
        if ($request->has('property_ids')) {
            $showroom->properties()->attach($request->property_ids);
        }

        // Handle showroom images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('metaverse/showrooms/' . $showroom->id, 'public');
                $showroom->images()->create(['path' => $path]);
            }
        }

        // Handle 3D models
        if ($request->hasFile('models')) {
            foreach ($request->file('models') as $model) {
                $path = $model->store('metaverse/showroom-models/' . $showroom->id, 'public');
                $showroom->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.showrooms.show', $showroom)
            ->with('success', 'تم إنشاء صالة العرض الافتراضية بنجاح');
    }

    /**
     * Display the specified metaverse showroom.
     */
    public function show(MetaverseShowroom $showroom)
    {
        $showroom->load([
            'virtualWorld',
            'owner',
            'images',
            'models',
            'properties' => function ($query) {
                $query->where('status', 'active')->with(['owner', 'nft']);
            },
            'events' => function ($query) {
                $query->where('start_time', '>', now())->orderBy('start_time');
            },
        ]);

        // Get showroom statistics
        $stats = [
            'total_visits' => $showroom->visits_count,
            'total_events' => $showroom->events_count,
            'property_count' => $showroom->properties->count(),
            'average_rating' => $showroom->reviews()->avg('rating') ?? 0,
            'total_reviews' => $showroom->reviews()->count(),
        ];

        // Get nearby showrooms
        $nearbyShowrooms = MetaverseShowroom::where('virtual_world_id', $showroom->virtual_world_id)
            ->where('id', '!=', $showroom->id)
            ->where('status', 'active')
            ->where('is_active', true)
            ->with(['owner'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Showrooms/Show', [
            'showroom' => $showroom,
            'stats' => $stats,
            'nearbyShowrooms' => $nearbyShowrooms,
        ]);
    }

    /**
     * Show the form for editing the specified metaverse showroom.
     */
    public function edit(MetaverseShowroom $showroom)
    {
        $this->authorize('update', $showroom);

        $showroom->load(['images', 'models', 'properties']);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $properties = MetaverseProperty::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Showrooms/Edit', [
            'showroom' => $showroom,
            'virtualWorlds' => $virtualWorlds,
            'properties' => $properties,
        ]);
    }

    /**
     * Update the specified metaverse showroom.
     */
    public function update(Request $request, MetaverseShowroom $showroom)
    {
        $this->authorize('update', $showroom);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'virtual_world_id' => 'required|exists:virtual_worlds,id',
            'showroom_type' => 'required|in:residential,commercial,mixed,exhibition,event_space',
            'location_coordinates' => 'required|string|max:255',
            'dimensions' => 'required|array',
            'dimensions.length' => 'required|numeric|min:1',
            'dimensions.width' => 'required|numeric|min:1',
            'dimensions.height' => 'required|numeric|min:1',
            'access_level' => 'required|in:public,private,restricted,premium',
            'capacity' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'theme' => 'nullable|string|max:100',
            'lighting_settings' => 'nullable|array',
            'audio_settings' => 'nullable|array',
            'interactive_elements' => 'nullable|array',
            'property_ids' => 'nullable|array',
            'property_ids.*' => 'exists:metaverse_properties,id',
            'is_active' => 'boolean',
        ]);

        $showroom->update([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'showroom_type' => $request->showroom_type,
            'location_coordinates' => $request->location_coordinates,
            'dimensions' => $request->dimensions,
            'access_level' => $request->access_level,
            'capacity' => $request->capacity,
            'theme' => $request->theme,
            'lighting_settings' => $request->lighting_settings ?? [],
            'audio_settings' => $request->audio_settings ?? [],
            'interactive_elements' => $request->interactive_elements ?? [],
            'is_active' => $request->boolean('is_active'),
            'updated_by' => auth()->id(),
        ]);

        // Sync properties
        if ($request->has('property_ids')) {
            $showroom->properties()->sync($request->property_ids);
        } else {
            $showroom->properties()->detach();
        }

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('metaverse/showrooms/' . $showroom->id, 'public');
                $showroom->images()->create(['path' => $path]);
            }
        }

        // Handle new 3D models
        if ($request->hasFile('new_models')) {
            foreach ($request->file('new_models') as $model) {
                $path = $model->store('metaverse/showroom-models/' . $showroom->id, 'public');
                $showroom->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.showrooms.show', $showroom)
            ->with('success', 'تم تحديث صالة العرض الافتراضية بنجاح');
    }

    /**
     * Remove the specified metaverse showroom.
     */
    public function destroy(MetaverseShowroom $showroom)
    {
        $this->authorize('delete', $showroom);

        // Delete associated files
        foreach ($showroom->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        foreach ($showroom->models as $model) {
            Storage::disk('public')->delete($model->path);
        }

        $showroom->delete();

        return redirect()->route('metaverse.showrooms.index')
            ->with('success', 'تم حذف صالة العرض الافتراضية بنجاح');
    }

    /**
     * Enter showroom (virtual visit).
     */
    public function enter(MetaverseShowroom $showroom)
    {
        $this->authorize('enter', $showroom);

        // Check access level
        if (!$this->checkAccess($showroom)) {
            return back()->with('error', 'ليس لديك صلاحية لدخول هذه الصالة');
        }

        // Check capacity
        if ($showroom->current_visitors >= $showroom->capacity) {
            return back()->with('error', 'صالة العرض ممتلئة حالياً');
        }

        // Log visit
        $showroom->visits()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'entered_at' => now(),
        ]);

        // Increment current visitors
        $showroom->increment('current_visitors');

        return Inertia::render('Metaverse/Showrooms/VirtualSpace', [
            'showroom' => $showroom->load(['properties', 'models']),
            'userAvatar' => auth()->user()->metaverseAvatar,
        ]);
    }

    /**
     * Exit showroom.
     */
    public function exit(MetaverseShowroom $showroom)
    {
        // Update visit record
        $visit = $showroom->visits()
            ->where('user_id', auth()->id())
            ->whereNull('exited_at')
            ->latest()
            ->first();

        if ($visit) {
            $visit->update([
                'exited_at' => now(),
                'duration' => now()->diffInSeconds($visit->entered_at),
            ]);
        }

        // Decrement current visitors
        $showroom->decrement('current_visitors');

        return redirect()->route('metaverse.showrooms.show', $showroom);
    }

    /**
     * Configure showroom settings.
     */
    public function configure(Request $request, MetaverseShowroom $showroom)
    {
        $this->authorize('configure', $showroom);

        $request->validate([
            'lighting_settings' => 'nullable|array',
            'audio_settings' => 'nullable|array',
            'interactive_elements' => 'nullable|array',
            'theme' => 'nullable|string|max:100',
            'ambient_settings' => 'nullable|array',
        ]);

        $showroom->update([
            'lighting_settings' => $request->lighting_settings ?? [],
            'audio_settings' => $request->audio_settings ?? [],
            'interactive_elements' => $request->interactive_elements ?? [],
            'theme' => $request->theme,
            'ambient_settings' => $request->ambient_settings ?? [],
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تحديث إعدادات الصالة بنجاح');
    }

    /**
     * Get showroom analytics.
     */
    public function analytics(MetaverseShowroom $showroom)
    {
        $this->authorize('view', $showroom);

        $analytics = [
            'visits' => $showroom->visits()
                ->selectRaw('DATE(entered_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
            
            'peak_hours' => $showroom->visits()
                ->selectRaw('HOUR(entered_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('count', 'desc')
                ->limit(24)
                ->get(),
            
            'visitor_demographics' => [
                'by_country' => $showroom->visits()
                    ->join('users', 'visits.user_id', '=', 'users.id')
                    ->selectRaw('users.country, COUNT(*) as count')
                    ->groupBy('users.country')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            
            'performance' => [
                'avg_visit_duration' => $showroom->visits()->avg('duration') ?? 0,
                'bounce_rate' => $this->calculateBounceRate($showroom),
                'return_visitor_rate' => $this->calculateReturnVisitorRate($showroom),
            ],
            
            'property_interactions' => $showroom->propertyInteractions()
                ->selectRaw('metaverse_property_id, COUNT(*) as interactions')
                ->groupBy('metaverse_property_id')
                ->with('property')
                ->orderBy('interactions', 'desc')
                ->get(),
        ];

        return response()->json($analytics);
    }

    /**
     * Check user access to showroom.
     */
    private function checkAccess(MetaverseShowroom $showroom): bool
    {
        switch ($showroom->access_level) {
            case 'public':
                return true;
            case 'private':
                return $showroom->owner_id === auth()->id();
            case 'restricted':
                return $showroom->owner_id === auth()->id() || 
                       $showroom->allowedUsers()->where('user_id', auth()->id())->exists();
            case 'premium':
                return auth()->user()->hasPremiumAccess();
            default:
                return false;
        }
    }

    /**
     * Calculate bounce rate.
     */
    private function calculateBounceRate(MetaverseShowroom $showroom): float
    {
        $totalVisits = $showroom->visits()->count();
        $bouncedVisits = $showroom->visits()->where('duration', '<', 60)->count();
        
        return $totalVisits > 0 ? ($bouncedVisits / $totalVisits) * 100 : 0;
    }

    /**
     * Calculate return visitor rate.
     */
    private function calculateReturnVisitorRate(MetaverseShowroom $showroom): float
    {
        $totalVisitors = $showroom->visits()->distinct('user_id')->count('user_id');
        $returnVisitors = $showroom->visits()
            ->selectRaw('user_id, COUNT(*) as visit_count')
            ->groupBy('user_id')
            ->having('visit_count', '>', 1)
            ->count();
            
        return $totalVisitors > 0 ? ($returnVisitors / $totalVisitors) * 100 : 0;
    }
}
