<?php

namespace App\Http\Controllers;

use App\Models\Nft;
use App\Models\CryptoWallet;
use App\Models\User;
use App\Models\Property;
use App\Repositories\Contracts\NftRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class NftController extends Controller
{
    protected $nftRepository;

    public function __construct(NftRepositoryInterface $nftRepository)
    {
        $this->middleware('auth');
        $this->nftRepository = $nftRepository;
    }

    public function index()
    {
        $nfts = $this->nftRepository->getPaginated(20);

        $stats = $this->nftRepository->getStats();
        $totalNfts = $stats['total_nfts'];
        $listedNfts = $stats['listed_nfts'];
        $totalVolume = $stats['total_volume'];
        $ownersCount = $stats['unique_owners'];

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
        $nft = $this->nftRepository->findById($id, ['*'], ['owner', 'smartContract']);
        if (!$nft) {
            abort(404);
        }
        return view('blockchain.nfts.show', compact('nft'));
    }

    public function edit($id)
    {
        $nft = $this->nftRepository->findById($id);
        if (!$nft) {
            abort(404);
        }
        return view('blockchain.nfts.edit', compact('nft'));
    }

    public function update(Request $request, $id)
    {
        $this->nftRepository->update($id, $request->all());
        return redirect()->route('blockchain.nfts.show', $id)->with('success', 'NFT updated successfully');
    }

    public function destroy($id)
    {
        $this->nftRepository->deleteById($id);
        return redirect()->route('blockchain.nfts.index')->with('success', 'NFT deleted successfully');
    }

    public function showBuy($id)
    {
        $nft = $this->nftRepository->findById($id);
        if (!$nft) {
            abort(404);
        }
        return view('blockchain.nfts.buy', compact('nft'));
    }

    public function processBuy(Request $request, $id)
    {
        // Implementation for processing the buy
        // This is a placeholder for the actual logic
        return redirect()->route('blockchain.nfts.show', $id)->with('success', 'NFT purchase processed successfully');
    }

    public function showAuction($id)
    {
        $nft = $this->nftRepository->findById($id);
        if (!$nft) {
            abort(404);
        }
        return view('blockchain.nfts.auction', compact('nft'));
    }

    public function getNfts(Request $request)
    {
        $filters = $request->only(['collection_id', 'owner_address', 'status']);
        
        return response()->json($this->nftRepository->getFiltered($filters, 20));
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

        $nft = $this->nftRepository->findById($request->nft_id);
        
        if ($nft->owner_address !== $request->owner_address) {
            return response()->json(['error' => 'You are not the owner of this NFT'], 403);
        }

        $this->nftRepository->update($nft->id, [
            'current_price' => $request->price,
            'currency' => $request->currency,
            'status' => 'listed',
            'listed_at' => now(),
            'expiration_date' => $request->expiration_date,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'nft' => $this->nftRepository->findById($nft->id)
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

        $nft = $this->nftRepository->findById($request->nft_id);
        
        if ($nft->status !== 'listed') {
            return response()->json(['error' => 'NFT is not for sale'], 400);
        }

        if ($request->bid_amount < $nft->current_price) {
            return response()->json(['error' => 'Bid amount is too low'], 400);
        }

        // Logic for purchase would go here (transaction verification etc)
        // For now we just update status
        $this->nftRepository->update($nft->id, [
            'status' => 'sold',
            'owner_address' => $request->buyer_address,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'NFT purchased successfully',
            'nft' => $this->nftRepository->findById($nft->id)
        ]);
    }

    public function burnNft(Request $request)
    {
        $request->validate([
            'nft_id' => 'required|integer|exists:nfts,id',
            'owner_address' => 'required|string|max:255'
        ]);

        $nft = $this->nftRepository->findById($request->nft_id);
        
        if ($nft->owner_address !== $request->owner_address) {
            return response()->json(['error' => 'You are not the owner of this NFT'], 403);
        }

        $this->nftRepository->update($nft->id, ['status' => 'burned']);

        return response()->json([
            'status' => 'success',
            'message' => 'NFT burned successfully'
        ]);
    }

    public function getNftStats(Request $request)
    {
        return response()->json($this->nftRepository->getStats());
    }

    public function getNftHistory(Request $request)
    {
        $nftId = $request->nft_id;
        $nft = $this->nftRepository->findById($nftId);
        
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
        $filters = $request->only(['search', 'collection_id', 'creator_address']);

        return response()->json($this->nftRepository->search($filters, 20));
    }

    public function mintNft(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'type' => 'required|string|in:art,music,game,real_estate,collectible,sports,photography,video',
            'rarity' => 'nullable|string|in:common,uncommon,rare,epic,legendary,mythic',
            'collection' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:10240',
        ]);

        try {
            $data = $request->except(['image']);
            $data['owner_address'] = auth()->user()->wallet_address ?? '0x0000000000000000000000000000000000000000';
            $data['token_id'] = uniqid();
            $data['created_at'] = now();
            $data['updated_at'] = now();

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('nfts', $imageName, 'public');
                $data['image_url'] = Storage::url($imagePath);
            } else {
                $data['image_url'] = '/placeholder-nft.jpg';
            }

            // Create NFT using repository
            $nft = $this->nftRepository->create($data);

            return redirect()->route('blockchain.metaverse.nft.index')
                ->with('success', 'تم إنشاء NFT بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء NFT: ' . $e->getMessage());
        }
    }
}
