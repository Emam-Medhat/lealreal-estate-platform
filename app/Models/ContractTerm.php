<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractTerm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'title',
        'content',
        'order',
        'is_required',
        'is_proposed',
        'proposed_by',
        'proposed_at',
        'original_content',
        'proposed_content',
        'change_status',
        'change_reason',
        'changed_by',
        'changed_at',
        'counter_proposed_by',
        'counter_proposed_at',
        'counter_notes',
        'accepted_by',
        'accepted_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'proposed_removal',
        'removal_reason',
        'change_requested_by',
        'change_requested_at',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_proposed' => 'boolean',
        'proposed_removal' => 'boolean',
        'proposed_at' => 'datetime',
        'changed_at' => 'datetime',
        'counter_proposed_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',
        'change_requested_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function proposedBy()
    {
        return $this->belongsTo(User::class, 'proposed_by');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function counterProposedBy()
    {
        return $this->belongsTo(User::class, 'counter_proposed_by');
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function changeRequestedBy()
    {
        return $this->belongsTo(User::class, 'change_requested_by');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOptional($query)
    {
        return $query->where('is_required', false);
    }

    public function scopeProposed($query)
    {
        return $query->where('is_proposed', true);
    }

    public function scopeWithChanges($query)
    {
        return $query->whereNotNull('change_status');
    }

    public function isModified(): bool
    {
        return !is_null($this->change_status);
    }

    public function isProposed(): bool
    {
        return $this->is_proposed;
    }

    public function isRequired(): bool
    {
        return $this->is_required;
    }

    public function hasProposedContent(): bool
    {
        return !is_null($this->proposed_content);
    }

    public function isProposedForRemoval(): bool
    {
        return $this->proposed_removal;
    }

    public function isAccepted(): bool
    {
        return $this->change_status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->change_status === 'rejected';
    }

    public function isCounterProposed(): bool
    {
        return $this->change_status === 'counter_proposed';
    }

    public function isChangeRequested(): bool
    {
        return $this->change_status === 'approval_requested';
    }

    public function getChangeStatusLabel(): string
    {
        return match($this->change_status) {
            'proposed' => 'مقترح',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
            'counter_proposed' => 'مقترح مضاد',
            'approval_requested' => 'مطلوب موافقة',
            default => 'بدون تغيير',
        };
    }

    public function getCurrentContent(): string
    {
        return $this->proposed_content ?? $this->content;
    }

    public function getDisplayContent(): string
    {
        if ($this->isProposedForRemoval()) {
            return '[مقترح للحذف: ' . $this->content . ']';
        }
        
        if ($this->hasProposedContent()) {
            return $this->proposed_content;
        }
        
        return $this->content;
    }

    public function proposeChange(string $newContent, string $reason, int $userId)
    {
        $this->update([
            'original_content' => $this->content,
            'proposed_content' => $newContent,
            'change_status' => 'proposed',
            'change_reason' => $reason,
            'changed_by' => $userId,
            'changed_at' => now(),
        ]);
    }

    public function acceptChange(int $userId)
    {
        if ($this->isProposedForRemoval()) {
            $this->delete();
            return;
        }
        
        $this->update([
            'content' => $this->proposed_content,
            'proposed_content' => null,
            'original_content' => null,
            'change_status' => 'accepted',
            'accepted_by' => $userId,
            'accepted_at' => now(),
        ]);
    }

    public function rejectChange(string $reason, int $userId)
    {
        $this->update([
            'proposed_content' => null,
            'original_content' => null,
            'change_status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_by' => $userId,
            'rejected_at' => now(),
        ]);
    }

    public function counterPropose(string $counterContent, string $notes, int $userId)
    {
        $this->update([
            'proposed_content' => $counterContent,
            'change_status' => 'counter_proposed',
            'counter_proposed_by' => $userId,
            'counter_proposed_at' => now(),
            'counter_notes' => $notes,
        ]);
    }

    public function requestChange(string $change, int $userId)
    {
        $this->update([
            'proposed_content' => $change,
            'change_status' => 'approval_requested',
            'change_requested_by' => $userId,
            'change_requested_at' => now(),
        ]);
    }

    public function proposeRemoval(string $reason, int $userId)
    {
        $this->update([
            'proposed_removal' => true,
            'removal_reason' => $reason,
            'proposed_by' => $userId,
            'proposed_at' => now(),
            'change_status' => 'proposed',
        ]);
    }

    public function resetChanges()
    {
        $this->update([
            'original_content' => null,
            'proposed_content' => null,
            'change_status' => null,
            'change_reason' => null,
            'changed_by' => null,
            'changed_at' => null,
            'counter_proposed_by' => null,
            'counter_proposed_at' => null,
            'counter_notes' => null,
            'accepted_by' => null,
            'accepted_at' => null,
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'proposed_removal' => false,
            'removal_reason' => null,
            'change_requested_by' => null,
            'change_requested_at' => null,
        ]);
    }

    public function getChangeHistory(): array
    {
        return [
            'original_content' => $this->original_content,
            'proposed_content' => $this->proposed_content,
            'change_status' => $this->change_status,
            'change_reason' => $this->change_reason,
            'changed_by' => $this->changedBy?->name,
            'changed_at' => $this->changed_at,
            'counter_proposed_by' => $this->counterProposedBy?->name,
            'counter_proposed_at' => $this->counter_proposed_at,
            'counter_notes' => $this->counter_notes,
            'accepted_by' => $this->acceptedBy?->name,
            'accepted_at' => $this->accepted_at,
            'rejected_by' => $this->rejectedBy?->name,
            'rejected_at' => $this->rejected_at,
            'rejection_reason' => $this->rejection_reason,
        ];
    }
}
