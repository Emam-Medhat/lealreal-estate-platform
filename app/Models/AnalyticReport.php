<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'category',
        'status',
        'file_path',
        'file_size',
        'parameters',
        'generated_by',
        'generated_at',
        'expires_at',
    ];

    protected $casts = [
        'parameters' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'completed')
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->file_size < 1024) {
            return $this->file_size . ' B';
        } elseif ($this->file_size < 1024 * 1024) {
            return round($this->file_size / 1024, 1) . ' KB';
        } else {
            return round($this->file_size / (1024 * 1024), 1) . ' MB';
        }
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
