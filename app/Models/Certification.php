<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'compliance_check_id',
        'inspector_id',
        'certification_type',
        'certificate_number',
        'issue_date',
        'expiry_date',
        'scope',
        'conditions',
        'notes',
        'status',
        'created_by',
        'renewal_reason',
        'renewal_notes',
        'renewed_at',
        'renewed_by',
        'suspension_reason',
        'suspension_notes',
        'suspended_at',
        'suspended_by',
        'reactivation_reason',
        'reactivation_notes',
        'reactivated_at',
        'reactivated_by',
        'revocation_reason',
        'revocation_notes',
        'revoked_at',
        'revoked_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'issue_date' => 'datetime',
        'expiry_date' => 'datetime',
        'renewed_at' => 'datetime',
        'suspended_at' => 'datetime',
        'reactivated_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function complianceCheck(): BelongsTo
    {
        return $this->belongsTo(ComplianceCheck::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(CertificationAttachment::class);
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'occupancy' => 'إشغال',
            'safety' => 'سلامة',
            'environmental' => 'بيئي',
            'accessibility' => 'إمكانية الوصول',
            'fire' => 'حريق',
            'structural' => 'إنشائي',
        ];

        return $labels[$this->certification_type] ?? $this->certification_type;
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'suspended' => 'موقوف',
            'revoked' => 'ملغي',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'active' => 'success',
            'expired' => 'danger',
            'suspended' => 'warning',
            'revoked' => 'dark',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getDaysUntilExpiry(): int
    {
        if (!$this->expiry_date) {
            return -1;
        }

        return $this->expiry_date->diffInDays(now(), false);
    }

    public function getDaysSinceIssue(): int
    {
        return $this->issue_date->diffInDays(now());
    }

    public function getConditionsCount(): int
    {
        return count($this->conditions ?? []);
    }

    public function hasConditions(): bool
    {
        return $this->getConditionsCount() > 0;
    }

    public function canBeRenewed(): bool
    {
        return $this->isActive() && $this->isExpiringSoon();
    }

    public function canBeSuspended(): bool
    {
        return $this->isActive();
    }

    public function canBeRevoked(): bool
    {
        return in_array($this->status, ['active', 'suspended']);
    }

    public function canBeReactivated(): bool
    {
        return $this->isSuspended() && !$this->isExpired();
    }

    public function getValidityPeriod(): string
    {
        if (!$this->issue_date || !$this->expiry_date) {
            return 'غير محدد';
        }

        $years = $this->issue_date->diffInYears($this->expiry_date);
        $months = $this->issue_date->diffInMonths($this->expiry_date) % 12;

        $period = '';
        if ($years > 0) {
            $period .= $years . ' سنة ';
        }
        if ($months > 0) {
            $period .= $months . ' شهر ';
        }

        return trim($period) ?: 'أقل من شهر';
    }

    public function getRemainingValidity(): string
    {
        $days = $this->getDaysUntilExpiry();

        if ($days < 0) {
            return 'منتهي';
        }

        if ($days === 0) {
            return 'ينتهي اليوم';
        }

        if ($days <= 30) {
            return $days . ' يوم';
        }

        $months = floor($days / 30);
        $remainingDays = $days % 30;

        $result = $months . ' شهر';
        if ($remainingDays > 0) {
            $result .= ' و ' . $remainingDays . ' يوم';
        }

        return $result;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    public function scopeExpiringSoon($query)
    {
        return $query->where('expiry_date', '<=', now()->addDays(30))
                    ->where('expiry_date', '>', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certification_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeForProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByInspector($query, $inspectorId)
    {
        return $query->where('inspector_id', $inspectorId);
    }
}
