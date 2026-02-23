<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\User;
use App\Repositories\Contracts\AuctionRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuctionService
{
    protected $auctionRepository;

    public function __construct(AuctionRepositoryInterface $auctionRepository)
    {
        $this->auctionRepository = $auctionRepository;
    }

    public function create(array $data, array $images = []): Auction
    {
        return DB::transaction(function () use ($data, $images) {
            $auction = $this->auctionRepository->create($data);

            if (!empty($images)) {
                foreach ($images as $image) {
                    $path = $image->store('auctions', 'public');
                    $auction->images()->create(['path' => $path]);
                }
            }

            return $auction;
        });
    }

    public function update(int $id, array $data): bool
    {
        return $this->auctionRepository->update($id, $data);
    }

    public function start(int $id): bool
    {
        $auction = $this->auctionRepository->findById($id);

        if (!$auction || $auction->status !== 'upcoming') {
            return false;
        }

        return $this->auctionRepository->update($id, [
            'status' => 'active',
            'started_at' => now()
        ]);
    }

    public function end(int $id): bool
    {
        $auction = $this->auctionRepository->findById($id);

        if (!$auction || $auction->status !== 'active') {
            return false;
        }

        $this->finalizeAuction($auction);
        return true;
    }

    public function cancel(int $id): bool
    {
        $auction = $this->auctionRepository->findById($id);

        if (!$auction || !in_array($auction->status, ['upcoming', 'active'])) {
            return false;
        }

        $this->auctionRepository->update($id, ['status' => 'cancelled']);

        // Notify participants
        $auction->participants->each(function ($participant) use ($auction) {
            $participant->user->notifications()->create([
                'title' => 'Auction Cancelled',
                'message' => 'The auction "' . $auction->title . '" has been cancelled',
                'type' => 'auction'
            ]);
        });

        return true;
    }

    public function finalizeAuction(Auction $auction)
    {
        $highestBid = $auction->bids()->orderBy('amount', 'desc')->first();

        if ($highestBid && $highestBid->amount >= ($auction->reserve_price ?? $auction->starting_price)) {
            // Auction won
            $this->auctionRepository->update($auction->id, [
                'status' => 'completed',
                'winner_id' => $highestBid->user_id,
                'final_price' => $highestBid->amount,
                'ended_at' => now()
            ]);

            // Create auction result
            $auction->result()->create([
                'winner_id' => $highestBid->user_id,
                'final_price' => $highestBid->amount,
                'completed_at' => now()
            ]);

            // Notify winner
            $highestBid->user->notifications()->create([
                'title' => 'Auction Won!',
                'message' => 'Congratulations! You won the auction "' . $auction->title . '"',
                'type' => 'auction',
                'action_url' => '/auctions/' . $auction->id,
                'action_text' => 'View Auction'
            ]);

        } else {
            // Auction ended without winner
            $this->auctionRepository->update($auction->id, [
                'status' => 'completed',
                'ended_at' => now()
            ]);
        }

        // Notify all participants
        $auction->participants->each(function ($participant) use ($auction) {
            $participant->user->notifications()->create([
                'title' => 'Auction Ended',
                'message' => 'The auction "' . $auction->title . '" has ended',
                'type' => 'auction',
                'action_url' => '/auctions/' . $auction->id,
                'action_text' => 'View Results'
            ]);
        });
    }
}
