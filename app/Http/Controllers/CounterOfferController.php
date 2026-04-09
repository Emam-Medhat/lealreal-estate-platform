<?php

namespace App\Http\Controllers;

use App\Models\Offer;
use App\Models\CounterOffer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CounterOfferController extends Controller
{
    public function store(Request $request, $offerId)
    {
        $offer = Offer::with('property')->findOrFail($offerId);

        // Check if user can make counter offer
        if (!$this->canMakeCounterOffer($offer)) {
            return back()->with('error', 'Cannot make counter offer at this time');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0',
            'message' => 'required|string|max:1000',
            'changes' => 'nullable|array',
            'changes.*' => 'string',
            'valid_until' => 'nullable|date|after:today'
        ]);

        $counterOffer = CounterOffer::create([
            'offer_id' => $offer->id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'message' => $request->message,
            'changes' => $request->changes ?? [],
            'valid_until' => $request->valid_until ?? now()->addDays(3),
            'status' => 'pending'
        ]);

        // Update offer status
        $offer->update(['status' => 'under_negotiation']);

        // Notify the other party
        $recipient = $offer->user_id === Auth::id() ? $offer->property->user : $offer->user;
        
        $recipient->notifications()->create([
            'title' => 'Counter Offer Received',
            'message' => 'You received a counter offer of $' . number_format($request->amount, 2),
            'type' => 'counter_offer',
            'action_url' => '/offers/' . $offer->id . '/negotiation',
            'action_text' => 'View Counter Offer'
        ]);

        return back()->with('success', 'Counter offer sent successfully');
    }

    public function accept(Request $request, $id)
    {
        $counterOffer = CounterOffer::with(['offer.property', 'user'])->findOrFail($id);

        // Check if user can accept this counter offer
        if (!$this->canAcceptCounterOffer($counterOffer)) {
            return back()->with('error', 'Cannot accept this counter offer');
        }

        $counterOffer->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);

        // Update original offer
        $counterOffer->offer->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'amount' => $counterOffer->amount
        ]);

        // Update property status
        $counterOffer->offer->property->update(['status' => 'under_contract']);

        // Notify counter offer creator
        $counterOffer->user->notifications()->create([
            'title' => 'Counter Offer Accepted!',
            'message' => 'Your counter offer has been accepted',
            'type' => 'counter_offer',
            'action_url' => '/offers/' . $counterOffer->offer->id,
            'action_text' => 'View Offer'
        ]);

        // Create contract
        $contract = Contract::create([
            'property_id' => $counterOffer->offer->property_id,
            'buyer_id' => $counterOffer->offer->user_id,
            'seller_id' => $counterOffer->offer->property->user_id,
            'amount' => $counterOffer->amount,
            'status' => 'pending',
            'offer_id' => $counterOffer->offer->id,
            'counter_offer_id' => $counterOffer->id
        ]);

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Counter offer accepted and contract created');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $counterOffer = CounterOffer::with(['offer', 'user'])->findOrFail($id);

        // Check if user can reject this counter offer
        if (!$this->canRejectCounterOffer($counterOffer)) {
            return back()->with('error', 'Cannot reject this counter offer');
        }

        $counterOffer->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        // Reset offer status to pending
        $counterOffer->offer->update(['status' => 'pending']);

        // Notify counter offer creator
        $counterOffer->user->notifications()->create([
            'title' => 'Counter Offer Rejected',
            'message' => 'Your counter offer has been rejected',
            'type' => 'counter_offer',
            'action_url' => '/offers/' . $counterOffer->offer->id,
            'action_text' => 'View Offer'
        ]);

        return back()->with('success', 'Counter offer rejected successfully');
    }

    public function withdraw($id)
    {
        $counterOffer = CounterOffer::where('user_id', Auth::id())
            ->with('offer')
            ->findOrFail($id);

        if ($counterOffer->status !== 'pending') {
            return back()->with('error', 'Cannot withdraw this counter offer');
        }

        $counterOffer->update([
            'status' => 'withdrawn',
            'withdrawn_at' => now()
        ]);

        // Reset offer status
        $counterOffer->offer->update(['status' => 'pending']);

        // Notify the other party
        $recipient = $counterOffer->offer->user_id === Auth::id() ? 
                   $counterOffer->offer->property->user : 
                   $counterOffer->offer->user;

        $recipient->notifications()->create([
            'title' => 'Counter Offer Withdrawn',
            'message' => 'The counter offer has been withdrawn',
            'type' => 'counter_offer',
            'action_url' => '/offers/' . $counterOffer->offer->id,
            'action_text' => 'View Offer'
        ]);

        return back()->with('success', 'Counter offer withdrawn successfully');
    }

    public function index($offerId)
    {
        $offer = Offer::with(['property', 'user'])
            ->findOrFail($offerId);

        // Check permission
        if ($offer->user_id !== Auth::id() && $offer->property->user_id !== Auth::id()) {
            abort(403);
        }

        $counterOffers = $counterOffer = CounterOffer::where('offer_id', $offerId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('offers.counter-offers', compact('offer', 'counterOffers'));
    }

    public function show($id)
    {
        $counterOffer = CounterOffer::with([
            'offer.property.images',
            'offer.property.user',
            'offer.user',
            'user'
        ])->findOrFail($id);

        // Check permission
        if ($counterOffer->offer->user_id !== Auth::id() && 
            $counterOffer->offer->property->user_id !== Auth::id()) {
            abort(403);
        }

        $canRespond = ($counterOffer->offer->user_id === Auth::id() && $counterOffer->user_id === $counterOffer->offer->property->user_id) ||
                     ($counterOffer->offer->property->user_id === Auth::id() && $counterOffer->user_id === $counterOffer->offer->user_id);

        return view('offers.counter-offer-detail', compact('counterOffer', 'canRespond'));
    }

    public function getHistory($offerId)
    {
        $counterOffers = CounterOffer::where('offer_id', $offerId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['counter_offers' => $counterOffers]);
    }

    public function getStats()
    {
        $user = Auth::id();

        $stats = [
            'sent_counter_offers' => CounterOffer::where('user_id', $user)->count(),
            'received_counter_offers' => CounterOffer::whereHas('offer', function ($query) use ($user) {
                $query->whereHas('property', function ($query) use ($user) {
                    $query->where('user_id', $user);
                });
            })->count(),
            'accepted_counter_offers' => CounterOffer::where('status', 'accepted')->count(),
            'rejected_counter_offers' => CounterOffer::where('status', 'rejected')->count(),
            'pending_counter_offers' => CounterOffer::where('status', 'pending')->count(),
            'average_counter_amount' => CounterOffer::avg('amount'),
            'success_rate' => $this->calculateSuccessRate($user)
        ];

        return response()->json(['stats' => $stats]);
    }

    private function canMakeCounterOffer(Offer $offer): bool
    {
        $user = Auth::user();

        // Check if offer is in negotiable state
        if (!in_array($offer->status, ['pending', 'under_negotiation'])) {
            return false;
        }

        // Check if user is property owner or offer creator
        if ($offer->property->user_id !== $user->id && $offer->user_id !== $user->id) {
            return false;
        }

        // Check if there's already a pending counter offer from this user
        $existingCounterOffer = CounterOffer::where('offer_id', $offer->id)
            ->where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($existingCounterOffer) {
            return false;
        }

        return true;
    }

    private function canAcceptCounterOffer(CounterOffer $counterOffer): bool
    {
        $user = Auth::user();

        // Check if counter offer is pending
        if ($counterOffer->status !== 'pending') {
            return false;
        }

        // Check if user is the recipient of this counter offer
        if ($counterOffer->user_id === $user->id) {
            return false; // Cannot accept your own counter offer
        }

        // Check if user is involved in the original offer
        if ($counterOffer->offer->user_id !== $user->id && 
            $counterOffer->offer->property->user_id !== $user->id) {
            return false;
        }

        return true;
    }

    private function canRejectCounterOffer(CounterOffer $counterOffer): bool
    {
        return $this->canAcceptCounterOffer($counterOffer);
    }

    private function calculateSuccessRate($userId): float
    {
        $total = CounterOffer::where('user_id', $userId)->count();
        $accepted = CounterOffer::where('user_id', $userId)
            ->where('status', 'accepted')
            ->count();

        if ($total === 0) {
            return 0;
        }

        return round(($accepted / $total) * 100, 2);
    }
}
