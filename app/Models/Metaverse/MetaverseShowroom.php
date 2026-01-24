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

class MetaverseShowroom extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'virtual_world_id',
        'showroom_type',
        'location_coordinates',
        'dimensions',
        'access_level',
        'capacity',
        'theme',
        'lighting_settings',
        'audio_settings',
        'interactive_elements',
        'status',
        'is_active',
        'owner_id',
        'current_visitors',
        'max_visitors',
        'features',
        'amenities',
        'multimedia_content',
        'navigation_settings',
        'ambient_settings',
        'customization_options',
        'view_count',
        'visit_count',
        'event_count',
        'rating_average',
        'rating_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'lighting_settings' => 'array',
        'audio_settings' => 'array',
        'interactive_elements' => 'array',
        'features' => 'array',
        'amenities' => 'array',
        'multimedia_content' => 'array',
        'navigation_settings' => 'array',
        'ambient_settings' => 'array',
        'customization_options' => 'array',
        'is_active' => 'boolean',
        'view_count' => 'integer',
        'visit_count' => 'integer',
        'event_count' => 'integer',
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
    public function virtualWorld(): BelongsTo
    {
        return $this->belongsTo(VirtualWorld::class, 'virtual_world_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(MetaverseProperty::class, 'metaverse_showroom_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(VirtualPropertyEvent::class, 'metaverse_showroom_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(MetaverseShowroomVisit::class, 'metaverse_showroom_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MetaverseShowroomReview::class, 'metaverse_showroom_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MetaverseShowroomImage::class, 'metaverse_showroom_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(MetaverseShowroomModel::class, 'metaverse_showroom_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function allowedUsers(): HasMany
    {
        return $this->hasMany(MetaverseShowroomAllowedUser::class, 'metaverse_showroom_id');
    }

    public function accessRequests(): HasMany
    {
        return $this->hasMany(MetaverseShowroomAccessRequest::class, 'metaverse_showroom_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('access_level', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('access_level', 'private');
    }

    public function scopeRestricted($query)
    {
        return $query->where('access_level', 'restricted');
    }

    public function scopePremium($query)
    {
        return $query->where('access_level', 'premium');
    }

    public function scopeByVirtualWorld($query, $worldId)
    {
        return $query->where('virtual_world_id', $worldId);
    }

    public function scopeByShowroomType($query, $showroomType)
    {
        return $query->where('showroom_type', $showroomType);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByCapacity($query, $minCapacity, $maxCapacity = null)
    {
        $query->where('capacity', '>=', $minCapacity);
        if ($maxCapacity) {
            $query->where('capacity', '<=', $maxCapacity);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('location_coordinates', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->dimensions) {
            return 'Not specified';
        }
        
        $dims = $this->dimensions;
        return "{$dims['length']}L x {$dims['width']}W x {$dims['height']}H";
    }

    public function getCapacityUsageAttribute(): float
    {
        return $this->max_visitors > 0 ? ($this->current_visitors / $this->max_visitors) * 100 : 0;
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_visitors >= $this->max_visitors;
    }

    public function getShowroomTypeTextAttribute(): string
    {
        return match($this->showroom_type) {
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'exhibition' => 'معرض',
            'event_space' => 'مساحة فعاليات',
            'gallery' => 'معرض فني',
            'showcase' => 'عرض',
            'demonstration' => 'عرض توضيحي',
            default => $this->showroom_type,
        };
    }

    public function getAccessLevelTextAttribute(): string
    {
        return match($this->access_level) {
            'public' => 'عام',
            'private' => 'خاص',
            'restricted' => 'مقيد',
            'premium' => 'مميز',
            default => $this->access_level,
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

    public function getIsPopularAttribute(): bool
    {
        return $this->visit_count > 100 || $this->rating_average >= 4.5;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsFeaturedAttribute(): bool
    {
        return $this->rating_average >= 4.5 && $this->rating_count >= 10;
    }

    // Methods
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function incrementVisit(): void
    {
        $this->increment('visit_count');
    }

    public function incrementEvent(): void
    {
        $this->increment('event_count');
    }

    public function canEnter(User $user): bool
    {
        // Owner can always enter
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Check access level
        return match($this->access_level) {
            'public' => true,
            'private' => false,
            'restricted' => $this->hasAccessPermission($user),
            'premium' => $user->hasPremiumAccess(),
            default => false,
        };
    }

    public function hasAccessPermission(User $user): bool
    {
        return $this->allowedUsers()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    public function addAllowedUser(User $user): void
    {
        $this->allowedUsers()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'granted_at' => now(),
        ]);
    }

    public function removeAllowedUser(User $user): void
    {
        $this->allowedUsers()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function grantAccess(User $user, string $reason = null): void
    {
        $this->allowedUsers()->create([
            'user_id' => $user->id,
            'status' => 'active',
            'reason' => $reason,
            'granted_at' => now(),
        ]);
    }

    public function revokeAccess(User $user): void
    {
        $this->allowedUsers()
            ->where('user_id', $user->id)
            ->update([
                'status' => 'revoked',
                'revoked_at' => now(),
            ]);
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

    public function getActiveEvents(): HasMany
    {
        return $this->events()->where('status', 'active');
    }

    public function getUpcomingEvents(): HasMany
    {
        return $this->events()
            ->where('start_time', '>', now())
            ->where('status', 'scheduled');
    }

    public function getFeaturedProperties(): HasMany
    {
        return $this->properties()
            ->where('status', 'active')
            ->where('is_featured', true);
    }

    public function generateVirtualTourUrl(): string
    {
        return route('metaverse.showrooms.enter', $this->id);
    }

    public function generateMarketplaceUrl(): string
    {
        return route('metaverse.marketplace.showroom', $this->id);
    }

    public function generateShareUrl(): string
    {
        return route('metaverse.showrooms.share', $this->id);
    }

    public function getThumbnailUrl(): string
    {
        $image = $this->images()->first();
        return $image ? asset('storage/' . $image->path) : asset('images/default-showroom.jpg');
    }

    public function getGalleryUrls(): array
    {
        return $this->images()->pluck('path')->map(function ($path) {
            return asset('storage/' . $path);
        })->toArray();
    }

    public function getRecentActivity(): array
    {
        return [
            'visits' => $this->visits()->latest()->limit(5)->get(),
            'reviews' => $this->reviews()->latest()->limit(5)->get(),
            'events' => $this->events()->latest()->limit(5)->get(),
        ];
    }

    public function getAnalytics(): array
    {
        return [
            'total_views' => $this->view_count,
            'total_visits' => $this->visit_count,
            'total_events' => $this->event_count,
            'average_rating' => $this->rating_average,
            'total_reviews' => $this->rating_count,
            'capacity_usage' => $this->getCapacityUsageAttribute(),
            'is_full' => $this->getIsFullAttribute(),
            'active_properties' => $this->properties()->where('status', 'active')->count(),
            'upcoming_events' => $this->getUpcomingEvents()->count(),
            'engagement_rate' => $this->calculateEngagementRate(),
        ];
    }

    public function calculateEngagementRate(): float
    {
        $totalInteractions = $this->view_count + $this->visit_count + $this->event_count;
        $daysSinceCreation = $this->created_at->diffInDays(now()) ?: 1;
        
        return $totalInteractions / $daysSinceCreation;
    }

    public function getOccupancyRate(): float
    {
        return $this->max_visitors > 0 ? ($this->current_visitors / $this->max_visitors) * 100 : 0;
    }

    public function isAvailableForEvent(): bool
    {
        return $this->is_active && 
               $this->status === 'active' && 
               !$this->getIsFullAttribute();
    }

    public function getAvailableTimeSlots(): array
    {
        // Generate available time slots for events
        $slots = [];
        $currentTime = now();
        
        for ($i = 0; $i < 24; $i++) {
            $slotTime = $currentTime->copy()->addHours($i);
            $slots[] = [
                'time' => $slotTime->format('H:i'),
                'available' => true, // This would check against existing events
            ];
        }
        
        return $slots;
    }

    public function hasPropertyType(string $propertyType): bool
    {
        return $this->properties()
            ->where('property_type', $propertyType)
            ->exists();
    }

    public function getPropertyTypes(): array
    {
        return $this->properties()
            ->pluck('property_type')
            ->unique()
            ->toArray();
    }

    public function getAveragePropertyValue(): float
    {
        return $this->properties()->avg('price') ?? 0;
    }

    public function getTotalPropertyValue(): float
    {
        return $this->properties()->sum('price') ?? 0;
    }

    public function getPopularFeatures(): array
    {
        $allFeatures = $this->properties()
            ->pluck('features')
            ->flatten()
            ->toArray();
        
        return array_count_values($allFeatures);
    }

    public function getVisitorDemographics(): array
    {
        return [
            'by_country' => $this->visits()
                ->join('users', 'metaverse_showroom_visits.user_id', '=', 'users.id')
                ->selectRaw('users.country, COUNT(*) as count')
                ->groupBy('users.country')
                ->orderBy('count', 'desc')
                ->limit(10)
                ->get(),
            
            'by_time' => $this->visits()
                ->selectRaw('HOUR(entered_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get(),
        ];
    }
}
