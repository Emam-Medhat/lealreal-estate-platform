<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Offer::with(['property.images', 'property.user', 'counterOffers']);

        if ($request->type === 'sent') {
            $query->where('buyer_id', $user->id);
        } elseif ($request->type === 'received') {
            $query->where('seller_id', $user->id);
        }

        // Filters
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->min_amount) {
            $query->where('offer_amount', '>=', $request->min_amount);
        }

        if ($request->max_amount) {
            $query->where('offer_amount', '<=', $request->max_amount);
        }

        $offers = $query->orderBy('created_at', 'desc')->paginate(15);

        $stats = [
            'total_sent' => Offer::where('buyer_id', $user->id)->count(),
            'total_received' => Offer::where('seller_id', $user->id)->count(),
            'pending' => Offer::where('status', 'pending')->count(),
            'accepted' => Offer::where('status', 'accepted')->count(),
            'rejected' => Offer::where('status', 'rejected')->count()
        ];

        return view('offers.index', compact('offers', 'stats'));
    }

    public function create($propertyId)
    {
        $property = Property::with(['images', 'features'])->findOrFail($propertyId);

        // Check if property is available for offers
        if ($property->status !== 'available') {
            return back()->with('error', 'Property is not available for offers');
        }

        // Check if user already has an active offer
        $existingOffer = Offer::where('user_id', Auth::id())
            ->where('property_id', $propertyId)
            ->whereIn('status', ['pending', 'under_negotiation'])
            ->first();

        if ($existingOffer) {
            return back()->with('error', 'You already have an active offer for this property');
        }

        return view('offers.create', compact('property'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'amount' => 'required|numeric|min:0',
            'message' => 'required|string|max:1000',
            'financing_type' => 'required|in:cash,mortgage,owner_financing',
            'contingencies' => 'nullable|array',
            'contingencies.*' => 'string',
            'closing_date' => 'nullable|date|after:today',
            'earnest_money' => 'nullable|numeric|min:0',
            'inspection_period' => 'nullable|integer|min:1|max:30'
        ]);

        $property = Property::findOrFail($request->property_id);

        // Check if property is available
        if ($property->status !== 'available') {
            return back()->with('error', 'Property is not available for offers');
        }

        $offer = Offer::create([
            'property_id' => $request->property_id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'message' => $request->message,
            'financing_type' => $request->financing_type,
            'contingencies' => $request->contingencies ?? [],
            'proposed_closing_date' => $request->closing_date,
            'earnest_money_amount' => $request->earnest_money,
            'inspection_period_days' => $request->inspection_period,
            'status' => 'pending',
            'expires_at' => now()->addDays(7) // Offers expire in 7 days
        ]);

        // Notify property owner
        $property->user->notifications()->create([
            'title' => 'New Offer Received',
            'message' => 'You received a new offer of $' . number_format($request->amount, 2) . ' for your property',
            'type' => 'offer',
            'action_url' => '/offers/' . $offer->id,
            'action_text' => 'View Offer'
        ]);

        return redirect()->route('offers.show', $offer->id)
            ->with('success', 'Offer submitted successfully');
    }

    public function show($id)
    {
        $user = Auth::user();
        
        $offer = Offer::with([
            'property.images',
            'property.features',
            'property.user',
            'user',
            'counterOffers.user'
        ])->findOrFail($id);

        // Check if user has permission to view
        if ($offer->user_id !== $user->id && $offer->property->user_id !== $user->id) {
            abort(403);
        }

        $canNegotiate = $offer->property->user_id === $user->id && 
                       in_array($offer->status, ['pending', 'under_negotiation']);

        $canRespond = $offer->user_id === $user->id && 
                     in_array($offer->status, ['pending', 'under_negotiation']);

        return view('offers.show', compact('offer', 'canNegotiate', 'canRespond'));
    }

    public function accept(Request $request, $id)
    {
        $offer = Offer::with('property')->findOrFail($id);

        // Check if user is property owner
        if ($offer->property->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if (!in_array($offer->status, ['pending', 'under_negotiation'])) {
            return back()->with('error', 'Cannot accept this offer');
        }

        $offer->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);

        // Update property status
        $offer->property->update(['status' => 'under_contract']);

        // Notify offerer
        $offer->user->notifications()->create([
            'title' => 'Offer Accepted!',
            'message' => 'Your offer for "' . $offer->property->title . '" has been accepted!',
            'type' => 'offer',
            'action_url' => '/offers/' . $offer->id,
            'action_text' => 'View Offer'
        ]);

        // Create contract
        $contract = Contract::create([
            'property_id' => $offer->property_id,
            'buyer_id' => $offer->user_id,
            'seller_id' => $offer->property->user_id,
            'amount' => $offer->amount,
            'status' => 'pending',
            'offer_id' => $offer->id
        ]);

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Offer accepted and contract created');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $offer = Offer::with('property')->findOrFail($id);

        // Check if user is property owner
        if ($offer->property->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        if (!in_array($offer->status, ['pending', 'under_negotiation'])) {
            return back()->with('error', 'Cannot reject this offer');
        }

        $offer->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        // Notify offerer
        $offer->user->notifications()->create([
            'title' => 'Offer Rejected',
            'message' => 'Your offer for "' . $offer->property->title . '" has been rejected',
            'type' => 'offer',
            'action_url' => '/offers/' . $offer->id,
            'action_text' => 'View Offer'
        ]);

        return back()->with('success', 'Offer rejected successfully');
    }

    public function withdraw($id)
    {
        $offer = Offer::where('user_id', Auth::id())->findOrFail($id);

        if (!in_array($offer->status, ['pending', 'under_negotiation'])) {
            return back()->with('error', 'Cannot withdraw this offer');
        }

        $offer->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now()
        ]);

        // Notify property owner
        $offer->property->user->notifications()->create([
            'title' => 'Offer Withdrawn',
            'message' => 'The offer for your property has been withdrawn',
            'type' => 'offer'
        ]);

        return back()->with('success', 'Offer withdrawn successfully');
    }

    public function negotiation($id)
    {
        $offer = Offer::with([
            'property.images',
            'property.user',
            'counterOffers.user'
        ])->findOrFail($id);

        // Check permission
        if ($offer->user_id !== Auth::id() && $offer->property->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($offer->status, ['pending', 'under_negotiation'])) {
            return back()->with('error', 'Negotiation not available for this offer');
        }

        return view('offers.negotiation', compact('offer'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,under_negotiation,accepted,rejected,withdrawn,expired'
        ]);

        $offer = Offer::findOrFail($id);

        // Check permission
        if ($offer->user_id !== Auth::id() && $offer->property->user_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $offer->update(['status' => $request->status]);

        return back()->with('success', 'Offer status updated successfully');
    }

    public function getStats()
    {
        $user = Auth::id();

        $stats = [
            'sent_offers' => Offer::where('user_id', $user)->count(),
            'received_offers' => Offer::whereHas('property', function ($query) use ($user) {
                $query->where('user_id', $user);
            })->count(),
            'accepted_offers' => Offer::where('status', 'accepted')->count(),
            'rejected_offers' => Offer::where('status', 'rejected')->count(),
            'pending_offers' => Offer::where('status', 'pending')->count(),
            'average_offer_amount' => Offer::avg('amount'),
            'total_offer_amount' => Offer::sum('amount')
        ];

        return response()->json(['stats' => $stats]);
    }
}
