<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiUsageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'feature',
        'model',
        'input_tokens',
        'output_tokens',
        'cost_usd',
        'user_id',
        'ip_address',
        'meta',
    ];

    protected $casts = [
        'input_tokens' => 'integer',
        'output_tokens' => 'integer',
        'cost_usd' => 'decimal:6',
        'meta' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    /**
     * Get the user that triggered the AI usage.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get usage for a specific feature.
     */
    public function scopeForFeature($query, string $feature)
    {
        return $query->where('feature', $feature);
    }

    /**
     * Scope to get usage for a specific model.
     */
    public function scopeForModel($query, string $model)
    {
        return $query->where('model', $model);
    }

    /**
     * Scope to get usage by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get usage from a specific IP.
     */
    public function scopeByIp($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to get usage within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get total cost for this usage log entry.
     */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->cost_usd;
    }

    /**
     * Get total tokens used (input + output).
     */
    public function getTotalTokensAttribute(): int
    {
        return $this->input_tokens + $this->output_tokens;
    }

    /**
     * Get human readable feature name.
     */
    public function getFeatureNameAttribute(): string
    {
        $features = [
            'smart_search' => 'البحث الذكي',
            'chatbot' => 'المساعد الذكي',
            'price_prediction' => 'التنبؤ بالأسعار',
            'image_analysis' => 'تحليل الصور',
            'property_description' => 'وصف العقارات',
            'recommendations' => 'التوصيات',
            'content_generation' => 'توليد المحتوى',
        ];

        return $features[$this->feature] ?? $this->feature;
    }

    /**
     * Get formatted cost display.
     */
    public function getFormattedCostAttribute(): string
    {
        return '$' . number_format($this->cost_usd, 6);
    }

    /**
     * Get formatted tokens display.
     */
    public function getFormattedTokensAttribute(): string
    {
        return number_format($this->getTotalTokensAttribute()) . ' tokens';
    }
}
