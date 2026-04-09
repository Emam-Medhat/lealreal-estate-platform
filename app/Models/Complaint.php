<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Complaint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'complaintable_type',
        'complaintable_id',
        'type',
        'title',
        'description',
        'urgency_level',
        'expected_resolution',
        'contact_preference',
        'contact_details',
        'status',
        'reference_number',
        'assigned_to',
        'internal_notes',
        'resolution_details',
        'resolved_at',
        'closed_at',
        'last_activity_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'resolved_at',
        'closed_at',
        'last_activity_at'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complaintable(): MorphTo
    {
        return $this->morphTo();
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments()
    {
        return $this->hasMany(ComplaintAttachment::class);
    }

    public function responses()
    {
        return $this->hasMany(ComplaintResponse::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeEscalated($query)
    {
        return $query->where('status', 'escalated');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByUrgency($query, $urgency)
    {
        return $query->where('urgency_level', $urgency);
    }

    public function scopeAssigned($query)
    {
        return $query->whereNotNull('assigned_to');
    }

    public function scopeUnassigned($query)
    {
        return $query->whereNull('assigned_to');
    }

    // Methods
    public function getTypeText()
    {
        $types = [
            'service_quality' => 'جودة الخدمة',
            'property_issue' => 'مشكلة في العقار',
            'payment_dispute' => 'نزاع دفع',
            'communication' => 'مشكلة تواصل',
            'contract_violation' => 'انتهاك العقد',
            'safety_concern' => 'قضية أمان',
            'discrimination' => 'تمييز',
            'fraud' => 'احتيال',
            'other' => 'أخرى'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeIcon()
    {
        $icons = [
            'service_quality' => 'fas fa-concierge-bell',
            'property_issue' => 'fas fa-home',
            'payment_dispute' => 'fas fa-dollar-sign',
            'communication' => 'fas fa-comments',
            'contract_violation' => 'fas fa-file-contract',
            'safety_concern' => 'fas fa-shield-alt',
            'discrimination' => 'fas fa-ban',
            'fraud' => 'fas fa-exclamation-triangle',
            'other' => 'fas fa-star'
        ];

        return $icons[$this->type] ?? 'fas fa-star';
    }

    public function getUrgencyText()
    {
        $urgencies = [
            'low' => 'منخفض',
            'medium' => 'متوسط',
            'high' => 'مرتفع',
            'critical' => 'حرج'
        ];

        return $urgencies[$this->urgency_level] ?? $this->urgency_level;
    }

    public function getUrgencyColor()
    {
        $colors = [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red'
        ];

        return $colors[$this->urgency_level] ?? 'gray';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار المعالجة',
            'in_progress' => 'قيد المعالجة',
            'resolved' => 'تم الحل',
            'closed' => 'مغلق',
            'escalated' => 'مرفوع'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'closed' => 'gray',
            'escalated' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getContactPreferenceText()
    {
        $preferences = [
            'email' => 'البريد الإلكتروني',
            'phone' => 'الهاتف',
            'sms' => 'رسالة نصية',
            'whatsapp' => 'واتساب',
            'in_person' => 'شخصياً'
        ];

        return $preferences[$this->contact_preference] ?? $this->contact_preference;
    }

    public function getContactPreferenceIcon()
    {
        $icons = [
            'email' => 'fas fa-envelope',
            'phone' => 'fas fa-phone',
            'sms' => 'fas fa-sms',
            'whatsapp' => 'fab fa-whatsapp',
            'in_person' => 'fas fa-user'
        ];

        return $icons[$this->contact_preference] ?? 'fas fa-envelope';
    }

    public function getFormattedDate()
    {
        return $this->created_at->format('Y-m-days H:iREW
   , 'Y-m-d H:i');
    }

    public function getFormattedDateArabic()
    {
        return $this->created_at->locale('ar')->translatedFormat('d F Y');
    }

    public function getTimeAgo()
    {
        return $this->created_at->diffForHumans();
    }

    public function getResolutionTime()
    {
        if (!$this->resolved_at) {
            return null;
        }

        return $this->created_at->diffInDays($this->resolved_at);
    }

    public function getResolutionTimeText()
    {
        $days = $this->getResolutionTime();
        
        if (!$days) {
            return null;
        }

        if ($days === 0) {
            return 'في نفس اليوم';
        } elseif ($days === 1) {
            return 'يوم واحد';
        } elseif ($days <= 7) {
            return "{$days} أيام";
        } elseif ($days <= 30) {
            $weeks = round($days / 7);
            return "{$weeks} أسابيع";
        } else {
            $months = round($days / 30);
            return "{$months} أشهر";
        }
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isResolved()
    {
        return $this->status === 'resolved';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isEscalated()
    {
        return $this->status === 'escalated';
    }

    public function isAssigned()
    {
        return !is_null($this->assigned_to);
    }

    public function isOverdue()
    {
        if ($this->isResolved() || $this->isClosed()) {
            return false;
        }

        $overdueDays = match($this->urgency_level) {
            'critical' => 1,
            'high' => 3,
            'medium' => 7,
            'low' => 14,
            default => 7
        };

        return $this->created_at->lessThan(now()->subDays($overdueDays));
    }

    public function hasAttachments()
    {
        return $this->attachments()->count() > 0;
    }

    public function hasResponses()
    {
        return $this->responses()->count() > 0;
    }

    public function getLatestResponse()
    {
        return $this->responses()->latest()->first();
    }

    public function canBeEditedBy($user)
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }

    public function canBeAssignedTo($user)
    {
        return $user->isAdmin() || $user->hasRole(['support', 'manager']);
    }

    public function canBeRespondedBy($user)
    {
        return $this->assigned_to === $user->id || $user->isAdmin();
    }

    public function canBeResolvedBy($user)
    {
        return $this->assigned_to === $user->id || $user->isAdmin();
    }

    public function canBeEscalatedBy($user)
    {
        return $user->isAdmin();
    }

    public function getExcerpt($length = 100)
    {
        $description = strip_tags($this->description);
        return strlen($description) > $length ? substr($description, 0, $length) . '...' : $description;
    }

    public function getMetaDescription()
    {
        return "شكوى - {$this->title}";
    }

    public function getMetaKeywords()
    {
        return ['شكوى', 'دعم فني', 'مشكلة', $this->title, $this->getTypeText()];
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function assignTo($userId)
    {
        $this->update([
            'assigned_to' => $userId,
            'status' => 'in_progress',
            'last_activity_at' => now()
        ]);
    }

    public function resolve($resolutionDetails = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolution_details' => $resolutionDetails,
            'resolved_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function close()
    {
        $this->update([
            'status' => 'closed',
            'closed_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function escalate()
    {
        $this->update([
            'status' => 'escalated',
            'last_activity_at' => now()
        ]);
    }

    // Static methods
    public static function getByReferenceNumber($referenceNumber)
    {
        return self::where('reference_number', $referenceNumber)->first();
    }

    public static function getStatistics()
    {
        return [
            'total' => self::count(),
            'pending' => self::pending()->count(),
            'in_progress' => self::inProgress()->count(),
            'resolved' => self::resolved()->count(),
            'closed' => self::closed()->count(),
            'escalated' => self::escalated()->count(),
            'unassigned' => self::unassigned()->count(),
            'overdue' => self::whereHas('assignedTo')->get()->filter->isOverdue()->count(),
            'by_type' => self::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'by_urgency' => self::selectRaw('urgency_level, COUNT(*) as count')
                ->groupBy('urgency_level')
                ->get(),
            'resolution_rate' => self::resolved()->count() / self::count() * 100,
            'average_resolution_time' => self::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, resolved_at)) as avg_days')
                ->first()
        ];
    }

    protected static function booted()
    {
        static::created(function ($complaint) {
            // Generate reference number if not set
            if (!$complaint->reference_number) {
                $complaint->reference_number = 'CMP-' . date('Y') . '-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT);
                $complaint->save();
            }
        });

        static::updated(function ($complaint) {
            if ($complaint->wasChanged('status')) {
                $complaint->updateLastActivity();
            }
        });
    }
}
