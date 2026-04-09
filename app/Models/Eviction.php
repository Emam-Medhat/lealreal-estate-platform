<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Eviction extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'tenant_id',
        'property_id',
        'eviction_number',
        'reason',
        'description',
        'status',
        'notice_date',
        'notice_type',
        'court_date',
        'court_order_number',
        'eviction_date',
        'actual_move_out_date',
        'legal_fees',
        'damages',
        'recovery_amount',
        'notes',
        'documents',
        'notice_served',
        'notice_served_date',
        'notice_served_method',
        'court_filing_date',
        'judgment_date',
        'writ_date',
        'sheriff_date',
        'user_id',
    ];

    protected $casts = [
        'notice_date' => 'date',
        'court_date' => 'date',
        'eviction_date' => 'date',
        'actual_move_out_date' => 'date',
        'notice_served_date' => 'datetime',
        'court_filing_date' => 'date',
        'judgment_date' => 'date',
        'writ_date' => 'date',
        'sheriff_date' => 'date',
        'legal_fees' => 'decimal:2',
        'damages' => 'decimal:2',
        'recovery_amount' => 'decimal:2',
        'documents' => 'array',
        'notice_served' => 'boolean',
    ];

    public function lease(): BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNoticeServed($query)
    {
        return $query->where('notice_served', true);
    }

    public function scopeCourtFiled($query)
    {
        return $query->whereNotNull('court_filing_date');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Attributes
    public function getIsPendingAttribute(): bool
    {
        return $this->status === 'pending';
    }

    public function getIsActiveAttribute(): bool
    {
        return in_array($this->status, ['pending', 'notice_served', 'court_filed', 'judgment']);
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }

    public function getDaysSinceNoticeAttribute(): int
    {
        if (!$this->notice_date) return 0;
        return now()->diffInDays($this->notice_date);
    }

    public function getTotalCostsAttribute(): float
    {
        return $this->legal_fees + $this->damages;
    }

    public function getNetRecoveryAttribute(): float
    {
        return $this->recovery_amount - $this->total_costs;
    }

    public function getCurrentStageAttribute(): string
    {
        if ($this->status === 'completed') return 'مكتمل';
        if ($this->actual_move_out_date) return 'تم الإخلاء';
        if ($this->sheriff_date) return 'تنفيذ الحكم';
        if ($this->writ_date) return 'أمر تنفيذ';
        if ($this->judgment_date) return 'صدر حكم';
        if ($this->court_filing_date) return 'مرفوع للمحكمة';
        if ($this->notice_served) return 'تم إبلاغ الإشعار';
        if ($this->notice_date) return 'صدر إشعار';
        return 'معلق';
    }

    // Methods
    public function serveNotice(string $method): void
    {
        $this->update([
            'notice_served' => true,
            'notice_served_date' => now(),
            'notice_served_method' => $method,
            'status' => 'notice_served',
        ]);
    }

    public function fileCourt(): void
    {
        $this->update([
            'court_filing_date' => now(),
            'status' => 'court_filed',
        ]);
    }

    public function recordJudgment(string $orderNumber): void
    {
        $this->update([
            'judgment_date' => now(),
            'court_order_number' => $orderNumber,
            'status' => 'judgment',
        ]);
    }

    public function issueWrit(): void
    {
        $this->update([
            'writ_date' => now(),
            'status' => 'writ_issued',
        ]);
    }

    public function scheduleSheriff(): void
    {
        $this->update([
            'sheriff_date' => now(),
            'status' => 'sheriff_scheduled',
        ]);
    }

    public function completeEviction(?Carbon $moveOutDate = null): void
    {
        $this->update([
            'actual_move_out_date' => $moveOutDate ?? now(),
            'status' => 'completed',
        ]);

        // Update lease status
        if ($this->lease) {
            $this->lease->terminate('Eviction completed', 'Eviction #' . $this->eviction_number);
        }
    }

    public function cancelEviction(string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'notes' => $this->notes . "\n\nCancelled: " . $reason . " (" . now()->toDateString() . ")",
        ]);
    }

    public function canBeServed(): bool
    {
        return $this->status === 'pending' && $this->notice_date;
    }

    public function canBeFiled(): bool
    {
        return $this->notice_served && !$this->court_filing_date;
    }

    public function canBeCompleted(): bool
    {
        return in_array($this->status, ['judgment', 'writ_issued', 'sheriff_scheduled']);
    }

    public function getStatusBadge(): string
    {
        return match($this->status) {
            'pending' => '<span class="badge badge-warning">معلق</span>',
            'notice_served' => '<span class="badge badge-info">تم إبلاغ الإشعار</span>',
            'court_filed' => '<span class="badge badge-primary">مرفوع للمحكمة</span>',
            'judgment' => '<span class="badge badge-success">صدر حكم</span>',
            'writ_issued' => '<span class="badge badge-info">أمر تنفيذ</span>',
            'sheriff_scheduled' => '<span class="badge badge-warning">تم جدول التنفيذ</span>',
            'completed' => '<span class="badge badge-success">مكتمل</span>',
            'cancelled' => '<span class="badge badge-danger">ملغي</span>',
            default => '<span class="badge badge-secondary">' . $this->status . '</span>',
        };
    }

    public function generateEvictionNumber(): string
    {
        return 'EV-' . date('Y') . '-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
