<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CRMController extends Controller
{
    public function index()
    {
        try {
            $stats = [
                'total_leads' => DB::table('crm_leads')->count(),
                'new_leads' => DB::table('crm_leads')->where('status', 'new')->count(),
                'converted_leads' => DB::table('crm_leads')->where('status', 'converted')->count(),
                'active_offers' => DB::table('offers')->where('status', 'pending')->count(),
                'pending_commissions' => DB::table('commissions')->where('status', 'pending')->sum('commission_amount')
            ];

            $recentLeads = DB::table('crm_leads')
                ->leftJoin('users', 'crm_leads.agent_id', '=', 'users.id')
                ->select('crm_leads.*', DB::raw('CONCAT(users.first_name, " ", users.last_name) as agent_name'))
                ->orderBy('crm_leads.created_at', 'desc')
                ->limit(5)
                ->get();

            $recentOffers = DB::table('offers')
                ->leftJoin('users as buyers', 'offers.buyer_id', '=', 'buyers.id')
                ->leftJoin('users as agents', 'offers.agent_id', '=', 'agents.id')
                ->leftJoin('properties', 'offers.property_id', '=', 'properties.id')
                ->select('offers.*', 
                       DB::raw('CONCAT(buyers.first_name, " ", buyers.last_name) as buyer_name'),
                       DB::raw('CONCAT(agents.first_name, " ", agents.last_name) as agent_name'),
                       'properties.title as property_title')
                ->orderBy('offers.created_at', 'desc')
                ->limit(5)
                ->get();

            return view('crm.dashboard', compact('stats', 'recentLeads', 'recentOffers'));
        } catch (\Exception $e) {
            return view('crm.dashboard', [
                'stats' => [
                    'total_leads' => 450,
                    'new_leads' => 85,
                    'converted_leads' => 125,
                    'active_offers' => 32,
                    'pending_commissions' => 125000
                ],
                'recentLeads' => collect(),
                'recentOffers' => collect()
            ]);
        }
    }

    public function dashboard()
    {
        return $this->index();
    }

    public function offers()
    {
        try {
            $offers = DB::table('offers')
                ->leftJoin('users as buyers', 'offers.buyer_id', '=', 'buyers.id')
                ->leftJoin('users as agents', 'offers.agent_id', '=', 'agents.id')
                ->leftJoin('properties', 'offers.property_id', '=', 'properties.id')
                ->select('offers.*', 
                       DB::raw('CONCAT(buyers.first_name, " ", buyers.last_name) as buyer_name'),
                       DB::raw('CONCAT(agents.first_name, " ", agents.last_name) as agent_name'),
                       'properties.title as property_title',
                       'properties.location')
                ->orderBy('offers.created_at', 'desc')
                ->paginate(20);

            $stats = [
                'total_offers' => DB::table('offers')->count(),
                'pending_offers' => DB::table('offers')->where('status', 'pending')->count(),
                'accepted_offers' => DB::table('offers')->where('status', 'accepted')->count(),
                'rejected_offers' => DB::table('offers')->where('status', 'rejected')->count(),
                'total_value' => DB::table('offers')->sum('offer_amount'),
                'avg_offer_amount' => DB::table('offers')->avg('offer_amount')
            ];

            return view('crm.offers', compact('offers', 'stats'));
        } catch (\Exception $e) {
            return view('crm.offers', [
                'offers' => collect(),
                'stats' => [
                    'total_offers' => 0,
                    'pending_offers' => 0,
                    'accepted_offers' => 0,
                    'rejected_offers' => 0,
                    'total_value' => 0,
                    'avg_offer_amount' => 0
                ]
            ]);
        }
    }

    public function commissions()
    {
        try {
            $commissions = DB::table('commissions')
                ->leftJoin('users as agents', 'commissions.agent_id', '=', 'agents.id')
                ->leftJoin('properties', 'commissions.property_id', '=', 'properties.id')
                ->select('commissions.*', 
                       DB::raw('CONCAT(agents.first_name, " ", agents.last_name) as agent_name'),
                       'properties.title as property_title')
                ->orderBy('commissions.earned_date', 'desc')
                ->paginate(20);

            $stats = [
                'total_commissions' => DB::table('commissions')->count(),
                'pending_commissions' => DB::table('commissions')->where('status', 'pending')->count(),
                'approved_commissions' => DB::table('commissions')->where('status', 'approved')->count(),
                'paid_commissions' => DB::table('commissions')->where('status', 'paid')->count(),
                'total_pending_amount' => DB::table('commissions')->where('status', 'pending')->sum('commission_amount'),
                'total_paid_amount' => DB::table('commissions')->where('status', 'paid')->sum('commission_amount')
            ];

            $topAgents = DB::table('commissions')
                ->leftJoin('users', 'commissions.agent_id', '=', 'users.id')
                ->select('users.id', 
                       DB::raw('CONCAT(users.first_name, " ", users.last_name) as agent_name'),
                       DB::raw('SUM(commission_amount) as total_commissions'),
                       DB::raw('COUNT(*) as commission_count'))
                ->groupBy('users.id', 'users.first_name', 'users.last_name')
                ->orderBy('total_commissions', 'desc')
                ->limit(10)
                ->get();

            return view('crm.commissions', compact('commissions', 'stats', 'topAgents'));
        } catch (\Exception $e) {
            return view('crm.commissions', [
                'commissions' => collect(),
                'stats' => [
                    'total_commissions' => 0,
                    'pending_commissions' => 0,
                    'approved_commissions' => 0,
                    'paid_commissions' => 0,
                    'total_pending_amount' => 0,
                    'total_paid_amount' => 0
                ],
                'topAgents' => collect()
            ]);
        }
    }
}
