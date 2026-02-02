<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaFile extends Model
{
    use HasFactory;

    protected $table = 'media_library';

    protected $fillable = [
        'filename',
        'original_name',
        'mime_type',
        'file_path',
        'file_size',
        'dimensions',
        'type',
        'alt_text',
        'description',
        'metadata',
        'uploaded_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeImages($query)
    {
        return $query->where('type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeDocuments($query)
    {
        return $query->where('type', 'document');
    }

    public function isImage()
    {
        return $this->type === 'image';
    }

    public function isVideo()
    {
        return $this->type === 'video';
    }

    public function isDocument()
    {
        return $this->type === 'document';
    }

    public function getFormattedFileSize()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDimensions()
    {
        if ($this->dimensions) {
            $dims = explode('x', $this->dimensions);
            return [
                'width' => $dims[0] ?? null,
                'height' => $dims[1] ?? null,
            ];
        }
        
        return null;
    }

    public function getUrl()
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrl()
    {
        if ($this->isImage()) {
            // Generate thumbnail logic here
            return $this->getUrl();
        }
        
        return null;
    }
}
