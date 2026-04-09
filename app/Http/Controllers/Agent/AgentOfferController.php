<?php

namespace App\Http\Controllers\Agent;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Models\Property;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AgentOfferController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function index(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $offers = Offer::with(['property', 'buyer', 'seller'])
            ->where(function($query) use ($agent) {
                $query->where('agent_id', $agent->id)
                      ->orWhereHas('property', function($q) use ($agent) {
                          $q->where('agent_id', $agent->id);
                      });
            })
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->when($request->property_id, function($query, $propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $properties = Property::where('agent_id', $agent->id)->pluck('title', 'id');
        $stats = $this->getOfferStats($agent);

        return view('agent.offers.index', compact('offers', 'properties', 'stats'));
    }

    public function received(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $offers = Offer::with(['property', 'buyer'])
            ->whereHas('property', function($query) use ($agent) {
                $query->where('agent_id', $agent->id);
            })
            ->where('type', 'purchase')
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('agent.offers.received', compact('offers'));
    }

    public function sent(Request $request)
    {
        $agent = Auth::user()->agent;
        
        $offers = Offer::with(['property', 'seller'])
            ->where('agent_id', $agent->id)
            ->where('type', 'sale')
            ->when($request->status, function($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('agent.offers.sent', compact('offers'));
    }

    public function create()
    {
        $agent = Auth::user()->agent;
        
        $properties = Property::where('agent_id', $agent->id)
            ->where('status', 'active')
            ->pluck('title', 'id');
            
        $leads = Lead::where('agent_id', $agent->id)
            ->where('lead_status', 'qualified')
            ->pluck('full_name', 'id');

        return view('agent.offers.create', compact('properties', 'leads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'buyer_id' => 'required|exists:leads,id',
            'offer_price' => 'required|numeric|min:0',
            'offer_type' => 'required|in:fixed,percentage',
            'contingencies' => 'nullable|array',
            'expiry_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $agent = Auth::user()->agent;
        
        $offer = Offer::create([
            'property_id' => $request->property_id,
            'buyer_id' => $request->buyer_id,
            'agent_id' => $agent->id,
            'offer_price' => $request->offer_price,
            'offer_type' => $request->offer_type,
            'contingencies' => $request->contingencies ?? [],
            'expiry_date' => $request->expiry_date,
            'notes' => $request->notes,
            'status' => 'pending',
            'type' => 'sale',
        ]);

        return redirect()
            ->route('agent.offers.show', $offer)
            ->with('success', 'Offer created successfully');
    }

    public function show(Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        $offer->load(['property', 'buyer', 'seller', 'agent', 'counterOffers']);
        
        return view('agent.offers.show', compact('offer'));
    }

    public function edit(Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Cannot edit offer that is not pending');
        }
        
        $agent = Auth::user()->agent;
        $properties = Property::where('agent_id', $agent->id)->pluck('title', 'id');
        $leads = Lead::where('agent_id', $agent->id)->pluck('full_name', 'id');

        return view('agent.offers.edit', compact('offer', 'properties', 'leads'));
    }

    public function update(Request $request, Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Cannot update offer that is not pending');
        }

        $request->validate([
            'offer_price' => 'required|numeric|min:0',
            'offer_type' => 'required|in:fixed,percentage',
            'contingencies' => 'nullable|array',
            'expiry_date' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        $offer->update($request->all());

        return redirect()
            ->route('agent.offers.show', $offer)
            ->with('success', 'Offer updated successfully');
    }

    public function destroy(Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Cannot delete offer that is not pending');
        }

        $offer->delete();

        return redirect()
            ->route('agent.offers.index')
            ->with('success', 'Offer deleted successfully');
    }

    public function accept(Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Offer is not pending');
        }

        DB::transaction(function() use ($offer) {
            $offer->update([
                'status' => 'accepted',
                'accepted_at' => now(),
            ]);

            // Update property status if applicable
            if ($offer->property) {
                $offer->property->update(['status' => 'under_contract']);
            }

            // Create activity log
            activity()->causedBy(Auth::user())
                ->performedOn($offer)
                ->log('Offer accepted');
        });

        return back()->with('success', 'Offer accepted successfully');
    }

    public function reject(Request $request, Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Offer is not pending');
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $offer->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
        ]);

        return back()->with('success', 'Offer rejected successfully');
    }

    public function counter(Request $request, Offer $offer)
    {
        $this->authorizeOfferAccess($offer);
        
        if ($offer->status !== 'pending') {
            return back()->with('error', 'Cannot counter offer that is not pending');
        }

        $request->validate([
            'counter_price' => 'required|numeric|min:0',
            'counter_notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function() use ($offer, $request) {
            // Create counter offer
            $offer->counterOffers()->create([
                'original_offer_id' => $offer->id,
                'agent_id' => Auth::user()->agent->id,
                'counter_price' => $request->counter_price,
                'notes' => $request->counter_notes,
                'status' => 'pending',
            ]);

            // Update original offer status
            $offer->update(['status' => 'countered']);
        });

        return back()->with('success', 'Counter offer sent successfully');
    }

    private function authorizeOfferAccess(Offer $offer)
    {
        $agent = Auth::user()->agent;
        
        if (!$agent || ($offer->agent_id !== $agent->id && $offer->property->agent_id !== $agent->id)) {
            abort(403, 'Unauthorized access to this offer');
        }
    }

    private function getOfferStats($agent)
    {
        return [
            'total_offers' => Offer::where('agent_id', $agent->id)->count(),
            'pending_offers' => Offer::where('agent_id', $agent->id)->where('status', 'pending')->count(),
            'accepted_offers' => Offer::where('agent_id', $agent->id)->where('status', 'accepted')->count(),
            'rejected_offers' => Offer::where('agent_id', $agent->id)->where('status', 'rejected')->count(),
            'total_value' => Offer::where('agent_id', $agent->id)->where('status', 'accepted')->sum('offer_price'),
        ];
    }
}
