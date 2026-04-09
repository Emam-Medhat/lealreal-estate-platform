<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class NeighborhoodGuide extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'title',
        'description',
        'guide_type',
        'status',
        'content',
        'media',
        'contact_info',
        'useful_links',
        'emergency_contacts',
        'transportation_info',
        'local_services',
        'cost_of_living',
        'weather_info',
        'cultural_info',
        'metadata',
        'view_count',
        'rating',
        'review_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'content' => 'array',
        'media' => 'array',
        'contact_info' => 'array',
        'useful_links' => 'array',
        'emergency_contacts' => 'array',
        'transportation_info' => 'array',
        'local_services' => 'array',
        'cost_of_living' => 'array',
        'weather_info' => 'array',
        'cultural_info' => 'array',
        'metadata' => 'array',
        'view_count' => 'integer',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the guide.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Scope a query to only include published guides.
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
     * Scope a query to filter by guide type.
     */
    public function scopeByType(Builder $query, string $guideType): Builder
    {
        return $query->where('guide_type', $guideType);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get featured guides.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('rating', '>=', 4.0);
    }

    /**
     * Scope a query to get popular guides.
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->orderBy('view_count', 'desc');
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
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'مسودة',
            'published' => 'منشور',
            'archived' => 'مؤرشف',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the guide type label.
     */
    public function getGuideTypeLabelAttribute(): string
    {
        $types = [
            'general' => 'عام',
            'living' => 'معيش',
            'schools' => 'مدارس',
            'healthcare' => 'رعاية صحية',
            'shopping' => 'تسوق',
            'transportation' => 'مواصلات',
            'recreation' => 'ترفيه',
            'safety' => 'سلامة',
        ];

        return $types[$this->guide_type] ?? 'غير معروف';
    }

    /**
     * Get the rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->rating . ' / 5';
    }

    /**
     * Get the view count label.
     */
    public function getViewCountLabelAttribute(): string
    {
        return number_format($this->view_count) . ' مشاهدة';
    }

    /**
     * Get the review count label.
     */
    public function getReviewCountLabelAttribute(): string
    {
        return number_format($this->review_count) . ' تقييم';
    }

    /**
     * Get the content sections.
     */
    public function getContentSectionsAttribute(): array
    {
        return $this->content['sections'] ?? [];
    }

    /**
     * Get the content highlights.
     */
    public function getContentHighlightsAttribute(): array
    {
        return $this->content['highlights'] ?? [];
    }

    /**
     * Get the content tips.
     */
    public function getContentTipsAttribute(): array
    {
        return $this->content['tips'] ?? [];
    }

    /**
     * Get the content warnings.
     */
    public function getContentWarningsAttribute(): array
    {
        return $this->content['warnings'] ?? [];
    }

    /**
     * Get the content recommendations.
     */
    public function getContentRecommendationsAttribute(): array
    {
        return $this->content['recommendations'] ?? [];
    }

    /**
     * Get the media images.
     */
    public function getMediaImagesAttribute(): array
    {
        return $this->media['images'] ?? [];
    }

    /**
     * Get the media videos.
     */
    public function getMediaVideosAttribute(): array
    {
        return $this->media['videos'] ?? [];
    }

    /**
     * Get the media documents.
     */
    public function getMediaDocumentsAttribute(): array
    {
        return $this->media['documents'] ?? [];
    }

    /**
     * Get the contact phone.
     */
    public function getContactPhoneAttribute(): ?string
    {
        return $this->contact_info['phone'] ?? null;
    }

    /**
     * Get the contact email.
     */
    public function getContactEmailAttribute(): ?string
    {
        return $this->contact_info['email'] ?? null;
    }

    /**
     * Get the contact website.
     */
    public function getContactWebsiteAttribute(): ?string
    {
        return $this->contact_info['website'] ?? null;
    }

    /**
     * Get the contact address.
     */
    public function getContactAddressAttribute(): ?string
    {
        return $this->contact_info['address'] ?? null;
    }

    /**
     * Get the useful links.
     */
    public function getUsefulLinksListAttribute(): array
    {
        return $this->useful_links ?? [];
    }

    /**
     * Get the emergency contacts.
     */
    public function getEmergencyContactsListAttribute(): array
    {
        return $this->emergency_contacts ?? [];
    }

    /**
     * Get the transportation info.
     */
    public function getTransportationInfoAttribute(): array
    {
        return $this->transportation_info ?? [];
    }

    /**
     * Get the local services.
     */
    public function getLocalServicesAttribute(): array
    {
        return $this->local_services ?? [];
    }

    /**
     * Get the cost of living info.
     */
    public function getCostOfLivingAttribute(): array
    {
        return $this->cost_of_living ?? [];
    }

    /**
     * Get the weather info.
     */
    public function getWeatherInfoAttribute(): array
    {
        return $this->weather_info ?? [];
    }

    /**
     * Get the cultural info.
     */
    public function getCulturalInfoAttribute(): array
    {
        return $this->cultural_info ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Check if the guide is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the guide is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the guide is archived.
     */
    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    /**
     * Check if the guide is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the guide has content sections.
     */
    public function hasContentSections(): bool
    {
        return !empty($this->content_sections);
    }

    /**
     * Check if the guide has highlights.
     */
    public function hasHighlights(): bool
    {
        return !empty($this->content_highlights);
    }

    /**
     * Check if the guide has tips.
     */
    public function hasTips(): bool
    {
        return !empty($this->content_tips);
    }

    /**
     * Check if the guide has warnings.
     */
    public function hasWarnings(): bool
    {
        return !empty($this->content_warnings);
    }

    /**
     * Check if the guide has recommendations.
     */
    public function hasRecommendations(): bool
    {
        return !empty($this->content_recommendations);
    }

    /**
     * Check if the guide has media.
     */
    public function hasMedia(): bool
    {
        return !empty($this->media);
    }

    /**
     * Check if the guide has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->media_images);
    }

    /**
     * Check if the guide has videos.
     */
    public function hasVideos(): bool
    {
        return !empty($this->media_videos);
    }

    /**
     * Check if the guide has documents.
     */
    public function hasDocuments(): bool
    {
        return !empty($this->media_documents);
    }

    /**
     * Check if the guide has contact info.
     */
    public function hasContactInfo(): bool
    {
        return !empty($this->contact_info);
    }

    /**
     * Check if the guide has useful links.
     */
    public function hasUsefulLinks(): bool
    {
        return !empty($this->useful_links);
    }

    /**
     * Check if the guide has emergency contacts.
     */
    public function hasEmergencyContacts(): bool
    {
        return !empty($this->emergency_contacts);
    }

    /**
     * Check if the guide has transportation info.
     */
    public function hasTransportationInfo(): bool
    {
        return !empty($this->transportation_info);
    }

    /**
     * Check if the guide has local services.
     */
    public function hasLocalServices(): bool
    {
        return !empty($this->local_services);
    }

    /**
     * Check if the guide has cost of living info.
     */
    public function hasCostOfLiving(): bool
    {
        return !empty($this->cost_of_living);
    }

    /**
     * Check if the guide has weather info.
     */
    public function hasWeatherInfo(): bool
    {
        return !empty($this->weather_info);
    }

    /**
     * Check if the guide has cultural info.
     */
    public function hasCulturalInfo(): bool
    {
        return !empty($this->cultural_info);
    }

    /**
     * Get the content completeness score.
     */
    public function getContentCompletenessScore(): float
    {
        $score = 0;
        $maxScore = 10;

        if ($this->hasContentSections()) $score += 2;
        if ($this->hasHighlights()) $score += 1;
        if ($this->hasTips()) $score += 1;
        if ($this->hasRecommendations()) $score += 1;
        if ($this->hasMedia()) $score += 1;
        if ($this->hasContactInfo()) $score += 1;
        if ($this->hasUsefulLinks()) $score += 1;
        if ($this->hasEmergencyContacts()) $score += 1;
        if ($this->hasTransportationInfo()) $score += 1;

        return $score / $maxScore;
    }

    /**
     * Get the content completeness label.
     */
    public function getContentCompletenessLabelAttribute(): string
    {
        $score = $this->content_completeness_score;

        if ($score >= 0.8) {
            return 'مكتمل';
        } elseif ($score >= 0.6) {
            return 'جيد جداً';
        } elseif ($score >= 0.4) {
            return 'جيد';
        } elseif ($score >= 0.2) {
            return 'ضعيف';
        } else {
            return 'ضعيف جداً';
        }
    }

    /**
     * Get the popularity score.
     */
    public function getPopularityScore(): float
    {
        // Calculate popularity based on views, rating, and reviews
        $viewScore = min($this->view_count / 1000, 1) * 0.4;
        $ratingScore = ($this->rating / 5) * 0.3;
        $reviewScore = min($this->review_count / 100, 1) * 0.3;

        return $viewScore + $ratingScore + $reviewScore;
    }

    /**
     * Get the popularity label.
     */
    public function getPopularityLabelAttribute(): string
    {
        $score = $this->popularity_score;

        if ($score >= 0.8) {
            return 'شعبي جداً';
        } elseif ($score >= 0.6) {
            return 'شعبي';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'قليل';
        } else {
            return 'قليل جداً';
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
            'description' => $this->description,
            'guide_type' => $this->guide_type,
            'status' => $this->status,
            'rating' => $this->rating,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
            'content_sections' => $this->content_sections,
            'highlights' => $this->content_highlights,
            'tips' => $this->content_tips,
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
