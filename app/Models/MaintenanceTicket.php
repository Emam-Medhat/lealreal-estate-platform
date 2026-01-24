<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'maintenance_request_id',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'assigned_to',
        'assigned_at',
        'assigned_by',
        'started_at',
        'completed_at',
        'closed_at',
        'closed_by',
        'reopened_at',
        'reopened_by',
        'reopened_reason',
        'resolution',
        'satisfaction_rating',
        'feedback',
        'created_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'closed_at' => 'datetime',
        'reopened_at' => 'datetime',
        'satisfaction_rating' => 'integer',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function reopenedBy()
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    public function replies()
    {
        return $this->hasMany(MaintenanceTicketReply::class);
    }

    public function attachments()
    {
        return $this->hasMany(MaintenanceTicketAttachment::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeAssigned($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeReopened($query)
    {
        return $query->where('status', 'reopened');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'عالي',
            'emergency' => 'طوارئ',
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'open' => 'مفتوحة',
            'assigned' => 'مكلفة',
            'in_progress' => 'قيد التنفيذ',
            'closed' => 'مغلقة',
            'reopened' => 'مفتوحة مرة أخرى',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getCategoryLabelAttribute()
    {
        $labels = [
            'bug' => 'خطأ',
            'feature' => 'ميزة',
            'request' => 'طلب',
            'info' => 'معلومات',
            'other' => 'أخرى',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'emergency' => 'red',
        ];

        return $colors[$this->priority] ?? 'gray';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'open' => 'gray',
            'assigned' => 'blue',
            'in_progress' => 'yellow',
            'closed' => 'green',
            'reopened' => 'orange',
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getSatisfactionRatingStarsAttribute()
    {
        if (!$this->satisfaction_rating) {
            return null;
        }

        return str_repeat('★', $this->satisfaction_rating) . str_repeat('☆', 5 - $this->satisfaction_rating);
    }

    public function canBeAssigned()
    {
        return $this->status === 'open';
    }

    public function canBeStarted()
    {
        return $this->status === 'assigned';
    }

    public function canBeClosed()
    {
        return $this->status === 'in_progress';
    }

    public function canBeReopened()
    {
        return $this->status === 'closed';
    }

    public function canBeReplied()
    {
        return !in_array($this->status, ['closed']);
    }

    public function getResponseTime()
    {
        if ($this->created_at && $this->assigned_at) {
            return $this->created_at->diffInMinutes($this->assigned_at);
        }
        
        return null;
    }

    public function getResolutionTime()
    {
        if ($this->created_at && $this->closed_at) {
            return $this->created_at->diffInMinutes($this->closed_at);
        }
        
        return null;
    }

    public function getHandlingTime()
    {
        if ($this->assigned_at && $this->closed_at) {
            return $this->assigned_at->diffInMinutes($this->closed_at);
        }
        
        return null;
    }

    public function getReopenCount()
    {
        return $this->reopened_at ? 1 : 0;
    }

    public function addReply($message, $userId = null)
    {
        return $this->replies()->create([
            'message' => $message,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    public function addAttachment($fileName, $filePath, $fileSize, $mimeType, $userId = null)
    {
        return $this->attachments()->create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

    public function getLatestReply()
    {
        return $this->replies()->latest()->first();
    }

    public function getReplyCount()
    {
        return $this->replies()->count();
    }

    public function getAttachmentCount()
    {
        return $this->attachments()->count();
    }

    public function isOverdue()
    {
        // Define overdue criteria (e.g., 24 hours for normal priority, 4 hours for high priority)
        $overdueHours = $this->priority === 'emergency' ? 4 : 
                       ($this->priority === 'high' ? 8 : 24);
        
        return $this->status !== 'closed' && 
               $this->created_at->diffInHours(now()) > $overdueHours;
    }

    public function getUrgencyLevel()
    {
        if ($this->priority === 'emergency') {
            return 'critical';
        } elseif ($this->priority === 'high') {
            return 'high';
        } elseif ($this->isOverdue()) {
            return 'medium';
        } else {
            return 'low';
        }
    }
}
