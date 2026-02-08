<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'agency_id',
        'company_id',
        'license_number',
        'specialization',
        'experience_years',
        'rating',
        'total_sales',
        'total_properties',
        'status',
        'is_verified',
        'is_active',
        'commission_rate',
        'territory_id',
        'hire_date',
        'join_date',
        'verified_at',
        'suspended_at',
        'created_by',
    ];

    protected $casts = [
        'rating' => 'float',
        'total_sales' => 'float',
        'total_properties' => 'integer',
        'experience_years' => 'integer',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'commission_rate' => 'float',
        'hire_date' => 'datetime',
        'join_date' => 'date',
        'verified_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(AgentProfile::class);
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(AgentTerritory::class);
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(AgentLead::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(AgentAppointment::class);
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(AgentCommission::class);
    }

    public function performances(): HasMany
    {
        return $this->hasMany(AgentPerformance::class);
    }

    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(AgentCertification::class, 'agent_certification_pivot')
                    ->withPivot('issued_date', 'expiry_date', 'certificate_number')
                    ->withTimestamps();
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(AgentLicense::class);
    }

    public function specializations(): BelongsToMany
    {
        return $this->belongsToMany(AgentSpecialization::class, 'agent_specialization_pivot')
                    ->withTimestamps();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(AgentReview::class);
    }

    public function clients(): HasMany
    {
        return $this->hasMany(AgentClient::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(AgentTask::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeByAgency($query, $agencyId)
    {
        return $query->where('agency_id', $agencyId);
    }

    public function scopeByTerritory($query, $territoryId)
    {
        return $query->where('territory_id', $territoryId);
    }

    public function scopeByRating($query, $minRating)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeByExperience($query, $minYears)
    {
        return $query->where('experience_years', '>=', $minYears);
    }

    // Helper Methods
    public function getFormattedRatingAttribute(): string
    {
        return number_format($this->rating, 1);
    }

    public function getFormattedTotalSalesAttribute(): string
    {
        return number_format($this->total_sales, 2) . ' SAR';
    }

    public function getFullNameAttribute(): string
    {
        return $this->user ? $this->user->full_name : $this->name;
    }

    public function getProfileImageAttribute(): string
    {
        return $this->user ? ($this->user->avatar ?? $this->avatar) : 'default-avatar.png';
    }

    public function getEmailAttribute(): ?string
    {
        return $this->user ? $this->user->email : null;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->user ? $this->user->phone : null;
    }

    public function getAvatarAttribute(): ?string
    {
        return $this->user ? $this->user->avatar : null;
    }

    public function getActivePropertiesCount(): int
    {
        return $this->properties()->where('status', 'active')->count();
    }

    public function getPendingLeadsCount(): int
    {
        return $this->leads()->where('status', 'pending')->count();
    }

    public function getTodayAppointmentsCount(): int
    {
        return $this->appointments()
                    ->whereDate('appointment_date', today())
                    ->where('status', 'scheduled')
                    ->count();
    }

    public function getThisMonthCommissions(): float
    {
        return $this->commissions()
                    ->whereMonth('commission_date', now()->month)
                    ->whereYear('commission_date', now()->year)
                    ->sum('amount');
    }

    public function getAverageRating(): float
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsCount(): int
    {
        return $this->reviews()->count();
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->is_verified;
    }

    public function canAddProperty(): bool
    {
        return $this->is_active && $this->is_verified;
    }

    public function canViewLead($leadId): bool
    {
        return $this->leads()->where('id', $leadId)->exists();
    }

    public function canViewAppointment($appointmentId): bool
    {
        return $this->appointments()->where('id', $appointmentId)->exists();
    }

    public function updateRating(): void
    {
        $this->update(['rating' => $this->getAverageRating()]);
    }

    public function incrementTotalSales($amount): void
    {
        $this->increment('total_sales', $amount);
    }

    public function incrementTotalProperties(): void
    {
        $this->increment('total_properties');
    }

    public function verify(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function suspend(): void
    {
        $this->update([
            'is_active' => false,
            'suspended_at' => now(),
        ]);
    }

    public function activate(): void
    {
        $this->update([
            'is_active' => true,
            'suspended_at' => null,
        ]);
    }

    public function getPerformanceScore(): float
    {
        // Calculate performance based on various metrics
        $ratingWeight = 0.3;
        $salesWeight = 0.25;
        $propertiesWeight = 0.2;
        $reviewsWeight = 0.15;
        $experienceWeight = 0.1;

        $ratingScore = ($this->rating / 5) * 100 * $ratingWeight;
        $salesScore = min(($this->total_sales / 1000000) * 100, 100) * $salesWeight;
        $propertiesScore = min(($this->total_properties / 100) * 100, 100) * $propertiesWeight;
        $reviewsScore = min(($this->getTotalReviewsCount() / 50) * 100, 100) * $reviewsWeight;
        $experienceScore = min(($this->experience_years / 20) * 100, 100) * $experienceWeight;

        return $ratingScore + $salesScore + $propertiesScore + $reviewsScore + $experienceScore;
    }

    public function getTopPerformingSpecializations(): array
    {
        // Return top performing specializations based on sales and properties
        return $this->specializations()
                    ->withCount(['properties' => function($query) {
                        $query->where('status', 'sold');
                    }])
                    ->orderBy('properties_count', 'desc')
                    ->limit(3)
                    ->pluck('name')
                    ->toArray();
    }

    public function getRecentActivity(): array
    {
        return [
            'recent_properties' => $this->properties()->latest()->limit(5)->get(),
            'recent_leads' => $this->leads()->latest()->limit(5)->get(),
            'recent_appointments' => $this->appointments()->latest()->limit(5)->get(),
            'recent_commissions' => $this->commissions()->latest()->limit(5)->get(),
        ];
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('license_number', 'like', '%' . $term . '%')
              ->orWhere('specialization', 'like', '%' . $term . '%')
              ->orWhereHas('user', function($subQuery) use ($term) {
                  $subQuery->where('first_name', 'like', '%' . $term . '%')
                           ->orWhere('last_name', 'like', '%' . $term . '%')
                           ->orWhere('email', 'like', '%' . $term . '%');
              });
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($agent) {
            // Create agent profile when agent is created
            AgentProfile::create([
                'agent_id' => $agent->id,
            ]);
        });

        static::updated(function ($agent) {
            // Update related data when agent status changes
            if ($agent->isDirty('is_active')) {
                if (!$agent->is_active) {
                    // Deactivate all active properties
                    $agent->properties()->where('status', 'active')->update(['status' => 'inactive']);
                }
            }
        });
    }
}
