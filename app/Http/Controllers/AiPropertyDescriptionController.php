<?php

namespace App\Http\Controllers;

use App\Models\AiGeneratedDescription;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AiPropertyDescriptionController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_descriptions' => AiGeneratedDescription::count(),
            'pending_descriptions' => AiGeneratedDescription::where('status', 'pending')->count(),
            'completed_descriptions' => AiGeneratedDescription::where('status', 'completed')->count(),
            'average_quality_score' => $this->getAverageQualityScore(),
            'total_properties_processed' => $this->getTotalPropertiesProcessed(),
            'success_rate' => $this->getSuccessRate(),
        ];

        $recentDescriptions = AiGeneratedDescription::with(['property', 'user'])
            ->latest()
            ->take(10)
            ->get();

        $descriptionTrends = $this->getDescriptionTrends();
        $qualityMetrics = $this->getQualityMetrics();

        return view('ai.property-description.dashboard', compact(
            'stats', 
            'recentDescriptions', 
            'descriptionTrends', 
            'qualityMetrics'
        ));
    }

    public function index(Request $request)
    {
        $query = AiGeneratedDescription::with(['property', 'user']);

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

        $descriptions = $query->latest()->paginate(20);

        $properties = Property::all();
        $statuses = ['pending', 'completed', 'failed', 'cancelled'];

        return view('ai.property-description.index', compact('descriptions', 'properties', 'statuses'));
    }

    public function create()
    {
        $properties = Property::all();
        $descriptionTemplates = $this->getAvailableTemplates();
        $writingStyles = $this->getWritingStyles();

        return view('ai.property-description.create', compact('properties', 'descriptionTemplates', 'writingStyles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'template' => 'required|string|in:' . implode(',', array_keys($this->getAvailableTemplates())),
            'writing_style' => 'required|string|in:' . implode(',', array_keys($this->getWritingStyles())),
            'target_audience' => 'required|string',
            'key_features' => 'required|array|min:3',
            'tone' => 'required|string|in:formal,casual,professional,friendly',
            'length' => 'required|integer|min:50|max:1000',
            'include_highlights' => 'boolean',
            'include_amenities' => 'boolean',
            'include_location_details' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $property = Property::findOrFail($validated['property_id']);
        $template = $validated['template'];
        $writingStyle = $validated['writing_style'];

        $description = AiGeneratedDescription::create([
            'property_id' => $validated['property_id'],
            'user_id' => auth()->id(),
            'template' => $template,
            'writing_style' => $writingStyle,
            'target_audience' => $validated['target_audience'],
            'key_features' => $validated['key_features'],
            'tone' => $validated['tone'],
            'length' => $validated['length'],
            'include_highlights' => $validated['include_highlights'] ?? false,
            'include_amenities' => $validated['include_amenities'] ?? false,
            'include_location_details' => $validated['include_location_details'] ?? false,
            'notes' => $validated['notes'],
            'status' => 'pending',
            'metadata' => [
                'model_version' => 'v1.0',
                'template_used' => $template,
                'style_used' => $writingStyle,
                'features_count' => count($validated['key_features']),
                'created_at' => now(),
            ],
        ]);

        // Trigger AI description generation
        $this->processDescription($description);

        return redirect()->route('ai.property-description.show', $description)
            ->with('success', 'تم إنشاء وصف العقار بالذكاء الاصطناعي بنجاح');
    }

    public function show(AiGeneratedDescription $description)
    {
        $description->load(['property', 'user', 'metadata']);
        
        $descriptionDetails = $this->getDescriptionDetails($description);
        $qualityAnalysis = $this->getQualityAnalysis($description);
        $similarDescriptions = $this->getSimilarDescriptions($description);

        return view('ai.property-description.show', compact(
            'description', 
            'descriptionDetails', 
            'qualityAnalysis', 
            'similarDescriptions'
        ));
    }

    public function edit(AiGeneratedDescription $description)
    {
        if ($description->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الوصف المكتمل');
        }

        $properties = Property::all();
        $descriptionTemplates = $this->getAvailableTemplates();
        $writingStyles = $this->getWritingStyles();

        return view('ai.property-description.edit', compact('description', 'properties', 'descriptionTemplates', 'writingStyles'));
    }

    public function update(Request $request, AiGeneratedDescription $description)
    {
        if ($description->status === 'completed') {
            return back()->with('error', 'لا يمكن تعديل الوصف المكتمل');
        }

        $validated = $request->validate([
            'template' => 'nullable|string|in:' . implode(',', array_keys($this->getAvailableTemplates())),
            'writing_style' => 'nullable|string|in:' . implode(',', array_keys($this->getWritingStyles())),
            'target_audience' => 'nullable|string',
            'key_features' => 'nullable|array|min:3',
            'tone' => 'nullable|string|in:formal,casual,professional,friendly',
            'length' => 'nullable|integer|min:50|max:1000',
            'include_highlights' => 'nullable|boolean',
            'include_amenities' => 'nullable|boolean',
            'include_location_details' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        $description->update([
            'template' => $validated['template'] ?? $description->template,
            'writing_style' => $validated['writing_style'] ?? $description->writing_style,
            'target_audience' => $validated['target_audience'] ?? $description->target_audience,
            'key_features' => $validated['key_features'] ?? $description->key_features,
            'tone' => $validated['tone'] ?? $description->tone,
            'length' => $validated['length'] ?? $description->length,
            'include_highlights' => $validated['include_highlights'] ?? $description->include_highlights,
            'include_amenities' => $validated['include_amenities'] ?? $description->include_amenities,
            'include_location_details' => $validated['include_location_details'] ?? $description->include_location_details,
            'notes' => $validated['notes'] ?? $description->notes,
            'metadata' => array_merge($description->metadata, [
                'updated_at' => now(),
                'features_updated' => $validated['key_features'] ?? $description->key_features,
            ]),
        ]);

        // Re-process description with updated data
        $this->processDescription($description);

        return redirect()->route('ai.property-description.show', $description)
            ->with('success', 'تم تحديث وصف العقار بنجاح');
    }

    public function destroy(AiGeneratedDescription $description)
    {
        if ($description->status === 'completed') {
            return back()->with('error', 'لا يمكن حذف الوصف المكتمل');
        }

        $description->delete();

        return redirect()->route('ai.property-description.index')
            ->with('success', 'تم حذف وصف العقار بنجاح');
    }

    public function generate(AiGeneratedDescription $description)
    {
        $generatedContent = $this->generateDescriptionContent($description);
        
        $description->update([
            'generated_content' => $generatedContent['content'],
            'quality_score' => $generatedContent['quality_score'],
            'word_count' => $generatedContent['word_count'],
            'readability_score' => $generatedContent['readability_score'],
            'seo_score' => $generatedContent['seo_score'],
            'status' => 'completed',
            'metadata' => array_merge($description->metadata, [
                'generation_results' => $generatedContent,
                'generation_date' => now(),
                'model_used' => $generatedContent['model_used'],
            ]),
        ]);

        return response()->json([
            'success' => true,
            'description' => $description->fresh(),
            'generated_content' => $generatedContent,
        ]);
    }

    public function analyze(AiGeneratedDescription $description)
    {
        $analysis = $this->performQualityAnalysis($description);
        
        $description->update([
            'metadata' => array_merge($description->metadata, [
                'quality_analysis' => $analysis,
                'analysis_date' => now(),
                'quality_score' => $analysis['overall_score'] ?? 0,
            ]),
        ]);

        return response()->json([
            'success' => true,
            'analysis' => $analysis,
            'updated_description' => $description->fresh(),
        ]);
    }

    public function approve(AiGeneratedDescription $description)
    {
        if ($description->status !== 'completed') {
            return back()->with('error', 'لا يمكن اعتماد الوصف غير المكتمل');
        }

        $description->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Update property description
        $description->property->update([
            'description' => $description->generated_content,
            'ai_description_id' => $description->id,
        ]);

        return back()->with('success', 'تم اعتماد الوصف بنجاح');
    }

    public function reject(AiGeneratedDescription $description, Request $request)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $description->update([
            'status' => 'rejected',
            'rejected_by' => auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return back()->with('success', 'تم رفض الوصف بنجاح');
    }

    // Helper Methods
    private function getAverageQualityScore(): float
    {
        return AiGeneratedDescription::where('status', 'completed')
            ->avg('quality_score') ?? 0;
    }

    private function getTotalPropertiesProcessed(): int
    {
        return AiGeneratedDescription::where('status', 'completed')
            ->distinct('property_id')
            ->count();
    }

    private function getSuccessRate(): float
    {
        $total = AiGeneratedDescription::count();
        $completed = AiGeneratedDescription::where('status', 'completed')->count();
        
        return $total > 0 ? ($completed / $total) * 100 : 0;
    }

    private function getDescriptionTrends(): array
    {
        return AiGeneratedDescription::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('AVG(quality_score) as avg_quality')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getQualityMetrics(): array
    {
        return [
            'total_descriptions' => AiGeneratedDescription::count(),
            'high_quality_descriptions' => AiGeneratedDescription::where('quality_score', '>=', 0.8)->count(),
            'average_quality_score' => AiGeneratedDescription::avg('quality_score') ?? 0,
            'template_performance' => $this->getTemplatePerformance(),
            'style_performance' => $this->getStylePerformance(),
        ];
    }

    private function getTemplatePerformance(): array
    {
        return [
            'luxury' => 0.85,
            'family' => 0.82,
            'investment' => 0.88,
            'rental' => 0.79,
            'commercial' => 0.83,
        ];
    }

    private function getStylePerformance(): array
    {
        return [
            'formal' => 0.84,
            'casual' => 0.78,
            'professional' => 0.86,
            'friendly' => 0.81,
        ];
    }

    private function getAvailableTemplates(): array
    {
        return [
            'luxury' => 'Luxury Property',
            'family' => 'Family Home',
            'investment' => 'Investment Property',
            'rental' => 'Rental Property',
            'commercial' => 'Commercial Property',
            'vacation' => 'Vacation Home',
            'student' => 'Student Housing',
        ];
    }

    private function getWritingStyles(): array
    {
        return [
            'formal' => 'Formal',
            'casual' => 'Casual',
            'professional' => 'Professional',
            'friendly' => 'Friendly',
            'persuasive' => 'Persuasive',
            'informative' => 'Informative',
        ];
    }

    private function processDescription(AiGeneratedDescription $description): void
    {
        // Simulate AI description generation process
        $this->sendAiRequest($description, 'generate', [
            'property_id' => $description->property_id,
            'template' => $description->template,
            'style' => $description->writing_style,
            'features' => $description->key_features,
            'tone' => $description->tone,
            'length' => $description->length,
        ]);

        // Update status to processing
        $description->update(['status' => 'processing']);
    }

    private function sendAiRequest(AiGeneratedDescription $description, string $action, array $data = []): void
    {
        // In a real implementation, this would call an AI service
        // For now, we'll simulate the AI response
        $mockResponse = [
            'success' => true,
            'action' => $action,
            'data' => $data,
            'response' => 'AI processing ' . ucfirst($action),
        ];

        // Update description with AI results
        if ($mockResponse['success']) {
            $description->update([
                'metadata' => array_merge($description->metadata, [
                    'ai_response' => $mockResponse,
                    'ai_response_date' => now(),
                ]),
            ]);
        }
    }

    private function generateDescriptionContent(AiGeneratedDescription $description): array
    {
        $property = $description->property;
        $template = $description->template;
        $style = $description->writing_style;
        $features = $description->key_features;

        // Generate content based on template and style
        $content = $this->buildDescription($property, $template, $style, $features);
        
        return [
            'content' => $content,
            'quality_score' => $this->calculateQualityScore($content),
            'word_count' => str_word_count($content),
            'readability_score' => $this->calculateReadabilityScore($content),
            'seo_score' => $this->calculateSeoScore($content, $property),
            'model_used' => 'gpt-4',
            'generation_time' => 2.5,
        ];
    }

    private function buildDescription(Property $property, string $template, string $style, array $features): string
    {
        $templates = [
            'luxury' => 'استمتع بالرفاهية المطلقة في هذا العقار الفاخر الذي يجمع بين الأناقة والراحة.',
            'family' => 'منزل عائلي مثالي يوفر المساحة والأمان لعائلتك الموقرة.',
            'investment' => 'فرصة استثمارية استثنائية في موقع استراتيجي يضمن عائدًا مجديًا.',
            'rental' => 'وحدة إيجار مميزة تلبي جميع احتياجات المستأجرين الحديثين.',
            'commercial' => 'مساحة تجارية احترافية في قلب الأعمال النشطة.',
        ];

        $baseDescription = $templates[$template] ?? 'عقار مميز يوفر جميع وسائل الراحة والرفاهية.';
        
        $featureText = implode('، ', $features);
        $propertyDetails = "مساحة {$property->area} متر مربع، {$property->bedrooms} غرف نوم، في {$property->location}.";

        return "{$baseDescription} يتميز بـ {$featureText}. {$propertyDetails} هذا العقار يوفر تجربة معيشية فريدة تجمع بين الجودة والموقع المتميز.";
    }

    private function calculateQualityScore(string $content): float
    {
        $wordCount = str_word_count($content);
        $sentenceCount = $this->countSentences($content);
        $avgWordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;

        // Quality factors
        $lengthScore = min($wordCount / 100, 1.0);
        $readabilityScore = $avgWordsPerSentence >= 10 && $avgWordsPerSentence <= 20 ? 1.0 : 0.7;
        $structureScore = $this->assessStructure($content);
        $vocabularyScore = $this->assessVocabulary($content);

        return ($lengthScore + $readabilityScore + $structureScore + $vocabularyScore) / 4;
    }

    private function calculateReadabilityScore(string $content): float
    {
        $wordCount = str_word_count($content);
        $sentenceCount = $this->countSentences($content);
        $avgWordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;

        if ($avgWordsPerSentence >= 10 && $avgWordsPerSentence <= 20) {
            return 0.9;
        } elseif ($avgWordsPerSentence >= 8 && $avgWordsPerSentence <= 25) {
            return 0.8;
        } else {
            return 0.6;
        }
    }

    private function calculateSeoScore(string $content, Property $property): float
    {
        $keywords = [$property->type, $property->location, 'عقار', 'بيع', 'إيجار'];
        $keywordCount = 0;

        foreach ($keywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $keywordCount++;
            }
        }

        return min($keywordCount / count($keywords), 1.0);
    }

    private function countSentences(string $content): int
    {
        return preg_match_all('/[.!?]+/', $content);
    }

    private function assessStructure(string $content): float
    {
        $hasIntroduction = preg_match('/^(.|[\n\r])*$/m', $content);
        $hasConclusion = preg_match('/(.|[\n\r])*$/m', $content);
        $hasParagraphs = substr_count($content, "\n") >= 2;

        return ($hasIntroduction + $hasConclusion + $hasParagraphs) / 3;
    }

    private function assessVocabulary(string $content): float
    {
        $words = str_word_count($content, 1);
        $uniqueWords = array_unique($words);
        
        return count($words) > 0 ? count($uniqueWords) / count($words) : 0;
    }

    private function getDescriptionDetails(AiGeneratedDescription $description): array
    {
        return [
            'property_id' => $description->property_id,
            'property' => [
                'id' => $description->property->id,
                'title' => $description->property->title,
                'type' => $description->property->type,
                'location' => $description->property->location,
                'area' => $description->property->area,
                'bedrooms' => $description->property->bedrooms,
                'price' => $description->property->price,
            ],
            'template' => $description->template,
            'writing_style' => $description->writing_style,
            'target_audience' => $description->target_audience,
            'key_features' => $description->key_features,
            'tone' => $description->tone,
            'length' => $description->length,
            'generated_content' => $description->generated_content,
            'quality_score' => $description->quality_score,
            'word_count' => $description->word_count,
            'readability_score' => $description->readability_score,
            'seo_score' => $description->seo_score,
            'metadata' => $description->metadata,
            'created_at' => $description->created_at,
            'updated_at' => $description->updated_at,
        ];
    }

    private function getQualityAnalysis(AiGeneratedDescription $description): array
    {
        return [
            'overall_score' => $description->quality_score,
            'readability' => $description->readability_score,
            'seo_optimization' => $description->seo_score,
            'length_appropriateness' => $this->assessLengthAppropriateness($description),
            'tone_consistency' => $this->assessToneConsistency($description),
            'feature_coverage' => $this->assessFeatureCoverage($description),
            'recommendations' => $this->generateQualityRecommendations($description),
        ];
    }

    private function getSimilarDescriptions(AiGeneratedDescription $description): Collection
    {
        return AiGeneratedDescription::where('property_id', '!=', $description->property_id)
            ->where('template', $description->template)
            ->where('writing_style', $description->writing_style)
            ->where('status', 'completed')
            ->take(5)
            ->get();
    }

    private function assessLengthAppropriateness(AiGeneratedDescription $description): float
    {
        $targetLength = $description->length;
        $actualLength = $description->word_count ?? 0;
        
        $diff = abs($targetLength - $actualLength);
        $tolerance = $targetLength * 0.2;
        
        return $diff <= $tolerance ? 1.0 : max(0, 1.0 - ($diff / $targetLength));
    }

    private function assessToneConsistency(AiGeneratedDescription $description): float
    {
        // Simplified tone assessment
        return 0.85; // Placeholder
    }

    private function assessFeatureCoverage(AiGeneratedDescription $description): float
    {
        $content = $description->generated_content ?? '';
        $features = $description->key_features ?? [];
        $coveredFeatures = 0;

        foreach ($features as $feature) {
            if (stripos($content, $feature) !== false) {
                $coveredFeatures++;
            }
        }

        return count($features) > 0 ? $coveredFeatures / count($features) : 0;
    }

    private function generateQualityRecommendations(AiGeneratedDescription $description): array
    {
        $recommendations = [];

        if ($description->quality_score < 0.7) {
            $recommendations[] = 'تحسين جودة المحتوى العام';
        }

        if ($description->readability_score < 0.7) {
            $recommendations[] = 'تحسين قابلية القراءة';
        }

        if ($description->seo_score < 0.7) {
            $recommendations[] = 'تحسين تحسين محركات البحث';
        }

        if ($this->assessLengthAppropriateness($description) < 0.7) {
            $recommendations[] = 'تعديل طول النص';
        }

        if ($this->assessFeatureCoverage($description) < 0.7) {
            $recommendations[] = 'تغطية جميع الميزات المطلوبة';
        }

        return $recommendations;
    }

    private function performQualityAnalysis(AiGeneratedDescription $description): array
    {
        return [
            'overall_score' => $description->quality_score,
            'readability' => $description->readability_score,
            'seo_optimization' => $description->seo_score,
            'length_appropriateness' => $this->assessLengthAppropriateness($description),
            'tone_consistency' => $this->assessToneConsistency($description),
            'feature_coverage' => $this->assessFeatureCoverage($description),
            'recommendations' => $this->generateQualityRecommendations($description),
        ];
    }
}
