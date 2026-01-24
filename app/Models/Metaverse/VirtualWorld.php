<?php

namespace App\Models\Metaverse;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class VirtualWorld extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'world_type',
        'theme',
        'access_level',
        'max_avatars',
        'world_settings',
        'environment_settings',
        'physics_settings',
        'graphics_settings',
        'audio_settings',
        'rules_guidelines',
        'monetization_settings',
        'moderation_settings',
        'status',
        'is_active',
        'launch_date',
        'creator_id',
        'dimensions',
        'world_map_path',
        'landmarks',
        'zones',
        'current_avatar_count',
        'max_building_height',
        'building_restrictions',
        'weather_system',
        'day_night_cycle',
        'seasonal_changes',
        'ambient_sounds',
        'customization_options',
        'api_settings',
        'integration_settings',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'world_settings' => 'array',
        'environment_settings' => 'array',
        'physics_settings' => 'array',
        'graphics_settings' => 'array',
        'audio_settings' => 'array',
        'rules_guidelines' => 'array',
        'monetization_settings' => 'array',
        'moderation_settings' => 'array',
        'dimensions' => 'array',
        'landmarks' => 'array',
        'zones' => 'array',
        'building_restrictions' => 'array',
        'weather_system' => 'boolean',
        'day_night_cycle' => 'boolean',
        'seasonal_changes' => 'boolean',
        'ambient_sounds' => 'boolean',
        'customization_options' => 'array',
        'api_settings' => 'array',
        'integration_settings' => 'array',
        'is_active' => 'boolean',
        'current_avatar_count' => 'integer',
        'max_building_height' => 'integer',
        'launch_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $dates = [
        'launch_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(MetaverseProperty::class, 'virtual_world_id');
    }

    public function lands(): HasMany
    {
        return $this->hasMany(VirtualLand::class, 'virtual_world_id');
    }

    public function showrooms(): HasMany
    {
        return $this->hasMany(MetaverseShowroom::class, 'virtual_world_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(VirtualPropertyEvent::class, 'virtual_world_id');
    }

    public function avatars(): HasMany
    {
        return $this->hasMany(MetaverseAvatar::class, 'virtual_world_id');
    }

    public function appearances(): HasMany
    {
        return $this->hasMany(WorldAppearance::class, 'virtual_world_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(MetaverseTransaction::class, 'virtual_world_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function allowedUsers(): HasMany
    {
        return $this->hasMany(VirtualWorldAllowedUser::class, 'virtual_world_id');
    }

    public function moderators(): HasMany
    {
        return $this->hasMany(VirtualWorldModerator::class, 'virtual_world_id');
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

    public function scopeByWorldType($query, $worldType)
    {
        return $query->where('world_type', $worldType);
    }

    public function scopeByTheme($query, $theme)
    {
        return $query->where('theme', $theme);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('theme', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getWorldTypeTextAttribute(): string
    {
        return match($this->world_type) {
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'gaming' => 'ألعاب',
            'educational' => 'تعليمي',
            'entertainment' => 'ترفيهي',
            'social' => 'اجتماعي',
            'industrial' => 'صناعي',
            'healthcare' => 'صحي',
            'retail' => 'تجزئة',
            default => $this->world_type,
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
            'development' => 'قيد التطوير',
            'testing' => 'تحت الاختبار',
            'active' => 'نشط',
            'suspended' => 'موقوف',
            'archived' => 'مؤرشف',
            'deleted' => 'محذوف',
            default => $this->status,
        };
    }

    public function getCapacityUsageAttribute(): float
    {
        return $this->max_avatars > 0 ? ($this->current_avatar_count / $this->max_avatars) * 100 : 0;
    }

    public function getIsFullAttribute(): bool
    {
        return $this->current_avatar_count >= $this->max_avatars;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsPopularAttribute(): bool
    {
        return $this->current_avatar_count > ($this->max_avatars * 0.7);
    }

    public function getFormattedDimensionsAttribute(): string
    {
        if (!$this->dimensions) {
            return 'Not specified';
        }
        
        $dims = $this->dimensions;
        return "{$dims['width']}W x {$dims['height']}H x {$dims['length']}L";
    }

    public function getWorldMapUrlAttribute(): string
    {
        return $this->world_map_path ? asset('storage/' . $this->world_map_path) : asset('images/default-world-map.jpg');
    }

    // Methods
    public function incrementAvatarCount(): void
    {
        $this->increment('current_avatar_count');
    }

    public function decrementAvatarCount(): void
    {
        $this->decrement('current_avatar_count');
    }

    public function canEnter(User $user): bool
    {
        // Creator can always enter
        if ($this->creator_id === $user->id) {
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

    public function addModerator(User $user, array $permissions = []): void
    {
        $this->moderators()->create([
            'user_id' => $user->id,
            'permissions' => $permissions,
            'status' => 'active',
            'appointed_at' => now(),
        ]);
    }

    public function removeModerator(User $user): void
    {
        $this->moderators()
            ->where('user_id', $user->id)
            ->update([
                'status' => 'removed',
                'removed_at' => now(),
            ]);
    }

    public function getTotalPropertyValue(): float
    {
        return $this->properties()->sum('price') ?? 0;
    }

    public function getTotalLandValue(): float
    {
        return $this->lands()->sum('price') ?? 0;
    }

    public function getTotalAssets(): float
    {
        return $this->getTotalPropertyValue() + $this->getTotalLandValue();
    }

    public function getActivePropertiesCount(): int
    {
        return $this->properties()->where('status', 'active')->count();
    }

    public function getActiveLandsCount(): int
    {
        return $this->lands()->where('status', 'active')->count();
    }

    public function getActiveShowroomsCount(): int
    {
        return $this->showrooms()->where('status', 'active')->where('is_active', true)->count();
    }

    public function getActiveEventsCount(): int
    {
        return $this->events()->where('status', 'active')->count();
    }

    public function getOnlineAvatarsCount(): int
    {
        return $this->avatars()->where('is_online', true)->count();
    }

    public function getRecentActivity(): array
    {
        return [
            'appearances' => $this->appearances()->latest()->limit(5)->get(),
            'transactions' => $this->transactions()->latest()->limit(5)->get(),
            'events' => $this->events()->latest()->limit(5)->get(),
        ];
    }

    public function getAnalytics(): array
    {
        return [
            'total_properties' => $this->properties()->count(),
            'total_lands' => $this->lands()->count(),
            'total_showrooms' => $this->showrooms()->count(),
            'total_events' => $this->events()->count(),
            'active_properties' => $this->getActivePropertiesCount(),
            'active_lands' => $this->getActiveLandsCount(),
            'active_showrooms' => $this->getActiveShowroomsCount(),
            'active_events' => $this->getActiveEventsCount(),
            'online_avatars' => $this->getOnlineAvatarsCount(),
            'capacity_usage' => $this->getCapacityUsageAttribute(),
            'total_assets_value' => $this->getTotalAssets(),
            'engagement_metrics' => $this->getEngagementMetrics(),
            'popular_zones' => $this->getPopularZones(),
        ];
    }

    public function getEngagementMetrics(): array
    {
        return [
            'daily_active_users' => $this->appearances()
                ->whereDate('entered_at', today())
                ->distinct('user_id')
                ->count(),
            
            'weekly_active_users' => $this->appearances()
                ->whereBetween('entered_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->distinct('user_id')
                ->count(),
            
            'monthly_active_users' => $this->appearances()
                ->whereMonth('entered_at', now()->month)
                ->whereYear('entered_at', now()->year)
                ->distinct('user_id')
                ->count(),
            
            'average_session_duration' => $this->appearances()
                ->whereNotNull('exited_at')
                ->avg(\DB::raw('TIMESTAMPDIFF(SECOND, entered_at, exited_at)')) ?? 0,
            
            'peak_concurrent_users' => $this->getPeakConcurrentUsers(),
        ];
    }

    public function getPopularZones(): array
    {
        if (!$this->zones) {
            return [];
        }

        $zoneActivity = [];
        foreach ($this->zones as $zone) {
            $zoneActivity[$zone['name']] = [
                'name' => $zone['name'],
                'visitor_count' => $this->getZoneVisitorCount($zone['name']),
                'property_count' => $this->getZonePropertyCount($zone['name']),
                'event_count' => $this->getZoneEventCount($zone['name']),
            ];
        }

        return $zoneActivity;
    }

    private function getPeakConcurrentUsers(): int
    {
        // This would require tracking concurrent users over time
        // For now, return current avatar count
        return $this->current_avatar_count;
    }

    private function getZoneVisitorCount(string $zoneName): int
    {
        return $this->appearances()
            ->where('current_zone', $zoneName)
            ->count();
    }

    private function getZonePropertyCount(string $zoneName): int
    {
        return $this->properties()
            ->where('current_zone', $zoneName)
            ->count();
    }

    private function getZoneEventCount(string $zoneName): int
    {
        return $this->events()
            ->where('location_zone', $zoneName)
            ->count();
    }

    public function launch(): void
    {
        $this->update([
            'status' => 'active',
            'launched_at' => now(),
            'is_active' => true,
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'is_active' => false,
        ]);

        // Kick all online avatars
        $this->avatars()->where('is_online', true)->update([
            'is_online' => false,
            'current_world_id' => null,
            'current_location' => null,
        ]);

        $this->update(['current_avatar_count' => 0]);
    }

    public function archive(): void
    {
        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
            'is_active' => false,
        ]);
    }

    public function generateMapData(): array
    {
        return [
            'world_info' => [
                'name' => $this->name,
                'dimensions' => $this->dimensions ?? ['width' => 1000, 'height' => 1000],
                'theme' => $this->theme,
            ],
            'properties' => $this->properties()
                ->where('status', 'active')
                ->select(['id', 'title', 'location_coordinates', 'property_type', 'price'])
                ->get(),
            'lands' => $this->lands()
                ->where('status', 'active')
                ->select(['id', 'title', 'coordinates', 'land_type', 'price'])
                ->get(),
            'showrooms' => $this->showrooms()
                ->where('status', 'active')
                ->where('is_active', true)
                ->select(['id', 'title', 'location_coordinates', 'showroom_type'])
                ->get(),
            'landmarks' => $this->landmarks ?? [],
            'zones' => $this->zones ?? [],
        ];
    }

    public function getWorldStatistics(): array
    {
        return [
            'total_users' => $this->appearances()->distinct('user_id')->count('user_id'),
            'total_properties' => $this->properties()->count(),
            'total_lands' => $this->lands()->count(),
            'total_showrooms' => $this->showrooms()->count(),
            'total_events' => $this->events()->count(),
            'total_transactions' => $this->transactions()->count(),
            'total_volume' => $this->transactions()->sum('amount'),
            'average_session_duration' => $this->getAverageSessionDuration(),
            'most_active_zones' => $this->getMostActiveZones(),
            'popular_property_types' => $this->getPopularPropertyTypes(),
        ];
    }

    private function getAverageSessionDuration(): float
    {
        return $this->appearances()
            ->whereNotNull('exited_at')
            ->avg(\DB::raw('TIMESTAMPDIFF(SECOND, entered_at, exited_at)')) ?? 0;
    }

    private function getMostActiveZones(): array
    {
        return $this->appearances()
            ->selectRaw('current_zone, COUNT(*) as count')
            ->groupBy('current_zone')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }

    private function getPopularPropertyTypes(): array
    {
        return $this->properties()
            ->selectRaw('property_type, COUNT(*) as count')
            ->groupBy('property_type')
            ->orderBy('count', 'desc')
            ->limit(5)
            ->get()
            ->toArray();
    }
}
