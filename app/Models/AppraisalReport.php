<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppraisalReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'appraisal_id',
        'appraiser_id',
        'estimated_value',
        'value_per_sqm',
        'market_analysis',
        'property_condition',
        'comparable_properties',
        'adjustments',
        'conclusion',
        'recommendations',
        'report_date',
        'status',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'comparable_properties' => 'array',
        'adjustments' => 'array',
        'estimated_value' => 'decimal:2',
        'value_per_sqm' => 'decimal:2',
        'report_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function appraisal(): BelongsTo
    {
        return $this->belongsTo(Appraisal::class);
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(AppraisalReportPhoto::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AppraisalReportAttachment::class);
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'في انتظار المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function getComparableCount(): int
    {
        return count($this->comparable_properties ?? []);
    }

    public function getAdjustmentCount(): int
    {
        return count($this->adjustments ?? []);
    }

    public function getTotalAdjustmentAmount(): float
    {
        $total = 0;
        foreach ($this->adjustments ?? [] as $adjustment) {
            $total += $adjustment['amount'] ?? 0;
        }
        return $total;
    }

    public function getAdjustedValue(): float
    {
        return $this->estimated_value + $this->getTotalAdjustmentAmount();
    }

    public function hasEnoughComparables(): bool
    {
        return $this->getComparableCount() >= 3;
    }

    public function getConfidenceLevel(): string
    {
        $comparableCount = $this->getComparableCount();
        
        if ($comparableCount >= 5) return 'عالي';
        if ($comparableCount >= 3) return 'متوسط';
        return 'منخفض';
    }

    public function getConfidenceColor(): string
    {
        $comparableCount = $this->getComparableCount();
        
        if ($comparableCount >= 5) return 'success';
        if ($comparableCount >= 3) return 'warning';
        return 'danger';
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('report_date', [$startDate, $endDate]);
    }
}
