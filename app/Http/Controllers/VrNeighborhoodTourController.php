<?php

namespace App\Http\Controllers;

use App\Models\VrNeighborhoodTour;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VrNeighborhoodTourController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_tours' => VrNeighborhoodTour::count(),
            'active_tours' => VrNeighborhoodTour::where('status', 'active')->count(),
            'completed_tours' => VrNeighborhoodTour::where('status', 'completed')->count(),
            'average_duration' => $this->getAverageDuration(),
            'total_sessions' => $this->getTotalSessions(),
            'completion_rate' => $this->getCompletionRate(),
        ];

        $recentTours = VrNeighborhoodTour::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $tourTrends = $this->getTourTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('vr.neighborhood-tour.dashboard', compact(
            'stats', 
            'recentTours', 
            'tourTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = VrNeighborhoodTour::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_type')) {
            $query->where('tour_type', $request->tour_type);
        }

        if ($request->filled('coverage_area')) {
            $query->where('coverage_area', $request->coverage_area);
        }

        $tours = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $tourTypes = ['walking', 'driving', 'cycling', 'mixed'];
        $coverageAreas = ['immediate', 'extended', 'comprehensive'];
        $statuses = ['active', 'completed', 'paused', 'cancelled'];

        return view('vr.neighborhood-tour.index', compact(
            'tours', 
            'properties', 
            'tourTypes', 
            'coverageAreas', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $tourTypes = ['walking', 'driving', 'cycling', 'mixed'];
        $coverageAreas = ['immediate', 'extended', 'comprehensive'];
        $navigationModes = ['guided', 'free_roam', 'points_of_interest', 'story_based'];
        $interactionModes = ['information_popups', 'audio_guide', 'video_overlays', 'interactive_map'];

        return view('vr.neighborhood-tour.create', compact(
            'properties', 
            'tourTypes', 
            'coverageAreas', 
            'navigationModes', 
            'interactionModes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $tourData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'tour_type' => 'required|in:walking,driving,cycling,mixed',
                'coverage_area' => 'required|in:immediate,extended,comprehensive',
                'navigation_mode' => 'required|in:guided,free_roam,points_of_interest,story_based',
                'interaction_mode' => 'required|in:information_popups,audio_guide,video_overlays,interactive_map',
                'duration_minutes' => 'required|integer|min:10|max:180',
                'tour_route' => 'required|array',
                'points_of_interest' => 'nullable|array',
                'narration_script' => 'nullable|array',
                'neighborhood_data' => 'required|array',
                'accessibility_options' => 'nullable|array',
            ]);

            $tourData['user_id'] = auth()->id();
            $tourData['status'] = 'processing';
            $tourData['created_by'] = auth()->id();

            // Process tour assets
            if ($request->hasFile('tour_files')) {
                $tourData['asset_files'] = $this->processTourAssets($request->file('tour_files'));
            }

            // Generate tour metadata
            $tourData['tour_metadata'] = $this->generateTourMetadata($request);

            $tour = VrNeighborhoodTour::create($tourData);

            // Process tour route
            if ($request->has('tour_route')) {
                $this->processTourRoute($tour, $request->tour_route);
            }

            // Set up points of interest
            if ($request->has('points_of_interest')) {
                $this->setupPointsOfInterest($tour, $request->points_of_interest);
            }

            // Set up narration
            if ($request->has('narration_script')) {
                $this->setupNarration($tour, $request->narration_script);
            }

            // Configure neighborhood data
            if ($request->has('neighborhood_data')) {
                $this->configureNeighborhoodData($tour, $request->neighborhood_data);
            }

            DB::commit();

            return redirect()
                ->route('vr.neighborhood-tour.show', $tour)
                ->with('success', 'تم إنشاء جولة الحي الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء جولة الحي الافتراضية: ' . $e->getMessage());
        }
    }

    public function show(VrNeighborhoodTour $tour)
    {
        $tour->load(['property', 'user', 'tourRoute', 'pointsOfInterest', 'narration', 'neighborhoodData']);
        $tourAnalytics = $this->getTourAnalytics($tour);
        $relatedTours = $this->getRelatedTours($tour);

        return view('vr.neighborhood-tour.show', compact(
            'tour', 
            'tourAnalytics', 
            'relatedTours'
        ));
    }

    public function edit(VrNeighborhoodTour $tour)
    {
        $properties = Property::where('status', 'active')->get();
        $tourTypes = ['walking', 'driving', 'cycling', 'mixed'];
        $coverageAreas = ['immediate', 'extended', 'comprehensive'];
        $navigationModes = ['guided', 'free_roam', 'points_of_interest', 'story_based'];
        $interactionModes = ['information_popups', 'audio_guide', 'video_overlays', 'interactive_map'];

        return view('vr.neighborhood-tour.edit', compact(
            'tour', 
            'properties', 
            'tourTypes', 
            'coverageAreas', 
            'navigationModes', 
            'interactionModes'
        ));
    }

    public function update(Request $request, VrNeighborhoodTour $tour)
    {
        DB::beginTransaction();
        try {
            $tourData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'tour_type' => 'required|in:walking,driving,cycling,mixed',
                'coverage_area' => 'required|in:immediate,extended,comprehensive',
                'navigation_mode' => 'required|in:guided,free_roam,points_of_interest,story_based',
                'interaction_mode' => 'required|in:information_popups,audio_guide,video_overlays,interactive_map',
                'duration_minutes' => 'required|integer|min:10|max:180',
                'tour_route' => 'required|array',
                'points_of_interest' => 'nullable|array',
                'narration_script' => 'nullable|array',
                'neighborhood_data' => 'required|array',
                'accessibility_options' => 'nullable|array',
            ]);

            $tourData['updated_by'] = auth()->id();

            // Process updated tour assets
            if ($request->hasFile('tour_files')) {
                $tourData['asset_files'] = $this->processTourAssets($request->file('tour_files'));
            }

            // Update tour metadata
            $tourData['tour_metadata'] = $this->generateTourMetadata($request);

            $tour->update($tourData);

            // Update tour route
            if ($request->has('tour_route')) {
                $this->processTourRoute($tour, $request->tour_route);
            }

            // Update points of interest
            if ($request->has('points_of_interest')) {
                $this->setupPointsOfInterest($tour, $request->points_of_interest);
            }

            // Update narration
            if ($request->has('narration_script')) {
                $this->setupNarration($tour, $request->narration_script);
            }

            // Update neighborhood data
            if ($request->has('neighborhood_data')) {
                $this->configureNeighborhoodData($tour, $request->neighborhood_data);
            }

            DB::commit();

            return redirect()
                ->route('vr.neighborhood-tour.show', $tour)
                ->with('success', 'تم تحديث جولة الحي الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث جولة الحي الافتراضية: ' . $e->getMessage());
        }
    }

    public function destroy(VrNeighborhoodTour $tour)
    {
        try {
            // Delete tour assets
            $this->deleteTourAssets($tour);

            // Delete tour
            $tour->delete();

            return redirect()
                ->route('vr.neighborhood-tour.index')
                ->with('success', 'تم حذف جولة الحي الافتراضية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف جولة الحي الافتراضية: ' . $e->getMessage());
        }
    }

    public function startTour(VrNeighborhoodTour $tour)
    {
        try {
            // Initialize VR neighborhood tour session
            $session = $this->initializeVrNeighborhoodSession($tour);

            // Update tour statistics
            $tour->increment('session_count');
            $tour->update(['last_accessed_at' => now()]);

            return view('vr.neighborhood-tour.session', compact('tour', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء بدء الجولة: ' . $e->getMessage());
        }
    }

    public function navigateToLocation(Request $request, VrNeighborhoodTour $tour)
    {
        try {
            $locationId = $request->location_id;
            $transitionType = $request->transition_type ?? 'teleport';

            // Navigate to specified location
            $navigationData = $this->navigateToLocation($tour, $locationId, $transitionType);

            return response()->json([
                'success' => true,
                'navigation' => $navigationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function explorePointOfInterest(Request $request, VrNeighborhoodTour $tour)
    {
        try {
            $poiId = $request->poi_id;
            $explorationType = $request->exploration_type ?? 'overview';

            // Explore point of interest
            $explorationData = $this->explorePointOfInterest($tour, $poiId, $explorationType);

            return response()->json([
                'success' => true,
                'exploration' => $explorationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function playAudioGuide(Request $request, VrNeighborhoodTour $tour)
    {
        try {
            $audioId = $request->audio_id;
            $language = $request->language ?? 'ar';

            // Play audio guide
            $audioData = $this->playAudioGuide($tour, $audioId, $language);

            return response()->json([
                'success' => true,
                'audio' => $audioData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function viewInteractiveMap(Request $request, VrNeighborhoodTour $tour)
    {
        try {
            $mapData = [
                'center_point' => $request->center_point,
                'zoom_level' => $request->zoom_level ?? 15,
                'show_pois' => $request->show_pois ?? true,
                'show_route' => $request->show_route ?? true,
            ];

            // Generate interactive map
            $interactiveMap = $this->generateInteractiveMap($tour, $mapData);

            return response()->json([
                'success' => true,
                'map' => $interactiveMap
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function recordProgress(Request $request, VrNeighborhoodTour $tour)
    {
        try {
            $progressData = [
                'location_id' => $request->location_id,
                'time_spent' => $request->time_spent,
                'pois_visited' => $request->pois_visited,
                'completion_percentage' => $request->completion_percentage,
                'timestamp' => now(),
            ];

            // Record tour progress
            $this->recordTourProgress($tour, $progressData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function completeTour(VrNeighborhoodTour $tour, Request $request)
    {
        try {
            $completionData = [
                'total_time' => $request->total_time,
                'locations_visited' => $request->locations_visited,
                'pois_explored' => $request->pois_explored,
                'rating' => $request->rating,
                'feedback' => $request->feedback,
                'completed_at' => now(),
            ];

            // Complete tour
            $this->completeTourSession($tour, $completionData);

            return response()->json([
                'success' => true,
                'message' => 'تم إكمال الجولة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analytics(VrNeighborhoodTour $tour)
    {
        $analytics = $this->getDetailedTourAnalytics($tour);
        $userBehavior = $this->getUserBehaviorAnalytics($tour);
        $performanceMetrics = $this->getTourPerformanceMetrics($tour);

        return view('vr.neighborhood-tour.analytics', compact(
            'analytics', 
            'userBehavior', 
            'performanceMetrics'
        ));
    }

    public function exportTour(VrNeighborhoodTour $tour, Request $request)
    {
        try {
            $exportFormat = $request->format ?? 'json';
            $exportData = $this->prepareTourExport($tour, $exportFormat);

            return response()->download($exportData['file'], $exportData['filename']);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تصدير الجولة: ' . $e->getMessage());
        }
    }

    private function processTourAssets($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('vr-neighborhood-tours', 'public');
            $filePaths[] = [
                'path' => $path,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $filePaths;
    }

    private function generateTourMetadata($request)
    {
        return [
            'total_route_points' => count($request->tour_route ?? []),
            'total_pois' => count($request->points_of_interest ?? []),
            'narration_duration' => $this->calculateNarrationDuration($request),
            'coverage_radius' => $this->calculateCoverageRadius($request),
            'vr_compatibility' => $request->vr_compatibility ?? [],
            'accessibility_features' => $request->accessibility_options ?? [],
            'estimated_bandwidth' => $this->estimateRequiredBandwidth($request),
            'device_requirements' => $this->calculateDeviceRequirements($request),
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processTourRoute($tour, $tourRoute)
    {
        foreach ($tourRoute as $routeData) {
            $tour->tourRoute()->create([
                'name' => $routeData['name'],
                'coordinates' => $routeData['coordinates'],
                'route_order' => $routeData['order'],
                'segment_type' => $routeData['segment_type'],
                'duration' => $routeData['duration'] ?? 5,
                'distance' => $routeData['distance'] ?? 0,
                'transport_mode' => $routeData['transport_mode'] ?? 'walking',
                'route_metadata' => $routeData['metadata'] ?? [],
            ]);
        }
    }

    private function setupPointsOfInterest($tour, $pointsOfInterest)
    {
        foreach ($pointsOfInterest as $poiData) {
            $tour->pointsOfInterest()->create([
                'name' => $poiData['name'],
                'type' => $poiData['type'],
                'coordinates' => $poiData['coordinates'],
                'description' => $poiData['description'] ?? null,
                'importance_level' => $poiData['importance_level'] ?? 'medium',
                'media_content' => $poiData['media_content'] ?? [],
                'interaction_type' => $poiData['interaction_type'] ?? 'view',
                'poi_metadata' => $poiData['metadata'] ?? [],
            ]);
        }
    }

    private function setupNarration($tour, $narrationScript)
    {
        foreach ($narrationScript as $narrationData) {
            $tour->narration()->create([
                'title' => $narrationData['title'],
                'script' => $narrationData['script'],
                'language' => $narrationData['language'] ?? 'ar',
                'audio_file_path' => $narrationData['audio_file_path'] ?? null,
                'duration' => $narrationData['duration'] ?? 30,
                'voice_type' => $narrationData['voice_type'] ?? 'professional',
                'location_id' => $narrationData['location_id'] ?? null,
                'auto_play' => $narrationData['auto_play'] ?? true,
                'narration_metadata' => $narrationData['metadata'] ?? [],
            ]);
        }
    }

    private function configureNeighborhoodData($tour, $neighborhoodData)
    {
        foreach ($neighborhoodData as $dataItem) {
            $tour->neighborhoodData()->create([
                'data_type' => $dataItem['type'],
                'data_value' => $dataItem['value'],
                'data_source' => $dataItem['source'] ?? 'official',
                'last_updated' => $dataItem['last_updated'] ?? now(),
                'confidence_level' => $dataItem['confidence_level'] ?? 0.95,
                'data_metadata' => $dataItem['metadata'] ?? [],
            ]);
        }
    }

    private function initializeVrNeighborhoodSession($tour)
    {
        return [
            'session_id' => uniqid('vr_neighborhood_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'tour_id' => $tour->id,
            'device_info' => $this->getDeviceInfo(),
            'vr_settings' => [
                'rendering_quality' => 'high',
                'navigation_mode' => $tour->navigation_mode,
                'interaction_mode' => $tour->interaction_mode,
                'comfort_settings' => $this->getComfortSettings(),
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

        return $capabilities;
    }

    private function getComfortSettings()
    {
        return [
            'snap_turning' => true,
            'vignette_strength' => 0.5,
            'movement_speed' => 1.0,
            'height_adjustment' => 'auto',
        ];
    }

    private function calculateNarrationDuration($request)
    {
        $totalDuration = 0;
        if ($request->has('narration_script')) {
            foreach ($request->narration_script as $narration) {
                $totalDuration += $narration['duration'] ?? 30;
            }
        }
        return $totalDuration;
    }

    private function calculateCoverageRadius($request)
    {
        $coverageAreas = [
            'immediate' => 500,    // 500 meters
            'extended' => 2000,    // 2 km
            'comprehensive' => 5000, // 5 km
        ];

        return $coverageAreas[$request->coverage_area] ?? 1000;
    }

    private function estimateRequiredBandwidth($request)
    {
        $baseBandwidth = 10; // 10 Mbps base
        
        if ($request->tour_type === 'driving') {
            $baseBandwidth *= 1.5;
        }
        
        if ($request->coverage_area === 'comprehensive') {
            $baseBandwidth *= 2;
        }
        
        return $baseBandwidth;
    }

    private function calculateDeviceRequirements($request)
    {
        return [
            'minimum_ram' => 8,
            'minimum_gpu' => 'GTX 1060',
            'recommended_cpu' => 'i5-8400',
            'storage_space' => $this->getStorageRequirement($request),
        ];
    }

    private function getStorageRequirement($request)
    {
        $baseSize = 3; // 3 GB base
        
        if ($request->coverage_area === 'comprehensive') {
            $baseSize *= 2;
        }
        
        return $baseSize;
    }

    private function getAverageDuration()
    {
        return VrNeighborhoodTour::avg('duration_minutes') ?? 0;
    }

    private function getTotalSessions()
    {
        return VrNeighborhoodTour::sum('session_count') ?? 0;
    }

    private function getCompletionRate()
    {
        return 72.5; // Placeholder - would calculate from actual completion data
    }

    private function getTourTrends()
    {
        return [
            'daily_sessions' => VrNeighborhoodTour::selectRaw('DATE(created_at) as date, SUM(session_count) as sessions')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_types' => VrNeighborhoodTour::select('tour_type', DB::raw('COUNT(*) as count'))
                ->groupBy('tour_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'rendering_fps' => 68,
            'load_time' => 12.5,
            'completion_rate' => 72.5,
            'user_satisfaction' => 4.3,
        ];
    }

    private function getTourAnalytics($tour)
    {
        return [
            'total_sessions' => $tour->session_count,
            'average_duration' => $tour->average_session_duration,
            'completion_rate' => $tour->completion_rate,
            'user_rating' => $tour->average_rating,
            'most_visited_locations' => $this->getMostVisitedLocations($tour),
        ];
    }

    private function getRelatedTours($tour)
    {
        return VrNeighborhoodTour::where('property_id', $tour->property_id)
            ->where('id', '!=', $tour->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteTourAssets($tour)
    {
        // Delete associated files from storage
        if ($tour->asset_files) {
            foreach ($tour->asset_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function navigateToLocation($tour, $locationId, $transitionType)
    {
        $location = $tour->tourRoute()->find($locationId);
        
        return [
            'location_id' => $locationId,
            'coordinates' => $location->coordinates,
            'transition_type' => $transitionType,
            'transition_duration' => $this->getTransitionDuration($transitionType),
            'narration' => $this->getLocationNarration($tour, $locationId),
        ];
    }

    private function explorePointOfInterest($tour, $poiId, $explorationType)
    {
        $poi = $tour->pointsOfInterest()->find($poiId);
        
        return [
            'poi_id' => $poiId,
            'exploration_type' => $explorationType,
            'poi_data' => $poi->toArray(),
            'media_content' => $poi->media_content,
            'interaction_options' => $this->getInteractionOptions($poi),
        ];
    }

    private function playAudioGuide($tour, $audioId, $language)
    {
        $audio = $tour->narration()->find($audioId);
        
        return [
            'audio_id' => $audioId,
            'audio_url' => $audio->audio_file_path,
            'duration' => $audio->duration,
            'language' => $language,
            'auto_play' => $audio->auto_play,
        ];
    }

    private function generateInteractiveMap($tour, $mapData)
    {
        return [
            'map_id' => uniqid('map_'),
            'center_point' => $mapData['center_point'],
            'zoom_level' => $mapData['zoom_level'],
            'route_path' => $this->getRoutePath($tour),
            'poi_markers' => $this->getPoiMarkers($tour),
            'interactive_elements' => $this->getInteractiveMapElements($tour),
        ];
    }

    private function recordTourProgress($tour, $progressData)
    {
        // Record progress for analytics
        // This would typically save to a tour_progress table
    }

    private function completeTourSession($tour, $completionData)
    {
        // Update tour completion statistics
        $tour->increment('completed_sessions');
        
        // Update average rating if provided
        if (isset($completionData['rating'])) {
            $this->updateAverageRating($tour, $completionData['rating']);
        }
        
        // Store completion data for analytics
        $this->storeCompletionData($tour, $completionData);
    }

    private function getTransitionDuration($transitionType)
    {
        $durations = [
            'instant' => 0,
            'smooth' => 3.0,
            'teleport' => 1.0,
            'fade' => 2.0,
        ];

        return $durations[$transitionType] ?? 2.0;
    }

    private function getLocationNarration($tour, $locationId)
    {
        // Get narration for specific location
        $narration = $tour->narration()->where('location_id', $locationId)->first();
        
        return $narration ? [
            'title' => $narration->title,
            'duration' => $narration->duration,
            'auto_play' => $narration->auto_play,
        ] : null;
    }

    private function getInteractionOptions($poi)
    {
        return [
            'view_details' => true,
            'play_audio' => $poi->type === 'historical',
            'view_media' => count($poi->media_content) > 0,
            'take_photo' => $poi->importance_level === 'high',
        ];
    }

    private function getRoutePath($tour)
    {
        return $tour->tourRoute->map(function ($route) {
            return [
                'coordinates' => $route->coordinates,
                'segment_type' => $route->segment_type,
                'transport_mode' => $route->transport_mode,
            ];
        })->toArray();
    }

    private function getPoiMarkers($tour)
    {
        return $tour->pointsOfInterest->map(function ($poi) {
            return [
                'id' => $poi->id,
                'name' => $poi->name,
                'type' => $poi->type,
                'coordinates' => $poi->coordinates,
                'importance_level' => $poi->importance_level,
            ];
        })->toArray();
    }

    private function getInteractiveMapElements($tour)
    {
        return [
            'navigation_controls' => true,
            'zoom_controls' => true,
            'poi_popups' => true,
            'route_highlighting' => true,
            'location_markers' => true,
        ];
    }

    private function updateAverageRating($tour, $newRating)
    {
        $currentRating = $tour->average_rating ?? 0;
        $completedSessions = $tour->completed_sessions ?? 1;
        
        $newAverage = (($currentRating * $completedSessions) + $newRating) / ($completedSessions + 1);
        
        $tour->update(['average_rating' => $newAverage]);
    }

    private function storeCompletionData($tour, $completionData)
    {
        // Store completion data for analytics
        // This would typically save to a tour_completions table
    }

    private function getMostVisitedLocations($tour)
    {
        // Get most visited locations from analytics
        return $tour->tourRoute->take(5)->pluck('name')->toArray();
    }

    private function getDetailedTourAnalytics($tour)
    {
        return [
            'session_analytics' => $this->getTourAnalytics($tour),
            'route_analytics' => $this->getRouteAnalytics($tour),
            'poi_analytics' => $this->getPoiAnalytics($tour),
            'performance_analytics' => $this->getPerformanceAnalytics($tour),
        ];
    }

    private function getUserBehaviorAnalytics($tour)
    {
        return [
            'navigation_patterns' => $this->getNavigationPatterns($tour),
            'poi_interaction_frequency' => $this->getPoiInteractionFrequency($tour),
            'completion_patterns' => $this->getCompletionPatterns($tour),
            'drop_off_points' => $this->getDropOffPoints($tour),
        ];
    }

    private function getTourPerformanceMetrics($tour)
    {
        return [
            'rendering_performance' => $this->getRenderingPerformance($tour),
            'network_performance' => $this->getNetworkPerformance($tour),
            'device_performance' => $this->getDevicePerformance($tour),
            'user_experience' => $this->getUserExperience($tour),
        ];
    }

    private function prepareTourExport($tour, $format)
    {
        $data = [
            'tour' => $tour->toArray(),
            'tour_route' => $tour->tourRoute->toArray(),
            'points_of_interest' => $tour->pointsOfInterest->toArray(),
            'narration' => $tour->narration->toArray(),
            'neighborhood_data' => $tour->neighborhoodData->toArray(),
            'analytics' => $this->getTourAnalytics($tour),
        ];

        if ($format === 'json') {
            $filename = 'neighborhood_tour_' . $tour->id . '.json';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            $filename = 'neighborhood_tour_' . $tour->id . '.txt';
            $content = serialize($data);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($tempFile, $content);

        return [
            'file' => $tempFile,
            'filename' => $filename,
        ];
    }

    // Additional helper methods would be implemented here...
}
