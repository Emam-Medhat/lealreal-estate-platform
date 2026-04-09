<?php

namespace App\Http\Controllers;

use App\Models\AiVirtualStaging;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AiVirtualStagingController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_stagings' => AiVirtualStaging::count(),
            'completed_stagings' => AiVirtualStaging::where('status', 'completed')->count(),
            'pending_stagings' => AiVirtualStaging::where('status', 'pending')->count(),
            'average_quality_score' => $this->getAverageQualityScore(),
            'total_rooms_staged' => $this->getTotalRoomsStaged(),
            'staging_success_rate' => $this->getStagingSuccessRate(),
        ];

        $recentStagings = AiVirtualStaging::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $stagingTrends = $this->getStagingTrends();
        $performanceMetrics = $this->getPerformanceMetrics();

        return view('ai.virtual-staging.dashboard', compact(
            'stats', 
            'recentStagings', 
            'stagingTrends', 
            'performanceMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiVirtualStaging::with(['property', 'user']);

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('quality_score_min')) {
            $query->where('quality_score', '>=', $request->quality_score_min);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $stagings = $query->latest()->paginate(20);

        $properties = Property::all();
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        return view('ai.virtual-staging.index', compact('stagings', 'properties', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $stagingStyles = $this->getStagingStyles();
        $roomTypes = $this->getRoomTypes();

        return view('ai.virtual-staging.create', compact('properties', 'stagingStyles', 'roomTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'staging_style' => 'required|string|in:' . implode(',', array_keys($this->getStagingStyles())),
            'room_type' => 'required|string|in:' . implode(',', array_keys($this->getRoomTypes())),
            'original_image' => 'required|image|mimes:jpeg,png,jpg|max:10240',
            'staging_preferences' => 'required|array',
            'furniture_style' => 'required|string',
            'color_scheme' => 'required|array',
            'budget_range' => 'required|array',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $style = $validated['staging_style'];
        $roomType = $validated['room_type'];

        // Process uploaded image
        $originalImagePath = $validated['original_image']->store('ai-staging/original/' . date('Y/m'), 'public');

        $staging = AiVirtualStaging::create([
            'property_id' => $validated['property_id'],
            'user_id' => auth()->id(),
            'staging_style' => $style,
            'room_type' => $roomType,
            'original_image_path' => $originalImagePath,
            'staging_preferences' => $validated['staging_preferences'],
            'furniture_style' => $validated['furniture_style'],
            'color_scheme' => $validated['color_scheme'],
            'budget_range' => $validated['budget_range'],
            'staged_image_path' => null,
            'quality_score' => 0,
            'processing_time' => 0,
            'notes' => $validated['notes'],
            'status' => 'pending',
            'metadata' => [
                'model_version' => 'v1.0',
                'style_used' => $style,
                'room_type' => $roomType,
                'created_at' => now(),
            ],
        ]);

        // Trigger AI virtual staging process
        $this->processVirtualStaging($staging);

        return redirect()->route('ai.virtual-staging.show', $staging)
            ->with('success', 'تم إنشاء التدريب الافتراضي بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiVirtualStaging $staging)
    {
        $staging->load(['property', 'user', 'metadata']);
        
        $stagingDetails = $this->getStagingDetails($staging);
        $qualityAnalysis = $this->getQualityAnalysis($staging);
        $styleRecommendations = $this->getStyleRecommendations($staging);

        return view('ai.virtual-staging.show', compact(
            'staging', 
            'stagingDetails', 
            'qualityAnalysis', 
            'styleRecommendations'
        ));
    }

    public function edit(AiVirtualStaging $staging)
    {
        if ($staging->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التدريب المكتمل');
        }

        $properties = Property::all();
        $stagingStyles = $this->getStagingStyles();
        $roomTypes = $this->getRoomTypes();

        return view('ai.virtual-staging.edit', compact('staging', 'properties', 'stagingStyles', 'roomTypes'));
    }

    public function update(Request $request, AiVirtualStaging $staging)
    {
        if ($staging->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل التدريب المكتمل');
        }

        $validated = $request->validate([
            'staging_preferences' => 'nullable|array',
            'furniture_style' => 'nullable|string',
            'color_scheme' => 'nullable|array',
            'budget_range' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $staging->update([
            'staging_preferences' => $validated['staging_preferences'] ?? $staging->staging_preferences,
            'furniture_style' => $validated['furniture_style'] ?? $staging->furniture_style,
            'color_scheme' => $validated['color_scheme'] ?? $staging->color_scheme,
            'budget_range' => $validated['budget_range'] ?? $staging->budget_range,
            'notes' => $validated['notes'] ?? $staging->notes,
            'metadata' => array_merge($staging->metadata, [
                'updated_at' => now(),
                'preferences_updated' => true,
            ]),
        ]);

        // Re-process staging with updated preferences
        $this->processVirtualStaging($staging);

        return redirect()->route('ai.virtual-staging.show', $staging)
            ->with('success', 'تم تحديث التدريب الافتراضي بنجاح');
    }

    public function destroy(AiVirtualStaging $staging)
    {
        if ($staging->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف التدريب المكتمل');
        }

        // Delete associated images
        if ($staging->original_image_path) {
            Storage::disk('public')->delete($staging->original_image_path);
        }
        if ($staging->staged_image_path) {
            Storage::disk('public')->delete($staging->staged_image_path);
        }

        $staging->delete();

        return redirect()->route('ai.virtual-staging.index')
            ->with('success', 'تم حذف التدريب الافتراضي بنجاح');
    }

    public function stage(AiVirtualStaging $staging)
    {
        $stagingResults = $this->performVirtualStaging($staging);
        
        $staging->update([
            'staged_image_path' => $stagingResults['staged_image_path'],
            'quality_score' => $stagingResults['quality_score'],
            'processing_time' => $stagingResults['processing_time'],
            'status' => 'completed',
            'metadata' => array_merge($staging->metadata, [
                'staging_results' => $stagingResults,
                'staging_date' => now(),
                'model_used' => $stagingResults['model_used'],
            ]),
        ]);

        return response()->json([
            'success' => true,
            'staging' => $staging->fresh(),
            'results' => $stagingResults,
        ]);
    }

    public function enhance(AiVirtualStaging $staging)
    {
        $enhancementResults = $this->performImageEnhancement($staging);
        
        $staging->update([
            'metadata' => array_merge($staging->metadata, [
                'enhancement_results' => $enhancementResults,
                'enhancement_date' => now(),
            ]),
        ]);

        return response()->json([
            'success' => true,
            'enhancement' => $enhancementResults,
            'updated_staging' => $staging->fresh(),
        ]);
    }

    public function compare(AiVirtualStaging $staging)
    {
        $comparisonResults = $this->performImageComparison($staging);
        
        return response()->json([
            'success' => true,
            'comparison' => $comparisonResults,
            'staging' => $staging,
        ]);
    }

    public function alternatives(AiVirtualStaging $staging)
    {
        $alternatives = $this->generateAlternativeStagings($staging);
        
        return response()->json([
            'success' => true,
            'alternatives' => $alternatives,
            'staging' => $staging,
        ]);
    }

    // Helper Methods
    private function getAverageQualityScore(): float
    {
        return AiVirtualStaging::where('status', 'completed')
            ->avg('quality_score') ?? 0;
    }

    private function getTotalRoomsStaged(): int
    {
        return AiVirtualStaging::where('status', 'completed')
            ->count();
    }

    private function getStagingSuccessRate(): float
    {
        $total = AiVirtualStaging::count();
        $completed = AiVirtualStaging::where('status', 'completed')->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getStagingTrends(): array
    {
        return AiVirtualStaging::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(quality_score) as avg_quality')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPerformanceMetrics(): array
    {
        return [
            'total_stagings' => AiVirtualStaging::count(),
            'high_quality_stagings' => AiVirtualStaging::where('quality_score', '>=', 0.8)->count(),
            'average_quality_score' => AiVirtualStaging::avg('quality_score') ?? 0,
            'style_performance' => $this->getStylePerformance(),
            'room_type_performance' => $this->getRoomTypePerformance(),
        ];
    }

    private function getStylePerformance(): array
    {
        return [
            'modern' => 0.92,
            'contemporary' => 0.88,
            'traditional' => 0.85,
            'minimalist' => 0.90,
            'industrial' => 0.87,
            'scandinavian' => 0.89,
        ];
    }

    private function getRoomTypePerformance(): array
    {
        return [
            'living_room' => 0.91,
            'bedroom' => 0.89,
            'kitchen' => 0.87,
            'bathroom' => 0.85,
            'dining_room' => 0.88,
            'office' => 0.86,
        ];
    }

    private function getStagingStyles(): array
    {
        return [
            'modern' => 'Modern',
            'contemporary' => 'Contemporary',
            'traditional' => 'Traditional',
            'minimalist' => 'Minimalist',
            'industrial' => 'Industrial',
            'scandinavian' => 'Scandinavian',
            'bohemian' => 'Bohemian',
            'rustic' => 'Rustic',
        ];
    }

    private function getRoomTypes(): array
    {
        return [
            'living_room' => 'Living Room',
            'bedroom' => 'Bedroom',
            'kitchen' => 'Kitchen',
            'bathroom' => 'Bathroom',
            'dining_room' => 'Dining Room',
            'office' => 'Office',
            'entryway' => 'Entryway',
            'basement' => 'Basement',
        ];
    }

    private function processVirtualStaging(AiVirtualStaging $staging): void
    {
        // Simulate AI virtual staging process
        $this->sendAiRequest($staging, 'stage', [
            'property_id' => $staging->property_id,
            'style' => $staging->staging_style,
            'room_type' => $staging->room_type,
            'original_image' => $staging->original_image_path,
            'preferences' => $staging->staging_preferences,
            'furniture_style' => $staging->furniture_style,
            'color_scheme' => $staging->color_scheme,
            'budget_range' => $staging->budget_range,
        ]);

        // Update status to processing
        $staging->update(['status' => 'processing']);
    }

    private function sendAiRequest(AiVirtualStaging $staging, string $action, array $data = []): void
    {
        // In a real implementation, this would call an AI service
        // For now, we'll simulate the AI response
        $mockResponse = [
            'success' => true,
            'action' => $action,
            'data' => $data,
            'response' => 'AI processing ' . ucfirst($action),
        ];

        // Update staging with AI results
        if ($mockResponse['success']) {
            $staging->update([
                'metadata' => array_merge($staging->metadata, [
                    'ai_response' => $mockResponse,
                    'ai_response_date' => now(),
                ]),
            ]);
        }
    }

    private function performVirtualStaging(AiVirtualStaging $staging): array
    {
        $startTime = microtime(true);
        
        // Simulate AI virtual staging
        $stagedImagePath = $this->generateStagedImage($staging);
        $qualityScore = $this->calculateQualityScore($staging);
        
        $processingTime = microtime(true) - $startTime;

        return [
            'staged_image_path' => $stagedImagePath,
            'quality_score' => $qualityScore,
            'processing_time' => $processingTime,
            'model_used' => 'gan_v2',
            'staging_date' => now(),
            'furniture_items' => $this->getFurnitureItems($staging),
            'color_analysis' => $this->analyzeColors($staging),
            'style_compliance' => $this->checkStyleCompliance($staging),
        ];
    }

    private function generateStagedImage(AiVirtualStaging $staging): string
    {
        // In a real implementation, this would generate a staged image
        // For now, we'll simulate by copying the original image
        $originalPath = $staging->original_image_path;
        $stagedPath = 'ai-staging/staged/' . date('Y/m') . '/' . uniqid() . '.jpg';
        
        // Simulate image processing
        Storage::disk('public')->copy($originalPath, $stagedPath);
        
        return $stagedPath;
    }

    private function calculateQualityScore(AiVirtualStaging $staging): float
    {
        $styleScore = $this->getStyleQualityScore($staging->staging_style);
        $roomScore = $this->getRoomQualityScore($staging->room_type);
        $preferenceScore = $this->getPreferenceQualityScore($staging->staging_preferences);
        $colorScore = $this->getColorQualityScore($staging->color_scheme);

        return ($styleScore + $roomScore + $preferenceScore + $colorScore) / 4;
    }

    private function getStyleQualityScore(string $style): float
    {
        $scores = [
            'modern' => 0.92,
            'contemporary' => 0.88,
            'traditional' => 0.85,
            'minimalist' => 0.90,
            'industrial' => 0.87,
            'scandinavian' => 0.89,
        ];

        return $scores[$style] ?? 0.85;
    }

    private function getRoomQualityScore(string $roomType): float
    {
        $scores = [
            'living_room' => 0.91,
            'bedroom' => 0.89,
            'kitchen' => 0.87,
            'bathroom' => 0.85,
            'dining_room' => 0.88,
            'office' => 0.86,
        ];

        return $scores[$roomType] ?? 0.85;
    }

    private function getPreferenceQualityScore(array $preferences): float
    {
        // Simulate preference quality assessment
        return 0.85;
    }

    private function getColorQualityScore(array $colorScheme): float
    {
        // Simulate color quality assessment
        return 0.88;
    }

    private function getFurnitureItems(AiVirtualStaging $staging): array
    {
        $furniture = [
            'living_room' => ['sofa', 'coffee_table', 'tv_stand', 'bookshelf', 'lamps'],
            'bedroom' => ['bed', 'nightstand', 'dresser', 'wardrobe', 'mirror'],
            'kitchen' => ['dining_table', 'chairs', 'cabinets', 'island', 'appliances'],
            'bathroom' => ['vanity', 'mirror', 'shower', 'bathtub', 'storage'],
            'dining_room' => ['dining_table', 'chairs', 'buffet', 'chandelier', 'artwork'],
            'office' => ['desk', 'chair', 'bookshelf', 'filing_cabinet', 'lamp'],
        ];

        $roomFurniture = $furniture[$staging->room_type] ?? [];
        $selectedFurniture = array_rand($roomFurniture, min(3, count($roomFurniture)));

        $items = [];
        foreach ($selectedFurniture as $index) {
            $items[] = [
                'item' => $roomFurniture[$index],
                'style' => $staging->furniture_style,
                'position' => ['x' => rand(10, 200), 'y' => rand(10, 200)],
                'confidence' => rand(75, 95) / 100,
            ];
        }

        return $items;
    }

    private function analyzeColors(AiVirtualStaging $staging): array
    {
        $colorScheme = $staging->color_scheme;
        
        return [
            'primary_colors' => $colorScheme['primary'] ?? ['#FFFFFF', '#F5F5F5'],
            'accent_colors' => $colorScheme['accent'] ?? ['#333333', '#666666'],
            'color_harmony' => 0.85,
            'brightness_level' => 'medium',
            'contrast_ratio' => 0.75,
        ];
    }

    private function checkStyleCompliance(AiVirtualStaging $staging): array
    {
        return [
            'style_match' => 0.88,
            'furniture_compliance' => 0.92,
            'color_compliance' => 0.85,
            'layout_compliance' => 0.90,
            'overall_compliance' => 0.89,
        ];
    }

    private function performImageEnhancement(AiVirtualStaging $staging): array
    {
        return [
            'brightness_adjusted' => true,
            'contrast_enhanced' => true,
            'color_corrected' => true,
            'noise_reduced' => true,
            'sharpness_improved' => true,
            'quality_improvement' => rand(15, 25),
            'processing_time' => rand(1, 3),
        ];
    }

    private function performImageComparison(AiVirtualStaging $staging): array
    {
        return [
            'original_vs_staged' => [
                'similarity_score' => rand(60, 80) / 100,
                'differences' => ['furniture_added', 'colors_changed', 'lighting_improved'],
                'improvement_areas' => ['furniture_arrangement', 'color_scheme', 'lighting'],
            ],
            'before_after_analysis' => [
                'space_utilization' => rand(70, 90) / 100,
                'visual_appeal' => rand(75, 95) / 100,
                'functionality' => rand(80, 95) / 100,
            ],
        ];
    }

    private function generateAlternativeStagings(AiVirtualStaging $staging): array
    {
        $alternatives = [];
        $styles = array_keys($this->getStagingStyles());
        
        // Generate 2-3 alternative styles
        $alternativeStyles = array_diff($styles, [$staging->staging_style]);
        $selectedStyles = array_rand($alternativeStyles, min(3, count($alternativeStyles)));

        foreach ($selectedStyles as $index) {
            $style = $alternativeStyles[$index];
            $alternatives[] = [
                'style' => $style,
                'quality_score' => $this->getStyleQualityScore($style),
                'estimated_cost' => rand(500, 3000),
                'processing_time' => rand(2, 5),
                'preview_path' => 'ai-staging/previews/' . uniqid() . '.jpg',
            ];
        }

        return $alternatives;
    }

    private function getStagingDetails(AiVirtualStaging $staging): array
    {
        return [
            'property_id' => $staging->property_id,
            'property' => [
                'id' => $staging->property->id,
                'title' => $staging->property->title,
                'type' => $staging->property->type,
                'location' => $staging->property->location,
            ],
            'staging_style' => $staging->staging_style,
            'room_type' => $staging->room_type,
            'original_image_path' => $staging->original_image_path,
            'staged_image_path' => $staging->staged_image_path,
            'staging_preferences' => $staging->staging_preferences,
            'furniture_style' => $staging->furniture_style,
            'color_scheme' => $staging->color_scheme,
            'budget_range' => $staging->budget_range,
            'quality_score' => $staging->quality_score,
            'processing_time' => $staging->processing_time,
            'metadata' => $staging->metadata,
            'created_at' => $staging->created_at,
            'updated_at' => $staging->updated_at,
        ];
    }

    private function getQualityAnalysis(AiVirtualStaging $staging): array
    {
        return [
            'overall_quality' => $staging->quality_score,
            'style_quality' => $this->getStyleQualityScore($staging->staging_style),
            'room_quality' => $this->getRoomQualityScore($staging->room_type),
            'preference_quality' => $this->getPreferenceQualityScore($staging->staging_preferences),
            'color_quality' => $this->getColorQualityScore($staging->color_scheme),
            'recommendations' => $this->generateQualityRecommendations($staging),
        ];
    }

    private function getStyleRecommendations(AiVirtualStaging $staging): array
    {
        return [
            'furniture_suggestions' => $this->getFurnitureSuggestions($staging),
            'color_recommendations' => $this->getColorRecommendations($staging),
            'layout_suggestions' => $this->getLayoutSuggestions($staging),
            'accessory_recommendations' => $this->getAccessoryRecommendations($staging),
        ];
    }

    private function getFurnitureSuggestions(AiVirtualStaging $staging): array
    {
        $suggestions = [
            'living_room' => ['modern_sofa', 'glass_coffee_table', 'minimalist_shelving'],
            'bedroom' => ['platform_bed', 'nightstands', 'wardrobe'],
            'kitchen' => ['island', 'bar_stools', 'modern_cabinets'],
            'bathroom' => ['vanity', 'frameless_mirror', 'glass_shower'],
        ];

        return $suggestions[$staging->room_type] ?? [];
    }

    private function getColorRecommendations(AiVirtualStaging $staging): array
    {
        return [
            'wall_colors' => ['#FFFFFF', '#F5F5F5', '#E8E8E8'],
            'accent_colors' => ['#333333', '#666666', '#999999'],
            'furniture_colors' => ['#8B4513', '#D2691E', '#A0522D'],
        ];
    }

    private function getLayoutSuggestions(AiVirtualStaging $staging): array
    {
        return [
            'furniture_arrangement' => 'create_open_flow',
            'lighting_placement' => 'natural_light_priority',
            'space_utilization' => 'maximize_floor_space',
            'traffic_flow' => 'clear_pathways',
        ];
    }

    private function getAccessoryRecommendations(AiVirtualStaging $staging): array
    {
        return [
            'decorative_items' => ['artwork', 'plants', 'mirrors'],
            'lighting_fixtures' => ['pendant_lights', 'floor_lamps', 'table_lamps'],
            'textiles' => ['rugs', 'curtains', 'cushions'],
            'storage_solutions' => ['built_in_storage', 'decorative_boxes'],
        ];
    }

    private function generateQualityRecommendations(AiVirtualStaging $staging): array
    {
        $recommendations = [];

        if ($staging->quality_score < 0.7) {
            $recommendations[] = 'تحسين جودة الصورة الأصلية';
        }

        if ($this->getStyleQualityScore($staging->staging_style) < 0.8) {
            $recommendations[] = 'تعديل نمط الأثاث ليتناسب مع النمط المختار';
        }

        if ($this->getColorQualityScore($staging->color_scheme) < 0.8) {
            $recommendations[] = 'تحسين تنسيق الألوان';
        }

        return $recommendations;
    }
}
