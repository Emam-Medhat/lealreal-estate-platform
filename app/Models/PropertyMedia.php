<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyMedia extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_id', 'media_type', 'file_name', 'file_path', 
        'file_size', 'file_type', 'is_primary', 'is_featured', 
        'uploaded_by', 'description', 'sort_order',
        'created_at', 'updated_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
        'file_size' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    // Scopes
    public function scopeImages($query)
    {
        return $query->where('media_type', 'image');
    }

    public function scopeVideos($query)
    {
        return $query->where('media_type', 'video');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByProperty($query, $propertyId)
    {
        return $query->where('property_id', $propertyId);
    }

    // Helper Methods
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->media_type === 'image') {
            return $this->getUrlAttribute();
        }
        
        return asset('images/default-video-thumbnail.jpg');
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }

    public function setAsPrimary(): void
    {
        // Remove primary from other images of this property
        PropertyMedia::where('property_id', $this->property_id)
            ->where('id', '!=', $this->id)
            ->update(['is_primary' => false]);
        
        $this->update(['is_primary' => true]);
    }

    public function setAsFeatured(): void
    {
        $this->update(['is_featured' => true]);
    }

    public function removeAsFeatured(): void
    {
        $this->update(['is_featured' => false]);
    }
}
