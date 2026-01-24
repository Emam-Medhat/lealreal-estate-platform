<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionParticipant;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionParticipantController extends Controller
{
    public function join(Request $request, $auctionId)
    {
        $auction = Auction::findOrFail($auctionId);

        if ($auction->status !== 'active' && $auction->status !== 'upcoming') {
            return back()->with('error', 'Cannot join this auction');
        }

        $user = Auth::user();

        // Check if user is the auction creator
        if ($auction->created_by === $user->id) {
            return back()->with('error', 'Cannot join your own auction');
        }

        // Check if already a participant
        if ($auction->participants()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'You are already a participant');
        }

        // Check if user meets requirements
        if (!$this->meetsRequirements($user, $auction)) {
            return back()->with('error', 'You do not meet the requirements to join this auction');
        }

        $participant = $auction->participants()->create([
            'user_id' => $user->id,
            'joined_at' => now(),
            'status' => 'active'
        ]);

        // Notify auction creator
        $auction->creator->notifications()->create([
            'title' => 'New Participant',
            'message' => $user->name . ' has joined your auction: ' . $auction->title,
            'type' => 'auction',
            'action_url' => '/auctions/' . $auction->id,
            'action_text' => 'View Auction'
        ]);

        return back()->with('success', 'Successfully joined the auction');
    }

    public function leave($auctionId)
    {
        $user = Auth::user();
        
        $participant = AuctionParticipant::where('user_id', $user->id)
            ->where('auction_id', $auctionId)
            ->with('auction')
            ->firstOrFail();

        $auction = $participant->auction;

        // Check if user has active bids
        if ($auction->bids()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'Cannot leave auction with active bids');
        }

        $participant->delete();

        return back()->with('success', 'Successfully left the auction');
    }

    public function index($auctionId)
    {
        $auction = Auction::with(['participants.user', 'participants.bids'])
            ->findOrFail($auctionId);

        $participants = $auction->participants()
            ->with(['user', 'bids' => function ($query) {
                $query->orderBy('amount', 'desc')->first();
            }])
            ->orderBy('joined_at', 'asc')
            ->get();

        $stats = [
            'total_participants' => $participants->count(),
            'active_bidders' => $participants->filter(function ($p) {
                return $p->bids->isNotEmpty();
            })->count(),
            'average_bid' => $auction->bids()->avg('amount'),
            'highest_bidder' => $auction->highestBid?->user
        ];

        return view('auctions.participants', compact('auction', 'participants', 'stats'));
    }

    public function removeParticipant(Request $request, $auctionId, $participantId)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->findOrFail($auctionId);

        $participant = $auction->participants()
            ->findOrFail($participantId);

        // Check if participant has active bids
        if ($auction->bids()->where('user_id', $participant->user_id)->exists()) {
            return back()->with('error', 'Cannot remove participant with active bids');
        }

        $participant->delete();

        // Notify removed participant
        $participant->user->notifications()->create([
            'title' => 'Removed from Auction',
            'message' => 'You have been removed from the auction: ' . $auction->title,
            'type' => 'auction'
        ]);

        return back()->with('success', 'Participant removed successfully');
    }

    public function approveParticipant(Request $request, $auctionId, $participantId)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->findOrFail($auctionId);

        $participant = $auction->participants()
            ->findOrFail($participantId);

        $participant->update(['status' => 'approved']);

        // Notify participant
        $participant->user->notifications()->create([
            'title' => 'Auction Participation Approved',
            'message' => 'Your participation in "' . $auction->title . '" has been approved',
            'type' => 'auction',
            'action_url' => '/auctions/' . $auction->id,
            'action_text' => 'View Auction'
        ]);

        return back()->with('success', 'Participant approved successfully');
    }

    public function rejectParticipant(Request $request, $auctionId, $participantId)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->findOrFail($auctionId);

        $participant = $auction->participants()
            ->findOrFail($participantId);

        $participant->update(['status' => 'rejected']);

        // Notify participant
        $participant->user->notifications()->create([
            'title' => 'Auction Participation Rejected',
            'message' => 'Your participation in "' . $auction->title . '" has been rejected',
            'type' => 'auction'
        ]);

        return back()->with('success', 'Participant rejected successfully');
    }

    public function myParticipations()
    {
        $user = Auth::user();

        $participations = AuctionParticipant::where('user_id', $user->id)
            ->with(['auction.property', 'auction.highestBid', 'bids'])
            ->orderBy('joined_at', 'desc')
            ->get();

        $stats = [
            'total_auctions' => $participations->count(),
            'active_auctions' => $participations->filter(function ($p) {
                return $p->auction->status === 'active';
            })->count(),
            'won_auctions' => $participations->filter(function ($p) {
                return $p->auction->winner_id === $user->id;
            })->count(),
            'total_bids' => $participations->sum(function ($p) {
                return $p->bids->count();
            })
        ];

        return view('auctions.my-participations', compact('participations', 'stats'));
    }

    public function getParticipantStats($auctionId)
    {
        $auction = Auction::findOrFail($auctionId);

        $stats = [
            'total_participants' => $auction->participants()->count(),
            'active_bidders' => $auction->bids()->distinct('user_id')->count('user_id'),
            'new_participants_today' => $auction->participants()
                ->whereDate('joined_at', today())
                ->count(),
            'participant_growth' => $this->getParticipantGrowth($auction)
        ];

        return response()->json(['stats' => $stats]);
    }

    private function meetsRequirements($user, $auction): bool
    {
        // Check verification status
        if ($auction->requires_verification && !$user->is_verified) {
            return false;
        }

        // Check minimum account age
        if ($auction->min_account_age) {
            $minAge = now()->subDays($auction->min_account_age);
            if ($user->created_at > $minAge) {
                return false;
            }
        }

        // Check previous auction participation
        if ($auction->requires_experience) {
            $previousParticipations = AuctionParticipant::where('user_id', $user->id)->count();
            if ($previousParticipations < ($auction->min_participations ?? 0)) {
                return false;
            }
        }

        return true;
    }

    private function getParticipantGrowth(Auction $auction): array
    {
        $participants = $auction->participants()
            ->orderBy('joined_at', 'asc')
            ->get();

        $growth = [];
        $currentCount = 0;

        foreach ($participants as $participant) {
            $currentCount++;
            $growth[] = [
                'date' => $participant->joined_at->format('Y-m-d'),
                'count' => $currentCount
            ];
        }

        return $growth;
    }
}
