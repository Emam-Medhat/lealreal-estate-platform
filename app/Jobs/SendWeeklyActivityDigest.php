<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendWeeklyActivityDigest implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::with([
            'activities',
            'profile',
            'wallet'
        ])->find($this->userId);
        
        if (!$user) {
            Log::error('User not found for activity digest', ['user_id' => $this->userId]);
            return;
        }
        
        try {
            $digestData = $this->generateDigestData($user);
            
            // Send email digest
            if ($digestData['has_activity']) {
                Mail::to($user->email)->send(new \App\Mail\WeeklyActivityDigestMail($user, $digestData));
            }
            
            // Create notification
            $user->notifications()->create([
                'title' => 'ملخص الأسبوع',
                'message' => $this->getDigestMessage($digestData),
                'type' => 'weekly_digest',
                'data' => $digestData
            ]);
            
            Log::info('Weekly activity digest sent', [
                'user_id' => $this->userId,
                'activities_count' => $digestData['activities_count']
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to send weekly activity digest', [
                'user_id' => $this->userId,
                'error' => $e->getMessage()
            ]);
            
            // Create failed notification
            $user->notifications()->create([
                'title' => 'فشل إرسال الملخص الأسبوعي',
                'message' => 'حدث خطأ أثناء إرسال ملخص نشاطك الأسبوعي',
                'type' => 'digest_failed',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ]);
            
            throw $e;
        }
    }
    
    /**
     * Generate digest data
     */
    private function generateDigestData(User $user): array
    {
        $weekStart = now()->subWeek()->startOfWeek();
        $weekEnd = now()->subWeek()->endOfWeek();
        
        // Get activities for the week
        $activities = $user->activities()
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group activities by type
        $activitiesByType = $activities->groupBy('activity_type');
        
        // Calculate statistics
        $stats = [
            'total_activities' => $activities->count(),
            'page_views' => $activities->where('activity_type', 'page_view')->count(),
            'actions' => $activities->where('activity_type', 'user_action')->count(),
            'logins' => $activities->where('action', 'login')->count(),
            'profile_updates' => $activities->where('action', 'profile_updated')->count(),
            'searches' => $activities->where('action', 'search')->count(),
            'favorites_added' => $activities->where('action', 'favorited_property')->count(),
            'comparisons_created' => $activities->where('action', 'created_comparison')->count()
        ];
        
        // Get most viewed pages
        $mostViewedPages = $activities
            ->where('activity_type', 'page_view')
            ->groupBy('page_url')
            ->map(function ($group) {
                return [
                    'url' => $group->first()->page_url,
                    'count' => $group->count(),
                    'title' => $this->getPageTitle($group->first()->page_url)
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
        
        // Get top actions
        $topActions = $activities
            ->where('activity_type', 'user_action')
            ->groupBy('action')
            ->map(function ($group) {
                return [
                    'action' => $group->first()->action,
                    'count' => $group->count(),
                    'description' => $this->getActionDescription($group->first()->action)
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values();
        
        // Get login history
        $loginHistory = $activities
            ->where('action', 'login')
            ->map(function ($activity) {
                return [
                    'time' => $activity->created_at,
                    'ip' => $activity->ip_address,
                    'device' => $this->getDeviceInfo($activity->user_agent),
                    'location' => $activity->location ?? null
                ];
            })
            ->take(5)
            ->values();
        
        // Profile changes
        $profileChanges = $activities
            ->where('action', 'profile_updated')
            ->map(function ($activity) {
                return [
                    'time' => $activity->created_at,
                    'changes' => $activity->data['changes'] ?? []
                ];
            })
            ->take(3)
            ->values();
        
        // Wallet activity if available
        $walletActivity = null;
        if ($user->wallet) {
            $walletTransactions = $user->wallet->transactions()
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->get();
            
            $walletActivity = [
                'total_transactions' => $walletTransactions->count(),
                'total_deposits' => $walletTransactions->where('amount', '>', 0)->sum('amount'),
                'total_withdrawals' => abs($walletTransactions->where('amount', '<', 0)->sum('amount')),
                'net_change' => $walletTransactions->sum('amount'),
                'current_balance' => $user->wallet->balance
            ];
        }
        
        return [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'profile_completion' => $user->profile_completion_percentage
            ],
            'period' => [
                'start' => $weekStart->toDateString(),
                'end' => $weekEnd->toDateString(),
                'days' => 7
            ],
            'has_activity' => $activities->count() > 0,
            'activities_count' => $activities->count(),
            'statistics' => $stats,
            'most_viewed_pages' => $mostViewedPages,
            'top_actions' => $topActions,
            'login_history' => $loginHistory,
            'profile_changes' => $profileChanges,
            'wallet_activity' => $walletActivity,
            'generated_at' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Get digest message
     */
    private function getDigestMessage(array $digestData): string
    {
        if (!$digestData['has_activity']) {
            return 'لم يكن لديك أي نشاط هذا الأسبوع';
        }
        
        $count = $digestData['activities_count'];
        
        if ($count <= 5) {
            return "لديك {$count} أنشطة هذا الأسبوع";
        } elseif ($count <= 20) {
            return "لديك {$count} نشاطًا هذا الأسبوع";
        } else {
            return "لديك {$count} نشاطًا هذا الأسبوع - نشاط رائع!";
        }
    }
    
    /**
     * Get page title
     */
    private function getPageTitle(string $url): string
    {
        $titles = [
            '/dashboard' => 'لوحة التحكم',
            '/properties' => 'قائمة العقارات',
            '/properties/search' => 'بحث العقارات',
            '/profile' => 'الملف الشخصي',
            '/favorites' => 'المفضلة',
            '/comparisons' => 'المقارنات'
        ];
        
        foreach ($titles as $pattern => $title) {
            if (strpos($url, $pattern) !== false) {
                return $title;
            }
        }
        
        // Extract from URL if no match
        $parts = explode('/', trim($url, '/'));
        return end($parts) ?: 'صفحة غير معروفة';
    }
    
    /**
     * Get action description
     */
    private function getActionDescription(string $action): string
    {
        $descriptions = [
            'login' => 'تسجيل الدخول',
            'logout' => 'تسجيل الخروج',
            'profile_updated' => 'تحديث الملف الشخصي',
            'search' => 'بحث',
            'favorited_property' => 'إضافة عقار للمفضلة',
            'created_comparison' => 'إنشاء مقارنة',
            'viewed_property' => 'عرض عقار',
            'contacted_agent' => 'التواصل مع وكيل',
            'submitted_offer' => 'تقديم عرض'
        ];
        
        return $descriptions[$action] ?? $action;
    }
    
    /**
     * Get device information
     */
    private function getDeviceInfo(string $userAgent): array
    {
        // Simple device detection
        if (strpos($userAgent, 'Mobile') !== false) {
            return ['type' => 'mobile', 'name' => 'جوال'];
        } elseif (strpos($userAgent, 'Tablet') !== false) {
            return ['type' => 'tablet', 'name' => 'جهاز لوحي'];
        } else {
            return ['type' => 'desktop', 'name' => 'كمبيوتر'];
        }
    }
    
    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Weekly activity digest job failed', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage()
        ]);
    }
}
