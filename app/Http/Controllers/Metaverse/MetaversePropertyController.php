<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metaverse\CreateMetaversePropertyRequest;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\MetaverseAvatar;
use App\Models\Metaverse\MetaversePropertyNft;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MetaversePropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of metaverse properties.
     */
    public function index(Request $request)
    {
        $query = MetaverseProperty::with(['virtualWorld', 'owner', 'nft'])
            ->withCount(['tours', 'events', 'showrooms']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('property_type')) {
            $query->where('property_type', $request->property_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', $request->price_min);
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', $request->price_max);
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

        $properties = $query->paginate(12);

        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        
        return Inertia::render('Metaverse/Properties/Index', [
            'properties' => $properties,
            'virtualWorlds' => $virtualWorlds,
            'filters' => $request->only(['world_id', 'property_type', 'status', 'price_min', 'price_max', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the form for creating a new metaverse property.
     */
    public function create()
    {
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        
        return Inertia::render('Metaverse/Properties/Create', [
            'virtualWorlds' => $virtualWorlds,
        ]);
    }

    /**
     * Store a newly created metaverse property.
     */
    public function store(CreateMetaversePropertyRequest $request)
    {
        $property = MetaverseProperty::create([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'property_type' => $request->property_type,
            'location_coordinates' => $request->location_coordinates,
            'dimensions' => $request->dimensions,
            'price' => $request->price,
            'currency' => $request->currency ?? 'ETH',
            'is_for_sale' => $request->boolean('is_for_sale', false),
            'is_for_rent' => $request->boolean('is_for_rent', false),
            'rent_price' => $request->rent_price,
            'rent_currency' => $request->rent_currency ?? 'ETH',
            'rent_period' => $request->rent_period ?? 'monthly',
            'status' => 'pending',
            'visibility' => $request->visibility ?? 'public',
            'access_level' => $request->access_level ?? 'public',
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Handle property images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('metaverse/properties/' . $property->id, 'public');
                $property->images()->create(['path' => $path]);
            }
        }

        // Handle 3D models
        if ($request->hasFile('models')) {
            foreach ($request->file('models') as $model) {
                $path = $model->store('metaverse/models/' . $property->id, 'public');
                $property->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        // Create NFT if requested
        if ($request->boolean('create_nft', false)) {
            $this->createNFT($property);
        }

        return redirect()->route('metaverse.properties.show', $property)
            ->with('success', 'تم إنشاء عقار الميتافيرس بنجاح');
    }

    /**
     * Display the specified metaverse property.
     */
    public function show(MetaverseProperty $property)
    {
        $property->load([
            'virtualWorld',
            'owner',
            'nft',
            'images',
            'models',
            'tours' => function ($query) {
                $query->where('is_active', true)->latest();
            },
            'events' => function ($query) {
                $query->where('start_time', '>', now())->orderBy('start_time');
            },
            'showrooms' => function ($query) {
                $query->where('is_active', true);
            }
        ]);

        // Get nearby properties
        $nearbyProperties = MetaverseProperty::where('virtual_world_id', $property->virtual_world_id)
            ->where('id', '!=', $property->id)
            ->where('status', 'active')
            ->with(['owner', 'nft'])
            ->limit(6)
            ->get();

        // Get property statistics
        $stats = [
            'total_visits' => $property->visit_count ?? 0,
            'total_interactions' => $property->interaction_count ?? 0,
            'average_rating' => $property->reviews()->avg('rating') ?? 0,
            'total_reviews' => $property->reviews()->count(),
        ];

        return Inertia::render('Metaverse/Properties/Show', [
            'property' => $property,
            'nearbyProperties' => $nearbyProperties,
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for editing the specified metaverse property.
     */
    public function edit(MetaverseProperty $property)
    {
        $this->authorize('update', $property);

        $property->load(['images', 'models', 'nft']);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();

        return Inertia::render('Metaverse/Properties/Edit', [
            'property' => $property,
            'virtualWorlds' => $virtualWorlds,
        ]);
    }

    /**
     * Update the specified metaverse property.
     */
    public function update(CreateMetaversePropertyRequest $request, MetaverseProperty $property)
    {
        $this->authorize('update', $property);

        $property->update([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'property_type' => $request->property_type,
            'location_coordinates' => $request->location_coordinates,
            'dimensions' => $request->dimensions,
            'price' => $request->price,
            'currency' => $request->currency ?? 'ETH',
            'is_for_sale' => $request->boolean('is_for_sale'),
            'is_for_rent' => $request->boolean('is_for_rent'),
            'rent_price' => $request->rent_price,
            'rent_currency' => $request->rent_currency ?? 'ETH',
            'rent_period' => $request->rent_period ?? 'monthly',
            'visibility' => $request->visibility,
            'access_level' => $request->access_level,
            'updated_by' => auth()->id(),
        ]);

        // Handle new images
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $image) {
                $path = $image->store('metaverse/properties/' . $property->id, 'public');
                $property->images()->create(['path' => $path]);
            }
        }

        // Handle new 3D models
        if ($request->hasFile('new_models')) {
            foreach ($request->file('new_models') as $model) {
                $path = $model->store('metaverse/models/' . $property->id, 'public');
                $property->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.properties.show', $property)
            ->with('success', 'تم تحديث عقار الميتافيرس بنجاح');
    }

    /**
     * Remove the specified metaverse property.
     */
    public function destroy(MetaverseProperty $property)
    {
        $this->authorize('delete', $property);

        // Delete associated files
        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->path);
        }

        foreach ($property->models as $model) {
            Storage::disk('public')->delete($model->path);
        }

        $property->delete();

        return redirect()->route('metaverse.properties.index')
            ->with('success', 'تم حذف عقار الميتافيرس بنجاح');
    }

    /**
     * Toggle property status (active/inactive).
     */
    public function toggleStatus(MetaverseProperty $property)
    {
        $this->authorize('update', $property);

        $property->update([
            'status' => $property->status === 'active' ? 'inactive' : 'active',
            'updated_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تحديث حالة العقار بنجاح');
    }

    /**
     * Visit property (increment visit count).
     */
    public function visit(MetaverseProperty $property)
    {
        $property->increment('visit_count');

        // Log visit
        $property->visits()->create([
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'visit_count' => $property->fresh()->visit_count,
        ]);
    }

    /**
     * Get property analytics.
     */
    public function analytics(MetaverseProperty $property)
    {
        $this->authorize('view', $property);

        $analytics = [
            'visits' => $property->visits()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->limit(30)
                ->get(),
            
            'interactions' => $property->interactions()
                ->selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            
            'demographics' => [
                'by_country' => $property->visits()
                    ->join('users', 'visits.user_id', '=', 'users.id')
                    ->selectRaw('users.country, COUNT(*) as count')
                    ->groupBy('users.country')
                    ->orderBy('count', 'desc')
                    ->limit(10)
                    ->get(),
            ],
            
            'performance' => [
                'avg_visit_duration' => $property->visits()->avg('duration') ?? 0,
                'bounce_rate' => $this->calculateBounceRate($property),
                'conversion_rate' => $this->calculateConversionRate($property),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Create NFT for property.
     */
    private function createNFT(MetaverseProperty $property)
    {
        $nft = MetaversePropertyNft::create([
            'metaverse_property_id' => $property->id,
            'token_id' => $this->generateTokenId(),
            'contract_address' => config('metaverse.nft_contract_address'),
            'token_uri' => $this->generateTokenURI($property),
            'metadata' => [
                'name' => $property->title,
                'description' => $property->description,
                'image' => $property->images->first()->path ?? null,
                'attributes' => [
                    'property_type' => $property->property_type,
                    'virtual_world' => $property->virtualWorld->name,
                    'dimensions' => $property->dimensions,
                    'location' => $property->location_coordinates,
                ],
            ],
            'created_by' => auth()->id(),
        ]);

        return $nft;
    }

    /**
     * Generate unique token ID.
     */
    private function generateTokenId(): string
    {
        return 'METAV-' . Str::random(8) . '-' . time();
    }

    /**
     * Generate token URI.
     */
    private function generateTokenURI(MetaverseProperty $property): string
    {
        return url("/api/metaverse/nfts/{$property->id}/metadata");
    }

    /**
     * Calculate bounce rate.
     */
    private function calculateBounceRate(MetaverseProperty $property): float
    {
        $totalVisits = $property->visits()->count();
        $bouncedVisits = $property->visits()->where('duration', '<', 30)->count();
        
        return $totalVisits > 0 ? ($bouncedVisits / $totalVisits) * 100 : 0;
    }

    /**
     * Calculate conversion rate.
     */
    private function calculateConversionRate(MetaverseProperty $property): float
    {
        $totalVisits = $property->visits()->count();
        $conversions = $property->transactions()->where('type', 'purchase')->count();
        
        return $totalVisits > 0 ? ($conversions / $totalVisits) * 100 : 0;
    }
}
