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

class VirtualLand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'virtual_world_id',
        'land_type',
        'coordinates',
        'area',
        'area_unit',
        'dimensions',
        'price',
        'currency',
        'ownership_status',
        'owner_id',
        'zoning_types',
        'max_building_height',
        'min_lot_size',
        'setback_requirements',
        'parking_requirements',
        'development_status',
        'development_type',
        'development_plan',
        'estimated_development_cost',
        'estimated_development_timeline',
        'zoning_compliance',
        'environmental_impact_assessment',
        'infrastructure_requirements',
        'last_purchase_date',
        'purchase_price',
        'purchase_currency',
        'last_transfer_date',
        'max_properties',
        'is_prime_location',
        'is_waterfront',
        'terrain_type',
        'soil_quality',
        'elevation',
        'distance_from_coast',
        'water_body_proximity',
        'flood_zone',
        'utilities_available',
        'access_roads',
        'public_transport_access',
        'nearby_amenities',
        'market_value',
        'assessment_value',
        'tax_assessment',
        'view_count',
        'inquiry_count',
        'offer_count',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'dimensions' => 'array',
        'zoning_types' => 'array',
        'development_plan' => 'array',
        'environmental_impact_assessment' => 'array',
        'infrastructure_requirements' => 'array',
        'utilities_available' => 'array',
        'access_roads' => 'array',
        'nearby_amenities' => 'array',
        'is_prime_location' => 'boolean',
        'is_waterfront' => 'boolean',
        'last_purchase_date' => 'datetime',
        'last_transfer_date' => 'datetime',
        'estimated_development_cost' => 'decimal:2',
        'market_value' => 'decimal:2',
        'assessment_value' => 'decimal:2',
        'tax_assessment' => 'decimal:2',
        'view_count' => 'integer',
        'inquiry_count' => 'integer',
        'offer_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'last_purchase_date',
        'last_transfer_date',
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
        return $this->hasMany(MetaverseProperty::class, 'virtual_land_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MetaverseTransaction::class, 'virtual_land_id');
    }

    public function visits(): HasMany
    {
        return $this->hasMany(VirtualLandVisit::class, 'virtual_land_id');
    }

    public function inquiries(): HasMany
    {
        return $this->hasMany(VirtualLandInquiry::class, 'virtual_land_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(VirtualLandOffer::class, 'virtual_land_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(VirtualLandAssessment::class, 'virtual_land_id');
    }

    public function developmentPlans(): HasMany
    {
        return $this->hasMany(VirtualLandDevelopmentPlan::class, 'virtual_land_id');
    }

    public function neighbors(): HasMany
    {
        return $this->hasMany(VirtualLandNeighbor::class, 'virtual_land_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(VirtualLandImage::class, 'virtual_land_id');
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
        return $query->where('ownership_status', 'for_sale');
    }

    public function scopeOwned($query)
    {
        return $query->where('ownership_status', 'owned');
    }

    public function scopeByVirtualWorld($query, $worldId)
    {
        return $query->where('virtual_world_id', $worldId);
    }

    public function scopeByLandType($query, $landType)
    {
        return $query->where('land_type', $landType);
    }

    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeByAreaRange($query, $minArea, $maxArea)
    {
        return $query->whereBetween('area', [$minArea, $maxArea]);
    }

    public function scopePrimeLocation($query)
    {
        return $query->where('is_prime_location', true);
    }

    public function scopeWaterfront($query)
    {
        return $query->where('is_waterfront', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('coordinates', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    public function getFormattedAreaAttribute(): string
    {
        return number_format($this->area, 2) . ' ' . $this->area_unit;
    }

    public function getPricePerSquareMeterAttribute(): float
    {
        if ($this->area_unit === 'sqm' && $this->area > 0) {
            return $this->price / $this->area;
        }
        
        // Convert to square meters if needed
        $areaInSqM = $this->convertToSquareMeters($this->area, $this->area_unit);
        return $areaInSqM > 0 ? $this->price / $areaInSqM : 0;
    }

    public function getOwnershipStatusTextAttribute(): string
    {
        return match($this->ownership_status) {
            'owned' => 'ملك',
            'for_sale' => 'للبيع',
            'under_contract' => 'تحت العقد',
            'pending_transfer' => 'في انتظار النقل',
            'leased' => 'مؤجر',
            'restricted' => 'مقيد',
            default => $this->ownership_status,
        };
    }

    public function getLandTypeTextAttribute(): string
    {
        return match($this->land_type) {
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'industrial' => 'صناعي',
            'mixed' => 'مختلط',
            'agricultural' => 'زراعي',
            'recreational' => 'ترفيهي',
            'institutional' => 'مؤسسي',
            'vacant' => 'شاغر',
            default => $this->land_type,
        };
    }

    public function getDevelopmentStatusTextAttribute(): string
    {
        return match($this->development_status) {
            'undeveloped' => 'غير مطور',
            'planned' => 'مخطط',
            'developing' => 'قيد التطوير',
            'developed' => 'مطور',
            'under_review' => 'تحت المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
            default => $this->development_status,
        };
    }

    public function getTerrainTypeTextAttribute(): string
    {
        return match($this->terrain_type) {
            'flat' => 'مسطح',
            'hilly' => 'تلال',
            'mountainous' => 'جبلي',
            'valley' => 'وادي',
            'coastal' => 'ساحلي',
            'riverfront' => 'على النهر',
            'lakefront' => 'على البحيرة',
            default => $this->terrain_type,
        };
    }

    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->dimensions) {
            return 'Not specified';
        }
        
        $dims = $this->dimensions;
        return "{$dims['length']}L x {$dims['width']}W";
    }

    public function getIsPremiumAttribute(): bool
    {
        return $this->is_prime_location || $this->is_waterfront;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsHotAttribute(): bool
    {
        return $this->inquiry_count > 10 || $this->offer_count > 5;
    }

    // Methods
    public function incrementView(): void
    {
        $this->increment('view_count');
    }

    public function incrementInquiry(): void
    {
        $this->increment('inquiry_count');
    }

    public function incrementOffer(): void
    {
        $this->increment('offer_count');
    }

    public function calculateMarketValue(): float
    {
        // Calculate based on similar lands
        $similarLands = self::where('virtual_world_id', $this->virtual_world_id)
            ->where('land_type', $this->land_type)
            ->where('status', 'active')
            ->where('id', '!=', $this->id)
            ->get();

        if ($similarLands->isEmpty()) {
            return $this->price;
        }

        return $similarLands->avg('price');
    }

    public function calculateDevelopmentPotential(): array
    {
        return [
            'max_buildings' => floor($this->area / 100), // Assume 100 units per building
            'max_floors' => $this->max_building_height ?? 10,
            'building_types_allowed' => $this->zoning_types ?? ['residential', 'commercial'],
            'infrastructure_score' => $this->calculateInfrastructureScore(),
            'environmental_score' => $this->calculateEnvironmentalScore(),
        ];
    }

    public function getZoningRestrictions(): array
    {
        return [
            'residential_allowed' => in_array('residential', $this->zoning_types ?? []),
            'commercial_allowed' => in_array('commercial', $this->zoning_types ?? []),
            'industrial_allowed' => in_array('industrial', $this->zoning_types ?? []),
            'max_building_height' => $this->max_building_height,
            'min_lot_size' => $this->min_lot_size,
            'setback_requirements' => $this->setback_requirements,
            'parking_requirements' => $this->parking_requirements,
        ];
    }

    public function canBeDeveloped(): bool
    {
        return $this->ownership_status === 'owned' && 
               $this->development_status !== 'developed' &&
               $this->zoning_compliance;
    }

    public function hasUtilities(): bool
    {
        return !empty($this->utilities_available);
    }

    public function getUtilityTypes(): array
    {
        return array_keys($this->utilities_available ?? []);
    }

    public function getNearbyAmenityTypes(): array
    {
        return array_keys($this->nearby_amenities ?? []);
    }

    public function calculateDistanceTo($coordinates): float
    {
        // Calculate distance between two coordinates
        // This is a simplified calculation
        list($lat1, $lon1) = explode(',', $this->coordinates);
        list($lat2, $lon2) = explode(',', $coordinates);
        
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }

    public function generateVirtualTourUrl(): string
    {
        return route('metaverse.tours.create', ['land_id' => $this->id]);
    }

    public function generateMarketplaceUrl(): string
    {
        return route('metaverse.marketplace.land', $this->id);
    }

    public function generateShareUrl(): string
    {
        return route('metaverse.lands.share', $this->id);
    }

    public function getThumbnailUrl(): string
    {
        $image = $this->images()->first();
        return $image ? asset('storage/' . $image->path) : asset('images/default-land.jpg');
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
            'inquiries' => $this->inquiries()->latest()->limit(5)->get(),
            'offers' => $this->offers()->latest()->limit(5)->get(),
            'transactions' => $this->transactions()->latest()->limit(5)->get(),
        ];
    }

    public function getAnalytics(): array
    {
        return [
            'total_views' => $this->view_count,
            'total_inquiries' => $this->inquiry_count,
            'total_offers' => $this->offer_count,
            'market_value' => $this->calculateMarketValue(),
            'price_per_sqm' => $this->getPricePerSquareMeterAttribute(),
            'days_on_market' => $this->ownership_status === 'for_sale' ? $this->updated_at->diffInDays(now()) : 0,
            'development_potential' => $this->calculateDevelopmentPotential(),
            'zoning_restrictions' => $this->getZoningRestrictions(),
        ];
    }

    private function convertToSquareMeters(float $area, string $unit): float
    {
        return match($unit) {
            'sqm' => $area,
            'sqft' => $area * 0.092903,
            'acre' => $area * 4046.86,
            'hectare' => $area * 10000,
            default => $area,
        };
    }

    private function calculateInfrastructureScore(): float
    {
        $score = 0;
        $utilities = $this->utilities_available ?? [];
        
        if (isset($utilities['electricity'])) $score += 20;
        if (isset($utilities['water'])) $score += 20;
        if (isset($utilities['sewage'])) $score += 15;
        if (isset($utilities['gas'])) $score += 15;
        if (isset($utilities['internet'])) $score += 15;
        if (isset($utilities['telecom'])) $score += 15;
        
        return min(100, $score);
    }

    private function calculateEnvironmentalScore(): float
    {
        $score = 100;
        $impact = $this->environmental_impact_assessment ?? [];
        
        if (isset($impact['pollution_level'])) $score -= $impact['pollution_level'] * 10;
        if (isset($impact['noise_level'])) $score -= $impact['noise_level'] * 5;
        if (isset($impact['green_space_ratio'])) $score += $impact['green_space_ratio'] * 20;
        
        return max(0, $score);
    }
}
