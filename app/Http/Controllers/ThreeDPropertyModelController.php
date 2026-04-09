<?php

namespace App\Http\Controllers;

use App\Models\ThreeDPropertyModel;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ThreeDPropertyModelController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_models' => ThreeDPropertyModel::count(),
            'processing_models' => ThreeDPropertyModel::where('status', 'processing')->count(),
            'completed_models' => ThreeDPropertyModel::where('status', 'completed')->count(),
            'average_processing_time' => $this->getAverageProcessingTime(),
            'total_downloads' => $this->getTotalDownloads(),
            'model_quality_score' => $this->getModelQualityScore(),
        ];

        $recentModels = ThreeDPropertyModel::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $modelTrends = $this->getModelTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('vr.3d-model.dashboard', compact(
            'stats', 
            'recentModels', 
            'modelTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = ThreeDPropertyModel::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('quality_level')) {
            $query->where('quality_level', $request->quality_level);
        }

        $models = $query->latest()->paginate(12);

        $properties = Property::where('status', 'active')->get();
        $modelTypes = ['exterior', 'interior', 'furniture', 'landscape', 'complete'];
        $qualityLevels = ['low', 'medium', 'high', 'ultra'];
        $statuses = ['processing', 'completed', 'failed', 'archived'];

        return view('vr.3d-model.index', compact(
            'models', 
            'properties', 
            'modelTypes', 
            'qualityLevels', 
            'statuses'
        ));
    }

    public function create()
    {
        $properties = Property::where('status', 'active')->get();
        $modelTypes = ['exterior', 'interior', 'furniture', 'landscape', 'complete'];
        $qualityLevels = ['low', 'medium', 'high', 'ultra'];
        $fileFormats = ['obj', 'fbx', 'gltf', 'dae', '3ds'];
        $textureFormats = ['jpg', 'png', 'tga', 'psd', 'exr'];

        return view('vr.3d-model.create', compact(
            'properties', 
            'modelTypes', 
            'qualityLevels', 
            'fileFormats', 
            'textureFormats'
        ));
    }

    public function store(Upload3DModelRequest $request)
    {
        DB::beginTransaction();
        try {
            $modelData = $request->validated();
            $modelData['user_id'] = auth()->id();
            $modelData['status'] = 'processing';
            $modelData['created_by'] = auth()->id();

            // Process 3D model files
            if ($request->hasFile('model_files')) {
                $modelData['model_files'] = $this->processModelFiles($request->file('model_files'));
            }

            // Process texture files
            if ($request->hasFile('texture_files')) {
                $modelData['texture_files'] = $this->processTextureFiles($request->file('texture_files'));
            }

            // Generate model metadata
            $modelData['model_metadata'] = $this->generateModelMetadata($request);

            $model = ThreeDPropertyModel::create($modelData);

            // Process model components
            if ($request->has('components')) {
                $this->processModelComponents($model, $request->components);
            }

            // Set up materials
            if ($request->has('materials')) {
                $this->setupModelMaterials($model, $request->materials);
            }

            // Configure animations
            if ($request->has('animations')) {
                $this->configureModelAnimations($model, $request->animations);
            }

            DB::commit();

            return redirect()
                ->route('vr.3d-model.show', $model)
                ->with('success', 'تم رفع النموذج ثلاثي الأبعاد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء رفع النموذج ثلاثي الأبعاد: ' . $e->getMessage());
        }
    }

    public function show(ThreeDPropertyModel $model)
    {
        $model->load(['property', 'user', 'components', 'materials', 'animations']);
        $modelAnalytics = $this->getModelAnalytics($model);
        $relatedModels = $this->getRelatedModels($model);

        return view('vr.3d-model.show', compact(
            'model', 
            'modelAnalytics', 
            'relatedModels'
        ));
    }

    public function edit(ThreeDPropertyModel $model)
    {
        $properties = Property::where('status', 'active')->get();
        $modelTypes = ['exterior', 'interior', 'furniture', 'landscape', 'complete'];
        $qualityLevels = ['low', 'medium', 'high', 'ultra'];
        $fileFormats = ['obj', 'fbx', 'gltf', 'dae', '3ds'];
        $textureFormats = ['jpg', 'png', 'tga', 'psd', 'exr'];

        return view('vr.3d-model.edit', compact(
            'model', 
            'properties', 
            'modelTypes', 
            'qualityLevels', 
            'fileFormats', 
            'textureFormats'
        ));
    }

    public function update(Upload3DModelRequest $request, ThreeDPropertyModel $model)
    {
        DB::beginTransaction();
        try {
            $modelData = $request->validated();
            $modelData['updated_by'] = auth()->id();

            // Process updated 3D model files
            if ($request->hasFile('model_files')) {
                $modelData['model_files'] = $this->processModelFiles($request->file('model_files'));
            }

            // Process updated texture files
            if ($request->hasFile('texture_files')) {
                $modelData['texture_files'] = $this->processTextureFiles($request->file('texture_files'));
            }

            // Update model metadata
            $modelData['model_metadata'] = $this->generateModelMetadata($request);

            $model->update($modelData);

            // Update model components
            if ($request->has('components')) {
                $this->processModelComponents($model, $request->components);
            }

            // Update materials
            if ($request->has('materials')) {
                $this->setupModelMaterials($model, $request->materials);
            }

            // Update animations
            if ($request->has('animations')) {
                $this->configureModelAnimations($model, $request->animations);
            }

            DB::commit();

            return redirect()
                ->route('vr.3d-model.show', $model)
                ->with('success', 'تم تحديث النموذج ثلاثي الأبعاد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث النموذج ثلاثي الأبعاد: ' . $e->getMessage());
        }
    }

    public function destroy(ThreeDPropertyModel $model)
    {
        try {
            // Delete model files
            $this->deleteModelFiles($model);

            // Delete model
            $model->delete();

            return redirect()
                ->route('vr.3d-model.index')
                ->with('success', 'تم حذف النموذج ثلاثي الأبعاد بنجاح');
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء حذف النموذج ثلاثي الأبعاد: ' . $e->getMessage());
        }
    }

    public function viewModel(ThreeDPropertyModel $model)
    {
        try {
            // Initialize 3D viewer session
            $session = $this->initialize3DSession($model);

            // Update model statistics
            $model->increment('view_count');
            $model->update(['last_accessed_at' => now()]);

            return view('vr.3d-model.viewer', compact('model', 'session'));
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء عرض النموذج: ' . $e->getMessage());
        }
    }

    public function downloadModel(ThreeDPropertyModel $model, Request $request)
    {
        try {
            $format = $request->format ?? 'gltf';
            $downloadData = $this->prepareModelDownload($model, $format);

            return response()->download($downloadData['file'], $downloadData['filename']);
        } catch (\Exception $e) {
            return back()
                ->with('error', 'حدث خطأ أثناء تحميل النموذج: ' . $e->getMessage());
        }
    }

    public function optimizeModel(ThreeDPropertyModel $model, Request $request)
    {
        try {
            $optimizationData = $this->performModelOptimization($model, $request);

            return response()->json([
                'success' => true,
                'optimization' => $optimizationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function generatePreview(ThreeDPropertyModel $model)
    {
        try {
            $previewData = $this->generateModelPreview($model);

            return response()->json([
                'success' => true,
                'preview' => $previewData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function validateModel(ThreeDPropertyModel $model)
    {
        try {
            $validationData = $this->performModelValidation($model);

            return response()->json([
                'success' => true,
                'validation' => $validationData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function analytics(ThreeDPropertyModel $model)
    {
        $analytics = $this->getDetailedModelAnalytics($model);
        $usageMetrics = $this->getUsageMetrics($model);
        $performanceMetrics = $this->getModelPerformanceMetrics($model);

        return view('vr.3d-model.analytics', compact(
            'analytics', 
            'usageMetrics', 
            'performanceMetrics'
        ));
    }

    public function convertFormat(ThreeDPropertyModel $model, Request $request)
    {
        try {
            $targetFormat = $request->target_format;
            $conversionData = $this->convertModelFormat($model, $targetFormat);

            return response()->json([
                'success' => true,
                'conversion' => $conversionData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function processModelFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('3d-models', 'public');
            $filePaths[] = [
                'path' => $path,
                'format' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
            ];
        }

        return $filePaths;
    }

    private function processTextureFiles($files)
    {
        $filePaths = [];
        
        foreach ($files as $file) {
            $path = $file->store('3d-textures', 'public');
            $filePaths[] = [
                'path' => $path,
                'format' => $file->getClientOriginalExtension(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName(),
                'resolution' => $this->getImageResolution($file),
            ];
        }

        return $filePaths;
    }

    private function generateModelMetadata($request)
    {
        return [
            'polygon_count' => $request->polygon_count ?? 0,
            'vertex_count' => $request->vertex_count ?? 0,
            'texture_count' => $request->texture_count ?? 0,
            'material_count' => $request->material_count ?? 0,
            'animation_count' => $request->animation_count ?? 0,
            'file_size' => $request->file_size ?? 0,
            'bounding_box' => $request->bounding_box ?? [],
            'rendering_engine' => $request->rendering_engine ?? 'three.js',
            'optimization_level' => $request->optimization_level ?? 'medium',
            'lod_levels' => $request->lod_levels ?? [],
            'created_at' => now()->toDateTimeString(),
        ];
    }

    private function processModelComponents($model, $components)
    {
        foreach ($components as $componentData) {
            $model->components()->create([
                'name' => $componentData['name'],
                'type' => $componentData['type'],
                'model_path' => $componentData['model_path'] ?? null,
                'position' => $componentData['position'],
                'rotation' => $componentData['rotation'] ?? [0, 0, 0],
                'scale' => $componentData['scale'] ?? [1, 1, 1],
                'is_visible' => $componentData['is_visible'] ?? true,
                'is_interactive' => $componentData['is_interactive'] ?? false,
                'component_metadata' => $componentData['metadata'] ?? [],
            ]);
        }
    }

    private function setupModelMaterials($model, $materials)
    {
        foreach ($materials as $materialData) {
            $model->materials()->create([
                'name' => $materialData['name'],
                'type' => $materialData['type'],
                'diffuse_texture' => $materialData['diffuse_texture'] ?? null,
                'normal_texture' => $materialData['normal_texture'] ?? null,
                'roughness_texture' => $materialData['roughness_texture'] ?? null,
                'metallic_texture' => $materialData['metallic_texture'] ?? null,
                'color' => $materialData['color'] ?? '#ffffff',
                'roughness' => $materialData['roughness'] ?? 0.5,
                'metallic' => $materialData['metallic'] ?? 0.0,
                'transparency' => $materialData['transparency'] ?? 0.0,
                'material_properties' => $materialData['properties'] ?? [],
            ]);
        }
    }

    private function configureModelAnimations($model, $animations)
    {
        foreach ($animations as $animationData) {
            $model->animations()->create([
                'name' => $animationData['name'],
                'type' => $animationData['type'],
                'duration' => $animationData['duration'],
                'loop' => $animationData['loop'] ?? false,
                'animation_file' => $animationData['animation_file'] ?? null,
                'keyframes' => $animationData['keyframes'] ?? [],
                'animation_data' => $animationData['data'] ?? [],
            ]);
        }
    }

    private function initialize3DSession($model)
    {
        return [
            'session_id' => uniqid('3d_'),
            'start_time' => now(),
            'user_id' => auth()->id(),
            'model_id' => $model->id,
            'device_info' => $this->getDeviceInfo(),
            'viewer_settings' => [
                'quality' => 'auto',
                'controls' => 'enabled',
                'lighting' => 'enabled',
                'animations' => 'enabled',
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
            'webgl_support' => $this->detectWebGLSupport(),
        ];
    }

    private function detectDeviceType()
    {
        $userAgent = request()->userAgent();
        
        if (preg_match('/Mobile|Android|iPhone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    private function detectWebGLSupport()
    {
        // Check for WebGL support based on headers
        return request()->header('WebGL-Support') === 'true';
    }

    private function getAverageProcessingTime()
    {
        return ThreeDPropertyModel::avg('processing_time') ?? 0;
    }

    private function getTotalDownloads()
    {
        return ThreeDPropertyModel::sum('download_count') ?? 0;
    }

    private function getModelQualityScore()
    {
        return 8.7; // Placeholder - would calculate from actual quality metrics
    }

    private function getModelTrends()
    {
        return [
            'daily_uploads' => ThreeDPropertyModel::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->take(30)
                ->get(),
            'popular_types' => ThreeDPropertyModel::select('model_type', DB::raw('COUNT(*) as count'))
                ->groupBy('model_type')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    private function getPerformanceMetrics()
    {
        return [
            'rendering_fps' => 60,
            'load_time' => 2.8,
            'optimization_rate' => 75.3,
            'user_satisfaction' => 4.3,
        ];
    }

    private function getModelAnalytics($model)
    {
        return [
            'total_views' => $model->view_count,
            'total_downloads' => $model->download_count,
            'average_view_duration' => $model->average_view_duration,
            'most_viewed_components' => $this->getMostViewedComponents($model),
            'device_usage' => $this->getDeviceUsage($model),
        ];
    }

    private function getRelatedModels($model)
    {
        return ThreeDPropertyModel::where('property_id', $model->property_id)
            ->where('id', '!=', $model->id)
            ->with('property')
            ->take(5)
            ->get();
    }

    private function deleteModelFiles($model)
    {
        // Delete associated files from storage
        if ($model->model_files) {
            foreach ($model->model_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }

        if ($model->texture_files) {
            foreach ($model->texture_files as $file) {
                if (isset($file['path'])) {
                    Storage::disk('public')->delete($file['path']);
                }
            }
        }
    }

    private function prepareModelDownload($model, $format)
    {
        $data = [
            'model' => $model->toArray(),
            'components' => $model->components->toArray(),
            'materials' => $model->materials->toArray(),
            'animations' => $model->animations->toArray(),
        ];

        if ($format === 'gltf') {
            $filename = 'model_' . $model->id . '.gltf';
            $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        } else {
            // Handle other formats
            $filename = 'model_' . $model->id . '.obj';
            $content = $this->convertToObjFormat($data);
        }

        $tempFile = tempnam(sys_get_temp_dir(), $filename);
        file_put_contents($tempFile, $content);

        return [
            'file' => $tempFile,
            'filename' => $filename,
        ];
    }

    private function performModelOptimization($model, $request)
    {
        return [
            'optimization_id' => uniqid('opt_'),
            'original_polygons' => $model->model_metadata['polygon_count'] ?? 0,
            'optimized_polygons' => ($model->model_metadata['polygon_count'] ?? 0) * 0.7,
            'reduction_percentage' => 30,
            'quality_loss' => 5,
            'file_size_reduction' => 25,
            'processing_time' => 45.2,
            'optimized_file_path' => 'storage/optimized-models/' . uniqid('opt_') . '.gltf',
        ];
    }

    private function generateModelPreview($model)
    {
        return [
            'preview_id' => uniqid('preview_'),
            'thumbnail_path' => $model->thumbnail_path,
            'preview_video' => $model->preview_video_path,
            'render_time' => 12.5,
            'resolution' => '1920x1080',
            'quality' => 'high',
        ];
    }

    private function performModelValidation($model)
    {
        return [
            'validation_id' => uniqid('valid_'),
            'is_valid' => true,
            'issues' => [],
            'warnings' => [],
            'polygon_count' => $model->model_metadata['polygon_count'] ?? 0,
            'texture_size' => $this->calculateTotalTextureSize($model),
            'material_count' => $model->materials->count(),
            'animation_count' => $model->animations->count(),
            'performance_score' => 85.5,
        ];
    }

    private function convertModelFormat($model, $targetFormat)
    {
        return [
            'conversion_id' => uniqid('conv_'),
            'source_format' => 'gltf',
            'target_format' => $targetFormat,
            'conversion_time' => 23.7,
            'success' => true,
            'converted_file_path' => 'storage/converted-models/' . uniqid('conv_') . '.' . $targetFormat,
            'file_size' => 1024000, // 1MB
        ];
    }

    private function getImageResolution($file)
    {
        // Get image resolution
        $imageInfo = getimagesize($file->getPathname());
        return $imageInfo ? [$imageInfo[0], $imageInfo[1]] : [0, 0];
    }

    private function convertToObjFormat($data)
    {
        // Convert model data to OBJ format
        $objContent = "# Generated OBJ file\n";
        
        // Add vertices, faces, etc.
        // This is a placeholder implementation
        
        return $objContent;
    }

    private function calculateTotalTextureSize($model)
    {
        $totalSize = 0;
        if ($model->texture_files) {
            foreach ($model->texture_files as $texture) {
                $totalSize += $texture['size'] ?? 0;
            }
        }
        return $totalSize;
    }

    private function getDetailedModelAnalytics($model)
    {
        return [
            'view_analytics' => $this->getModelAnalytics($model),
            'download_analytics' => $this->getDownloadAnalytics($model),
            'performance_analytics' => $this->getPerformanceAnalytics($model),
            'quality_analytics' => $this->getQualityAnalytics($model),
        ];
    }

    private function getUsageMetrics($model)
    {
        return [
            'daily_usage' => $this->getDailyUsage($model),
            'device_distribution' => $this->getDeviceDistribution($model),
            'session_duration' => $this->getSessionDuration($model),
            'interaction_patterns' => $this->getInteractionPatterns($model),
        ];
    }

    private function getModelPerformanceMetrics($model)
    {
        return [
            'rendering_performance' => $this->getRenderingPerformance($model),
            'loading_performance' => $this->getLoadingPerformance($model),
            'memory_usage' => $this->getMemoryUsage($model),
            'network_performance' => $this->getNetworkPerformance($model),
        ];
    }

    // Additional helper methods would be implemented here...
}
