<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'path',
        'url',
        'thumbnail_url'
    ];

    protected $casts = [
        'size' => 'integer'
    ];

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/svg+xml'
        ]);
    }

    public function isVideo(): bool
    {
        return in_array($this->mime_type, [
            'video/mp4',
            'video/avi',
            'video/mov',
            'video/wmv',
            'video/flv'
        ]);
    }

    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain'
        ]);
    }

    public function isAudio(): bool
    {
        return in_array($this->mime_type, [
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
            'audio/mp3'
        ]);
    }

    public function getFormattedSize(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getExtension(): string
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public function getIcon(): string
    {
        if ($this->isImage()) {
            return 'image';
        } elseif ($this->isVideo()) {
            return 'video';
        } elseif ($this->isAudio()) {
            return 'music';
        } elseif ($this->isDocument()) {
            return 'file-text';
        } else {
            return 'file';
        }
    }

    public function canPreview(): bool
    {
        return $this->isImage() || $this->isVideo();
    }

    public function getPreviewUrl(): string
    {
        if ($this->isImage()) {
            return $this->url;
        } elseif ($this->isVideo()) {
            return $this->url;
        } elseif ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        return '';
    }
}
