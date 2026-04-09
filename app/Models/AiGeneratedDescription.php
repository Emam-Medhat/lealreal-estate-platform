<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiGeneratedDescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'description_type',
        'generated_content',
        'language',
        'tone',
        'target_audience',
        'key_features',
        'selling_points',
        'call_to_action',
        'seo_keywords',
        'quality_score',
        'readability_score',
        'engagement_prediction',
        'ai_model_version',
        'generation_metadata',
        'status',
        'is_published',
        'published_at',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'generated_content' => 'array',
        'key_features' => 'array',
        'selling_points' => 'array',
        'seo_keywords' => 'array',
        'generation_metadata' => 'array',
        'quality_score' => 'decimal:2',
        'readability_score' => 'decimal:2',
        'engagement_prediction' => 'decimal:2',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the property that owns the description.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the user that requested the description.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created the description.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the description.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include published descriptions.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope a query to only include descriptions with high quality.
     */
    public function scopeHighQuality($query)
    {
        return $query->where('quality_score', '>=', 8.0);
    }

    /**
     * Scope a query to only include descriptions by type.
     */
    public function scopeByType($query, $type)
    {
        return $query->where('description_type', $type);
    }

    /**
     * Scope a query to only include descriptions by language.
     */
    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Get full description text.
     */
    public function getFullDescriptionAttribute(): string
    {
        $content = $this->generated_content ?? [];
        
        $parts = [];
        if (isset($content['title'])) $parts[] = $content['title'];
        if (isset($content['introduction'])) $parts[] = $content['introduction'];
        if (isset($content['main_description'])) $parts[] = $content['main_description'];
        if (isset($content['features'])) $parts[] = implode(' ', $content['features']);
        if (isset($content['conclusion'])) $parts[] = $content['conclusion'];
        if (isset($content['call_to_action'])) $parts[] = $content['call_to_action'];

        return implode(' ', $parts);
    }

    /**
     * Get description title.
     */
    public function getTitleAttribute(): ?string
    {
        return $this->generated_content['title'] ?? null;
    }

    /**
     * Get description introduction.
     */
    public function getIntroductionAttribute(): ?string
    {
        return $this->generated_content['introduction'] ?? null;
    }

    /**
     * Get main description.
     */
    public function getMainDescriptionAttribute(): ?string
    {
        return $this->generated_content['main_description'] ?? null;
    }

    /**
     * Get features list.
     */
    public function getFeaturesListAttribute(): array
    {
        return $this->generated_content['features'] ?? [];
    }

    /**
     * Get conclusion.
     */
    public function getConclusionAttribute(): ?string
    {
        return $this->generated_content['conclusion'] ?? null;
    }

    /**
     * Get call to action.
     */
    public function getCallToActionTextAttribute(): ?string
    {
        return $this->generated_content['call_to_action'] ?? null;
    }

    /**
     * Get description type label in Arabic.
     */
    public function getDescriptionTypeLabelAttribute(): string
    {
        $types = [
            'detailed' => 'وصف مفصل',
            'brief' => 'وصف موجز',
            'marketing' => 'وصف تسويقي',
            'technical' => 'وصف فني',
            'luxury' => 'وصف فاخر',
            'investment' => 'وصف استثماري',
        ];

        return $types[$this->description_type] ?? 'غير معروف';
    }

    /**
     * Get tone label in Arabic.
     */
    public function getToneLabelAttribute(): string
    {
        $tones = [
            'professional' => 'احترافي',
            'friendly' => 'ودود',
            'formal' => 'رسمي',
            'casual' => 'عادي',
            'luxurious' => 'فاخر',
            'persuasive' => 'إقناعي',
        ];

        return $tones[$this->tone] ?? 'غير معروف';
    }

    /**
     * Get target audience label in Arabic.
     */
    public function getTargetAudienceLabelAttribute(): string
    {
        $audiences = [
            'families' => 'عائلات',
            'investors' => 'مستثمرين',
            'young_professionals' => 'شباب محترفين',
            'retirees' => 'متقاعدين',
            'students' => 'طلاب',
            'expats' => 'وافدين',
        ];

        return $audiences[$this->target_audience] ?? 'عام';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'مسودة',
            'generating' => 'قيد التوليد',
            'ready' => 'جاهز',
            'published' => 'منشور',
            'archived' => 'مؤرشف',
            'failed' => 'فشل',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get quality level.
     */
    public function getQualityLevelAttribute(): string
    {
        if ($this->quality_score >= 9.0) return 'ممتاز';
        if ($this->quality_score >= 8.0) return 'جيد جداً';
        if ($this->quality_score >= 7.0) return 'جيد';
        if ($this->quality_score >= 6.0) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get readability level.
     */
    public function getReadabilityLevelAttribute(): string
    {
        if ($this->readability_score >= 8.0) return 'سهل جداً';
        if ($this->readability_score >= 6.0) return 'سهل';
        if ($this->readability_score >= 4.0) return 'متوسط';
        return 'صعب';
    }

    /**
     * Get word count.
     */
    public function getWordCountAttribute(): int
    {
        return str_word_count(strip_tags($this->full_description));
    }

    /**
     * Get character count.
     */
    public function getCharacterCountAttribute(): int
    {
        return strlen(strip_tags($this->full_description));
    }

    /**
     * Check if description is optimized for SEO.
     */
    public function isSeoOptimized(): bool
    {
        return $this->quality_score >= 7.5 && 
               count($this->seo_keywords ?? []) >= 5 &&
               $this->readability_score >= 6.0;
    }

    /**
     * Get SEO score.
     */
    public function getSeoScoreAttribute(): float
    {
        $score = 0;
        
        // Keywords presence
        $keywordScore = min(25, count($this->seo_keywords ?? []) * 5);
        $score += $keywordScore;
        
        // Quality score
        $score += $this->quality_score * 0.4;
        
        // Readability
        $score += $this->readability_score * 0.2;
        
        // Length appropriateness
        $wordCount = $this->word_count;
        if ($wordCount >= 100 && $wordCount <= 500) {
            $score += 15;
        } elseif ($wordCount > 500 && $wordCount <= 1000) {
            $score += 10;
        }
        
        return min(100, $score);
    }

    /**
     * Get engagement prediction level.
     */
    public function getEngagementLevelAttribute(): string
    {
        if ($this->engagement_prediction >= 8.0) return 'مرتفع جداً';
        if ($this->engagement_prediction >= 6.0) return 'مرتفع';
        if ($this->engagement_prediction >= 4.0) return 'متوسط';
        return 'منخفض';
    }

    /**
     * Check if description needs improvement.
     */
    public function needsImprovement(): bool
    {
        return $this->quality_score < 7.0 || 
               $this->readability_score < 6.0 ||
               count($this->seo_keywords ?? []) < 3;
    }

    /**
     * Get improvement suggestions.
     */
    public function getImprovementSuggestionsAttribute(): array
    {
        $suggestions = [];
        
        if ($this->quality_score < 7.0) {
            $suggestions[] = 'تحسين جودة المحتوى وإضافة تفاصيل أكثر';
        }
        
        if ($this->readability_score < 6.0) {
            $suggestions[] = 'تبسيط اللغة وتحسين قابلية القراءة';
        }
        
        if (count($this->seo_keywords ?? []) < 3) {
            $suggestions[] = 'إضافة المزيد من الكلمات المفتاحية المناسبة';
        }
        
        if ($this->word_count < 100) {
            $suggestions[] = 'زيادة طول الوصف ليكون أكثر شمولية';
        }
        
        if ($this->word_count > 1000) {
            $suggestions[] = 'تقليص طول الوصف ليكون أكثر تركيزاً';
        }

        return $suggestions;
    }

    /**
     * Create a new AI-generated description.
     */
    public static function generateDescription(array $data): self
    {
        // Simulate AI description generation
        $propertyType = $data['property_type'] ?? 'apartment';
        $tone = $data['tone'] ?? 'professional';
        $targetAudience = $data['target_audience'] ?? 'families';
        
        $contentTemplates = [
            'apartment' => [
                'title' => 'شقة فاخرة في موقع استراتيجي',
                'introduction' => 'اكتشف حياتك المثالية في هذه الشقة الرائعة التي تجمع بين التصميم العصري والراحة الفائقة.',
                'main_description' => 'تتميز هذه الشقة بمساحتها الواسعة وتصميمها الداخلي الأنيق، مع إطلالات خلابة وتهوية ممتازة.',
                'features' => [
                    'غرف نوم واسعة مع خزائن مدمجة',
                    'مطبخ حديث مجهز بالكامل',
                    'صالة معيشة فسيحة',
                    'شرفات بإطلالات رائعة',
                    'مواقف سيارات مغطاة',
                    'صالة رياضية وسباحة'
                ],
                'conclusion' => 'فرصة استثنائية للعيش في حي راقٍ مع جميع الخدمات المتاحة.',
                'call_to_action' => 'لا تفوت فرصة امتلاك هذه الشقة المميزة. تواصل معنا اليوم لترتيب جولة استكشافية.'
            ],
            'villa' => [
                'title' => 'فيلا فاخرة مع حديقة خاصة',
                'introduction' => 'استمتع بالخصوصية والرفاهية في هذه الفيلا الاستثنائية المصممة لتلبية أعلى معايير الحياة الفاخرة.',
                'main_description' => 'تتميز الفيلا بتصميمها المعماري الفريد وحديقتها الخضراء الواسعة، مع مساحات داخلية فاخرة وتشطيبات عالية الجودة.',
                'features' => [
                    '4 غرف نوم رئيسية مع سويت',
                    'مطبخ مفتوح حديث مع غرفة طعام',
                    'صالة معيشة مزدوجة',
                    'حديقة خاصة مع بركة سباحة',
                    'غرفة خادم وسائق',
                    'نظام أمني متقدم'
                ],
                'conclusion' => 'تجربة حياة فاخرة لا مثيل لها في موقع مميز يوفر الخصوصية والراحة.',
                'call_to_action' => 'احجز موعدك اليوم لاستكشاف هذه الفيلا الفاخرة التي تنتظر مالكها الجديد.'
            ]
        ];

        $template = $contentTemplates[$propertyType] ?? $contentTemplates['apartment'];
        
        // Customize based on tone and audience
        if ($tone === 'luxurious') {
            $template['title'] = 'قطعة فنية فاخرة في قلب المدينة';
            $template['introduction'] = 'نقدم لكم تحفة معمارية فريدة تجسد معاني الرفاهية والتميز.';
        }

        $keyFeatures = array_slice($template['features'], 0, 5);
        $sellingPoints = [
            'موقع استراتيجي قريب من جميع المرافق',
            'تصميم عصري يلبي احتياجات العصر',
            'جودة بناء فائقة وتشطيبات ممتازة',
            'فرصة استثمارية واعدة'
        ];

        $seoKeywords = [
            'عقارات فاخرة',
            'شقق للبيع',
            'فيلا راقية',
            'استثمار عقاري',
            'سكن مريح',
            'موقع مميز'
        ];

        $qualityScore = rand(7.5, 9.8);
        $readabilityScore = rand(6.0, 9.0);
        $engagementPrediction = rand(6.5, 9.2);

        return static::create([
            'property_id' => $data['property_id'],
            'user_id' => $data['user_id'] ?? auth()->id(),
            'description_type' => $data['description_type'] ?? 'detailed',
            'generated_content' => $template,
            'language' => $data['language'] ?? 'ar',
            'tone' => $tone,
            'target_audience' => $targetAudience,
            'key_features' => $keyFeatures,
            'selling_points' => $sellingPoints,
            'call_to_action' => $template['call_to_action'],
            'seo_keywords' => $seoKeywords,
            'quality_score' => $qualityScore,
            'readability_score' => $readabilityScore,
            'engagement_prediction' => $engagementPrediction,
            'ai_model_version' => '3.2.1',
            'generation_metadata' => [
                'processing_time' => rand(2.1, 5.8) . 's',
                'data_points_analyzed' => rand(100, 500),
                'template_used' => $propertyType,
                'customization_level' => 'high',
                'generation_date' => now()->toDateTimeString(),
            ],
            'status' => 'ready',
            'is_published' => false,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Publish the description.
     */
    public function publish(): bool
    {
        $this->is_published = true;
        $this->published_at = now();
        $this->status = 'published';
        
        return $this->save();
    }

    /**
     * Unpublish the description.
     */
    public function unpublish(): bool
    {
        $this->is_published = false;
        $this->status = 'ready';
        
        return $this->save();
    }

    /**
     * Get description summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'title' => $this->title,
            'type' => $this->description_type_label,
            'quality_score' => $this->quality_score,
            'quality_level' => $this->quality_level,
            'status' => $this->status_label,
            'is_published' => $this->is_published,
            'word_count' => $this->word_count,
            'seo_score' => $this->seo_score,
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }

    /**
     * Get export data for different formats.
     */
    public function getExportData(string $format = 'array'): array|string
    {
        $data = [
            'title' => $this->title,
            'introduction' => $this->introduction,
            'main_description' => $this->main_description,
            'features' => $this->features_list,
            'conclusion' => $this->conclusion,
            'call_to_action' => $this->call_to_action_text,
            'seo_keywords' => $this->seo_keywords,
            'quality_score' => $this->quality_score,
            'readability_score' => $this->readability_score,
        ];

        switch ($format) {
            case 'json':
                return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            case 'html':
                $html = "<h2>{$data['title']}</h2>";
                $html .= "<p>{$data['introduction']}</p>";
                $html .= "<p>{$data['main_description']}</p>";
                if (!empty($data['features'])) {
                    $html .= "<ul>";
                    foreach ($data['features'] as $feature) {
                        $html .= "<li>{$feature}</li>";
                    }
                    $html .= "</ul>";
                }
                $html .= "<p>{$data['conclusion']}</p>";
                $html .= "<p><strong>{$data['call_to_action']}</strong></p>";
                return $html;
            default:
                return $data;
        }
    }
}
