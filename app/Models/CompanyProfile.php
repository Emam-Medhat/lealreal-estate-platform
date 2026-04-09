<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
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
        'phone',
        'email',
        'website',
        'logo',
        'cover_image',
        'services',
        'specializations',
        'certifications',
        'awards',
        'social_links',
        'contact_person',
        'contact_title',
        'business_hours',
        'languages',
        'service_areas',
        'company_size',
        'industry',
        'target_markets',
        'mission_statement',
        'vision_statement',
        'company_values',
        'history',
        'achievements',
        'partners',
        'clients',
        'testimonials',
        'faq',
        'press_mentions',
        'gallery_images',
        'video_url',
        'virtual_tour_url',
        'brokerage_license',
        'insurance_info',
        'compliance_info',
        'seo_title',
        'seo_description',
        'seo_keywords',
        'metadata',
    ];

    protected $casts = [
        'founded_date' => 'date',
        'annual_revenue' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'services' => 'json',
        'specializations' => 'json',
        'certifications' => 'json',
        'awards' => 'json',
        'social_links' => 'json',
        'business_hours' => 'json',
        'languages' => 'array',
        'service_areas' => 'array',
        'target_markets' => 'array',
        'partners' => 'array',
        'clients' => 'array',
        'testimonials' => 'array',
        'faq' => 'array',
        'press_mentions' => 'array',
        'gallery_images' => 'array',
        'company_values' => 'array',
        'metadata' => 'json',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo ? asset('storage/' . $this->logo) : asset('images/default-company-logo.png');
    }

    public function getCoverImageUrlAttribute(): string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : asset('images/default-cover.jpg');
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

    public function getCompanySizeLabelAttribute(): string
    {
        return match($this->company_size) {
            'startup' => __('Startup (1-10)'),
            'small' => __('Small (11-50)'),
            'medium' => __('Medium (51-200)'),
            'large' => __('Large (201-1000)'),
            'enterprise' => __('Enterprise (1000+)'),
            default => __('Unknown')
        };
    }

    public function getIndustryLabelAttribute(): string
    {
        return match($this->industry) {
            'residential' => __('Residential Real Estate'),
            'commercial' => __('Commercial Real Estate'),
            'industrial' => __('Industrial Real Estate'),
            'mixed_use' => __('Mixed-Use Development'),
            'property_management' => __('Property Management'),
            'real_estate_finance' => __('Real Estate Finance'),
            'construction' => __('Construction & Development'),
            'architecture' => __('Architecture & Design'),
            'consulting' => __('Real Estate Consulting'),
            'other' => __('Other'),
            default => __(ucfirst($this->industry))
        };
    }

    public function getFormattedEmployeeCountAttribute(): string
    {
        return number_format($this->employee_count);
    }

    public function getFormattedAnnualRevenueAttribute(): string
    {
        return '$' . number_format($this->annual_revenue, 0);
    }

    public function getYearsInBusinessAttribute(): int
    {
        return $this->founded_date ? $this->founded_date->diffInYears(now()) : 0;
    }

    public function getServicesListAttribute(): array
    {
        return $this->services ?? [];
    }

    public function getSpecializationsListAttribute(): array
    {
        return $this->specializations ?? [];
    }

    public function getCertificationsListAttribute(): array
    {
        return $this->certifications ?? [];
    }

    public function getAwardsListAttribute(): array
    {
        return $this->awards ?? [];
    }

    public function getLanguagesListAttribute(): array
    {
        return $this->languages ?? [];
    }

    public function getServiceAreasListAttribute(): array
    {
        return $this->service_areas ?? [];
    }

    public function hasService(string $service): bool
    {
        return in_array($service, $this->services_list);
    }

    public function hasSpecialization(string $specialization): bool
    {
        return in_array($specialization, $this->specializations_list);
    }

    public function hasCertification(string $certification): bool
    {
        return in_array($certification, $this->certifications_list);
    }

    public function hasAward(string $award): bool
    {
        return in_array($award, $this->awards_list);
    }

    public function speaksLanguage(string $language): bool
    {
        return in_array($language, $this->languages_list);
    }

    public function servesArea(string $area): bool
    {
        return in_array($area, $this->service_areas_list);
    }

    public function getSocialLinksAttribute(): array
    {
        return $this->social_links ?? [];
    }

    public function getWebsiteUrlAttribute(): ?string
    {
        $website = $this->website;
        return $website && !str_starts_with($website, 'http') ? 'https://' . $website : $website;
    }

    public function getBusinessHoursFormattedAttribute(): string
    {
        if (!$this->business_hours) {
            return 'Not specified';
        }

        $hours = [];
        foreach ($this->business_hours as $day => $time) {
            if ($time['open'] && $time['close']) {
                $hours[] = ucfirst($day) . ': ' . $time['open'] . ' - ' . $time['close'];
            }
        }

        return implode(' | ', $hours);
    }

    public function isOpenNow(): bool
    {
        if (!$this->business_hours) {
            return false;
        }

        $now = now();
        $currentDay = strtolower($now->format('l'));
        $currentTime = $now->format('H:i');

        if (!isset($this->business_hours[$currentDay])) {
            return false;
        }

        $dayHours = $this->business_hours[$currentDay];
        
        if (!$dayHours['open'] || !$dayHours['close']) {
            return false;
        }

        return $currentTime >= $dayHours['open'] && $currentTime <= $dayHours['close'];
    }

    public function getCompletionPercentageAttribute(): int
    {
        $fields = [
            'description', 'founded_date', 'employee_count', 'annual_revenue',
            'address', 'city', 'state', 'country', 'postal_code', 'phone',
            'email', 'website', 'logo', 'services', 'specializations',
            'certifications', 'social_links', 'business_hours', 'languages',
            'service_areas', 'company_size', 'industry', 'mission_statement'
        ];

        $completed = 0;
        $total = count($fields);

        foreach ($fields as $field) {
            if ($this->$field && (!is_array($this->$field) || !empty($this->$field))) {
                $completed++;
            }
        }

        return round(($completed / $total) * 100);
    }

    public function scopeWithLogo($query)
    {
        return $query->whereNotNull('logo')->where('logo', '!=', '');
    }

    public function scopeInLocation($query, string $city, string $state = null)
    {
        $query->where('city', $city);
        
        if ($state) {
            $query->where('state', $state);
        }
        
        return $query;
    }

    public function scopeByIndustry($query, string $industry)
    {
        return $query->where('industry', $industry);
    }

    public function scopeBySize($query, string $size)
    {
        return $query->where('company_size', $size);
    }

    public function scopeWithService($query, string $service)
    {
        return $query->whereJsonContains('services', $service);
    }

    public function scopeWithSpecialization($query, string $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeWithCertification($query, string $certification)
    {
        return $query->whereJsonContains('certifications', $certification);
    }
}
