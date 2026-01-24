<?php

namespace App\Http\Controllers;

use App\Models\VirtualTour;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VirtualTourController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_tours' => VirtualTour::count(),
            'active_tours' => VirtualTour::where('status', 'active')->count(),
            'completed_tours' => VirtualTour::where('status', 'completed')->count(),
            'average_duration' => $this->getAverageDuration(),
            'total_views' => $this->getTotalViews(),
            'engagement_rate' => $this->getEngagementRate(),
        ];

        $recentTours = VirtualTour::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $tourTrends = $this->getTourTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('vr.virtual-tour.dashboard', compact(
            'stats', 
            'recentTours', 
            'tourTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = VirtualTour::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tour_type')) {
            $query->where('tour_type', $request->tour_type);
        }

        if ($request->filled('quality_level')) {
            $query->where('quality_level', $request->quality_level);
        }

        $tours = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $tourTypes = VirtualTour::select('tour_type')->distinct()->pluck('tour_type');
        $qualityLevels = ['basic', 'standard', 'premium', 'ultra_hd'];

        return view('vr.virtual-tour.index', compact(
            'tours', 
            'properties', 
            'tourTypes', 
            'qualityLevels'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $tourTypes = ['360_walkthrough', 'guided_tour', 'interactive_tour', 'cinematic_tour'];
        $qualityLevels = ['basic', 'standard', 'premium', 'ultra_hd'];
        $interactionModes = ['click_based', 'gesture_based', 'voice_controlled', 'vr_headset'];

        return view('vr.virtual-tour.create', compact(
            'properties', 
            'tourTypes', 
            'qualityLevels', 
            'interactionModes'
        ));
    }

    public function store(CreateVirtualTourRequest $request)
    {
        DB::beginTransaction();
        try {
            $tourData = $request->validated();
            $tourData['user_id'] = auth()->id();
            $tourData['status'] = 'processing';
            $tourData['created_by'] = auth()->id();

            // Process tour files
            if ($request->hasFile('tour_files')) {
                $tourData['file_paths'] = $this->processTourFiles($request->file('tour_files'));
            }

            // Generate tour metadata
            $tourData['tour_metadata'] = $this->generateTourMetadata($request);

            $tour = VirtualTour::create($tourData);

            // Process tour scenes
            if ($request->has('scenes')) {
                $this->processTourScenes($tour, $request->scenes);
            }

            // Set up tour hotspots
            if ($request->has('hotspots')) {
                $this->setupTourHotspots($tour, $request->hotspots);
            }

            DB::commit();

            return redirect()
                ->route('vr.virtual-tour.show', $tour)
                ->with('success', 'تم إنشاء الجولة الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الجولة الافتراضية: ' . $e->getMessage());
        }
    }

    public function show(VirtualTour $tour)
    {
        $tour->load(['property', 'user', 'scenes', 'hotspots']);
        $tourAnalytics = $this->getTourAnalytics($tour);
        $relatedTours = $this->getRelatedTours($tour);

        return view('vr.virtual-tour.show', compact(
            'tour', 
            'tourAnalytics', 
            'relatedTours'
        ));
    }

    public function edit(VirtualTour $tour)
    {
        $properties = Property::where('status', 'active')->get();
        $tourTypes = ['360_walkthrough', 'guided_tour', 'interactive_tour', 'cinematic_tour'];
        $qualityLevels = ['basic', 'standard', 'premium', 'ultra_hd'];
        $interactionModes = ['click_based', 'gesture_based', 'voice_controlled', 'vr_headset'];

        return view('vr.virtual-tour.edit', compact(
            'tour', 
            'properties', 
            'tourTypes', 
            'qualityLevels', 
            'interactionModes'
        ));
    }

    public function update(CreateVirtualTourRequest $request, VirtualTour $tour)
    {
        DB::beginTransaction();
        try {
            $tourData = $request->validated();
            $tourData['updated_by'] = auth()->id();

            // Process updated tour files
            if ($request->hasFile('tour_files')) {
                $tourData['file_paths'] = $this->processTourFiles($request->file('tour_files'));
            }

            // Update tour metadata
            $tourData['tour_metadata'] = $this->generateTourMetadata($request);

            $tour->update($tourData);

            // Update tour scenes
            if ($request->has('scenes')) {
                $this->processTourScenes($tour, $request->scenes);
            }

            // Update tour hotspots
            if ($request->has('hotspots')) {
                $this->setupTourHotspots($tour, $request->hotspots);
            }

            DB::commit();

            return redirect()
                ->route('vr.virtual-tour.show', $tour)
                ->with('success', 'تم تحديث الجولة الافتراضية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الجولة الافتراضية: ' . $e->getMessage());
        }
    }

    public function destroy(VirtualTour $tour)
    {
        try {
            // Delete tour files
            $this->deleteTourFiles($tour);

            // Delete tour
            $tour->delete();

            return redirect()
                ->route('vr.virtual-tour.index')
                ->with('success', 'تم حذف الجولة الافتراضية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف الجولة الافتراضية: ' . $e->getMessage());
        }
    }

    public function startTour(VirtualTour $tour)
    {
        try {
            // Initialize tour session
            $session = $this->initializeTourSession($tour);

            // Update tour statistics
            $tour->increment('view_count');
            $tour->update(['last_accessed_at' => now()]);

            return view('vr.virtual-tour.player', compact('tour', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء بدء الجولة: ' . $e->getMessage());
        }
    }

    public function recordInteraction(Request $request, VirtualTour $tour)
    {
        try {
            $interactionData = [
                'tour_id' => $tour->id,
                'user_id' => auth()->id(),
                'interaction_type' => $request->interaction_type,
                'scene_id' => $request->scene_id,
                'hotspot_id' => $request->hotspot_id,
                'duration' => $request->duration,
                'coordinates' => $request->coordinates,
                'device_info' => $this->getDeviceInfo(),
                'timestamp' => now(),
            ];

            // Record interaction
            $this->recordTourInteraction($interactionData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generatePreview(VirtualTour $tour)
    {
        try {
            $previewData = $this->generateTourPreview($tour);

            return response()->json([
                'success' => true,
                'preview' => $previewData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function exportTour(VirtualTour $tour, Request $request)
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

    public function analytics(VirtualTour $tour)
    {
        $analytics = $this->getDetailedTourAnalytics($tour);
        $userBehavior = $this->getUserBehaviorAnalytics($tour);
        $performanceMetrics = $this->getTourPerformanceMetrics($tour);

        return view('vr.virtual-tour.analytics', compact(
            'analytics', 
            'userBehavior', 
            'performanceMetrics'
        ));
    }

    public function duplicate(VirtualTour $tour)
    {
        try {
            $newTour = $tour->replicate();
            $newTour->title = $tour->title . ' (نسخة)';
            $newTour->status = 'processing';
            $newTour->view_count = 0;
            $newTour->created_by = auth()->id();
            $newTour->save();

            // Duplicate scenes and hotspots
            $this->duplicateTourComponents($tour, $newTour);

            return redirect()
                ->route('vr.virtual-tour.edit', $newTour)
                ->with('success', 'تم نسخ الجولة الافتراضية بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء نسخ الجولة: ' . $e->getMessage());
        }
    }

    private function processTourFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('virtual-tours', 'public');
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
            'duration' => $request->duration ?? 0,
            'scene_count' => count($request->scenes ?? []),
            'hotspot_count' => count($request->hotspots ?? []),
            'quality_settings' => $request->quality_settings ?? [],
            'interaction_settings' => $request->interaction_settings ?? [],
            'device_compatibility' => $request->device_compatibility ?? [],
            'accessibility_features' => $request->accessibility_features ?? [],
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processTourScenes($tour, $scenes)
    {
        foreach ($scenes as $sceneData) {
            $tour->scenes()->create([
                'name' => $sceneData['name'],
                'description' => $sceneData['description'] ?? null,
                'scene_order' => $sceneData['order'],
                'image_path' => $sceneData['image_path'] ?? null,
                'panorama_path' => $sceneData['panorama_path'] ?? null,
                'navigation_points' => $sceneData['navigation_points'] ?? [],
                'scene_metadata' => $sceneData['metadata'] ?? [],
            ]);
        }
    }

    private function setupTourHotspots($tour, $hotspots)
    {
        foreach ($hotspots as $hotspotData) {
            $tour->hotspots()->create([
                'scene_id' => $hotspotData['scene_id'],
                'title' => $hotspotData['title'],
                'description' => $hotspotData['description'] ?? null,
                'type' => $hotspotData['type'],
                'position' => $hotspotData['position'],
                'action' => $hotspotData['action'] ?? null,
                'content' => $hotspotData['content'] ?? null,
                'icon' => $hotspotData['icon'] ?? null,
                'style' => $hotspotData['style'] ?? [],
            ]);
        }
    }

    private function initializeTourSession($tour)
    {
        return [
            'session_id' => uniqid('tour_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'tour_id' => $tour->id,
            'device_info' => $this->getDeviceInfo(),
            'settings' => [
                'quality' => 'auto',
                'controls' => 'enabled',
                'navigation' => 'enabled',
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
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/VR|Oculus|HTC/i', $userAgent)) {
            return 'vr_headset';
        } elseif (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function getAverageDuration()
    {
        return VirtualTour::avg('duration') ?? 0;
    }

    private function getTotalViews()
    {
        return VirtualTour::sum('view_count') ?? 0;
    }

    private function getEngagementRate()
    {
        // Calculate engagement based on interactions and completion rates
        return 85.5; // Placeholder
    }

    private function getTourTrends()
    {
        return [
            'daily_views' => VirtualTour::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_tour_types' => VirtualTour::select('tour_type', DB::raw('COUNT(*) as count'))
                ->groupBy('tour_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'load_time' => 2.5,
            'interaction_rate' => 78.3,
            'completion_rate' => 65.7,
            'user_satisfaction' => 4.2,
        ];
    }

    private function getTourAnalytics($tour)
    {
        return [
            'total_views' => $tour->view_count,
            'unique_visitors' => $tour->analytics()->distinct('user_id')->count(),
            'average_session_duration' => $tour->analytics()->avg('duration') ?? 0,
            'most_viewed_scenes' => $this->getMostViewedScenes($tour),
            'interaction_heatmap' => $this->getInteractionHeatmap($tour),
        ];
    }

    private function getRelatedTours($tour)
    {
        return VirtualTour::where('property_id', $tour->property_id)
            ->where('id', '!=', $tour->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteTourFiles($tour)
    {
        // Delete associated files from storage
        if ($tour->file_paths) {
            foreach ($tour->file_paths as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function recordTourInteraction($interactionData)
    {
        // Store interaction data for analytics
        // This would typically save to a tour_analytics table
    }

    private function generateTourPreview($tour)
    {
        return [
            'thumbnail' => $tour->thumbnail_path,
            'duration' => $tour->duration,
            'scene_count' => $tour->scenes()->count(),
            'preview_video' => $tour->preview_video_path,
        ];
    }

    private function prepareTourExport($tour, $format)
    {
        $data = [
            'tour' => $tour->toArray(),
            'scenes' => $tour->scenes->toArray(),
            'hotspots' => $tour->hotspots->toArray(),
            'analytics' => $this->getTourAnalytics($tour),
        ];

        if ($format === 'json') {
            $filename = 'tour_' . $tour->id . '.json';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            // Handle other formats
            $filename = 'tour_' . $tour->id . '.txt';
            $content = serialize($data);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($tempFile, $content);

        return [
            'file' => $tempFile,
            'filename' => $filename,
        ];
    }

    private function getDetailedTourAnalytics($tour)
    {
        return [
            'view_analytics' => $this->getViewAnalytics($tour),
            'interaction_analytics' => $this->getInteractionAnalytics($tour),
            'performance_analytics' => $this->getPerformanceAnalytics($tour),
            'demographic_analytics' => $this->getDemographicAnalytics($tour),
        ];
    }

    private function getUserBehaviorAnalytics($tour)
    {
        return [
            'navigation_patterns' => $this->getNavigationPatterns($tour),
            'interaction_frequency' => $this->getInteractionFrequency($tour),
            'session_duration_distribution' => $this->getSessionDurationDistribution($tour),
            'drop_off_points' => $this->getDropOffPoints($tour),
        ];
    }

    private function getTourPerformanceMetrics($tour)
    {
        return [
            'load_performance' => $this->getLoadPerformance($tour),
            'render_performance' => $this->getRenderPerformance($tour),
            'device_performance' => $this->getDevicePerformance($tour),
            'quality_metrics' => $this->getQualityMetrics($tour),
        ];
    }

    private function duplicateTourComponents($originalTour, $newTour)
    {
        // Duplicate scenes
        foreach ($originalTour->scenes as $scene) {
            $newScene = $scene->replicate();
            $newScene->tour_id = $newTour->id;
            $newScene->save();
        }

        // Duplicate hotspots
        foreach ($originalTour->hotspots as $hotspot) {
            $newHotspot = $hotspot->replicate();
            $newHotspot->tour_id = $newTour->id;
            $newHotspot->save();
        }
    }

    // Additional helper methods would be implemented here...
}
