<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectNote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'noteable_type',
        'noteable_id',
        'content',
        'note_type',
        'is_private',
        'author_id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function noteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('note_type', $type);
    }

    public function scopeByAuthor($query, $authorId)
    {
        return $query->where('author_id', $authorId);
    }

    public function isPublic()
    {
        return !$this->is_private;
    }

    public function isPrivateNote()
    {
        return $this->is_private;
    }

    public function getExcerpt($length = 100)
    {
        return str_limit(strip_tags($this->content), $length);
    }

    public function getFormattedDate()
    {
        return $this->created_at->format('Y-m-d H:i');
    }

    // Static methods for note types
    public static function getNoteTypes()
    {
        return [
            'general' => 'عام',
            'meeting' => 'اجتماع',
            'decision' => 'قرار',
            'issue' => 'مشكلة',
            'update' => 'تحديث',
            'reminder' => 'تذكير',
            'action' => 'إجراء',
        ];
    }
}
