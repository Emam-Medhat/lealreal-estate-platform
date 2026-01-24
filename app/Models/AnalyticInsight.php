<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'confidence_score',
        'impact_level',
        'data_source',
        'insight_data',
        'recommendations',
        'status',
        'generated_by',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
        'impact_level' => 'integer',
        'insight_data' => 'array',
        'recommendations' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByImpact($query, int $level)
    {
        return $query->where('impact_level', $level);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function getConfidenceLabelAttribute(): string
    {
        if ($this->confidence_score >= 90) {
            return 'عالي جداً';
        } elseif ($this->confidence_score >= 75) {
            return 'عالي';
        } elseif ($this->confidence_score >= 60) {
            return 'متوسط';
        } else {
            return 'منخفض';
        }
    }

    public function getImpactLabelAttribute(): string
    {
        switch ($this->impact_level) {
            case 3:
                return 'عالي';
            case 2:
                return 'متوسط';
            case 1:
                return 'منخفض';
            default:
                return 'غير محدد';
        }
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
