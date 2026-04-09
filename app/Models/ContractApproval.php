<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'approver_id',
        'approval_type',
        'approval_order',
        'status',
        'requested_by',
        'requested_at',
        'deadline',
        'decision_at',
        'decision',
        'comments',
        'requested_changes',
        'delegated_to',
        'delegated_by',
        'delegated_at',
        'delegation_reason',
        'escalated_to',
        'escalated_by',
        'escalated_at',
        'escalation_reason',
        'parent_approval_id',
        'notes',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'deadline' => 'datetime',
        'decision_at' => 'datetime',
        'delegated_at' => 'datetime',
        'escalated_at' => 'datetime',
        'requested_changes' => 'array',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function delegatedTo()
    {
        return $this->belongsTo(User::class, 'delegated_to');
    }

    public function delegatedBy()
    {
        return $this->belongsTo(User::class, 'delegated_by');
    }

    public function escalatedTo()
    {
        return $this->belongsTo(User::class, 'escalated_to');
    }

    public function escalatedBy()
    {
        return $this->belongsTo(User::class, 'escalated_by');
    }

    public function parentApproval()
    {
        return $this->belongsTo(ContractApproval::class, 'parent_approval_id');
    }

    public function childApprovals()
    {
        return $this->hasMany(ContractApproval::class, 'parent_approval_id');
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

    public function scopeRequiresChanges($query)
    {
        return $query->where('status', 'changes_requested');
    }

    public function scopeByApprover($query, $userId)
    {
        return $query->where('approver_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())->where('status', 'pending');
    }

    public function scopeSequential($query)
    {
        return $query->where('approval_type', 'sequential');
    }

    public function scopeParallel($query)
    {
        return $query->where('approval_type', 'parallel');
    }

    public function scopeEscalation($query)
    {
        return $query->where('approval_type', 'escalation');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function requiresChanges(): bool
    {
        return $this->status === 'changes_requested';
    }

    public function isOverdue(): bool
    {
        return $this->deadline && $this->deadline->isPast() && $this->isPending();
    }

    public function isDelegated(): bool
    {
        return !is_null($this->delegated_to);
    }

    public function isEscalated(): bool
    {
        return !is_null($this->escalated_to);
    }

    public function isSequential(): bool
    {
        return $this->approval_type === 'sequential';
    }

    public function isParallel(): bool
    {
        return $this->approval_type === 'parallel';
    }

    public function isEscalation(): bool
    {
        return $this->approval_type === 'escalation';
    }

    public function getApprovalTypeLabel(): string
    {
        return match($this->approval_type) {
            'sequential' => 'تسلسلي',
            'parallel' => 'متوازٍ',
            'escalation' => 'تصعيد',
            default => 'غير محدد',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'approved' => 'موافق',
            'rejected' => 'مرفوض',
            'changes_requested' => 'مطلوب تعديلات',
            'delegated' => 'مفوض',
            'escalated' => 'مُصعّد',
            default => 'غير محدد',
        };
    }

    public function getDecisionLabel(): string
    {
        return match($this->decision) {
            'approve' => 'موافقة',
            'reject' => 'رفض',
            'request_changes' => 'طلب تعديلات',
            default => 'غير محدد',
        };
    }

    public function getFormattedDeadline(): string
    {
        return $this->deadline ? $this->deadline->format('Y-m-d H:i') : '';
    }

    public function getFormattedDecisionAt(): string
    {
        return $this->decision_at ? $this->decision_at->format('Y-m-d H:i') : '';
    }

    public function getTimeUntilDeadline(): string
    {
        if (!$this->deadline) {
            return 'غير محدد';
        }
        
        if ($this->deadline->isPast()) {
            return 'متأخر ' . $this->deadline->diffForHumans(now());
        }
        
        return 'متبقي ' . $this->deadline->diffForHumans(now());
    }

    public function approve(string $comments = null, array $requestedChanges = null)
    {
        $this->update([
            'status' => 'approved',
            'decision' => 'approve',
            'decision_at' => now(),
            'comments' => $comments,
            'requested_changes' => $requestedChanges,
        ]);
    }

    public function reject(string $comments = null)
    {
        $this->update([
            'status' => 'rejected',
            'decision' => 'reject',
            'decision_at' => now(),
            'comments' => $comments,
        ]);
    }

    public function requestChanges(string $comments = null, array $requestedChanges = null)
    {
        $this->update([
            'status' => 'changes_requested',
            'decision' => 'request_changes',
            'decision_at' => now(),
            'comments' => $comments,
            'requested_changes' => $requestedChanges,
        ]);
    }

    public function delegate(int $toUserId, string $reason)
    {
        $this->update([
            'status' => 'delegated',
            'delegated_to' => $toUserId,
            'delegated_by' => auth()->id(),
            'delegated_at' => now(),
            'delegation_reason' => $reason,
        ]);
    }

    public function escalate(int $toUserId, string $reason)
    {
        $this->update([
            'status' => 'escalated',
            'escalated_to' => $toUserId,
            'escalated_by' => auth()->id(),
            'escalated_at' => now(),
            'escalation_reason' => $reason,
        ]);
    }

    public function reset()
    {
        $this->update([
            'status' => 'pending',
            'decision_at' => null,
            'decision' => null,
            'comments' => null,
            'requested_changes' => null,
        ]);
    }

    public function canBeDecidedBy(int $userId): bool
    {
        return $this->approver_id === $userId || 
               $this->delegated_to === $userId || 
               $this->escalated_to === $userId;
    }

    public function getEffectiveApprover()
    {
        return $this->escalated_to ?? $this->delegated_to ?? $this->approver;
    }

    public function getApprovalChain(): array
    {
        $chain = [];
        $approval = $this;
        
        while ($approval) {
            $chain[] = [
                'approver_id' => $approval->approver_id,
                'approval_type' => $approval->approval_type,
                'status' => $approval->status,
                'decision_at' => $approval->decision_at,
            ];
            
            $approval = $approval->parentApproval;
        }
        
        return array_reverse($chain);
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'approval_type_label' => $this->getApprovalTypeLabel(),
            'status_label' => $this->getStatusLabel(),
            'decision_label' => $this->getDecisionLabel(),
            'is_overdue' => $this->isOverdue(),
            'time_until_deadline' => $this->getTimeUntilDeadline(),
            'effective_approver' => $this->getEffectiveApprover(),
        ]);
    }
}
