<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyMedia;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PropertyMediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Property $property)
    {
        $media = $property->media()
            ->with(['uploadedBy:id,name'])
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        return view('properties.media.index', compact('property', 'media'));
    }

    public function upload(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,jpg,png,gif,mp4,avi,mov,pdf,doc,docx|max:10240',
            'media_type' => 'required|in:image,video,document',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'string|max:500',
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            try {
                $mediaType = $request->media_type;
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();

                // Determine storage path
                $storagePath = "properties/{$property->id}/{$mediaType}s";
                $filePath = $file->store($storagePath, 'public');

                // Get image dimensions if it's an image
                $width = null;
                $height = null;
                if ($mediaType === 'image' && in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif'])) {
                    $imageInfo = getimagesize($file->getPathname());
                    $width = $imageInfo[0] ?? null;
                    $height = $imageInfo[1] ?? null;
                }

                // Create thumbnail for images
                $thumbnailPath = null;
                if ($mediaType === 'image') {
                    $thumbnailPath = $this->createThumbnail($file, $storagePath);
                }

                $media = PropertyMedia::create([
                    'property_id' => $property->id,
                    'media_type' => $mediaType,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'width' => $width,
                    'height' => $height,
                    'thumbnail_path' => $thumbnailPath,
                    'description' => $request->descriptions[$index] ?? null,
                    'is_primary' => false, // Will be set later if needed
                    'sort_order' => $property->media()->count() + $index,
                    'uploaded_by' => Auth::id(),
                ]);

                $uploadedFiles[] = [
                    'id' => $media->id,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'url' => $media->getUrlAttribute(),
                    'thumbnail_url' => $media->getThumbnailUrlAttribute(),
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'media_type' => $mediaType,
                ];

            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$fileName}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'message' => count($uploadedFiles) . ' files uploaded successfully',
            'uploaded_files' => $uploadedFiles,
            'errors' => $errors,
        ]);
    }

    public function update(Request $request, Property $property, PropertyMedia $media): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'description' => 'nullable|string|max:500',
            'is_primary' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        // If setting as primary, remove primary from other media
        if ($request->is_primary) {
            $property->media()->where('id', '!=', $media->id)->update(['is_primary' => false]);
        }

        $media->update($request->only(['description', 'is_primary', 'sort_order']));

        return response()->json([
            'success' => true,
            'message' => 'Media updated successfully',
            'media' => $media,
        ]);
    }

    public function destroy(Property $property, PropertyMedia $media): JsonResponse
    {
        $this->authorize('update', $property);

        try {
            // Delete files from storage
            if ($media->file_path) {
                Storage::disk('public')->delete($media->file_path);
            }

            if ($media->thumbnail_path) {
                Storage::disk('public')->delete($media->thumbnail_path);
            }

            // Delete database record
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete media: ' . $e->getMessage(),
            ]);
        }
    }

    public function reorder(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:property_media,id',
        ]);

        foreach ($request->media_ids as $index => $mediaId) {
            PropertyMedia::where('id', $mediaId)
                ->where('property_id', $property->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Media reordered successfully',
        ]);
    }

    public function setPrimary(Request $request, Property $property, PropertyMedia $media): JsonResponse
    {
        $this->authorize('update', $property);

        // Remove primary from all other media
        $property->media()->where('id', '!=', $media->id)->update(['is_primary' => false]);

        // Set this as primary
        $media->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image set successfully',
            'media' => $media,
        ]);
    }

    public function bulkDelete(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'media_ids' => 'required|array',
            'media_ids.*' => 'exists:property_media,id',
        ]);

        $mediaItems = PropertyMedia::where('property_id', $property->id)
            ->whereIn('id', $request->media_ids)
            ->get();

        $deletedCount = 0;
        $errors = [];

        foreach ($mediaItems as $media) {
            try {
                // Delete files from storage
                if ($media->file_path) {
                    Storage::disk('public')->delete($media->file_path);
                }

                if ($media->thumbnail_path) {
                    Storage::disk('public')->delete($media->thumbnail_path);
                }

                // Delete database record
                $media->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to delete {$media->file_name}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $deletedCount > 0,
            'message' => $deletedCount . ' media files deleted successfully',
            'deleted_count' => $deletedCount,
            'errors' => $errors,
        ]);
    }

    public function gallery(Property $property)
    {
        $media = $property->media()
            ->where('media_type', 'image')
            ->orderBy('sort_order')
            ->orderBy('created_at')
            ->get();

        return view('properties.media.gallery', compact('property', 'media'));
    }

    public function download(Property $property, PropertyMedia $media)
    {
        $this->authorize('view', $property);

        if (!Storage::disk('public')->exists($media->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::disk('public')->download($media->file_path, $media->file_name);
    }

    public function getMediaStats(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $stats = [
            'total_media' => $property->media()->count(),
            'images' => $property->media()->where('media_type', 'image')->count(),
            'videos' => $property->media()->where('media_type', 'video')->count(),
            'documents' => $property->media()->where('media_type', 'document')->count(),
            'total_size' => $property->media()->sum('file_size'),
            'primary_image' => $property->media()->where('media_type', 'image')->where('is_primary', true)->first(),
            'recent_uploads' => $property->media()->latest()->limit(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    private function createThumbnail($file, string $storagePath): ?string
    {
        try {
            // This would use an image manipulation library like Intervention Image
            // For now, return null as placeholder
            $thumbnailPath = $storagePath . '/thumbnails/' . Str::random(16) . '.jpg';
            
            // Create thumbnail logic would go here
            // Image::make($file)->resize(300, 200)->save(storage_path('app/public/' . $thumbnailPath));
            
            return $thumbnailPath;
        } catch (\Exception $e) {
            return null;
        }
    }
}
