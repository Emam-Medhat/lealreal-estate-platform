<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'content',
        'variables',
        'category',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'variables' => 'array',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getVariableNames()
    {
        return collect($this->variables ?? [])->pluck('name')->toArray();
    }

    public function renderWithVariables($data = [])
    {
        $content = $this->content;

        foreach ($this->variables ?? [] as $variable) {
            $placeholder = '{{' . $variable['name'] . '}}';
            $value = $data[$variable['name']] ?? '[' . $variable['name'] . ']';
            $content = str_replace($placeholder, $value, $content);
        }

        return $content;
    }
}
