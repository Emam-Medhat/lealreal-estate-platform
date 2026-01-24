<?php

namespace App\Http\Controllers;

use App\Models\ArPropertyView;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ArPropertyViewerController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_views' => ArPropertyView::count(),
            'active_views' => ArPropertyView::where('status', 'active')->count(),
            'completed_views' => ArPropertyView::where('status', 'completed')->count(),
            'average_session_duration' => $this->getAverageSessionDuration(),
            'total_interactions' => $this->getTotalInteractions(),
            'device_distribution' => $this->getDeviceDistribution(),
        ];

        $recentViews = ArPropertyView::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $viewTrends = $this->getViewTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ar.property-viewer.dashboard', compact(
            'stats', 
            'recentViews', 
            'viewTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = ArPropertyView::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('device_type')) {
            $query->where('device_type', $request->device_type);
        }

        if ($request->filled('view_mode')) {
            $query->where('view_mode', $request->view_mode);
        }

        $views = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $viewModes = ['marker_based', 'markerless', 'location_based', 'image_based'];
        $deviceTypes = ['mobile', 'tablet', 'ar_glasses', 'smartphone'];

        return view('ar.property-viewer.index', compact(
            'views', 
            'properties', 
            'viewModes', 
            'deviceTypes'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $viewModes = ['marker_based', 'markerless', 'location_based', 'image_based'];
        $trackingTypes = ['plane_detection', 'object_detection', 'face_detection', 'image_tracking'];
        $interactionTypes = ['touch_gesture', 'voice_command', 'gaze_based', 'hand_gesture'];

        return view('ar.property-viewer.create', compact(
            'properties', 
            'viewModes', 
            'trackingTypes', 
            'interactionTypes'
        ));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $viewData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'view_mode' => 'required|in:marker_based,markerless,location_based,image_based',
                'tracking_type' => 'required|in:plane_detection,object_detection,face_detection,image_tracking',
                'interaction_type' => 'required|in:touch_gesture,voice_command,gaze_based,hand_gesture',
                'ar_content' => 'required|array',
                'marker_images' => 'nullable|array',
                'tracking_targets' => 'nullable|array',
                'interaction_zones' => 'nullable|array',
                'device_compatibility' => 'nullable|array',
                'quality_settings' => 'nullable|array',
            ]);

            $viewData['user_id'] = auth()->id();
            $viewData['status'] = 'processing';
            $viewData['created_by'] = auth()->id();

            // Process AR content files
            if ($request->hasFile('ar_content_files')) {
                $viewData['content_files'] = $this->processArContentFiles($request->file('ar_content_files'));
            }

            // Process marker images
            if ($request->hasFile('marker_images')) {
                $viewData['marker_image_paths'] = $this->processMarkerImages($request->file('marker_images'));
            }

            // Generate AR metadata
            $viewData['ar_metadata'] = $this->generateArMetadata($request);

            $view = ArPropertyView::create($viewData);

            // Process AR content items
            if ($request->has('ar_content')) {
                $this->processArContentItems($view, $request->ar_content);
            }

            // Set up tracking targets
            if ($request->has('tracking_targets')) {
                $this->setupTrackingTargets($view, $request->tracking_targets);
            }

            // Configure interaction zones
            if ($request->has('interaction_zones')) {
                $this->configureInteractionZones($view, $request->interaction_zones);
            }

            DB::commit();

            return redirect()
                ->route('ar.property-viewer.show', $view)
                ->with('success', 'تم إنشاء عرض الواقع المعزز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء عرض الواقع المعزز: ' . $e->getMessage());
        }
    }

    public function show(ArPropertyView $view)
    {
        $view->load(['property', 'user', 'arContent', 'trackingTargets', 'interactionZones']);
        $viewAnalytics = $this->getViewAnalytics($view);
        $relatedViews = $this->getRelatedViews($view);

        return view('ar.property-viewer.show', compact(
            'view', 
            'viewAnalytics', 
            'relatedViews'
        ));
    }

    public function edit(ArPropertyView $view)
    {
        $properties = Property::where('status', 'active')->get();
        $viewModes = ['marker_based', 'markerless', 'location_based', 'image_based'];
        $trackingTypes = ['plane_detection', 'object_detection', 'face_detection', 'image_tracking'];
        $interactionTypes = ['touch_gesture', 'voice_command', 'gaze_based', 'hand_gesture'];

        return view('ar.property-viewer.edit', compact(
            'view', 
            'properties', 
            'viewModes', 
            'trackingTypes', 
            'interactionTypes'
        ));
    }

    public function update(Request $request, ArPropertyView $view)
    {
        DB::beginTransaction();
        try {
            $viewData = $request->validate([
                'property_id' => 'required|exists:properties,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'view_mode' => 'required|in:marker_based,markerless,location_based,image_based',
                'tracking_type' => 'required|in:plane_detection,object_detection,face_detection,image_tracking',
                'interaction_type' => 'required|in:touch_gesture,voice_command,gaze_based,hand_gesture',
                'ar_content' => 'required|array',
                'marker_images' => 'nullable|array',
                'tracking_targets' => 'nullable|array',
                'interaction_zones' => 'nullable|array',
                'device_compatibility' => 'nullable|array',
                'quality_settings' => 'nullable|array',
            ]);

            $viewData['updated_by'] = auth()->id();

            // Process updated AR content files
            if ($request->hasFile('ar_content_files')) {
                $viewData['content_files'] = $this->processArContentFiles($request->file('ar_content_files'));
            }

            // Update AR metadata
            $viewData['ar_metadata'] = $this->generateArMetadata($request);

            $view->update($viewData);

            // Update AR content items
            if ($request->has('ar_content')) {
                $this->processArContentItems($view, $request->ar_content);
            }

            // Update tracking targets
            if ($request->has('tracking_targets')) {
                $this->setupTrackingTargets($view, $request->tracking_targets);
            }

            // Update interaction zones
            if ($request->has('interaction_zones')) {
                $this->configureInteractionZones($view, $request->interaction_zones);
            }

            DB::commit();

            return redirect()
                ->route('ar.property-viewer.show', $view)
                ->with('success', 'تم تحديث عرض الواقع المعزز بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث عرض الواقع المعزز: ' . $e->getMessage());
        }
    }

    public function destroy(ArPropertyView $view)
    {
        try {
            // Delete AR content files
            $this->deleteArContentFiles($view);

            // Delete view
            $view->delete();

            return redirect()
                ->route('ar.property-viewer.index')
                ->with('success', 'تم حذف عرض الواقع المعزز بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف عرض الواقع المعزز: ' . $e->getMessage());
        }
    }

    public function launchViewer(ArPropertyView $view)
    {
        try {
            // Initialize AR session
            $session = $this->initializeArSession($view);

            // Update view statistics
            $view->increment('launch_count');
            $view->update(['last_accessed_at' => now()]);

            return view('ar.property-viewer.viewer', compact('view', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تشغيل العارض: ' . $e->getMessage());
        }
    }

    public function recordInteraction(Request $request, ArPropertyView $view)
    {
        try {
            $interactionData = [
                'view_id' => $view->id,
                'user_id' => auth()->id(),
                'interaction_type' => $request->interaction_type,
                'content_id' => $request->content_id,
                'coordinates' => $request->coordinates,
                'duration' => $request->duration,
                'device_info' => $this->getDeviceInfo(),
                'timestamp' => now(),
            ];

            // Record interaction
            $this->recordArInteraction($interactionData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generateMarker(ArPropertyView $view)
    {
        try {
            $markerData = $this->generateArMarker($view);

            return response()->json([
                'success' => true,
                'marker' => $markerData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function calibrateTracking(Request $request, ArPropertyView $view)
    {
        try {
            $calibrationData = $this->performTrackingCalibration($view, $request);

            return response()->json([
                'success' => true,
                'calibration' => $calibrationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function testCompatibility(Request $request)
    {
        try {
            $deviceInfo = $this->analyzeDeviceCompatibility($request);
            
            return response()->json([
                'success' => true,
                'compatibility' => $deviceInfo
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analytics(ArPropertyView $view)
    {
        $analytics = $this->getDetailedViewAnalytics($view);
        $userBehavior = $this->getUserBehaviorAnalytics($view);
        $performanceMetrics = $this->getViewPerformanceMetrics($view);

        return view('ar.property-viewer.analytics', compact(
            'analytics', 
            'userBehavior', 
            'performanceMetrics'
        ));
    }

    public function exportView(ArPropertyView $view, Request $request)
    {
        try {
            $exportFormat = $request->format ?? 'json';
            $exportData = $this->prepareViewExport($view, $exportFormat);

            return response()->download($exportData['file'], $exportData['filename']);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تصدير العرض: ' . $e->getMessage());
        }
    }

    private function processArContentFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('ar-content', 'public');
            $filePaths[] = [
                'path' => $path,
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $filePaths;
    }

    private function processMarkerImages($files)
    {
        $imagePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('ar-markers', 'public');
            $imagePaths[] = [
                'path' => $path,
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
            ];
        }

        return $imagePaths;
    }

    private function generateArMetadata($request)
    {
        return [
            'tracking_accuracy' => $request->tracking_accuracy ?? 'high',
            'rendering_quality' => $request->rendering_quality ?? 'medium',
            'performance_mode' => $request->performance_mode ?? 'balanced',
            'supported_devices' => $request->supported_devices ?? [],
            'ar_kit_version' => $request->ar_kit_version ?? null,
            'ar_core_version' => $request->ar_core_version ?? null,
            'tracking_features' => $request->tracking_features ?? [],
            'interaction_features' => $request->interaction_features ?? [],
            'content_size' => $request->content_size ?? 0,
            'loading_time' => $request->loading_time ?? 0,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processArContentItems($view, $contentItems)
    {
        foreach ($contentItems as $contentData) {
            $view->arContent()->create([
                'name' => $contentData['name'],
                'type' => $contentData['type'],
                'model_path' => $contentData['model_path'] ?? null,
                'texture_path' => $contentData['texture_path'] ?? null,
                'position' => $contentData['position'],
                'rotation' => $contentData['rotation'] ?? [0, 0, 0],
                'scale' => $contentData['scale'] ?? [1, 1, 1],
                'animation' => $contentData['animation'] ?? null,
                'interaction_type' => $contentData['interaction_type'] ?? 'none',
                'content_metadata' => $contentData['metadata'] ?? [],
            ]);
        }
    }

    private function setupTrackingTargets($view, $targets)
    {
        foreach ($targets as $targetData) {
            $view->trackingTargets()->create([
                'name' => $targetData['name'],
                'type' => $targetData['type'],
                'target_image' => $targetData['target_image'] ?? null,
                'tracking_data' => $targetData['tracking_data'] ?? [],
                'confidence_threshold' => $targetData['confidence_threshold'] ?? 0.8,
                'tracking_quality' => $targetData['tracking_quality'] ?? 'medium',
                'target_metadata' => $targetData['metadata'] ?? [],
            ]);
        }
    }

    private function configureInteractionZones($view, $zones)
    {
        foreach ($zones as $zoneData) {
            $view->interactionZones()->create([
                'name' => $zoneData['name'],
                'type' => $zoneData['type'],
                'position' => $zoneData['position'],
                'size' => $zoneData['size'],
                'trigger_type' => $zoneData['trigger_type'],
                'action' => $zoneData['action'],
                'parameters' => $zoneData['parameters'] ?? [],
                'zone_metadata' => $zoneData['metadata'] ?? [],
            ]);
        }
    }

    private function initializeArSession($view)
    {
        return [
            'session_id' => uniqid('ar_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'view_id' => $view->id,
            'device_info' => $this->getDeviceInfo(),
            'tracking_config' => $view->ar_metadata['tracking_features'] ?? [],
            'settings' => [
                'quality' => 'auto',
                'tracking' => 'enabled',
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
            'ar_capabilities' => $this->detectArCapabilities(),
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/AR|Magic|Holo/i', $userAgent)) {
            return 'ar_glasses';
        } elseif (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function detectArCapabilities()
    {
        // Check for AR support based on user agent and headers
        $userAgent = request()->userAgent();
        $capabilities = [];

        if (preg_match('/ARKit/i', $userAgent)) {
            $capabilities[] = 'arkit';
        }
        
        if (preg_match('/ARCore/i', $userAgent)) {
            $capabilities[] = 'arcore';
        }

        if (preg_match('/WebXR/i', $userAgent)) {
            $capabilities[] = 'webxr';
        }

        return $capabilities;
    }

    private function getAverageSessionDuration()
    {
        return ArPropertyView::avg('average_session_duration') ?? 0;
    }

    private function getTotalInteractions()
    {
        return 1250; // Placeholder - would calculate from interactions table
    }

    private function getDeviceDistribution()
    {
        return [
            'mobile' => 45,
            'tablet' => 25,
            'ar_glasses' => 20,
            'desktop' => 10,
        ];
    }

    private function getViewTrends()
    {
        return [
            'daily_views' => ArPropertyView::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_view_modes' => ArPropertyView::select('view_mode', DB::raw('COUNT(*) as count'))
                ->groupBy('view_mode')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'tracking_accuracy' => 92.5,
            'rendering_fps' => 58,
            'load_time' => 3.2,
            'user_satisfaction' => 4.1,
        ];
    }

    private function getViewAnalytics($view)
    {
        return [
            'total_views' => $view->launch_count,
            'unique_visitors' => $view->analytics()->distinct('user_id')->count(),
            'average_session_duration' => $view->average_session_duration,
            'most_interacted_content' => $this->getMostInteractedContent($view),
            'device_usage' => $this->getDeviceUsage($view),
        ];
    }

    private function getRelatedViews($view)
    {
        return ArPropertyView::where('property_id', $view->property_id)
            ->where('id', '!=', $view->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteArContentFiles($view)
    {
        // Delete associated files from storage
        if ($view->content_files) {
            foreach ($view->content_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function recordArInteraction($interactionData)
    {
        // Store interaction data for analytics
        // This would typically save to an ar_interactions table
    }

    private function generateArMarker($view)
    {
        return [
            'marker_id' => uniqid('marker_'),
            'marker_image' => $view->marker_image_path,
            'tracking_data' => $view->tracking_targets->first()->tracking_data ?? [],
            'download_url' => route('ar.property-viewer.download-marker', $view),
        ];
    }

    private function performTrackingCalibration($view, $request)
    {
        return [
            'calibration_id' => uniqid('cal_'),
            'accuracy_score' => 94.2,
            'tracking_quality' => 'excellent',
            'recommended_settings' => [
                'lighting' => 'optimal',
                'distance' => '2-3 meters',
                'angle' => '0-45 degrees',
            ],
        ];
    }

    private function analyzeDeviceCompatibility($request)
    {
        $userAgent = $request->userAgent();
        $compatibility = [
            'ar_support' => false,
            'tracking_type' => 'none',
            'performance_level' => 'low',
            'recommended_quality' => 'low',
        ];

        if (preg_match('/iPhone|Android/i', $userAgent)) {
            $compatibility['ar_support'] = true;
            $compatibility['tracking_type'] = 'plane_detection';
            $compatibility['performance_level'] = 'medium';
            $compatibility['recommended_quality'] = 'medium';
        }

        return $compatibility;
    }

    private function getDetailedViewAnalytics($view)
    {
        return [
            'view_analytics' => $this->getViewAnalytics($view),
            'interaction_analytics' => $this->getInteractionAnalytics($view),
            'performance_analytics' => $this->getPerformanceAnalytics($view),
            'device_analytics' => $this->getDeviceAnalytics($view),
        ];
    }

    private function getUserBehaviorAnalytics($view)
    {
        return [
            'interaction_patterns' => $this->getInteractionPatterns($view),
            'session_duration' => $this->getSessionDurationAnalytics($view),
            'content_engagement' => $this->getContentEngagement($view),
            'drop_off_points' => $this->getDropOffPoints($view),
        ];
    }

    private function getViewPerformanceMetrics($view)
    {
        return [
            'tracking_performance' => $this->getTrackingPerformance($view),
            'rendering_performance' => $this->getRenderingPerformance($view),
            'device_performance' => $this->getDevicePerformance($view),
            'quality_metrics' => $this->getQualityMetrics($view),
        ];
    }

    private function prepareViewExport($view, $format)
    {
        $data = [
            'view' => $view->toArray(),
            'ar_content' => $view->arContent->toArray(),
            'tracking_targets' => $view->trackingTargets->toArray(),
            'interaction_zones' => $view->interactionZones->toArray(),
            'analytics' => $this->getViewAnalytics($view),
        ];

        if ($format === 'json') {
            $filename = 'ar_view_' . $view->id . '.json';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            // Handle other formats
            $filename = 'ar_view_' . $view->id . '.txt';
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
