<?php

namespace App\Http\Controllers;

use App\Models\Nft;
use App\Models\CryptoWallet;
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
        $nfts = Nft::with(['owner', 'collection'])->latest()->paginate(20);
        
        return view('blockchain.nfts.index', compact('nfts'));
    }

    public function create()
    {
        return view('blockchain.nfts.create');
    }

    public function mintNft(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'collection_id' => 'nullable|integer|exists:nft_collections,id',
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
            'created_at' => 'now()',
            'updated_at' => 'now()'
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

        return response()->json([
            'status' => 'success',
            'nft' => $nft
        ]);
    }

    public function getNfts(Request $request)
    {
        $query = Nft::with(['owner', 'collection']);
        
        if ($request->collection_id) {
            $query->where('collection_id', $request->collection_id);
        }
        
        if ($request->owner_address) {
            $query->where('owner_address', $request->owner_address);
        }
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        if ($request->min_price) {
            $query->where('current_price', '>=', $request->min_price);
        }
        
        if ($request->max_price) {
            $query->where('current_price', '<=', $request->max_price);
        }

        $nfts = $query->latest()->paginate(20);
        
        return response()->json($nfts);
    }

    public function getNft(Request $request)
    {
        $nft = Nft::with(['owner', 'collection', 'transactions'])
            ->where('id', $request->id)
            ->orWhere('token_id', $request->token_id)
            ->first();
        
        if (!$nft) {
            return response()->json(['error' => 'NFT not found'], 404);
        }

        return response()->json($nft);
    }

    public function transferNft(Request $request)
    {
        $request->validate([
            'nft_id' => 'required|integer|exists:nfts,id',
            'to_address' => 'required|string|max:255',
            'from_address' => 'required|string|max:255',
            'gas_price' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:0'
        ]);

        $nft = Nft::findOrFail($request->nft_id);
        
        if ($nft->owner_address !== $request->from_address) {
            return response()->json(['error' => 'You are not the owner of this NFT'], 403);
        }

        $result = $this->performTransfer($nft, $request->to_address, $request->all());

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash'],
            'gas_used' => $result['gas_used']
        ]);
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

        $result = $this->performPurchase($nft, $request->all());

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash'],
            'gas_used' => $result['gas_used']
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

        $result = $this->performBurn($nft);

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash'],
            'gas_used' => $result['gas_used']
        ]);
    }

    public function getNftStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_nfts' => Nft::count(),
            'minted_nfts' => Nft::where('status', 'minted')->count(),
            'transferred_nfts' => Nft::where('status', 'transferred')->count(),
            'burned_nfts' => Nft::where('status', 'burned')->count(),
            'listed_nfts' => Nft::where('status', 'listed')->count(),
            'total_volume' => $this->getTotalVolume($startDate),
            'average_price' => $this->getAveragePrice($startDate),
            'top_collections' => $this->getTopCollections($startDate),
            'recent_mints' => Nft::where('created_at', '>=', $startDate)->count(),
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
        
        $history = [
            'minting' => [
                'date' => $nft->created_at,
                'transaction_hash' => $nft->transaction_hash,
                'block_number' => $nft->block_number,
                'gas_used' => $nft->gas_used,
                'minting_cost' => $nft->minting_cost
            ],
            'transfers' => $this->getTransferHistory($nft),
            'listings' => $this->getListingHistory($nft),
            'sales' => $this->getSalesHistory($nft)
        ];

        return response()->json($history);
    }

    public function searchNfts(Request $request)
    {
        $query = Nft::with(['owner', 'collection']);
        
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
        
        if ($request->attributes) {
            foreach ($request->attributes as $key => $value) {
                $query->whereJsonContains('attributes', [$key => $value]);
            }
        }

        $nfts = $query->latest()->paginate(20);
        
        return response()->json($nfts);
    }

    public function getOwnerNfts(Request $request)
    {
        $ownerAddress = $request->owner_address;
        
        $nfts = Nft::with(['collection'])
            ->where('owner_address', $ownerAddress)
            ->latest()
            ->paginate(20);

        return response()->json($nfts);
    }

    public function getCreatorNfts(Request $request)
    {
        $creatorAddress = $request->creator_address;
        
        $nfts = Nft::with(['collection'])
            ->where('creator_address', $creatorAddress)
            ->latest()
            ->paginate(20);

        return response()->json($nfts);
    }

    private function performTransfer($nft, $toAddress, $params)
    {
        try {
            $gasUsed = $params['gas_limit'] ?? 21000;
            $transactionHash = '0x' . bin2hex(random_bytes(32));
            
            $nft->update([
                'owner_address' => $toAddress,
                'status' => 'transferred',
                'updated_at' => now()
            ]);

            return [
                'status' => 'success',
                'transaction_hash' => $transactionHash,
                'gas_used' => $gasUsed
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0
            ];
        }
    }

    private function performPurchase($nft, $params)
    {
        try {
            $gasUsed = 50000;
            $transactionHash = '0x' . bin2hex(random_bytes(32));
            
            $nft->update([
                'owner_address' => $params['buyer_address'],
                'status' => 'transferred',
                'current_price' => null,
                'currency' => null,
                'listed_at' => null,
                'expiration_date' => null,
                'updated_at' => now()
            ]);

            return [
                'status' => 'success',
                'transaction_hash' => $transactionHash,
                'gas_used' => $gasUsed
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0
            ];
        }
    }

    private function performBurn($nft)
    {
        try {
            $gasUsed = 25000;
            $transactionHash = '0x' . bin2hex(random_bytes(32));
            
            $nft->update([
                'status' => 'burned',
                'updated_at' => now()
            ]);

            return [
                'status' => 'success',
                'transaction_hash' => $transactionHash,
                'gas_used' => $gasUsed
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0
            ];
        }
    }

    private function getStartDate($period)
    {
        return match($period) {
            '1h' => now()->subHour(),
            '24h' => now()->subDay(),
            '7d' => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y' => now()->subYear(),
            default => now()->subDay()
        };
    }

    private function getTotalVolume($startDate)
    {
        // Simplified volume calculation
        return Nft::where('status', 'transferred')
            ->where('updated_at', '>=', $startDate)
            ->sum('current_price');
    }

    private function getAveragePrice($startDate)
    {
        $totalPrice = Nft::where('status', 'transferred')
            ->where('updated_at', '>=', $startDate)
            ->sum('current_price');
        
        $count = Nft::where('status', 'transferred')
            ->where('updated_at', '>=', $startDate)
            ->count();
        
        return $count > 0 ? $totalPrice / $count : 0;
    }

    private function getTopCollections($startDate)
    {
        return Nft::with('collection')
            ->where('created_at', '>=', $startDate)
            ->selectRaw('collection_id, COUNT(*) as count')
            ->groupBy('collection_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
    }

    private function getTransferHistory($nft)
    {
        // Simplified transfer history
        return [
            [
                'from' => $nft->creator_address,
                'to' => $nft->owner_address,
                'date' => $nft->created_at,
                'transaction_hash' => $nft->transaction_hash
            ]
        ];
    }

    private function getListingHistory($nft)
    {
        // Simplified listing history
        if ($nft->status === 'listed' && $nft->listed_at) {
            return [
                [
                    'price' => $nft->current_price,
                    'currency' => $nft->currency,
                    'date' => $nft->listed_at,
                    'expiration_date' => $nft->expiration_date
                ]
            ];
        }
        
        return [];
    }

    private function getSalesHistory($nft)
    {
        // Simplified sales history
        return [];
    }

    public function exportNfts(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $nfts = Nft::with(['owner', 'collection'])->latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportNftsToCsv($nfts);
        }

        return response()->json($nfts);
    }

    private function exportNftsToCsv($nfts)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="nfts.csv"'
        ];

        $callback = function() use ($nfts) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Token ID', 'Contract Address', 'Owner Address', 
                'Creator Address', 'Status', 'Current Price', 'Currency', 'Royalty %', 'Created At'
            ]);
            
            foreach ($nfts as $nft) {
                fputcsv($file, [
                    $nft->id,
                    $nft->name,
                    $nft->description,
                    $nft->token_id,
                    $nft->contract_address,
                    $nft->owner_address,
                    $nft->creator_address,
                    $nft->status,
                    $nft->current_price,
                    $nft->currency,
                    $nft->royalty_percentage,
                    $nft->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
