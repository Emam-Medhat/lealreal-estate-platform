<?php

namespace App\Http\Controllers;

use App\Models\VrShowroom;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VrShowroomController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_showrooms' => VrShowroom::count(),
            'active_showrooms' => VrShowroom::where('status', 'active')->count(),
            'featured_showrooms' => VrShowroom::where('is_featured', true)->count(),
            'average_visit_duration' => $this->getAverageVisitDuration(),
            'total_visits' => $this->getTotalVisits(),
            'conversion_rate' => $this->getConversionRate(),
        ];

        $recentShowrooms = VrShowroom::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $showroomTrends = $this->getShowroomTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('vr.showroom.dashboard', compact(
            'stats', 
            'recentShowrooms', 
            'showroomTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = VrShowroom::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('showroom_type')) {
            $query->where('showroom_type', $request->showroom_type);
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $showrooms = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $showroomTypes = ['residential', 'commercial', 'luxury', 'show_home', 'model_unit'];
        $statuses = ['active', 'inactive', 'maintenance', 'archived'];

        return view('vr.showroom.index', compact(
            'showrooms', 
            'properties', 
            'showroomTypes', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $showroomTypes = ['residential', 'commercial', 'luxury', 'show_home', 'model_unit'];
        $environmentTypes = ['modern', 'classic', 'minimalist', 'industrial', 'traditional'];
        $lightingModes = ['natural', 'artificial', 'mixed', 'dynamic'];
        $interactionModes = ['free_roam', 'guided_tour', 'interactive_stations', 'presentation_mode'];

        return view('vr.showroom.create', compact(
            'properties', 
            'showroomTypes', 
            'environmentTypes', 
            'lightingModes', 
            'interactionModes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $showroomData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'showroom_type' => 'required|in:residential,commercial,luxury,show_home,model_unit',
                'environment_type' => 'required|in:modern,classic,minimalist,industrial,traditional',
                'lighting_mode' => 'required|in:natural,artificial,mixed,dynamic',
                'interaction_mode' => 'required|in:free_roam,guided_tour,interactive_stations,presentation_mode',
                'max_capacity' => 'required|integer|min:1|max:100',
                'duration_minutes' => 'required|integer|min:5|max:120',
                'showroom_assets' => 'required|array',
                'furniture_items' => 'nullable|array',
                'decor_elements' => 'nullable|array',
                'lighting_setup' => 'nullable|array',
                'audio_settings' => 'nullable|array',
                'accessibility_features' => 'nullable|array',
            ]);

            $showroomData['user_id'] = auth()->id();
            $showroomData['status'] = 'processing';
            $showroomData['created_by'] = auth()->id();

            // Process showroom assets
            if ($request->hasFile('showroom_files')) {
                $showroomData['asset_files'] = $this->processShowroomAssets($request->file('showroom_files'));
            }

            // Generate showroom metadata
            $showroomData['showroom_metadata'] = $this->generateShowroomMetadata($request);

            $showroom = VrShowroom::create($showroomData);

            // Process furniture items
            if ($request->has('furniture_items')) {
                $this->processFurnitureItems($showroom, $request->furniture_items);
            }

            // Set up decor elements
            if ($request->has('decor_elements')) {
                $this->setupDecorElements($showroom, $request->decor_elements);
            }

            // Configure lighting setup
            if ($request->has('lighting_setup')) {
                $this->configureLightingSetup($showroom, $request->lighting_setup);
            }

            // Set up audio settings
            if ($request->has('audio_settings')) {
                $this->setupAudioSettings($showroom, $request->audio_settings);
            }

            DB::commit();

            return redirect()
                ->route('vr.showroom.show', $showroom)
                ->with('success', 'تم إنشاء صالة العرض الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء صالة العرض الافتراضية: ' . $e->getMessage());
        }
    }

    public function show(VrShowroom $showroom)
    {
        $showroom->load(['property', 'user', 'furnitureItems', 'decorElements', 'lightingSetup']);
        $showroomAnalytics = $this->getShowroomAnalytics($showroom);
        $relatedShowrooms = $this->getRelatedShowrooms($showroom);

        return view('vr.showroom.show', compact(
            'showroom', 
            'showroomAnalytics', 
            'relatedShowrooms'
        ));
    }

    public function edit(VrShowroom $showroom)
    {
        $properties = Property::where('status', 'active')->get();
        $showroomTypes = ['residential', 'commercial', 'luxury', 'show_home', 'model_unit'];
        $environmentTypes = ['modern', 'classic', 'minimalist', 'industrial', 'traditional'];
        $lightingModes = ['natural', 'artificial', 'mixed', 'dynamic'];
        $interactionModes = ['free_roam', 'guided_tour', 'interactive_stations', 'presentation_mode'];

        return view('vr.showroom.edit', compact(
            'showroom', 
            'properties', 
            'showroomTypes', 
            'environmentTypes', 
            'lightingModes', 
            'interactionModes'
        ));
    }

    public function update(Request $request, VrShowroom $showroom)
    {
        DB::beginTransaction();
        try {
            $showroomData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'showroom_type' => 'required|in:residential,commercial,luxury,show_home,model_unit',
                'environment_type' => 'required|in:modern,classic,minimalist,industrial,traditional',
                'lighting_mode' => 'required|in:natural,artificial,mixed,dynamic',
                'interaction_mode' => 'required|in:free_roam,guided_tour,interactive_stations,presentation_mode',
                'max_capacity' => 'required|integer|min:1|max:100',
                'duration_minutes' => 'required|integer|min:5|max:120',
                'showroom_assets' => 'required|array',
                'furniture_items' => 'nullable|array',
                'decor_elements' => 'nullable|array',
                'lighting_setup' => 'nullable|array',
                'audio_settings' => 'nullable|array',
                'accessibility_features' => 'nullable|array',
            ]);

            $showroomData['updated_by'] = auth()->id();

            // Process updated showroom assets
            if ($request->hasFile('showroom_files')) {
                $showroomData['asset_files'] = $this->processShowroomAssets($request->file('showroom_files'));
            }

            // Update showroom metadata
            $showroomData['showroom_metadata'] = $this->generateShowroomMetadata($request);

            $showroom->update($showroomData);

            // Update furniture items
            if ($request->has('furniture_items')) {
                $this->processFurnitureItems($showroom, $request->furniture_items);
            }

            // Update decor elements
            if ($request->has('decor_elements')) {
                $this->setupDecorElements($showroom, $request->decor_elements);
            }

            // Update lighting setup
            if ($request->has('lighting_setup')) {
                $this->configureLightingSetup($showroom, $request->lighting_setup);
            }

            // Update audio settings
            if ($request->has('audio_settings')) {
                $this->setupAudioSettings($showroom, $request->audio_settings);
            }

            DB::commit();

            return redirect()
                ->route('vr.showroom.show', $showroom)
                ->with('success', 'تم تحديث صالة العرض الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث صالة العرض الافتراضية: ' . $e->getMessage());
        }
    }

    public function destroy(VrShowroom $showroom)
    {
        try {
            // Delete showroom assets
            $this->deleteShowroomAssets($showroom);

            // Delete showroom
            $showroom->delete();

            return redirect()
                ->route('vr.showroom.index')
                ->with('success', 'تم حذف صالة العرض الافتراضية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف صالة العرض الافتراضية: ' . $e->getMessage());
        }
    }

    public function enterShowroom(VrShowroom $showroom)
    {
        try {
            // Initialize VR session
            $session = $this->initializeVrSession($showroom);

            // Update showroom statistics
            $showroom->increment('visit_count');
            $showroom->update(['last_accessed_at' => now()]);

            return view('vr.showroom.viewer', compact('showroom', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء دخول صالة العرض: ' . $e->getMessage());
        }
    }

    public function recordInteraction(Request $request, VrShowroom $showroom)
    {
        try {
            $interactionData = [
                'showroom_id' => $showroom->id,
                'user_id' => auth()->id(),
                'interaction_type' => $request->interaction_type,
                'item_type' => $request->item_type,
                'item_id' => $request->item_id,
                'position' => $request->position,
                'duration' => $request->duration,
                'device_info' => $this->getDeviceInfo(),
                'timestamp' => now(),
            ];

            // Record interaction
            $this->recordShowroomInteraction($interactionData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function customizeEnvironment(Request $request, VrShowroom $showroom)
    {
        try {
            $customizationData = $this->processEnvironmentCustomization($showroom, $request);

            return response()->json([
                'success' => true,
                'customization' => $customizationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function toggleLighting(Request $request, VrShowroom $showroom)
    {
        try {
            $lightingState = $this->toggleShowroomLighting($showroom, $request);

            return response()->json([
                'success' => true,
                'lighting' => $lightingState
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function arrangeFurniture(Request $request, VrShowroom $showroom)
    {
        try {
            $arrangementData = $this->processFurnitureArrangement($showroom, $request);

            return response()->json([
                'success' => true,
                'arrangement' => $arrangementData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analytics(VrShowroom $showroom)
    {
        $analytics = $this->getDetailedShowroomAnalytics($showroom);
        $userBehavior = $this->getUserBehaviorAnalytics($showroom);
        $performanceMetrics = $this->getShowroomPerformanceMetrics($showroom);

        return view('vr.showroom.analytics', compact(
            'analytics', 
            'userBehavior', 
            'performanceMetrics'
        ));
    }

    public function duplicate(VrShowroom $showroom)
    {
        try {
            $newShowroom = $showroom->replicate();
            $newShowroom->title = $showroom->title . ' (نسخة)';
            $newShowroom->status = 'processing';
            $newShowroom->visit_count = 0;
            $newShowroom->created_by = auth()->id();
            $newShowroom->save();

            // Duplicate showroom components
            $this->duplicateShowroomComponents($showroom, $newShowroom);

            return redirect()
                ->route('vr.showroom.edit', $newShowroom)
                ->with('success', 'تم نسخ صالة العرض الافتراضية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء نسخ صالة العرض: ' . $e->getMessage());
        }
    }

    private function processShowroomAssets($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('vr-showrooms', 'public');
            $filePaths[] = [
                'path' => $path,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $filePaths;
    }

    private function generateShowroomMetadata($request)
    {
        return [
            'environment_settings' => $request->environment_settings ?? [],
            'rendering_quality' => $request->rendering_quality ?? 'high',
            'performance_mode' => $request->performance_mode ?? 'balanced',
            'vr_compatibility' => $request->vr_compatibility ?? [],
            'accessibility_options' => $request->accessibility_options ?? [],
            'space_utilization' => $request->space_utilization ?? 85,
            'ambiance_score' => $request->ambiance_score ?? 8.5,
            'comfort_rating' => $request->comfort_rating ?? 9.0,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processFurnitureItems($showroom, $furnitureItems)
    {
        foreach ($furnitureItems as $furnitureData) {
            $showroom->furnitureItems()->create([
                'name' => $furnitureData['name'],
                'type' => $furnitureData['type'],
                'model_path' => $furnitureData['model_path'] ?? null,
                'texture_path' => $furnitureData['texture_path'] ?? null,
                'position' => $furnitureData['position'],
                'rotation' => $furnitureData['rotation'] ?? [0, 0, 0],
                'scale' => $furnitureData['scale'] ?? [1, 1, 1],
                'is_interactive' => $furnitureData['is_interactive'] ?? false,
                'interaction_type' => $furnitureData['interaction_type'] ?? 'none',
                'furniture_metadata' => $furnitureData['metadata'] ?? [],
            ]);
        }
    }

    private function setupDecorElements($showroom, $decorElements)
    {
        foreach ($decorElements as $decorData) {
            $showroom->decorElements()->create([
                'name' => $decorData['name'],
                'type' => $decorData['type'],
                'model_path' => $decorData['model_path'] ?? null,
                'position' => $decorData['position'],
                'rotation' => $decorData['rotation'] ?? [0, 0, 0],
                'scale' => $decorData['scale'] ?? [1, 1, 1],
                'material' => $decorData['material'] ?? null,
                'color' => $decorData['color'] ?? null,
                'is_animated' => $decorData['is_animated'] ?? false,
                'animation_data' => $decorData['animation_data'] ?? [],
            ]);
        }
    }

    private function configureLightingSetup($showroom, $lightingSetup)
    {
        foreach ($lightingSetup as $lightingData) {
            $showroom->lightingSetup()->create([
                'name' => $lightingData['name'],
                'type' => $lightingData['type'],
                'position' => $lightingData['position'],
                'intensity' => $lightingData['intensity'],
                'color' => $lightingData['color'] ?? '#ffffff',
                'range' => $lightingData['range'] ?? 10,
                'is_dynamic' => $lightingData['is_dynamic'] ?? false,
                'animation_data' => $lightingData['animation_data'] ?? [],
                'lighting_metadata' => $lightingData['metadata'] ?? [],
            ]);
        }
    }

    private function setupAudioSettings($showroom, $audioSettings)
    {
        foreach ($audioSettings as $audioData) {
            $showroom->audioSettings()->create([
                'name' => $audioData['name'],
                'type' => $audioData['type'],
                'audio_file_path' => $audioData['audio_file_path'] ?? null,
                'volume' => $audioData['volume'] ?? 0.5,
                'loop' => $audioData['loop'] ?? false,
                'spatial_audio' => $audioData['spatial_audio'] ?? false,
                'position' => $audioData['position'] ?? [0, 0, 0],
                'range' => $audioData['range'] ?? 20,
            ]);
        }
    }

    private function initializeVrSession($showroom)
    {
        return [
            'session_id' => uniqid('vr_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'showroom_id' => $showroom->id,
            'device_info' => $this->getDeviceInfo(),
            'settings' => [
                'quality' => 'auto',
                'controls' => 'enabled',
                'audio' => 'enabled',
                'interactions' => 'enabled',
            ],
        ];
    }

    private function getDeviceInfo()
    {
        return [
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'screen_resolution' => request()->header('Screen-Resolution'),
            'device_type' => $this->detectDeviceType(),
            'vr_capabilities' => $this->detectVrCapabilities(),
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/VR|Oculus|HTC|Valve/i', $userAgent)) {
            return 'vr_headset';
        } elseif (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function detectVrCapabilities()
    {
        // Check for VR support based on user agent and headers
        $userAgent = request()->userAgent();
        $capabilities = [];

        if (preg_match('/Oculus/i', $userAgent)) {
            $capabilities[] = 'oculus_rift';
        }
        
        if (preg_match('/HTC/i', $userAgent)) {
            $capabilities[] = 'htc_vive';
        }

        if (preg_match('/Valve/i', $userAgent)) {
            $capabilities[] = 'valve_index';
        }

        if (preg_match('/WebXR/i', $userAgent)) {
            $capabilities[] = 'webxr';
        }

        return $capabilities;
    }

    private function getAverageVisitDuration()
    {
        return VrShowroom::avg('average_visit_duration') ?? 0;
    }

    private function getTotalVisits()
    {
        return VrShowroom::sum('visit_count') ?? 0;
    }

    private function getConversionRate()
    {
        return 12.5; // Placeholder - would calculate from actual conversions
    }

    private function getShowroomTrends()
    {
        return [
            'daily_visits' => VrShowroom::selectRaw('DATE(created_at) as date, SUM(visit_count) as visits')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_types' => VrShowroom::select('showroom_type', DB::raw('COUNT(*) as count'))
                ->groupBy('showroom_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'rendering_fps' => 75,
            'load_time' => 4.2,
            'interaction_rate' => 82.3,
            'user_satisfaction' => 4.5,
        ];
    }

    private function getShowroomAnalytics($showroom)
    {
        return [
            'total_visits' => $showroom->visit_count,
            'unique_visitors' => $showroom->analytics()->distinct('user_id')->count(),
            'average_visit_duration' => $showroom->average_visit_duration,
            'most_interactive_items' => $this->getMostInteractiveItems($showroom),
            'peak_visit_times' => $this->getPeakVisitTimes($showroom),
        ];
    }

    private function getRelatedShowrooms($showroom)
    {
        return VrShowroom::where('property_id', $showroom->property_id)
            ->where('id', '!=', $showroom->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteShowroomAssets($showroom)
    {
        // Delete associated files from storage
        if ($showroom->asset_files) {
            foreach ($showroom->asset_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function recordShowroomInteraction($interactionData)
    {
        // Store interaction data for analytics
        // This would typically save to a showroom_interactions table
    }

    private function processEnvironmentCustomization($showroom, $request)
    {
        return [
            'customization_id' => uniqid('custom_'),
            'environment_changes' => $request->changes,
            'applied_at' => now(),
            'preview_image' => $this->generateCustomizationPreview($showroom, $request),
        ];
    }

    private function toggleShowroomLighting($showroom, $request)
    {
        $lightingMode = $request->lighting_mode ?? 'natural';
        
        return [
            'current_mode' => $lightingMode,
            'intensity' => $this->calculateLightingIntensity($lightingMode),
            'color_temperature' => $this->getColorTemperature($lightingMode),
            'energy_consumption' => $this->calculateEnergyConsumption($lightingMode),
        ];
    }

    private function processFurnitureArrangement($showroom, $request)
    {
        return [
            'arrangement_id' => uniqid('arrange_'),
            'furniture_positions' => $request->positions,
            'space_utilization' => $this->calculateSpaceUtilization($request->positions),
            'flow_analysis' => $this->analyzeFlowPattern($request->positions),
            'saved_at' => now(),
        ];
    }

    private function generateCustomizationPreview($showroom, $request)
    {
        // Generate a preview image of the customized environment
        return 'storage/showroom-previews/' . uniqid('preview_') . '.jpg';
    }

    private function calculateLightingIntensity($mode)
    {
        $intensities = [
            'natural' => 0.8,
            'artificial' => 0.6,
            'mixed' => 0.7,
            'dynamic' => 0.9,
        ];

        return $intensities[$mode] ?? 0.7;
    }

    private function getColorTemperature($mode)
    {
        $temperatures = [
            'natural' => 5500,
            'artificial' => 3200,
            'mixed' => 4500,
            'dynamic' => 5000,
        ];

        return $temperatures[$mode] ?? 4500;
    }

    private function calculateEnergyConsumption($mode)
    {
        $consumption = [
            'natural' => 0.2,
            'artificial' => 0.8,
            'mixed' => 0.5,
            'dynamic' => 0.6,
        ];

        return $consumption[$mode] ?? 0.5;
    }

    private function calculateSpaceUtilization($positions)
    {
        // Calculate how efficiently the space is being used
        return 78.5; // Placeholder calculation
    }

    private function analyzeFlowPattern($positions)
    {
        // Analyze the flow pattern through the arranged furniture
        return [
            'flow_score' => 85.2,
            'bottlenecks' => [],
            'recommended_paths' => [],
        ];
    }

    private function getDetailedShowroomAnalytics($showroom)
    {
        return [
            'visit_analytics' => $this->getShowroomAnalytics($showroom),
            'interaction_analytics' => $this->getInteractionAnalytics($showroom),
            'performance_analytics' => $this->getPerformanceAnalytics($showroom),
            'demographic_analytics' => $this->getDemographicAnalytics($showroom),
        ];
    }

    private function getUserBehaviorAnalytics($showroom)
    {
        return [
            'navigation_patterns' => $this->getNavigationPatterns($showroom),
            'interaction_frequency' => $this->getInteractionFrequency($showroom),
            'session_duration_distribution' => $this->getSessionDurationDistribution($showroom),
            'popular_areas' => $this->getPopularAreas($showroom),
        ];
    }

    private function getShowroomPerformanceMetrics($showroom)
    {
        return [
            'rendering_performance' => $this->getRenderingPerformance($showroom),
            'network_performance' => $this->getNetworkPerformance($showroom),
            'device_performance' => $this->getDevicePerformance($showroom),
            'quality_metrics' => $this->getQualityMetrics($showroom),
        ];
    }

    private function duplicateShowroomComponents($originalShowroom, $newShowroom)
    {
        // Duplicate furniture items
        foreach ($originalShowroom->furnitureItems as $furniture) {
            $newFurniture = $furniture->replicate();
            $newFurniture->showroom_id = $newShowroom->id;
            $newFurniture->save();
        }

        // Duplicate decor elements
        foreach ($originalShowroom->decorElements as $decor) {
            $newDecor = $decor->replicate();
            $newDecor->showroom_id = $newShowroom->id;
            $newDecor->save();
        }

        // Duplicate lighting setup
        foreach ($originalShowroom->lightingSetup as $lighting) {
            $newLighting = $lighting->replicate();
            $newLighting->showroom_id = $newShowroom->id;
            $newLighting->save();
        }

        // Duplicate audio settings
        foreach ($originalShowroom->audioSettings as $audio) {
            $newAudio = $audio->replicate();
            $newAudio->showroom_id = $newShowroom->id;
            $newAudio->save();
        }
    }

    // Additional helper methods would be implemented here...
}
