<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyMedia; // Assuming this model matches
// use Intervention\Image\Facades\Image; // Use if available

class PropertyMediaService
{
    /**
     * Upload property images.
     *
     * @param mixed $propertyId
     * @param array $files
     * @return array
     */
    public function uploadImages($propertyId, array $files): array
    {
        $uploaded = [];
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                // $path = $file->store('properties', 'public');
                // Store file (mocking storage path for now or real storage)
                $path = $file->store('properties/' . $propertyId, 'public');

                // Create PropertyMedia record
                // $media = PropertyMedia::create([...]);

                $uploaded[] = $path;

                // dispatch OptimizeImage job
            }
        }
        return $uploaded;
    }

    /**
     * Upload property video.
     *
     * @param mixed $propertyId
     * @param UploadedFile $file
     * @return string|null
     */
    public function uploadVideo($propertyId, UploadedFile $file)
    {
        return $file->store('properties/' . $propertyId . '/videos', 'public');
    }

    public function generateThumbnails()
    {
        // Logic using intervention/image
    }

    public function optimizeImages()
    {
        // Spatie ImageOptimizer or similar
    }
}
