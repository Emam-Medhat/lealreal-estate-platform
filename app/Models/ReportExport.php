<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'user_id',
        'format',
        'file_path',
        'file_name',
        'file_size',
        'parameters',
        'status',
        'exported_at',
        'expires_at',
        'download_count',
        'max_downloads',
        'error_message'
    ];

    protected $casts = [
        'parameters' => 'array',
        'exported_at' => 'datetime',
        'expires_at' => 'datetime',
        'max_downloads' => 'integer'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(Report::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForReport($query, $reportId)
    {
        return $query->where('report_id', $reportId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByFormat($query, $format)
    {
        return $query->where('format', $format);
    }

    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now());
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function canBeDownloaded(): bool
    {
        return $this->isCompleted() && 
               !$this->isExpired() && 
               ($this->max_downloads === null || $this->download_count < $this->max_downloads) &&
               file_exists($this->file_path);
    }

    public function hasReachedDownloadLimit(): bool
    {
        return $this->max_downloads && $this->download_count >= $this->max_downloads;
    }

    public function getFormatLabel(): string
    {
        return match($this->format) {
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV',
            'html' => 'HTML',
            'json' => 'JSON',
            default => strtoupper($this->format)
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'processing' => 'قيد المعالجة',
            'completed' => 'مكتمل',
            'failed' => 'فشل',
            'expired' => 'منتهي الصلاحية',
            default => 'غير معروف'
        };
    }

    public function getStatusColor(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'expired' => 'secondary',
            default => 'secondary'
        };
    }

    public function getFormattedFileSize(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getFormattedExportedAt(): string
    {
        return $this->exported_at ? $this->exported_at->format('Y-m-d H:i:s') : 'N/A';
    }

    public function getFormattedExpiresAt(): string
    {
        return $this->expires_at ? $this->expires_at->format('Y-m-d H:i:s') : 'لا ينتهي';
    }

    public function getDownloadProgress(): string
    {
        if ($this->max_downloads === null) {
            return 'غير محدود';
        }

        return "{$this->download_count} / {$this->max_downloads}";
    }

    public function getDownloadProgressPercentage(): int
    {
        if ($this->max_downloads === null) {
            return 0;
        }

        return min(($this->download_count / $this->max_downloads) * 100, 100);
    }

    public function getDownloadUrl(): string
    {
        return route('report-exports.download', $this->id);
    }

    public function getPreviewUrl(): string
    {
        return route('report-exports.preview', $this->id);
    }

    public function getDeleteUrl(): string
    {
        return route('report-exports.destroy', $this->id);
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function markAsCompleted($filePath, $fileSize = null)
    {
        $this->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'file_size' => $fileSize ?: filesize($filePath),
            'exported_at' => now()
        ]);
    }

    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    public function markAsExpired()
    {
        $this->update(['status' => 'expired']);
    }

    public function generateFileName(): string
    {
        $report = $this->report;
        $timestamp = now()->format('Y-m-d_H-i-s');
        $safeTitle = str()->slug($report->title, '_');
        
        return "{$safeTitle}_{$timestamp}.{$this->format}";
    }

    public function getPublicUrl(): string
    {
        return asset("storage/baath/reports/{$this->file_name}");
    }

    public function getRemainingDownloads(): int
    {
        if ($this->max_downloads === null) {
            return -1; // Unlimited
        }

        return max(0, $this->max_downloads - $this->download_count);
    }

    public function getExpirationStatus(): string
    {
        if (!$this->expires_at) {
            return 'لا ينتهي';
        }

        if ($this->isExpired()) {
            return 'منتهي';
        }

        return $this->expires_at->diffForHumans(now(), true);
    }

    public function getExpirationColor(): string
    {
        if (!$this->expires_at) {
            return 'success';
        }

        return $this->isExpired() ? 'danger' : 'warning';
    }

    public function canBeDeleted(): bool
    {
        return true; // User can always delete their exports
    }

    public function cleanup()
    {
        // Delete the file
        if ($this->file_path && file_exists($this->file_path)) {
            unlink($this->file_path);
        }

        // Delete the record
        $this->delete();
    }

    protected static function booted()
    {
        static::deleting(function ($export) {
            // Delete the physical file
            if ($export->file_path && file_exists($export->file_path)) {
                unlink($export->file_path);
            }
        });

        static::created(function ($export) {
            // Set expiration if not set
            if (!$export->expires_at) {
                $export->expires_at = now()->addDays(7);
                $export->save();
            }
        });
    }
}
