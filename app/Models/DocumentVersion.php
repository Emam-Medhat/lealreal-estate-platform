<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentVersion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'document_id',
        'version_number',
        'content',
        'changes_summary',
        'version_type',
        'status',
        'created_by',
        'updated_by',
        'published_at',
        'published_by',
        'archived_at',
        'archived_by',
        'restored_from',
    ];

    protected $casts = [
        'content' => 'string',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function publishedBy()
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function archivedBy()
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function restoredFrom()
    {
        return $this->belongsTo(DocumentVersion::class, 'restored_from');
    }

    public function signatures()
    {
        return $this->hasMany(DocumentSignature::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('version_number', 'desc');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }

    public function canBeEdited(): bool
    {
        return $this->isDraft() || $this->isLatest();
    }

    public function isLatest(): bool
    {
        $latestVersion = $this->document->versions()->latest()->first();
        return $latestVersion && $latestVersion->id === $this->id;
    }

    public function getVersionTypeLabel(): string
    {
        return match($this->version_type) {
            'major' => 'رئيسي',
            'minor' => 'ثانوي',
            'patch' => 'تصحيح',
            default => 'غير محدد',
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'draft' => 'مسودة',
            'published' => 'منشور',
            'archived' => 'مؤرشف',
            default => 'غير محدد',
        };
    }

    public function getContentLength(): int
    {
        return strlen($this->content ?? '');
    }

    public function getContentWordCount(): int
    {
        return str_word_count($this->content ?? '');
    }

    public function getFormattedVersionNumber(): string
    {
        return 'v' . $this->version_number;
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
            'published_by' => auth()->id(),
        ]);
    }

    public function archive()
    {
        $this->update([
            'status' => 'archived',
            'archived_at' => now(),
            'archived_by' => auth()->id(),
        ]);
    }

    public function restore()
    {
        $this->update([
            'status' => 'draft',
            'archived_at' => null,
            'archived_by' => null,
        ]);
    }
}
