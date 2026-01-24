<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:manage-media');
    }

    public function index(Request $request): View
    {
        $query = MediaFile::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where('filename', 'like', "%{$request->search}%")
                  ->orWhere('alt_text', 'like', "%{$request->search}%");
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $mediaFiles = $query->latest()->paginate(24);

        $types = MediaFile::distinct()->pluck('type');

        return view('admin.media.index', compact('mediaFiles', 'types'));
    }

    public function upload(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'files.*' => 'required|file|max:10240', // Max 10MB per file
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);

        $uploadedFiles = [];

        foreach ($validated['files'] as $file) {
            $originalName = $file->getClientOriginalName();
            $filename = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('media', $filename, 'public');
            
            $type = $this->getFileType($file);
            $size = $file->getSize();
            $dimensions = $this->getImageDimensions($file);

            $mediaFile = MediaFile::create([
                'filename' => $filename,
                'original_name' => $originalName,
                'path' => $path,
                'type' => $type,
                'mime_type' => $file->getMimeType(),
                'size' => $size,
                'dimensions' => $dimensions,
                'alt_text' => $request->alt_text,
                'caption' => $request->caption,
                'category' => $request->category ?? 'general',
                'uploaded_by' => auth()->id(),
            ]);

            $uploadedFiles[] = $mediaFile;
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'تم رفع ' . count($uploadedFiles) . ' ملفات بنجاح');
    }

    public function show(MediaFile $mediaFile): View
    {
        return view('admin.media.show', compact('mediaFile'));
    }

    public function edit(MediaFile $mediaFile): View
    {
        return view('admin.media.edit', compact('mediaFile'));
    }

    public function update(Request $request, MediaFile $mediaFile): RedirectResponse
    {
        $validated = $request->validate([
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:100',
        ]);

        $mediaFile->update($validated);

        return redirect()->route('admin.media.show', $mediaFile)
            ->with('success', 'تم تحديث معلومات الملف بنجاح');
    }

    public function destroy(MediaFile $mediaFile): RedirectResponse
    {
        // Delete file from storage
        Storage::disk('public')->delete($mediaFile->path);
        
        $mediaFile->delete();

        return redirect()->route('admin.media.index')
            ->with('success', 'تم حذف الملف بنجاح');
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'files' => 'required|array',
            'files.*' => 'exists:media_files,id',
        ]);

        $mediaFiles = MediaFile::whereIn('id', $validated['files'])->get();

        foreach ($mediaFiles as $mediaFile) {
            Storage::disk('public')->delete($mediaFile->path);
            $mediaFile->delete();
        }

        return redirect()->route('admin.media.index')
            ->with('success', 'تم حذف ' . $mediaFiles->count() . ' ملفات بنجاح');
    }

    public function download(MediaFile $mediaFile)
    {
        return Storage::disk('public')->download($mediaFile->path, $mediaFile->original_name);
    }

    private function getFileType($file): string
    {
        $mimeType = $file->getMimeType();
        
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        } elseif (str_starts_with($mimeType, 'video/')) {
            return 'video';
        } elseif (str_starts_with($mimeType, 'audio/')) {
            return 'audio';
        } elseif (str_starts_with($mimeType, 'application/pdf')) {
            return 'document';
        } elseif (in_array($mimeType, ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])) {
            return 'document';
        } elseif (in_array($mimeType, ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])) {
            return 'spreadsheet';
        } elseif (in_array($mimeType, ['application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation'])) {
            return 'presentation';
        } else {
            return 'other';
        }
    }

    private function getImageDimensions($file): ?string
    {
        if (!str_starts_with($file->getMimeType(), 'image/')) {
            return null;
        }

        try {
            $imageInfo = getimagesize($file->getPathname());
            if ($imageInfo) {
                return $imageInfo[0] . 'x' . $imageInfo[1];
            }
        } catch (\Exception $e) {
            // Could not get image dimensions
        }

        return null;
    }
}
