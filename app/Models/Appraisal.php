<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Appraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'appraiser_id',
        'client_id',
        'appraisal_type',
        'purpose',
        'scheduled_date',
        'priority',
        'status',
        'estimated_cost',
        'assignment_reason',
        'assigned_at',
        'reschedule_reason',
        'rescheduled_at',
        'cancelled_at',
        'started_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'assigned_at' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function appraiser(): BelongsTo
    {
        return $this->belongsTo(Appraiser::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(AppraisalReport::class);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('appraisal_type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function getTypeLabel(): string
    {
        $labels = [
            'market_value' => 'قيمة السوق',
            'insurance' => 'تأمين',
            'tax' => 'ضريبة',
            'refinance' => 'إعادة تمويل',
        ];

        return $labels[$this->appraisal_type] ?? $this->appraisal_type;
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'scheduled' => 'مجدول',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getPriorityLabel(): string
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function isRescheduled(): bool
    {
        return !is_null($this->rescheduled_at);
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date->isPast() && $this->status === 'scheduled';
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress']);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress']);
    }

    public function canStart(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_date->isPast();
    }

    public function canComplete(): bool
    {
        return $this->status === 'in_progress';
    }

    public function hasReport(): bool
    {
        return $this->report()->exists();
    }

    public function getEstimatedValue(): float
    {
        return $this->report?->estimated_value ?? 0;
    }

    public function getValuePerSqm(): float
    {
        return $this->report?->value_per_sqm ?? 0;
    }

    public function getDurationInDays(): int
    {
        if (!$this->started_at || !$this->completed_at) {
            return 0;
        }

        return $this->started_at->diffInDays($this->completed_at);
    }
}
