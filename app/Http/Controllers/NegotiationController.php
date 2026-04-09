<?php

namespace App\Http\Controllers;

use App\Models\Negotiation;
use App\Models\Offer;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NegotiationController extends Controller
{
    public function start(Request $request, $offerId)
    {
        $offer = Offer::with('property')->findOrFail($offerId);

        // Check if user can start negotiation
        if (!$this->canStartNegotiation($offer)) {
            return back()->with('error', 'Cannot start negotiation for this offer');
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'proposed_terms' => 'nullable|array',
            'proposed_terms.*' => 'string'
        ]);

        $negotiation = Negotiation::create([
            'offer_id' => $offer->id,
            'initiated_by' => Auth::id(),
            'status' => 'active',
            'message' => $request->message,
            'proposed_terms' => $request->proposed_terms ?? [],
            'expires_at' => now()->addDays(7)
        ]);

        // Update offer status
        $offer->update(['status' => 'under_negotiation']);

        // Add participants
        $negotiation->participants()->createMany([
            ['user_id' => $offer->user_id],
            ['user_id' => $offer->property->user_id]
        ]);

        // Notify the other party
        $recipient = $offer->user_id === Auth::id() ? $offer->property->user : $offer->user;
        
        $recipient->notifications()->create([
            'title' => 'Negotiation Started',
            'message' => 'A negotiation has been started for your offer',
            'type' => 'negotiation',
            'action_url' => '/negotiations/' . $negotiation->id,
            'action_text' => 'View Negotiation'
        ]);

        return redirect()->route('negotiations.show', $negotiation->id)
            ->with('success', 'Negotiation started successfully');
    }

    public function show($id)
    {
        $negotiation = Negotiation::with([
            'offer.property.images',
            'offer.user',
            'offer.property.user',
            'participants.user',
            'messages.user',
            'documents'
        ])->findOrFail($id);

        // Check if user is participant
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists()) {
            abort(403);
        }

        $messages = $negotiation->messages()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        return view('negotiations.show', compact('negotiation', 'messages'));
    }

    public function sendMessage(Request $request, $id)
    {
        $negotiation = Negotiation::findOrFail($id);

        // Check if user is participant and negotiation is active
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists() ||
            $negotiation->status !== 'active') {
            return back()->with('error', 'Cannot send message to this negotiation');
        }

        $request->validate([
            'message' => 'required|string|max:2000',
            'type' => 'required|in:text,proposal,question,answer'
        ]);

        $message = $negotiation->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'type' => $request->type
        ]);

        // Update last activity
        $negotiation->update(['last_activity_at' => now()]);

        // Notify other participants
        $negotiation->participants->each(function ($participant) use ($message, $negotiation) {
            if ($participant->user_id !== Auth::id()) {
                $participant->user->notifications()->create([
                    'title' => 'New Negotiation Message',
                    'message' => 'New message in negotiation: ' . substr($message->message, 0, 50) . '...',
                    'type' => 'negotiation',
                    'action_url' => '/negotiations/' . $negotiation->id,
                    'action_text' => 'View Message'
                ]);
            }
        });

        return back()->with('success', 'Message sent successfully');
    }

    public function proposeTerms(Request $request, $id)
    {
        $negotiation = Negotiation::findOrFail($id);

        // Check permission
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists() ||
            $negotiation->status !== 'active') {
            return back()->with('error', 'Cannot propose terms for this negotiation');
        }

        $request->validate([
            'terms' => 'required|array',
            'terms.*' => 'string',
            'message' => 'required|string|max:1000'
        ]);

        $proposal = $negotiation->proposals()->create([
            'user_id' => Auth::id(),
            'terms' => $request->terms,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        // Notify other participants
        $negotiation->participants->each(function ($participant) use ($proposal, $negotiation) {
            if ($participant->user_id !== Auth::id()) {
                $participant->user->notifications()->create([
                    'title' => 'New Terms Proposed',
                    'message' => 'New terms have been proposed in the negotiation',
                    'type' => 'negotiation',
                    'action_url' => '/negotiations/' . $negotiation->id,
                    'action_text' => 'View Proposal'
                ]);
            }
        });

        return back()->with('success', 'Terms proposed successfully');
    }

    public function acceptProposal(Request $request, $id, $proposalId)
    {
        $negotiation = Negotiation::findOrFail($id);
        $proposal = $negotiation->proposals()->findOrFail($proposalId);

        // Check permission
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists() ||
            $proposal->user_id === Auth::id()) {
            return back()->with('error', 'Cannot accept this proposal');
        }

        // Accept proposal
        $proposal->update(['status' => 'accepted', 'accepted_at' => now()]);
        
        // Reject other pending proposals
        $negotiation->proposals()
            ->where('id', '!=', $proposalId)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Complete negotiation
        $negotiation->update([
            'status' => 'completed',
            'completed_at' => now(),
            'final_terms' => $proposal->terms
        ]);

        // Update offer with accepted terms
        $negotiation->offer->update([
            'status' => 'accepted',
            'accepted_at' => now()
        ]);

        // Update property status
        $negotiation->offer->property->update(['status' => 'under_contract']);

        // Notify all participants
        $negotiation->participants->each(function ($participant) use ($negotiation) {
            $participant->user->notifications()->create([
                'title' => 'Negotiation Completed',
                'message' => 'The negotiation has been completed successfully',
                'type' => 'negotiation',
                'action_url' => '/negotiations/' . $negotiation->id,
                'action_text' => 'View Results'
            ]);
        });

        // Create contract
        $contract = Contract::create([
            'property_id' => $negotiation->offer->property_id,
            'buyer_id' => $negotiation->offer->user_id,
            'seller_id' => $negotiation->offer->property->user_id,
            'amount' => $negotiation->offer->amount,
            'status' => 'pending',
            'offer_id' => $negotiation->offer->id,
            'negotiation_id' => $negotiation->id
        ]);

        return redirect()->route('contracts.show', $contract->id)
            ->with('success', 'Negotiation completed and contract created');
    }

    public function rejectProposal(Request $request, $id, $proposalId)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $negotiation = Negotiation::findOrFail($id);
        $proposal = $negotiation->proposals()->findOrFail($proposalId);

        // Check permission
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists() ||
            $proposal->user_id === Auth::id()) {
            return back()->with('error', 'Cannot reject this proposal');
        }

        $proposal->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejection_reason' => $request->reason
        ]);

        // Notify proposal creator
        $proposal->user->notifications()->create([
            'title' => 'Proposal Rejected',
            'message' => 'Your proposal has been rejected',
            'type' => 'negotiation',
            'action_url' => '/negotiations/' . $negotiation->id,
            'action_text' => 'View Negotiation'
        ]);

        return back()->with('success', 'Proposal rejected successfully');
    }

    public function pause(Request $request, $id)
    {
        $negotiation = Negotiation::findOrFail($id);

        // Check if user is negotiation initiator
        if ($negotiation->initiated_by !== Auth::id()) {
            return back()->with('error', 'Only the initiator can pause the negotiation');
        }

        $negotiation->update([
            'status' => 'paused',
            'paused_at' => now()
        ]);

        // Notify participants
        $negotiation->participants->each(function ($participant) use ($negotiation) {
            if ($participant->user_id !== Auth::id()) {
                $participant->user->notifications()->create([
                    'title' => 'Negotiation Paused',
                    'message' => 'The negotiation has been paused',
                    'type' => 'negotiation',
                    'action_url' => '/negotiations/' . $negotiation->id,
                    'action_text' => 'View Negotiation'
                ]);
            }
        });

        return back()->with('success', 'Negotiation paused successfully');
    }

    public function resume(Request $request, $id)
    {
        $negotiation = Negotiation::findOrFail($id);

        // Check if user is negotiation initiator
        if ($negotiation->initiated_by !== Auth::id()) {
            return back()->with('error', 'Only the initiator can resume the negotiation');
        }

        $negotiation->update([
            'status' => 'active',
            'resumed_at' => now()
        ]);

        // Notify participants
        $negotiation->participants->each(function ($participant) use ($negotiation) {
            if ($participant->user_id !== Auth::id()) {
                $participant->user->notifications()->create([
                    'title' => 'Negotiation Resumed',
                    'message' => 'The negotiation has been resumed',
                    'type' => 'negotiation',
                    'action_url' => '/negotiations/' . $negotiation->id,
                    'action_text' => 'View Negotiation'
                ]);
            }
        });

        return back()->with('success', 'Negotiation resumed successfully');
    }

    public function terminate(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $negotiation = Negotiation::findOrFail($id);

        // Check if user is participant
        if (!$negotiation->participants()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'Unauthorized');
        }

        $negotiation->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'termination_reason' => $request->reason
        ]);

        // Notify other party
        $otherParty = $negotiation->offer->user_id === Auth::id() ? $negotiation->offer->property->user : $negotiation->offer->user;
        
        $otherParty->notifications()->create([
            'title' => 'Negotiation Terminated',
            'message' => 'The negotiation has been terminated',
            'type' => 'negotiation',
            'action_url' => '/offers/' . $negotiation->offer->id,
            'action_text' => 'View Offer'
        ]);

        return redirect()->route('offers.show', $negotiation->offer->id)
            ->with('success', 'Negotiation terminated successfully');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $negotiations = Negotiation::with(['offer.property', 'offer.user', 'participants.user'])
            ->whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('last_activity_at', 'desc')
            ->paginate(15);

        $stats = [
            'total_negotiations' => Negotiation::count(),
            'active_negotiations' => Negotiation::where('status', 'active')->count(),
            'completed_negotiations' => Negotiation::where('status', 'completed')->count(),
            'my_negotiations' => Negotiation::whereHas('participants', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count()
        ];

        return view('negotiations.index', compact('negotiations', 'stats'));
    }

    private function canStartNegotiation(Offer $offer): bool
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

        // Check if negotiation already exists
        $existingNegotiation = Negotiation::where('offer_id', $offer->id)
            ->whereIn('status', ['active', 'paused'])
            ->first();

        if ($existingNegotiation) {
            return false;
        }

        return true;
    }
}
