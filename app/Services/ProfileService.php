<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;

class ProfileService
{
    /**
     * Calculate profile completion percentage
     */
    public function calculateCompletionPercentage(User $user): int
    {
        $profile = $user->profile;
        $completion = 0;
        
        // Basic info (40%)
        if ($user->name) $completion += 8;
        if ($user->email) $completion += 8;
        if ($user->phone) $completion += 8;
        if ($user->avatar) $completion += 8;
        if ($user->date_of_birth) $completion += 8;
        
        // Profile details (30%)
        if ($profile?->bio) $completion += 6;
        if ($profile?->address) $completion += 6;
        if ($profile?->profession) $completion += 6;
        if ($profile?->education) $completion += 6;
        if ($profile?->interests) $completion += 6;
        
        // Verification (20%)
        if ($user->email_verified_at) $completion += 10;
        if ($user->kyc_verified) $completion += 10;
        
        // Additional info (10%)
        if ($profile?->website) $completion += 5;
        if ($profile?->social_links) $completion += 5;
        
        return min($completion, 100);
    }
    
    /**
     * Suggest profile improvements
     */
    public function suggestProfileImprovements(User $user): array
    {
        $profile = $user->profile;
        $suggestions = [];
        
        // Check missing basic info
        if (!$user->name) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'name',
                'message' => 'أضف اسمك الكامل',
                'priority' => 'high',
                'points' => 8
            ];
        }
        
        if (!$user->phone) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'phone',
                'message' => 'أضف رقم هاتفك',
                'priority' => 'high',
                'points' => 8
            ];
        }
        
        if (!$user->avatar) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'avatar',
                'message' => 'أضف صورة شخصية',
                'priority' => 'high',
                'points' => 8
            ];
        }
        
        if (!$user->date_of_birth) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'date_of_birth',
                'message' => 'أضف تاريخ ميلادك',
                'priority' => 'medium',
                'points' => 8
            ];
        }
        
        // Check profile details
        if (!$profile?->bio) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'bio',
                'message' => 'أضف نبذة عنك',
                'priority' => 'medium',
                'points' => 6
            ];
        }
        
        if (!$profile?->address) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'address',
                'message' => 'أضف عنوانك',
                'priority' => 'medium',
                'points' => 6
            ];
        }
        
        if (!$profile?->profession) {
            $suggestions[] = [
                'type' => 'missing_info',
                'field' => 'profession',
                'message' => 'أضف مهنتك',
                'priority' => 'low',
                'points' => 6
            ];
        }
        
        // Check verification
        if (!$user->email_verified_at) {
            $suggestions[] = [
                'type' => 'verification',
                'field' => 'email_verification',
                'message' => 'تحقق من بريدك الإلكتروني',
                'priority' => 'high',
                'points' => 10
            ];
        }
        
        if (!$user->kyc_verified) {
            $suggestions[] = [
                'type' => 'verification',
                'field' => 'kyc_verification',
                'message' => 'أكمل عملية التحقق من الهوية',
                'priority' => 'high',
                'points' => 10
            ];
        }
        
        // Sort by priority and points
        usort($suggestions, function ($a, $b) {
            $priorityOrder = ['high' => 3, 'medium' => 2, 'low' => 1];
            
            if ($priorityOrder[$a['priority']] !== $priorityOrder[$b['priority']]) {
                return $priorityOrder[$b['priority']] - $priorityOrder[$a['priority']];
            }
            
            return $b['points'] - $a['points'];
        });
        
        return $suggestions;
    }
    
    /**
     * Get profile strength indicator
     */
    public function getProfileStrength(User $user): array
    {
        $percentage = $this->calculateCompletionPercentage($user);
        
        if ($percentage >= 90) {
            return [
                'level' => 'excellent',
                'color' => '#10b981',
                'message' => 'ملف شخصي ممتاز',
                'next_milestone' => null
            ];
        } elseif ($percentage >= 75) {
            return [
                'level' => 'very_good',
                'color' => '#22c55e',
                'message' => 'ملف شخصي جيد جداً',
                'next_milestone' => 90
            ];
        } elseif ($percentage >= 50) {
            return [
                'level' => 'good',
                'color' => '#3b82f6',
                'message' => 'ملف شخصي جيد',
                'next_milestone' => 75
            ];
        } elseif ($percentage >= 25) {
            return [
                'level' => 'fair',
                'color' => '#f59e0b',
                'message' => 'ملف شخصي متوسط',
                'next_milestone' => 50
            ];
        } else {
            return [
                'level' => 'poor',
                'color' => '#ef4444',
                'message' => 'ملف شخصي ضعيف',
                'next_milestone' => 25
            ];
        }
    }
    
    /**
     * Get profile completion breakdown
     */
    public function getCompletionBreakdown(User $user): array
    {
        $profile = $user->profile;
        
        return [
            'basic_info' => [
                'completed' => [
                    'name' => (bool)$user->name,
                    'email' => (bool)$user->email,
                    'phone' => (bool)$user->phone,
                    'avatar' => (bool)$user->avatar,
                    'date_of_birth' => (bool)$user->date_of_birth
                ],
                'total_points' => 40,
                'earned_points' => $this->calculateBasicInfoPoints($user)
            ],
            'profile_details' => [
                'completed' => [
                    'bio' => (bool)$profile?->bio,
                    'address' => (bool)$profile?->address,
                    'profession' => (bool)$profile?->profession,
                    'education' => (bool)$profile?->education,
                    'interests' => (bool)$profile?->interests
                ],
                'total_points' => 30,
                'earned_points' => $this->calculateProfileDetailsPoints($profile)
            ],
            'verification' => [
                'completed' => [
                    'email_verified' => (bool)$user->email_verified_at,
                    'kyc_verified' => (bool)$user->kyc_verified
                ],
                'total_points' => 20,
                'earned_points' => $this->calculateVerificationPoints($user)
            ],
            'additional_info' => [
                'completed' => [
                    'website' => (bool)$profile?->website,
                    'social_links' => (bool)$profile?->social_links
                ],
                'total_points' => 10,
                'earned_points' => $this->calculateAdditionalInfoPoints($profile)
            ]
        ];
    }
    
    /**
     * Calculate basic info points
     */
    private function calculateBasicInfoPoints(User $user): int
    {
        $points = 0;
        if ($user->name) $points += 8;
        if ($user->email) $points += 8;
        if ($user->phone) $points += 8;
        if ($user->avatar) $points += 8;
        if ($user->date_of_birth) $points += 8;
        return $points;
    }
    
    /**
     * Calculate profile details points
     */
    private function calculateProfileDetailsPoints(?UserProfile $profile): int
    {
        if (!$profile) return 0;
        
        $points = 0;
        if ($profile->bio) $points += 6;
        if ($profile->address) $points += 6;
        if ($profile->profession) $points += 6;
        if ($profile->education) $points += 6;
        if ($profile->interests) $points += 6;
        return $points;
    }
    
    /**
     * Calculate verification points
     */
    private function calculateVerificationPoints(User $user): int
    {
        $points = 0;
        if ($user->email_verified_at) $points += 10;
        if ($user->kyc_verified) $points += 10;
        return $points;
    }
    
    /**
     * Calculate additional info points
     */
    private function calculateAdditionalInfoPoints(?UserProfile $profile): int
    {
        if (!$profile) return 0;
        
        $points = 0;
        if ($profile->website) $points += 5;
        if ($profile->social_links) $points += 5;
        return $points;
    }
}
