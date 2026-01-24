<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyBranch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'company_branches';

    protected $fillable = [
        'company_id',
        'name',
        'address',
        'city',
        'state',
        'country',
        'phone',
        'email',
        'manager_id',
        'is_main',
        'status',
        'coordinates'
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'coordinates' => 'json'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}
