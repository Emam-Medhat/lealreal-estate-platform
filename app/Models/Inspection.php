<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'inspector_id',
        'client_id',
        'scheduled_date',
        'inspection_type',
        'priority',
        'status',
        'estimated_duration',
        'estimated_cost',
        'notes',
        'reschedule_reason',
        'rescheduled_at',
        'cancelled_at',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'rescheduled_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(Inspector::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function report(): HasOne
    {
        return $this->hasOne(InspectionReport::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(InspectionPhoto::class);
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

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('inspection_type', $type);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    public function isRescheduled(): bool
    {
        return !is_null($this->rescheduled_at);
    }

    public function isOverdue(): bool
    {
        return $this->scheduled_date->isPast() && $this->status === 'scheduled';
    }

    public function getDurationInHours(): int
    {
        return $this->estimated_duration ?? 60;
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

    public function getTypeLabel(): string
    {
        $labels = [
            'routine' => 'روتيني',
            'detailed' => 'مفصل',
            'pre_sale' => 'قبل البيع',
            'post_repair' => 'بعد الإصلاح',
        ];

        return $labels[$this->inspection_type] ?? $this->inspection_type;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['scheduled']);
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
}
