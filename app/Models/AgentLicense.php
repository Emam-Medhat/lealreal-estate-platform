<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgentLicense extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'agent_id',
        'license_number',
        'license_type',
        'issuing_authority',
        'state_province',
        'country',
        'issue_date',
        'expiry_date',
        'status',
        'verification_status',
        'verification_code',
        'verified_at',
        'verified_by',
        'renewal_date',
        'renewal_count',
        'suspension_date',
        'suspension_reason',
        'revocation_date',
        'revocation_reason',
        'disciplinary_actions',
        'restrictions',
        'endorsements',
        'specializations',
        'license_class',
        'practice_areas',
        'educational_requirements',
        'experience_requirements',
        'examination_passed',
        'continuing_education_credits',
        'last_ce_credit_date',
        'next_ce_deadline',
        'professional_liability_insurance',
        'insurance_provider',
        'insurance_policy_number',
        'insurance_expiry_date',
        'bond_information',
        'bond_number',
        'bond_expiry_date',
        'office_address',
        'supervising_broker',
        'broker_license_number',
        'company_affiliation',
        'affiliation_date',
        'independent_contractor',
        'commission_split_percentage',
        'fees_structure',
        'document_url',
        'certificate_url',
        'notes',
        'custom_fields',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'renewal_date' => 'date',
        'suspension_date' => 'date',
        'revocation_date' => 'date',
        'last_ce_credit_date' => 'date',
        'next_ce_deadline' => 'date',
        'insurance_expiry_date' => 'date',
        'bond_expiry_date' => 'date',
        'affiliation_date' => 'date',
        'renewal_count' => 'integer',
        'continuing_education_credits' => 'integer',
        'professional_liability_insurance' => 'boolean',
        'independent_contractor' => 'boolean',
        'disciplinary_actions' => 'json',
        'restrictions' => 'json',
        'endorsements' => 'json',
        'specializations' => 'json',
        'practice_areas' => 'json',
        'educational_requirements' => 'json',
        'experience_requirements' => 'json',
        'examination_passed' => 'json',
        'fees_structure' => 'json',
        'custom_fields' => 'json',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(AgentLicenseRenewal::class);
    }

    // Scopes
    public function scopeByAgent($query, $agentId)
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('license_type', $type);
    }

    public function scopeByAuthority($query, $authority)
    {
        return $query->where('issuing_authority', $authority);
    }

    public function scopeByState($query, $state)
    {
        return $query->where('state_province', $state);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVerificationStatus($query, $verificationStatus)
    {
        return $query->where('verification_status', $verificationStatus);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>', today());
                    });
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', today());
    }

    public function scopeExpiringSoon($query, $days = 90)
    {
        return $query->where('expiry_date', '>', today())
                    ->where('expiry_date', '<=', today()->addDays($days));
    }

    public function scopeExpiringThisMonth($query)
    {
        return $query->whereMonth('expiry_date', now()->month)
                    ->whereYear('expiry_date', now()->year);
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeUnverified($query)
    {
        return $query->where('verification_status', 'unverified');
    }

    public function scopePendingVerification($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeWithInsurance($query)
    {
        return $query->where('professional_liability_insurance', true);
    }

    public function scopeWithoutInsurance($query)
    {
        return $query->where('professional_liability_insurance', false);
    }

    public function scopeIndependentContractors($query)
    {
        return $query->where('independent_contractor', true);
    }

    public function scopeEmployees($query)
    {
        return $query->where('independent_contractor', false);
    }

    public function scopeByClass($query, $class)
    {
        return $query->where('license_class', $class);
    }

    public function scopeWithSpecialization($query, $specialization)
    {
        return $query->whereJsonContains('specializations', $specialization);
    }

    public function scopeWithPracticeArea($query, $area)
    {
        return $query->whereJsonContains('practice_areas', $area);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('license_number', 'like', '%' . $term . '%')
              ->orWhere('license_type', 'like', '%' . $term . '%')
              ->orWhere('issuing_authority', 'like', '%' . $term . '%')
              ->orWhere('state_province', 'like', '%' . $term . '%')
              ->orWhere('country', 'like', '%' . $term . '%')
              ->orWhere('supervising_broker', 'like', '%' . $term . '%')
              ->orWhere('company_affiliation', 'like', '%' . $term . '%')
              ->orWhere('notes', 'like', '%' . $term . '%');
        });
    }

    // Helper Methods
    public function getFormattedIssueDateAttribute(): string
    {
        return $this->issue_date->format('M d, Y');
    }

    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expiry_date ? $this->expiry_date->format('M d, Y') : 'No expiry';
    }

    public function getFormattedRenewalDateAttribute(): string
    {
        return $this->renewal_date ? $this->renewal_date->format('M d, Y') : 'Not set';
    }

    public function getFormattedVerificationDateAttribute(): string
    {
        return $this->verified_at ? $this->verified_at->format('M d, Y H:i') : 'Not verified';
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        $diff = $this->expiry_date->diffInDays(today(), false);
        
        return $diff >= 0 ? $diff : -$diff;
    }

    public function getDaysUntilRenewalAttribute(): ?int
    {
        if (!$this->renewal_date) {
            return null;
        }

        $diff = $this->renewal_date->diffInDays(today(), false);
        
        return $diff >= 0 ? $diff : -$diff;
    }

    public function getDaysUntilNextCEDeadlineAttribute(): ?int
    {
        if (!$this->next_ce_deadline) {
            return null;
        }

        $diff = $this->next_ce_deadline->diffInDays(today(), false);
        
        return $diff >= 0 ? $diff : -$diff;
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->isFuture() && 
               $this->expiry_date->diffInDays(today()) <= 90;
    }

    public function getIsExpiringThisMonthAttribute(): bool
    {
        return $this->expiry_date && 
               $this->expiry_date->month === now()->month && 
               $this->expiry_date->year === now()->year;
    }

    public function getIsRenewalDueAttribute(): bool
    {
        return $this->renewal_date && $this->renewal_date->isPast();
    }

    public function getIsRenewalDueSoonAttribute(): bool
    {
        return $this->renewal_date && 
               $this->renewal_date->isFuture() && 
               $this->renewal_date->diffInDays(today()) <= 30;
    }

    public function getIsCEDueAttribute(): bool
    {
        return $this->next_ce_deadline && $this->next_ce_deadline->isPast();
    }

    public function getIsCEDueSoonAttribute(): bool
    {
        return $this->next_ce_deadline && 
               $this->next_ce_deadline->isFuture() && 
               $this->next_ce_deadline->diffInDays(today()) <= 60;
    }

    public function getIsInsuranceExpiredAttribute(): bool
    {
        return $this->professional_liability_insurance && 
               $this->insurance_expiry_date && 
               $this->insurance_expiry_date->isPast();
    }

    public function getIsBondExpiredAttribute(): bool
    {
        return $this->bond_number && 
               $this->bond_expiry_date && 
               $this->bond_expiry_date->isPast();
    }

    public function getYearsActiveAttribute(): int
    {
        return $this->issue_date ? $this->issue_date->diffInYears(today()) : 0;
    }

    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'active':
                return $this->is_expired ? 'orange' : 'green';
            case 'inactive':
                return 'gray';
            case 'suspended':
                return 'red';
            case 'revoked':
                return 'black';
            default:
                return 'gray';
        }
    }

    public function getVerificationStatusColorAttribute(): string
    {
        switch ($this->verification_status) {
            case 'verified':
                return 'green';
            case 'unverified':
                return 'red';
            case 'pending':
                return 'yellow';
            default:
                return 'gray';
        }
    }

    public function getExpiryStatusAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'no_expiry';
        } elseif ($this->is_expired) {
            return 'expired';
        } elseif ($this->is_expiring_soon) {
            return 'expiring_soon';
        } else {
            return 'valid';
        }
    }

    public function getExpiryStatusColorAttribute(): string
    {
        switch ($this->expiry_status) {
            case 'expired':
                return 'red';
            case 'expiring_soon':
                return 'orange';
            case 'valid':
                return 'green';
            case 'no_expiry':
                return 'blue';
            default:
                return 'gray';
        }
    }

    public function getDisciplinaryActionsListAttribute(): array
    {
        return $this->disciplinary_actions ?? [];
    }

    public function getRestrictionsListAttribute(): array
    {
        return $this->restrictions ?? [];
    }

    public function getEndorsementsListAttribute(): array
    {
        return $this->endorsements ?? [];
    }

    public function getSpecializationsListAttribute(): array
    {
        return $this->specializations ?? [];
    }

    public function getPracticeAreasListAttribute(): array
    {
        return $this->practice_areas ?? [];
    }

    public function getEducationalRequirementsListAttribute(): array
    {
        return $this->educational_requirements ?? [];
    }

    public function getExperienceRequirementsListAttribute(): array
    {
        return $this->experience_requirements ?? [];
    }

    public function getExaminationPassedListAttribute(): array
    {
        return $this->examination_passed ?? [];
    }

    public function getFeesStructureListAttribute(): array
    {
        return $this->fees_structure ?? [];
    }

    public function getCustomFieldsListAttribute(): array
    {
        return $this->custom_fields ?? [];
    }

    public function getFullLicenseNumberAttribute(): string
    {
        return $this->license_type . ' - ' . $this->license_number;
    }

    public function getFullLocationAttribute(): string
    {
        $parts = array_filter([
            $this->state_province,
            $this->country,
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedCommissionSplitAttribute(): string
    {
        return $this->commission_split_percentage ? $this->commission_split_percentage . '%' : 'Not set';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->is_expired;
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function canPractice(): bool
    {
        return $this->isActive() && $this->isVerified() && !$this->is_suspended;
    }

    public function needsRenewal(): bool
    {
        return $this->renewal_date && $this->renewal_date->isPast();
    }

    public function needsCECredits(): bool
    {
        return $this->next_ce_deadline && $this->next_ce_deadline->isPast();
    }

    public function hasDisciplinaryActions(): bool
    {
        return !empty($this->disciplinary_actions);
    }

    public function hasRestrictions(): bool
    {
        return !empty($this->restrictions);
    }

    public function hasEndorsements(): bool
    {
        return !empty($this->endorsements);
    }

    public function hasSpecializations(): bool
    {
        return !empty($this->specializations);
    }

    public function hasPracticeAreas(): bool
    {
        return !empty($this->practice_areas);
    }

    public function hasInsurance(): bool
    {
        return $this->professional_liability_insurance;
    }

    public function hasBond(): bool
    {
        return !empty($this->bond_number);
    }

    public function hasDocument(): bool
    {
        return !empty($this->document_url);
    }

    public function hasCertificate(): bool
    {
        return !empty($this->certificate_url);
    }

    public function verify($verifiedBy = null): void
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_at' => now(),
            'verified_by' => $verifiedBy,
        ]);
    }

    public function unverify(): void
    {
        $this->update([
            'verification_status' => 'unverified',
            'verified_at' => null,
            'verified_by' => null,
        ]);
    }

    public function suspend($reason = null): void
    {
        $this->update([
            'status' => 'suspended',
            'suspension_date' => now(),
            'suspension_reason' => $reason,
        ]);
    }

    public function revoke($reason = null): void
    {
        $this->update([
            'status' => 'revoked',
            'revocation_date' => now(),
            'revocation_reason' => $reason,
        ]);
    }

    public function reactivate(): void
    {
        $this->update([
            'status' => 'active',
            'suspension_date' => null,
            'suspension_reason' => null,
        ]);
    }

    public function renew($newExpiryDate, $renewalCount = null): void
    {
        $this->update([
            'expiry_date' => $newExpiryDate,
            'renewal_date' => null,
            'renewal_count' => $renewalCount ?? ($this->renewal_count + 1),
        ]);
    }

    public function addDisciplinaryAction(array $action): void
    {
        $actions = $this->disciplinary_actions ?? [];
        $actions[] = array_merge($action, ['date' => now()->format('Y-m-d')]);
        $this->update(['disciplinary_actions' => $actions]);
    }

    public function addRestriction(string $restriction): void
    {
        $restrictions = $this->restrictions ?? [];
        
        if (!in_array($restriction, $restrictions)) {
            $restrictions[] = $restriction;
            $this->update(['restrictions' => $restrictions]);
        }
    }

    public function removeRestriction(string $restriction): void
    {
        $restrictions = $this->restrictions ?? [];
        
        if (($key = array_search($restriction, $restrictions)) !== false) {
            unset($restrictions[$key]);
            $this->update(['restrictions' => array_values($restrictions)]);
        }
    }

    public function addEndorsement(string $endorsement): void
    {
        $endorsements = $this->endorsements ?? [];
        
        if (!in_array($endorsement, $endorsements)) {
            $endorsements[] = $endorsement;
            $this->update(['endorsements' => $endorsements]);
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

    public function addPracticeArea(string $area): void
    {
        $areas = $this->practice_areas ?? [];
        
        if (!in_array($area, $areas)) {
            $areas[] = $area;
            $this->update(['practice_areas' => $areas]);
        }
    }

    public function updateCECredits($credits): void
    {
        $this->update([
            'continuing_education_credits' => $credits,
            'last_ce_credit_date' => now(),
        ]);
    }

    public function setNextCEDeadline($deadline): void
    {
        $this->update(['next_ce_deadline' => $deadline]);
    }

    public function updateInsurance($provider, $policyNumber, $expiryDate): void
    {
        $this->update([
            'professional_liability_insurance' => true,
            'insurance_provider' => $provider,
            'insurance_policy_number' => $policyNumber,
            'insurance_expiry_date' => $expiryDate,
        ]);
    }

    public function removeInsurance(): void
    {
        $this->update([
            'professional_liability_insurance' => false,
            'insurance_provider' => null,
            'insurance_policy_number' => null,
            'insurance_expiry_date' => null,
        ]);
    }

    public function updateBond($bondNumber, $expiryDate): void
    {
        $this->update([
            'bond_number' => $bondNumber,
            'bond_expiry_date' => $expiryDate,
        ]);
    }

    public function removeBond(): void
    {
        $this->update([
            'bond_number' => null,
            'bond_expiry_date' => null,
        ]);
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

    public function generateVerificationCode(): void
    {
        $code = 'LIC-' . strtoupper(uniqid());
        $this->update(['verification_code' => $code]);
    }

    public function getLicenseTypeDisplayAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->license_type));
    }

    public function getLicenseClassDisplayAttribute(): string
    {
        return $this->license_class ? strtoupper($this->license_class) : 'Not specified';
    }

    public function getComplianceStatusAttribute(): string
    {
        if (!$this->isVerified()) {
            return 'unverified';
        } elseif ($this->isSuspended() || $this->isRevoked()) {
            return 'non_compliant';
        } elseif ($this->isExpired()) {
            return 'expired';
        } elseif ($this->isCEDue()) {
            return 'ce_required';
        } elseif ($this->isInsuranceExpired()) {
            return 'insurance_expired';
        } elseif ($this->isBondExpired()) {
            return 'bond_expired';
        } else {
            return 'compliant';
        }
    }

    public function getComplianceStatusColorAttribute(): string
    {
        switch ($this->compliance_status) {
            case 'compliant':
                return 'green';
            case 'unverified':
                return 'yellow';
            case 'non_compliant':
                return 'red';
            case 'expired':
                return 'red';
            case 'ce_required':
                return 'orange';
            case 'insurance_expired':
                return 'orange';
            case 'bond_expired':
                return 'orange';
            default:
                return 'gray';
        }
    }

    public function getComplianceIssuesAttribute(): array
    {
        $issues = [];

        if (!$this->isVerified()) {
            $issues[] = 'License not verified';
        }

        if ($this->isSuspended()) {
            $issues[] = 'License suspended: ' . ($this->suspension_reason ?? 'No reason provided');
        }

        if ($this->isRevoked()) {
            $issues[] = 'License revoked: ' . ($this->revocation_reason ?? 'No reason provided');
        }

        if ($this->isExpired()) {
            $issues[] = 'License expired on ' . $this->formatted_expiry_date;
        }

        if ($this->isCEDue()) {
            $issues[] = 'Continuing education credits required';
        }

        if ($this->isInsuranceExpired()) {
            $issues[] = 'Professional liability insurance expired';
        }

        if ($this->isBondExpired()) {
            $issues[] = 'Bond expired';
        }

        if ($this->hasDisciplinaryActions()) {
            $issues[] = 'Disciplinary actions on record';
        }

        if ($this->hasRestrictions()) {
            $issues[] = 'License restrictions apply';
        }

        return $issues;
    }

    public function hasComplianceIssues(): bool
    {
        return !empty($this->compliance_issues);
    }

    public function getLicenseValidityPeriodAttribute(): string
    {
        if (!$this->expiry_date) {
            return 'Lifetime';
        }

        return $this->issue_date->format('M d, Y') . ' - ' . $this->formatted_expiry_date;
    }
}
