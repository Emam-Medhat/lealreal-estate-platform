<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'lease_id',
        'tenant_id',
        'inspection_number',
        'inspection_type',
        'scheduled_date',
        'completed_date',
        'status',
        'inspector_id',
        'inspection_notes',
        'checklist_items',
        'overall_condition',
        'estimated_damages',
        'photos',
        'videos',
        'recommendations',
        'tenant_comments',
        'tenant_present',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_notes',
        'documents',
        'user_id',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
        'follow_up_date' => 'datetime',
        'checklist_items' => 'array',
        'photos' => 'array',
        'videos' => 'array',
        'documents' => 'array',
        'estimated_damages' => 'decimal:2',
        'tenant_present' => 'boolean',
        'requires_follow_up' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspector_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
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

    public function scopeMoveIn($query)
    {
        return $query->where('inspection_type', 'move_in');
    }

    public function scopeMoveOut($query)
    {
        return $query->where('inspection_type', 'move_out');
    }

    public function scopeRoutine($query)
    {
        return $query->where('inspection_type', 'routine');
    }

    public function scopeEmergency($query)
    {
        return $query->where('inspection_type', 'emergency');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now())
            ->where('scheduled_date', '<=', now()->addDays(7));
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_date', '<', now())
            ->where('status', 'scheduled');
    }

    // Attributes
    public function getIsScheduledAttribute(): bool
    {
        return $this->status === 'scheduled';
    }

    public function getIsInProgressAttribute(): bool
    {
        return $this->status === 'in_progress';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getIsCancelledAttribute(): bool
    {
        return $this->status === 'cancelled';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->scheduled_date < now() && $this->status === 'scheduled';
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->scheduled_date >= now() && $this->scheduled_date <= now()->addDays(7);
    }

    public function getDurationAttribute(): int
    {
        if (!$this->completed_date) return 0;
        return $this->scheduled_date->diffInHours($this->completed_date);
    }

    public function getConditionColorAttribute(): string
    {
        return match($this->overall_condition) {
            'excellent' => 'success',
            'good' => 'info',
            'fair' => 'warning',
            'poor' => 'danger',
            'damaged' => 'dark',
            default => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->inspection_type) {
            'move_in' => 'معاينة الدخول',
            'move_out' => 'معاينة الخروج',
            'routine' => 'معاينة روتينية',
            'emergency' => 'معاينة طارئة',
            'pre_renewal' => 'معاينة قبل التجديد',
            default => $this->inspection_type,
        };
    }

    // Methods
    public function startInspection(): void
    {
        $this->update([
            'status' => 'in_progress',
            'inspector_id' => auth()->id(),
        ]);
    }

    public function completeInspection(array $data): void
    {
        $this->update([
            'status' => 'completed',
            'completed_date' => now(),
            'inspection_notes' => $data['inspection_notes'] ?? null,
            'checklist_items' => $data['checklist_items'] ?? [],
            'overall_condition' => $data['overall_condition'] ?? 'fair',
            'estimated_damages' => $data['estimated_damages'] ?? 0,
            'photos' => $data['photos'] ?? [],
            'videos' => $data['videos'] ?? [],
            'recommendations' => $data['recommendations'] ?? null,
            'tenant_comments' => $data['tenant_comments'] ?? null,
            'tenant_present' => $data['tenant_present'] ?? false,
            'requires_follow_up' => $data['requires_follow_up'] ?? false,
            'follow_up_date' => $data['follow_up_date'] ?? null,
            'follow_up_notes' => $data['follow_up_notes'] ?? null,
            'documents' => $data['documents'] ?? [],
        ]);
    }

    public function cancel(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'inspection_notes' => ($this->inspection_notes ?? '') . "\n\nCancelled: " . $reason . " (" . now()->toDateString() . ")",
        ]);
    }

    public function reschedule(\DateTime $newDate): void
    {
        $this->update([
            'scheduled_date' => $newDate,
            'status' => 'scheduled',
            'inspection_notes' => ($this->inspection_notes ?? '') . "\n\nRescheduled from " . $this->scheduled_date->format('Y-m-d') . " to " . $newDate->format('Y-m-d') . " (" . now()->toDateString() . ")",
        ]);
    }

    public function addPhoto(string $photoUrl): void
    {
        $photos = $this->photos ?? [];
        $photos[] = [
            'url' => $photoUrl,
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id(),
        ];
        
        $this->update(['photos' => $photos]);
    }

    public function addVideo(string $videoUrl): void
    {
        $videos = $this->videos ?? [];
        $videos[] = [
            'url' => $videoUrl,
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id(),
        ];
        
        $this->update(['videos' => $videos]);
    }

    public function addDocument(string $documentUrl, string $documentType): void
    {
        $documents = $this->documents ?? [];
        $documents[] = [
            'url' => $documentUrl,
            'type' => $documentType,
            'added_at' => now()->toISOString(),
            'added_by' => auth()->id(),
        ];
        
        $this->update(['documents' => $documents]);
    }

    public function updateChecklistItem(string $itemId, bool $completed, string $notes = null): void
    {
        $checklist = $this->checklist_items ?? [];
        
        foreach ($checklist as &$item) {
            if ($item['id'] === $itemId) {
                $item['completed'] = $completed;
                $item['notes'] = $notes;
                $item['updated_at'] = now()->toISOString();
                $item['updated_by'] = auth()->id();
                break;
            }
        }
        
        $this->update(['checklist_items' => $checklist]);
    }

    public function generateInspectionNumber(): string
    {
        return 'INS-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'scheduled' => '<span class="badge badge-info">مجدول</span>',
            'in_progress' => '<span class="badge badge-warning">قيد التنفيذ</span>',
            'completed' => '<span class="badge badge-success">مكتمل</span>',
            'cancelled' => '<span class="badge badge-danger">ملغي</span>',
            'rescheduled' => '<span class="badge badge-secondary">تم إعادة الجدولة</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }

    public function getConditionBadge(): string
    {
        return match($this->overall_condition) {
            'excellent' => '<span class="badge badge-success">ممتاز</span>',
            'good' => '<span class="badge badge-info">جيد</span>',
            'fair' => '<span class="badge badge-warning'>متوسط</span>',
            'poor' => '<span class="badge badge-danger">سيء</span>',
            'damaged' => '<span class="badge badge-dark">متضرر</span>',
            default => '<span class="badge badge-secondary">' . $this->overall_condition . '</span>',
        };
    }

    public function getChecklistCompletion(): int
    {
        if (empty($this->checklist_items)) return 0;
        
        $total = count($this->checklist_items);
        $completed = count(array_filter($this->checklist_items, fn($item) => $item['completed'] ?? false));
        
        return $total > 0 ? round(($completed / $total) * 100) : 0;
    }

    public function hasDamages(): bool
    {
        return $this->estimated_damages > 0;
    }

    public function requiresMaintenance(): bool
    {
        return in_array($this->overall_condition, ['poor', 'damaged']) || $this->hasDamages();
    }
}
