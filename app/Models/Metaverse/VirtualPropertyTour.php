<?php

namespace App\Models\Metaverse;

use App\Models\User;
use App\Models\VirtualWorld;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class VirtualPropertyTour extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'tour_type',
        'metaverse_property_id',
        'metaverse_showroom_id',
        'guide_id',
        'duration_minutes',
        'max_participants',
        'price',
        'currency',
        'languages',
        'difficulty_level',
        'accessibility_features',
        'equipment_required',
        'tour_points',
        'schedule_settings',
        'interactive_elements',
        'multimedia_content',
        'navigation_settings',
        'customization_options',
        'status',
        'is_active',
        'is_featured',
        'view_count',
        'participant_count',
        'session_count',
        'rating_average',
        'rating_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'languages' => 'array',
        'accessibility_features' => 'array',
        'equipment_required' => 'array',
        'tour_points' => 'array',
        'schedule_settings' => 'array',
        'interactive_elements' => 'array',
        'multimedia_content' => 'array',
        'navigation_settings' => 'array',
        'customization_options' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'view_count' => 'integer',
        'participant_count' => 'integer',
        'session_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function guide(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    public function metaverseProperty(): BelongsTo
    {
        return $this->belongsTo(MetaverseProperty::class, 'metaverse_property_id');
    }

    public function showroom(): BelongsTo
    {
        return $this->belongsTo(MetaverseShowroom::class, 'metaverse_showroom_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(TourSession::class, 'virtual_property_tour_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TourParticipant::class, 'virtual_property_tour_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(TourImage::class, 'virtual_property_tour_id');
    }

    public function mediaFiles(): HasMany
    {
        return $this->hasMany(TourMediaFile::class, 'virtual_property_tour_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(TourReview::class, 'virtual_property_tour_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(TourBooking::class, 'virtual_property_tour_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(TourSchedule::class, 'virtual_property_tour_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByTourType($query, $tourType)
    {
        return $query->where('tour_type', $tourType);
    }

    public function scopeByGuide($query, $guideId)
    {
        return $query->where('guide_id', $guideId);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice = null)
    {
        $query->where('price', '>=', $minPrice);
        if ($maxPrice) {
            $query->where('price', '<=', $maxPrice);
        }
        return $query;
    }

    public function scopeByDuration($query, $minDuration, $maxDuration = null)
    {
        $query->where('duration_minutes', '>=', $minDuration);
        if ($maxDuration) {
            $query->where('duration_minutes', '<=', $maxDuration);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getTourTypeTextAttribute(): string
    {
        return match($this->tour_type) {
            'guided' => 'موجه',
            'self_guided' => 'ذاتي التوجيه',
            'group' => 'جماعي',
            'private' => 'خاص',
            'virtual_reality' => 'واقع افتراضي',
            'augmented_reality' => 'واقع معزز',
            default => $this->tour_type,
        };
    }

    public function getDifficultyLevelTextAttribute(): string
    {
        return match($this->difficulty_level) {
            'beginner' => 'مبتدئ',
            'intermediate' => 'متوسط',
            'advanced' => 'متقدم',
            'expert' => 'خبير',
            default => $this->difficulty_level,
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'maintenance' => 'تحت الصيانة',
            'suspended' => 'موقوف',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        $minutes = $this->duration_minutes;
        
        if ($minutes < 60) {
            return $minutes . ' دقيقة';
        } else {
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;
            return $hours . ' ساعة' . ($remainingMinutes > 0 ? ' و ' . $remainingMinutes . ' دقيقة' : '');
        }
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsPopularAttribute(): bool
    {
        return $this->participant_count > 50 || $this->rating_average >= 4.5;
    }

    public function getIsPremiumAttribute(): bool
    {
        return $this->price > 100 || $this->is_featured;
    }

    public function getCapacityUsageAttribute(): float
    {
        return $this->max_participants > 0 ? ($this->participant_count / $this->max_participants) * 100 : 0;
    }

    public function getIsFullAttribute(): bool
    {
        return $this->participant_count >= $this->max_participants;
    }

    public function getAvailableLanguagesAttribute(): array
    {
        return $this->languages ?? ['en'];
    }

    public function getTourComplexityAttribute(): string
    {
        $complexity = 0;
        
        // Base complexity from tour points
        $complexity += count($this->tour_points ?? []) * 10;
        
        // Add complexity from interactive elements
        $complexity += count($this->interactive_elements ?? []) * 5;
        
        // Add complexity from multimedia content
        $complexity += count($this->multimedia_content ?? []) * 3;
        
        if ($complexity < 50) {
            return 'simple';
        } elseif ($complexity < 100) {
            return 'moderate';
        } else {
            return 'complex';
        }
    }

    // Methods
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function incrementParticipant(): void
    {
        $this->increment('participant_count');
    }

    public function incrementSession(): void
    {
        $this->increment('session_count');
    }

    public function calculateRating(): void
    {
        $averageRating = $this->reviews()->avg('rating');
        $ratingCount = $this->reviews()->count();
        
        $this->update([
            'rating_average' => $averageRating,
            'rating_count' => $ratingCount,
        ]);
    }

    public function createSession(array $data): TourSession
    {
        return $this->sessions()->create([
            'user_id' => $data['user_id'],
            'scheduled_time' => $data['scheduled_time'],
            'participant_count' => $data['participant_count'] ?? 1,
            'total_price' => $this->price * ($data['participant_count'] ?? 1),
            'currency' => $this->currency,
            'special_requirements' => $data['special_requirements'] ?? null,
            'customization_choices' => $data['customization_choices'] ?? [],
            'status' => 'confirmed',
            'payment_status' => $this->price > 0 ? 'pending' : 'free',
            'booked_at' => now(),
        ]);
    }

    public function bookTour(array $bookingData): TourBooking
    {
        return $this->bookings()->create([
            'user_id' => $bookingData['user_id'],
            'scheduled_time' => $bookingData['scheduled_time'],
            'participant_count' => $bookingData['participant_count'] ?? 1,
            'total_price' => $this->price * ($bookingData['participant_count'] ?? 1),
            'currency' => $this->currency,
            'special_requirements' => $bookingData['special_requirements'] ?? null,
            'status' => 'confirmed',
            'payment_status' => $this->price > 0 ? 'pending' : 'free',
            'booked_at' => now(),
        ]);
    }

    public function canBeBooked(): bool
    {
        return $this->is_active && 
               $this->status === 'active' && 
               !$this->getIsFullAttribute();
    }

    public function getAvailableTimeSlots(): array
    {
        $availableTimes = $this->schedule_settings['available_times'] ?? [];
        $bookedTimes = $this->sessions()
            ->where('scheduled_time', '>', now())
            ->where('status', '!=', 'cancelled')
            ->pluck('scheduled_time')
            ->map(function ($time) {
                return $time->format('Y-m-d H:i');
            })
            ->toArray();

        return array_diff($availableTimes, $bookedTimes);
    }

    public function isTimeSlotAvailable(string $dateTime): bool
    {
        $bookedCount = $this->sessions()
            ->where('scheduled_time', $dateTime)
            ->where('status', '!=', 'cancelled')
            ->count();

        return $bookedCount === 0;
    }

    public function getTourStatistics(): array
    {
        return [
            'total_sessions' => $this->session_count,
            'total_participants' => $this->participant_count,
            'average_rating' => $this->rating_average,
            'total_reviews' => $this->rating_count,
            'completion_rate' => $this->calculateCompletionRate(),
            'average_duration' => $this->calculateAverageDuration(),
            'revenue' => $this->calculateTotalRevenue(),
            'popular_languages' => $this->getPopularLanguages(),
            'peak_hours' => $this->getPeakHours(),
        ];
    }

    public function getParticipantDemographics(): array
    {
        return [
            'by_country' => $this->participants()
                ->join('users', 'tour_participants.user_id', '=', 'users.id')
                ->selectRaw('users.country, COUNT(*) as count')
                ->groupBy('users.country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            
            'by_experience' => $this->participants()
                ->join('users', 'tour_participants.user_id', '=', 'users.id')
                ->selectRaw('users.experience_level, COUNT(*) as count')
                ->groupBy('users.experience_level')
                ->orderBy('count', 'desc')
                ->get(),
        ];
    }

    public function getEngagementMetrics(): array
    {
        return [
            'interaction_frequency' => $this->calculateInteractionFrequency(),
            'popular_points' => $this->getPopularPoints(),
            'drop_off_points' => $this->getDropOffPoints(),
            'time_per_point' => $this->getTimePerPoint(),
            'completion_rate' => $this->calculateCompletionRate(),
        ];
    }

    public function getRevenueMetrics(): array
    {
        return [
            'total_revenue' => $this->calculateTotalRevenue(),
            'revenue_by_month' => $this->getRevenueByMonth(),
            'revenue_by_language' => $this->getRevenueByLanguage(),
            'average_revenue_per_session' => $this->calculateAverageRevenuePerSession(),
        ];
    }

    public function generateTourUrl(): string
    {
        return route('metaverse.tours.start', $this->id);
    }

    public function generateBookingUrl(): string
    {
        return route('metaverse.tours.book', $this->id);
    }

    public function generateShareUrl(): string
    {
        return route('metaverse.tours.share', $this->id);
    }

    public function getThumbnailUrl(): string
    {
        $image = $this->images()->first();
        return $image ? asset('storage/' . $image->path) : asset('images/default-tour.jpg');
    }

    public function getGalleryUrls(): array
    {
        return $this->images()
            ->pluck('path')
            ->map(function ($path) {
                return asset('storage/' . $path);
            })
            ->toArray();
    }

    public function getTourPointsData(): array
    {
        return array_map(function ($point) {
            return [
                'id' => $point['id'] ?? null,
                'title' => $point['title'] ?? '',
                'description' => $point['description'] ?? '',
                'coordinates' => $point['coordinates'] ?? '',
                'duration' => $point['duration'] ?? 0,
                'media_type' => $point['media_type'] ?? 'text',
                'media_url' => $point['media_url'] ?? '',
                'interaction_type' => $point['interaction_type'] ?? '',
                'order' => $point['order'] ?? 0,
            ];
        }, $this->tour_points ?? []);
    }

    public function getAccessibilityInfo(): array
    {
        return [
            'features' => $this->accessibility_features ?? [],
            'supported_languages' => $this->languages ?? ['en'],
            'difficulty_level' => $this->difficulty_level,
            'equipment_required' => $this->equipment_required ?? [],
            'estimated_duration' => $this->getFormattedDurationAttribute(),
        ];
    }

    public function getMultimediaAssets(): array
    {
        return [
            'images' => $this->images()->get(),
            'media_files' => $this->mediaFiles()->get(),
            'tour_point_media' => $this->getTourPointMedia(),
        ];
    }

    private function calculateCompletionRate(): float
    {
        $totalParticipants = $this->participants()->count();
        $completedParticipants = $this->participants()
            ->whereNotNull('completed_at')
            ->count();
        
        return $totalParticipants > 0 ? ($completedParticipants / $totalParticipants) * 100 : 0;
    }

    private function calculateAverageDuration(): float
    {
        return $this->participants()
            ->whereNotNull('duration')
            ->avg('duration') ?? 0;
    }

    private function calculateTotalRevenue(): float
    {
        return $this->sessions()
            ->where('payment_status', 'paid')
            ->sum('total_price');
    }

    private function getPopularLanguages(): array
    {
        return $this->sessions()
            ->join('users', 'tour_sessions.user_id', '=', 'users.id')
            ->selectRaw('users.preferred_language, COUNT(*) as count')
            ->groupBy('users.preferred_language')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getPeakHours(): array
    {
        return $this->sessions()
            ->selectRaw('HOUR(scheduled_time) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function calculateInteractionFrequency(): float
    {
        $totalInteractions = $this->participants()
            ->whereNotNull('interaction_data')
            ->count();
        
        $totalParticipants = $this->participants()->count();
        
        return $totalParticipants > 0 ? $totalInteractions / $totalParticipants : 0;
    }

    private function getPopularPoints(): array
    {
        return $this->participants()
            ->selectRaw('current_point, COUNT(*) as count')
            ->groupBy('current_point')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getDropOffPoints(): array
    {
        return $this->participants()
            ->whereNull('completed_at')
            ->selectRaw('current_point, COUNT(*) as count')
            ->groupBy('current_point')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getTimePerPoint(): array
    {
        // This would require detailed tracking of time spent at each point
        // For now, return placeholder data
        return [];
    }

    private function getRevenueByMonth(): array
    {
        return $this->sessions()
            ->where('payment_status', 'paid')
            ->selectRaw('YEAR_MONTH(scheduled_time) as month, SUM(total_price) as revenue')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->toArray();
    }

    private function getRevenueByLanguage(): array
    {
        return $this->sessions()
            ->where('payment_status', 'paid')
            ->join('users', 'tour_sessions.user_id', '=', 'users.id')
            ->selectRaw('users.preferred_language, SUM(total_price) as revenue')
            ->groupBy('users.preferred_language')
            ->orderBy('revenue', 'desc')
            ->get()
            ->toArray();
    }

    private function calculateAverageRevenuePerSession(): float
    {
        $paidSessions = $this->sessions()->where('payment_status', 'paid')->count();
        $totalRevenue = $this->calculateTotalRevenue();
        
        return $paidSessions > 0 ? $totalRevenue / $paidSessions : 0;
    }

    private function getTourPointMedia(): array
    {
        $media = [];
        
        foreach ($this->tour_points ?? [] as $point) {
            if (isset($point['media_url']) && $point['media_url']) {
                $media[$point['id']] = [
                    'point_id' => $point['id'],
                    'media_type' => $point['media_type'],
                    'media_url' => $point['media_url'],
                ];
            }
        }
        
        return $media;
    }

    public function validateTour(): array
    {
        $errors = [];
        
        // Check required fields
        if (empty($this->tour_points)) {
            $errors[] = 'Tour points are required';
        }
        
        if (empty($this->title)) {
            $errors[] = 'Title is required';
        }
        
        // Check tour points structure
        foreach ($this->tour_points ?? [] as $index => $point) {
            if (!isset($point['title'])) {
                $errors[] = "Point " . ($index + 1) . " requires a title";
            }
            
            if (!isset($point['coordinates'])) {
                $errors[] = "Point " . ($index + 1) . " requires coordinates";
            }
        }
        
        // Check capacity
        if ($this->max_participants <= 0) {
            $errors[] = 'Max participants must be greater than 0';
        }
        
        return $errors;
    }

    public function cloneTour(string $newTitle, User $creator): VirtualPropertyTour
    {
        $newTour = $this->replicate([
            'title',
            'description',
            'tour_points',
            'interactive_elements',
            'multimedia_content',
            'navigation_settings',
            'customization_options',
        ]);

        $newTour->update([
            'title' => $newTitle,
            'status' => 'draft',
            'creator_id' => $creator->id,
            'view_count' => 0,
            'participant_count' => 0,
            'session_count' => 0,
            'rating_average' => 0,
            'rating_count' => 0,
            'is_featured' => false,
            'created_by' => $creator->id,
        ]);

        return $newTour;
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'active',
            'published_at' => now(),
        ]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);
    }
}
