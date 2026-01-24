<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\User;

class Dispute extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'initiator_id',
        'respondent_id',
        'disputable_type',
        'disputable_id',
        'type',
        'title',
        'description',
        'dispute_amount',
        'desired_outcome',
        'evidence_description',
        'preferred_resolution_method',
        'willing_to_mediate',
        'status',
        'reference_number',
        'mediator_id',
        'mediation_started_at',
        'resolution_method',
        'resolution_details',
        'resolution_amount',
        'agreement_terms',
        'escalated_at',
        'resolved_at',
        'closed_at',
        'last_activity_at'
    ];

    protected $casts = [
        'dispute_amount' => 'decimal:2',
        'resolution_amount' => 'decimal:2',
        'willing_to_mediate' => 'boolean',
        'mediation_started_at' => 'datetime',
        'escalated_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'last_activity_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'mediation_started_at',
        'escalated_at',
        'resolved_at',
        'closed_at',
        'last_activity_at'
    ];

    // Relationships
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_id');
    }

    public function respondent()
    {
        return $this->belongsTo(User::class, 'respondent_id');
    }

    public function disputable(): MorphTo
    {
        return $this->morphTo();
    }

    public function mediator()
    {
        return $this->belongsTo(User::class, 'mediator_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeNegotiation($query)
    {
        return $query->where('status', 'negotiation');
    }

    public function scopeMediation($query)
    {
        return $query->where('status', 'mediation');
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

    public function scopeByUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('initiator_id', $userId)
              ->orWhere('respondent_id', $userId);
        });
    }

    public function scopeWithMediator($query)
    {
        return $query->whereNotNull('mediator_id');
    }

    // Methods
    public function getTypeText()
    {
        $types = [
            'breach_of_contract' => 'خرق العقد',
            'payment_dispute' => 'نزاع دفع',
            'property_condition' => 'حالة العقار',
            'service_quality' => 'جودة الخدمة',
            'misrepresentation' => 'معلومات مضللة',
            'timeline_delays' => 'تأخيرات في الجدول الزمني',
            'deposit_return' => 'استرداد العربون',
            'other' => 'أخرى'
        ];

        return $types[$this->type] ?? $this->type;
    }

    public function getTypeIcon()
    {
        $icons = [
            'breach_of_contract' => 'fas fa-file-contract',
            'payment_dispute' => 'fas fa-dollar-sign',
            'property_condition' => 'fas fa-home',
            'service_quality' => 'fas fa-concierge-bell',
            'misrepresentation' => 'fas fa-exclamation-triangle',
            'timeline_delays' => 'fas fa-clock',
            'deposit_return' => 'fas fa-money-bill',
            'other' => 'fas fa-star'
        ];

        return $icons[$this->type] ?? 'fas fa-star';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار الرد',
            'negotiation' => 'قيد التفاوض',
            'mediation' => 'قيد الوساطة',
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
            'negotiation' => 'blue',
            'mediation' => 'purple',
            'resolved' => 'green',
            'closed' => 'gray',
            'escalated' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getResolutionMethodText()
    {
        $methods = [
            'negotiation' => 'تفاوض مباشر',
            'mediation' => 'وساطة',
            'arbitration' => 'تحكيم',
            'legal_action' => 'إجراء قانوني',
            'settlement' => 'تسوية',
            'agreement' => 'اتفاق'
        ];

        return $methods[$this->resolution_method] ?? $this->resolution_method;
    }

    public function getResolutionMethodIcon()
    {
        $icons = [
            'negotiation' => 'fas fa-handshake',
            'mediation' => 'fas fa-users',
            'arbitration' => 'fas fa-gavel',
            'legal_action' => 'fas fa-balance-scale',
            'settlement' => 'fas fa-file-signature',
            'agreement' => 'fas fa-check-circle'
        ];

        return $icons[$this->resolution_method] ?? 'fas fa-check-circle';
    }

    public function getFormattedDate()
    {
        return $this->created_at->format('Y-m-d H:i');
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

    public function isInNegotiation()
    {
        return $this->status === 'negotiation';
    }

    public function isInMediation()
    {
        return $this->status === 'mediation';
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

    public function hasMediator()
    {
        return !is_null($this->mediator_id);
    }

    public function hasEvidence()
    {
        return $this->evidenceFiles()->count() > 0;
    }

    public function hasResponses()
    {
        return $this->responses()->count() > 0;
    }

    public function hasMediationSessions()
    {
        return $this->mediationSessions()->count() > 0;
    }

    public function getLatestResponse()
    {
        return $this->responses()->latest()->first();
    }

    public function getInitiatorResponse()
    {
        return $this->responses()->where('user_id', $this->initiator_id)->first();
    }

    public function getRespondentResponse()
    {
        return $this->responses()->where('user_id', $this->respondent_id)->first();
    }

    public function canBeRespondedBy($user)
    {
        return $this->respondent_id === $user->id || $user->isAdmin();
    }

    public function canBeMediatedBy($user)
    {
        return $this->mediator_id === $user->id || $user->hasRole(['mediator', 'admin']);
    }

    public function canBeEscalatedBy($user)
    {
        return $user->isAdmin();
    }

    public function canBeResolvedBy($user)
    {
        return $this->mediator_id === $user->id || $user->isAdmin();
    }

    public function canBeClosedBy($user)
    {
        return $user->isAdmin();
    }

    public function getOtherParty($userId)
    {
        if ($this->initiator_id === $userId) {
            return $this->respondent;
        } elseif ($this->respondent_id === $userId) {
            return $this->initiator;
        }

        return null;
    }

    public function isInitiator($userId)
    {
        return $this->initiator_id === $userId;
    }

    public function isRespondent($userId)
    {
        return $this->respondent_id === $userId;
    }

    public function getExcerpt($length = 100)
    {
        $description = strip_tags($this->description);
        return strlen($description) > $length ? substr($description, 0, $length) . '...' : $description;
    }

    public function getFormattedDisputeAmount()
    {
        return $this->dispute_amount ? number_format($this->dispute_amount, 2) . ' ريال' : 'غير محدد';
    }

    public function getFormattedResolutionAmount()
    {
        return $this->resolution_amount ? number_format($this->resolution_amount, 2) . ' ريال' : 'غير محدد';
    }

    public function getMetaDescription()
    {
        return "نزاع - {$this->title}";
    }

    public function getMetaKeywords()
    {
        return ['نزاع', 'خلاف', 'وساطة', $this->title, $this->getTypeText()];
    }

    public function updateLastActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }

    public function assignMediator($mediatorId)
    {
        $this->update([
            'mediator_id' => $mediatorId,
            'status' => 'mediation',
            'mediation_started_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function escalate()
    {
        $this->update([
            'status' => 'escalated',
            'escalated_at' => now(),
            'last_activity_at' => now()
        ]);
    }

    public function resolve($method, $details = null, $amount = null, $terms = null)
    {
        $this->update([
            'status' => 'resolved',
            'resolution_method' => $method,
            'resolution_details' => $details,
            'resolution_amount' => $amount,
            'agreement_terms' => $terms,
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
            'negotiation' => self::negotiation()->count(),
            'mediation' => self::mediation()->count(),
            'resolved' => self::resolved()->count(),
            'closed' => self::closed()->count(),
            'escalated' => self::escalated()->count(),
            'by_type' => self::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get(),
            'resolution_methods' => self::whereNotNull('resolution_method')
                ->selectRaw('resolution_method, COUNT(*) as count')
                ->groupBy('resolution_method')
                ->get(),
            'resolution_rate' => self::resolved()->count() / self::count() * 100,
            'average_resolution_time' => self::whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(DAY, created_at, resolved_at)) as avg_days')
                ->first(),
            'total_dispute_amount' => self::sum('dispute_amount'),
            'total_resolution_amount' => self::sum('resolution_amount')
        ];
    }

    protected static function booted()
    {
        static::created(function ($dispute) {
            // Generate reference number if not set
            if (!$dispute->reference_number) {
                $dispute->reference_number = 'DSP-' . date('Y') . '-' . str_pad($dispute->id, 6, '0', STR_PAD_LEFT);
                $dispute->save();
            }
        });

        static::updated(function ($dispute) {
            if ($dispute->wasChanged('status')) {
                $dispute->updateLastActivity();
            }
        });
    }
}
