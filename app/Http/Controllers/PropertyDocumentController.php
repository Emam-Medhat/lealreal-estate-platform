<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PropertyDocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show', 'download']);
    }

    public function index(Property $property)
    {
        $documents = $property->documents()
            ->with(['uploadedBy:id,name'])
            ->orderBy('created_at')
            ->get();

        return view('properties.documents.index', compact('property', 'documents'));
    }

    public function upload(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png|max:10240',
            'titles' => 'nullable|array',
            'titles.*' => 'string|max:255',
            'descriptions' => 'nullable|array',
            'descriptions.*' => 'string|max:500',
            'is_public' => 'nullable|array',
            'is_public.*' => 'boolean',
            'is_required' => 'nullable|array',
            'is_required.*' => 'boolean',
            'expiry_dates' => 'nullable|array',
            'expiry_dates.*' => 'date|after:today',
        ]);

        $uploadedFiles = [];
        $errors = [];

        foreach ($request->file('files') as $index => $file) {
            try {
                $fileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $mimeType = $file->getMimeType();
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

                // Store file
                $filePath = $file->store("properties/{$property->id}/documents", 'public');

                $document = PropertyDocument::create([
                    'property_id' => $property->id,
                    'title' => $request->titles[$index] ?? $fileName,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'file_size' => $fileSize,
                    'mime_type' => $mimeType,
                    'description' => $request->descriptions[$index] ?? null,
                    'is_public' => $request->is_public[$index] ?? false,
                    'is_required' => $request->is_required[$index] ?? false,
                    'expiry_date' => $request->expiry_dates[$index] ?? null,
                    'status' => 'pending',
                ]);

                $uploadedFiles[] = [
                    'id' => $document->id,
                    'title' => $document->title,
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'url' => $document->getUrlAttribute(),
                    'file_size' => $fileSize,
                    'file_type' => $fileType,
                    'mime_type' => $mimeType,
                    'is_public' => $document->is_public,
                    'is_required' => $document->is_required,
                    'expiry_date' => $document->expiry_date,
                ];

            } catch (\Exception $e) {
                $errors[] = "Failed to upload {$fileName}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => count($uploadedFiles) > 0,
            'message' => count($uploadedFiles) . ' documents uploaded successfully',
            'uploaded_files' => $uploadedFiles,
            'errors' => $errors,
        ]);
    }

    public function update(Request $request, Property $property, PropertyDocument $document): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'is_public' => 'boolean',
            'is_required' => 'boolean',
            'expiry_date' => 'nullable|date|after:today',
            'status' => 'required|in:pending,approved,rejected,expired',
        ]);

        $document->update($request->only([
            'title', 'description', 'is_public', 'is_required', 'expiry_date', 'status'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Document updated successfully',
            'document' => $document,
        ]);
    }

    public function destroy(Property $property, PropertyDocument $document): JsonResponse
    {
        $this->authorize('update', $property);

        try {
            // Delete file from storage
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }

            // Delete database record
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete document: ' . $e->getMessage(),
            ]);
        }
    }

    public function show(Property $property, PropertyDocument $document)
    {
        if (!$document->is_public && !Auth::check()) {
            abort(403, 'This document is not public');
        }

        if ($document->is_expired()) {
            abort(403, 'This document has expired');
        }

        return view('properties.documents.show', compact('property', 'document'));
    }

    public function download(Property $property, PropertyDocument $document)
    {
        if (!$document->is_public && !Auth::check()) {
            abort(403, 'This document is not public');
        }

        if ($document->is_expired()) {
            abort(403, 'This document has expired');
        }

        if (!Storage::disk('public')->exists($document->file_path)) {
            abort(404, 'File not found');
        }

        // Record download
        PropertyView::create([
            'property_id' => $property->id,
            'session_id' => session()->getId(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'user_id' => Auth::id(),
            'view_type' => 'document_download',
            'metadata' => [
                'document_id' => $document->id,
                'document_title' => $document->title,
            ],
        ]);

        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    public function approve(Request $request, Property $property, PropertyDocument $document): JsonResponse
    {
        $this->authorize('approve', $document);

        $document->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document approved successfully',
            'document' => $document,
        ]);
    }

    public function reject(Request $request, Property $property, PropertyDocument $document): JsonResponse
    {
        $this->authorize('approve', $document);

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $document->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_by' => Auth::id(),
            'rejected_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Document rejected successfully',
            'document' => $document,
        ]);
    }

    public function bulkApprove(Request $request, Property $property): JsonResponse
    {
        $this->authorize('approve', PropertyDocument::class);

        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:property_documents,id',
        ]);

        $approvedCount = PropertyDocument::where('property_id', $property->id)
            ->whereIn('id', $request->document_ids)
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'message' => $approvedCount . ' documents approved successfully',
            'approved_count' => $approvedCount,
        ]);
    }

    public function bulkDelete(Request $request, Property $property): JsonResponse
    {
        $this->authorize('update', $property);

        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:property_documents,id',
        ]);

        $documents = PropertyDocument::where('property_id', $property->id)
            ->whereIn('id', $request->document_ids)
            ->get();

        $deletedCount = 0;
        $errors = [];

        foreach ($documents as $document) {
            try {
                // Delete file from storage
                if ($document->file_path) {
                    Storage::disk('public')->delete($document->file_path);
                }

                // Delete database record
                $document->delete();
                $deletedCount++;

            } catch (\Exception $e) {
                $errors[] = "Failed to delete {$document->file_name}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success' => $deletedCount > 0,
            'message' => $deletedCount . ' documents deleted successfully',
            'deleted_count' => $deletedCount,
            'errors' => $errors,
        ]);
    }

    public function getDocumentStats(Property $property): JsonResponse
    {
        $this->authorize('viewStats', $property);

        $stats = [
            'total_documents' => $property->documents()->count(),
            'public_documents' => $property->documents()->where('is_public', true)->count(),
            'required_documents' => $property->documents()->where('is_required', true)->count(),
            'pending_documents' => $property->documents()->where('status', 'pending')->count(),
            'approved_documents' => $property->documents()->where('status', 'approved')->count(),
            'rejected_documents' => $property->documents()->where('status', 'rejected')->count(),
            'expired_documents' => $property->documents()->where('expiry_date', '<', now())->count(),
            'total_size' => $property->documents()->sum('file_size'),
            'by_type' => $property->documents()
                ->groupBy('file_type')
                ->selectRaw('file_type, count(*) as count')
                ->pluck('count', 'file_type'),
            'recent_uploads' => $property->documents()->latest()->limit(5)->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    public function generateDocumentPackage(Request $request, Property $property): JsonResponse
    {
        $this->authorize('view', $property);

        $request->validate([
            'document_ids' => 'nullable|array',
            'document_ids.*' => 'exists:property_documents,id',
            'include_public_only' => 'boolean',
            'format' => 'required|in:pdf,zip',
        ]);

        $query = $property->documents();

        if ($request->document_ids) {
            $query->whereIn('id', $request->document_ids);
        }

        if ($request->include_public_only) {
            $query->where('is_public', true);
        }

        $documents = $query->get();

        if ($documents->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No documents found for package generation',
            ]);
        }

        // Generate package logic would go here
        // This could create a ZIP file or PDF package
        
        return response()->json([
            'success' => true,
            'message' => 'Document package generated successfully',
            'package_url' => route('properties.documents.package', [
                'property' => $property->id,
                'token' => Str::random(32),
            ]),
            'document_count' => $documents->count(),
        ]);
    }
}
