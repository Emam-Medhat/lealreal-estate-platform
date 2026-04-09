<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AgentCertification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'issuing_organization',
        'certification_type',
        'category',
        'level',
        'requirements',
        'validity_period_months',
        'renewal_requirements',
        'cost',
        'accreditation_body',
        'recognition_level',
        'specializations',
        'prerequisites',
        'examination_details',
        'study_materials',
        'online_resources',
        'contact_information',
        'website_url',
        'application_process',
        'approval_process',
        'is_active',
        'is_mandatory',
        'is_recognized_nationally',
        'is_recognized_internationally',
        'status',
        'notes',
        'custom_fields',
    ];

    protected $casts = [
        'validity_period_months' => 'integer',
        'cost' => 'decimal:10,2',
        'requirements' => 'json',
        'renewal_requirements' => 'json',
        'specializations' => 'json',
        'prerequisites' => 'json',
        'examination_details' => 'json',
        'study_materials' => 'json',
        'online_resources' => 'json',
        'contact_information' => 'json',
        'application_process' => 'json',
        'approval_process' => 'json',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'is_recognized_nationally' => 'boolean',
        'is_recognized_internationally' => 'boolean',
        'custom_fields' => 'json',
    ];

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(Agent::class, 'agent_certification_pivot')
                    ->withPivot('issued_date', 'expiry_date', 'certificate_number', 'status', 'verification_code', 'notes')
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_mandatory', false);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certification_type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    public function scopeByOrganization($query, $organization)
    {
        return $query->where('issuing_organization', $organization);
    }

    public function scopeNationallyRecognized($query)
    {
        return $query->where('is_recognized_nationally', true);
    }

    public function scopeInternationallyRecognized($query)
    {
        return $query->where('is_recognized_internationally', true);
    }

    public function scopeWithValidity($query, $months)
    {
        return $query->where('validity_period_months', $months);
    }

    public function scopeWithMaxCost($query, $maxCost)
    {
        return $query->where('cost', '<=', $maxCost);
    }

    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%' . $term . '%')
              ->orWhere('description', 'like', '%' . $term . '%')
              ->orWhere('issuing_organization', 'like', '%' . $term . '%')
              ->orWhere('certification_type', 'like', '%' . $term . '%')
              ->orWhere('category', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getFormattedCostAttribute(): string
    {
        return number_format($this->cost, 2) . ' SAR';
    }

    public function getValidityPeriodAttribute(): string
    {
        if ($this->validity_period_months < 12) {
            return $this->validity_period_months . ' months';
        } elseif ($this->validity_period_months === 12) {
            return '1 year';
        } else {
            $years = floor($this->validity_period_months / 12);
            $months = $this->validity_period_months % 12;
            
            if ($months === 0) {
                return $years . ' years';
            } else {
                return $years . ' years ' . $months . ' months';
            }
        }
    }

    public function getSpecializationsListAttribute(): array
    {
        return $this->specializations ?? [];
    }

    public function getPrerequisitesListAttribute(): array
    {
        return $this->prerequisites ?? [];
    }

    public function getRequirementsListAttribute(): array
    {
        return $this->requirements ?? [];
    }

    public function getRenewalRequirementsListAttribute(): array
    {
        return $this->renewal_requirements ?? [];
    }

    public function getExaminationDetailsListAttribute(): array
    {
        return $this->examination_details ?? [];
    }

    public function getStudyMaterialsListAttribute(): array
    {
        return $this->study_materials ?? [];
    }

    public function getOnlineResourcesListAttribute(): array
    {
        return $this->online_resources ?? [];
    }

    public function getContactInformationListAttribute(): array
    {
        return $this->contact_information ?? [];
    }

    public function getApplicationProcessListAttribute(): array
    {
        return $this->application_process ?? [];
    }

    public function getApprovalProcessListAttribute(): array
    {
        return $this->approval_process ?? [];
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function getWebsiteUrlAttribute(): string
    {
        if (empty($this->website_url)) {
            return '';
        }

        return strpos($this->website_url, 'http') === 0 ? $this->website_url : 'https://' . $this->website_url;
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'active':
                return 'green';
            case 'inactive':
                return 'gray';
            case 'suspended':
                return 'red';
            case 'pending':
                return 'yellow';
            default:
                return 'gray';
        }
    }

    public function getTypeIconAttribute(): string
    {
        switch ($this->certification_type) {
            case 'real_estate':
                return 'home';
            case 'property_management':
                return 'building';
            case 'legal':
                return 'gavel';
            case 'finance':
                return 'dollar-sign';
            case 'marketing':
                return 'megaphone';
            case 'customer_service':
                return 'users';
            case 'technology':
                return 'laptop';
            case 'sustainability':
                return 'leaf';
            default:
                return 'award';
        }
    }

    public function getCategoryIconAttribute(): string
    {
        switch ($this->category) {
            case 'residential':
                return 'house';
            case 'commercial':
                return 'briefcase';
            case 'industrial':
                return 'factory';
            case 'land':
                return 'map';
            case 'luxury':
                return 'crown';
            case 'property_management':
                return 'settings';
            default:
                return 'folder';
        }
    }

    public function getLevelBadgeAttribute(): string
    {
        switch ($this->level) {
            case 'beginner':
                return 'bg-blue-100 text-blue-800';
            case 'intermediate':
                return 'bg-yellow-100 text-yellow-800';
            case 'advanced':
                return 'bg-purple-100 text-purple-800';
            case 'expert':
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function isMandatory(): bool
    {
        return $this->is_mandatory;
    }

    public function isNationallyRecognized(): bool
    {
        return $this->is_recognized_nationally;
    }

    public function isInternationallyRecognized(): bool
    {
        return $this->is_recognized_internationally;
    }

    public function hasWebsite(): bool
    {
        return !empty($this->website_url);
    }

    public function hasRequirements(): bool
    {
        return !empty($this->requirements);
    }

    public function hasRenewalRequirements(): bool
    {
        return !empty($this->renewal_requirements);
    }

    public function hasPrerequisites(): bool
    {
        return !empty($this->prerequisites);
    }

    public function hasExaminationDetails(): bool
    {
        return !empty($this->examination_details);
    }

    public function hasStudyMaterials(): bool
    {
        return !empty($this->study_materials);
    }

    public function hasOnlineResources(): bool
    {
        return !empty($this->online_resources);
    }

    public function hasContactInformation(): bool
    {
        return !empty($this->contact_information);
    }

    public function hasApplicationProcess(): bool
    {
        return !empty($this->application_process);
    }

    public function hasApprovalProcess(): bool
    {
        return !empty($this->approval_process);
    }

    public function hasSpecializations(): bool
    {
        return !empty($this->specializations);
    }

    public function hasCustomFields(): bool
    {
        return !empty($this->custom_fields);
    }

    public function getCertifiedAgentsCount(): int
    {
        return $this->agents()->wherePivot('status', 'active')->count();
    }

    public function getExpiredCertificationsCount(): int
    {
        return $this->agents()->wherePivot('expiry_date', '<', today())->count();
    }

    public function getExpiringSoonCount($days = 30): int
    {
        return $this->agents()
                    ->wherePivot('expiry_date', '>', today())
                    ->wherePivot('expiry_date', '<=', today()->addDays($days))
                    ->count();
    }

    public function getActiveCertificationsCount(): int
    {
        return $this->agents()
                    ->wherePivot('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('expiry_date')
                              ->orWhere('expiry_date', '>', today());
                    })
                    ->count();
    }

    public function isApplicableForAgent($agent): bool
    {
        // Check if certification is applicable based on agent's specializations, experience, etc.
        if ($this->has_specializations && $agent->specializations) {
            $agentSpecializations = $agent->specializations->pluck('name')->toArray();
            $certSpecializations = $this->specializations_list;
            
            if (!empty(array_intersect($agentSpecializations, $certSpecializations))) {
                return true;
            }
        }

        // Check experience requirements
        if ($this->has_prerequisites && isset($this->prerequisites['experience_years'])) {
            if ($agent->experience_years < $this->prerequisites['experience_years']) {
                return false;
            }
        }

        return true;
    }

    public function getRenewalDate($issuedDate): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse($issuedDate)->addMonths($this->validity_period_months);
    }

    public function isExpired($issuedDate): bool
    {
        if (!$this->validity_period_months) {
            return false;
        }

        return $this->getRenewalDate($issuedDate)->isPast();
    }

    public function isExpiringSoon($issuedDate, $days = 30): bool
    {
        if (!$this->validity_period_months) {
            return false;
        }

        $expiryDate = $this->getRenewalDate($issuedDate);
        
        return $expiryDate->isFuture() && $expiryDate->diffInDays(today()) <= $days;
    }

    public function getDaysUntilExpiry($issuedDate): ?int
    {
        if (!$this->validity_period_months) {
            return null;
        }

        $expiryDate = $this->getRenewalDate($issuedDate);
        
        if ($expiryDate->isPast()) {
            return 0;
        }

        return $expiryDate->diffInDays(today());
    }

    public function getExpiryStatus($issuedDate): string
    {
        if (!$this->validity_period_months) {
            return 'lifetime';
        }

        $daysUntilExpiry = $this->getDaysUntilExpiry($issuedDate);

        if ($daysUntilExpiry === 0) {
            return 'expired';
        } elseif ($daysUntilExpiry <= 30) {
            return 'expiring_soon';
        } elseif ($daysUntilExpiry <= 90) {
            return 'expiring';
        } else {
            return 'valid';
        }
    }

    public function getExpiryStatusColor($issuedDate): string
    {
        switch ($this->getExpiryStatus($issuedDate)) {
            case 'expired':
                return 'red';
            case 'expiring_soon':
                return 'orange';
            case 'expiring':
                return 'yellow';
            case 'valid':
                return 'green';
            case 'lifetime':
                return 'blue';
            default:
                return 'gray';
        }
    }

    public function addSpecialization(string $specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (!in_array($specialization, $specializations)) {
            $specializations[] = $specialization;
            $this->update(['specializations' => $specializations]);
        }
    }

    public function removeSpecialization(string $specialization): void
    {
        $specializations = $this->specializations ?? [];
        
        if (($key = array_search($specialization, $specializations)) !== false) {
            unset($specializations[$key]);
            $this->update(['specializations' => array_values($specializations)]);
        }
    }

    public function addRequirement(string $requirement): void
    {
        $requirements = $this->requirements ?? [];
        
        if (!in_array($requirement, $requirements)) {
            $requirements[] = $requirement;
            $this->update(['requirements' => $requirements]);
        }
    }

    public function addRenewalRequirement(string $requirement): void
    {
        $renewalRequirements = $this->renewal_requirements ?? [];
        
        if (!in_array($requirement, $renewalRequirements)) {
            $renewalRequirements[] = $requirement;
            $this->update(['renewal_requirements' => $renewalRequirements]);
        }
    }

    public function addPrerequisite(string $prerequisite): void
    {
        $prerequisites = $this->prerequisites ?? [];
        
        if (!in_array($prerequisite, $prerequisites)) {
            $prerequisites[] = $prerequisite;
            $this->update(['prerequisites' => $prerequisites]);
        }
    }

    public function setCustomField(string $key, $value): void
    {
        $customFields = $this->custom_fields ?? [];
        $customFields[$key] = $value;
        $this->update(['custom_fields' => $customFields]);
    }

    public function getCustomField(string $key, $default = null)
    {
        $customFields = $this->custom_fields ?? [];
        return $customFields[$key] ?? $default;
    }

    public function activate(): void
    {
        $this->update(['is_active' => true, 'status' => 'active']);
    }

    public function deactivate(): void
    {
        $this->update(['is_active' => false, 'status' => 'inactive']);
    }

    public function makeMandatory(): void
    {
        $this->update(['is_mandatory' => true]);
    }

    public function makeOptional(): void
    {
        $this->update(['is_mandatory' => false]);
    }

    public function getCertificationPath(): string
    {
        return strtolower(str_replace(' ', '-', $this->name));
    }

    public function getFullNameAttribute(): string
    {
        return $this->name . ' - ' . $this->issuing_organization;
    }

    public function getShortDescriptionAttribute(): string
    {
        return strlen($this->description) > 100 ? substr($this->description, 0, 100) . '...' : $this->description;
    }

    public function getDifficultyLevelAttribute(): string
    {
        switch ($this->level) {
            case 'beginner':
                return 'Easy';
            case 'intermediate':
                return 'Medium';
            case 'advanced':
                return 'Hard';
            case 'expert':
                return 'Very Hard';
            default:
                return 'Unknown';
        }
    }

    public function getEstimatedTimeToCompleteAttribute(): string
    {
        // This could be based on the type and level of certification
        switch ($this->level) {
            case 'beginner':
                return '1-3 months';
            case 'intermediate':
                return '3-6 months';
            case 'advanced':
                return '6-12 months';
            case 'expert':
                return '1-2 years';
            default:
                return 'Varies';
        }
    }
}
