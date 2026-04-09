<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core Identifiers
        'document_number',
        'title',
        'description',
        'type', // legal, financial, property, contract, identification, insurance, tax, other
        'category', // primary, secondary, supporting, archived
        'classification', // public, confidential, restricted, classified
        
        // Document Information
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'mime_type',
        'extension',
        'checksum',
        'version',
        
        // Owner and Access
        'user_id',
        'client_id',
        'agent_id',
        'company_id',
        'property_id',
        'contract_id',
        'transaction_id',
        'category_id', // Legacy field for backward compatibility
        'filename', // Legacy field
        'original_filename', // Legacy field
        
        // Document Status
        'status', // draft, pending, approved, rejected, expired, archived
        'approval_status', // pending, approved, rejected
        'approval_required',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        
        // Security and Permissions
        'access_level', // public, internal, restricted, confidential
        'permissions',
        'allowed_users',
        'allowed_roles',
        'expiry_date',
        'expires_at', // Legacy field
        'password_protected',
        'password',
        
        // Document Details
        'language',
        'pages_count',
        'keywords',
        'tags',
        'summary',
        'content',
        'metadata',
        
        // Legal Information
        'legal_type', // deed, contract, agreement, lease, mortgage, title, permit
        'jurisdiction',
        'effective_date',
        'expiration_date',
        'signing_date',
        'execution_date',
        'notary_public',
        'witnesses',
        
        // Financial Information
        'currency',
        'amount',
        'tax_amount',
        'fees',
        'payment_terms',
        'payment_status',
        
        // Property Information (if applicable)
        'property_address',
        'parcel_number',
        'registration_number',
        'title_number',
        'deed_number',
        
        // Digital Information
        'digital_signature',
        'electronic_seal',
        'timestamp',
        'blockchain_hash',
        'verification_status',
        
        // Storage and Backup
        'storage_location',
        'backup_location',
        'cloud_storage',
        'local_storage',
        'archive_location',
        
        // Workflow
        'workflow_stage', // creation, review, approval, signing, execution, archiving
        'current_stage',
        'next_stage',
        'stage_deadline',
        'assigned_to',
        
        // Notifications and Reminders
        'notification_enabled',
        'reminder_enabled',
        'reminder_date',
        'reminder_frequency',
        'notification_recipients',
        
        // Audit and Compliance
        'compliance_status',
        'compliance_notes',
        'audit_trail',
        'retention_period',
        'disposal_date',
        
        // Integration
        'external_system_id',
        'integration_type',
        'sync_status',
        'last_synced_at',
        
        // Notes and Metadata
        'notes',
        'internal_notes',
        'comments',
        'custom_fields',
        
        // Legacy fields for backward compatibility
        'is_confidential',
        
        // Audit
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'pages_count' => 'integer',
        'amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'effective_date' => 'datetime',
        'expiration_date' => 'datetime',
        'signing_date' => 'datetime',
        'execution_date' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'expiry_date' => 'datetime',
        'expires_at' => 'date', // Legacy field
        'reminder_date' => 'datetime',
        'disposal_date' => 'datetime',
        'last_synced_at' => 'datetime',
        'password_protected' => 'boolean',
        'digital_signature' => 'boolean',
        'electronic_seal' => 'boolean',
        'approval_required' => 'boolean',
        'notification_enabled' => 'boolean',
        'reminder_enabled' => 'boolean',
        'is_confidential' => 'boolean', // Legacy field
        'witnesses' => 'array',
        'permissions' => 'array',
        'allowed_users' => 'array',
        'allowed_roles' => 'array',
        'keywords' => 'array',
        'tags' => 'array',
        'content' => 'array',
        'metadata' => 'json',
        'audit_trail' => 'array',
        'custom_fields' => 'json',
        'comments' => 'json',
        'notification_recipients' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DocumentCategory::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(PropertyTransaction::class);
    }

    // Document Relationships
    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class);
    }

    public function signatures(): HasMany
    {
        return $this->hasMany(DocumentSignature::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(DocumentApproval::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(DocumentComment::class);
    }

    // Audit Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(DocumentAccessLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeExpired($query)
    {
        return $query->where('expiration_date', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByAccessLevel($query, $level)
    {
        return $query->where('access_level', $level);
    }

    public function scopeByOwner($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%")
              ->orWhereJsonContains('tags', $term);
        });
    }

    // Helper Methods
    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date && $this->expiration_date->isPast();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiration_date && 
               $this->expiration_date->diffInDays(now()) <= 30;
    }

    public function getIsPasswordProtectedAttribute(): bool
    {
        return $this->password_protected && !empty($this->password);
    }

    public function getRequiresApprovalAttribute(): bool
    {
        return $this->approval_required && $this->approval_status === 'pending';
    }

    public function getIsAccessibleByUser(User $user): bool
    {
        // Owner can always access
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check user permissions
        if (in_array($user->id, $this->allowed_users ?? [])) {
            return true;
        }

        // Check role permissions
        if ($user->hasAnyRole($this->allowed_roles ?? [])) {
            return true;
        }

        // Check access level
        if ($this->access_level === 'public') {
            return true;
        }

        return false;
    }

    public function getFileUrlAttribute(): string
    {
        return asset('storage/documents/' . $this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        return route('documents.download', $this->id);
    }

    public function getPreviewUrlAttribute(): string
    {
        return route('documents.preview', $this->id);
    }

    public function getTypeLabelAttribute(): string
    {
        $types = [
            'legal' => 'Legal',
            'financial' => 'Financial',
            'property' => 'Property',
            'contract' => 'Contract',
            'identification' => 'Identification',
            'insurance' => 'Insurance',
            'tax' => 'Tax',
            'other' => 'Other',
        ];

        return $types[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusLabelAttribute(): string
    {
        $statuses = [
            'draft' => 'Draft',
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            'archived' => 'Archived',
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getAccessLevelLabelAttribute(): string
    {
        $levels = [
            'public' => 'Public',
            'internal' => 'Internal',
            'restricted' => 'Restricted',
            'confidential' => 'Confidential',
        ];

        return $levels[$this->access_level] ?? ucfirst($this->access_level);
    }

    // Business Logic Methods
    public function approveDocument(User $approver, string $notes = ''): bool
    {
        return $this->update([
            'approval_status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'notes' => $notes,
        ]);
    }

    public function rejectDocument(User $rejecter, string $reason): bool
    {
        return $this->update([
            'approval_status' => 'rejected',
            'rejected_by' => $rejecter->id,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    public function archiveDocument(): bool
    {
        return $this->update([
            'status' => 'archived',
            'archived_at' => now(),
        ]);
    }

    public function expireDocument(): bool
    {
        return $this->update([
            'status' => 'expired',
            'expiration_date' => now(),
        ]);
    }

    public function addSignature(array $signatureData): DocumentSignature
    {
        return $this->signatures()->create([
            'user_id' => $signatureData['user_id'],
            'signature_data' => $signatureData['signature_data'],
            'ip_address' => $signatureData['ip_address'] ?? request()->ip(),
            'user_agent' => $signatureData['user_agent'] ?? request()->userAgent(),
            'timestamp' => $signatureData['timestamp'] ?? now(),
            'metadata' => $signatureData['metadata'] ?? [],
        ]);
    }

    public function createVersion(array $versionData): DocumentVersion
    {
        return $this->versions()->create([
            'version_number' => $this->versions()->count() + 1,
            'file_path' => $versionData['file_path'],
            'file_size' => $versionData['file_size'] ?? 0,
            'checksum' => $versionData['checksum'] ?? '',
            'changes' => $versionData['changes'] ?? '',
            'created_by' => auth()->id(),
        ]);
    }

    public function generateDocumentNumber(): string
    {
        $prefix = 'DOC';
        $year = date('Y');
        $sequence = str_pad(static::withTrashed()->count() + 1, 8, '0', STR_PAD_LEFT);
        
        return "{$prefix}-{$year}-{$sequence}";
    }

    public function calculateChecksum(): string
    {
        $filePath = storage_path('app/' . $this->file_path);
        
        if (file_exists($filePath)) {
            return hash_file('sha256', $filePath);
        }
        
        return '';
    }

    public function verifyChecksum(): bool
    {
        return $this->checksum === $this->calculateChecksum();
    }

    public function isCompliant(): bool
    {
        // Check if document meets compliance requirements
        return $this->compliance_status === 'compliant' && 
               $this->hasRequiredSignatures() &&
               !$this->getIsExpiredAttribute();
    }

    public function hasRequiredSignatures(): bool
    {
        // This would depend on document type requirements
        switch ($this->legal_type) {
            case 'deed':
            case 'contract':
            case 'agreement':
                return $this->signatures()->count() >= 2;
            default:
                return true;
        }
    }

    public function getRetentionPeriod(): int
    {
        // Default retention periods by document type
        $periods = [
            'legal' => 365 * 10, // 10 years
            'financial' => 365 * 7, // 7 years
            'tax' => 365 * 7, // 7 years
            'property' => 365 * 5, // 5 years
            'contract' => 365 * 6, // 6 years
            'identification' => 365 * 3, // 3 years
            'insurance' => 365 * 5, // 5 years
            'other' => 365 * 3, // 3 years
        ];

        return $periods[$this->type] ?? $periods['other'];
    }

    public function getDisposalDate(): Carbon
    {
        return $this->created_at->addDays($this->getRetentionPeriod());
    }

    public function shouldDispose(): bool
    {
        return $this->disposal_date && $this->disposal_date->isPast();
    }

    public function addToAuditTrail(string $action, array $data = []): void
    {
        $auditEntry = [
            'action' => $action,
            'user_id' => auth()->id(),
            'timestamp' => now()->toISOString(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data,
        ];

        $this->audit_trail = array_merge($this->audit_trail ?? [], [$auditEntry]);
        $this->save();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->document_number)) {
                $document->document_number = $document->generateDocumentNumber();
            }
            
            if (empty($document->status)) {
                $document->status = 'draft';
            }
            
            if (empty($document->approval_status)) {
                $document->approval_status = 'pending';
            }
            
            if (empty($document->access_level)) {
                $document->access_level = 'internal';
            }
            
            if (empty($document->language)) {
                $document->language = 'en';
            }
        });

        static::updating(function ($document) {
            $document->addToAuditTrail('updated', [
                'changes' => $document->getDirty(),
            ]);
        });

        static::deleted(function ($document) {
            $document->addToAuditTrail('deleted');
        });
    }
}
