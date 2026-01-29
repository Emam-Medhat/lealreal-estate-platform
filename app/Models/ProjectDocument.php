<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class ProjectDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'category',
        'status',
        'document_date',
        'expiry_date',
        'tags',
        'notes',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'document_date' => 'date',
        'expiry_date' => 'date',
        'tags' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function getSizeInKB()
    {
        return round($this->size / 1024, 2);
    }

    public function getSizeInMB()
    {
        return round($this->size / 1024 / 1024, 2);
    }

    public function getFileExtension()
    {
        return pathinfo($this->filename, PATHINFO_EXTENSION);
    }

    public function isImage()
    {
        return in_array($this->mime_type, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ]);
    }

    public function isPDF()
    {
        return $this->mime_type === 'application/pdf';
    }

    public function isDocument()
    {
        return in_array($this->mime_type, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ]);
    }

    public function getDownloadUrl()
    {
        return Storage::url($this->path);
    }

    public function approve($approvedBy = null)
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);
    }

    public function reject($approvedBy = null)
    {
        $this->update([
            'status' => 'rejected',
            'approved_by' => $approvedBy,
        ]);
    }

    public function createNewVersion($newFile, $uploadedBy = null)
    {
        // Store new file
        $path = $newFile->store('project-documents', 'public');
        
        // Create new version
        return static::create([
            'documentable_type' => $this->documentable_type,
            'documentable_id' => $this->documentable_id,
            'filename' => $newFile->getClientOriginalName(),
            'original_filename' => $this->original_filename,
            'path' => $path,
            'size' => $newFile->getSize(),
            'mime_type' => $newFile->getMimeType(),
            'description' => $this->description,
            'version' => $this->version + 1,
            'status' => 'pending',
            'uploaded_by' => $uploadedBy,
        ]);
    }
}
