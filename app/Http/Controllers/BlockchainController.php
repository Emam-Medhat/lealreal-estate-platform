<?php

namespace App\Http\Controllers;

use App\Models\BlockchainRecord;
use App\Models\SmartContract;
use App\Models\CryptoTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BlockchainController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $records = BlockchainRecord::latest()->paginate(50);
        $recentBlocks = BlockchainRecord::latest()->take(10)->get();
        $recentTransactions = collect([]); // Will be implemented when CryptoTransaction model is ready
        $stats = $this->buildBlockchainStats();
        
        return view('blockchain.dashboard', compact('records', 'recentBlocks', 'recentTransactions', 'stats'));
    }

    public function createRecord(Request $request)
    {
        $request->validate([
            'hash' => 'required|string|max:64',
            'previous_hash' => 'nullable|string|max:64',
            'height' => 'required|integer|min:1',
            'transaction_count' => 'required|integer|min:0',
            'difficulty' => 'required|integer|min:0',
            'miner' => 'nullable|string|max:255',
            'timestamp' => 'required|date',
            'nonce' => 'required|integer|min:0',
            'size' => 'required|numeric|min:0',
            'merkle_root' => 'nullable|string|max:64',
            'data' => 'nullable|array'
        ]);

        $record = BlockchainRecord::create([
            'hash' => $request->hash,
            'previous_hash' => $request->previous_hash,
            'height' => $request->height,
            'transaction_count' => $request->transaction_count,
            'difficulty' => $request->difficulty,
            'miner' => $request->miner,
            'timestamp' => $request->timestamp,
            'nonce' => $request->nonce,
            'size' => $request->size,
            'merkle_root' => $request->merkle_root,
            'data' => $request->data ?? [],
            'status' => 'pending',
            'created_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'record' => $record
        ]);
    }

    public function getRecords(Request $request)
    {
        $limit = $request->limit ?? 50;
        $offset = $request->offset ?? 0;
        
        $records = BlockchainRecord::latest()
            ->offset($offset)
            ->limit($limit)
            ->get();

        return response()->json([
            'records' => $records,
            'total' => BlockchainRecord::count(),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getBlock(Request $request)
    {
        $blockHash = $request->hash;
        
        $block = BlockchainRecord::where('hash', $blockHash)->first();
        
        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }

        return response()->json($block);
    }

    public function getLatestBlock()
    {
        $block = BlockchainRecord::latest()->first();
        
        return response()->json($block);
    }

    public function getBlockchainStats()
    {
        return $this->buildBlockchainStats();
    }

    public function validateChain(Request $request)
    {
        $latestBlock = BlockchainRecord::latest()->first();
        
        if (!$latestBlock) {
            return response()->json(['error' => 'No blocks found'], 404);
        }

        $isValid = $this->validateChainIntegrity($latestBlock);
        
        return response()->json([
            'is_valid' => $isValid,
            'latest_block' => $latestBlock,
            'validation_details' => $this->getValidationDetails($latestBlock)
        ]);
    }

    public function syncChain(Request $request)
    {
        $syncResult = $this->performChainSync();
        
        return response()->json([
            'status' => $syncResult['status'],
            'blocks_synced' => $syncResult['blocks_synced'],
            'errors' => $syncResult['errors'] ?? []
        ]);
    }

    public function getNetworkInfo()
    {
        $info = [
            'network_name' => config('blockchain.network_name', 'Ethereum Mainnet'),
            'chain_id' => config('blockchain.chain_id', '0x1'),
            'block_time' => config('blockchain.block_time', 12),
            'gas_limit' => config('blockchain.gas_limit', 21000),
            'current_block' => BlockchainRecord::latest()->value('height') ?? 0,
            'total_blocks' => BlockchainRecord::count(),
            'difficulty' => BlockchainRecord::latest()->value('difficulty') ?? 0,
            'hash_rate' => $this->calculateHashRate()
        ];

        return response()->json($info);
    }

    private function buildBlockchainStats()
    {
        return [
            'total_blocks' => BlockchainRecord::count(),
            'latest_block' => BlockchainRecord::latest()->first(),
            'total_transactions' => BlockchainRecord::sum('transaction_count'),
            'total_contracts' => 0, // Will be implemented when SmartContract model is ready
            'total_wallets' => 0, // Will be implemented when CryptoWallet model is ready
            'total_difficulty' => BlockchainRecord::sum('difficulty'),
            'average_block_size' => BlockchainRecord::avg('size'),
            'average_difficulty' => BlockchainRecord::avg('difficulty'),
            'confirmed_blocks' => BlockchainRecord::where('status', 'confirmed')->count(),
            'pending_blocks' => BlockchainRecord::where('status', 'pending')->count(),
            'orphaned_blocks' => BlockchainRecord::where('status', 'orphaned')->count(),
            'total_miners' => BlockchainRecord::distinct('miner')->count('miner'),
            'latest_height' => BlockchainRecord::max('height'),
            'hash_rate' => $this->calculateHashRate(),
            'network_hashrate' => $this->calculateHashRate(),
            'difficulty' => BlockchainRecord::latest()->value('difficulty') ?? 0,
            'gas_price' => 0 // Will be implemented when gas price tracking is ready
        ];
    }

    private function validateChainIntegrity($block)
    {
        // Simplified validation - in real implementation, this would validate
        // the entire blockchain from genesis to latest block
        return true;
    }

    private function getValidationDetails($block)
    {
        return [
            'block_hash_valid' => strlen($block->hash) === 64,
            'nonce_valid' => is_numeric($block->nonce),
            'difficulty_valid' => $block->difficulty > 0,
            'timestamp_valid' => $block->timestamp instanceof \Carbon\Carbon,
            'parent_hash_valid' => $block->height === 1 || $block->previous_hash !== null
        ];
    }

    private function performChainSync()
    {
        // Simplified sync implementation
        return [
            'status' => 'success',
            'blocks_synced' => 0,
            'errors' => []
        ];
    }

    private function calculateHashRate()
    {
        $latestBlock = BlockchainRecord::latest()->first();
        
        if (!$latestBlock) {
            return 0;
        }
        
        $previousBlock = BlockchainRecord::where('height', $latestBlock->height - 1)->first();
        
        if (!$previousBlock) {
            return 0;
        }

        $timeDiff = $latestBlock->timestamp->timestamp - $previousBlock->timestamp->timestamp;
        
        return $timeDiff > 0 ? 1 / $timeDiff : 0;
    }

    public function exportRecords(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $records = BlockchainRecord::latest()->limit($limit)->get();

        if ($format === 'csv') {
            return $this->exportToCsv($records);
        }

        return response()->json($records);
    }

    private function exportToCsv($records)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="blockchain_records.csv"'
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Block Hash', 'Block Height', 'Transaction Count', 'Difficulty', 
                'Miner', 'Timestamp', 'Size', 'Status'
            ]);
            
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->hash,
                    $record->height,
                    $record->transaction_count,
                    $record->difficulty,
                    $record->miner,
                    $record->timestamp->toDateTimeString(),
                    $record->size,
                    $record->status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getTransaction($hash)
    {
        $transaction = CryptoTransaction::where('hash', $hash)->first();
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function getBlockTransactions($blockHash)
    {
        $block = BlockchainRecord::where('hash', $blockHash)->first();
        
        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }

        $transactions = CryptoTransaction::where('block_hash', $blockHash)->get();
        
        return response()->json($transactions);
    }

    public function searchTransactions(Request $request)
    {
        $query = CryptoTransaction::query();
        
        if ($request->from_address) {
            $query->where('from_address', $request->from_address);
        }
        
        if ($request->to_address) {
            $query->where('to_address', $request->to_address);
        }
        
        if ($request->amount_min) {
            $query->where('amount', '>=', $request->amount_min);
        }
        
        if ($request->amount_max) {
            $query->where('amount', '<=', $request->amount_max);
        }
        
        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(50);
        
        return response()->json($transactions);
    }

    public function getTransactionStats(Request $request)
    {
        $period = $request->period ?? '24h';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_transactions' => CryptoTransaction::where('created_at', '>=', $startDate)->count(),
            'total_volume' => CryptoTransaction::where('created_at', '>=', $startDate)->sum('amount'),
            'average_gas_price' => $this->calculateAverageGasPrice($startDate),
            'unique_addresses' => CryptoTransaction::where('created_at', '>=', $startDate)
                ->selectRaw('COUNT(DISTINCT from_address) as unique_addresses')
                ->value('unique_addresses'),
            'transaction_types' => $this->getTransactionTypes($startDate),
            'hourly_volume' => $this->getHourlyVolume($startDate),
            'top_addresses' => $this->getTopAddresses($startDate)
        ];

        return response()->json($stats);
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

    private function calculateAverageGasPrice($startDate)
    {
        $totalGas = CryptoTransaction::where('created_at', '>=', $startDate)->sum('gas_used');
        $totalFees = CryptoTransaction::where('created_at', '>=', $startDate)->sum('gas_price');
        
        return $totalGas > 0 ? $totalFees / $totalGas : 0;
    }

    private function getTransactionTypes($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('transaction_type, COUNT(*) as count')
            ->groupBy('transaction_type')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getHourlyVolume($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-%d %H:00:00") as hour, SUM(amount) as volume')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    private function getTopAddresses($startDate)
    {
        return CryptoTransaction::where('created_at', '>=', $startDate)
            ->selectRaw('from_address, SUM(amount) as total_volume')
            ->groupBy('from_address')
            ->orderByDesc('total_volume')
            ->limit(10)
            ->get();
    }
}
