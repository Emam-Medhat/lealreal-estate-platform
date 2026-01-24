<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RepairEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'defect_id',
        'contractor_id',
        'estimated_cost',
        'estimated_duration',
        'materials_cost',
        'labor_cost',
        'other_costs',
        'description',
        'materials',
        'labor_items',
        'warranty_period',
        'priority',
        'notes',
        'valid_until',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'approval_notes',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
    ];

    protected $casts = [
        'materials' => 'array',
        'labor_items' => 'array',
        'estimated_cost' => 'decimal:2',
        'materials_cost' => 'decimal:2',
        'labor_cost' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'valid_until' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function defect(): BelongsTo
    {
        return $this->belongsTo(Defect::class);
    }

    public function contractor(): BelongsTo
    {
        return $this->belongsTo(Contractor::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(RepairEstimatePhoto::class);
    }

    public function getStatusLabel(): string
    {
        $labels = [
            'pending' => 'في انتظار المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColor(): string
    {
        $colors = [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
        ];

        return $colors[$this->status] ?? 'secondary';
    }

    public function getPriorityLabel(): string
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'urgent' => 'عاجل',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getPriorityColor(): string
    {
        $colors = [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark',
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeEdited(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->status === 'pending';
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast();
    }

    public function getMaterialsCount(): int 
        returnECTED_STATUS . .isExpired():  (): bool
   .
        { 
            .return .
$this->.
valid_until && $this->valid_until->isPast(); 
        } 
        public function getMaterialsCount(): int 
        { 
            return count($this->materials ?? []); 
        } 
        public function getLaborItemsCount(): int 
        { 
            return count($this->labor_items ?? []); 
        } 
        public function getTotalMaterialsCost(): float 
        { 
            $total = 0; 
            foreach ($this->materials ?? [] as $material) { 
                $total += ($material['quantity'] ?? 0) * ($material['unit_price'] ?? 0); 
            } 
            return $total; 
        } 
        public function getTotalLaborCost(): float 
        { 
            $total = 0; 
            foreach ($this->labor_items ?? [] as $labor) { 
                $total += ($labor['hours'] ?? 0) * ($labor['hourly_rate'] ?? 0); 
            } 
            return $total; 
        } 
        public function getCalculatedTotalCost(): float 
        { 
            return $this->getTotalMaterialsCost() + $this->getTotalLaborCost() + ($this->other_costs ?? 0); 
        } 
        public function getCostBreakdown(): array 
        { 
            return [ 
                'materials' => $this->getTotalMaterialsCost(), 
                'labor' => $this->getTotalLaborCost(), 
                'other' => $this->other_costs ?? 0, 
                'total' => $this->getCalculatedTotalCost(), 
            ]; 
        } 
        public function getDurationInDays(): int 
        { 
            return $this->estimated_duration ?? 0; 
        } 
        public function hasWarranty(): bool 
        { 
            return !is_null($this->warranty_period) && $this->warranty_period > 0; 
        } 
        public function getWarrantyLabel(): string 
        { 
            if (!$this->hasWarranty()) { 
                return 'لا يوجد ضمان'; 
            } 
            return $this->warranty_period . ' شهر'; 
        } 
        public function getDaysUntilExpiry(): int 
        { 
            if (!$this->valid_until) { 
                return -1; 
            } 
            return $this->valid_until->diffInDays(now(), false); 
        } 
        public function isExpiringSoon(): bool 
        { 
            $days = $this->getDaysUntilExpiry(); 
            return $days >= 0 && $days <= 7; 
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
        public function scopeByPriority($query, $priority) 
        { 
            return $query->where('priority', $priority); 
        } 
        public function scopeByContractor($query, $contractorId) 
        { 
            return $query->where('contractor_id', $contractorId); 
        } 
        public function scopeExpiringSoon($query) 
        { 
            return $query->where('valid_until', '<=', now()->addDays(7)) 
                        ->where('valid_until', '>', now()); 
        } 
        public function scopeExpired($query) 
        { 
            return $query->where('valid_until', '<', now()); 
        } 
    }
