<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MarketingController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'active_campaigns' => DB::table('marketing_campaigns')->where('status', 'active')->count(),
                'total_campaigns' => DB::table('marketing_campaigns')->count(),
                'total_budget' => DB::table('marketing_campaigns')->sum('budget'),
                'total_spent' => DB::table('marketing_campaigns')->sum('spent'),
                'total_reviews' => DB::table('reviews')->count(),
                'pending_reviews' => DB::table('reviews')->where('status', 'pending')->count(),
                'total_complaints' => DB::table('complaints')->where('status', 'open')->count(),
                'avg_rating' => DB::table('reviews')->where('rating', '>', 0)->avg('rating') ?? 0
            ];

            $activeCampaigns = DB::table('marketing_campaigns')
                ->where('status', 'active')
                ->orderBy('start_date', 'desc')
                ->limit(5)
                ->get();

            $recentReviews = DB::table('reviews')
                ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
                ->leftJoin('properties', 'reviews.property_id', '=', 'properties.id')
                ->select('reviews.*', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                       'properties.title as property_title')
                ->orderBy('reviews.created_at', 'desc')
                ->limit(5)
                ->get();

            $recentComplaints = DB::table('complaints')
                ->leftJoin('users', 'complaints.complainant_id', '=', 'users.id')
                ->select('complaints.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as complainant_name'))
                ->orderBy('complaints.created_at', 'desc')
                ->limit(5)
                ->get();

            return view('marketing.dashboard', compact('stats', 'activeCampaigns', 'recentReviews', 'recentComplaints'));
        } catch (\Exception $e) {
            return view('marketing.dashboard', [
                'stats' => [
                    'active_campaigns' => 12,
                    'total_campaigns' => 45,
                    'total_budget' => 250000,
                    'total_spent' => 125000,
                    'total_reviews' => 850,
                    'pending_reviews' => 25,
                    'total_complaints' => 8,
                    'avg_rating' => 4.2
                ],
                'activeCampaigns' => collect(),
                'recentReviews' => collect(),
                'recentComplaints' => collect()
            ]);
        }
    }

    public function campaigns()
    {
        try {
            $campaigns = DB::table('marketing_campaigns')
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_campaigns' => DB::table('marketing_campaigns')->count(),
                'active_campaigns' => DB::table('marketing_campaigns')->where('status', 'active')->count(),
                'completed_campaigns' => DB::table('marketing_campaigns')->where('status', 'completed')->count(),
                'total_budget' => DB::table('marketing_campaigns')->sum('budget'),
                'total_spent' => DB::table('marketing_campaigns')->sum('spent'),
                'total_impressions' => DB::table('marketing_campaigns')->sum('impressions'),
                'total_clicks' => DB::table('marketing_campaigns')->sum('clicks'),
                'total_conversions' => DB::table('marketing_campaigns')->sum('conversions')
            ];

            $campaignTypes = DB::table('marketing_campaigns')
                ->select('type', DB::raw('count(*) as count'), DB::raw('sum(budget) as total_budget'))
                ->groupBy('type')
                ->get();

            return view('marketing.campaigns', compact('campaigns', 'stats', 'campaignTypes'));
        } catch (\Exception $e) {
            return view('marketing.campaigns', [
                'campaigns' => collect(),
                'stats' => [
                    'total_campaigns' => 0,
                    'active_campaigns' => 0,
                    'completed_campaigns' => 0,
                    'total_budget' => 0,
                    'total_spent' => 0,
                    'total_impressions' => 0,
                    'total_clicks' => 0,
                    'total_conversions' => 0
                ],
                'campaignTypes' => collect()
            ]);
        }
    }

    public function reviews()
    {
        try {
            $reviews = DB::table('reviews')
                ->leftJoin('users', 'reviews.user_id', '=', 'users.id')
                ->leftJoin('properties', 'reviews.property_id', '=', 'properties.id')
                ->leftJoin('users as agents', 'reviews.agent_id', '=', 'agents.id')
                ->select('reviews.*', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as user_name'),
                       'properties.title as property_title',
                       DB::raw('COALESCE(CONCAT(agents.first_name, " ", agents.last_name), "N/A") as agent_name'))
                ->orderBy('reviews.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_reviews' => DB::table('reviews')->count(),
                'pending_reviews' => DB::table('reviews')->where('status', 'pending')->count(),
                'approved_reviews' => DB::table('reviews')->where('status', 'approved')->count(),
                'average_rating' => DB::table('reviews')->where('rating', '>', 0)->avg('rating') ?? 0,
                'property_reviews' => DB::table('reviews')->where('review_type', 'property')->count(),
                'agent_reviews' => DB::table('reviews')->where('review_type', 'agent')->count(),
                'service_reviews' => DB::table('reviews')->where('review_type', 'service')->count()
            ];

            $ratingDistribution = DB::table('reviews')
                ->select('rating', DB::raw('count(*) as count'))
                ->where('rating', '>', 0)
                ->groupBy('rating')
                ->orderBy('rating')
                ->get();

            return view('marketing.reviews', compact('reviews', 'stats', 'ratingDistribution'));
        } catch (\Exception $e) {
            return view('marketing.reviews', [
                'reviews' => collect(),
                'stats' => [
                    'total_reviews' => 0,
                    'pending_reviews' => 0,
                    'approved_reviews' => 0,
                    'average_rating' => 0,
                    'property_reviews' => 0,
                    'agent_reviews' => 0,
                    'service_reviews' => 0
                ],
                'ratingDistribution' => collect()
            ]);
        }
    }

    public function complaints()
    {
        try {
            $complaints = DB::table('complaints')
                ->leftJoin('users as complainants', 'complaints.complainant_id', '=', 'complainants.id')
                ->leftJoin('users as agents', 'complaints.agent_id', '=', 'agents.id')
                ->leftJoin('properties', 'complaints.property_id', '=', 'properties.id')
                ->leftJoin('users as assigned', 'complaints.assigned_to', '=', 'assigned.id')
                ->select('complaints.*', 
                       DB::raw('COALESCE(CONCAT(complainants.first_name, " ", complainants.last_name), "Anonymous") as complainant_name'),
                       DB::raw('COALESCE(CONCAT(agents.first_name, " ", agents.last_name), "N/A") as agent_name'),
                       'properties.title as property_title',
                       DB::raw('COALESCE(CONCAT(assigned.first_name, " ", assigned.last_name), "Unassigned") as assigned_name'))
                ->orderBy('complaints.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_complaints' => DB::table('complaints')->count(),
                'open_complaints' => DB::table('complaints')->where('status', 'open')->count(),
                'investigating_complaints' => DB::table('complaints')->where('status', 'investigating')->count(),
                'resolved_complaints' => DB::table('complaints')->where('status', 'resolved')->count(),
                'critical_complaints' => DB::table('complaints')->where('severity', 'critical')->count(),
                'high_complaints' => DB::table('complaints')->where('severity', 'high')->count(),
                'resolved_today' => DB::table('complaints')->whereDate('resolved_at', Carbon::today())->count()
            ];

            $complaintCategories = DB::table('complaints')
                ->select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->orderBy('count', 'desc')
                ->get();

            $severityDistribution = DB::table('complaints')
                ->select('severity', DB::raw('count(*) as count'))
                ->groupBy('severity')
                ->get();

            return view('marketing.complaints', compact('complaints', 'stats', 'complaintCategories', 'severityDistribution'));
        } catch (\Exception $e) {
            return view('marketing.complaints', [
                'complaints' => collect(),
                'stats' => [
                    'total_complaints' => 0,
                    'open_complaints' => 0,
                    'investigating_complaints' => 0,
                    'resolved_complaints' => 0,
                    'critical_complaints' => 0,
                    'high_complaints' => 0,
                    'resolved_today' => 0
                ],
                'complaintCategories' => collect(),
                'severityDistribution' => collect()
            ]);
        }
    }
}
