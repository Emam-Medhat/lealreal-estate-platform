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

class MetaverseProperty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'virtual_world_id',
        'property_type',
        'location_coordinates',
        'dimensions',
        'price',
        'currency',
        'is_for_sale',
        'is_for_rent',
        'rent_price',
        'rent_currency',
        'rent_period',
        'status',
        'visibility',
        'access_level',
        'owner_id',
        'virtual_property_design_id',
        'nft_id',
        'is_nft',
        'features',
        'amenities',
        'utilities',
        'zoning_info',
        'building_restrictions',
        'environmental_settings',
        'security_settings',
        'accessibility_features',
        'multimedia_settings',
        'interaction_settings',
        'customization_options',
        'view_count',
        'like_count',
        'share_count',
        'rating_average',
        'rating_count',
        'last_visit_date',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'features' => 'array',
        'amenities' => 'array',
        'utilities' => 'array',
        'zoning_info' => 'array',
        'building_restrictions' => 'array',
        'environmental_settings' => 'array',
        'security_settings' => 'array',
        'accessibility_features' => 'array',
        'multimedia_settings' => 'array',
        'interaction_settings' => 'array',
        'customization_options' => 'array',
        'is_for_sale' => 'boolean',
        'is_for_rent' => 'boolean',
        'is_nft' => 'boolean',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'share_count' => 'integer',
        'rating_average' => 'decimal:2',
        'rating_count' => 'integer',
        'last_visit_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_visit_date',
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

    public function design(): BelongsTo
    {
        return $this->belongsTo(VirtualPropertyDesign::class, 'virtual_property_design_id');
    }

    public function nft(): BelongsTo
    {
        return $this->belongsTo(MetaversePropertyNft::class, 'nft_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MetaversePropertyImage::class, 'metaverse_property_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(MetaversePropertyModel::class, 'metaverse_property_id');
    }

    public function textures(): HasMany
    {
        return $this->hasMany(MetaversePropertyTexture::class, 'metaverse_property_id');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(VirtualPropertyTour::class, 'metaverse_property_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(VirtualPropertyEvent::class, 'metaverse_property_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(MetaversePropertyVisit::class, 'metaverse_property_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MetaversePropertyReview::class, 'metaverse_property_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MetaverseTransaction::class, 'metaverse_property_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(MetaversePropertyLike::class, 'metaverse_property_id');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(MetaversePropertyShare::class, 'metaverse_property_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForSale($query)
    {
        return $query->where('is_for_sale', true);
    }

    public function scopeForRent($query)
    {
        return $query->where('is_for_rent', true);
    }

    public function scopeByVirtualWorld($query, $worldId)
    {
        return $query->where('virtual_world_id', $worldId);
    }

    public function scopeByPropertyType($query, $propertyType)
    {
        return $query->where('property_type', $propertyType);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
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
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getFormattedRentPriceAttribute(): string
    {
        if (!$this->rent_price) {
            return 'Not available';
        }
        return number_format($this->rent_price, 2) . ' ' . $this->rent_currency . '/' . $this->rent_period;
    }

    public function getDimensionsTextAttribute(): string
    {
        if (!$this->dimensions) {
            return 'Not specified';
        }
        
        $dims = $this->dimensions;
        return "{$dims['length']}L x {$dims['width']}W x {$dims['height']}H";
    }

    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'نشط',
            'inactive' => 'غير نشط',
            'building' => 'قيد البناء',
            'maintenance' => 'تحت الصيانة',
            'suspended' => 'موقوف',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    public function getPropertyTypeTextAttribute(): string
    {
        return match($this->property_type) {
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'mixed' => 'مختلط',
            'recreational' => 'ترفيهي',
            'educational' => 'تعليمي',
            'healthcare' => 'صحي',
            'office' => 'مكتبي',
            'retail' => 'تجزئة',
            'hospitality' => 'ضيافة',
            default => $this->property_type,
        };
    }

    public function getVisibilityTextAttribute(): string
    {
        return match($this->visibility) {
            'public' => 'عام',
            'private' => 'خاص',
            'restricted' => 'مقيد',
            'unlisted' => 'غير مدرج',
            default => $this->visibility,
        };
    }

    public function getAccessLevelTextAttribute(): string
    {
        return match($this->access_level) {
            'public' => 'عام',
            'private' => 'خاص',
            'restricted' => 'مقيد',
            'premium' => 'مميز',
            'invite_only' => 'دعوة فقط',
            default => $this->access_level,
        };
    }

    public function getIsPopularAttribute(): bool
    {
        return $this->view_count > 100 || $this->like_count > 50;
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
        $this->update(['last_visit_date' => now()]);
    }

    public function incrementLike(): void
    {
        $this->increment('like_count');
    }

    public function incrementShare(): void
    {
        $this->increment('share_count');
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

    public function canBeAccessedBy(User $user): bool
    {
        // Owner can always access
        if ($this->owner_id === $user->id) {
            return true;
        }

        // Check access level
        return match($this->access_level) {
            'public' => true,
            'private' => false,
            'restricted' => $this->hasAccessPermission($user),
            'premium' => $user->hasPremiumAccess(),
            'invite_only' => $this->isInvited($user),
            default => false,
        };
    }

    public function hasAccessPermission(User $user): bool
    {
        // Check if user has explicit permission
        return $this->accessPermissions()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    public function isInvited(User $user): bool
    {
        return $this->invitations()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->exists();
    }

    public function getMarketValue(): float
    {
        // Calculate market value based on similar properties
        $similarProperties = self::where('virtual_world_id', $this->virtual_world_id)
            ->where('property_type', $this->property_type)
            ->where('status', 'active')
            ->where('id', '!=', $this->id)
            ->get();

        if ($similarProperties->isEmpty()) {
            return $this->price;
        }

        return $similarProperties->avg('price');
    }

    public function getPricePerSquareMeter(): float
    {
        if (!$this->dimensions || !isset($this->dimensions['length']) || !isset($this->dimensions['width'])) {
            return 0;
        }

        $area = $this->dimensions['length'] * $this->dimensions['width'];
        return $area > 0 ? $this->price / $area : 0;
    }

    public function generateVirtualTourUrl(): string
    {
        return route('metaverse.tours.enter', $this->id);
    }

    public function generateMarketplaceUrl(): string
    {
        return route('metaverse.marketplace.property', $this->id);
    }

    public function generateShareUrl(): string
    {
        return route('metaverse.properties.share', $this->id);
    }

    public function getThumbnailUrl(): string
    {
        $image = $this->images()->first();
        return $image ? asset('storage/' . $image->path) : asset('images/default-property.jpg');
    }

    public function getGalleryUrls(): array
    {
        return $this->images()->pluck('path')->map(function ($path) {
            return asset('storage/' . $path);
        })->toArray();
    }

    public function hasActiveTours(): bool
    {
        return $this->tours()->where('is_active', true)->exists();
    }

    public function hasUpcomingEvents(): bool
    {
        return $this->events()
            ->where('start_time', '>', now())
            ->where('status', 'scheduled')
            ->exists();
    }

    public function getRecentActivity(): array
    {
        return [
            'visits' => $this->visits()->latest()->limit(5)->get(),
            'reviews' => $this->reviews()->latest()->limit(5)->get(),
            'transactions' => $this->transactions()->latest()->limit(5)->get(),
        ];
    }

    public function getAnalytics(): array
    {
        return [
            'total_views' => $this->view_count,
            'total_likes' => $this->like_count,
            'total_shares' => $this->share_count,
            'average_rating' => $this->rating_average,
            'total_reviews' => $this->rating_count,
            'market_value' => $this->getMarketValue(),
            'price_per_sqm' => $this->getPricePerSquareMeter(),
            'days_on_market' => $this->created_at->diffInDays(now()),
            'engagement_rate' => $this->calculateEngagementRate(),
        ];
    }

    private function calculateEngagementRate(): float
    {
        $totalInteractions = $this->view_count + $this->like_count + $this->share_count;
        $daysSinceCreation = $this->created_at->diffInDays(now()) ?: 1;
        
        return $totalInteractions / $daysSinceCreation;
    }
}
