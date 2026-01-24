<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractParty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'contract_id',
        'name',
        'type',
        'role',
        'address',
        'phone',
        'email',
        'national_id',
        'commercial_register',
        'tax_id',
        'legal_representative',
        'contact_person',
        'is_primary',
        'signature_required',
        'signature_status',
        'signed_at',
        'signature_method',
        'signature_data',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'signature_required' => 'boolean',
        'signed_at' => 'datetime',
        'signature_data' => 'array',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function signatures()
    {
        return $this->hasMany(ContractSignature::class, 'party_id');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeSecondary($query)
    {
        return $query->where('is_primary', false);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeSignatureRequired($query)
    {
        return $query->where('signature_required', true);
    }

    public function scopeSigned($query)
    {
        return $query->where('signature_status', 'signed');
    }

    public function scopePendingSignature($query)
    {
        return $query->where('signature_status', 'pending');
    }

    public function isPrimary(): bool
    {
        return $this->is_primary;
    }

    public function requiresSignature(): bool
    {
        return $this->signature_required;
    }

    public function isSigned(): bool
    {
        return $this->signature_status === 'signed';
    }

    public function isPendingSignature(): bool
    {
        return $this->signature_status === 'pending';
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'individual' => 'فرد',
            'company' => 'شركة',
            'government' => 'حكومي',
            'organization' => 'منظمة',
            default => 'غير محدد',
        };
    }

    public function getRoleLabel(): string
    {
        return match($this->role) {
            'landlord' => 'المؤجر',
            'tenant' => 'المستأجر',
            'buyer' => 'المشتري',
            'seller' => 'البائع',
            'contractor' => 'المقاول',
            'client' => 'العميل',
            'supplier' => 'المورد',
            'partner' => 'شريك',
            'guarantor' => 'كفيل',
            'witness' => 'شاهد',
            default => 'غير محدد',
        };
    }

    public function getSignatureStatusLabel(): string
    {
        return match($this->signature_status) {
            'pending' => 'في الانتظار',
            'signed' => 'موقّع',
            'declined' => 'مرفوض',
            'waived' => 'تنازل',
            default => 'غير محدد',
        };
    }

    public function getSignatureMethodLabel(): string
    {
        return match($this->signature_method) {
            'digital' => 'رقمي',
            'electronic' => 'إلكتروني',
            'handwritten' => 'يدوي',
            'stamp' => 'ختم',
            default => 'غير محدد',
        };
    }

    public function getFormattedAddress(): string
    {
        return $this->address ?? 'غير متوفر';
    }

    public function getFormattedPhone(): string
    {
        return $this->phone ?? 'غير متوفر';
    }

    public function getFormattedEmail(): string
    {
        return $this->email ?? 'غير متوفر';
    }

    public function getFullIdentifier(): string
    {
        $identifier = $this->name;
        
        if ($this->commercial_register) {
            $identifier .= ' (س.ت: ' . $this->commercial_register . ')';
        } elseif ($this->national_id) {
            $identifier .= ' (رقم هوية: ' . $this->national_id . ')';
        }
        
        return $identifier;
    }

    public function markAsSigned(string $method = null, array $signatureData = null)
    {
        $this->update([
            'signature_status' => 'signed',
            'signed_at' => now(),
            'signature_method' => $method ?? 'digital',
            'signature_data' => $signatureData,
        ]);
    }

    public function markAsPending()
    {
        $this->update([
            'signature_status' => 'pending',
            'signed_at' => null,
            'signature_method' => null,
            'signature_data' => null,
        ]);
    }

    public function markAsDeclined(string $reason = null)
    {
        $this->update([
            'signature_status' => 'declined',
            'signed_at' => null,
            'signature_method' => null,
            'signature_data' => null,
            'notes' => $reason,
        ]);
    }

    public function waiveSignature(string $reason = null)
    {
        $this->update([
            'signature_status' => 'waived',
            'signed_at' => null,
            'signature_method' => null,
            'signature_data' => null,
            'notes' => $reason,
        ]);
    }

    public function canSign(): bool
    {
        return $this->requiresSignature() && $this->isPendingSignature();
    }

    public function getContactInfo(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'contact_person' => $this->contact_person,
            'legal_representative' => $this->legal_representative,
        ];
    }

    public function getLegalInfo(): array
    {
        return [
            'type' => $this->type,
            'role' => $this->role,
            'national_id' => $this->national_id,
            'commercial_register' => $this->commercial_register,
            'tax_id' => $this->tax_id,
        ];
    }

    public function getSignatureInfo(): array
    {
        return [
            'signature_required' => $this->signature_required,
            'signature_status' => $this->signature_status,
            'signature_method' => $this->signature_method,
            'signed_at' => $this->signed_at,
            'signature_data' => $this->signature_data,
        ];
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'type_label' => $this->getTypeLabel(),
            'role_label' => $this->getRoleLabel(),
            'signature_status_label' => $this->getSignatureStatusLabel(),
            'signature_method_label' => $this->getSignatureMethodLabel(),
            'full_identifier' => $this->getFullIdentifier(),
            'can_sign' => $this->canSign(),
        ]);
    }
}
