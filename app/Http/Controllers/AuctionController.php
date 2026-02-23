<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Property;
use App\Repositories\Contracts\AuctionRepositoryInterface;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuctionController extends Controller
{
    protected $auctionRepository;
    protected $auctionService;

    public function __construct(
        AuctionRepositoryInterface $auctionRepository,
        AuctionService $auctionService
    ) {
        $this->auctionRepository = $auctionRepository;
        $this->auctionService = $auctionService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['type', 'min_price', 'max_price', 'status', 'sort']);
        $auctions = $this->auctionRepository->getActiveAuctions($filters);

        $stats = [
            'total' => $this->auctionRepository->count(),
            'active' => $this->auctionRepository->count(['status' => 'active']),
            'completed' => $this->auctionRepository->count(['status' => 'completed']),
            'upcoming' => $this->auctionRepository->count(['status' => 'upcoming'])
        ];

        return view('auctions.index', compact('auctions', 'stats'));
    }

    public function show($id)
    {
        $auction = $this->auctionRepository->findById($id, ['*'], [
            'property.images',
            'property.features',
            'bids.user',
            'participants.user',
            'winner'
        ]);

        if (!$auction) {
            abort(404);
        }

        $user = Auth::user();
        $isParticipant = $user ? $auction->participants()->where('user_id', $user->id)->exists() : false;
        $hasActiveBid = $user ? $auction->bids()->where('user_id', $user->id)->exists() : false;
        
        $bidHistory = $this->auctionRepository->getBidHistory($id);

        $similarAuctions = $this->auctionRepository->getSimilar($id, $auction->property_id);

        return view('auctions.show', compact(
            'auction',
            'isParticipant',
            'hasActiveBid',
            'bidHistory',
            'similarAuctions'
        ));
    }

    public function create()
    {
        $properties = Property::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->get();

        return view('auctions.create', compact('properties'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:starting_price',
            'bid_increment' => 'required|numeric|min:1',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:public,private',
            'auto_extend' => 'boolean',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except('images');
        $data['current_price'] = $request->starting_price;
        $data['auto_extend'] = $request->auto_extend ?? false;
        $data['status'] = 'upcoming';
        $data['seller_id'] = Auth::id();

        $images = $request->hasFile('images') ? $request->file('images') : [];

        $auction = $this->auctionService->create($data, $images);

        return redirect()->route('auctions.show', $auction->id)
            ->with('success', 'Auction created successfully');
    }

    public function edit($id)
    {
        $auction = $this->auctionRepository->findById($id, ['*'], ['property']);

        if (!$auction || $auction->seller_id !== Auth::id()) {
            abort(404);
        }

        if ($auction->status === 'active') {
            return back()->with('error', 'Cannot edit active auction');
        }

        return view('auctions.edit', compact('auction'));
    }

    public function update(Request $request, $id)
    {
        $auction = $this->auctionRepository->findById($id);

        if (!$auction || $auction->seller_id !== Auth::id()) {
            abort(404);
        }

        if ($auction->status === 'active') {
            return back()->with('error', 'Cannot edit active auction');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'starting_price' => 'required|numeric|min:0',
            'reserve_price' => 'nullable|numeric|min:starting_price',
            'bid_increment' => 'required|numeric|min:1',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'type' => 'required|in:public,private',
            'auto_extend' => 'boolean'
        ]);

        $this->auctionService->update($id, $request->all());

        return redirect()->route('auctions.show', $auction->id)
            ->with('success', 'Auction updated successfully');
    }

    public function start($id)
    {
        // Permission check is implicit in finding the auction by seller_id in original code.
        // But here I should check it.
        $auction = $this->auctionRepository->findById($id);
        
        if (!$auction || $auction->seller_id !== Auth::id()) {
             abort(404);
        }

        if ($this->auctionService->start($id)) {
            return back()->with('success', 'Auction started successfully');
        }

        return back()->with('error', 'Could not start auction');
    }

    public function end($id)
    {
        $auction = $this->auctionRepository->findById($id);
        
        if (!$auction || $auction->seller_id !== Auth::id()) {
             abort(404);
        }

        if ($this->auctionService->end($id)) {
            return back()->with('success', 'Auction ended successfully');
        }

        return back()->with('error', 'Could not end auction');
    }

    public function cancel($id)
    {
        $auction = $this->auctionRepository->findById($id);
        
        if (!$auction || $auction->seller_id !== Auth::id()) {
             abort(404);
        }

        if ($this->auctionService->cancel($id)) {
            return back()->with('success', 'Auction cancelled successfully');
        }

        return back()->with('error', 'Could not cancel auction');
    }

    public function myAuctions()
    {
        $userId = Auth::id();
        
        $createdAuctions = $this->auctionRepository->getBySeller($userId);
        $participatedAuctions = $this->auctionRepository->getParticipatedByUser($userId);

        return view('auctions.my-auctions', compact('createdAuctions', 'participatedAuctions'));
    }
}
