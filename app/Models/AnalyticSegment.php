<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AnalyticSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'criteria',
        'is_active',
        'user_count',
        'conversion_rate',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'criteria' => 'array',
        'is_active' => 'boolean',
        'conversion_rate' => 'decimal:2',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_segments')
            ->withTimestamps()
            ->withPivot(['added_at', 'removed_at']);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function getCriteriaDescriptionAttribute(): string
    {
        if (!$this->criteria) {
            return 'لا توجد معايير';
        }

        $descriptions = [];
        foreach ($this->criteria as $criterion) {
            $descriptions[] = "{$criterion['field']} {$criterion['operator']} {$criterion['value']}";
        }

        return implode(', ', $descriptions);
    }
}
