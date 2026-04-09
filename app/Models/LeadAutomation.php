<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadAutomation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'triggers',
        'actions',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'triggers' => 'array',
        'actions' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByTrigger($query, $triggerType)
    {
        return $query->whereJsonContains('triggers', [['type' => $triggerType]]);
    }

    public function scopeByAction($query, $actionType)
    {
        return $query->whereJsonContains('actions', [['type' => $actionType]]);
    }
}
