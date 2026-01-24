<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Community extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'neighborhood_id',
        'name',
        'description',
        'type',
        'status',
        'founded_date',
        'member_count',
        'property_count',
        'activity_level',
        'contact_info',
        'social_media',
        'rules',
        'amenities',
        'services',
        'events_count',
        'posts_count',
        'news_count',
        'rating',
        'review_count',
        'verification_status',
        'featured',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'neighborhood_id' => 'integer',
        'founded_date' => 'date',
        'member_count' => 'integer',
        'property_count' => 'integer',
        'events_count' => 'integer',
        'posts_count' => 'integer',
        'news_count' => 'integer',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'verification_status' => 'string',
        'featured' => 'boolean',
        'contact_info' => 'array',
        'social_media' => 'array',
        'rules' => 'array',
        'amenities' => 'array',
        'services' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the neighborhood that owns the community.
     */
    public function neighborhood(): BelongsTo
    {
        return $this->belongsTo(Neighborhood::class, 'neighborhood_id');
    }

    /**
     * Get the events for the community.
     */
    public function events(): HasMany
    {
        return $this->hasMany(CommunityEvent::class, 'community_id');
    }

    /**
     * Get the posts for the community.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(ResidentPost::class, 'community_id');
    }

    /**
     * Get the news for the community.
     */
    public function news(): HasMany
    {
        return $this->hasMany(CommunityNews::class, 'community_id');
    }

    /**
     * Get the businesses in the community.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(LocalBusiness::class, 'community_id');
    }

    /**
     * Get the amenities in the community.
     */
    public function amenities(): HasMany
    {
        return $this->hasMany(CommunityAmenity::class, 'community_id');
    }

    /**
     * Scope a query to only include active communities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by neighborhood.
     */
    public function scopeByNeighborhood(Builder $query, int $neighborhoodId): Builder
    {
        return $query->where('neighborhood_id', $neighborhoodId);
    }

    /**
     * Scope a query to filter by type.
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to filter by activity level.
     */
    public function scopeByActivityLevel(Builder $query, string $activityLevel): Builder
    {
        return $query->where('activity_level', $activityLevel);
    }

    /**
     * Scope a query to filter by verification status.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verification_status', 'verified');
    }

    /**
     * Scope a query to get featured communities.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to filter by member count range.
     */
    public function scopeByMemberCount(Builder $query, $min, $max = null): Builder
    {
        if ($min !== null) {
            $query->where('member_count', '>=', $min);
        }
        if ($max !== null) {
            $query->where('member_count', '<=', $max);
        }
        return $query;
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
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'suspended' => 'معلق',
            'pending' => 'قيد المراجعة',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the type label.
     */
    public function getTypeLabelAttribute(): string
    {
        $types = [
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'professional' => 'مهني',
            'educational' => 'تعليمي',
            'religious' => 'ديني',
            'cultural' => 'ثقافي',
            'sports' => 'رياضي',
            'recreational' => 'ترفيهي',
        ];

        return $types[$this->type] ?? 'غير معروف';
    }

    /**
     * Get the activity level label.
     */
    public function getActivityLevelLabelAttribute(): string
    {
        $levels = [
            'high' => 'عالي',
            'medium' => 'متوسط',
            'low' => 'منخفض',
            'inactive' => 'غير نشط',
        ];

        return $levels[$this->activity_level] ?? 'غير معروف';
    }

    /**
     * Get the verification status label.
     */
    public function getVerificationStatusLabelAttribute(): string
    {
        $statuses = [
            'verified' => 'موثق',
            'pending' => 'قيد المراجعة',
            'rejected' => 'مرفوض',
            'unverified' => 'غير موثق',
        ];

        return $statuses[$this->verification_status] ?? 'غير معروف';
    }

    /**
     * Get the member count label.
     */
    public function getMemberCountLabelAttribute(): string
    {
        return number_format($this->member_count) . ' عضو';
    }

    /**
     * Get the property count label.
     */
    public function getPropertyCountLabelAttribute(): string
    {
        return number_format($this->property_count) . ' عقار';
    }

    /**
     * Get the events count label.
     */
    public function getEventsCountLabelAttribute(): string
    {
        return number_format($this->events_count) . ' فعالية';
    }

    /**
     * Get the posts count label.
     */
    public function getPostsCountLabelAttribute(): string
    {
        return number_format($this->posts_count) . ' منشور';
    }

    /**
     * Get the news count label.
     */
    public function getNewsCountLabelAttribute(): string
    {
        return number_format($this->news_count) . ' خبر';
    }

    /**
     * Get the rating label.
     */
    public function getRatingLabelAttribute(): string
    {
        return $this->rating . ' / 5';
    }

    /**
     * Get the founded date label.
     */
    public function getFoundedDateLabelAttribute(): string
    {
        return $this->founded_date ? $this->founded_date->format('Y-m-d') : 'غير محدد';
    }

    /**
     * Get the age of the community.
     */
    public function getAgeAttribute(): int
    {
        return $this->founded_date ? $this->founded_date->age : 0;
    }

    /**
     * Get the age label.
     */
    public function getAgeLabelAttribute(): string
    {
        $age = $this->age;
        
        if ($age === 0) {
            return 'غير محدد';
        } elseif ($age < 1) {
            return 'أقل من سنة';
        } elseif ($age < 5) {
            return $age . ' سنوات';
        } elseif ($age < 10) {
            return $age . ' سنوات';
        } else {
            return $age . ' سنة';
        }
    }

    /**
     * Get the contact info as formatted string.
     */
    public function getContactInfoStringAttribute(): string
    {
        if (!$this->contact_info) {
            return 'غير محدد';
        }

        $parts = [];
        if (!empty($this->contact_info['email'])) {
            $parts[] = $this->contact_info['email'];
        }
        if (!empty($this->contact_info['phone'])) {
            $parts[] = $this->contact_info['phone'];
        }
        if (!empty($this->contact_info['website'])) {
            $parts[] = $this->contact_info['website'];
        }

        return implode(' | ', $parts);
    }

    /**
     * Get the social media links as array.
     */
    public function getSocialMediaLinksAttribute(): array
    {
        return $this->social_media ?? [];
    }

    /**
     * Get the amenities list.
     */
    public function getAmenitiesListAttribute(): string
    {
        return implode(', ', $this->amenities ?? []);
    }

    /**
     * Get the services list.
     */
    public function getServicesListAttribute(): string
    {
        return implode(', ', $this->services ?? []);
    }

    /**
     * Get the rules list.
     */
    public function getRulesListAttribute(): string
    {
        return implode(', ', $this->rules ?? []);
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Check if the community is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the community is verified.
     */
    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    /**
     * Check if the community is featured.
     */
    public function isFeatured(): bool
    {
        return $this->featured;
    }

    /**
     * Check if the community has high activity.
     */
    public function hasHighActivity(): bool
    {
        return $this->activity_level === 'high';
    }

    /**
     * Check if the community has members.
     */
    public function hasMembers(): bool
    {
        return $this->member_count > 0;
    }

    /**
     * Check if the community has properties.
     */
    public function hasProperties(): bool
    {
        return $this->property_count > 0;
    }

    /**
     * Check if the community has events.
     */
    public function hasEvents(): bool
    {
        return $this->events_count > 0;
    }

    /**
     * Check if the community has posts.
     */
    public function hasPosts(): bool
    {
        return $this->posts_count > 0;
    }

    /**
     * Check if the community has news.
     */
    public function hasNews(): bool
    {
        return $this->news_count > 0;
    }

    /**
     * Check if the community is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the community is established.
     */
    public function isEstablished(): bool
    {
        return $this->age >= 1;
    }

    /**
     * Check if the community is large.
     */
    public function isLarge(): bool
    {
        return $this->member_count >= 100;
    }

    /**
     * Check if the community is medium sized.
     */
    public function isMedium(): bool
    {
        return $this->member_count >= 20 && $this->member_count < 100;
    }

    /**
     * Check if the community is small.
     */
    public function isSmall(): bool
    {
        return $this->member_count < 20;
    }

    /**
     * Get the engagement score.
     */
    public function getEngagementScore(): float
    {
        // Calculate engagement based on various factors
        $scores = [
            'activity_level' => $this->getActivityLevelScore() * 0.3,
            'member_count' => $this->getMemberCountScore() * 0.2,
            'events_count' => $this->getEventsCountScore() * 0.2,
            'posts_count' => $this->getPostsCountScore() * 0.15,
            'news_count' => $this->getNewsCountScore() * 0.15,
        ];

        return array_sum($scores);
    }

    /**
     * Get activity level score.
     */
    private function getActivityLevelScore(): float
    {
        $scores = [
            'high' => 1.0,
            'medium' => 0.7,
            'low' => 0.4,
            'inactive' => 0.1,
        ];

        return $scores[$this->activity_level] ?? 0.5;
    }

    /**
     * Get member count score.
     */
    private function getMemberCountScore(): float
    {
        if ($this->member_count >= 100) {
            return 1.0;
        } elseif ($this->member_count >= 50) {
            return 0.8;
        } elseif ($this->member_count >= 20) {
            return 0.6;
        } elseif ($this->member_count >= 10) {
            return 0.4;
        } else {
            return 0.2;
        }
    }

    /**
     * Get events count score.
     */
    private function getEventsCountScore(): float
    {
        if ($this->events_count >= 50) {
            return 1.0;
        } elseif ($this->events_count >= 20) {
            return 0.8;
        } elseif ($this->events_count >= 10) {
            return 0.6;
        } elseif ($this->events_count >= 5) {
            return 0.4;
        } else {
            return 0.2;
        }
    }

    /**
     * Get posts count score.
     */
    private function getPostsCountScore(): float
    {
        if ($this->posts_count >= 100) {
            return 1.0;
        } elseif ($this->posts_count >= 50) {
            return 0.8;
        } elseif ($this->posts_count >= 20) {
            return 0.6;
        } elseif ($this->posts_count >= 10) {
            return 0.4;
        } else {
            return 0.2;
        }
    }

    /**
     * Get news count score.
     */
    private function getNewsCountScore(): float
    {
        if ($this->news_count >= 50) {
            return 1.0;
        } elseif ($this->news_count >= 20) {
            return 0.8;
        } elseif ($this->news_count >= 10) {
            return 0.6;
        } elseif ($this->news_count >= 5) {
            return 0.4;
        } else {
            return 0.2;
        }
    }

    /**
     * Get the size category.
     */
    public function getSizeCategory(): string
    {
        if ($this->isLarge()) {
            return 'large';
        } elseif ($this->isMedium()) {
            return 'medium';
        } else {
            return 'small';
        }
    }

    /**
     * Get the size category label.
     */
    public function getSizeCategoryLabelAttribute(): string
    {
        $categories = [
            'large' => 'كبير',
            'medium' => 'متوسط',
            'small' => 'صغير',
        ];

        return $categories[$this->size_category] ?? 'غير معروف';
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        if ($this->neighborhood) {
            return $this->neighborhood->city . ', ' . $this->neighborhood->district . ', ' . $this->neighborhood->name;
        }
        return 'غير محدد';
    }

    /**
     * Get the search index.
     */
    public function getSearchIndex(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->type,
            'status' => $this->status,
            'activity_level' => $this->activity_level,
            'verification_status' => $this->verification_status,
            'rating' => $this->rating,
            'member_count' => $this->member_count,
            'property_count' => $this->property_count,
            'neighborhood' => $this->neighborhood?->name ?? '',
            'city' => $this->neighborhood?->city ?? '',
            'district' => $this->neighborhood?->district ?? '',
        ];
    }

    /**
     * Get the engagement score label.
     */
    public function getEngagementScoreLabelAttribute(): string
    {
        $score = $this->engagement_score;
        
        if ($score >= 0.8) {
            return 'عالي جداً';
        } elseif ($score >= 0.6) {
            return 'عالي';
        } elseif ($score >= 0.4) {
            return 'متوسط';
        } elseif ($score >= 0.2) {
            return 'منخفض';
        } else {
            return 'ضعيف جداً';
        }
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
