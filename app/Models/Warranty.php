<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warranty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warranty_code',
        'warranty_number',
        'property_id',
        'service_provider_id',
        'warranty_type',
        'title',
        'description',
        'coverage_details',
        'start_date',
        'end_date',
        'duration_months',
        'coverage_amount',
        'deductible_amount',
        'terms_conditions',
        'contact_person',
        'contact_phone',
        'contact_email',
        'status',
        'notes',
        'attachments',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'duration_months' => 'integer',
        'coverage_amount' => 'decimal:2',
        'deductible_amount' => 'decimal:2',
        'attachments' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function serviceProvider()
    {
        return $this->belongsTo(ServiceProvider::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function claims()
    {
        return $this->hasMany(WarrantyClaim::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('warranty_type', $type);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('end_date', '>', now())
                    ->where('end_date', '<=', now()->addDays($days))
                    ->where('status', 'active');
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_date', '<', now())
                    ->where('status', '!=', 'expired');
    }

    public function getWarrantyTypeLabelAttribute()
    {
        $labels = [
            'product' => 'منتج',
            'labor' => 'عمالة',
            'combined' => 'مجمع',
            'extended' => 'ممدد',
        ];

        return $labels[$this->warranty_type] ?? $this->warranty_type;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'نشط',
            'expired' => 'منتهي',
            'suspended' => 'موقف',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'active' => 'green',
            'expired' => 'red',
            'suspended' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function isExpired()
    {
        return $this->status === 'expired' || $this->end_date < now();
    }

    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    public function isExpiringSoon($days = 30)
    {
        return $this->isActive() && $this->end_date <= now()->addDays($days);
    }

    public function canBeExtended()
    {
        return $this->status === 'active';
    }

    public function canBeSuspended()
    {
        return $this->status === 'active';
    }

    public function canBeReactivated()
    {
        return $this->status === 'suspended';
    }

    public function canBeExpired()
    {
        return $this->status === 'active';
    }

    public function getDaysRemaining()
    {
        if ($this->end_date) {
            return $this->end_date->diffInDays(now(), false);
        }

        return null;
    }

    public function getDaysActive()
    {
        if ($this->start_date) {
            return $this->start_date->diffInDays(now());
        }

        return null;
    }

    public function getPercentageComplete()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $elapsedDays = $this->start_date->diffInDays(now());

        if ($elapsedDays >= $totalDays) {
            return 100;
        }

        return ($elapsedDays / $totalDays) * 100;
    }

    public function getTotalClaims()
    {
        return $this->claims()->count();
    }

    public function getApprovedClaims()
    {
        return $this->claims()->where('status', 'approved')->count();
    }

    public function getRejectedClaims()
    {
        return $this->claims()->where('status', 'rejected')->count();
    }

    public function getPendingClaims()
    {
        return $this->claims()->where('status', 'pending')->count();
    }

    public function getTotalClaimedAmount()
    {
        return $this->claims()->where('status', 'approved')->sum('amount');
    }

    public function getRemainingCoverage()
    {
        return $this->coverage_amount - $this->getTotalClaimedAmount();
    }

    public function getCoverageUtilization()
    {
        if ($this->coverage_amount == 0) {
            return 0;
        }

        return ($this->getTotalClaimedAmount() / $this->coverage_amount) * 100;
    }

    public function isCoverageExhausted()
    {
        return $this->getRemainingCoverage() <= 0;
    }

    public function canMakeClaim($amount)
    {
        return $this->isActive() && !$this->isCoverageExhausted() && 
               ($this->getRemainingCoverage() >= $amount);
    }

    public function createClaim($claimNumber, $description, $amount, $claimDate, $incidentDate, $userId = null)
    {
        if (!$this->canMakeClaim($amount)) {
            return null;
        }

        return $this->claims()->create([
            'claim_number' => $claimNumber,
            'description' => $description,
            'amount' => $amount,
            'claim_date' => $claimDate,
            'incident_date' => $incidentDate,
            'status' => 'pending',
            'created_by' => $userId ?? auth()->id(),
        ]);
    }

    public function extend($months, $cost = null, $notes = null, $extendedBy)
    {
        $newEndDate = $this->end_date->addMonths($months);
        $newDuration = $this->duration_months + $months;

        $this->update([
            'end_date' => $newEndDate,
            'duration_months' => $newDuration,
            'extension_cost' => $cost,
            'extended_at' => now(),
            'extended_by' => $extendedBy,
            'extension_notes' => $notes,
        ]);

        return $this;
    }

    public function suspend($reason, $suspendedBy)
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'suspended_by' => $suspendedBy,
            'suspension_reason' => $reason,
        ]);

        return $this;
    }

    public function reactivate($reactivatedBy, $newEndDate = null)
    {
        $updateData = [
            'status' => 'active',
            'reactivated_at' => now(),
            'reactivated_by' => $reactivatedBy,
        ];

        if ($newEndDate) {
            $updateData['end_date'] = $newEndDate;
        }

        $this->update($updateData);

        return $this;
    }

    public function expire($reason, $expiredBy)
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
            'expired_by' => $expiredBy,
            'expiry_reason' => $reason,
        ]);

        return $this;
    }

    public function getClaimHistory()
    {
        return $this->claims()
            ->with('createdBy')
            ->orderBy('claim_date', 'desc')
            ->get();
    }

    public function getRecentClaims($limit = 5)
    {
        return $this->claims()
            ->with('createdBy')
            ->latest('claim_date')
            ->take($limit)
            ->get();
    }

    public function getClaimStatistics()
    {
        return [
            'total_claims' => $this->getTotalClaims(),
            'approved_claims' => $this->getApprovedClaims(),
            'rejected_claims' => $this->getRejectedClaims(),
            'pending_claims' => $this->getPendingClaims(),
            'total_claimed_amount' => $this->getTotalClaimedAmount(),
            'remaining_coverage' => $this->getRemainingCoverage(),
            'coverage_utilization' => $this->getCoverageUtilization(),
            'approval_rate' => $this->getTotalClaims() > 0 ? 
                ($this->getApprovedClaims() / $this->getTotalClaims()) * 100 : 0,
        ];
    }

    public function getWarrantySummary()
    {
        return [
            'warranty_code' => $this->warranty_code,
            'warranty_number' => $this->warranty_number,
            'property' => $this->property->title ?? 'N/A',
            'service_provider' => $this->serviceProvider->name ?? 'N/A',
            'warranty_type' => $this->warranty_type_label,
            'title' => $this->title,
            'status' => $this->status_label,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_months' => $this->duration_months,
            'coverage_amount' => $this->coverage_amount,
            'remaining_coverage' => $this->getRemainingCoverage(),
            'days_remaining' => $this->getDaysRemaining(),
            'is_expiring_soon' => $this->isExpiringSoon(),
            'claim_stats' => $this->getClaimStatistics(),
        ];
    }

    public function getAttachmentCount()
    {
        return count($this->attachments ?? []);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function addAttachment($attachment)
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = $attachment;
        $this->update(['attachments' => $attachments]);
    }

    public function removeAttachment($index)
    {
        $attachments = $this->attachments ?? [];
        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $this->update(['attachments' => array_values($attachments)]);
        }
    }

    public function getCoveragePeriod()
    {
        return [
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_months' => $this->duration_months,
            'days_remaining' => $this->getDaysRemaining(),
            'percentage_complete' => $this->getPercentageComplete(),
        ];
    }

    public function isValidClaimDate($incidentDate)
    {
        return $this->start_date && 
               $this->end_date && 
               $incidentDate >= $this->start_date && 
               $incidentDate <= $this->end_date;
    }

    public function getWarrantyDetails()
    {
        return [
            'basic_info' => [
                'warranty_code' => $this->warranty_code,
                'warranty_number' => $this->warranty_number,
                'title' => $this->title,
                'description' => $this->description,
                'warranty_type' => $this->warranty_type_label,
                'status' => $this->status_label,
            ],
            'coverage_info' => [
                'coverage_amount' => $this->coverage_amount,
                'deductible_amount' => $this->deductible_amount,
                'remaining_coverage' => $this->getRemainingCoverage(),
                'coverage_details' => $this->coverage_details,
                'terms_conditions' => $this->terms_conditions,
            ],
            'period_info' => $this->getCoveragePeriod(),
            'contact_info' => [
                'contact_person' => $this->contact_person,
                'contact_phone' => $this->contact_phone,
                'contact_email' => $this->contact_email,
            ],
            'claim_stats' => $this->getClaimStatistics(),
        ];
    }
}
