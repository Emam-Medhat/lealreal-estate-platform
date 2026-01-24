<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NotaryVerification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'notary_id',
        'verification_type',
        'status',
        'requested_by',
        'requested_at',
        'verified_at',
        'verification_code',
        'verification_details',
        'verification_notes',
        'witnesses',
        'documents',
        'additional_requirements',
        'additional_documents',
        'additional_notes',
        'info_provided_at',
        'estimated_completion',
        'notary_seal_path',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'verified_at' => 'datetime',
        'verification_details' => 'array',
        'witnesses' => 'array',
        'documents' => 'array',
        'additional_requirements' => 'array',
        'additional_documents' => 'array',
        'estimated_completion' => 'datetime',
        'info_provided_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function notary()
    {
        return $this->belongsTo(User::class, 'notary_id');
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeRequiresInfo($query)
    {
        return $query->where('status', 'requires_info');
    }

    public function scopeByNotary($query, $notaryId)
    {
        return $query->where('notary_id', $notaryId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('verification_type', $type);
    }

    public function scopeOverdue($query)
    {
        return $query->where('estimated_completion', '<', now())->where('status', 'pending');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function requiresInfo(): bool
    {
        return $this->status === 'requires_info';
    }

    public function isOverdue(): bool
    {
        return $this->estimated_completion && 
               $this->estimated_completion->isPast() && 
               $this->isPending();
    }

    public function getVerificationTypeLabel(): string
    {
        return match($this->verification_type) {
            'standard' => 'قياسي',
            'expedited' => 'معجل',
            'priority' => 'أولوية',
            default => 'غير محدد',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'verified' => 'موثق',
            'rejected' => 'مرفوض',
            'requires_info' => 'مطلوب معلومات',
            default => 'غير محدد',
        };
    }

    public function getFormattedVerificationCode(): string
    {
        return $this->verification_code ?? 'غير متوفر';
    }

    public function getFormattedRequestedAt(): string
    {
        return $this->requested_at ? $this->requested_at->format('Y-m-d H:i') : '';
    }

    public function getFormattedVerifiedAt(): string
    {
        return $this->verified_at ? $this->verified_at->format('Y-m-d H:i') : '';
    }

    public function getFormattedEstimatedCompletion(): string
    {
        return $this->estimated_completion ? $this->estimated_completion->format('Y-m-d H:i') : '';
    }

    public function getTimeUntilCompletion(): string
    {
        if (!$this->estimated_completion) {
            return 'غير محدد';
        }
        
        if ($this->estimated_completion->isPast()) {
            return 'متأخر ' . $this->estimated_completion->diffForHumans(now());
        }
        
        return 'متبقي ' . $this->estimated_completion->diffForHumans(now());
    }

    public function getWitnessesCount(): int
    {
        return count($this->witnesses ?? []);
    }

    public function getDocumentsCount(): int
    {
        return count($this->documents ?? []);
    }

    public function getAdditionalDocumentsCount(): int
    {
        return count($this->additional_documents ?? []);
    }

    public function verify(array $verificationDetails, string $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verification_details' => $verificationDetails,
            'verification_notes' => $notes,
        ]);
    }

    public function reject(string $reason)
    {
        $this->update([
            'status' => 'rejected',
            'verification_notes' => $reason,
        ]);
    }

    public function requestAdditionalInfo(array $requirements)
    {
        $this->update([
            'status' => 'requires_info',
            'additional_requirements' => $requirements,
        ]);
    }

    public function provideAdditionalInfo(array $documents, string $notes = null)
    {
        $this->update([
            'status' => 'pending',
            'additional_documents' => $documents,
            'additional_notes' => $notes,
            'info_provided_at' => now(),
        ]);
    }

    public function canBeVerifiedBy(int $userId): bool
    {
        return $this->notary_id === $userId;
    }

    public function getVerificationSummary(): array
    {
        return [
            'verification_code' => $this->verification_code,
            'verification_type' => $this->verification_type,
            'status' => $this->status,
            'requested_at' => $this->requested_at,
            'verified_at' => $this->verified_at,
            'witnesses_count' => $this->getWitnessesCount(),
            'documents_count' => $this->getDocumentsCount(),
            'verification_details' => $this->verification_details,
        ];
    }

    public function getWitnessInfo(): array
    {
        return $this->witnesses ?? [];
    }

    public function getDocumentInfo(): array
    {
        return $this->documents ?? [];
    }

    public function getAdditionalDocumentInfo(): array
    {
        return $this->additional_documents ?? [];
    }

    public function getVerificationDetails(): array
    {
        return $this->verification_details ?? [];
    }

    public function hasAllRequiredDocuments(): bool
    {
        $requiredDocs = ['id_document', 'contract_copy', 'proof_of_address'];
        $providedDocs = collect($this->documents)->pluck('type')->toArray();
        
        return empty(array_diff($requiredDocs, $providedDocs));
    }

    public function hasValidWitnesses(): bool
    {
        return count($this->witnesses ?? []) >= 2;
    }

    public function isReadyForVerification(): bool
    {
        return $this->isPending() && 
               $this->hasAllRequiredDocuments() && 
               $this->hasValidWitnesses();
    }

    public function generateCertificate(): array
    {
        if (!$this->isVerified()) {
            return [];
        }
        
        return [
            'verification_code' => $this->verification_code,
            'contract_title' => $this->contract->title,
            'contract_number' => $this->contract->contract_number,
            'notary_name' => $this->notary->name,
            'verified_at' => $this->verified_at,
            'witnesses' => $this->witnesses,
            'verification_details' => $this->verification_details,
        ];
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'verification_type_label' => $this->getVerificationTypeLabel(),
            'status_label' => $this->getStatusLabel(),
            'is_overdue' => $this->isOverdue(),
            'time_until_completion' => $this->getTimeUntilCompletion(),
            'witnesses_count' => $this->getWitnessesCount(),
            'documents_count' => $this->getDocumentsCount(),
            'additional_documents_count' => $this->getAdditionalDocumentsCount(),
            'is_ready_for_verification' => $this->isReadyForVerification(),
        ]);
    }
}
