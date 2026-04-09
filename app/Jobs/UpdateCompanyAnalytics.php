<?php

namespace App\Jobs;

use App\Models\Company;
use App\Models\CompanyMember;
use App\Models\CompanyBranch;
use App\Models\User;
use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class UpdateCompanyAnalytics implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900];
    public $timeout = 600;

    protected $companyId;
    protected $analyticsType;

    /**
     * Create a new job instance.
     */
    public function __construct(int $companyId, string $analyticsType = 'daily')
    {
        $this->companyId = $companyId;
        $this->analyticsType = $analyticsType;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $company = Company::with(['members', 'branches', 'properties'])->find($this->companyId);

            if (!$company) {
                Log::error('Company not found for analytics update', ['company_id' => $this->companyId]);
                return;
            }

            // Calculate analytics based on type
            $analyticsData = $this->calculateAnalytics($company, $this->analyticsType);

            // Update company analytics
            $company->analytics()->create([
                'type' => $this->analyticsType,
                'data' => $analyticsData,
                'calculated_at' => now()
            ]);

            // Clear company cache
            Cache::tags(['company', 'company.' . $this->companyId])->flush();

            Log::info('Company analytics updated', [
                'company_id' => $this->companyId,
                'analytics_type' => $this->analyticsType,
                'data_points' => count($analyticsData)
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update company analytics', [
                'company_id' => $this->companyId,
                'analytics_type' => $this->analyticsType,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Calculate analytics data
     */
    private function calculateAnalytics(Company $company, string $type): array
    {
        switch ($type) {
            case 'daily':
                return $this->calculateDailyAnalytics($company);
            case 'weekly':
                return $this->calculateWeeklyAnalytics($company);
            case 'monthly':
                return $this->calculateMonthlyAnalytics($company);
            case 'yearly':
                return $this->calculateYearlyAnalytics($company);
            default:
                return $this->calculateDailyAnalytics($company);
        }
    }

    /**
     * Calculate daily analytics
     */
    private function calculateDailyAnalytics(Company $company): array
    {
        $today = now()->toDateString();

        return [
            'date' => $today,
            'properties' => [
                'total' => $company->properties()->whereDate('created_at', $today)->count(),
                'new' => $company->properties()->whereDate('created_at', $today)->count(),
                'views' => $this->getPropertyViews($company->id, $today),
                'inquiries' => $this->getPropertyInquiries($company->id, $today)
            ],
            'team' => [
                'active_members' => $company->members()->where('status', 'active')->count(),
                'new_members' => $company->members()->whereDate('created_at', $today)->count(),
                'productivity_score' => $this->getAverageProductivity($company->id)
            ],
            'leads' => [
                'total' => $company->leads()->whereDate('created_at', $today)->count(),
                'converted' => $company->leads()->whereDate('created_at', $today)->where('status', 'converted')->count(),
                'conversion_rate' => $this->getConversionRate($company->id, $today)
            ],
            'revenue' => [
                'total' => $this->getDailyRevenue($company->id),
                'from_properties' => $this->getDailyRevenueFromProperties($company->id),
                'from_services' => $this->getDailyRevenueFromServices($company->id)
            ]
        ];
    }

    /**
     * Calculate weekly analytics
     */
    private function calculateWeeklyAnalytics(Company $company): array
    {
        $weekStart = now()->subWeek()->startOfWeek()->toDateString();
        $weekEnd = now()->subWeek()->endOfWeek()->toDateString();

        return [
            'period' => 'week',
            'date_range' => ['start' => $weekStart, 'end' => $weekEnd],
            'properties' => [
                'total' => $company->properties()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'new' => $company->properties()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'views' => $this->getPropertyViews($company->id, $weekStart, $weekEnd),
                'inquiries' => $this->getPropertyInquiries($company->id, $weekStart, $weekEnd)
            ],
            'team' => [
                'active_members' => $company->members()->whereBetween('created_at', [$weekStart, $weekEnd])->where('status', 'active')->count(),
                'new_members' => $company->members()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'productivity_score' => $this->getAverageProductivity($company->id, $weekStart, $weekEnd)
            ],
            'leads' => [
                'total' => $company->leads()->whereBetween('created_at', [$weekStart, $weekEnd])->count(),
                'converted' => $company->leads()->whereBetween('created_at', [$weekStart, $weekEnd])->where('status', 'converted')->count(),
                'conversion_rate' => $this->getConversionRate($company->id, $weekStart, $weekEnd)
            ],
            'revenue' => [
                'total' => $this->getWeeklyRevenue($company->id, $weekStart, $weekEnd),
                'from_properties' => $this->getWeeklyRevenueFromProperties($company->id, $weekStart, $weekEnd),
                'from_services' => $this->getWeeklyRevenueFromServices($company->id, $weekStart, $weekEnd)
            ]
        ];
    }

    /**
     * Calculate monthly analytics
     */
    private function calculateMonthlyAnalytics(Company $company): array
    {
        $monthStart = now()->subMonth()->startOfMonth()->toDateString();
        $monthEnd = now()->subMonth()->endOfMonth()->toDateString();

        return [
            'period' => 'month',
            'date_range' => ['start' => $monthStart, 'end' => $monthEnd],
            'properties' => [
                'total' => $company->properties()->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'new' => $company->properties()->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'views' => $this->getPropertyViews($company->id, $monthStart, $monthEnd),
                'inquiries' => $this->getPropertyInquiries($company->id, $monthStart, $monthEnd)
            ],
            'team' => [
                'active_members' => $company->members()->whereBetween('created_at', [$monthStart, $monthEnd])->where('status', 'active')->count(),
                'new_members' => $company->members()->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'productivity_score' => $this->getAverageProductivity($company->id, $monthStart, $monthEnd)
            ],
            'leads' => [
                'total' => $company->leads()->whereBetween('created_at', [$monthStart, $monthEnd])->count(),
                'converted' => $company->leads()->whereBetween('created_at', [$monthStart, $monthEnd])->where('status', 'converted')->count(),
                'conversion_rate' => $this->getConversionRate($company->id, $monthStart, $monthEnd)
            ],
            'revenue' => [
                'total' => $this->getMonthlyRevenue($company->id, $monthStart, $monthEnd),
                'from_properties' => $this->getMonthlyRevenueFromProperties($company->id, $monthStart, $monthEnd),
                'from_services' => $this->getMonthlyRevenueFromServices($company->id, $monthStart, $monthEnd)
            ]
        ];
    }

    /**
     * Calculate yearly analytics
     */
    private function calculateYearlyAnalytics(Company $company): array
    {
        $yearStart = now()->subYear()->startOfYear()->toDateString();
        $yearEnd = now()->subYear()->endOfYear()->toDateString();

        return [
            'period' => 'year',
            'date_range' => ['start' => $yearStart, 'end' => $yearEnd],
            'properties' => [
                'total' => $company->properties()->whereBetween('created_at', [$yearStart, $yearEnd])->count(),
                'new' => $company->properties()->whereBetween('created_at', [$yearStart, $yearEnd])->count(),
                'views' => $this->getPropertyViews($company->id, $yearStart, $yearEnd),
                'inquiries' => $this->getPropertyInquiries($company->id, $yearStart, $yearEnd)
            ],
            'team' => [
                'active_members' => $company->members()->whereBetween('created_at', [$yearStart, $yearEnd])->where('status', 'active')->count(),
                'new_members' => $company->members()->whereBetween('created_at', [$yearStart, $yearEnd])->count(),
                'productivity_score' => $this->getAverageProductivity($company->id, $yearStart, $yearEnd)
            ],
            'leads' => [
                'total' => $company->leads()->whereBetween('created_at', [$yearStart, $yearEnd])->count(),
                'converted' => $company->leads()->whereBetween('created_at', [$yearStart, $yearEnd])->where('status', 'converted')->count(),
                'conversion_rate' => $this->getConversionRate($company->id, $yearStart, $yearEnd)
            ],
            'revenue' => [
                'total' => $this->getYearlyRevenue($company->id, $yearStart, $yearEnd),
                'from_properties' => $this->getYearlyRevenueFromProperties($company->id, $yearStart, $yearEnd),
                'from_services' => $this->getYearlyRevenueFromServices($company->id, $yearStart, $yearEnd)
            ]
        ];
    }

    /**
     * Get property views for period
     */
    private function getPropertyViews(int $companyId, string $start, string $end = null): int
    {
        // This would integrate with your analytics system
        // Placeholder implementation
        return 0;
    }

    /**
     * Get property inquiries for period
     */
    private function getPropertyInquiries(int $companyId, string $start, string $end = null): int
    {
        // This would integrate with your analytics system
        // Placeholder implementation
        return 0;
    }

    /**
     * Get average productivity
     */
    private function getAverageProductivity(int $companyId, string $start = null, string $end = null): float
    {
        $query = CompanyMember::where('company_id', $companyId);

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        }

        $members = $query->with('user')->get();

        if ($members->isEmpty()) {
            return 0;
        }

        $totalScore = 0;

        foreach ($members as $member) {
            // Calculate individual member productivity
            $memberProductivity = $this->calculateMemberProductivity($member->user, $companyId);
            $totalScore += $memberProductivity;
        }

        return $totalScore / $members->count();
    }

    /**
     * Calculate member productivity
     */
    private function calculateMemberProductivity(User $user, int $companyId): float
    {
        // Properties created by member
        $properties = Property::where('company_id', $companyId)
            ->where('assigned_agent_id', $user->id)
            ->count();

        // Leads converted by member
        $leads = $user->leads()
            ->where('status', 'converted')
            ->count();

        // Tasks completed by member
        $tasks = $user->tasks()
            ->where('status', 'completed')
            ->count();

        // Calculate productivity score
        $score = ($properties * 40) + ($leads * 30) + ($tasks * 10);

        return $score;
    }

    /**
     * Get conversion rate
     */
    private function getConversionRate(int $companyId, string $start = null, string $end = null): float
    {
        $company = Company::find($companyId);
        $query = $company->leads();

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        }

        $totalLeads = $query->count();
        $convertedLeads = $query->where('status', 'converted')->count();

        return $totalLeads > 0 ? ($convertedLeads / $totalLeads) * 100 : 0;
    }

    /**
     * Get daily revenue
     */
    private function getDailyRevenue(int $companyId): float
    {
        return $this->getRevenueFromProperties($companyId, now()->toDateString()) +
            $this->getRevenueFromServices($companyId, now()->toDateString());
    }

    /**
     * Get weekly revenue
     */
    private function getWeeklyRevenue(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end) +
            $this->getRevenueFromServices($companyId, $start, $end);
    }

    /**
     * Get monthly revenue
     */
    private function getMonthlyRevenue(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end) +
            $this->getRevenueFromServices($companyId, $start, $end);
    }

    /**
     * Get yearly revenue
     */
    private function getYearlyRevenue(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end) +
            $this->getRevenueFromServices($companyId, $start, $end);
    }

    private function getDailyRevenueFromProperties(int $companyId): float
    {
        return $this->getRevenueFromProperties($companyId, now()->toDateString());
    }

    private function getDailyRevenueFromServices(int $companyId): float
    {
        return $this->getRevenueFromServices($companyId, now()->toDateString());
    }

    private function getWeeklyRevenueFromProperties(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end);
    }

    private function getWeeklyRevenueFromServices(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromServices($companyId, $start, $end);
    }

    private function getMonthlyRevenueFromProperties(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end);
    }

    private function getMonthlyRevenueFromServices(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromServices($companyId, $start, $end);
    }

    private function getYearlyRevenueFromProperties(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromProperties($companyId, $start, $end);
    }

    private function getYearlyRevenueFromServices(int $companyId, string $start, string $end): float
    {
        return $this->getRevenueFromServices($companyId, $start, $end);
    }

    /**
     * Get revenue from properties
     */
    private function getRevenueFromProperties(int $companyId, string $start, string $end = null): float
    {
        $query = Property::where('company_id', $companyId)
            ->where('status', 'sold');

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        } elseif ($start) {
            $query->whereDate('created_at', '>=', $start);
        }

        return $query->sum('price') * 0.025; // 2.5% average commission
    }

    /**
     * Get revenue from services
     */
    private function getRevenueFromServices(int $companyId, string $start, string $end = null): float
    {
        // This would integrate with your service revenue system
        // Placeholder implementation
        return 0;
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Company analytics update job failed', [
            'company_id' => $this->companyId,
            'analytics_type' => $this->analyticsType,
            'error' => $exception->getMessage()
        ]);
    }
}
