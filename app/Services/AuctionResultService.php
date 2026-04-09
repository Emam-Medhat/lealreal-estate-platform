<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\AuctionResult;
use App\Models\Contract;
use App\Repositories\Contracts\AuctionResultRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionResultService
{
    protected $auctionResultRepository;

    public function __construct(AuctionResultRepositoryInterface $auctionResultRepository)
    {
        $this->auctionResultRepository = $auctionResultRepository;
    }

    public function createResult(Auction $auction, string $notes = null): ?AuctionResult
    {
        $highestBid = $auction->bids()->orderBy('amount', 'desc')->first();

        if (!$highestBid) {
            return null;
        }

        return DB::transaction(function () use ($auction, $highestBid, $notes) {
            // End the auction if not already ended
            if ($auction->status !== 'completed') {
                $auction->update([
                    'status' => 'completed',
                    'ended_at' => now()
                ]);
            }

            // Create result
            $result = $this->auctionResultRepository->create([
                'auction_id' => $auction->id,
                'winner_id' => $highestBid->user_id,
                'winning_bid' => $highestBid->amount,
                'final_price' => $highestBid->amount,
                'result_time' => now(),
                'total_amount' => $highestBid->amount,
                'notes' => $notes
            ]);

            // Update auction with winner info
            $auction->update([
                'winner_id' => $highestBid->user_id,
                'final_price' => $highestBid->amount
            ]);

            // Create initial contract
            Contract::create([
                'auction_id' => $auction->id,
                'buyer_id' => $highestBid->user_id,
                'seller_id' => $auction->seller_id,
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

            return $result;
        });
    }

    public function confirmWinner(AuctionResult $result): bool
    {
        $result->update([
            'winner_confirmed_at' => now(),
            'status' => 'confirmed'
        ]);

        // Notify winner
        $result->winner->notifications()->create([
            'title' => 'Winner Confirmation',
            'message' => 'Your win in "' . $result->auction->title . '" has been confirmed',
            'type' => 'auction',
            'action_url' => '/contracts/' . $result->contract->id,
            'action_text' => 'View Contract'
        ]);

        return true;
    }

    public function rejectWinner(AuctionResult $result, string $reason): bool
    {
        return DB::transaction(function () use ($result, $reason) {
            $result->update([
                'status' => 'rejected',
                'rejection_reason' => $reason,
                'rejected_at' => now()
            ]);

            // Notify winner
            $result->winner->notifications()->create([
                'title' => 'Win Rejected',
                'message' => 'Your win in "' . $result->auction->title . '" has been rejected. Reason: ' . $reason,
                'type' => 'auction'
            ]);

            // Consider next highest bidder
            $nextBid = $result->auction->bids()
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
                    'message' => 'You have been selected as the new winner for "' . $result->auction->title . '"',
                    'type' => 'auction',
                    'action_url' => '/auctions/results/' . $result->id,
                    'action_text' => 'View Results'
                ]);
            }

            return true;
        });
    }
}
