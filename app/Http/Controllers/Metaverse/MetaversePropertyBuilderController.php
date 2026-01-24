<?php

namespace App\Http\Controllers\Metaverse;

use App\Http\Controllers\Controller;
use App\Http\Requests\Metaverse\BuildVirtualPropertyRequest;
use App\Models\Metaverse\MetaverseProperty;
use App\Models\Metaverse\VirtualLand;
use App\Models\Metaverse\VirtualWorld;
use App\Models\Metaverse\VirtualPropertyDesign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MetaversePropertyBuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display the property builder interface.
     */
    public function index(Request $request)
    {
        $query = VirtualPropertyDesign::with(['creator', 'virtualWorld', 'virtualLand'])
            ->withCount(['properties', 'templates']);

        // Filters
        if ($request->filled('world_id')) {
            $query->where('virtual_world_id', $request->world_id);
        }

        if ($request->filled('land_id')) {
            $query->where('virtual_land_id', $request->land_id);
        }

        if ($request->filled('design_type')) {
            $query->where('design_type', $request->design_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $designs = $query->paginate(12);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();

        return Inertia::render('Metaverse/Builder/Index', [
            'designs' => $designs,
            'virtualWorlds' => $virtualWorlds,
            'filters' => $request->only(['world_id', 'land_id', 'design_type', 'status', 'search', 'sort_by', 'sort_order']),
        ]);
    }

    /**
     * Show the property builder workspace.
     */
    public function create()
    {
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $userLands = VirtualLand::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Builder/Create', [
            'virtualWorlds' => $virtualWorlds,
            'userLands' => $userLands,
        ]);
    }

    /**
     * Store a new property design.
     */
    public function store(BuildVirtualPropertyRequest $request)
    {
        $design = VirtualPropertyDesign::create([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'virtual_land_id' => $request->virtual_land_id,
            'design_type' => $request->design_type,
            'architectural_style' => $request->architectural_style,
            'building_specifications' => $request->building_specifications,
            'materials_used' => $request->materials_used,
            'color_scheme' => $request->color_scheme,
            'lighting_design' => $request->lighting_design,
            'interior_design' => $request->interior_design,
            'landscape_design' => $request->landscape_design,
            'amenities' => $request->amenities,
            'special_features' => $request->special_features,
            'blueprint_data' => $request->blueprint_data,
            'model_data' => $request->model_data,
            'texture_data' => $request->texture_data,
            'animation_data' => $request->animation_data,
            'interaction_points' => $request->interaction_points,
            'navigation_paths' => $request->navigation_paths,
            'performance_settings' => $request->performance_settings,
            'compatibility_settings' => $request->compatibility_settings,
            'estimated_build_time' => $request->estimated_build_time,
            'estimated_cost' => $request->estimated_cost,
            'currency' => $request->currency ?? 'ETH',
            'difficulty_level' => $request->difficulty_level ?? 'medium',
            'required_skills' => $request->required_skills,
            'tools_needed' => $request->tools_needed,
            'status' => 'draft',
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Handle blueprint files
        if ($request->hasFile('blueprints')) {
            foreach ($request->file('blueprints') as $blueprint) {
                $path = $blueprint->store('metaverse/designs/' . $design->id . '/blueprints', 'public');
                $design->blueprints()->create([
                    'path' => $path,
                    'file_type' => $blueprint->getClientOriginalExtension(),
                    'file_size' => $blueprint->getSize(),
                ]);
            }
        }

        // Handle 3D models
        if ($request->hasFile('models')) {
            foreach ($request->file('models') as $model) {
                $path = $model->store('metaverse/designs/' . $design->id . '/models', 'public');
                $design->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        // Handle textures
        if ($request->hasFile('textures')) {
            foreach ($request->file('textures') as $texture) {
                $path = $texture->store('metaverse/designs/' . $design->id . '/textures', 'public');
                $design->textures()->create([
                    'path' => $path,
                    'file_type' => $texture->getClientOriginalExtension(),
                    'file_size' => $texture->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.builder.show', $design)
            ->with('success', 'تم إنشاء تصميم العقار بنجاح');
    }

    /**
     * Display the specified property design.
     */
    public function show(VirtualPropertyDesign $design)
    {
        $design->load([
            'creator',
            'virtualWorld',
            'virtualLand',
            'blueprints',
            'models',
            'textures',
            'properties',
            'templates',
            'reviews' => function ($query) {
                $query->with('reviewer')->latest();
            },
        ]);

        // Get design statistics
        $stats = [
            'total_properties' => $design->properties_count,
            'total_templates' => $design->templates_count,
            'average_rating' => $design->reviews()->avg('rating') ?? 0,
            'total_reviews' => $design->reviews()->count(),
            'download_count' => $design->download_count ?? 0,
            'usage_count' => $design->properties()->count(),
        ];

        // Get similar designs
        $similarDesigns = VirtualPropertyDesign::where('id', '!=', $design->id)
            ->where('design_type', $design->design_type)
            ->where('status', 'published')
            ->with(['creator', 'virtualWorld'])
            ->limit(6)
            ->get();

        return Inertia::render('Metaverse/Builder/Show', [
            'design' => $design,
            'stats' => $stats,
            'similarDesigns' => $similarDesigns,
        ]);
    }

    /**
     * Show the form for editing the specified property design.
     */
    public function edit(VirtualPropertyDesign $design)
    {
        $this->authorize('update', $design);

        $design->load(['blueprints', 'models', 'textures']);
        $virtualWorlds = VirtualWorld::where('is_active', true)->get();
        $userLands = VirtualLand::where('owner_id', auth()->id())
            ->where('status', 'active')
            ->with(['virtualWorld'])
            ->get();

        return Inertia::render('Metaverse/Builder/Edit', [
            'design' => $design,
            'virtualWorlds' => $virtualWorlds,
            'userLands' => $userLands,
        ]);
    }

    /**
     * Update the specified property design.
     */
    public function update(BuildVirtualPropertyRequest $request, VirtualPropertyDesign $design)
    {
        $this->authorize('update', $design);

        $design->update([
            'title' => $request->title,
            'description' => $request->description,
            'virtual_world_id' => $request->virtual_world_id,
            'virtual_land_id' => $request->virtual_land_id,
            'design_type' => $request->design_type,
            'architectural_style' => $request->architectural_style,
            'building_specifications' => $request->building_specifications,
            'materials_used' => $request->materials_used,
            'color_scheme' => $request->color_scheme,
            'lighting_design' => $request->lighting_design,
            'interior_design' => $request->interior_design,
            'landscape_design' => $request->landscape_design,
            'amenities' => $request->amenities,
            'special_features' => $request->special_features,
            'blueprint_data' => $request->blueprint_data,
            'model_data' => $request->model_data,
            'texture_data' => $request->texture_data,
            'animation_data' => $request->animation_data,
            'interaction_points' => $request->interaction_points,
            'navigation_paths' => $request->navigation_paths,
            'performance_settings' => $request->performance_settings,
            'compatibility_settings' => $request->compatibility_settings,
            'estimated_build_time' => $request->estimated_build_time,
            'estimated_cost' => $request->estimated_cost,
            'currency' => $request->currency ?? 'ETH',
            'difficulty_level' => $request->difficulty_level,
            'required_skills' => $request->required_skills,
            'tools_needed' => $request->tools_needed,
            'updated_by' => auth()->id(),
        ]);

        // Handle new blueprints
        if ($request->hasFile('new_blueprints')) {
            foreach ($request->file('new_blueprints') as $blueprint) {
                $path = $blueprint->store('metaverse/designs/' . $design->id . '/blueprints', 'public');
                $design->blueprints()->create([
                    'path' => $path,
                    'file_type' => $blueprint->getClientOriginalExtension(),
                    'file_size' => $blueprint->getSize(),
                ]);
            }
        }

        // Handle new models
        if ($request->hasFile('new_models')) {
            foreach ($request->file('new_models') as $model) {
                $path = $model->store('metaverse/designs/' . $design->id . '/models', 'public');
                $design->models()->create([
                    'path' => $path,
                    'file_type' => $model->getClientOriginalExtension(),
                    'file_size' => $model->getSize(),
                ]);
            }
        }

        // Handle new textures
        if ($request->hasFile('new_textures')) {
            foreach ($request->file('new_textures') as $texture) {
                $path = $texture->store('metaverse/designs/' . $design->id . '/textures', 'public');
                $design->textures()->create([
                    'path' => $path,
                    'file_type' => $texture->getClientOriginalExtension(),
                    'file_size' => $texture->getSize(),
                ]);
            }
        }

        return redirect()->route('metaverse.builder.show', $design)
            ->with('success', 'تم تحديث تصميم العقار بنجاح');
    }

    /**
     * Remove the specified property design.
     */
    public function destroy(VirtualPropertyDesign $design)
    {
        $this->authorize('delete', $design);

        // Delete associated files
        foreach ($design->blueprints as $blueprint) {
            Storage::disk('public')->delete($blueprint->path);
        }

        foreach ($design->models as $model) {
            Storage::disk('public')->delete($model->path);
        }

        foreach ($design->textures as $texture) {
            Storage::disk('public')->delete($texture->path);
        }

        $design->delete();

        return redirect()->route('metaverse.builder.index')
            ->with('success', 'تم حذف تصميم العقار بنجاح');
    }

    /**
     * Build property from design.
     */
    public function build(Request $request, VirtualPropertyDesign $design)
    {
        $this->authorize('build', $design);

        $request->validate([
            'virtual_land_id' => 'required|exists:virtual_lands,id',
            'property_title' => 'required|string|max:255',
            'property_description' => 'required|string|max:2000',
            'customizations' => 'nullable|array',
            'build_settings' => 'nullable|array',
        ]);

        $land = VirtualLand::findOrFail($request->virtual_land_id);

        // Check if user owns the land
        if ($land->owner_id !== auth()->id()) {
            return back()->with('error', 'لا يمكنك البناء على أرض لا تملكها');
        }

        // Check land capacity
        if ($land->properties()->count() >= $land->max_properties) {
            return back()->with('error', 'الأرض وصلت إلى الحد الأقصى من العقارات');
        }

        // Create property from design
        $property = MetaverseProperty::create([
            'title' => $request->property_title,
            'description' => $request->property_description,
            'virtual_world_id' => $design->virtual_world_id,
            'property_type' => $design->design_type,
            'location_coordinates' => $this->generateLocationCoordinates($land),
            'dimensions' => $design->building_specifications['dimensions'] ?? ['length' => 100, 'width' => 100, 'height' => 50],
            'price' => $design->estimated_cost,
            'currency' => $design->currency,
            'is_for_sale' => true,
            'is_for_rent' => false,
            'status' => 'building',
            'visibility' => 'public',
            'access_level' => 'public',
            'owner_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        // Link with design
        $property->update(['virtual_property_design_id' => $design->id]);

        // Copy design assets to property
        $this->copyDesignAssets($design, $property);

        // Apply customizations
        if ($request->has('customizations')) {
            $this->applyCustomizations($property, $request->customizations);
        }

        // Start building process
        $this->startBuildingProcess($property, $design);

        return redirect()->route('metaverse.properties.show', $property)
            ->with('success', 'تم بدء عملية بناء العقار');
    }

    /**
     * Get builder workspace data.
     */
    public function workspace(VirtualPropertyDesign $design)
    {
        $this->authorize('view', $design);

        $workspace = [
            'design' => $design->load(['blueprints', 'models', 'textures']),
            'tools' => $this->getAvailableTools(),
            'materials' => $this->getAvailableMaterials(),
            'templates' => $this->getDesignTemplates($design),
            'assets_library' => $this->getAssetsLibrary(),
            'collaborators' => $design->collaborators()->with('user')->get(),
        ];

        return response()->json($workspace);
    }

    /**
     * Save design progress.
     */
    public function saveProgress(Request $request, VirtualPropertyDesign $design)
    {
        $this->authorize('update', $design);

        $request->validate([
            'progress_data' => 'required|array',
            'auto_save' => 'boolean',
        ]);

        $design->update([
            'progress_data' => $request->progress_data,
            'last_saved_at' => now(),
            'auto_save_enabled' => $request->boolean('auto_save'),
        ]);

        return response()->json([
            'success' => true,
            'saved_at' => $design->last_saved_at,
        ]);
    }

    /**
     * Publish design.
     */
    public function publish(VirtualPropertyDesign $design)
    {
        $this->authorize('publish', $design);

        if ($design->status === 'published') {
            return back()->with('error', 'التصميم منشور بالفعل');
        }

        // Validate design completeness
        if (!$this->validateDesignCompleteness($design)) {
            return back()->with('error', 'التصميم غير مكتمل. يرجى إكمال جميع المكونات المطلوبة');
        }

        $design->update([
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'تم نشر التصميم بنجاح');
    }

    /**
     * Clone design.
     */
    public function clone(VirtualPropertyDesign $design)
    {
        $this->authorize('view', $design);

        $newDesign = $design->replicate([
            'title',
            'description',
            'virtual_world_id',
            'virtual_land_id',
            'design_type',
            'architectural_style',
            'building_specifications',
            'materials_used',
            'color_scheme',
            'lighting_design',
            'interior_design',
            'landscape_design',
            'amenities',
            'special_features',
            'blueprint_data',
            'model_data',
            'texture_data',
            'animation_data',
            'interaction_points',
            'navigation_paths',
            'performance_settings',
            'compatibility_settings',
            'estimated_build_time',
            'estimated_cost',
            'currency',
            'difficulty_level',
            'required_skills',
            'tools_needed',
        ]);

        $newDesign->update([
            'title' => $design->title . ' (نسخة)',
            'status' => 'draft',
            'creator_id' => auth()->id(),
            'created_by' => auth()->id(),
            'parent_design_id' => $design->id,
        ]);

        // Copy assets
        $this->copyDesignAssets($design, $newDesign);

        return redirect()->route('metaverse.builder.show', $newDesign)
            ->with('success', 'تم نسخ التصميم بنجاح');
    }

    /**
     * Get design analytics.
     */
    public function analytics(VirtualPropertyDesign $design)
    {
        $this->authorize('view', $design);

        $analytics = [
            'usage_stats' => [
                'total_properties' => $design->properties()->count(),
                'active_properties' => $design->properties()->where('status', 'active')->count(),
                'total_value' => $design->properties()->sum('price'),
            ],
            
            'engagement_stats' => [
                'views' => $design->views_count ?? 0,
                'downloads' => $design->download_count ?? 0,
                'shares' => $design->shares_count ?? 0,
                'reviews' => $design->reviews()->count(),
            ],
            
            'performance_metrics' => [
                'build_success_rate' => $this->calculateBuildSuccessRate($design),
                'average_build_time' => $this->calculateAverageBuildTime($design),
                'user_satisfaction' => $design->reviews()->avg('rating') ?? 0,
            ],
            
            'trending_data' => [
                'daily_views' => $this->getDailyViews($design),
                'popular_features' => $this->getPopularFeatures($design),
                'user_demographics' => $this->getUserDemographics($design),
            ],
        ];

        return response()->json($analytics);
    }

    /**
     * Generate location coordinates.
     */
    private function generateLocationCoordinates(VirtualLand $land): string
    {
        // Generate random coordinates within the land
        $x = rand(0, 100);
        $y = rand(0, 100);
        $z = 0;
        
        return "{$x},{$y},{$z}";
    }

    /**
     * Copy design assets.
     */
    private function copyDesignAssets(VirtualPropertyDesign $design, MetaverseProperty $property)
    {
        // Copy models
        foreach ($design->models as $model) {
            $property->models()->create([
                'path' => $model->path,
                'file_type' => $model->file_type,
                'file_size' => $model->file_size,
            ]);
        }

        // Copy textures
        foreach ($design->textures as $texture) {
            $property->textures()->create([
                'path' => $texture->path,
                'file_type' => $texture->file_type,
                'file_size' => $texture->file_size,
            ]);
        }
    }

    /**
     * Apply customizations.
     */
    private function applyCustomizations(MetaverseProperty $property, array $customizations)
    {
        // Apply customizations to property
        $property->update([
            'customizations' => $customizations,
        ]);
    }

    /**
     * Start building process.
     */
    private function startBuildingProcess(MetaverseProperty $property, VirtualPropertyDesign $design)
    {
        // Simulate building process
        $buildTime = $design->estimated_build_time ?? 60; // minutes
        
        // Update property status after build time
        // In real implementation, this would be a background job
        $property->update([
            'build_started_at' => now(),
            'build_completed_at' => now()->addMinutes($buildTime),
        ]);
    }

    /**
     * Get available tools.
     */
    private function getAvailableTools(): array
    {
        return [
            '3d_modeler' => ['name' => '3D Modeler', 'description' => 'Create 3D models'],
            'texture_editor' => ['name' => 'Texture Editor', 'description' => 'Edit textures'],
            'lighting_tool' => ['name' => 'Lighting Tool', 'description' => 'Design lighting'],
            'animation_tool' => ['name' => 'Animation Tool', 'description' => 'Create animations'],
        ];
    }

    /**
     * Get available materials.
     */
    private function getAvailableMaterials(): array
    {
        return [
            'wood' => ['name' => 'Wood', 'cost' => 10, 'durability' => 80],
            'stone' => ['name' => 'Stone', 'cost' => 20, 'durability' => 95],
            'metal' => ['name' => 'Metal', 'cost' => 30, 'durability' => 90],
            'glass' => ['name' => 'Glass', 'cost' => 25, 'durability' => 70],
        ];
    }

    /**
     * Get design templates.
     */
    private function getDesignTemplates(VirtualPropertyDesign $design): array
    {
        return [
            'modern' => ['name' => 'Modern', 'style' => 'minimalist'],
            'classic' => ['name' => 'Classic', 'style' => 'traditional'],
            'futuristic' => ['name' => 'Futuristic', 'style' => 'sci-fi'],
        ];
    }

    /**
     * Get assets library.
     */
    private function getAssetsLibrary(): array
    {
        return [
            'furniture' => [],
            'decorations' => [],
            'lighting' => [],
            'landscaping' => [],
        ];
    }

    /**
     * Validate design completeness.
     */
    private function validateDesignCompleteness(VirtualPropertyDesign $design): bool
    {
        return !empty($design->blueprint_data) && 
               !empty($design->model_data) && 
               $design->models()->count() > 0;
    }

    /**
     * Calculate build success rate.
     */
    private function calculateBuildSuccessRate(VirtualPropertyDesign $design): float
    {
        $totalBuilds = $design->properties()->count();
        $successfulBuilds = $design->properties()->where('status', 'active')->count();
        
        return $totalBuilds > 0 ? ($successfulBuilds / $totalBuilds) * 100 : 0;
    }

    /**
     * Calculate average build time.
     */
    private function calculateAverageBuildTime(VirtualPropertyDesign $design): float
    {
        return $design->properties()
            ->whereNotNull('build_completed_at')
            ->avg(\DB::raw('TIMESTAMPDIFF(MINUTE, build_started_at, build_completed_at)')) ?? 0;
    }

    /**
     * Get daily views.
     */
    private function getDailyViews(VirtualPropertyDesign $design): array
    {
        return $design->views()
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get()
            ->toArray();
    }

    /**
     * Get popular features.
     */
    private function getPopularFeatures(VirtualPropertyDesign $design): array
    {
        return []; // Placeholder
    }

    /**
     * Get user demographics.
     */
    private function getUserDemographics(VirtualPropertyDesign $design): array
    {
        return []; // Placeholder
    }
}
