<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'reason',
        'description',
        'status',
        'admin_notes',
        'resolved_by',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }
}
