<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeveloperMetaverse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'developer_id',
        'project_id',
        'title',
        'description',
        'metaverse_type',
        'platform',
        'access_url',
        'status',
        'visibility',
        'version',
        'compatibility',
        'features',
        'assets',
        'environments',
        'interactions',
        'avatar_options',
        'navigation_options',
        'multiplayer_enabled',
        'max_concurrent_users',
        'access_requirements',
        'pricing_model',
        'subscription_required',
        'subscription_price',
        'trial_period_days',
        'technical_specs',
        'system_requirements',
        'supported_devices',
        'languages',
        'analytics_enabled',
        'privacy_settings',
        'moderation_level',
        'content_guidelines',
        'integration_options',
        'api_endpoints',
        'webhook_urls',
        'model_files',
        'texture_files',
        'preview_images',
        'thumbnail',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'compatibility' => 'array',
        'features' => 'array',
        'assets' => 'array',
        'environments' => 'array',
        'interactions' => 'array',
        'avatar_options' => 'array',
        'navigation_options' => 'array',
        'access_requirements' => 'array',
        'technical_specs' => 'array',
        'system_requirements' => 'array',
        'supported_devices' => 'array',
        'languages' => 'array',
        'privacy_settings' => 'array',
        'content_guidelines' => 'array',
        'integration_options' => 'array',
        'api_endpoints' => 'array',
        'webhook_urls' => 'array',
        'model_files' => 'array',
        'texture_files' => 'array',
        'preview_images' => 'array',
        'multiplayer_enabled' => 'boolean',
        'subscription_required' => 'boolean',
        'analytics_enabled' => 'boolean',
        'subscription_price' => 'decimal:8,2',
        'trial_period_days' => 'integer',
        'max_concurrent_users' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(DeveloperProject::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('metaverse_type', $type);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeMultiplayer($query)
    {
        return $query->where('multiplayer_enabled', true);
    }

    public function scopeSubscriptionRequired($query)
    {
        return $query->where('subscription_required', true);
    }

    // Helper methods
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isPublic(): bool
    {
        return $this->visibility === 'public';
    }

    public function isMultiplayerEnabled(): bool
    {
        return $this->multiplayer_enabled;
    }

    public function requiresSubscription(): bool
    {
        return $this->subscription_required;
    }

    public function hasAnalytics(): bool
    {
        return $this->analytics_enabled;
    }

    public function getThumbnailUrl(): string
    {
        return $this->thumbnail ? asset('storage/' . $this->thumbnail) : '';
    }

    public function getAccessUrl(): string
    {
        return $this->access_url ?: '';
    }

    public function getModelFilesCount(): int
    {
        return count($this->model_files ?? []);
    }

    public function getTextureFilesCount(): int
    {
        return count($this->texture_files ?? []);
    }

    public function getPreviewImagesCount(): int
    {
        return count($this->preview_images ?? []);
    }

    public function getFeaturesCount(): int
    {
        return count($this->features ?? []);
    }

    public function getEnvironmentsCount(): int
    {
        return count($this->environments ?? []);
    }

    public function getSupportedLanguagesCount(): int
    {
        return count($this->languages ?? []);
    }

    public function getSupportedDevicesCount(): int
    {
        return count($this->supported_devices ?? []);
    }

    public function canBeAccessed(): bool
    {
        return $this->isPublished() && $this->isPublic();
    }

    public function hasFreeTrial(): bool
    {
        return $this->trial_period_days > 0;
    }

    public function getTrialPeriodDays(): int
    {
        return $this->trial_period_days ?? 0;
    }

    public function getMaxConcurrentUsers(): int
    {
        return $this->max_concurrent_users ?? 0;
    }

    public function getSubscriptionPrice(): float
    {
        return $this->subscription_price ?? 0;
    }

    public function getPricingModel(): string
    {
        return $this->pricing_model ?? 'free';
    }

    public function getPlatform(): string
    {
        return $this->platform ?? '';
    }

    public function getMetaverseType(): string
    {
        return $this->metaverse_type ?? '';
    }

    public function getVersion(): string
    {
        return $this->version ?? '1.0.0';
    }

    public function getModerationLevel(): string
    {
        return $this->moderation_level ?? 'medium';
    }
}
