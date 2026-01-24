<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Developer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_name_ar',
        'license_number',
        'commercial_register',
        'tax_number',
        'developer_type',
        'status',
        'phone',
        'email',
        'website',
        'description',
        'description_ar',
        'logo',
        'address',
        'contact_person',
        'established_year',
        'total_projects',
        'total_investment',
        'specializations',
        'certifications',
        'is_verified',
        'is_featured',
        'rating',
        'review_count',
        'social_media',
        'settings',
        'verified_at',
    ];

    protected $casts = [
        'address' => 'array',
        'contact_person' => 'array',
        'specializations' => 'array',
        'certifications' => 'array',
        'social_media' => 'array',
        'settings' => 'array',
        'total_investment' => 'decimal:2',
        'rating' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_featured' => 'boolean',
        'verified_at' => 'datetime',
        'established_year' => 'integer',
        'total_projects' => 'integer',
        'review_count' => 'integer',
    ];

    protected $dates = [
        'verified_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(DeveloperProfile::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(DeveloperProject::class);
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(DeveloperCertification::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(DeveloperPortfolio::class);
    }

    public function bimModels(): HasMany
    {
        return $this->hasMany(DeveloperBimModel::class);
    }

    public function constructionUpdates(): HasMany
    {
        return $this->hasMany(DeveloperConstructionUpdate::class);
    }

    public function permits(): HasMany
    {
        return $this->hasMany(DeveloperPermit::class);
    }

    public function contractors(): HasMany
    {
        return $this->hasMany(DeveloperContractor::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(DeveloperMilestone::class);
    }

    public function investors(): HasMany
    {
        return $this->hasMany(DeveloperInvestor::class);
    }

    public function financing(): HasMany
    {
        return $this->hasMany(DeveloperFinancing::class);
    }

    public function metaversePreviews(): HasMany
    {
        return $this->hasMany(DeveloperMetaversePreview::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('developer_type', $type);
    }

    public function scopeByRating($query, $minRating = 0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeWithProjects($query)
    {
        return $query->withCount('projects')->having('projects_count', '>', 0);
    }

    // Helper Methods
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->is_verified;
    }

    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    public function getFullAddressAttribute(): string
    {
        if (is_array($this->address)) {
            return implode(', ', array_filter($this->address));
        }
        return $this->address ?? '';
    }

    public function getContactPersonNameAttribute(): string
    {
        return $this->contact_person['name'] ?? '';
    }

    public function getContactPersonPhoneAttribute(): string
    {
        return $this->contact_person['phone'] ?? '';
    }

    public function getContactPersonEmailAttribute(): string
    {
        return $this->contact_person['email'] ?? '';
    }

    public function getWebsiteUrlAttribute(): string
    {
        if ($this->website) {
            return strpos($this->website, 'http') === 0 ? $this->website : 'https://' . $this->website;
        }
        return '';
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : '';
    }

    public function getTotalInvestmentFormattedAttribute(): string
    {
        return number_format($this->total_investment, 2) . ' ' . $this->currency;
    }

    public function getStatusLabelAttribute(): string
    {
        return [
            'pending' => 'Pending',
            'active' => 'Active',
            'suspended' => 'Suspended',
            'inactive' => 'Inactive',
        ][$this->status] ?? $this->status;
    }

    public function getTypeLabelAttribute(): string
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'mixed' => 'Mixed Use',
            'industrial' => 'Industrial',
        ][$this->developer_type] ?? $this->developer_type;
    }

    public function updateRating(): void
    {
        // This would typically calculate rating from reviews
        // For now, we'll keep the existing rating
        $this->save();
    }

    public function incrementProjectCount(): void
    {
        $this->increment('total_projects');
    }

    public function decrementProjectCount(): void
    {
        $this->decrement('total_projects');
    }

    public function addToTotalInvestment(float $amount): void
    {
        $this->increment('total_investment', $amount);
    }

    public function verify(): void
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
        ]);
    }

    public function unverify(): void
    {
        $this->update([
            'is_verified' => false,
            'verified_at' => null,
        ]);
    }

    public function feature(): void
    {
        $this->update(['is_featured' => true]);
    }

    public function unfeature(): void
    {
        $this->update(['is_featured' => false]);
    }

    public function activate(): void
    {
        $this->update(['status' => 'active']);
    }

    public function suspend(): void
    {
        $this->update(['status' => 'suspended']);
    }

    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);
    }

    // Boot methods
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($developer) {
            // Set default values
            $developer->status = $developer->status ?? 'pending';
            $developer->total_projects = $developer->total_projects ?? 0;
            $developer->total_investment = $developer->total_investment ?? 0;
            $developer->rating = $developer->rating ?? 0;
            $developer->review_count = $developer->review_count ?? 0;
        });
        static::created(function ($developer) {
            // Create profile
            if (!$developer->profile) {
                $developer->profile()->create([
                    'company_name_ar' => $developer->company_name_ar ?? $developer->company_name,
                    'about_us' => null,
                    'about_us_ar' => null,
                    'vision' => null,
                    'vision_ar' => null,
                    'mission' => null,
                    'mission_ar' => null,
                    'established_year' => $developer->established_year,
                    'employees_count' => 0,
                    'engineers_count' => 0,
                    'headquarters_address' => $developer->address ?? [],
                ]);
            }
        });

        static::updated(function ($developer) {
            // Handle status changes
            if ($developer->isDirty('status')) {
                // Log status change
                activity()
                    ->performedOn($developer)
                    ->causedBy(auth()->user())
                    ->withProperties(['old_status' => $developer->getOriginal('status'), 'new_status' => $developer->status])
                    ->log('Developer status changed');
            }
        });
    }
}
