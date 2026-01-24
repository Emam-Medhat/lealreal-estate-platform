<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Models\Property;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AuctionController extends Controller
{
    public function index(Request $request)
    {
        $query = Auction::with(['property', 'highestBid.user', 'participants.user'])
            ->where('status', 'active');

        // Filters
        if ($request->type) {
            $query->where('type', $request->type);
        }
        
        if ($request->min_price) {
            $query->where('starting_price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('starting_price', '<=', $request->max_price);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->sort === 'ending_soon') {
            $query->orderBy('end_time', 'asc');
        } elseif ($request->sort === 'newest') {
            $query->orderBy('created_at', 'desc');
        } elseif ($request->sort === 'price_high') {
            $query->orderBy('current_price', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $auctions = $query->paginate(12);

        $stats = [
            'total' => Auction::count(),
            'active' => Auction::where('status', 'active')->count(),
            'completed' => Auction::where('status', 'completed')->count(),
            'upcoming' => Auction::where('status', 'upcoming')->count()
        ];

        return view('auctions.index', compact('auctions', 'stats'));
    }

    public function show($id)
    {
        $auction = Auction::with([
            'property.images',
            'property.features',
            'bids.user',
            'participants.user',
            'winner'
        ])->findOrFail($id);

        $user = Auth::user();
        $isParticipant = $user ? $auction->participants()->where('user_id', $user->id)->exists() : false;
        $hasActiveBid = $user ? $auction->bids()->where('user_id', $user->id)->exists() : false;
        
        $bidHistory = $auction->bids()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $similarAuctions = Auction::where('property_id', '!=', $auction->property_id)
            ->where('status', 'active')
            ->with('property')
            ->limit(3)
            ->get();

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

        $auction = Auction::create([
            'property_id' => $request->property_id,
            'title' => $request->title,
            'description' => $request->description,
            'starting_price' => $request->starting_price,
            'current_price' => $request->starting_price,
            'reserve_price' => $request->reserve_price,
            'bid_increment' => $request->bid_increment,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'type' => $request->type,
            'auto_extend' => $request->auto_extend ?? false,
            'status' => 'upcoming',
            'created_by' => Auth::id()
        ]);

        // Handle images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('auctions', 'public');
                $auction->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('auctions.show', $auction->id)
            ->with('success', 'Auction created successfully');
    }

    public function edit($id)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->with('property')
            ->findOrFail($id);

        if ($auction->status === 'active') {
            return back()->with('error', 'Cannot edit active auction');
        }

        return view('auctions.edit', compact('auction'));
    }

    public function update(Request $request, $id)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->findOrFail($id);

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

        $auction->update($request->all());

        return redirect()->route('auctions.show', $auction->id)
            ->with('success', 'Auction updated successfully');
    }

    public function start($id)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->where('status', 'upcoming')
            ->findOrFail($id);

        $auction->update([
            'status' => 'active',
            'started_at' => now()
        ]);

        return back()->with('success', 'Auction started successfully');
    }

    public function end($id)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->where('status', 'active')
            ->findOrFail($id);

        $this->finalizeAuction($auction);

        return back()->with('success', 'Auction ended successfully');
    }

    public function cancel($id)
    {
        $auction = Auction::where('created_by', Auth::id())
            ->whereIn('status', ['upcoming', 'active'])
            ->findOrFail($id);

        $auction->update(['status' => 'cancelled']);

        // Notify participants
        $auction->participants->each(function ($participant) {
            $participant->user->notifications()->create([
                'title' => 'Auction Cancelled',
                'message' => 'The auction "' . $auction->title . '" has been cancelled',
                'type' => 'auction'
            ]);
        });

        return back()->with('success', 'Auction cancelled successfully');
    }

    public function myAuctions()
    {
        $user = Auth::user();
        
        $createdAuctions = Auction::where('created_by', $user->id)
            ->with(['property', 'highestBid.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $participatedAuctions = Auction::whereHas('participants', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['property', 'highestBid.user'])
          ->orderBy('created_at', 'desc')
          ->get();

        return view('auctions.my-auctions', compact('createdAuctions', 'participatedAuctions'));
    }

    private function finalizeAuction(Auction $auction)
    {
        $highestBid = $auction->bids()->orderBy('amount', 'desc')->first();

        if ($highestBid && $highestBid->amount >= ($auction->reserve_price ?? $auction->starting_price)) {
            // Auction won
            $auction->update([
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
            $auction->update([
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
