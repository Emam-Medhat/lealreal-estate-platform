<?php

namespace App\Services;

use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyMediaService
{
    /**
     * Store a single media file for a property.
     */
    public function storeMedia(Property $property, UploadedFile $file, string $type, int $sortOrder = 0, bool $isFeatured = false): PropertyMedia
    {
        $directory = 'properties/' . Str::plural($type);
        $path = $file->store($directory, 'public');
        
        return PropertyMedia::create([
            'property_id' => $property->id,
            'type' => $type,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'sort_order' => $sortOrder,
            'is_featured' => $isFeatured,
        ]);
    }

    /**
     * Store multiple media files for a property.
     */
    public function storeMultipleMedia(Property $property, array $files, string $type): array
    {
        $results = [];
        $currentCount = $property->media()->where('type', $type)->count();

        foreach ($files as $index => $file) {
            $isFeatured = false;
            
            // If it's an image and it's the first one being added to a property with no images, make it featured
            if ($type === 'image' && $currentCount === 0 && $index === 0) {
                $isFeatured = true;
            }

            $results[] = $this->storeMedia($property, $file, $type, $currentCount + $index, $isFeatured);
        }

        return $results;
    }

    /**
     * Delete a media item.
     */
    public function deleteMedia(PropertyMedia $media): bool
    {
        if (Storage::disk('public')->exists($media->file_path)) {
            Storage::disk('public')->delete($media->file_path);
        }
        
        return $media->delete();
    }

    /**
     * Set a media item as featured.
     */
    public function setFeatured(PropertyMedia $media): bool
    {
        if ($media->type !== 'image') {
            return false;
        }

        $media->property->media()
            ->where('type', 'image')
            ->update(['is_featured' => false]);

        return $media->update(['is_featured' => true]);
    }
}
