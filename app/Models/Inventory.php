<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Inventory extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($inventory) {
            if (empty($inventory->item_code)) {
                $inventory->item_code = 'INV-' . strtoupper(Str::random(8));
            }
            
            if (empty($inventory->unit)) {
                $inventory->unit = 'pcs'; // Default to pieces
            }
            
            if (empty($inventory->unit_cost)) {
                $inventory->unit_cost = 0.00; // Default to zero
            }
        });
    }

    protected $table = 'inventory_items';

    protected $fillable = [
        'item_code',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'category',
        'category_id',
        'status',
        'brand',
        'model',
        'sku',
        'unit',
        'unit_ar',
        'unit_of_measure',
        'unit_cost',
        'unit_price',
        'selling_price',
        'quantity',
        'min_quantity',
        'max_quantity',
        'max_stock',
        'reorder_point',
        'reorder_level',
        'reorder_quantity',
        'supplier',
        'supplier_id',
        'supplier_contact',
        'last_purchase_date',
        'next_purchase_date',
        'location',
        'location_ar',
        'specifications',
        'images',
        'attachments',
        'barcode',
        'qr_code',
        'warranty_expiry',
        'expiry_date',
        'requires_maintenance',
        'maintenance_instructions',
        'maintenance_instructions_ar',
        'maintenance_schedule',
        'last_maintenance_date',
        'next_maintenance_date',
        'safety_notes',
        'safety_notes_ar',
        'usage_history',
        'notes',
        'notes_ar',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'max_stock' => 'integer',
        'reorder_point' => 'integer',
        'reorder_level' => 'integer',
        'reorder_quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'specifications' => 'array',
        'images' => 'array',
        'attachments' => 'array',
        'maintenance_schedule' => 'array',
        'usage_history' => 'array',
        'requires_maintenance' => 'boolean',
        'last_purchase_date' => 'date',
        'next_purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'expiry_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(InventoryCategory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class);
    }

    public function getCategoryName()
    {
        $categories = [
            'tools' => 'Tools',
            'materials' => 'Materials',
            'equipment' => 'Equipment',
            'supplies' => 'Supplies',
            'safety' => 'Safety',
            'other' => 'Other'
        ];
        
        return $categories[$this->category] ?? 'Unknown';
    }

    public function getCategoryNameAr()
    {
        $categories = [
            'tools' => 'أدوات',
            'materials' => 'مواد',
            'equipment' => 'معدات',
            'supplies' => 'لوازم',
            'safety' => 'سلامة',
            'other' => 'أخرى'
        ];
        
        return $categories[$this->category] ?? 'غير محدد';
    }

    public function getSupplierName()
    {
        return $this->supplier ?: 'Unknown';
    }

    public function transactions()
    {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function workOrderItems()
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySupplier($query, $supplierId)
    {
        return $query->where('supplier_id', $supplierId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= reorder_point')
                    ->where('quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    public function scopeAvailable($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeOverstock($query)
    {
        return $query->whereRaw('quantity > max_stock_level');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'available' => 'متاح',
            'low_stock' => 'مخزون منخفض',
            'out_of_stock' => 'نفد المخزون',
            'discontinued' => 'متوقف',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'available' => 'green',
            'low_stock' => 'yellow',
            'out_of_stock' => 'red',
            'discontinued' => 'gray',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function isLowStock()
    {
        return $this->quantity > 0 && $this->quantity <= $this->reorder_point;
    }

    public function isOutOfStock()
    {
        return $this->quantity == 0;
    }

    public function isAvailable()
    {
        return $this->quantity > 0;
    }

    public function isOverstock()
    {
        return $this->max_stock_level && $this->quantity > $this->max_stock_level;
    }

    public function canReserve($requestedQuantity)
    {
        return $this->quantity >= $requestedQuantity;
    }

    public function reserve($quantity)
    {
        if (!$this->canReserve($quantity)) {
            return false;
        }

        $this->decrement('quantity', $quantity);
        $this->updateTotalValue();
        $this->updateStatus();
        $this->update(['last_stock_out' => now()]);

        return true;
    }

    public function addStock($quantity, $unitCost = null)
    {
        $oldQuantity = $this->quantity;
        $newQuantity = $oldQuantity + $quantity;

        if ($unitCost) {
            $this->unit_cost = $unitCost;
        }

        $this->quantity = $newQuantity;
        $this->updateTotalValue();
        $this->updateStatus();
        $this->update(['last_stock_in' => now()]);
        $this->save();

        return $this;
    }

    public function updateTotalValue()
    {
        $this->total_value = $this->quantity * $this->unit_cost;
        $this->save();
    }

    public function updateStatus()
    {
        if ($this->quantity == 0) {
            $this->status = 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            $this->status = 'low_stock';
        } else {
            $this->status = 'available';
        }

        $this->save();
    }

    public function getStockLevel()
    {
        if ($this->quantity == 0) {
            return 'out_of_stock';
        } elseif ($this->quantity <= $this->reorder_level) {
            return 'low_stock';
        } elseif ($this->max_stock_level && $this->quantity > $this->max_stock_level) {
            return 'overstock';
        } else {
            return 'optimal';
        }
    }

    public function getStockLevelLabelAttribute()
    {
        $labels = [
            'out_of_stock' => 'نفد المخزون',
            'low_stock' => 'مخزون منخفض',
            'optimal' => 'مثالي',
            'overstock' => 'زيادة المخزون',
        ];

        return $labels[$this->getStockLevel()] ?? 'غير معروف';
    }

    public function getStockLevelColorAttribute()
    {
        $colors = [
            'out_of_stock' => 'red',
            'low_stock' => 'yellow',
            'optimal' => 'green',
            'overstock' => 'blue',
        ];

        return $colors[$this->getStockLevel()] ?? 'gray';
    }

    public function getReorderQuantity()
    {
        if ($this->max_stock_level) {
            return $this->max_stock_level - $this->quantity;
        }

        return $this->reorder_level * 2;
    }

    public function getDaysOfStock()
    {
        // Calculate based on average usage (if available)
        $avgUsage = $this->transactions()
            ->where('type', 'out')
            ->where('transaction_date', '>=', now()->subDays(30))
            ->avg('quantity');

        if ($avgUsage && $avgUsage > 0) {
            return floor($this->quantity / $avgUsage);
        }

        return null;
    }

    public function getTotalValue()
    {
        return $this->quantity * $this->unit_cost;
    }

    public function getProfitMargin()
    {
        if ($this->selling_price && $this->unit_cost) {
            return (($this->selling_price - $this->unit_cost) / $this->unit_cost) * 100;
        }

        return null;
    }

    public function getProfitAmount()
    {
        if ($this->selling_price && $this->unit_cost) {
            return $this->selling_price - $this->unit_cost;
        }

        return null;
    }

    public function getUsageStats($days = 30)
    {
        $startDate = now()->subDays($days);
        
        $totalIn = $this->transactions()
            ->where('type', 'in')
            ->where('transaction_date', '>=', $startDate)
            ->sum('quantity');

        $totalOut = $this->transactions()
            ->where('type', 'out')
            ->where('transaction_date', '>=', $startDate)
            ->sum('quantity');

        $avgDailyOut = $totalOut / $days;

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'net_change' => $totalIn - $totalOut,
            'avg_daily_out' => $avgDailyOut,
            'days_of_stock' => $avgDailyOut > 0 ? floor($this->quantity / $avgDailyOut) : null,
        ];
    }

    public function getRecentTransactions($limit = 10)
    {
        return $this->transactions()
            ->with('user')
            ->latest('transaction_date')
            ->take($limit)
            ->get();
    }

    public function createTransaction($type, $quantity, $reference = null, $notes = null, $userId = null)
    {
        return $this->transactions()->create([
            'type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $this->unit_cost,
            'reference' => $reference,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
            'transaction_date' => now(),
        ]);
    }

    public function adjustStock($newQuantity, $reason, $userId = null)
    {
        $oldQuantity = $this->quantity;
        $difference = $newQuantity - $oldQuantity;

        if ($difference == 0) {
            return false;
        }

        $this->quantity = $newQuantity;
        $this->updateTotalValue();
        $this->updateStatus();
        $this->save();

        // Create adjustment transaction
        $this->createTransaction('adjustment', abs($difference), 'Stock Adjustment', $reason, $userId);

        return true;
    }

    public function getTotalTransactions($type = null, $days = 30)
    {
        $query = $this->transactions()
            ->where('transaction_date', '>=', now()->subDays($days));

        if ($type) {
            $query->where('type', $type);
        }

        return $query->sum('quantity');
    }

    public function getTransactionCount($type = null, $days = 30)
    {
        $query = $this->transactions()
            ->where('transaction_date', '>=', now()->subDays($days));

        if ($type) {
            $query->where('type', $type);
        }

        return $query->count();
    }

    public function isDiscontinued()
    {
        return $this->status === 'discontinued';
    }

    public function discontinue()
    {
        $this->update(['status' => 'discontinued']);
    }

    public function activate()
    {
        $this->updateStatus();
    }

    public function getAttachmentCount()
    {
        return count($this->attachments ?? []);
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }
}
