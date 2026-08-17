<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\Lead;
use App\Models\Property;
use Illuminate\Support\Facades\Schema;

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

        // Optimize property performance via SQL conditional aggregation (1 query)
        $propertyStats = Property::where('company_id', $companyId)
            ->whereBetween('created_at', [$dateRange['start'].' 00:00:00', $dateRange['end'].' 23:59:59'])
            ->selectRaw("
                COUNT(*) as total,
                COUNT(CASE WHEN status = 'sold' THEN 1 END) as sold,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                AVG(price) as average_price,
                SUM(price) as total_value
            ")
            ->first();

        $totalProperties = (int) ($propertyStats->total ?? 0);
        $soldProperties = (int) ($propertyStats->sold ?? 0);
        $pendingProperties = (int) ($propertyStats->pending ?? 0);
        $averagePrice = (float) ($propertyStats->average_price ?? 0);
        $propertyTotalValue = (float) ($propertyStats->total_value ?? 0);

        // Get team members once
        $teamMembers = $company->members()->with('user')->get();
        $totalMembers = $teamMembers->count();
        $activeMembers = $teamMembers->where('status', 'active')->count();

        // Get leads performance by querying assigned_to for company team members
        $memberUserIds = $teamMembers->pluck('user_id')->filter()->toArray();

        if (! empty($memberUserIds)) {
            $leadStats = Lead::whereIn('assigned_to', $memberUserIds)
                ->whereBetween('created_at', [$dateRange['start'].' 00:00:00', $dateRange['end'].' 23:59:59'])
                ->selectRaw("
                    COUNT(*) as total,
                    COUNT(CASE WHEN lead_status = 'converted' THEN 1 END) as converted,
                    SUM(COALESCE(estimated_value, 0)) as total_value
                ")
                ->first();

            $totalLeads = (int) ($leadStats->total ?? 0);
            $convertedLeads = (int) ($leadStats->converted ?? 0);
            $leadTotalValue = (float) ($leadStats->total_value ?? 0);
        } else {
            $totalLeads = 0;
            $convertedLeads = 0;
            $leadTotalValue = 0;
        }

        $conversionRate = $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;

        return [
            'period' => $period,
            'date_range' => $dateRange,
            'properties' => [
                'total' => $totalProperties,
                'sold' => $soldProperties,
                'pending' => $pendingProperties,
                'average_price' => $averagePrice,
                'total_value' => $propertyTotalValue,
            ],
            'team' => [
                'total_members' => $totalMembers,
                'active_members' => $activeMembers,
                'productivity_score' => $this->calculateTeamProductivity($teamMembers),
            ],
            'leads' => [
                'total' => $totalLeads,
                'converted' => $convertedLeads,
                'conversion_rate' => $conversionRate,
                'total_value' => $leadTotalValue,
                'average_value' => $totalLeads > 0 ? $leadTotalValue / $totalLeads : 0,
            ],
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
            $performance[] = $this->getMemberPerformance($member, $companyId);
        }

        // Sort by performance score
        usort($performance, function ($a, $b) {
            return $b['productivity_score'] - $a['productivity_score'];
        });

        $count = count($performance);

        return [
            'team_size' => count($teamMembers),
            'average_productivity' => $count > 0 ? array_sum(array_column($performance, 'productivity_score')) / $count : 0,
            'top_performers' => array_slice($performance, 0, 5),
            'bottom_performers' => array_slice($performance, -5, 5),
        ];
    }

    /**
     * Get individual member performance
     */
    private function getMemberPerformance(CompanyMember $member, int $companyId): array
    {
        $startDate = now()->subMonth();
        $endDate = now();

        $memberProperties = Property::where('company_id', $companyId)
            ->where(function ($query) use ($member) {
                $query->where('assigned_agent_id', $member->user_id)
                    ->orWhere('agent_id', $member->user_id);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $memberLeads = 0;
        $memberDeals = 0;

        if (Schema::hasTable('leads')) {
            $leadStats = $member->leads()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw("
                    COUNT(*) as leads_count,
                    SUM(CASE WHEN lead_status = 'converted' THEN COALESCE(estimated_value, 0) ELSE 0 END) as deals_value
                ")
                ->first();

            $memberLeads = (int) ($leadStats->leads_count ?? 0);
            $memberDeals = (float) ($leadStats->deals_value ?? 0);
        }

        $memberTasks = 0;
        if (Schema::hasTable('agent_tasks')) {
            try {
                $memberTasks = $member->tasks()
                    ->whereBetween('due_date', [$startDate, $endDate])
                    ->count();
            } catch (\Exception $e) {
                $memberTasks = 0;
            }
        }

        $memberAppointments = 0;
        if (Schema::hasTable('appointments')) {
            try {
                $memberAppointments = $member->appointments()
                    ->whereBetween('start_time', [$startDate, $endDate])
                    ->count();
            } catch (\Exception $e) {
                $memberAppointments = 0;
            }
        }

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
            'member_name' => $member->user?->name ?? $member->user?->first_name ?? 'Unknown',
            'member_role' => $member->role,
            'productivity_score' => $productivityScore,
            'properties_count' => $memberProperties,
            'leads_count' => $memberLeads,
            'deals_count' => $memberDeals,
            'tasks_count' => $memberTasks,
            'appointments_count' => $memberAppointments,
        ];
    }

    /**
     * Calculate team productivity score
     */
    private function calculateTeamProductivity($teamMembers): float
    {
        if (empty($teamMembers) || (is_object($teamMembers) && $teamMembers->isEmpty())) {
            return 0.0;
        }

        $totalScore = 0;
        $count = 0;

        foreach ($teamMembers as $member) {
            $memberPerformance = $this->getMemberPerformance($member, $member->company_id);
            $totalScore += $memberPerformance['productivity_score'];
            $count++;
        }

        return $count > 0 ? $totalScore / $count : 0.0;
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
                    'start' => $now->copy()->startOfWeek()->toDateString(),
                    'end' => $now->copy()->endOfWeek()->toDateString(),
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->endOfMonth()->toDateString(),
                ];
            case 'quarter':
                return [
                    'start' => $now->copy()->startOfQuarter()->toDateString(),
                    'end' => $now->copy()->endOfQuarter()->toDateString(),
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear()->toDateString(),
                    'end' => $now->copy()->endOfYear()->toDateString(),
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->endOfMonth()->toDateString(),
                ];
        }
    }
}
