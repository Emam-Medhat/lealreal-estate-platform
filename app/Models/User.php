<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens; // Uncomment after installing Laravel Sanctum
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use HasFactory, Notifiable, /* HasApiTokens, */ SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'status',
        'avatar',
        'profile_image',
        'bio',
        'location',
        'country',
        'city',
        'address',
        'postal_code',
        'date_of_birth',
        'gender',
        'website',
        'social_links',
        'preferences',
        'settings',
        'subscription_plan_id',
        'subscription_status',
        'subscription_start_date',
        'subscription_end_date',
        'kyc_status',
        'kyc_verified_at',
        'id_document_type',
        'id_document_number',
        'kyc_documents',
        'wallet_balance',
        'wallet_currency',
        'wallet_address',
        'user_type',
        'first_name',
        'last_name',
        'username',
        'language',
        'currency',
                        'terms',
        'uuid',
        'full_name',
        'account_status',
        'registration_ip',
        'preferred_currency',
        'preferred_language',
        'timezone',
        'email_verified_at',
        'phone_verified_at',
        'last_login_at',
        'last_login_ip',
        'last_login_device',
        'login_count',
        'referral_code',
        'referred_by',
        'enterprise_id',
        'is_active',
        'is_verified',
        'is_premium',
        'premium_expires_at',
        'api_token',
        'remember_token',
        'referred_by_user_id',
        'referral_count',
        'referral_earnings',
        'marketing_consent',
        'newsletter_subscribed',
        'is_agent',
        'agent_license_number',
        'agent_license_expiry',
        'agent_company',
        'agent_bio',
        'agent_specializations',
        'agent_service_areas',
        'agent_commission_rate',
        'properties_listed',
        'properties_sold',
        'properties_rented',
        'total_commission_earned',
        'average_response_time',
        'client_count',
        'client_satisfaction_rate',
        'is_company',
        'company_id',
        'company_role',
        'company_registration_number',
        'company_established_date',
        'company_employees_count',
        'company_headquarters',
        'company_branches',
        'company_annual_revenue',
        'is_developer',
        'developer_id',
        'developer_certification',
        'developer_license_number',
        'developer_license_expiry',
        'projects_completed',
        'projects_ongoing',
        'total_units_built',
        'developer_specializations',
        'is_investor',
        'investor_type',
        'investment_portfolio_value',
        'minimum_investment_amount',
        'maximum_investment_amount',
        'investment_preferences',
        'properties_invested',
        'total_investments',
        'investment_returns',
        'facebook_url',
        'twitter_url',
        'linkedin_url',
        'instagram_url',
        'youtube_url',
        'notifications_preferences',
        'metadata',
        'banned_at',
        'banned_reason',
        'banned_by',
        'suspended_until',
        'suspension_reason',
        'preferred_property_type',
        'preferred_price_min',
        'preferred_price_max',
        'preferred_bedrooms_min',
        'preferred_bedrooms_max',
        'preferred_bathrooms_min',
        'preferred_area_min',
        'preferred_area_max',
        'preferred_amenities',
        'preferred_locations',
        'is_first_time_buyer',
        'is_look_to_rent',
        'is_look_to_buy',
        'property_purpose',
        'last_seen_at',
        'two_factor_enabled',
        'biometric_enabled',
        'property_preferences',
    ];

    protected $guarded = ['_token'];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'api_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'agent_license_expiry' => 'date',
        'company_established_date' => 'date',
        'developer_license_expiry' => 'date',
        'subscription_start_date' => 'datetime',
        'subscription_end_date' => 'datetime',
        'kyc_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'last_login_device' => 'string',
        'last_seen_at' => 'datetime',
        'banned_at' => 'datetime',
        'suspended_until' => 'datetime',
        'premium_expires_at' => 'datetime',
        'deleted_at' => 'datetime',
        'wallet_balance' => 'decimal:2',
        'referral_earnings' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'agent_commission_rate' => 'decimal:2',
        'total_commission_earned' => 'decimal:2',
        'average_response_time' => 'decimal:2',
        'client_satisfaction_rate' => 'decimal:2',
        'company_annual_revenue' => 'decimal:2',
        'total_units_built' => 'decimal:2',
        'investment_portfolio_value' => 'decimal:2',
        'minimum_investment_amount' => 'decimal:2',
        'maximum_investment_amount' => 'decimal:2',
        'total_investments' => 'decimal:2',
        'investment_returns' => 'decimal:2',
        'preferred_price_min' => 'decimal:2',
        'preferred_price_max' => 'decimal:2',
        'preferred_area_min' => 'decimal:2',
        'preferred_area_max' => 'decimal:2',
        'two_factor_enabled' => 'boolean',
        'biometric_enabled' => 'boolean',
        'marketing_consent' => 'boolean',
        'newsletter_subscribed' => 'boolean',
        'is_agent' => 'boolean',
        'is_company' => 'boolean',
        'is_developer' => 'boolean',
        'is_investor' => 'boolean',
        'is_first_time_buyer' => 'boolean',
        'is_look_to_rent' => 'boolean',
        'is_look_to_buy' => 'boolean',
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'is_premium' => 'boolean',
        'property_preferences' => 'array',
        'agent_specializations' => 'array',
        'agent_service_areas' => 'array',
        'company_branches' => 'array',
        'developer_specializations' => 'array',
        'investment_preferences' => 'array',
        'preferred_amenities' => 'array',
        'preferred_locations' => 'array',
        'notifications_preferences' => 'array',
        'metadata' => 'array',
        'preferences' => 'array',
        'settings' => 'array',
        'social_links' => 'array',
        'kyc_documents' => 'array',
        'password' => 'hashed',
    ];

    // Relationships
    public function socialAccounts()
    {
        return $this->hasMany(\App\Models\Auth\UserSocialAccount::class);
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_string($roles)) {
            return $this->user_type === $roles;
        }

        return in_array($this->user_type, $roles);
    }

    public function getIsAdminAttribute(): bool
    {
        return $this->hasRole('admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(\App\Models\Auth\UserDevice::class);
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(\App\Models\Auth\UserSession::class);
    }

    public function reportSchedules(): HasMany
    {
        return $this->hasMany(\App\Models\ReportSchedule::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by_user_id');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referred_by_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function developer(): BelongsTo
    {
        return $this->belongsTo(Developer::class);
    }

    public function bannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'banned_by');
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'agent_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'agent_id');
    }

    public function savedProperties(): HasMany
    {
        return $this->hasMany(\App\Models\UserFavorite::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(\App\Models\UserActivityLog::class);
    }

    public function agent(): HasOne
    {
        return $this->hasOne(Agent::class);
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

    // TODO: Create Transaction model
    // public function transactions(): HasMany
    // {
    //     return $this->hasMany(Transaction::class);
    // }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('account_status', 'active');
    }

    public function scopeAgents($query)
    {
        return $query->where('is_agent', true);
    }

    public function scopeCompanies($query)
    {
        return $query->where('is_company', true);
    }

    public function scopeDevelopers($query)
    {
        return $query->where('is_developer', true);
    }

    public function scopeInvestors($query)
    {
        return $query->where('is_investor', true);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function scopeKYCVerified($query)
    {
        return $query->where('kyc_status', 'verified');
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function isVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function isKYCVerified(): bool
    {
        return $this->kyc_status === 'verified';
    }

    public function isBanned(): bool
    {
        return !is_null($this->banned_at);
    }

    public function isSuspended(): bool
    {
        return $this->suspended_until && $this->suspended_until > now();
    }

    public function canLogin(): bool
    {
        return !$this->isBanned() && !$this->isSuspended() && $this->account_status === 'active';
    }

    public function generateReferralCode(): string
    {
        do {
            $code = strtoupper(substr(md5($this->id . $this->email . time()), 0, 8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }

    public function incrementLoginCount(): void
    {
        $this->increment('login_count');
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'last_login_device' => request()->userAgent(),
        ]);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(UserWallet::class);
    }

    public function favoriteProperties()
    {
        return $this->morphedByMany(Property::class, 'favoritable', 'user_favorites')
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->morphMany(\Illuminate\Notifications\DatabaseNotification::class, 'notifiable');
    }

    public function updateWalletBalance(float $amount, string $operation = 'add'): void
    {
        if ($operation === 'add') {
            $this->increment('wallet_balance', $amount);
        } elseif ($operation === 'subtract') {
            $this->decrement('wallet_balance', $amount);
        }
    }

    public function getAgeAttribute(): ?int
    {
        return $this->date_of_birth ? \Carbon\Carbon::parse($this->date_of_birth)->age : null;
    }

    public function getLocationAttribute(): string
    {
        $parts = array_filter([$this->city, $this->state, $this->country]);
        return implode(', ', $parts);
    }

    public function getAvatarUrlAttribute(): ?string
    {
        // Remove 'avatars/' prefix if it exists in the filename
        $filename = $this->avatar;
        if ($filename && str_starts_with($filename, 'avatars/')) {
            $filename = substr($filename, 8); // Remove 'avatars/' prefix
        }

        return $filename ? asset('storage/avatars/' . $filename) : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->avatar_thumbnail ? asset('storage/avatars/thumbnails/' . $this->avatar_thumbnail) : null;
    }

    // Notification Preferences
    public function getNotificationPreference(string $key, $default = true): bool
    {
        $preferences = $this->notifications_preferences ?? [];
        return $preferences[$key] ?? $default;
    }

    public function setNotificationPreference(string $key, bool $value): void
    {
        $preferences = $this->notifications_preferences ?? [];
        $preferences[$key] = $value;
        $this->update(['notifications_preferences' => $preferences]);
    }
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }
}
