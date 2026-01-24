<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserComparison extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'criteria',
        'weights',
        'scores',
        'results',
        'is_public',
        'is_template',
        'template_name',
        'comparison_data',
        'metadata',
        'expires_at',
        'shared_with',
        'comparison_type'
    ];

    protected $casts = [
        'criteria' => 'json',
        'weights' => 'json',
        'scores' => 'json',
        'results' => 'json',
        'comparison_data' => 'json',
        'metadata' => 'json',
        'shared_with' => 'json',
        'is_public' => 'boolean',
        'is_template' => 'boolean',
        'expires_at' => 'datetime'
    ];

    protected $dates = [
        'expires_at',
        'created_at',
        'updated_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comparableItems(): BelongsToMany
    {
        // Using a generic model reference since Property model may not exist yet
        // This will be updated when Property model is implemented
        return $this->belongsToMany('App\Models\Property', 'comparison_items')
                    ->withPivot(['score', 'rank', 'notes', 'custom_data'])
                    ->withTimestamps();
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('comparison_type', $type);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function addItem($item, $score = null, $notes = null): bool
    {
        $result = $this->comparableItems()->attach($item, [
            'score' => $score,
            'notes' => $notes
        ]);
        return true; // attach() returns void, so we return true for success
    }

    public function removeItem($item): bool
    {
        return $this->comparableItems()->detach($item);
    }

    public function updateItemScore($item, $score): bool
    {
        return $this->comparableItems()->updateExistingPivot($item, [
            'score' => $score
        ]);
    }

    public function updateItemNotes($item, $notes): bool
    {
        return $this->comparableItems()->updateExistingPivot($item, [
            'notes' => $notes
        ]);
    }

    public function calculateScores(): array
    {
        $items = $this->comparableItems()->get();
        $criteria = $this->criteria ?? [];
        $weights = $this->weights ?? [];
        $scores = [];

        foreach ($items as $item) {
            $totalScore = 0;
            $maxScore = 0;

            foreach ($criteria as $criterion => $config) {
                $weight = $weights[$criterion] ?? 1;
                $itemScore = $this->calculateCriterionScore($item, $criterion, $config);
                
                $totalScore += $itemScore * $weight;
                $maxScore += $config['max_score'] * $weight;
            }

            $scores[$item->id] = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        }

        // Update scores in database
        $this->update(['scores' => $scores]);

        return $scores;
    }

    private function calculateCriterionScore($item, $criterion, $config): float
    {
        // This would implement the actual scoring logic based on criterion type
        // For example: price, location, size, amenities, etc.
        $value = $this->getItemValue($item, $criterion);
        $maxScore = $config['max_score'] ?? 10;
        $minValue = $config['min_value'] ?? 0;
        $maxValue = $config['max_value'] ?? 100;

        if ($config['type'] === 'lower_is_better') {
            return $maxScore * (1 - (($value - $minValue) / ($maxValue - $minValue)));
        } else {
            return $maxScore * (($value - $minValue) / ($maxValue - $minValue));
        }
    }

    private function getItemValue($item, $criterion): float
    {
        // Extract value from item based on criterion
        $method = 'get' . ucfirst($criterion) . 'Value';
        if (method_exists($item, $method)) {
            return $item->$method();
        }

        // Fallback to direct property access
        return $item->$criterion ?? 0;
    }

    public function getRanking(): array
    {
        $scores = $this->scores ?? [];
        arsort($scores);
        
        return array_keys($scores);
    }

    public function getWinner()
    {
        $ranking = $this->getRanking();
        return $ranking ? 'App\Models\Property'::find($ranking[0]) : null;
    }

    public function makePublic(): bool
    {
        return $this->update(['is_public' => true]);
    }

    public function makePrivate(): bool
    {
        return $this->update(['is_public' => false]);
    }

    public function makeTemplate(string $templateName): bool
    {
        return $this->update([
            'is_template' => true,
            'template_name' => $templateName
        ]);
    }

    public function shareWithUser($userId): bool
    {
        $sharedWith = $this->shared_with ?? [];
        if (!in_array($userId, $sharedWith)) {
            $sharedWith[] = $userId;
            return $this->update(['shared_with' => $sharedWith]);
        }
        return true;
    }

    public function unshareWithUser($userId): bool
    {
        $sharedWith = $this->shared_with ?? [];
        $key = array_search($userId, $sharedWith);
        if ($key !== false) {
            unset($sharedWith[$key]);
            return $this->update(['shared_with' => array_values($sharedWith)]);
        }
        return true;
    }

    public function getCategoryAttribute(): string
    {
        return $this->attributes['category'] ?? 'general';
    }

    public function getComparisonTypeAttribute(): string
    {
        return $this->attributes['comparison_type'] ?? 'property';
    }

    public function getCriteriaAttribute(): array
    {
        return $this->attributes['criteria'] ? json_decode($this->attributes['criteria'], true) : [];
    }

    public function setCriteriaAttribute($value)
    {
        $this->attributes['criteria'] = is_array($value) ? json_encode($value) : $value;
    }

    public function getWeightsAttribute(): array
    {
        return $this->attributes['weights'] ? json_decode($this->attributes['weights'], true) : [];
    }

    public function setWeightsAttribute($value)
    {
        $this->attributes['weights'] = is_array($value) ? json_encode($value) : $value;
    }
}
