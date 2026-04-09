<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\Property;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CompanyAnalyticsService
{
    /**
     * Get performance metrics for company
     */
    public function getPerformanceMetrics(int $companyId, string $period = 'month'): array
    {
        $company = Company::findOrFail($companyId);
        
        // Get date range
        $dateRange = $this->getDateRange($period);
        
        // Get properties performance
        $properties = Property::where('company_id', $companyId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'title', 'price', 'status', 'created_at']);

        $totalProperties = $properties->count();
        $soldProperties = $properties->where('status', 'sold')->count();
        $pendingProperties = $properties->where('status', 'pending')->count();
        $averagePrice = $properties->avg('price');
        $totalValue = $properties->sum('price');

        // Get team performance
        $teamMembers = $company->members()->with('user')->get();
        $totalMembers = $teamMembers->count();
        $activeMembers = $teamMembers->where('status', 'active')->count();

        // Get leads performance
        $leads = $company->leads()
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->get(['id', 'title', 'status', 'value', 'created_at']);

        $totalLeads = $leads->count();
        $convertedLeads = $leads->where('status', 'converted')->count();
        $totalValue = $leads->sum('value');
        $conversionRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'properties' => [
                'total' => $totalProperties,
                'sold' => $soldProperties,
                'pending' => $pendingProperties,
                'average_price' => $averagePrice,
                'total_value' => $totalValue
            ],
            'team' => [
                'total_members' => $totalMembers,
                'active_members' => $activeMembers,
                'productivity_score' => $this->calculateTeamProductivity($teamMembers)
            ],
            'leads' => [
                'total' => $totalLeads,
                'converted' => $convertedLeads,
                'conversion_rate' => $conversionRate,
                'total_value' => $totalValue,
                'average_value' => $totalLeads > 0 ? $totalValue / $totalLeads : 0
            ]
        ];
    }

    /**
     * Get team performance analytics
     */
    public function getTeamPerformance(int $companyId): array
    {
        $teamMembers = CompanyMember::with('user')->where('company_id', $companyId)->get();
        
        $performance = [];
        
        foreach ($teamMembers as $member) {
            $memberPerformance = $this->getMemberPerformance($member, $companyId);
            $performance[] = $memberPerformance;
        }

        // Sort by performance score
        usort($performance, function ($a, $b) {
            return $b['productivity_score'] - $a['productivity_score'];
        });

        return [
            'team_size' => count($teamMembers),
            'average_productivity' => array_sum(array_column($performance, 'productivity_score')) / count($performance),
            'top_performers' => array_slice($performance, 0, 5),
            'bottom_performers' => array_slice($performance, -5, 5)
        ];
    }

    /**
     * Get individual member performance
     */
    private function getMemberPerformance(CompanyMember $member, int $companyId): array
    {
        $memberProperties = Property::where('company_id', $companyId)
            ->where('assigned_agent_id', $member->user_id)
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->count();

        $memberLeads = $member->leads()
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->count();

        $memberDeals = $member->leads()
            ->whereBetween('created_at', [now()->subMonth(), now()])
            ->where('status', 'converted')
            ->sum('value');

        $memberTasks = $member->tasks()
            ->whereBetween('due_date', [now()->subMonth(), now()])
            ->count();

        $memberAppointments = $member->appointments()
            ->whereBetween('start_time', [now()->subMonth(), now()])
            ->count();

        // Calculate metrics
        $productivityScore = 0;
        
        // Property performance (40%)
        if ($memberProperties > 0) {
            $productivityScore += 40;
        }
        
        // Lead performance (30%)
        if ($memberLeads > 0) {
            $productivityScore += 30;
        }
        
        // Deal performance (20%)
        if ($memberDeals > 0) {
            $productivityScore += 20;
        }
        
        // Task completion (10%)
        if ($memberTasks > 0) {
            $productivityScore += 10;
        }

        return [
            'member_id' => $member->id,
            'member_name' => $member->user->name,
            'member_role' => $member->role,
            'productivity_score' => $productivityScore,
            'properties_count' => $memberProperties,
            'leads_count' => $memberLeads,
            'deals_count' => $memberDeals,
            'tasks_count' => $memberTasks,
            'appointments_count' => $memberAppointments
        ];
    }

    /**
     * Calculate team productivity score
     */
    private function calculateTeamProductivity(array $teamMembers): float
    {
        $totalScore = 0;
        
        foreach ($teamMembers as $member) {
            $memberPerformance = $this->getMemberPerformance($member, $member->company_id);
            $totalScore += $memberPerformance['productivity_score'];
        }

        return $totalScore > 0 ? $totalScore / count($teamMembers) : 0;
    }

    /**
     * Get date range for period
     */
    private function getDateRange(string $period): array
    {
        $now = now();
        
        switch ($period) {
            case 'week':
                return [
                    'start' => $now->subWeek()->startOfWeek()->toDateString(),
                    'end' => $now->endOfWeek()->toDateString()
                ];
            case 'month':
                return [
                    'start' => $now->subMonth()->startOfMonth()->toDateString(),
                    'end' => $now->endOfMonth()->toDateString()
                ];
            case 'quarter':
                return [
                    'start' => $now->subQuarter()->startOfMonth()->toDateString(),
                    'end' => $now->endOfQuarter()->toDateString()
                ];
            case 'year':
                return [
                    'start' => $now->subYear()->startOfYear()->toDateString(),
                    'end' => $now->endOfYear()->toDateString()
                ];
            default:
                return [
                    'start' => $now->subMonth()->startOfMonth()->toDateString(),
                    'end' => $now()->endOfMonth()->toDateString()
                ];
        }
    }
}
