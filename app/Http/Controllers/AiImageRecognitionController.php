<?php

namespace App\Http\Controllers;

use App\Models\AiImageAnalysis;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AiImageRecognitionController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_analyses' => AiImageAnalysis::count(),
            'completed_analyses' => AiImageAnalysis::where('status', 'completed')->count(),
            'pending_analyses' => AiImageAnalysis::where('status', 'pending')->count(),
            'average_confidence' => $this->getAverageConfidence(),
            'total_images_processed' => $this->getTotalImagesProcessed(),
            'recognition_success_rate' => $this->getRecognitionSuccessRate(),
        ];

        $recentAnalyses = AiImageAnalysis::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $analysisTrends = $this->getAnalysisTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ai.image-recognition.dashboard', compact(
            'stats', 
            'recentAnalyses', 
            'analysisTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiImageAnalysis::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('confidence_min')) {
            $query->where('confidence_score', '>=', $request->confidence_min);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $analyses = $query->latest()->paginate(20);

        $properties = Property::all();
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        return view('ai.image-recognition.index', compact('analyses', 'properties', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $recognitionModels = $this->getAvailableModels();
        $analysisTypes = $this->getAnalysisTypes();

        return view('ai.image-recognition.create', compact('properties', 'recognitionModels', 'analysisTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'recognition_model' => 'required|string|in:' . implode(',', array_keys($this->getAvailableModels())),
            'analysis_type' => 'required|string|in:' . implode(',', array_keys($this->getAnalysisTypes())),
            'images' => 'required|array|min:1',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            'analysis_parameters' => 'required|array',
            'detection_threshold' => 'required|numeric|min:0|max:1',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $model = $validated['recognition_model'];
        $analysisType = $validated['analysis_type'];

        // Process uploaded images
        $imagePaths = [];
        foreach ($validated['images'] as $image) {
            $path = $image->store('ai-images/' . date('Y/m'), 'public');
            $imagePaths[] = $path;
        }

        $analysis = AiImageAnalysis::create([
            'property_id' => $validated['property_id'],
            'user_id' => auth()->id(),
            'recognition_model' => $model,
            'analysis_type' => $analysisType,
            'image_paths' => $imagePaths,
            'analysis_parameters' => $validated['analysis_parameters'],
            'detection_threshold' => $validated['detection_threshold'],
            'confidence_score' => 0,
            'detected_objects' => [],
            'analysis_results' => [],
            'notes' => $validated['notes'],
            'status' => 'pending',
            'metadata' => [
                'model_version' => 'v1.0',
                'image_count' => count($imagePaths),
                'analysis_type' => $analysisType,
                'created_at' => now(),
            ],
        ]);

        // Trigger AI image analysis process
        $this->processImageAnalysis($analysis);

        return redirect()->route('ai.image-recognition.show', $analysis)
            ->with('success', 'تم إنشاء تحليل الصور بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiImageAnalysis $analysis)
    {
        $analysis->load(['property', 'user', 'metadata']);
        
        $analysisDetails = $this->getAnalysisDetails($analysis);
        $detectionResults = $this->getDetectionResults($analysis);
        $imageMetadata = $this->getImageMetadata($analysis);

        return view('ai.image-recognition.show', compact(
            'analysis', 
            'analysisDetails', 
            'detectionResults', 
            'imageMetadata'
        ));
    }

    public function edit(AiImageAnalysis $analysis)
    {
        if ($analysis->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التحليل المكتمل');
        }

        $properties = Property::all();
        $recognitionModels = $this->getAvailableModels();
        $analysisTypes = $this->getAnalysisTypes();

        return view('ai.image-recognition.edit', compact('analysis', 'properties', 'recognitionModels', 'analysisTypes'));
    }

    public function update(Request $request, AiImageAnalysis $analysis)
    {
        if ($analysis->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التحليل المكتمل');
        }

        $validated = $request->validate([
            'analysis_parameters' => 'nullable|array',
            'detection_threshold' => 'nullable|numeric|min:0|max:1',
            'notes' => 'nullable|string',
        ]);

        $analysis->update([
            'analysis_parameters' => $validated['analysis_parameters'] ?? $analysis->analysis_parameters,
            'detection_threshold' => $validated['detection_threshold'] ?? $analysis->detection_threshold,
            'notes' => $validated['notes'] ?? $analysis->notes,
            'metadata' => array_merge($analysis->metadata, [
                'updated_at' => now(),
                'parameters_updated' => true,
            ]),
        ]);

        // Re-process analysis with updated parameters
        $this->processImageAnalysis($analysis);

        return redirect()->route('ai.image-recognition.show', $analysis)
            ->with('success', 'تم تحديث تحليل الصور بنجاح');
    }

    public function destroy(AiImageAnalysis $analysis)
    {
        if ($analysis->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف التحليل المكتمل');
        }

        // Delete associated images
        foreach ($analysis->image_paths ?? [] as $imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $analysis->delete();

        return redirect()->route('ai.image-recognition.index')
            ->with('success', 'تم حذف تحليل الصور بنجاح');
    }

    public function analyze(AiImageAnalysis $analysis)
    {
        $analysisResults = $this->performImageAnalysis($analysis);
        
        $analysis->update([
            'detected_objects' => $analysisResults['detected_objects'],
            'analysis_results' => $analysisResults['analysis_results'],
            'confidence_score' => $analysisResults['confidence_score'],
            'status' => 'completed',
            'metadata' => array_merge($analysis->metadata, [
                'analysis_results' => $analysisResults,
                'analysis_date' => now(),
                'model_used' => $analysisResults['model_used'],
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis->fresh(),
            'results' => $analysisResults,
        ]);
    }

    public function detect(AiImageAnalysis $analysis)
    {
        $detectionResults = $this->performObjectDetection($analysis);
        
        $analysis->update([
            'detected_objects' => $detectionResults['objects'],
            'confidence_score' => $detectionResults['confidence_score'],
            'metadata' => array_merge($analysis->metadata, [
                'detection_results' => $detectionResults,
                'detection_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'detection' => $detectionResults,
            'updated_analysis' => $analysis->fresh(),
        ]);
    }

    public function classify(AiImageAnalysis $analysis)
    {
        $classificationResults = $this->performImageClassification($analysis);
        
        $analysis->update([
            'analysis_results' => array_merge($analysis->analysis_results ?? [], [
                'classification' => $classificationResults,
            ]),
            'metadata' => array_merge($analysis->metadata, [
                'classification_results' => $classificationResults,
                'classification_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'classification' => $classificationResults,
            'updated_analysis' => $analysis->fresh(),
        ]);
    }

    public function enhance(AiImageAnalysis $analysis)
    {
        $enhancementResults = $this->performImageEnhancement($analysis);
        
        $analysis->update([
            'analysis_results' => array_merge($analysis->analysis_results ?? [], [
                'enhancement' => $enhancementResults,
            ]),
            'metadata' => array_merge($analysis->metadata, [
                'enhancement_results' => $enhancementResults,
                'enhancement_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'enhancement' => $enhancementResults,
            'updated_analysis' => $analysis->fresh(),
        ]);
    }

    public function compare(AiImageAnalysis $analysis)
    {
        $comparisonResults = $this->performImageComparison($analysis);
        
        return response()->json([
            'success' => true,
            'comparison' => $comparisonResults,
            'analysis' => $analysis,
        ]);
    }

    // Helper Methods
    private function getAverageConfidence(): float
    {
        return AiImageAnalysis::where('status', 'completed')
            ->avg('confidence_score') ?? 0;
    }

    private function getTotalImagesProcessed(): int
    {
        return AiImageAnalysis::where('status', 'completed')
            ->sum(DB::raw('JSON_LENGTH(image_paths)'));
    }

    private function getRecognitionSuccessRate(): float
    {
        $total = AiImageAnalysis::count();
        $completed = AiImageAnalysis::where('status', 'completed')->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getAnalysisTrends(): array
    {
        return AiImageAnalysis::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(confidence_score) as avg_confidence')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'total_analyses' => AiImageAnalysis::count(),
            'high_confidence_analyses' => AiImageAnalysis::where('confidence_score', '>=', 0.8)->count(),
            'average_confidence' => AiImageAnalysis::avg('confidence_score') ?? 0,
            'model_performance' => $this->getModelPerformance(),
            'analysis_type_performance' => $this->getAnalysisTypePerformance(),
        ];
    }

    private function getModelPerformance(): array
    {
        return [
            'yolo' => 0.92,
            'resnet' => 0.88,
            'efficientnet' => 0.85,
            'vit' => 0.90,
            'mobilenet' => 0.82,
        ];
    }

    private function getAnalysisTypePerformance(): array
    {
        return [
            'object_detection' => 0.90,
            'image_classification' => 0.88,
            'scene_analysis' => 0.85,
            'quality_assessment' => 0.87,
            'feature_extraction' => 0.89,
        ];
    }

    private function getAvailableModels(): array
    {
        return [
            'yolo' => 'YOLO (You Only Look Once)',
            'resnet' => 'ResNet (Residual Network)',
            'efficientnet' => 'EfficientNet',
            'vit' => 'Vision Transformer (ViT)',
            'mobilenet' => 'MobileNet',
            'ssd' => 'SSD (Single Shot Detector)',
        ];
    }

    private function getAnalysisTypes(): array
    {
        return [
            'object_detection' => 'Object Detection',
            'image_classification' => 'Image Classification',
            'scene_analysis' => 'Scene Analysis',
            'quality_assessment' => 'Quality Assessment',
            'feature_extraction' => 'Feature Extraction',
            'damage_detection' => 'Damage Detection',
        ];
    }

    private function processImageAnalysis(AiImageAnalysis $analysis): void
    {
        // Simulate AI image analysis process
        $this->sendAiRequest($analysis, 'analyze', [
            'property_id' => $analysis->property_id,
            'model' => $analysis->recognition_model,
            'analysis_type' => $analysis->analysis_type,
            'image_paths' => $analysis->image_paths,
            'parameters' => $analysis->analysis_parameters,
            'threshold' => $analysis->detection_threshold,
        ]);

        // Update status to processing
        $analysis->update(['status' => 'processing']);
    }

    private function sendAiRequest(AiImageAnalysis $analysis, string $action, array $data = []): void
    {
        // In a real implementation, this would call an AI service
        // For now, we'll simulate the AI response
        $mockResponse = [
            'success' => true,
            'action' => $action,
            'data' => $data,
            'response' => 'AI processing ' . ucfirst($action),
        ];

        // Update analysis with AI results
        if ($mockResponse['success']) {
            $analysis->update([
                'metadata' => array_merge($analysis->metadata, [
                    'ai_response' => $mockResponse,
                    'ai_response_date' => now(),
                ]),
            ]);
        }
    }

    private function performImageAnalysis(AiImageAnalysis $analysis): array
    {
        $imagePaths = $analysis->image_paths;
        $model = $analysis->recognition_model;
        $analysisType = $analysis->analysis_type;
        $threshold = $analysis->detection_threshold;

        $detectedObjects = [];
        $analysisResults = [];
        $totalConfidence = 0;
        $processedImages = 0;

        foreach ($imagePaths as $imagePath) {
            $result = $this->analyzeImage($imagePath, $model, $analysisType, $threshold);
            
            $detectedObjects = array_merge($detectedObjects, $result['objects']);
            $analysisResults[] = $result['analysis'];
            $totalConfidence += $result['confidence'];
            $processedImages++;
        }

        $averageConfidence = $processedImages > 0 ? $totalConfidence / $processedImages : 0;

        return [
            'detected_objects' => $detectedObjects,
            'analysis_results' => $analysisResults,
            'confidence_score' => $averageConfidence,
            'model_used' => $model,
            'analysis_date' => now(),
            'processed_images' => $processedImages,
            'total_objects_detected' => count($detectedObjects),
        ];
    }

    private function analyzeImage(string $imagePath, string $model, string $analysisType, float $threshold): array
    {
        // Simulate AI image analysis
        $objects = $this->simulateObjectDetection($imagePath, $model, $threshold);
        $analysis = $this->simulateImageAnalysis($imagePath, $analysisType);
        $confidence = $this->calculateImageConfidence($imagePath, $model);

        return [
            'objects' => $objects,
            'analysis' => $analysis,
            'confidence' => $confidence,
            'image_path' => $imagePath,
        ];
    }

    private function simulateObjectDetection(string $imagePath, string $model, float $threshold): array
    {
        // Simulate object detection results
        $possibleObjects = [
            'building', 'house', 'apartment', 'room', 'kitchen', 'bathroom', 
            'bedroom', 'living_room', 'garage', 'garden', 'pool', 'terrace',
            'window', 'door', 'stairs', 'furniture', 'decoration'
        ];

        $detectedObjects = [];
        $numObjects = rand(2, 8);

        for ($i = 0; $i < $numObjects; $i++) {
            $object = $possibleObjects[array_rand($possibleObjects)];
            $confidence = rand(60, 95) / 100;

            if ($confidence >= $threshold) {
                $detectedObjects[] = [
                    'object' => $object,
                    'confidence' => $confidence,
                    'bbox' => [
                        'x' => rand(10, 200),
                        'y' => rand(10, 200),
                        'width' => rand(50, 150),
                        'height' => rand(50, 150),
                    ],
                    'attributes' => $this->getObjectAttributes($object),
                ];
            }
        }

        return $detectedObjects;
    }

    private function simulateImageAnalysis(string $imagePath, string $analysisType): array
    {
        $analyses = [
            'object_detection' => ['objects_found' => rand(3, 10), 'accuracy' => rand(75, 95)],
            'image_classification' => ['category' => 'interior', 'subcategory' => 'living_room'],
            'scene_analysis' => ['scene_type' => 'residential', 'lighting' => 'natural'],
            'quality_assessment' => ['quality_score' => rand(70, 95), 'resolution' => 'high'],
            'feature_extraction' => ['features' => ['modern', 'spacious', 'well-lit']],
            'damage_detection' => ['damage_found' => rand(0, 2), 'severity' => 'low'],
        ];

        return $analyses[$analysisType] ?? ['analysis' => 'completed'];
    }

    private function calculateImageConfidence(string $imagePath, string $model): float
    {
        $modelConfidence = [
            'yolo' => 0.92,
            'resnet' => 0.88,
            'efficientnet' => 0.85,
            'vit' => 0.90,
            'mobilenet' => 0.82,
        ];

        return $modelConfidence[$model] ?? 0.85;
    }

    private function getObjectAttributes(string $object): array
    {
        $attributes = [
            'building' => ['material' => 'concrete', 'condition' => 'good'],
            'room' => ['size' => 'medium', 'lighting' => 'natural'],
            'kitchen' => ['style' => 'modern', 'equipment' => 'full'],
            'bathroom' => ['size' => 'medium', 'condition' => 'excellent'],
            'bedroom' => ['size' => 'large', 'furniture' => 'included'],
            'garden' => ['size' => 'medium', 'maintenance' => 'good'],
            'pool' => ['size' => 'medium', 'condition' => 'clean'],
        ];

        return $attributes[$object] ?? [];
    }

    private function performObjectDetection(AiImageAnalysis $analysis): array
    {
        $objects = [];
        $totalConfidence = 0;

        foreach ($analysis->image_paths as $imagePath) {
            $detection = $this->simulateObjectDetection(
                $imagePath, 
                $analysis->recognition_model, 
                $analysis->detection_threshold
            );
            
            $objects = array_merge($objects, $detection);
            $totalConfidence += array_sum(array_column($detection, 'confidence'));
        }

        $averageConfidence = count($objects) > 0 ? $totalConfidence / count($objects) : 0;

        return [
            'objects' => $objects,
            'confidence_score' => $averageConfidence,
            'total_objects' => count($objects),
            'detection_summary' => $this->summarizeDetections($objects),
        ];
    }

    private function performImageClassification(AiImageAnalysis $analysis): array
    {
        $classifications = [];
        
        foreach ($analysis->image_paths as $imagePath) {
            $classifications[] = [
                'image_path' => $imagePath,
                'category' => 'interior',
                'subcategory' => 'living_room',
                'confidence' => rand(75, 95) / 100,
                'tags' => ['modern', 'spacious', 'well-lit'],
            ];
        }

        return [
            'classifications' => $classifications,
            'overall_category' => 'residential_property',
            'confidence' => rand(80, 90) / 100,
        ];
    }

    private function performImageEnhancement(AiImageAnalysis $analysis): array
    {
        $enhancements = [];
        
        foreach ($analysis->image_paths as $imagePath) {
            $enhancements[] = [
                'image_path' => $imagePath,
                'brightness_adjusted' => true,
                'contrast_enhanced' => true,
                'noise_reduced' => true,
                'sharpness_improved' => true,
                'quality_score' => rand(85, 95) / 100,
            ];
        }

        return [
            'enhancements' => $enhancements,
            'overall_quality_improvement' => rand(15, 25),
            'processing_time' => rand(2, 5),
        ];
    }

    private function performImageComparison(AiImageAnalysis $analysis): array
    {
        $comparisons = [];
        $imagePaths = $analysis->image_paths;

        for ($i = 0; $i < count($imagePaths) - 1; $i++) {
            for ($j = $i + 1; $j < count($imagePaths); $j++) {
                $comparisons[] = [
                    'image1' => $imagePaths[$i],
                    'image2' => $imagePaths[$j],
                    'similarity_score' => rand(60, 90) / 100,
                    'differences' => ['lighting', 'angle', 'objects'],
                ];
            }
        }

        return [
            'comparisons' => $comparisons,
            'average_similarity' => rand(70, 85) / 100,
            'total_comparisons' => count($comparisons),
        ];
    }

    private function summarizeDetections(array $objects): array
    {
        $summary = [];
        
        foreach ($objects as $object) {
            $name = $object['object'];
            if (!isset($summary[$name])) {
                $summary[$name] = [
                    'count' => 0,
                    'average_confidence' => 0,
                    'total_confidence' => 0,
                ];
            }
            
            $summary[$name]['count']++;
            $summary[$name]['total_confidence'] += $object['confidence'];
        }

        foreach ($summary as $name => &$data) {
            $data['average_confidence'] = $data['total_confidence'] / $data['count'];
        }

        return $summary;
    }

    private function getAnalysisDetails(AiImageAnalysis $analysis): array
    {
        return [
            'property_id' => $analysis->property_id,
            'property' => [
                'id' => $analysis->property->id,
                'title' => $analysis->property->title,
                'type' => $analysis->property->type,
                'location' => $analysis->property->location,
            ],
            'recognition_model' => $analysis->recognition_model,
            'analysis_type' => $analysis->analysis_type,
            'image_paths' => $analysis->image_paths,
            'image_count' => count($analysis->image_paths),
            'detection_threshold' => $analysis->detection_threshold,
            'confidence_score' => $analysis->confidence_score,
            'detected_objects' => $analysis->detected_objects,
            'analysis_results' => $analysis->analysis_results,
            'metadata' => $analysis->metadata,
            'created_at' => $analysis->created_at,
            'updated_at' => $analysis->updated_at,
        ];
    }

    private function getDetectionResults(AiImageAnalysis $analysis): array
    {
        return [
            'total_objects' => count($analysis->detected_objects),
            'object_categories' => $this->categorizeObjects($analysis->detected_objects),
            'confidence_distribution' => $this->getConfidenceDistribution($analysis->detected_objects),
            'detection_summary' => $this->summarizeDetections($analysis->detected_objects),
        ];
    }

    private function getImageMetadata(AiImageAnalysis $analysis): array
    {
        $metadata = [];
        
        foreach ($analysis->image_paths as $imagePath) {
            $metadata[] = [
                'path' => $imagePath,
                'size' => $this->getImageSize($imagePath),
                'dimensions' => $this->getImageDimensions($imagePath),
                'format' => $this->getImageFormat($imagePath),
                'upload_date' => $analysis->created_at,
            ];
        }

        return $metadata;
    }

    private function categorizeObjects(array $objects): array
    {
        $categories = [
            'structural' => ['building', 'wall', 'floor', 'ceiling'],
            'rooms' => ['room', 'kitchen', 'bathroom', 'bedroom', 'living_room'],
            'features' => ['window', 'door', 'stairs', 'garage'],
            'outdoor' => ['garden', 'pool', 'terrace', 'balcony'],
            'furniture' => ['furniture', 'sofa', 'table', 'chair'],
        ];

        $categorized = [];
        
        foreach ($objects as $object) {
            $name = $object['object'];
            
            foreach ($categories as $category => $items) {
                if (in_array($name, $items)) {
                    if (!isset($categorized[$category])) {
                        $categorized[$category] = [];
                    }
                    $categorized[$category][] = $object;
                    break;
                }
            }
        }

        return $categorized;
    }

    private function getConfidenceDistribution(array $objects): array
    {
        $distribution = [
            'high' => 0,    // >= 0.8
            'medium' => 0,  // 0.6 - 0.8
            'low' => 0,     // < 0.6
        ];

        foreach ($objects as $object) {
            $confidence = $object['confidence'];
            
            if ($confidence >= 0.8) {
                $distribution['high']++;
            } elseif ($confidence >= 0.6) {
                $distribution['medium']++;
            } else {
                $distribution['low']++;
            }
        }

        return $distribution;
    }

    private function getImageSize(string $imagePath): string
    {
        // Simulate getting image size
        return rand(100, 5000) . ' KB';
    }

    private function getImageDimensions(string $imagePath): array
    {
        // Simulate getting image dimensions
        return [
            'width' => rand(800, 4000),
            'height' => rand(600, 3000),
        ];
    }

    private function getImageFormat(string $imagePath): string
    {
        // Extract format from file extension
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        return strtoupper($extension);
    }
}
