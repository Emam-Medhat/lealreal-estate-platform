<?php

namespace App\Http\Controllers;

use App\Models\Nft;
use App\Models\CryptoWallet;
use App\Models\User;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class NftController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $nfts = Nft::with(['owner'])->latest()->paginate(20);

        $totalNfts = Nft::count();
        $listedNfts = Nft::where('status', 'listed')->count();
        $totalVolume = Nft::sum('price');
        $ownersCount = Nft::distinct('owner_address')->count('owner_address');

        return view('blockchain.nfts', compact('nfts', 'totalNfts', 'listedNfts', 'totalVolume', 'ownersCount'));
    }

    public function create()
    {
        $users = User::all();
        $properties = Property::all();
        return view('blockchain.mint_nft', compact('users', 'properties'));
    }

    public function store(Request $request)
    {
        // Wrapper for mintNft or standard creation
        return $this->mintNft($request);
    }

    public function show($id)
    {
        $nft = Nft::with(['owner', 'smartContract'])->findOrFail($id);
        return view('blockchain.nfts.show', compact('nft'));
    }

    public function edit($id)
    {
        $nft = Nft::findOrFail($id);
        return view('blockchain.nfts.edit', compact('nft'));
    }

    public function update(Request $request, $id)
    {
        $nft = Nft::findOrFail($id);
        $nft->update($request->all());
        return redirect()->route('blockchain.nfts.show', $nft->id)->with('success', 'NFT updated successfully');
    }

    public function destroy($id)
    {
        $nft = Nft::findOrFail($id);
        $nft->delete();
        return redirect()->route('blockchain.nfts.index')->with('success', 'NFT deleted successfully');
    }

    public function mintNft(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'collection_id' => 'nullable|integer',
            'token_id' => 'required|integer|min:0',
            'contract_address' => 'required|string|max:255',
            'owner_address' => 'required|string|max:255',
            'creator_address' => 'required|string|max:255',
            'metadata' => 'nullable|array',
            'attributes' => 'nullable|array',
            'royalty_percentage' => 'nullable|numeric|min:0|max:50',
            'minting_cost' => 'nullable|numeric|min:0',
            'gas_used' => 'nullable|integer|min:0',
            'transaction_hash' => 'nullable|string|max:255',
            'block_number' => 'nullable|integer|min:0',
            'status' => 'required|string|in:minted,transferred,burned,listed',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imagePath = $image->store('nfts', 'public');
        }

        $nft = Nft::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $imagePath,
            'collection_id' => $request->collection_id,
            'token_id' => $request->token_id,
            'contract_address' => $request->contract_address,
            'owner_address' => $request->owner_address,
            'creator_address' => $request->creator_address,
            'metadata' => $request->metadata ?? [],
            'attributes' => $request->attributes ?? [],
            'royalty_percentage' => $request->royalty_percentage ?? 0,
            'minting_cost' => $request->minting_cost ?? 0,
            'gas_used' => $request->gas_used ?? 0,
            'transaction_hash' => $request->transaction_hash,
            'block_number' => $request->block_number,
            'status' => $request->status,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'nft' => $nft
            ]);
        }

        return redirect()->route('blockchain.nfts.show', $nft->id)->with('success', 'NFT minted successfully');
    }

    public function getNfts(Request $request)
    {
        $query = Nft::with(['owner']);
        
        if ($request->collection_id) {
            $query->where('collection_id', $request->collection_id);
        }
        
        if ($request->owner_address) {
            $query->where('owner_address', $request->owner_address);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function listNft(Request $request)
    {
        $request->validate([
            'nft_id' => 'required|integer|exists:nfts,id',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,USDC,USDT',
            'owner_address' => 'required|string|max:255',
            'expiration_date' => 'nullable|date|after:now'
        ]);

        $nft = Nft::findOrFail($request->nft_id);
        
        if ($nft->owner_address !== $request->owner_address) {
            return response()->json(['error' => 'You are not the owner of this NFT'], 403);
        }

        $nft->update([
            'current_price' => $request->price,
            'currency' => $request->currency,
            'status' => 'listed',
            'listed_at' => now(),
            'expiration_date' => $request->expiration_date,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'nft' => $nft
        ]);
    }

    public function buyNft(Request $request)
    {
        $request->validate([
            'nft_id' => 'required|integer|exists:nfts,id',
            'buyer_address' => 'required|string|max:255',
            'bid_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,USDC,USDT'
        ]);

        $nft = Nft::findOrFail($request->nft_id);
        
        if ($nft->status !== 'listed') {
            return response()->json(['error' => 'NFT is not for sale'], 400);
        }

        if ($request->bid_amount < $nft->current_price) {
            return response()->json(['error' => 'Bid amount is too low'], 400);
        }

        // Logic for purchase would go here (transaction verification etc)
        // For now we just update status
        $nft->update([
            'status' => 'sold',
            'owner_address' => $request->buyer_address,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'NFT purchased successfully',
            'nft' => $nft
        ]);
    }

    public function burnNft(Request $request)
    {
        $request->validate([
            'nft_id' => 'required|integer|exists:nfts,id',
            'owner_address' => 'required|string|max:255'
        ]);

        $nft = Nft::findOrFail($request->nft_id);
        
        if ($nft->owner_address !== $request->owner_address) {
            return response()->json(['error' => 'You are not the owner of this NFT'], 403);
        }

        $nft->update(['status' => 'burned']);

        return response()->json([
            'status' => 'success',
            'message' => 'NFT burned successfully'
        ]);
    }

    public function getNftStats(Request $request)
    {
        // Simplified stats
        $stats = [
            'total_nfts' => Nft::count(),
            'minted_nfts' => Nft::where('status', 'minted')->count(),
            'transferred_nfts' => Nft::where('status', 'transferred')->count(),
            'burned_nfts' => Nft::where('status', 'burned')->count(),
            'listed_nfts' => Nft::where('status', 'listed')->count(),
            'total_volume' => Nft::sum('price'),
            'unique_owners' => Nft::distinct('owner_address')->count(),
            'gas_used_total' => Nft::sum('gas_used'),
            'minting_costs_total' => Nft::sum('minting_cost')
        ];

        return response()->json($stats);
    }

    public function getNftHistory(Request $request)
    {
        $nftId = $request->nft_id;
        $nft = Nft::findOrFail($nftId);
        
        // Placeholder for history
        $history = [
            'minting' => [
                'date' => $nft->created_at,
                'transaction_hash' => $nft->transaction_hash,
                'block_number' => $nft->block_number,
                'gas_used' => $nft->gas_used,
                'minting_cost' => $nft->minting_cost
            ]
        ];

        return response()->json($history);
    }

    public function searchNfts(Request $request)
    {
        $query = Nft::with(['owner']);
        
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('token_id', 'like', "%{$search}%");
            });
        }
        
        if ($request->collection_id) {
            $query->where('collection_id', $request->collection_id);
        }
        
        if ($request->creator_address) {
            $query->where('creator_address', $request->creator_address);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function showBuy($id)
    {
        $nft = Nft::findOrFail($id);
        return view('blockchain.nfts.buy', compact('nft'));
    }

    public function showAuction($id)
    {
        $nft = Nft::findOrFail($id);
        return view('blockchain.nfts.auction', compact('nft'));
    }
}
