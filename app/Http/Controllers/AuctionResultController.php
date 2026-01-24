<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionResult;
use App\Models\UserNotification;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuctionResultController extends Controller
{
    public function index(Request $request)
    {
        $query = AuctionResult::with(['auction.property', 'winner', 'auction.creator'])
            ->whereHas('auction', function ($query) {
                $query->where('status', 'completed');
            });

        // Filters
        if ($request->winner_id) {
            $query->where('winner_id', $request->winner_id);
        }

        if ($request->creator_id) {
            $query->whereHas('auction', function ($query) use ($request) {
                $query->where('created_by', $request->creator_id);
            });
        }

        if ($request->date_from) {
            $query->whereDate('completed_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('completed_at', '<=', $request->date_to);
        }

        if ($request->min_price) {
            $query->where('final_price', '>=', $request->min_price);
        }

        if ($request->max_price) {
            $query->where('final_price', '<=', $request->max_price);
        }

        $results = $query->orderBy('completed_at', 'desc')->paginate(20);

        $stats = [
            'total_completed' => AuctionResult::count(),
            'total_revenue' => AuctionResult::sum('final_price'),
            'average_price' => AuctionResult::avg('final_price'),
            'this_month' => AuctionResult::whereMonth('completed_at', now()->month)->count()
        ];

        return view('auctions.results', compact('results', 'stats'));
    }

    public function show($id)
    {
        $result = AuctionResult::with([
            'auction.property.images',
            'auction.property.features',
            'winner',
            'auction.creator',
            'auction.bids.user',
            'contract'
        ])->findOrFail($id);

        $bidHistory = $result->auction->bids()
            ->with('user')
            ->orderBy('amount', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $participantStats = $this->getParticipantStats($result->auction);

        return view('auctions.result-detail', compact(
            'result',
            'bidHistory',
            'participantStats'
        ));
    }

    public function create(Request $request, $auctionId)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->where('status', 'active')
            ->findOrFail($auctionId);

        $highestBid = $auction->bids()->orderBy('amount', 'desc')->first();

        if (!$highestBid) {
            return back()->with('error', 'No bids placed on this auction');
        }

        DB::beginTransaction();

        try {
            // End the auction
            $auction->update([
                'status' => 'completed',
                'ended_at' => now()
            ]);

            // Create result
            $result = AuctionResult::create([
                'auction_id' => $auction->id,
                'winner_id' => $highestBid->user_id,
                'final_price' => $highestBid->amount,
                'completed_at' => now(),
                'notes' => $request->notes
            ]);

            // Update auction with winner info
            $auction->update([
                'winner_id' => $highestBid->user_id,
                'final_price' => $highestBid->amount
            ]);

            // Create initial contract
            $contract = Contract::create([
                'auction_id' => $auction->id,
                'buyer_id' => $highestBid->user_id,
                'seller_id' => $auction->created_by,
                'property_id' => $auction->property_id,
                'amount' => $highestBid->amount,
                'status' => 'pending',
                'created_at' => now()
            ]);

            // Notify winner
            $highestBid->user->notifications()->create([
                'title' => 'Auction Won!',
                'message' => 'Congratulations! You won the auction "' . $auction->title . '" for $' . number_format($highestBid->amount, 2),
                'type' => 'auction',
                'action_url' => '/auctions/results/' . $result->id,
                'action_text' => 'View Results'
            ]);

            // Notify other participants
            $auction->participants->each(function ($participant) use ($auction, $result) {
                if ($participant->user_id !== $result->winner_id) {
                    $participant->user->notifications()->create([
                        'title' => 'Auction Ended',
                        'message' => 'The auction "' . $auction->title . '" has ended. Winner: ' . $result->winner->name,
                        'type' => 'auction',
                        'action_url' => '/auctions/results/' . $result->id,
                        'action_text' => 'View Results'
                    ]);
                }
            });

            DB::commit();

            return redirect()->route('auctions.results.show', $result->id)
                ->with('success', 'Auction results processed successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to process auction results');
        }
    }

    public function confirmWinner(Request $request, $id)
    {
        $result = AuctionResult::findOrFail($id);
        $auction = $result->auction;

        // Check if user is auction creator
        if ($auction->created_by !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $result->update([
            'winner_confirmed_at' => now(),
            'status' => 'confirmed'
        ]);

        // Notify winner
        $result->winner->notifications()->create([
            'title' => 'Winner Confirmation',
            'message' => 'Your win in "' . $auction->title . '" has been confirmed',
            'type' => 'auction',
            'action_url' => '/contracts/' . $result->contract->id,
            'action_text' => 'View Contract'
        ]);

        return back()->with('success', 'Winner confirmed successfully');
    }

    public function rejectWinner(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $result = AuctionResult::findOrFail($id);
        $auction = $result->auction;

        // Check if user is auction creator
        if ($auction->created_by !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $result->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'rejected_at' => now()
        ]);

        // Notify winner
        $result->winner->notifications()->create([
            'title' => 'Win Rejected',
            'message' => 'Your win in "' . $auction->title . '" has been rejected. Reason: ' . $request->reason,
            'type' => 'auction'
        ]);

        // Consider next highest bidder
        $nextBid = $auction->bids()
            ->where('user_id', '!=', $result->winner_id)
            ->orderBy('amount', 'desc')
            ->first();

        if ($nextBid) {
            // Update result with next winner
            $result->update([
                'winner_id' => $nextBid->user_id,
                'final_price' => $nextBid->amount,
                'status' => 'pending'
            ]);

            // Notify new winner
            $nextBid->user->notifications()->create([
                'title' => 'New Winner Selected',
                'message' => 'You have been selected as the new winner for "' . $auction->title . '"',
                'type' => 'auction',
                'action_url' => '/auctions/results/' . $result->id,
                'action_text' => 'View Results'
            ]);
        }

        return back()->with('success', 'Winner rejected and next bidder considered');
    }

    public function myResults()
    {
        $user = Auth::user();

        $wonAuctions = AuctionResult::where('winner_id', $user->id)
            ->with(['auction.property', 'contract'])
            ->orderBy('completed_at', 'desc')
            ->get();

        $createdAuctions = AuctionResult::whereHas('auction', function ($query) use ($user) {
            $query->where('created_by', $user->id);
        })->with(['auction.property', 'winner', 'contract'])
          ->orderBy('completed_at', 'desc')
          ->get();

        return view('auctions.my-results', compact('wonAuctions', 'createdAuctions'));
    }

    public function downloadReport($id)
    {
        $result = AuctionResult::with([
            'auction.property',
            'winner',
            'auction.creator',
            'auction.bids.user'
        ])->findOrFail($id);

        // Check if user has permission
        if ($result->winner_id !== Auth::id() && $result->auction->created_by !== Auth::id()) {
            abort(403);
        }

        $pdf = $this->generateResultPDF($result);

        return $pdf->download('auction-result-' . $result->id . '.pdf');
    }

    public function getStats()
    {
        $stats = [
            'total_completed' => AuctionResult::count(),
            'total_revenue' => AuctionResult::sum('final_price'),
            'average_price' => AuctionResult::avg('final_price'),
            'highest_price' => AuctionResult::max('final_price'),
            'lowest_price' => AuctionResult::min('final_price'),
            'this_month' => AuctionResult::whereMonth('completed_at', now()->month)->count(),
            'last_month' => AuctionResult::whereMonth('completed_at', now()->subMonth())->count(),
            'growth_rate' => $this->calculateGrowthRate()
        ];

        return response()->json(['stats' => $stats]);
    }

    private function getParticipantStats(Auction $auction): array
    {
        $participants = $auction->participants()->count();
        $bidders = $auction->bids()->distinct('user_id')->count('user_id');
        $totalBids = $auction->bids()->count();
        $averageBids = $participants > 0 ? $totalBids / $participants : 0;

        return [
            'total_participants' => $participants,
            'unique_bidders' => $bidders,
            'total_bids' => $totalBids,
            'average_bids_per_participant' => round($averageBids, 2)
        ];
    }

    private function calculateGrowthRate(): float
    {
        $thisMonth = AuctionResult::whereMonth('completed_at', now()->month)->count();
        $lastMonth = AuctionResult::whereMonth('completed_at', now()->subMonth())->count();

        if ($lastMonth === 0) {
            return 0;
        }

        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function generateResultPDF(AuctionResult $result)
    {
        // Generate PDF report (implementation would depend on PDF library)
        // This is a placeholder for PDF generation
        return response()->make('PDF content', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="auction-result.pdf"'
        ]);
    }
}
