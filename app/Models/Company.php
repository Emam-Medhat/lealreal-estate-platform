<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'type',
        'registration_number',
        'tax_id',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'logo_url',
        'cover_image_url',
        'description',
        'founded_date',
        'employee_count',
        'annual_revenue',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'is_featured',
        'is_verified',
        'verification_level',
        'rating',
        'total_reviews',
        'subscription_plan',
        'subscription_expires_at',
        'api_key',
        'webhook_url',
        'webhook_url',
        // 'settings', // Moved to separate table/relationship
        'metadata',
    ];

    protected $casts = [
        'founded_date' => 'date',
        'annual_revenue' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'verification_level' => 'integer',
        'rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'subscription_expires_at' => 'datetime',
        'settings' => 'json',
        'metadata' => 'json',
    ];

    protected $attributes = [
        'status' => 'pending',
        'is_featured' => false,
        'is_verified' => false,
        'verification_level' => 0,
        'rating' => 0,
        'total_reviews' => 0,
        'subscription_plan' => 'basic',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function branches(): HasMany
    {
        return $this->hasMany(CompanyBranch::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(CompanyMember::class);
    }

    public function teams(): HasMany
    {
        return $this->hasMany(CompanyTeam::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function agents(): HasMany
    {
        return $this->hasMany(Agent::class);
    }

    public function developers(): HasMany
    {
        return $this->hasMany(Developer::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(CompanyAnalytic::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(CompanySubscription::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(CompanyPortfolio::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CompanyInvoice::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CompanyTransaction::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_members')
            ->withPivot(['role', 'status', 'joined_at', 'left_at'])
            ->withTimestamps();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'agency' => __('Real Estate Agency'),
            'development' => __('Property Developer'),
            'property_management' => __('Property Management'),
            'investment' => __('Investment Firm'),
            'construction' => __('Construction Company'),
            'architecture' => __('Architecture Firm'),
            'legal' => __('Legal Services'),
            'mortgage' => __('Mortgage Company'),
            'insurance' => __('Insurance Company'),
            'inspection' => __('Inspection Service'),
            'other' => __('Other'),
            default => __(ucfirst($this->type))
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => __('Pending Approval'),
            'active' => __('Active'),
            'inactive' => __('Inactive'),
            'suspended' => __('Suspended'),
            'rejected' => __('Rejected'),
            'closed' => __('Closed'),
            default => __(ucfirst($this->status))
        };
    }

    public function getSubscriptionPlanLabelAttribute(): string
    {
        return match ($this->subscription_plan) {
            'basic' => __('Basic'),
            'professional' => __('Professional'),
            'enterprise' => __('Enterprise'),
            'custom' => __('Custom'),
            default => __(ucfirst($this->subscription_plan))
        };
    }

    public function getVerificationLevelLabelAttribute(): string
    {
        return match ($this->verification_level) {
            0 => __('Not Verified'),
            1 => __('Basic'),
            2 => __('Standard'),
            3 => __('Premium'),
            4 => __('Platinum'),
            default => __('Unknown')
        };
    }

    public function getFormattedRatingAttribute(): string
    {
        return number_format((float) $this->rating, 1);
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }

    public function getActiveMembersCountAttribute(): int
    {
        return $this->members()->where('status', 'active')->count();
    }

    public function getActivePropertiesCountAttribute(): int
    {
        return $this->properties()->where('status', 'published')->count();
    }

    public function getTotalPropertiesCountAttribute(): int
    {
        return $this->properties()->count();
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription_expires_at && $this->subscription_expires_at > now();
    }

    public function canAddMoreProperties(int $limit = null): bool
    {
        if (!$limit) {
            $limit = $this->getPropertyLimitForPlan();
        }

        return $this->properties()->count() < $limit;
    }

    public function canAddMoreMembers(int $limit = null): bool
    {
        if (!$limit) {
            $limit = $this->getMemberLimitForPlan();
        }

        return $this->members()->where('status', 'active')->count() < $limit;
    }

    private function getPropertyLimitForPlan(): int
    {
        return match ($this->subscription_plan) {
            'basic' => 10,
            'professional' => 100,
            'enterprise' => 1000,
            'custom' => 10000,
            default => 10
        };
    }

    private function getMemberLimitForPlan(): int
    {
        return match ($this->subscription_plan) {
            'basic' => 5,
            'professional' => 50,
            'enterprise' => 500,
            'custom' => 5000,
            default => 5
        };
    }

    public function approve(User $approver = null): bool
    {
        return $this->update([
            'status' => 'active',
            'approved_by' => $approver?->id ?? auth()->id(),
            'approved_at' => now(),
        ]);
    }

    public function reject(string $reason, User $rejecter = null): bool
    {
        return $this->update([
            'status' => 'rejected',
            'rejected_by' => $rejecter?->id ?? auth()->id(),
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function suspend(string $reason = null): bool
    {
        return $this->update([
            'status' => 'suspended',
            'suspension_reason' => $reason,
            'suspended_at' => now(),
        ]);
    }

    public function activate(): bool
    {
        return $this->update([
            'status' => 'active',
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPlan($query, string $plan)
    {
        return $query->where('subscription_plan', $plan);
    }

    public function scopeInLocation($query, string $city, string $state = null)
    {
        $query->where('city', $city);

        if ($state) {
            $query->where('state', $state);
        }

        return $query;
    }

    public function scopeWithRating($query, float $minRating = 0)
    {
        return $query->where('rating', '>=', $minRating);
    }
}
