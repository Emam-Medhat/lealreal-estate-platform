<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentRevision extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'content',
        'changes',
        'author_id',
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeForModel($query, $model)
    {
        return $query->where('model_type', get_class($model))
            ->where('model_id', $model->id);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function getChangesSummary()
    {
        $changes = $this->changes ?? [];
        $summary = [];
        
        foreach ($changes as $field => $change) {
            if (is_string($change)) {
                $summary[] = $field . ': ' . $change;
            } elseif (is_array($change) && isset($change['from'], $change['to'])) {
                $summary[] = $field . ': "' . $change['from'] . '" → "' . $change['to'] . '"';
            }
        }
        
        return implode(', ', $summary);
    }
}
