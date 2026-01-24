<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class ReviewVerification extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'verified_by',
        'verification_method',
        'verification_status',
        'verified_at',
        'notes',
        'rejection_reason'
    ];

    protected $casts = [
        'verified_at' => 'datetime'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'verified_at'
    ];

    // Relationships
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopeVerified($query)
    {
        return $query->where('verification_status', 'verified');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('verification_method', $method);
    }

    public function scopeByVerifier($query, $verifierId)
    {
        return $query->where('verified_by', $verifierId);
    }

    // Methods
    public function getVerificationMethodText()
    {
        $methods = [
            'manual' => 'يدوي',
            'auto' => 'تلقائي',
            'user_request' => 'طلب المستخدم',
            'admin_review' => 'مراجعة المدير',
            'document_verification' => 'التحقق من الوثائق',
            'phone_verification' => 'التحقق من الهاتف',
            'email_verification' => 'التحقق من البريد',
            'identity_verification' => 'التحقق من الهوية'
        ];

        return $methods[$this->verification_method] ?? $this->verification_method;
    }

    public function getVerificationMethodIcon()
    {
        $icons = [
            'manual' => 'fas fa-user-check',
            'auto' => 'fas fa-robot',
            'user_request' => 'fas fa-user-plus',
            'admin_review' => 'fas fa-user-shield',
            'document_verification' => 'fas fa-file-check',
            'phone_verification' => 'fas fa-phone-check',
            'email_verification' => 'fas fa-envelope-check',
            'identity_verification' => 'fas fa-id-card'
        ];

        return $icons[$this->verification_method] ?? 'fas fa-check';
    }

    public function getVerificationStatusText()
    {
        $statuses = [
            'pending' => 'في انتظار التحقق',
            'verified' => 'تم التحقق',
            'rejected' => 'مرفوض',
            'approved' => 'موافق عليه'
        ];

        return $statuses[$this->verification_status] ?? $this->verification_status;
    }

    public function getVerificationStatusColor()
    {
        $colors = [
            'pending' => 'yellow',
            'verified' => 'green',
            'rejected' => 'red',
            'approved' => 'blue'
        ];

        return $colors[$this->verification_status] ?? 'gray';
    }

    public function isPending()
    {
        return $this->verification_status === 'pending';
    }

    public function isVerified()
    {
        return $this->verification_status === 'verified';
    }

    public function isRejected()
    {
        return $this->verification_status === 'rejected';
    }

    public function isApproved()
    {
        return $this->verification_status === 'approved';
    }

    public function isManual()
    {
        return $this->verification_method === 'manual';
    }

    public function isAutomatic()
    {
        return $this->verification_method === 'auto';
    }

    public function isUserRequested()
    {
        return $this->verification_method === 'user_request';
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

    public function getVerifiedDate()
    {
        return $this->verified_at ? $this->verified_at->format('Y-m-d H:i') : null;
    }

    public function getVerifiedDateArabic()
    {
        return $this->verified_at ? $this->verified_at->locale('ar')->translatedFormat('d F Y') : null;
    }

    public function getVerificationTime()
    {
        if (!$this->verified_at) {
            return null;
        }

        return $this->created_at->diffInHours($this->verified_at);
    }

    public function getVerificationTimeText()
    {
        $hours = $this->getVerificationTime();
        
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

    public function canBeApprovedBy($user)
    {
        return $user->isAdmin() && $this->isPending();
    }

    public function canBeRejectedBy($user)
    {
        return $user->isAdmin() && $this->isPending();
    }

    public function canBeVerifiedBy($user)
    {
        return $user->isAdmin() && $this->isPending();
    }

    public function approve($verifierId = null)
    {
        $this->update([
            'verification_status' => 'approved',
            'verified_by' => $verifierId ?? auth()->user()->id,
            'verified_at' => now()
        ]);

        // Update review verification status
        $this->review->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $this->verified_by
        ]);
    }

    public function reject($rejectionReason, $verifierId = null)
    {
        $this->update([
            'verification_status' => 'rejected',
            'rejection_reason' => $rejectionReason,
            'verified_by' => $verifierId ?? auth()->user()->id,
            'verified_at' => now()
        ]);
    }

    public function verify($verifierId = null)
    {
        $this->update([
            'verification_status' => 'verified',
            'verified_by' => $verifierId ?? auth()->user()->id,
            'verified_at' => now()
        ]);

        // Update review verification status
        $this->review->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verified_by' => $this->verified_by
        ]);
    }

    public function getExcerpt($length = 100)
    {
        $notes = strip_tags($this->notes);
        return strlen($notes) > $length ? substr($notes, 0, $length) . '...' : $notes;
    }

    public function getRejectionExcerpt($length = 100)
    {
        if (!$this->rejection_reason) {
            return null;
        }

        $reason = strip_tags($this->rejection_reason);
        return strlen($reason) > $length ? substr($reason, 0, $length) . '...' : $reason;
    }

    public function getMetaDescription()
    {
        return "تحقق من تقييم - {$this->getVerificationMethodText()}";
    }

    public function getMetaKeywords()
    {
        return ['تحقق', 'توثيق', 'تقييم', $this->getVerificationMethodText()];
    }

    // Static methods
    public static function getMethods()
    {
        return [
            'manual' => 'يدوي',
            'auto' => 'تلقائي',
            'user_request' => 'طلب المستخدم',
            'admin_review' => 'مراجعة المدير',
            'document_verification' => 'التحقق من الوثائق',
            'phone_verification' => 'التحقق من الهاتف',
            'email_verification' => 'التحقق من البريد',
            'identity_verification' => 'التحقق من الهوية'
        ];
    }

    public static function getStatuses()
    {
        return [
            'pending' => 'في انتظار التحقق',
            'verified' => 'تم التحقق',
            'rejected' => 'مرفوض',
            'approved' => 'موافق عليه'
        ];
    }

    public static function getStatistics()
    {
        return [
            'total_verifications' => self::count(),
            'pending_verifications' => self::pending()->count(),
            'verified_verifications' => self::verified()->count(),
            'rejected_verifications' => self::rejected()->count(),
            'approved_verifications' => self::approved()->count(),
            'by_method' => self::selectRaw('verification_method, COUNT(*) as count')
                ->groupBy('verification_method')
                ->get(),
            'by_status' => self::selectRaw('verification_status, COUNT(*) as count')
                ->groupBy('verification_status')
                ->get(),
            'by_verifier' => self::with('verifier')
                ->selectRaw('verified_by, COUNT(*) as count')
                ->groupBy('verified_by')
                ->get(),
            'average_verification_time' => self::whereNotNull('verified_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, verified_at)) as avg_hours')
                ->first(),
            'verification_rate' => self::verified()->count() / self::count() * 100
        ];
    }

    public static function getVerifierStatistics($verifierId)
    {
        return [
            'total_verifications' => self::where('verified_by', $verifierId)->count(),
            'verified_count' => self::where('verified_by', $verifierId)->verified()->count(),
            'rejected_count' => self::where('verified_by', $verifierId)->rejected()->count(),
            'approved_count' => self::where('verified_by', $verifierId)->approved()->count(),
            'by_method' => self::where('verified_by', $verifierId)
                ->selectRaw('verification_method, COUNT(*) as count')
                ->groupBy('verification_method')
                ->get(),
            'average_verification_time' => self::where('verified_by', $verifierId)
                ->whereNotNull('verified_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, verified_at)) as avg_hours')
                ->first()
        ];
    }

    protected static function booted()
    {
        static::created(function ($verification) {
            // Notify review author about verification request
            if ($verification->verification_method === 'user_request') {
                User::where('role', 'admin')->get()->each(function($admin) use ($verification) {
                    $admin->notifications()->create([
                        'type' => 'verification_request',
                        'title' => 'طلب تحقق جديد',
                        'message' => 'تم طلب تحقق من تقييم',
                        'data' => ['verification_id' => $verification->id]
                    ]);
                });
            }
        });

        static::updated(function ($verification) {
            if ($verification->wasChanged('verification_status') && $verification->isVerified()) {
                // Notify review author about verification
                $verification->review->user->notifications()->create([
                    'type' => 'review_verified',
                    'title' => 'تم توثيق تقييمك',
                    'message' => 'تم توثيق تقييمك بنجاح',
                    'data' => ['verification_id' => $verification->id]
                ]);
            } elseif ($verification->wasChanged('verification_status') && $verification->isRejected()) {
                // Notify review author about rejection
                $verification->review->user->notifications()->create([
                    'type' => 'verification_rejected',
                    'title' => 'تم رفض طلب التحقق',
                    'message' => 'تم رفض طلب التحقق من تقييمك',
                    'data' => ['verification_id' => $verification->id]
                ]);
            }
        });
    }
}
