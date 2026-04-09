<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Request extends Model
{
    protected $fillable = [
        'request_id',
        'method',
        'url',
        'ip_address',
        'user_agent',
        'headers',
        'payload',
        'status',
        'response_code',
        'response_time',
        'error_message',
        'started_at',
        'completed_at',
        'user_id',
    ];

    protected $casts = [
        'headers' => 'array',
        'payload' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'response_time' => 'float',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    // Status colors for UI
    public static function getStatusColors()
    {
        return [
            self::STATUS_PENDING => 'warning',
            self::STATUS_PROCESSING => 'info',
            self::STATUS_COMPLETED => 'success',
            self::STATUS_FAILED => 'danger',
        ];
    }

    // Status labels in Arabic
    public static function getStatusLabels()
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_PROCESSING => 'قيد المعالجة',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_FAILED => 'فشل',
        ];
    }

    // Get status color
    public function getStatusColorAttribute()
    {
        return self::getStatusColors()[$this->status] ?? 'secondary';
    }

    // Get status label
    public function getStatusLabelAttribute()
    {
        return self::getStatusLabels()[$this->status] ?? 'غير معروف';
    }

    // Relationship with User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Scope for status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for recent requests
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    // Get duration in human readable format
    public function getDurationAttribute()
    {
        if (!$this->started_at || !$this->completed_at) {
            return 'غير محدد';
        }

        $duration = $this->completed_at->diffInSeconds($this->started_at);
        
        if ($duration < 1) {
            return '< 1 ثانية';
        } elseif ($duration < 60) {
            return $duration . ' ثانية';
        } else {
            return round($duration / 60, 2) . ' دقيقة';
        }
    }

    // Get formatted response time
    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time) {
            return 'غير محدد';
        }

        if ($this->response_time < 1000) {
            return round($this->response_time, 2) . ' ms';
        } else {
            return round($this->response_time / 1000, 2) . ' s';
        }
    }
}
