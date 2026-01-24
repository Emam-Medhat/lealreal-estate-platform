<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LeadTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'description',
    ];

    public function leads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'lead_tag')
            ->withTimestamps();
    }

    public function scopePopular($query)
    {
        return $query->withCount('leads')
            ->orderBy('leads_count', 'desc');
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }
}
