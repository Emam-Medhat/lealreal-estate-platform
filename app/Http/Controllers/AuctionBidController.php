<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionBid;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionBidController extends Controller
{
    public function placeBid(Request $request, $auctionId)
    {
        $auction = Auction::findOrFail($auctionId);

        if ($auction->status !== 'active') {
            return response()->json(['error' => 'Auction is not active'], 400);
        }

        if ($auction->end_time <= now()) {
            return response()->json(['error' => 'Auction has ended'], 400);
        }

        $request->validate([
            'amount' => 'required|numeric|min:' . ($auction->current_price + $auction->bid_increment)
        ]);

        $user = Auth::user();

        // Check if user is the auction creator
        if ($auction->created_by === $user->id) {
            return response()->json(['error' => 'Cannot bid on your own auction'], 403);
        }

        // Check if user has sufficient funds (if required)
        if ($this->requiresVerification($user, $request->amount)) {
            return response()->json(['error' => 'Account verification required for this bid amount'], 403);
        }

        DB::beginTransaction();

        try {
            // Place bid
            $bid = AuctionBid::create([
                'auction_id' => $auction->id,
                'user_id' => $user->id,
                'amount' => $request->amount,
                'is_auto_bid' => $request->auto_bid ?? false,
                'max_auto_bid' => $request->max_auto_bid ?? null
            ]);

            // Update auction current price
            $auction->update([
                'current_price' => $request->amount,
                'bid_count' => $auction->bid_count + 1,
                'last_bid_at' => now()
            ]);

            // Add user as participant if not already
            if (!$auction->participants()->where('user_id', $user->id)->exists()) {
                $auction->participants()->create(['user_id' => $user->id]);
            }

            // Auto-extend auction if enabled and bid is placed in last minutes
            if ($auction->auto_extend && $auction->end_time->diffInMinutes(now()) <= 5) {
                $auction->update([
                    'end_time' => $auction->end_time->addMinutes(5)
                ]);
            }

            // Handle auto-bids
            $this->processAutoBids($auction, $bid);

            // Notify previous highest bidder
            if ($auction->highestBid && $auction->highestBid->user_id !== $user->id) {
                $previousBidder = $auction->highestBid->user;
                $previousBidder->notifications()->create([
                    'title' => 'Outbid',
                    'message' => 'You have been outbid on "' . $auction->title . '". New bid: $' . number_format($request->amount, 2),
                    'type' => 'auction',
                    'action_url' => '/auctions/' . $auction->id,
                    'action_text' => 'Place New Bid'
                ]);
            }

            // Notify auction creator
            $auction->creator->notifications()->create([
                'title' => 'New Bid Placed',
                'message' => 'A new bid of $' . number_format($request->amount, 2) . ' has been placed on your auction',
                'type' => 'auction',
                'action_url' => '/auctions/' . $auction->id,
                'action_text' => 'View Auction'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'bid' => $bid->load('user'),
                'current_price' => $auction->current_price,
                'bid_count' => $auction->bid_count,
                'extended' => $auction->auto_extend && $auction->end_time->diffInMinutes(now()) <= 5
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to place bid'], 500);
        }
    }

    public function retractBid($bidId)
    {
        $bid = AuctionBid::where('user_id', Auth::id())
            ->with('auction')
            ->findOrFail($bidId);

        $auction = $bid->auction;

        if ($auction->status !== 'active') {
            return back()->with('error', 'Cannot retract bid on inactive auction');
        }

        if ($auction->end_time <= now()->addMinutes(5)) {
            return back()->with('error', 'Cannot retract bid in last 5 minutes');
        }

        // Check if this is the highest bid
        $isHighestBid = $auction->bids()->where('amount', '>', $bid->amount)->count() === 0;

        if ($isHighestBid) {
            return back()->with('error', 'Cannot retract highest bid');
        }

        $bid->delete();

        return back()->with('success', 'Bid retracted successfully');
    }

    public function myBids()
    {
        $user = Auth::user();

        $activeBids = AuctionBid::where('user_id', $user->id)
            ->whereHas('auction', function ($query) {
                $query->where('status', 'active');
            })
            ->with(['auction.property', 'auction.highestBid'])
            ->orderBy('created_at', 'desc')
            ->get();

        $wonBids = AuctionBid::where('user_id', $user->id)
            ->whereHas('auction', function ($query) {
                $query->where('status', 'completed')
                      ->where('winner_id', $user->id);
            })
            ->with(['auction.property', 'auction.result'])
            ->orderBy('created_at', 'desc')
            ->get();

        $lostBids = AuctionBid::where('user_id', $user->id)
            ->whereHas('auction', function ($query) {
                $query->where('status', 'completed')
                      ->where('winner_id', '!=', $user->id);
            })
            ->with(['auction.property', 'auction.result'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('auctions.my-bids', compact('activeBids', 'wonBids', 'lostBids'));
    }

    public function bidHistory($auctionId)
    {
        $auction = Auction::with(['bids.user', 'property'])
            ->findOrFail($auctionId);

        $bids = $auction->bids()
            ->with('user')
            ->orderBy('amount', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['bids' => $bids]);
    }

    public function getHighestBid($auctionId)
    {
        $auction = Auction::findOrFail($auctionId);
        $highestBid = $auction->bids()->with('user')->orderBy('amount', 'desc')->first();

        return response()->json(['highest_bid' => $highestBid]);
    }

    public function getBidStats($auctionId)
    {
        $auction = Auction::findOrFail($auctionId);

        $stats = [
            'total_bids' => $auction->bid_count,
            'unique_bidders' => $auction->bids()->distinct('user_id')->count('user_id'),
            'highest_bid' => $auction->current_price,
            'average_bid' => $auction->bids()->avg('amount'),
            'bid_increment' => $auction->bid_increment,
            'time_remaining' => $auction->end_time->diffForHumans(now(), true)
        ];

        return response()->json(['stats' => $stats]);
    }

    private function requiresVerification($user, $amount): bool
    {
        // Check if bid amount requires verification
        $threshold = config('auctions.verification_threshold', 10000);
        return $amount > $threshold && !$user->is_verified;
    }

    private function processAutoBids(Auction $auction, AuctionBid $newBid)
    {
        // Find auto-bids that are higher than the new bid
        $autoBids = $auction->bids()
            ->where('is_auto_bid', true)
            ->where('user_id', '!=', $newBid->user_id)
            ->where('max_auto_bid', '>', $newBid->amount)
            ->orderBy('max_auto_bid', 'desc')
            ->get();

        foreach ($autoBids as $autoBid) {
            $nextBidAmount = min(
                $newBid->amount + $auction->bid_increment,
                $autoBid->max_auto_bid
            );

            if ($nextBidAmount > $newBid->amount) {
                // Place auto-bid
                AuctionBid::create([
                    'auction_id' => $auction->id,
                    'user_id' => $autoBid->user_id,
                    'amount' => $nextBidAmount,
                    'is_auto_bid' => true,
                    'max_auto_bid' => $autoBid->max_auto_bid
                ]);

                $auction->update([
                    'current_price' => $nextBidAmount,
                    'bid_count' => $auction->bid_count + 1,
                    'last_bid_at' => now()
                ]);

                // Notify the auto-bidder
                $autoBid->user->notifications()->create([
                    'title' => 'Auto-Bid Placed',
                    'message' => 'Your auto-bid placed $' . number_format($nextBidAmount, 2) . ' on "' . $auction->title . '"',
                    'type' => 'auction',
                    'action_url' => '/auctions/' . $auction->id,
                    'action_text' => 'View Auction'
                ]);

                break; // Only process the highest auto-bid
            }
        }
    }
}
