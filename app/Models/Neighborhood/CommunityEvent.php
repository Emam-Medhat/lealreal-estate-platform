<?php

namespace App\Models\Neighborhood;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class CommunityEvent extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'community_id',
        'title',
        'description',
        'event_type',
        'status',
        'start_date',
        'end_date',
        'location',
        'latitude',
        'longitude',
        'organizer_name',
        'organizer_email',
        'organizer_phone',
        'max_participants',
        'current_participants',
        'age_restriction',
        'price_info',
        'schedule',
        'requirements',
        'facilities',
        'contact_info',
        'social_sharing',
        'images',
        'cover_image',
        'gallery',
        'tags',
        'metadata',
        'view_count',
        'rating',
        'review_count',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'community_id' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'latitude' => 'decimal:8,6',
        'longitude' => 'decimal:9,6',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'age_restriction' => 'string',
        'price_info' => 'array',
        'schedule' => 'array',
        'requirements' => 'array',
        'facilities' => 'array',
        'contact_info' => 'array',
        'social_sharing' => 'array',
        'images' => 'array',
        'cover_image' => 'string',
        'gallery' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'view_count' => 'integer',
        'rating' => 'decimal:2,1',
        'review_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the community that owns the event.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    /**
     * Scope a query to only include published events.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to filter by community.
     */
    public function scopeByCommunity(Builder $query, int $communityId): Builder
    {
        return $query->where('community_id', $communityId);
    }

    /**
     * Scope a query to filter by event type.
     */
    public function scopeByType(Builder $query, string $eventType): Builder
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope a query to filter by status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to get upcoming events.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope a query to get past events.
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('end_date', '<', now());
    }

    /**
     * Scope a query to get events today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('start_date', today());
    }

    /**
     * Scope a query to get events this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('start_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    /**
     * Scope a query to get events this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('start_date', now()->month);
    }

    /**
     * Scope a query to get events with available spots.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereRaw('(max_participants IS NULL OR current_participants < max_participants)');
    }

    /**
     * Scope a query to get free events.
     */
    public function scopeFree(Builder $query): Builder
    {
        return $query->whereJsonContains('price_info', ['is_free' => true]);
    }

    /**
     * Scope a query to get events by rating range.
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
            'cancelled' => 'ملغاة',
            'completed' => 'مكتملة',
        ];

        return $statuses[$this->status] ?? 'غير معروف';
    }

    /**
     * Get the event type label.
     */
    public function getEventTypeLabelAttribute(): string
    {
        $types = [
            'social' => 'اجتماعي',
            'educational' => 'تعليمي',
            'sports' => 'رياضي',
            'cultural' => 'ثقافي',
            'religious' => 'ديني',
            'charity' => 'خيري',
            'business' => 'تجاري',
            'entertainment' => 'ترفيه',
            'health' => 'صحي',
            'other' => 'أخرى',
        ];

        return $types[$this->event_type] ?? 'غير معروف';
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
     * Get the start date label.
     */
    public function getStartDateLabelAttribute(): string
    {
        return $this->start_date ? $this->start_date->format('Y-m-d H:i') : 'غير محدد';
    }

    /**
     * Get the end date label.
     */
    public function getEndDateLabelAttribute(): string
    {
        return $this->end_date ? $this->end_date->format('Y-m-d H:i') : 'غير محدد';
    }

    /**
     * Get the duration label.
     */
    public function getDurationLabelAttribute(): string
    {
        if (!$this->start_date || !$this->end_date) {
            return 'غير محدد';
        }

        $duration = $this->start_date->diffInHours($this->end_date);
        
        if ($duration < 1) {
            return $this->start_date->diffInMinutes($this->end_date) . ' دقيقة';
        } elseif ($duration < 24) {
            return $duration . ' ساعة';
        } else {
            return $this->start_date->diffInDays($this->end_date) . ' يوم';
        }
    }

    /**
     * Get the participants label.
     */
    public function getParticipantsLabelAttribute(): string
    {
        if ($this->max_participants === null) {
            return $this->current_participants . ' مشارك';
        }
        return $this->current_participants . ' / ' . $this->max_participants . ' مشارك';
    }

    /**
     * Get the availability label.
     */
    public function getAvailabilityLabelAttribute(): string
    {
        if ($this->max_participants === null) {
            return 'غير محدد';
        }

        if ($this->current_participants >= $this->max_participants) {
            'ممتلئ بالكامل';
        } else {
            $available = $this->max_participants - $this->current_participants;
            return $available . ' مكان متاح';
        }
    }

    /**
     * Get the age restriction label.
     */
    public function getAgeRestrictionLabelAttribute(): string
    {
        $restrictions = [
            'all' => 'الجميع',
            '18+' => '18 سنة فأكثر',
            '21+' => '21 سنة فأكثر',
            'family' => 'عائلي',
            'kids' => 'أطفال',
        ];

        return $restrictions[$this->age_restriction] ?? 'الجميع';
    }

    /**
     * Get the price info.
     */
    public function getPriceInfoAttribute(): array
    {
        return $this->price_info ?? [];
    }

    /**
     * Get the price label.
     */
    public function getPriceLabelAttribute(): string
    {
        $priceInfo = $this->price_info;
        
        if (empty($priceInfo)) {
            return 'غير محدد';
        }

        if ($priceInfo['is_free'] ?? false) {
            return 'مجاني';
        }

        $price = $priceInfo['price'] ?? 0;
        $currency = $priceInfo['currency'] ?? 'ريال';

        return number_format($price, 2) . ' ' . $currency;
    }

    /**
     * Get the schedule sessions.
     */
    public function getScheduleSessionsAttribute(): array
    {
        return $this->schedule['sessions'] ?? [];
    }

    /**
     * Get the requirements list.
     */
    public function getRequirementsListAttribute(): string
    {
        return implode(', ', $this->requirements ?? []);
    }

    /**
     * Get the facilities list.
     */
    public function getFacilitiesListAttribute(): string
    {
        return implode(', ', $this->facilities ?? []);
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
     * Get the gallery list.
     */
    public function getGalleryListAttribute(): array
    {
        return $this->gallery ?? [];
    }

    /**
     * Get the metadata as JSON.
     */
    public function getMetadataAttribute(): string
    {
        return json_encode($this->metadata ?? []);
    }

    /**
     * Get the formatted coordinates.
     */
    public function getCoordinatesAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            return $this->latitude . ', ' . $this->longitude;
        }
        return 'غير محدد';
    }

    /**
     * Get the contact info array.
     */
    public function getContactInfoArrayAttribute(): array
    {
        return [
            'email' => $this->organizer_email,
            'phone' => $this->organizer_phone,
            'website' => $this->contact_info['website'] ?? null,
        ];
    }

    /**
     * Check if the event is published.
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if the event is a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Check if the event is cancelled.
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }

    /**
     * Check if the event is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->start_date >= now();
    }

    /**
     * Check if the event is past.
     */
    public function isPast(): bool
    {
        return $this->end_date < now();
    }
    
    /**
     * Check if the event is today.
     */
    public function isToday(): bool
    {
        return $this->start_date->isToday();
    }

    /**
     * Check if the event is happening now.
     */
    public function isHappeningNow(): bool
    {
        return $this->start_date <= now() && $this->end_date >= now();
    }

    /**
     * Check if the event is free.
     */
    public function isFree(): bool
    {
        return ($this->price_info['is_free'] ?? false);
    }

    /**
     * Check if the event has capacity.
     */
    public function hasCapacity(): bool
    {
        return $this->max_participants !== null;
    }

    /**
     * Check if the event is full.
     */
    public function isFull(): bool
    {
        return $this->hasCapacity() && $this->current_participants >= $this->max_participants;
    }

    /**
     * Check if the event has available spots.
     */
    public function hasAvailableSpots(): bool
    {
        return $this->hasCapacity() && $this->current_participants < $this->max_participants;
    }

    /**
     * Check if the event has age restriction.
     */
    public function hasAgeRestriction(): bool
    {
        return !empty($this->age_restriction) && $this->age_restriction !== 'all';
    }

    /**
     * Check if the event is for adults only.
     */
    public function isAdultsOnly(): bool
    {
        return in_array($this->age_restriction, ['18+', '21+']);
    }

    /**
     * Check if the event is for families.
     */
    public function isForFamilies(): bool
    {
        return $this->age_restriction === 'family';
    }

    /**
     * Check if the event is for kids.
     */
    public function isForKids(): bool
    {
        return $this->age_restriction === 'kids';
    }

    /**
     * Check if the event has schedule.
     */
    public function hasSchedule(): bool
    {
        return !empty($this->schedule);
    }

    /**
     * Check if the event has requirements.
     */
    public function hasRequirements(): bool
    {
        return !empty($this->requirements);
    }

    /**
     * Check if the event has facilities.
     */
    public function hasFacilities(): bool
    {
        return !empty($this->facilities);
    }

    /**
     * Check if the event has images.
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Check if the event has a cover image.
     */
    public function hasCoverImage(): bool
    {
        return !empty($this->cover_image);
    }

    /**
     * Check if the event has a gallery.
     */
    public function hasGallery(): bool
    {
        return !empty($this->gallery);
    }

    /**
     * Check if the event is highly rated.
     */
    public function isHighlyRated(): bool
    {
        return $this->rating >= 4.0;
    }

    /**
     * Check if the event has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Get the event status label.
     */
    public function getEventStatusAttribute(): string
    {
        if ($this->isHappeningNow()) {
            'جاري الآن';
        } elseif ($this->isUpcoming()) {
            'قادم';
        } elseif ($this->isPast()) {
            'منتهي';
        } else {
            'غير محدد';
        }
    }

    /**
     * Get the popularity score.
     */
    public function getPopularityScore(): float
    {
        // Calculate popularity based on views, rating, and participants
        $viewScore = min($this->view_count / 1000, 1) * 0.4;
        $ratingScore = ($this->rating / 5) * 0.3;
        $participantScore = min($this->current_participants / 100, 1) * 0.3;

        return $viewScore + $ratingScore + $participantScore;
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
     * Get the full title with community.
     */
    public function getFullTitleAttribute(): string
    {
        if ($this->community) {
            return $this->title . ' - ' . $this->community->name;
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
            'event_type' => $this->event_type,
            'status' => $this->status,
            'rating' => $this->rating,
            'location' => $this->location,
            'organizer_name' => $this->organizer_name,
            'start_date' => $this->start_date->format('Y-m-d H:i'),
            'end_date' => $this->end_date->format('Y-m-d H:i'),
            'tags' => $this->tags,
            'community' => $this->community?->name ?? '',
            'neighborhood' => $this->community?->neighborhood?->name ?? '',
            'city' => $this->community?->neighborhood?->city ?? '',
            'district' => $this->community?->neighborhood?->district ?? '',
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
     * Add participant.
     */
    public function addParticipant(): void
    {
        if ($this->hasCapacity()) {
            $this->increment('current_participants');
        }
    }

    /**
     * Remove participant.
     */
    public function removeParticipant(): void
    {
        if ($this->current_participants > 0) {
            $this->decrement('current_participants');
        }
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
