<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\AuctionResult;
use App\Repositories\Contracts\AuctionRepositoryInterface;
use App\Repositories\Contracts\AuctionResultRepositoryInterface;
use App\Services\AuctionResultService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionResultController extends Controller
{
    protected $auctionResultRepository;
    protected $auctionRepository;
    protected $auctionResultService;

    public function __construct(
        AuctionResultRepositoryInterface $auctionResultRepository,
        AuctionRepositoryInterface $auctionRepository,
        AuctionResultService $auctionResultService
    ) {
        $this->auctionResultRepository = $auctionResultRepository;
        $this->auctionRepository = $auctionRepository;
        $this->auctionResultService = $auctionResultService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['winner_id', 'creator_id', 'date_from', 'date_to', 'min_price', 'max_price']);
        $results = $this->auctionResultRepository->getFilteredResults($filters);
        $stats = $this->auctionResultRepository->getStats();

        return view('auctions.results', compact('results', 'stats'));
    }

    public function show($id)
    {
        $result = $this->auctionResultRepository->findById($id, ['*'], [
            'auction.property.images',
            'auction.property.features',
            'winner',
            'auction.creator',
            'auction.bids.user',
            'contract'
        ]);

        if (!$result) {
            abort(404);
        }

        $bidHistory = $this->auctionRepository->getBidHistory($result->auction_id);
        
        $participantStats = $this->getParticipantStats($result->auction);

        return view('auctions.result-detail', compact(
            'result',
            'bidHistory',
            'participantStats'
        ));
    }

    public function create(Request $request, $auctionId)
    {
        $auction = $this->auctionRepository->findById($auctionId);

        if (!$auction || $auction->seller_id !== Auth::id()) {
            abort(404);
        }

        try {
            $result = $this->auctionResultService->createResult($auction, $request->notes);
            
            if (!$result) {
                 return back()->with('error', 'No bids placed on this auction');
            }

            return redirect()->route('auctions.results.show', $result->id)
                ->with('success', 'Auction results processed successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to process auction results');
        }
    }

    public function confirmWinner(Request $request, $id)
    {
        $result = $this->auctionResultRepository->findById($id);
        
        if (!$result || $result->auction->seller_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $this->auctionResultService->confirmWinner($result);

        return back()->with('success', 'Winner confirmed successfully');
    }

    public function rejectWinner(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:1000'
        ]);

        $result = $this->auctionResultRepository->findById($id);

        if (!$result || $result->auction->seller_id !== Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $this->auctionResultService->rejectWinner($result, $request->reason);

        return back()->with('success', 'Winner rejected and next bidder considered');
    }

    public function myResults()
    {
        $userId = Auth::id();
        $wonAuctions = $this->auctionResultRepository->getWonByUser($userId);
        $createdAuctions = $this->auctionResultRepository->getCreatedByUser($userId);

        return view('auctions.my-results', compact('wonAuctions', 'createdAuctions'));
    }

    public function downloadReport($id)
    {
        $result = $this->auctionResultRepository->findById($id, ['*'], [
            'auction.property',
            'winner',
            'auction.creator',
            'auction.bids.user'
        ]);

        if (!$result) {
            abort(404);
        }

        // Check if user has permission
        if ($result->winner_id !== Auth::id() && $result->auction->seller_id !== Auth::id()) {
            abort(403);
        }

        $pdf = $this->generateResultPDF($result);

        return $pdf->download('auction-result-' . $result->id . '.pdf');
    }

    public function getStats()
    {
        $stats = $this->auctionResultRepository->getStats();
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

    private function generateResultPDF(AuctionResult $result)
    {
        // Placeholder
        return response()->make('PDF content', 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="auction-result.pdf"'
        ]);
    }
}
