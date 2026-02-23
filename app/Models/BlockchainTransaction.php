<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockchainTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_address',
        'function',
        'parameters',
        'tx_hash',
        'network',
        'gas_used',
        'cost',
        'status',
        'result',
        'executed_at',
        'confirmed_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'result' => 'array',
        'executed_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'gas_used' => 'integer',
        'cost' => 'decimal:8',
    ];

    public function smartContract()
    {
        return $this->belongsTo(SmartContract::class, 'contract_address', 'address');
    }
}
