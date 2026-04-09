<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'description',
        'report_template_id',
        'parameters',
        'filters',
        'data',
        'status',
        'generated_at',
        'expires_at',
        'generated_by',
        'user_id',
        'file_size',
        'file_path',
        'format',
        'is_public',
        'view_count',
    ];

    protected $casts = [
        'parameters' => 'array',
        'filters' => 'array',
        'data' => 'array',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
        'view_count' => 'integer',
    ];

    public function salesReport(): HasOne
    {
        return $this->hasOne(SalesReport::class);
    }

    public function performanceReport(): HasOne
    {
        return $this->hasOne(PerformanceReport::class);
    }

    public function marketReport(): HasOne
    {
        return $this->hasOne(MarketReport::class);
    }

    public function customReport(): HasOne
    {
        return $this->hasOne(CustomReport::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(ReportExport::class);
    }

    public function visualizations(): HasMany
    {
        return $this->hasMany(DataVisualization::class)->orderBy('position_order');
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'report_template_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return 'N/A';
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'completed' => 'green',
            'generating' => 'blue',
            'failed' => 'red',
            'scheduled' => 'yellow',
            default => 'gray',
        };
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'completed' => 'Completed',
            'generating' => 'Generating',
            'failed' => 'Failed',
            'scheduled' => 'Scheduled',
            default => 'Unknown',
        };
    }

    public function getFormatLabelAttribute()
    {
        return match($this->format) {
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV',
            'json' => 'JSON',
            default => strtoupper($this->format)
        };
    }

    protected static function booted()
    {
        static::deleting(function ($report) {
            // Delete associated files
            if ($report->file_path && file_exists($report->file_path)) {
                unlink($report->file_path);
            }

            // Delete related exports
            $report->exports()->delete();
            $report->visualizations()->delete();
        });
    }
}
