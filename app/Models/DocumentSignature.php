<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentSignature extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'version_id',
        'signer_id',
        'signer_name',
        'signer_email',
        'signature_type',
        'signature_data',
        'signature_image_path',
        'ip_address',
        'user_agent',
        'signed_at',
        'status',
        'verified_at',
        'verified_by',
        'verification_status',
        'verification_code',
        'revoked_at',
        'revoked_by',
        'revocation_reason',
    ];

    protected $casts = [
        'signature_data' => 'array',
        'signed_at' => 'datetime',
        'verified_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function version()
    {
        return $this->belongsTo(DocumentVersion::class);
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_id');
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRevoked($query)
    {
        return $query->where('status', 'revoked');
    }

    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function isSigned(): bool
    {
        return $this->status === 'signed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isRevoked(): bool
    {
        return $this->status === 'revoked';
    }

    public function isVerified(): bool
    {
        return $this->verification_status === 'verified';
    }

    public function canBeRevoked(): bool
    {
        return $this->isSigned() && 
               $this->signed_at->diffInHours(now()) <= 24 &&
               !$this->isRevoked();
    }

    public function getSignatureTypeLabel(): string
    {
        return match($this->signature_type) {
            'digital' => 'رقمي',
            'electronic' => 'إلكتروني',
            'handwritten' => 'يدوي',
            'stamp' => 'ختم',
            default => 'غير محدد',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'signed' => 'موقّع',
            'revoked' => 'ملغي',
            default => 'غير محدد',
        };
    }

    public function getVerificationStatusLabel(): string
    {
        return match($this->verification_status) {
            'verified' => 'موثق',
            'unverified' => 'غير موثق',
            'failed' => 'فشل التحقق',
            default => 'غير محدد',
        };
    }

    public function getFormattedSignedAt(): string
    {
        return $this->signed_at ? $this->signed_at->format('Y-m-d H:i:s') : '';
    }

    public function getSignatureImageUrl(): string
    {
        if ($this->signature_image_path) {
            return asset('storage/' . $this->signature_image_path);
        }
        
        return '';
    }

    public function verify(string $code): bool
    {
        $isValid = hash('sha256', $this->signature_data . $this->document_id) === $code;
        
        if ($isValid) {
            $this->update([
                'verification_status' => 'verified',
                'verified_at' => now(),
                'verified_by' => auth()->id(),
            ]);
        } else {
            $this->update([
                'verification_status' => 'failed',
            ]);
        }
        
        return $isValid;
    }

    public function revoke(string $reason = null)
    {
        $this->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by' => auth()->id(),
            'revocation_reason' => $reason ?? 'إلغاء من قبل المستخدم',
        ]);
    }

    public function generateVerificationCode(): string
    {
        $this->verification_code = hash('sha256', $this->signature_data . $this->document_id);
        $this->save();
        
        return $this->verification_code;
    }

    public function getSignatureMetadata(): array
    {
        return [
            'signer_name' => $this->signer_name,
            'signer_email' => $this->signer_email,
            'signature_type' => $this->signature_type,
            'signed_at' => $this->signed_at,
            'ip_address' => $this->ip_address,
            'user_agent' => $this->user_agent,
            'status' => $this->status,
            'verification_status' => $this->verification_status,
        ];
    }
}
