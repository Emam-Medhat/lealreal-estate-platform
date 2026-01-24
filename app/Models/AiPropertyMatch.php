<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class AiPropertyMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'buyer_profile_id',
        'match_score',
        'compatibility_factors',
        'matching_criteria',
        'property_analysis',
        'buyer_preferences',
        'recommendation_level',
        'match_confidence',
        'price_suitability',
        'location_match',
        'feature_match',
        'market_timing',
        'ai_model_version',
        'matching_metadata',
        'status',
        'contacted',
        'contacted_at',
        'viewing_scheduled',
        'viewing_date',
        'offer_made',
        'offer_amount',
        'deal_closed',
        'deal_amount',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'match_score' => 'decimal:2',
        'compatibility_factors' => 'array',
        'matching_criteria' => 'array',
        'property_analysis' => 'array',
        'buyer_preferences' => 'array',
        'match_confidence' => 'decimal:2',
        'price_suitability' => 'decimal:2',
        'location_match' => 'decimal:2',
        'feature_match' => 'decimal:2',
        'market_timing' => 'array',
        'matching_metadata' => 'array',
        'contacted' => 'boolean',
        'contacted_at' => 'datetime',
        'viewing_scheduled' => 'boolean',
        'viewing_date' => 'datetime',
        'offer_made' => 'boolean',
        'offer_amount' => 'decimal:2',
        'deal_closed' => 'boolean',
        'deal_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the match.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the property that is matched.
     */
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * Get the buyer profile for the match.
     */
    public function buyerProfile(): BelongsTo
    {
        return $this->belongsTo(BuyerProfile::class);
    }

    /**
     * Get the user who created the match.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who updated the match.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Scope a query to only include high-score matches.
     */
    public function scopeHighScore($query, $threshold = 80.0)
    {
        return $query->where('match_score', '>=', $threshold);
    }

    /**
     * Scope a query to only include matches by recommendation level.
     */
    public function scopeByRecommendation($query, $level)
    {
        return $query->where('recommendation_level', $level);
    }

    /**
     * Scope a query to only include contacted matches.
     */
    public function scopeContacted($query)
    {
        return $query->where('contacted', true);
    }

    /**
     * Scope a query to only include matches with viewing scheduled.
     */
    public function scopeWithViewing($query)
    {
        return $query->where('viewing_scheduled', true);
    }

    /**
     * Scope a query to only include recent matches.
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', Carbon::now()->subDays($days));
    }

    /**
     * Get match level based on score.
     */
    public function getMatchLevelAttribute(): string
    {
        if ($this->match_score >= 90) return 'مثالي';
        if ($this->match_score >= 80) return 'ممتاز';
        if ($this->match_score >= 70) return 'جيد جداً';
        if ($this->match_score >= 60) return 'جيد';
        if ($this->match_score >= 50) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get recommendation level label in Arabic.
     */
    public function getRecommendationLevelLabelAttribute(): string
    {
        $levels = [
            'highly_recommended' => 'موصى به بشدة',
            'recommended' => 'موصى به',
            'consider' => 'ينبغي النظر فيه',
            'not_recommended' => 'غير موصى به',
        ];

        return $levels[$this->recommendation_level] ?? 'غير محدد';
    }

    /**
     * Get status label in Arabic.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'reviewing' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'expired' => 'منتهي الصلاحية',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get confidence level text.
     */
    public function getConfidenceLevelAttribute(): string
    {
        if ($this->match_confidence >= 0.9) return 'عالي جداً';
        if ($this->match_confidence >= 0.8) return 'عالي';
        if ($this->match_confidence >= 0.7) return 'متوسط';
        if ($this->match_confidence >= 0.6) return 'منخفض';
        return 'منخفض جداً';
    }

    /**
     * Get price suitability level.
     */
    public function getPriceSuitabilityLevelAttribute(): string
    {
        if ($this->price_suitability >= 0.9) return 'مثالي';
        if ($this->price_suitability >= 0.8) return 'جيد جداً';
        if ($this->price_suitability >= 0.7) return 'جيد';
        if ($this->price_suitability >= 0.6) return 'مقبول';
        return 'غير مناسب';
    }

    /**
     * Get location match level.
     */
    public function getLocationMatchLevelAttribute(): string
    {
        if ($this->location_match >= 0.9) return 'مثالي';
        if ($this->location_match >= 0.8) return 'ممتاز';
        if ($this->location_match >= 0.7) return 'جيد';
        if ($this->location_match >= 0.6) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Get feature match level.
     */
    public function getFeatureMatchLevelAttribute(): string
    {
        if ($this->feature_match >= 0.9) return 'مثالي';
        if ($this->feature_match >= 0.8) return 'ممتاز';
        if ($this->feature_match >= 0.7) return 'جيد';
        if ($this->feature_match >= 0.6) return 'مقبول';
        return 'ضعيف';
    }

    /**
     * Check if match is recent (within last 7 days).
     */
    public function isRecent(): bool
    {
        return $this->created_at->diffInDays(Carbon::now()) <= 7;
    }

    /**
     * Check if match is hot (high score and recent).
     */
    public function isHot(): bool
    {
        return $this->match_score >= 85 && $this->isRecent();
    }

    /**
     * Get match progress percentage.
     */
    public function getProgressPercentageAttribute(): int
    {
        $progress = 0;
        
        if ($this->contacted) $progress += 25;
        if ($this->viewing_scheduled) $progress += 25;
        if ($this->offer_made) $progress += 25;
        if ($this->deal_closed) $progress += 25;
        
        return $progress;
    }

    /**
     * Get next action needed.
     */
    public function getNextActionAttribute(): string
    {
        if (!$this->contacted) return 'التواصل مع المشتري';
        if (!$this->viewing_scheduled) return 'جدولة المعاينة';
        if (!$this->offer_made) return 'انتظار العرض';
        if (!$this->deal_closed) return 'إتمام الصفقة';
        return 'مكتمل';
    }

    /**
     * Get key matching factors.
     */
    public function getKeyFactorsAttribute(): array
    {
        $factors = $this->compatibility_factors ?? [];
        
        return array_filter($factors, function($value) {
            return $value >= 0.7; // Only return factors with 70% or higher compatibility
        });
    }

    /**
     * Get missing features.
     */
    public function getMissingFeaturesAttribute(): array
    {
        $preferences = $this->buyer_preferences ?? [];
        $analysis = $this->property_analysis ?? [];
        
        $missing = [];
        
        if (isset($preferences['required_features']) && isset($analysis['available_features'])) {
            $required = $preferences['required_features'];
            $available = $analysis['available_features'];
            
            $missing = array_diff($required, $available);
        }
        
        return array_values($missing);
    }

    /**
     * Get additional benefits.
     */
    public function getAdditionalBenefitsAttribute(): array
    {
        $preferences = $this->buyer_preferences ?? [];
        $analysis = $this->property_analysis ?? [];
        
        $benefits = [];
        
        if (isset($preferences['desired_features']) && isset($analysis['available_features'])) {
            $desired = $preferences['desired_features'];
            $available = $analysis['available_features'];
            
            $benefits = array_intersect($desired, $available);
        }
        
        return array_values($benefits);
    }

    /**
     * Calculate conversion probability.
     */
    public function getConversionProbabilityAttribute(): float
    {
        $baseProbability = $this->match_score / 100;
        
        // Adjust based on progress
        if ($this->contacted) $baseProbability += 0.1;
        if ($this->viewing_scheduled) $baseProbability += 0.2;
        if ($this->offer_made) $baseProbability += 0.3;
        
        // Adjust based on market timing
        $timing = $this->market_timing ?? [];
        if (isset($timing['buyer_market_position']) && $timing['buyer_market_position'] === 'favorable') {
            $baseProbability += 0.1;
        }
        
        return min(1.0, $baseProbability);
    }

    /**
     * Get urgency level.
     */
    public function getUrgencyLevelAttribute(): string
    {
        $urgency = 'low';
        
        $timing = $this->market_timing ?? [];
        if (isset($timing['market_demand'])) {
            if ($timing['market_demand'] >= 0.8) $urgency = 'high';
            elseif ($timing['market_demand'] >= 0.6) $urgency = 'medium';
        }
        
        // Adjust for hot properties
        if ($this->isHot()) {
            $urgency = $urgency === 'high' ? 'critical' : 'high';
        }
        
        return $urgency;
    }

    /**
     * Create a new AI property match.
     */
    public static function createMatch(array $data): self
    {
        // Simulate AI matching algorithm
        $userPreferences = $data['buyer_preferences'] ?? [];
        $propertyData = $data['property_data'] ?? [];
        
        // Calculate individual scores
        $priceScore = static::calculatePriceScore($userPreferences, $propertyData);
        $locationScore = static::calculateLocationScore($userPreferences, $propertyData);
        $featureScore = static::calculateFeatureScore($userPreferences, $propertyData);
        $sizeScore = static::calculateSizeScore($userPreferences, $propertyData);
        $typeScore = static::calculateTypeScore($userPreferences, $propertyData);
        
        // Calculate weighted total score
        $weights = [
            'price' => 0.25,
            'location' => 0.30,
            'features' => 0.20,
            'size' => 0.15,
            'type' => 0.10,
        ];
        
        $totalScore = (
            $priceScore * $weights['price'] +
            $locationScore * $weights['location'] +
            $featureScore * $weights['features'] +
            $sizeScore * $weights['size'] +
            $typeScore * $weights['type']
        ) * 100;
        
        // Determine recommendation level
        $recommendationLevel = static::determineRecommendationLevel($totalScore);
        
        // Calculate confidence
        $confidence = min(0.95, 0.6 + ($totalScore / 100) * 0.35);
        
        // Generate compatibility factors
        $compatibilityFactors = [
            'price_alignment' => $priceScore,
            'location_preference' => $locationScore,
            'feature_satisfaction' => $featureScore,
            'size_appropriateness' => $sizeScore,
            'type_suitability' => $typeScore,
        ];
        
        // Generate market timing analysis
        $marketTiming = [
            'market_demand' => rand(50, 95) / 100,
            'price_trend' => ['increasing', 'stable', 'decreasing'][array_rand(['increasing', 'stable', 'decreasing'])],
            'inventory_level' => rand(20, 80) / 100,
            'buyer_market_position' => ['favorable', 'neutral', 'challenging'][array_rand(['favorable', 'neutral', 'challenging'])],
        ];

        return static::create([
            'user_id' => $data['user_id'],
            'property_id' => $data['property_id'],
            'buyer_profile_id' => $data['buyer_profile_id'] ?? null,
            'match_score' => round($totalScore, 2),
            'compatibility_factors' => $compatibilityFactors,
            'matching_criteria' => $data['matching_criteria'] ?? [],
            'property_analysis' => $propertyData,
            'buyer_preferences' => $userPreferences,
            'recommendation_level' => $recommendationLevel,
            'match_confidence' => round($confidence, 2),
            'price_suitability' => round($priceScore, 2),
            'location_match' => round($locationScore, 2),
            'feature_match' => round($featureScore, 2),
            'market_timing' => $marketTiming,
            'ai_model_version' => '4.1.2',
            'matching_metadata' => [
                'processing_time' => rand(0.8, 2.3) . 's',
                'data_points_analyzed' => rand(50, 150),
                'algorithm_version' => 'neural_network_v3',
                'match_date' => now()->toDateTimeString(),
            ],
            'status' => 'pending',
            'contacted' => false,
            'viewing_scheduled' => false,
            'offer_made' => false,
            'deal_closed' => false,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /**
     * Calculate price compatibility score.
     */
    private static function calculatePriceScore(array $preferences, array $property): float
    {
        if (!isset($preferences['budget_max']) || !isset($property['price'])) {
            return rand(60, 80) / 100; // Default score if data missing
        }

        $budgetMax = $preferences['budget_max'];
        $propertyPrice = $property['price'];
        
        if ($propertyPrice <= $budgetMax * 0.8) {
            return 0.95; // Well within budget
        } elseif ($propertyPrice <= $budgetMax) {
            return 0.85; // Within budget
        } elseif ($propertyPrice <= $budgetMax * 1.1) {
            return 0.60; // Slightly over budget
        } else {
            return 0.30; // Significantly over budget
        }
    }

    /**
     * Calculate location compatibility score.
     */
    private static function calculateLocationScore(array $preferences, array $property): float
    {
        if (!isset($preferences['preferred_locations']) || !isset($property['location'])) {
            return rand(60, 80) / 100;
        }

        $preferredLocations = $preferences['preferred_locations'];
        $propertyLocation = $property['location'];
        
        if (in_array($propertyLocation, $preferredLocations)) {
            return 0.95; // Exact match
        }
        
        // Check for nearby locations (simplified)
        foreach ($preferredLocations as $location) {
            if (strpos($propertyLocation, $location) !== false || strpos($location, $propertyLocation) !== false) {
                return 0.80; // Partial match
            }
        }
        
        return rand(40, 60) / 100; // No match
    }

    /**
     * Calculate feature compatibility score.
     */
    private static function calculateFeatureScore(array $preferences, array $property): float
    {
        $requiredFeatures = $preferences['required_features'] ?? [];
        $availableFeatures = $property['features'] ?? [];
        
        if (empty($requiredFeatures)) {
            return 0.80; // No specific requirements
        }
        
        $matchedFeatures = array_intersect($requiredFeatures, $availableFeatures);
        $matchRatio = count($matchedFeatures) / count($requiredFeatures);
        
        return $matchRatio;
    }

    /**
     * Calculate size compatibility score.
     */
    private static function calculateSizeScore(array $preferences, array $property): float
    {
        if (!isset($preferences['min_size']) || !isset($preferences['max_size']) || !isset($property['size'])) {
            return rand(60, 80) / 100;
        }

        $minSize = $preferences['min_size'];
        $maxSize = $preferences['max_size'];
        $propertySize = $property['size'];
        
        if ($propertySize >= $minSize && $propertySize <= $maxSize) {
            return 0.95; // Perfect size match
        } elseif ($propertySize >= $minSize * 0.9 && $propertySize <= $maxSize * 1.1) {
            return 0.80; // Close size match
        } else {
            return 0.50; // Size mismatch
        }
    }

    /**
     * Calculate property type compatibility score.
     */
    private static function calculateTypeScore(array $preferences, array $property): float
    {
        if (!isset($preferences['property_types']) || !isset($property['type'])) {
            return rand(60, 80) / 100;
        }

        $preferredTypes = $preferences['property_types'];
        $propertyType = $property['type'];
        
        return in_array($propertyType, $preferredTypes) ? 0.95 : 0.40;
    }

    /**
     * Determine recommendation level based on score.
     */
    private static function determineRecommendationLevel(float $score): string
    {
        if ($score >= 85) return 'highly_recommended';
        if ($score >= 75) return 'recommended';
        if ($score >= 65) return 'consider';
        return 'not_recommended';
    }

    /**
     * Mark as contacted.
     */
    public function markAsContacted(): bool
    {
        $this->contacted = true;
        $this->contacted_at = now();
        
        return $this->save();
    }

    /**
     * Schedule viewing.
     */
    public function scheduleViewing($date): bool
    {
        $this->viewing_scheduled = true;
        $this->viewing_date = $date;
        
        return $this->save();
    }

    /**
     * Make offer.
     */
    public function makeOffer(float $amount): bool
    {
        $this->offer_made = true;
        $this->offer_amount = $amount;
        
        return $this->save();
    }

    /**
     * Close deal.
     */
    public function closeDeal(float $amount): bool
    {
        $this->deal_closed = true;
        $this->deal_amount = $amount;
        
        return $this->save();
    }

    /**
     * Get match summary for dashboard.
     */
    public function getDashboardSummary(): array
    {
        return [
            'id' => $this->id,
            'property_id' => $this->property_id,
            'user_id' => $this->user_id,
            'match_score' => $this->match_score,
            'match_level' => $this->match_level,
            'recommendation' => $this->recommendation_level_label,
            'progress' => $this->progress_percentage,
            'next_action' => $this->next_action,
            'urgency' => $this->urgency_level,
            'is_hot' => $this->isHot(),
            'created_at' => $this->created_at->format('Y-m-d H:i'),
        ];
    }
}
