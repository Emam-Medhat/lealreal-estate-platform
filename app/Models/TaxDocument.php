<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'expires_at',
        'user_id',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'expires_at' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('document_type', $type);
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
                    ->where('expires_at', '<=', now());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    public function getFileSizeInKBAttribute(): int
    {
        return round($this->file_size / 1024, 2);
    }

    public function getFileSizeInMBAttribute(): float
    {
        return round($this->file_size / (1024 * 1024), 2);
    }

    public function getFileExtensionAttribute(): string
    {
        return pathinfo($this->file_name, PATHINFO_EXTENSION);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $size = $this->file_size;
        
        if ($size < 1024) {
            return $size . ' bytes';
        } elseif ($size < 1024 * 1024) {
            return round($size / 1024, 2) . ' KB';
        } else {
            return round($size / (1024 * 1024), 2) . ' MB';
        }
    }
}
