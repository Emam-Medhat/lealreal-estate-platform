<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_id',
        'bio',
        'photo',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'social_media',
        'languages',
        'specializations',
        'service_areas',
        'achievements',
        'education',
        'experience',
        'awards',
        'professional_memberships',
        'areas_of_expertise',
        'personal_statement',
        'company_name',
        'company_logo',
        'company_description',
        'office_address',
        'office_phone',
        'office_email',
        'working_hours',
        'response_time',
        'preferred_contact_method',
        'additional_info',
    ];

    protected $casts = [
        'social_media' => 'json',
        'languages' => 'json',
        'specializations' => 'json',
        'service_areas' => 'json',
        'achievements' => 'json',
        'education' => 'json',
        'experience' => 'json',
        'awards' => 'json',
        'professional_memberships' => 'json',
        'areas_of_expertise' => 'json',
        'working_hours' => 'json',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    // Helper Methods
    public function getFullNameAttribute(): string
    {
        return $this->agent ? $this->agent->full_name : 'N/A';
    }

    public function getFormattedPhoneAttribute(): string
    {
        return $this->phone ? format_phone($this->phone) : '';
    }

    public function getPrimaryLanguageAttribute(): ?string
    {
        $languages = $this->languages ?? [];
        return $languages[0] ?? null;
    }

    public function getTopSpecialtiesAttribute(): array
    {
        $specializations = $this->specializations ?? [];
        return array_slice($specializations, 0, 3);
    }

    public function getYearsOfExperienceAttribute(): int
    {
        return $this->agent ? $this->agent->experience_years : 0;
    }

    public function getRatingAttribute(): float
    {
        return $this->agent ? $this->agent->rating : 0;
    }

    public function getTotalSalesAttribute(): float
    {
        return $this->agent ? $this->agent->total_sales : 0;
    }

    public function getActivePropertiesCountAttribute(): int
    {
        return $this->agent ? $this->agent->getActivePropertiesCount() : 0;
    }

    public function isAvailable(): bool
    {
        return $this->agent ? $this->agent->isAvailable() : false;
    }

    public function getSocialMediaLinksAttribute(): array
    {
        return $this->social_media ?? [];
    }

    public function getLanguagesListAttribute(): array
    {
        return $this->languages ?? [];
    }

    public function getSpecialtiesListAttribute(): array
    {
        return $this->specialties ?? [];
    }

    public function getAchievementsListAttribute(): array
    {
        return $this->achievements ?? [];
    }

    public function getEducationListAttribute(): array
    {
        return $this->education ?? [];
    }

    public function getExperienceListAttribute(): array
    {
        return $this->experience ?? [];
    }

    public function getAwardsListAttribute(): array
    {
        return $this->awards ?? [];
    }

    public function getProfessionalMembershipsListAttribute(): array
    {
        return $this->professional_memberships ?? [];
    }

    public function getAreasOfExpertiseListAttribute(): array
    {
        return $this->areas_of_expertise ?? [];
    }

    public function getWorkingHoursAttribute(): array
    {
        return $this->working_hours ?? [
            'sunday' => ['closed'],
            'monday' => ['9:00 AM', '5:00 PM'],
            'tuesday' => ['9:00 AM', '5:00 PM'],
            'wednesday' => ['9:00 AM', '5:00 PM'],
            'thursday' => ['9:00 AM', '5:00 PM'],
            'friday' => ['9:00 AM', '5:00 PM'],
            'saturday' => ['closed'],
        ];
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public function getOfficeFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->office_address,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    public function hasWebsite(): bool
    {
        return !empty($this->website);
    }

    public function hasSocialMedia(): bool
    {
        return !empty($this->social_media);
    }

    public function hasCompanyInfo(): bool
    {
        return !empty($this->company_name);
    }

    public function hasPhoto(): bool
    {
        return !empty($this->photo);
    }

    public function hasCompanyLogo(): bool
    {
        return !empty($this->company_logo);
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->hasPhoto()) {
            return asset('storage/' . $this->photo);
        }

        return asset('images/default-agent-avatar.png');
    }

    public function getCompanyLogoUrlAttribute(): string
    {
        if ($this->hasCompanyLogo()) {
            return asset('storage/' . $this->company_logo);
        }

        return asset('images/default-company-logo.png');
    }

    public function getWebsiteUrlAttribute(): string
    {
        if ($this->hasWebsite()) {
            return strpos($this->website, 'http') === 0 ? $this->website : 'https://' . $this->website;
        }

        return '';
    }

    public function getContactInfoAttribute(): array
    {
        return [
            'phone' => $this->phone,
            'email' => $this->email,
            'website' => $this->getWebsiteUrlAttribute(),
            'address' => $this->getFullAddressAttribute(),
            'preferred_contact_method' => $this->preferred_contact_method,
            'response_time' => $this->response_time,
        ];
    }

    public function getProfessionalInfoAttribute(): array
    {
        return [
            'bio' => $this->bio,
            'specialties' => $this->getSpecialtiesListAttribute(),
            'languages' => $this->getLanguagesListAttribute(),
            'education' => $this->getEducationListAttribute(),
            'experience' => $this->getExperienceListAttribute(),
            'achievements' => $this->getAchievementsListAttribute(),
            'awards' => $this->getAwardsListAttribute(),
            'professional_memberships' => $this->getProfessionalMembershipsListAttribute(),
            'areas_of_expertise' => $this->getAreasOfExpertiseListAttribute(),
        ];
    }

    public function getCompanyInfoAttribute(): array
    {
        return [
            'name' => $this->company_name,
            'logo' => $this->getCompanyLogoUrlAttribute(),
            'description' => $this->company_description,
            'address' => $this->getOfficeFullAddressAttribute(),
            'phone' => $this->office_phone,
            'email' => $this->office_email,
            'working_hours' => $this->getWorkingHoursAttribute(),
        ];
    }

    public function getCompletionPercentageAttribute(): int
    {
        $requiredFields = [
            'bio', 'phone', 'email', 'address', 'city', 'country',
            'languages', 'specialties', 'experience'
        ];

        $filledFields = 0;
        $totalFields = count($requiredFields);

        foreach ($requiredFields as $field) {
            if (!empty($this->$field)) {
                $filledFields++;
            }
        }

        return round(($filledFields / $totalFields) * 100);
    }

    public function isProfileComplete(): bool
    {
        return $this->getCompletionPercentageAttribute() >= 80;
    }

    public function scopeComplete($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM agent_profiles WHERE agent_id = agents.id AND 
            (bio IS NOT NULL AND phone IS NOT NULL AND email IS NOT NULL AND address IS NOT NULL AND 
             city IS NOT NULL AND country IS NOT NULL AND languages IS NOT NULL AND specialties IS NOT NULL AND 
             experience IS NOT NULL)) >= 8');
    }

    public function scopeIncomplete($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM agent_profiles WHERE agent_id = agents.id AND 
            (bio IS NOT NULL AND phone IS NOT NULL AND email IS NOT NULL AND address IS NOT NULL AND 
             city IS NOT NULL AND country IS NOT NULL AND languages IS NOT NULL AND specialties IS NOT NULL AND 
             experience IS NOT NULL)) < 8');
    }

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeWithLanguage($query, $language)
    {
        return $query->whereJsonContains('languages', $language);
    }

    public function scopeWithSpecialty($query, $specialty)
    {
        return $query->whereJsonContains('specialties', $specialty);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('bio', 'like', '%' . $term . '%')
              ->orWhere('company_name', 'like', '%' . $term . '%')
              ->orWhere('city', 'like', '%' . $term . '%')
              ->orWhere('country', 'like', '%' . $term . '%')
              ->orWhereHas('agent', function($subQuery) use ($term) {
                  $subQuery->whereHas('user', function($userQuery) use ($term) {
                      $userQuery->where('first_name', 'like', '%' . $term . '%')
                               ->orWhere('last_name', 'like', '%' . $term . '%')
                               ->orWhere('email', 'like', '%' . $term . '%');
                  });
              });
        });
    }
}
