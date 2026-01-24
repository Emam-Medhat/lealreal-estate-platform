<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class LoanDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'loan_id',
        'document_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'mime_type',
        'uploaded_by',
        'status',
        'verification_status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'expiry_date',
        'tags',
        'metadata',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'uploaded_by' => 'integer',
        'verified_by' => 'integer',
        'verified_at' => 'datetime',
        'expiry_date' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array',
        'loan_id' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByVerificationStatus($query, $status)
    {
        return $query->where('verification_status', $status);
    }

    public function scopeByLoan($query, $loanId)
    {
        return $query->where('loan_id', $loanId);
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now());
    }

    // Helper methods
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected(): bool
    {
        return $this->verification_status === 'rejected';
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiring(): bool
    {
        return $this->expiry_date && $this->expiry_date->diffInDays(now()) <= 30;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDocumentTypeDisplayAttribute(): string
    {
        $types = [
            'id_proof' => 'ID Proof',
            'income_proof' => 'Income Proof',
            'employment_proof' => 'Employment Proof',
            'asset_proof' => 'Asset Proof',
            'property_proof' => 'Property Proof',
            'credit_report' => 'Credit Report',
            'bank_statement' => 'Bank Statement',
            'tax_return' => 'Tax Return',
            'insurance_policy' => 'Insurance Policy',
            'appraisal_report' => 'Appraisal Report',
            'title_deed' => 'Title Deed',
            'purchase_agreement' => 'Purchase Agreement',
            'other' => 'Other Document',
        ];

        return $types[$this->document_type] ?? 'Unknown';
    }

    public function getStatusDisplayAttribute(): string
    {
        $statuses = [
            'active' => '🟢 Active',
            'inactive' => '🔴 Inactive',
            'archived' => '⚫ Archived',
            'deleted' => '⚫ Deleted',
        ];

        return $statuses[$this->status] ?? '❓ Unknown';
    }

    public function getVerificationStatusDisplayAttribute(): string
    {
        $statuses = [
            'pending' => '🟡 Pending',
            'verified' => '🟢 Verified',
            'rejected' => '🔴 Rejected',
            'expired' => '⚫ Expired',
        ];

        return $statuses[$this->verification_status] ?? '❓ Unknown';
    }

    public function getFileTypeDisplayAttribute(): string
    {
        $types = [
            'pdf' => 'PDF Document',
            'doc' => 'Word Document',
            'docx' => 'Word Document',
            'jpg' => 'JPEG Image',
            'jpeg' => 'JPEG Image',
            'png' => 'PNG Image',
            'mp4' => 'MP4 Video',
            'mov' => 'MOV Video',
        ];

        return $types[$this->file_type] ?? 'Unknown File';
    }

    public function getFileIconAttribute(): string
    {
        $icons = [
            'pdf' => '📄',
            'doc' => '📝',
            'docx' => '📝',
            'jpg' => '🖼️',
            'jpeg' => '🖼️',
            'png' => '🖼️',
            'mp4' => '🎥',
            'mov' => '🎥',
        ];

        return $icons[$this->file_type] ?? '📎';
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expiry_date) return 0;
        return now()->diffInDays($this->expiry_date, false);
    }

    public function getExpiryStatusAttribute(): string
    {
        $days = $this->getDaysUntilExpiryAttribute();
        
        if ($days < 0) {
            return 'Expired';
        } elseif ($days === 0) {
            return 'Expires today';
        } elseif ($days <= 7) {
            return 'Expires in ' . $days . ' days';
        } elseif ($days <= 30) {
            return 'Expires in ' . $days . ' days';
        } else {
            return 'Valid';
        }
    }

    public function getExpiryColorAttribute(): string
    {
        $days = $this->getDaysUntilExpiryAttribute();
        
        if ($days < 0) {
            return 'text-red-600';
        } elseif ($days <= 7) {
            return 'text-orange-600';
        } elseif ($days <= 30) {
            return 'text-yellow-600';
        } else {
            return 'text-green-600';
        }
    }

    public function getPublicUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('loans.documents.download', $this->id);
    }

    public function getPreviewUrlAttribute(): ?string
    {
        if (in_array($this->file_type, ['jpg', 'jpeg', 'png'])) {
            return $this->getPublicUrlAttribute();
        }
        
        return null;
    }

    public function canBeVerified(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->verification_status === 'pending';
    }

    public function canBeDownloaded(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function canBeDeleted(): bool
    {
        return !$this->isVerified();
    }

    public function getRequiredFieldsAttribute(): array
    {
        $fields = [];
        
        switch ($this->document_type) {
            case 'id_proof':
                $fields = ['full_name', 'document_number', 'expiry_date', 'issuing_country'];
                break;
            case 'income_proof':
                $fields = ['employer_name', 'annual_income', 'employment_status'];
                break;
            case 'property_proof':
                $fields = ['property_address', 'property_value', 'ownership_type'];
                break;
            case 'bank_statement':
                $fields = ['account_number', 'bank_name', 'statement_period'];
                break;
        }
        
        return $fields;
    }

    public function getValidationRulesAttribute(): array
    {
        $rules = [];
        
        switch ($this->document_type) {
            case 'id_proof':
                $rules = [
                    'document_number' => 'required|string|max:50',
                    'expiry_date' => 'required|date|after:today',
                    'issuing_country' => 'required|string|size:2',
                ];
                break;
            case 'income_proof':
                $rules = [
                    'employer_name' => 'required|string|max:255',
                    'annual_income' => 'required|numeric|min:0',
                    'employment_status' => 'required|in:full_time,part_time,self_employed,retired',
                ];
                break;
        }
        
        return $rules;
    }

    public function getComplianceStatusAttribute(): string
    {
        $complianceChecks = $this->getComplianceChecksAttribute();
        $passedChecks = collect($complianceChecks)->where('status', 'Passed')->count();
        $totalChecks = count($complianceChecks);
        
        if ($passedChecks === $totalChecks) {
            return 'Compliant';
        } elseif ($passedChecks > 0) {
            return 'Partially Compliant';
        } else {
            return 'Non-Compliant';
        }
    }

    public function getComplianceColorAttribute(): string
    {
        $colors = [
            'Compliant' => 'text-green-600',
            'Partially Compliant' => 'text-yellow-600',
            'Non-Compliant' => 'text-red-600',
        ];

        return $colors[$this->getComplianceStatusAttribute()] ?? 'text-gray-600';
    }

    public function getComplianceChecksAttribute(): array
    {
        $checks = [];
        
        // Document validity check
        $checks[] = [
            'name' => 'Document Validity',
            'status' => $this->isExpired() ? 'Failed' : 'Passed',
            'description' => 'Document is not expired',
        ];
        
        // File size check
        $checks[] = [
            'name' => 'File Size',
            'status' => $this->file_size <= 10485760 ? 'Passed' : 'Failed', // 10MB limit
            'description' => 'File size within limits',
        ];
        
        // File type check
        $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $checks[] = [
            'name' => 'File Type',
            'status' => in_array($this->file_type, $allowedTypes) ? 'Passed' : 'Failed',
            'description' => 'File type is allowed',
        ];
        
        return $checks;
    }

    public function getDocumentSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->document_type,
            'title' => $this->title,
            'status' => $this->status,
            'verification_status' => $this->verification_status,
            'file_name' => $this->file_name,
            'file_size' => $this->getFileSizeFormattedAttribute(),
            'file_type' => $this->file_type,
            'uploaded_at' => $this->created_at,
            'verified_at' => $this->verified_at,
            'expiry_date' => $this->expiry_date,
            'is_expired' => $this->isExpired(),
            'is_expiring' => $this->isExpiring(),
            'days_until_expiry' => $this->getDaysUntilExpiryAttribute(),
        ];
    }

    public function getAuditTrailAttribute(): array
    {
        $trail = [];
        
        if ($this->created_at) {
            $trail[] = [
                'action' => 'Uploaded',
                'user' => $this->uploader->name ?? 'System',
                'date' => $this->created_at,
                'description' => 'Document uploaded',
            ];
        }
        
        if ($this->verified_at) {
            $trail[] = [
                'action' => 'Verified',
                'user' => $this->verifier->name ?? 'System',
                'date' => $this->verified_at,
                'description' => 'Document verified',
            ];
        }
        
        if ($this->rejection_reason) {
            $trail[] = [
                'action' => 'Rejected',
                'user' => $this->verifier->name ?? 'System',
                'date' => $this->verified_at,
                'description' => 'Document rejected: ' . $this->rejection_reason,
            ];
        }
        
        return $trail;
    }
}
