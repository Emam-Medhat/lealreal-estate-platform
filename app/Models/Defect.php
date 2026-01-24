<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Defect extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_report_id',
        'description',
        'severity',
        'location',
        'estimated_cost',
        'urgency',
        'category',
        'status',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'assignment_notes',
        'notes',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'assigned_at' => 'datetime',
    ];

    public function inspectionReport(): BelongsTo
    {
        return $this->belongsTo(InspectionReport::class);
    }

    public function repairEstimate(): HasOne
    {
        return $this->hasOne(RepairEstimate::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(DefectPhoto::class);
    }

    public function getSeverityLabel(): string
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'critical' => 'حرج',
        ];

        return $labels[$this->severity] ?? $this->severity;
    }

    public function getSeverityColor(): string
    {
        $colors = [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
        ];

        return $colors[$this->severity] ?? 'secondary';
    }

    public function getUrgencyLabel(): string
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return $labels[$this->urgency] ?? $this->urgency;
    }

    public function getUrgencyColor(): string
    {
        $colors = [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
        ];

        return $colors[$this->urgency] ?? 'secondary';
    }

    public function getCategoryLabel(): string
    {
        $labels = [
            'structural' => 'إنشائي',
            'electrical' => 'كهربائي',
            'plumbing' => 'سباكة',
            'hvac' => 'تكييف',
            'interior' => 'داخلي',
            'exterior' => 'خارجي',
            'safety' => 'سلامة',
            'other' => 'أخرى',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'في انتظار',
            'in_progress' => 'قيد التنفيذ',
            'completed' => 'مكتمل',
            'deferred' => 'مؤجل',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'pending' => 'warning',
            'in_progress' => 'info',
            'completed' => 'success',
            'deferred' => 'secondary',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isUrgent(): bool
    {
        return $this->urgency === 'urgent';
    }

    public function isAssigned(): bool
    {
        return !is_null($this->assigned_to);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeAssigned(): bool
    {
        return !$this->isAssigned() && !$this->isCompleted();
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'in_progress';
    }

    public function hasRepairEstimate(): bool
    {
        return $this->repairEstimate()->exists();
    }

    public function getRepairCost(): float
    {
        return $this->repairEstimate?->estimated_cost ?? $this->estimated_cost ?? 0;
    }

    public function getPriorityScore(): int
    {
        $score = 0;

        // Severity scoring
        switch ($this->severity) {
            case 'critical':
                $score += 40;
                break;
            case 'high':
                $score += 30;
                break;
            case 'medium':
                $score += 20;
                break;
            case 'low':
                $score += 10;
                break;
        }

        // Urgency scoring
        switch ($this->urgency) {
            case 'urgent':
                $score += 30;
                break;
            case 'high':
                $score += 20;
                break;
            case 'medium':
                $score += 10;
                break;
        }

        // Category scoring
        switch ($this->category) {
            case 'structural':
                $score += 20;
                break;
            case 'safety':
                $score += 15;
                break;
            case 'electrical':
                $score += 10;
                break;
            case 'plumbing':
                $score += 8;
                break;
        }

        return $score;
    }

    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency', $urgency);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    public function scopeUrgent($query)
    {
        return $query->where('urgency', 'urgent');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }
}
