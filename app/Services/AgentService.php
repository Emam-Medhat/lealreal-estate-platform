<?php

namespace App\Services;

use App\Models\Agent;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Repositories\Contracts\AgentRepositoryInterface;

class AgentService
{
    protected $agentRepository;
    protected $cacheTTL = 3600; // 1 hour

    public function __construct(AgentRepositoryInterface $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    /**
     * Get agent profile data with eager loaded relationships.
     */
    public function getAgentProfile(Agent $agent): Agent
    {
        return $agent->load(['profile', 'certifications', 'licenses', 'reviews' => function ($query) {
            $query->latest()->limit(5);
        }, 'company']);
    }

    /**
     * Get agent dashboard/profile stats with caching.
     */
    public function getAgentStats(Agent $agent): array
    {
        $cacheKey = "agent_stats_{$agent->id}";

        return Cache::remember($cacheKey, 600, function () use ($agent) {
            $agent->loadCount([
                'properties', 
                'properties as active_properties' => fn($q) => $q->where('status', 'active'),
                'properties as sold_properties' => fn($q) => $q->where('status', 'sold'),
                'reviews',
                'clients',
                'commissions'
            ]);
            
            $agent->loadAvg('reviews', 'rating');

            return [
                'total_properties' => $agent->properties_count,
                'active_properties' => $agent->active_properties_count,
                'sold_properties' => $agent->sold_properties_count,
                'total_reviews' => $agent->reviews_count,
                'average_rating' => $agent->reviews_avg_rating ?? 0,
                'total_commissions' => $agent->commissions()->sum('amount'), // Still need sum
                'this_month_commissions' => $agent->commissions()
                    ->whereMonth('created_at', now()->month)
                    ->sum('amount'),
                'properties_sold' => $agent->sold_properties_count,
                'total_revenue' => $agent->commissions_count > 0 ? $agent->commissions()->sum('amount') : 0,
                'active_clients' => $agent->clients_count,
                'rating' => $agent->reviews_avg_rating ?? 0,
            ];
        });
    }

    /**
     * Get recent activities for the agent.
     */
    public function getRecentActivities(Agent $agent): array
    {
        $cacheKey = "agent_activities_{$agent->id}";

        return Cache::remember($cacheKey, 300, function () use ($agent) {
            $activities = [];
            
            // Recent properties
            $properties = $agent->properties()->latest()->limit(3)->get();
            foreach ($properties as $property) {
                $activities[] = [
                    'icon' => 'home',
                    'message' => "Added new property: {$property->title}",
                    'time' => $property->created_at->diffForHumans(),
                    'timestamp' => $property->created_at
                ];
            }

            // Recent reviews
            $reviews = $agent->reviews()->latest()->limit(3)->get();
            foreach ($reviews as $review) {
                $activities[] = [
                    'icon' => 'star',
                    'message' => "Received a {$review->rating}-star review from {$review->client_name}",
                    'time' => $review->created_at->diffForHumans(),
                    'timestamp' => $review->created_at
                ];
            }

            // Recent commissions
            $commissions = $agent->commissions()->latest()->limit(3)->get();
            foreach ($commissions as $commission) {
                $activities[] = [
                    'icon' => 'dollar-sign',
                    'message' => "Earned commission of " . number_format($commission->amount, 2) . " SAR",
                    'time' => $commission->created_at->diffForHumans(),
                    'timestamp' => $commission->created_at
                ];
            }

            // Sort by timestamp
            usort($activities, function ($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            return array_slice($activities, 0, 5);
        });
    }

    /**
     * Invalidate agent cache.
     */
    public function invalidateCache(int $agentId): void
    {
        Cache::forget("agent_stats_{$agentId}");
        Cache::forget("agent_activities_{$agentId}");
        Cache::forget("agent_public_profile_{$agentId}"); // Invalidate public profile cache
    }

    /**
     * Update agent profile data and handle photo uploads.
     */
    public function updateAgentProfile(Agent $agent, array $profileData, $profilePhoto = null, $coverImage = null): Agent
    {
        if ($profilePhoto) {
            if ($agent->profile && $agent->profile->profile_photo) {
                Storage::disk('public')->delete($agent->profile->profile_photo);
            }
            $profileData['profile_photo'] = $profilePhoto->store('agent-photos', 'public');
        }

        if ($coverImage) {
            if ($agent->profile && $agent->profile->cover_image) {
                Storage::disk('public')->delete($agent->profile->cover_image);
            }
            $profileData['cover_image'] = $coverImage->store('agent-covers', 'public');
        }

        $agent->profile()->updateOrCreate(['agent_id' => $agent->id], $profileData);
        $this->invalidateCache($agent->id); // Invalidate cache after update
        return $this->getAgentProfile($agent);
    }

    /**
     * Upload agent profile photo.
     */
    public function uploadAgentPhoto(Agent $agent, $photo): string
    {
        if ($agent->profile && $agent->profile->profile_photo) {
            Storage::disk('public')->delete($agent->profile->profile_photo);
        }
        $path = $photo->store('agent-photos', 'public');
        $agent->profile()->updateOrCreate(['agent_id' => $agent->id], ['profile_photo' => $path]);
        $this->invalidateCache($agent->id);
        return asset('storage/' . $path);
    }

    /**
     * Remove agent profile photo.
     */
    public function removeAgentPhoto(Agent $agent): void
    {
        if ($agent->profile && $agent->profile->profile_photo) {
            Storage::disk('public')->delete($agent->profile->profile_photo);
            $agent->profile->update(['profile_photo' => null]);
            $this->invalidateCache($agent->id);
        }
    }

    /**
     * Get public agent profile data with eager loaded relationships and stats.
     */
    public function getPublicAgentProfile(Agent $agent): array
    {
        $cacheKey = "agent_public_profile_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $agent->load([
                'profile',
                'properties' => function ($query) {
                    $query->with(['media', 'propertyType', 'location', 'price'])->where('status', 'active')->latest()->limit(6);
                },
                'reviews' => function ($query) {
                    $query->latest()->limit(10);
                },
                'certifications',
                'licenses'
            ]);

            $agent->loadCount([
                'properties',
                'properties as sold_properties_count' => fn($q) => $q->where('status', 'sold'),
                'reviews'
            ]);
            $agent->loadAvg('reviews', 'rating');

            $stats = [
                'total_properties' => $agent->properties_count,
                'sold_properties' => $agent->sold_properties_count,
                'average_rating' => $agent->reviews_avg_rating ?? 0,
                'total_reviews' => $agent->reviews_count,
                'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
            ];

            return compact('agent', 'stats');
        });
    }

    /**
     * Get paginated agents with filters and caching.
     */
    public function getAgentsPaginated(array $filters, int $perPage = 12, bool $forDirectory = false)
    {
        $cacheKey = 'agents_paginated_' . md5(json_encode($filters) . $perPage . $forDirectory);

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($filters, $perPage, $forDirectory) {
            return $this->agentRepository->getPaginated($filters, $perPage, $forDirectory);
        });
    }

    /**
     * Get agent specializations with caching.
     */
    public function getAgentSpecializations(): \Illuminate\Support\Collection
    {
        return Cache::remember('agent_specializations', $this->cacheTTL, function () {
            return $this->agentRepository->getSpecializations();
        });
    }

    /**
     * Get agent details with eager loaded relationships and stats for show page.
     */
    public function getAgentDetails(Agent $agent): array
    {
        $cacheKey = "agent_details_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $agent->load(['profile', 'user', 'company', 'properties' => function ($query) {
                $query->with(['media', 'propertyType', 'location', 'price'])->latest()->limit(10);
            }, 'reviews' => function ($query) {
                $query->latest()->limit(5);
            }]);

            $stats = [
                'total_properties' => $agent->properties()->count(),
                'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
                'average_rating' => $agent->reviews()->avg('rating') ?? 0,
                'total_reviews' => $agent->reviews()->count(),
                'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
            ];

            return compact('agent', 'stats');
        });
    }

    /**
     * Get filtered agents for dropdowns/APIs with caching.
     */
    public function getFilteredAgents(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $cacheKey = 'filtered_agents_' . md5(json_encode($filters));

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($filters) {
            return $this->agentRepository->getFiltered($filters);
        });
    }

    /**
     * Get agent performance metrics.
     */
    public function getAgentPerformanceMetrics(Agent $agent): array
    {
        $cacheKey = "agent_performance_metrics_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $currentMonth = now()->startOfMonth();
            
            // Get metrics from database
            $dbMetrics = \DB::table('agent_performance_metrics')
                ->where('agent_id', $agent->id)
                ->where('period', 'monthly')
                ->where('period_start', '>=', $currentMonth)
                ->pluck('value', 'metric_type')
                ->toArray();
            
            return [
                'total_sales' => $dbMetrics['total_sales'] ?? 0,
                'commission_earned' => $dbMetrics['commission_earned'] ?? 0,
                'properties_listed' => $dbMetrics['properties_listed'] ?? 0,
                'satisfaction_rate' => $dbMetrics['satisfaction_rate'] ?? 0
            ];
        });
    }

    /**
     * Get agent monthly performance data.
     */
    public function getAgentMonthlyPerformance(Agent $agent, int $months = 12): array
    {
        $cacheKey = "agent_monthly_performance_{$agent->id}_{$months}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent, $months) {
            return \DB::table('agent_activities')
                ->where('agent_id', $agent->id)
                ->orderBy('created_at', 'desc')
                ->limit($months)
                ->get()
                ->map(function ($activity) {
                    return [
                        'title' => $activity->title,
                        'date' => \Carbon\Carbon::parse($activity->created_at)->format('Y-m-d'),
                        'value' => $activity->value,
                        'status' => $activity->status,
                        'icon' => $activity->icon
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get agent ranking data.
     */
    public function getAgentRanking(Agent $agent): array
    {
        $cacheKey = "agent_ranking_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            // Get all agents with their sales for comparison
            $agentRankings = \DB::table('agent_performance_metrics')
                ->where('metric_type', 'total_sales')
                ->where('period', 'monthly')
                ->where('period_start', '>=', now()->startOfMonth())
                ->join('agents', 'agent_performance_metrics.agent_id', '=', 'agents.id')
                ->select('agents.id', 'agents.name', 'agent_performance_metrics.value as total_sales')
                ->orderBy('total_sales', 'desc')
                ->get()
                ->toArray();

            $currentAgentRank = array_search($agent->id, array_column($agentRankings, 'id')) + 1;

            return [
                'current_rank' => $currentAgentRank,
                'total_agents' => count($agentRankings),
                'rankings' => $agentRankings
            ];
        });
    }

    /**
     * Get agent goals data.
     */
    public function getAgentGoals(Agent $agent): array
    {
        $cacheKey = "agent_goals_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            $currentMonth = now()->startOfMonth();
            $metrics = \DB::table('agent_performance_metrics')
                ->where('agent_id', $agent->id)
                ->where('period', 'monthly')
                ->where('period_start', '>=', $currentMonth)
                ->pluck('value', 'metric_type')
                ->toArray();

            // Calculate progress percentages
            $targets = [
                'monthly_sales_target' => 30,
                'commission_target' => 20000,
                'satisfaction_target' => 95
            ];

            return [
                'monthly_sales_progress' => min(100, round(($metrics['total_sales'] ?? 0) / $targets['monthly_sales_target'] * 100)),
                'monthly_sales_current' => $metrics['total_sales'] ?? 0,
                'monthly_sales_target' => $targets['monthly_sales_target'],
                'commission_progress' => min(100, round(($metrics['commission_earned'] ?? 0) / $targets['commission_target'] * 100)),
                'commission_current' => $metrics['commission_earned'] ?? 0,
                'commission_target' => $targets['commission_target'],
                'satisfaction_progress' => min(100, round(($metrics['satisfaction_rate'] ?? 0) / $targets['satisfaction_target'] * 100)),
                'satisfaction_current' => $metrics['satisfaction_rate'] ?? 0,
                'satisfaction_target' => $targets['satisfaction_target'],
                'active_goals' => [
                    ['title' => 'Monthly Sales Target', 'description' => 'Achieve 30 sales this month', 'progress' => min(100, round(($metrics['total_sales'] ?? 0) / $targets['monthly_sales_target'] * 100)), 'status' => 'on-track', 'due_date' => now()->endOfMonth()->format('Y-m-d')],
                    ['title' => 'Client Satisfaction', 'description' => 'Maintain 95% satisfaction rate', 'progress' => min(100, round(($metrics['satisfaction_rate'] ?? 0) / $targets['satisfaction_target'] * 100)), 'status' => 'on-track', 'due_date' => now()->endOfMonth()->format('Y-m-d')]
                ],
                'completed_goals' => [
                    ['title' => 'Q4 Sales Goal', 'completed_date' => now()->subMonth()->endOfMonth()->format('Y-m-d'), 'achievement' => 'Exceeded Target', 'result' => '125% of goal achieved']
                ]
            ];
        });
    }

    /**
     * Get agent rankings for display.
     */
    public function getAgentRankings(string $period = 'monthly'): array
    {
        $cacheKey = "agent_rankings_{$period}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($period) {
            $periodStart = $period === 'monthly' ? now()->startOfMonth() : 
                          ($period === 'quarterly' ? now()->startOfQuarter() : now()->startOfYear());

            return \DB::table('agent_performance_metrics')
                ->where('metric_type', 'total_sales')
                ->where('period', $period)
                ->where('period_start', '>=', $periodStart)
                ->join('agents', 'agent_performance_metrics.agent_id', '=', 'agents.id')
                ->join('users', 'agents.user_id', '=', 'users.id')
                ->select('agents.id', 'users.name as agent_name', 'agent_performance_metrics.value as sales', 'agents.company_id')
                ->orderBy('sales', 'desc')
                ->limit(20)
                ->get()
                ->map(function ($agent, $index) {
                    return [
                        'rank' => $index + 1,
                        'name' => $agent->agent_name,
                        'sales' => $agent->sales,
                        'commission' => $agent->sales * 0.03, // Assuming 3% commission
                        'rating' => rand(4.0, 5.0),
                        'status' => 'Active'
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get agent statistics with caching.
     */
    public function getAgentStatistics(Agent $agent): array
    {
        $cacheKey = "agent_statistics_{$agent->id}";

        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($agent) {
            return [
                'total_properties' => $agent->properties()->count(),
                'active_listings' => $agent->properties()->where('status', 'published')->count(),
                'sold_properties' => $agent->properties()->where('status', 'sold')->count(),
                'total_reviews' => $agent->reviews()->count(),
                'average_rating' => $agent->reviews()->avg('rating') ?? 0,
                'total_commissions' => $agent->commissions()->sum('amount'),
                'experience_years' => $agent->profile ? $agent->profile->experience_years : 0,
                'member_since' => $agent->created_at->format('M d, Y'),
            ];
        });
    }
}
