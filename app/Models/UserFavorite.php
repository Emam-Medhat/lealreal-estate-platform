<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserFavorite extends Model
{
    protected $fillable = [
        'user_id',
        'favoritable_type',
        'favoritable_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Removed non-existent columns from casts and fillable

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function favoritable(): MorphTo
    {
        return $this->morphTo();
    }
}
