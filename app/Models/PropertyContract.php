<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyContract extends Model
{
    protected $fillable = [
        'property_id',
        'ipfs_hash',
        'chain_tx',
        'status',
        'party_a_ekyc_id',
        'party_b_ekyc_id',
    ];
}
