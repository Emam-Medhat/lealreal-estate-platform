<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NeighborhoodReview extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'title',
        'content',
        'rating',
        'status',
        'reviewer_name',
        'reviewer_email',
        'reviewer_phone',
        'reviewer_type',
        'pros',
        'cons',
        'recommendation',
        'experience_period',
        'property_type',
        'property_details',
        'community_aspects',
        'improvement_suggestions',
        'images',
        'photos',
        'videos',
        'verified',
        'featured',
        'tags',
        'metadata',
        'helpful_count',
        'report_count',
        'view_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'rating' => 'decimal:2,1',
        'pros' => 'array',
        'cons' => 'array',
        'recommendation' => 'string',
        'experience_period' => 'string',
        'property_type' => 'string',
        'property_details' => 'array',
        'community_aspects' => 'array',
        'improvement_suggestions' => 'array',
        'images' => 'array',
        'photos' => 'array',
        'videos' => 'array',
        'verified' => 'boolean',
        'featured' => 'boolean',
        'tags' => 'array',
        'metadata' => 'array',
        'helpful_count' => 'integer',
        'report_count' => 'integer',
        'view_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the review.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to only include published reviews.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by rating range.
     */
    public function scopeByRatingRange(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('rating', '>=', $min);
        }
        if ($max !== null) {
            $query->where('rating', '<=', $max);
        }
        return $query;
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get verified reviews.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Scope a query to get featured reviews.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to get reviews by recommendation.
     */
    public function scopeByRecommendation(Builder $query, string $recommendation): Builder
    {
        return $query->where('recommendation', $recommendation);
    }

    /**
     * Scope a query to get reviews by reviewer type.
     */
    public function scopeByReviewerType(Builder $query, string $reviewerType): Builder
    {
        return $query->where('reviewer_type', $reviewerType);
    }

    /**
     * Scope a query to get reviews by property type.
     */
    public function scopeByPropertyType(Builder $query, string $propertyType): Builder
    {
        return $query->where('property_type', $propertyType);
    }

    /**
     * Scope a query to get reviews by experience period.
     */
    public function scopeByExperiencePeriod(Builder $query, string $period): Builder
    {
        return $query->where('experience_period', $period);
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'مسودة',
            'published' => 'منشور',
            'hidden' => 'مخفي',
            'reported' => 'مبلغ عنه',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->rating . ' / 5';
    }

    /**
     * Get the recommendation label.
     */
    public function getRecommendationLabelAttribute(): string
    {
        $recommendations = [
            'yes' => 'نعم',
            'no' => 'لا',
            'maybe' => 'ربما',
        ];

        return $recommendations[$this->recommendation] ?? 'غير محدد';
    }

    /**
     * Get the reviewer type label.
     */
    public function getReviewerTypeLabelAttribute(): string
    {
        $types = [
            'resident' => 'ساكن',
            'owner' => 'مالك',
            'visitor' => 'زائر',
            'professional' => 'محترف',
        ];

        return $types[$this->reviewer_type] ?? 'غير معروف';
    }

    /**
     * Get the experience period label.
     */
    public function getExperiencePeriodLabelAttribute(): string
    {
        $periods = [
            'less_than_6_months' => 'أقل من 6 أشهر',
            '6_months_to_1_year' => '6 أشهر - سنة',
            '1_to_3_years' => '1 - 3 سنوات',
            '3_to_5_years' => '3 - 5 سنوات',
            'more_than_5_years' => 'أكثر من 5 سنوات',
        ];

        return $periods[$this->experience_period] ?? 'غير محدد';
    }

    /**
     * Get the property type label.
     */
    public function getPropertyTypeLabelAttribute(): string
    {
        $types = [
            'apartment' => 'شقة',
            'villa' => 'فيلا',
            'townhouse' => 'منزل',
            'duplex' => 'شقة مزدوجة',
            'studio' => 'استوديو',
            'penthouse' => 'بنتهاوس',
            'land' => 'أرض',
            'commercial' => 'تجاري',
            'office' => 'مكتب',
            'other' => 'أخرى',
        ];

        return $types[$this->property_type] ?? 'غير معروف';
    }

    /**
     * Get the helpful count label.
     */
    public function getHelpfulCountLabelAttribute(): string
    {
        return number_format($this->helpful_count) . ' مفيد';
    }

    /**
     * Get the report count label.
     */
    public function getReportCountLabelAttribute(): string
    {
        return number_format($this->report_count) . ' بلاغ';
    }

    /**
     * Get the view count label.
     */
    public function getViewCountLabelAttribute(): string
    {
        return number_format($->view_count) . ' مشاهدة';
    }

    /**
     * Get the pros list.
     */
    public function getProsListAttribute(): string
    {
        return implode(', ', $this->pros ?? []);
    }

    /**
     * Get the cons list.
     */
    public function getConsListAttribute(): string
    {
        return implode(', ', $this->cons ?? []);
    }

    /**
     * Get the community aspects.
     */
    public function getCommunityAspectsAttribute(): array
    {
        return $this->community_aspects ?? [];
    }

    /**
     * Get the improvement suggestions.
     */
    public function getImprovementSuggestionsAttribute(): array
    {
        return $this->improvement_suggestions ?? [];
    }

    /**
     * Get the tags list.
     */
    public function getTagsListAttribute(): string
    {
        return implode(', ', $this->tags ?? []);
    }

    /**
     * Get the images list.
     */
    public function getImagesListAttribute(): array
    {
        return $this->images ?? [];
    }

    /**
     * Get the photos list.
     */
    public function getPhotosListAttribute(): array
    {
        return $this->photos ?? [];
    }

    /**
     * Get the videos list.
     */
    public function getVideosListAttribute(): array
    {
        return $this->videos ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the property details.
     */
    public function getPropertyDetailsAttribute(): array
    {
        return $this->property_details ?? [];
    }

    /**
     * Check if the review is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the review is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the review is hidden.
     */
    public function isHidden(): bool
    {
        return $this->status === 'hidden';
    }

    /**
     * Check if the review is reported.
     */
    public function isReported(): bool
    {
        return $this->status === 'reported';
    }

    /**
     * Check if the review is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified;
    }

     /**
     * Check if the review is featured.
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * Check if the review is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the review is poorly rated.
     */
    public function isPoorlyRated(): bool
    {
        return $this->rating < 2.5;
    }

    /**
     * Check if the review has pros.
     */
    public function hasPros(): bool
    {
        return !empty($this->pros);
    }

    /**
     * Check if the review has cons.
     */
    public function hasCons(): bool
    {
        return !empty($this->cons);
    }

    /**
     * Check if the review has community aspects.
     */
    public function hasCommunityAspects(): bool
    {
        return !empty($this->community_aspects);
    }

    /**
     * Check if the review has improvement suggestions.
     */
    public function hasImprovementSuggestions(): bool
    {
        return !empty($this->improvement_suggestions);
    }

    /**
     * Check if the review has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Check if the review has photos.
     */
    public function hasPhotos(): bool
    {
        return !empty($this->photos);
    }

    /**
     * Check if the review has videos.
     */
    public function hasVideos(): bool
    {
        return !empty($this->videos);
    }

    /**
     * Check if the review recommends the neighborhood.
     */
    public function recommendsNeighborhood(): bool
    {
        return $this->recommendation === 'yes';
    }

    /**
     * Check if the review does not recommend the neighborhood.
     */
    public function doesNotRecommendNeighborhood(): bool
    {
        return $this->recommendation === 'no';
    }

    /**
     * Check if the review is neutral.
     */
    public function isNeutral(): bool
    {
        return $this->recommendation === 'maybe';
    }

    /**
     * Check if the reviewer is a resident.
     */
    public function isResident(): bool
    {
        return $this->reviewer_type === 'resident';
    }

    /**
     * Check if the reviewer is an owner.
     */
    public function isOwner(): bool
    {
        return $this->reviewer_type === 'owner';
    }

    /**
     * Check if the reviewer is a visitor.
     */
    public function isVisitor(): bool
    {
        return $this->reviewer_type === 'visitor';
    }

    /**
     * Check if the reviewer is a professional.
     */
    public function isProfessional(): bool
    {
        return $this->reviewer_type === 'professional';
    }

    /**
     * Check if the reviewer has long experience.
     */
    public function hasLongExperience(): bool
    {
        return in_array($this->experience_period, ['3_to_5_years', 'more_than_5_years']);
    }

    /**
     * Check if the reviewer has short experience.
     */
    public function hasShortExperience(): bool
    {
        return in_array($this->experience_period, ['less_than_6_months', '6_months_to_1_year']);
    }

    /**
     * Check if the review is for an apartment.
     */
    public function isApartmentReview(): bool
    {
        return $this->property_type === 'apartment';
    }

     /**
     * Check if the review is for a villa.
     */
    public function isVillaReview(): bool
    {
        return $this->property_type === 'villa';
    }

    /**
     * Check if the review is for a townhouse.
     */
    public function isTownhouseReview(): bool
    {
        return $this->property_type === 'townhouse';
    }

    /**
     * Check if the review is for a duplex.
     */
    public function isDuplexReview(): bool
    {
        return $this->property_type === 'duplex';
    }

    /**
     * Check if the review is for a studio.
     */
    public function isStudioReview(): bool
    {
        return $this->property_type === 'studio';
    }

    /**
     * Check if the review is for a penthouse.
     */
    public function isPenthouseReview(): bool
    {
        return $this->property_type === 'penthouse';
    }

    /**
     * Check if the review is for land.
     */
    public function isLandReview(): bool
    {
        return $this->property_type === 'land';
    }

    /**
     * Check if the review is for commercial property.
     */
    public function isCommercialReview(): bool
    {
        return $this->property_type === 'commercial';
    }

    /**
     * Check if the review is for office.
     */
    public function isOfficeReview(): bool
    {
        return $this->property_type === 'office';
    }

    /**
     * Get the sentiment score.
     */
    public function getSentimentScore(): float
    {
        // Calculate sentiment based on rating and recommendation
        $ratingScore = ($this->rating - 3) / 2; // Convert 1-5 scale to -1 to +1
        $recommendationScore = 0;
        
        if ($this->recommendsNeighborhood()) {
            $recommendationScore = 0.5;
        } elseif ($this->doesNotRecommendNeighborhood()) {
            $recommendationScore = -0.5;
        }

        return ($ratingScore + $recommendationScore) / 1.5;
    }

    /**
     * Get the helpfulness score.
     */
    public function getHelpfulnessScore(): float
    {
        if ($this->review_count === 0) {
            return 0;
        }

        return $this->helpful_count / $this->review_count;
    }

    /**
     * Get the helpfulness label.
     */
    public function getHelpfulnessLabelAttribute(): string
    {
        $score = $this->helpfulness_score;

        if ($score >= 0.8) {
            return 'مفيد جداً';
        } elseif ($score >= 0.6) {
            return 'مفيد';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'قليل';
        } else {
            return 'ضعيف جداً';
        }
    }

    /**
     * Get the full title with neighborhood.
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->title . ' - ' . $this->neighborhood->name;
        }
        return $this->title;
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'title' => $this->title,
            'content' => $this->content,
            'rating' => $this->rating,
            'recommendation' => $this->recommendation,
            'reviewer_name' => $this->reviewer_name,
            'reviewer_type' => $this->reviewer_type,
            'experience_period' => $this->experience_period,
            'property_type' => $this->property_type,
            'tags' => $this->tags,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
            'pros' => $this->pros,
            'cons' => $this->cons,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    /**
     * Increment helpful count.
     */
    public function incrementHelpfulCount(): void
    {
        $this->increment('helpful_count');
    }

    /**
     * Increment report count.
     */
    public function incrementReportCount(): void
    {
        $this->increment('report_count');
    }

    /**
     * Update rating.
     */
    public function updateRating(float $newRating): void
    {
        $totalRating = $this->rating * $this->review_count;
        $this->review_count++;
        $this->rating = ($totalRating + $newRating) / $this->review_count;
        $this->save();
    }

    /**
     * Bootstrap the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(function ($builder) {
            $builder->whereNull('deleted_at');
        });
    }
}
