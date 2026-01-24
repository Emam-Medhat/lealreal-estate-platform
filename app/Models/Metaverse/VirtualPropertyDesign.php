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

class VirtualPropertyDesign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'virtual_world_id',
        'virtual_land_id',
        'design_type',
        'architectural_style',
        'building_specifications',
        'materials_used',
        'color_scheme',
        'lighting_design',
        'interior_design',
        'landscape_design',
        'amenities',
        'special_features',
        'blueprint_data',
        'model_data',
        'texture_data',
        'animation_data',
        'interaction_points',
        'navigation_paths',
        'performance_settings',
        'compatibility_settings',
        'estimated_build_time',
        'estimated_cost',
        'currency',
        'difficulty_level',
        'required_skills',
        'tools_needed',
        'status',
        'creator_id',
        'parent_design_id',
        'download_count',
        'usage_count',
        'rating_average',
        'rating_count',
        'is_featured',
        'is_public',
        'license_type',
        'usage_rights',
        'commercial_use_allowed',
        'modification_allowed',
        'attribution_required',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'building_specifications' => 'array',
        'materials_used' => 'array',
        'color_scheme' => 'array',
        'lighting_design' => 'array',
        'interior_design' => 'array',
        'landscape_design' => 'array',
        'amenities' => 'array',
        'special_features' => 'array',
        'blueprint_data' => 'array',
        'model_data' => 'array',
        'texture_data' => 'array',
        'animation_data' => 'array',
        'interaction_points' => 'array',
        'navigation_paths' => 'array',
        'performance_settings' => 'array',
        'compatibility_settings' => 'array',
        'required_skills' => 'array',
        'tools_needed' => 'array',
        'usage_rights' => 'array',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'commercial_use_allowed' => 'boolean',
        'modification_allowed' => 'boolean',
        'attribution_required' => 'boolean',
        'estimated_build_time' => 'integer',
        'estimated_cost' => 'decimal:2',
        'download_count' => 'integer',
        'usage_count' => 'integer',
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

    public function virtualLand(): BelongsTo
    {
        return $this->belongsTo(VirtualLand::class, 'virtual_land_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(VirtualPropertyDesign::class, 'parent_design_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(VirtualPropertyDesign::class, 'parent_design_id');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(MetaverseProperty::class, 'virtual_property_design_id');
    }

    public function blueprints(): HasMany
    {
        return $this->hasMany(DesignBlueprint::class, 'virtual_property_design_id');
    }

    public function models(): HasMany
    {
        return $this->hasMany(DesignModel::class, 'virtual_property_design_id');
    }

    public function textures(): HasMany
    {
        return $this->hasMany(DesignTexture::class, 'virtual_property_design_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(DesignReview::class, 'virtual_property_design_id');
    }

    public function downloads(): HasMany
    {
        return $this->hasMany(DesignDownload::class, 'virtual_property_design_id');
    }

    public function templates(): HasMany
    {
        return $this->hasMany(DesignTemplate::class, 'virtual_property_design_id');
    }

    public function collaborators(): HasMany
    {
        return $this->hasMany(DesignCollaborator::class, 'virtual_property_design_id');
    }

    public function tags(): MorphMany
    {
        return $this->morphMany(MetaverseTag::class, 'taggable');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DesignVersion::class, 'virtual_property_design_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByVirtualWorld($query, $worldId)
    {
        return $query->where('virtual_world_id', $worldId);
    }

    public function scopeByDesignType($query, $designType)
    {
        return $query->where('design_type', $designType);
    }

    public function scopeByArchitecturalStyle($query, $style)
    {
        return $query->where('architectural_style', $style);
    }

    public function scopeByCreator($query, $creatorId)
    {
        return $query->where('creator_id', $creatorId);
    }

    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    public function scopeByPriceRange($query, $minCost, $maxCost = null)
    {
        $query->where('estimated_cost', '>=', $minCost);
        if ($maxCost) {
            $query->where('estimated_cost', '<=', $maxCost);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('architectural_style', 'like', "%{$search}%");
        });
    }

    // Accessors
    public function getFormattedCostAttribute(): string
    {
        return number_format($this->estimated_cost, 2) . ' ' . $this->currency;
    }

    public function getDesignTypeTextAttribute(): string
    {
        return match($this->design_type) {
            'residential' => 'سكني',
            'commercial' => 'تجاري',
            'mixed' => 'مختلط',
            'industrial' => 'صناعي',
            'recreational' => 'ترفيهي',
            'educational' => 'تعليمي',
            'healthcare' => 'صحي',
            'office' => 'مكتبي',
            'retail' => 'تجزئة',
            'hospitality' => 'ضيافة',
            default => $this->design_type,
        };
    }

    public function getArchitecturalStyleTextAttribute(): string
    {
        return match($this->architectural_style) {
            'modern' => 'حديث',
            'classical' => 'كلاسيكي',
            'minimalist' => 'بسيط',
            'industrial' => 'صناعي',
            'scandinavian' => 'إسكندنافي',
            'mediterranean' => 'متوسطي',
            'victorian' => 'فيكتوري',
            'colonial' => 'استعماري',
            'contemporary' => 'معاصر',
            'traditional' => 'تقليدي',
            default => $this->architectural_style,
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
            'draft' => 'مسودة',
            'in_progress' => 'قيد العمل',
            'review' => 'تحت المراجعة',
            'published' => 'منشور',
            'deprecated' => 'مهمل',
            'archived' => 'مؤرشف',
            default => $this->status,
        };
    }

    public function getLicenseTypeTextAttribute(): string
    {
        return match($this->license_type) {
            'free' => 'مجاني',
            'commercial' => 'تجاري',
            'creative_commons' => 'المشاع الإبداعي',
            'proprietary' => 'خاص',
            'educational' => 'تعليمي',
            'trial' => 'تجريبي',
            default => $this->license_type,
        };
    }

    public function getFormattedBuildTimeAttribute(): string
    {
        $hours = $this->estimated_build_time;
        
        if ($hours < 24) {
            return $hours . ' ساعة';
        } elseif ($hours < 168) {
            return round($hours / 24, 1) . ' يوم';
        } else {
            return round($hours / 168, 1) . ' أسبوع';
        }
    }

    public function getIsPopularAttribute(): bool
    {
        return $this->download_count > 100 || $this->usage_count > 50;
    }

    public function getIsNewAttribute(): bool
    {
        return $this->created_at->diffInDays(now()) <= 7;
    }

    public function getIsPremiumAttribute(): bool
    {
        return $this->license_type === 'commercial' || $this->estimated_cost > 1000;
    }

    public function getComplexityScoreAttribute(): float
    {
        $score = 0;
        
        // Base score from difficulty
        $difficultyScores = [
            'beginner' => 10,
            'intermediate' => 30,
            'advanced' => 60,
            'expert' => 90,
        ];
        
        $score += $difficultyScores[$this->difficulty_level] ?? 0;
        
        // Add score for features
        if ($this->special_features) {
            $score += count($this->special_features) * 5;
        }
        
        // Add score for materials
        if ($this->materials_used) {
            $score += count($this->materials_used) * 2;
        }
        
        return min(100, $score);
    }

    // Methods
    public function incrementDownload(): void
    {
        $this->increment('download_count');
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
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

    public function addCollaborator(User $user, string $role = 'contributor'): void
    {
        $this->collaborators()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'role' => $role,
                'status' => 'active',
                'joined_at' => now(),
            ]
        );
    }

    public function removeCollaborator(User $user): void
    {
        $this->collaborators()
            ->where('user_id', $user->id)
            ->delete();
    }

    public function hasCollaborator(User $user): bool
    {
        return $this->collaborators()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    public function canBeDownloadedBy(User $user): bool
    {
        // Creator can always download
        if ($this->creator_id === $user->id) {
            return true;
        }

        // Check if public
        if ($this->is_public) {
            return true;
        }

        // Check if collaborator
        if ($this->hasCollaborator($user)) {
            return true;
        }

        // Check license type
        return $this->license_type === 'free';
    }

    public function canBeUsedCommercially(): bool
    {
        return $this->commercial_use_allowed;
    }

    public function canBeModified(): bool
    {
        return $this->modification_allowed;
    }

    public function requiresAttribution(): bool
    {
        return $this->attribution_required;
    }

    public function createVersion(string $description): DesignVersion
    {
        return $this->versions()->create([
            'version_number' => $this->getNextVersionNumber(),
            'description' => $description,
            'blueprint_data' => $this->blueprint_data,
            'model_data' => $this->model_data,
            'texture_data' => $this->texture_data,
            'created_by' => auth()->id(),
        ]);
    }

    public function clone(string $newTitle, User $creator): VirtualPropertyDesign
    {
        $newDesign = $this->replicate([
            'title',
            'description',
            'design_type',
            'architectural_style',
            'building_specifications',
            'materials_used',
            'color_scheme',
            'lighting_design',
            'interior_design',
            'landscape_design',
            'amenities',
            'special_features',
            'blueprint_data',
            'model_data',
            'texture_data',
            'animation_data',
            'interaction_points',
            'navigation_paths',
            'performance_settings',
            'compatibility_settings',
            'estimated_build_time',
            'estimated_cost',
            'difficulty_level',
            'required_skills',
            'tools_needed',
        ]);

        $newDesign->update([
            'title' => $newTitle,
            'status' => 'draft',
            'creator_id' => $creator->id,
            'parent_design_id' => $this->id,
            'download_count' => 0,
            'usage_count' => 0,
            'rating_average' => 0,
            'rating_count' => 0,
            'is_featured' => false,
            'created_by' => $creator->id,
        ]);

        return $newDesign;
    }

    public function publish(): void
    {
        $this->update([
            'status' => 'published',
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

    public function getBuildRequirements(): array
    {
        return [
            'skills' => $this->required_skills ?? [],
            'tools' => $this->tools_needed ?? [],
            'estimated_time' => $this->getFormattedBuildTimeAttribute(),
            'difficulty' => $this->getDifficultyLevelTextAttribute(),
            'complexity_score' => $this->getComplexityScoreAttribute(),
        ];
    }

    public function getPerformanceMetrics(): array
    {
        return [
            'polygons' => $this->model_data['polygon_count'] ?? 0,
            'textures' => count($this->texture_data ?? []),
            'animations' => count($this->animation_data ?? []),
            'interaction_points' => count($this->interaction_points ?? []),
            'file_size' => $this->calculateTotalFileSize(),
            'load_time' => $this->calculateEstimatedLoadTime(),
        ];
    }

    public function getCompatibilityInfo(): array
    {
        return [
            'supported_platforms' => $this->compatibility_settings['platforms'] ?? [],
            'minimum_requirements' => $this->compatibility_settings['minimum_requirements'] ?? [],
            'recommended_requirements' => $this->compatibility_settings['recommended_requirements'] ?? [],
            'file_formats' => $this->getSupportedFileFormats(),
        ];
    }

    public function getUsageStatistics(): array
    {
        return [
            'total_downloads' => $this->download_count,
            'total_usage' => $this->usage_count,
            'properties_built' => $this->properties()->count(),
            'average_rating' => $this->rating_average,
            'total_reviews' => $this->rating_count,
            'collaborators_count' => $this->collaborators()->count(),
            'versions_count' => $this->versions()->count(),
        ];
    }

    public function generateBuilderUrl(): string
    {
        return route('metaverse.builder.create', ['design_id' => $this->id]);
    }

    public function generateMarketplaceUrl(): string
    {
        return route('metaverse.marketplace.design', $this->id);
    }

    public function generateShareUrl(): string
    {
        return route('metaverse.designs.share', $this->id);
    }

    public function getThumbnailUrl(): string
    {
        $blueprint = $this->blueprints()->first();
        return $blueprint ? asset('storage/' . $blueprint->path) : asset('images/default-design.jpg');
    }

    public function getGalleryUrls(): array
    {
        return $this->blueprints()
            ->pluck('path')
            ->map(function ($path) {
                return asset('storage/' . $path);
            })
            ->toArray();
    }

    private function getNextVersionNumber(): string
    {
        $lastVersion = $this->versions()->orderBy('version_number', 'desc')->first();
        
        if (!$lastVersion) {
            return '1.0.0';
        }

        $parts = explode('.', $lastVersion->version_number);
        $parts[2] = (int) $parts[2] + 1;
        
        return implode('.', $parts);
    }

    private function calculateTotalFileSize(): int
    {
        $size = 0;
        
        foreach ($this->blueprints as $blueprint) {
            $size += $blueprint->file_size ?? 0;
        }
        
        foreach ($this->models as $model) {
            $size += $model->file_size ?? 0;
        }
        
        foreach ($this->textures as $texture) {
            $size += $texture->file_size ?? 0;
        }
        
        return $size;
    }

    private function calculateEstimatedLoadTime(): float
    {
        $fileSize = $this->calculateTotalFileSize();
        
        // Estimate load time based on file size (simplified)
        if ($fileSize < 1000000) { // < 1MB
            return 2.5; // seconds
        } elseif ($fileSize < 10000000) { // < 10MB
            return 5.0;
        } elseif ($fileSize < 50000000) { // < 50MB
            return 10.0;
        } else {
            return 20.0;
        }
    }

    private function getSupportedFileFormats(): array
    {
        $formats = [];
        
        foreach ($this->models as $model) {
            $formats[] = $model->file_type;
        }
        
        foreach ($this->textures as $texture) {
            $formats[] = $texture->file_type;
        }
        
        return array_unique($formats);
    }

    public function validateDesign(): array
    {
        $errors = [];
        
        // Check required fields
        if (empty($this->blueprint_data)) {
            $errors[] = 'Blueprint data is required';
        }
        
        if (empty($this->model_data)) {
            $errors[] = 'Model data is required';
        }
        
        // Check complexity
        if ($this->getComplexityScoreAttribute() > 80) {
            $errors[] = 'Design is too complex for current platform';
        }
        
        // Check file size
        if ($this->calculateTotalFileSize() > 100000000) { // 100MB
            $errors[] = 'Total file size exceeds limit';
        }
        
        return $errors;
    }

    public function getEstimatedValue(): float
    {
        $baseValue = $this->estimated_cost;
        
        // Add value based on popularity
        $popularityBonus = ($this->download_count * 0.1) + ($this->usage_count * 0.5);
        
        // Add value based on rating
        $ratingBonus = $this->rating_average * 10;
        
        // Add value based on complexity
        $complexityBonus = $this->getComplexityScoreAttribute() * 2;
        
        return $baseValue + $popularityBonus + $ratingBonus + $complexityBonus;
    }
}
