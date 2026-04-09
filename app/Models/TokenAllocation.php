<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenAllocation extends Model
{
    protected $fillable = [
        'property_id',
        'user_id',
        'tokens',
        'ledger_tx',
        'income_accrual_json',
    ];

    protected $casts = [
        'income_accrual_json' => 'array',
    ];
}
