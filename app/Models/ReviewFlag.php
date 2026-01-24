<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ReviewFlag extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'response_id',
        'user_id',
        'reason',
        'description',
        'status',
        'reviewed_by',
        'reviewed_at',
        'action_taken'
    ];

    protected $casts = [
        'reviewed_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'reviewed_at'
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function response()
    {
        return $this->belongsTo(ReviewResponse::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', 'reviewed');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForReview($query, $reviewId)
    {
        return $query->where('review_id', $reviewId);
    }

    public function scopeForResponse($query, $responseId)
    {
        return $query->where('response_id', $responseId);
    }

    public function scopeByReason($query, $reason)
    {
        return $query->where('reason', $reason);
    }

    // Methods
    public function getReasonText()
    {
        $reasons = [
            'inappropriate_content' => 'محتوى غير لائق',
            'spam' => 'رسائل مزعجة',
            'fake_review' => 'تقييم مزيف',
            'offensive_language' => 'لغة مسيئة',
            'personal_attack' => 'هجوم شخصي',
            'discrimination' => 'تمييز',
            'false_information' => 'معلومات كاذبة',
            'copyright_violation' => 'انتهاك حقوق الطبع والنشر',
            'privacy_violation' => 'انتهاك الخصوصية',
            'harassment' => 'مضايقة',
            'violence' => 'عنف',
            'hate_speech' => 'خطاب كراهية',
            'other' => 'أخرى'
        ];

        return $reasons[$this->reason] ?? $this->reason;
    }

    public function getReasonIcon()
    {
        $icons = [
            'inappropriate_content' => 'fas fa-exclamation-triangle',
            'spam' => 'fas fa-ban',
            'fake_review' => 'fas fa-fake',
            'offensive_language' => 'fas fa-language',
            'personal_attack' => 'fas fa-user-slash',
            'discrimination' => 'fas fa-ban',
            'false_information' => 'fas fa-times-circle',
            'copyright_violation' => 'fas fa-copyright',
            'privacy_violation' => 'fas fa-lock',
            'harassment' => 'fas fa-user-times',
            'violence' => 'fas fa-fist-raised',
            'hate_speech' => 'fas fa-comment-slash',
            'other' => 'fas fa-flag'
        ];

        return $icons[$this->reason] ?? 'fas fa-flag';
    }

    public function getStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار المراجعة',
            'reviewed' => 'تمت المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'approved' => 'green',
            'rejected' => 'red'
        ];

        return $colors[$this->status] ?? 'gray';
    }

    public function getActionTakenText()
    {
        $actions = [
            'removed' => 'تم الحذف',
            'edited' => 'تم التعديل',
            'hidden' => 'تم الإخفاء',
            'warned' => 'تم التحذير',
            'suspended' => 'تم تعليق الحساب',
            'banned' => 'تم حظر الحساب',
            'no_action' => 'لم يتم اتخاذ إجراء'
        ];

        return $actions[$this->action_taken] ?? $this->action_taken;
    }

    public function getActionTakenIcon()
    {
        $icons = [
            'removed' => 'fas fa-trash',
            'edited' => 'fas fa-edit',
            'hidden' => 'fas fa-eye-slash',
            'warned' => 'fas fa-exclamation',
            'suspended' => 'fas fa-pause',
            'banned' => 'fas fa-ban',
            'no_action' => 'fas fa-check'
        ];

        return $icons[$this->action_taken] ?? 'fas fa-check';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isReviewed()
    {
        return $this->status === 'reviewed';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isForReview()
    {
        return !is_null($this->review_id);
    }

    public function isForResponse()
    {
        return !is_null($this->response_id);
    }

    public function getFlaggedItem()
    {
        if ($this->isForReview()) {
            return $this->review;
        } elseif ($this->isForResponse()) {
            return $this->response;
        }

        return null;
    }

    public function getFlaggedItemType()
    {
        if ($this->isForReview()) {
            return 'review';
        } elseif ($this->isForResponse()) {
            return 'response';
        }

        return null;
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

    public function getReviewedDate()
    {
        return $this->reviewed_at ? $this->reviewed_at->format('Y-m-d H:i') : null;
    }

    public function getReviewedDateArabic()
    {
        return $this->reviewed_at ? $this->reviewed_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getReviewTime()
    {
        if (!$this->reviewed_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->reviewed_at);
    }

    public function getReviewTimeText()
    {
        $hours = $this->getReviewTime();
        
        if (!$hours) {
            return null;
        }

        if ($hours < 1) {
            return 'أقل من ساعة';
        } elseif ($hours < 24) {
            return "{$hours} ساعات";
        } elseif ($hours < 168) { // 7 days
            $days = round($hours / 24);
            return "{$days} أيام";
        } else {
            $weeks = round($hours / 168);
            return "{$weeks} أسابيع";
        }
    }

    public function canBeReviewedBy($user)
    {
        return $user->isAdmin() && $this->isPending();
    }

    public function approve($actionTaken = null, $reviewerId = null)
    {
        $this->update([
            'status' => 'approved',
            'action_taken' => $actionTaken,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'reviewed_at' => now()
        ]);
    }

    public function reject($reviewerId = null)
    {
        $this->update([
            'status' => 'rejected',
            'action_taken' => 'no_action',
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'reviewed_at' => now()
        ]);
    }

    public function reviewAction($actionTaken = null, $reviewerId = null)
    {
        $this->update([
            'status' => 'reviewed',
            'action_taken' => $actionTaken,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'reviewed_at' => now()
        ]);
    }

    public function getExcerpt($length = 100)
    {
        $description = strip_tags($this->description);
        return strlen($description) > $length ? substr($description, 0, $length) . '...' : $description;
    }

    public function getMetaDescription()
    {
        $itemType = $this->getFlaggedItemType() === 'review' ? 'تقييم' : 'رد';
        return "إبلاغ عن {$itemType} - {$this->getReasonText()}";
    }

    public function getMetaKeywords()
    {
        return ['إبلاغ', 'حجز', $this->getReasonText(), 'تقييم', 'رد'];
    }

    // Static methods
    public static function getReasons()
    {
        return [
            'inappropriate_content' => 'محتوى غير لائق',
            'spam' => 'رسائل مزعجة',
            'fake_review' => 'تقييم مزيف',
            'offensive_language' => 'لغة مسيئة',
            'personal_attack' => 'هجوم شخصي',
            'discrimination' => 'تمييز',
            'false_information' => 'معلومات كاذبة',
            'copyright_violation' => 'انتهاك حقوق الطبع والنشر',
            'privacy_violation' => 'انتهاك الخصوصية',
            'harassment' => 'مضايقة',
            'violence' => 'عنف',
            'hate_speech' => 'خطاب كراهية',
            'other' => 'أخرى'
        ];
    }

    public static function getActions()
    {
        return [
            'removed' => 'تم الحذف',
            'edited' => 'تم التعديل',
            'hidden' => 'تم الإخفاء',
            'warned' => 'تم التحذير',
            'suspended' => 'تم تعليق الحساب',
            'banned' => 'تم حظر الحساب',
            'no_action' => 'لم يتم اتخاذ إجراء'
        ];
    }

    public static function getStatistics()
    {
        return [
            'total_flags' => self::count(),
            'pending_flags' => self::pending()->count(),
            'reviewed_flags' => self::reviewed()->count(),
            'approved_flags' => self::approved()->count(),
            'rejected_flags' => self::rejected()->count(),
            'by_reason' => self::selectRaw('reason, COUNT(*) as count')
                ->groupBy('reason')
                ->get(),
            'by_status' => self::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get(),
            'by_action' => self::whereNotNull('action_taken')
                ->selectRaw('action_taken, COUNT(*) as count')
                ->groupBy('action_taken')
                ->get(),
            'average_review_time' => self::whereNotNull('reviewed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, reviewed_at)) as avg_hours')
                ->first()
        ];
    }

    public static function getFlaggedItemsCount($type = null)
    {
        $query = self::query();

        if ($type === 'review') {
            $query->whereNotNull('review_id');
        } elseif ($type === 'response') {
            $query->whereNotNull('response_id');
        }

        return $query->count();
    }

    protected static function booted()
    {
        static::created(function ($flag) {
            // Notify admins about new flag
            User::where('role', 'admin')->get()->each(function($admin) use ($flag) {
                $admin->notifications()->create([
                    'type' => 'review_flagged',
                    'title' => 'إبلاغ جديد عن تقييم',
                    'message' => "تم الإبلاغ عن {$flag->getFlaggedItemType()}: {$flag->getReasonText()}",
                    'data' => [
                        'flag_id' => $flag->id,
                        'type' => $flag->getFlaggedItemType()
                    ]
                ]);
            });
        });

        static::updated(function ($flag) {
            if ($flag->wasChanged('status') && $flag->isReviewed()) {
                // Notify the user who reported about the review
                $flag->user->notifications()->create([
                    'type' => 'flag_reviewed',
                    'title' => 'تم مراجعة إبلاغك',
                    'message' => "تم مراجعة إبلاغك عن {$flag->getFlaggedItemType()}",
                    'data' => ['flag_id' => $flag->id]
                ]);
            }
        });
    }
}
