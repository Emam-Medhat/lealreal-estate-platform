<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InventorySupplier extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'description',
        'website',
        'status',
        'credit_limit',
        'payment_terms',
        'notes',
    ];
    
    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms' => 'integer',
        'status' => 'string',
    ];
    
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
