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
        $stats = $this->buildBlockchainStats();
        
        return view('blockchain.dashboard', compact('records', 'stats'));
    }

    public function createRecord(Request $request)
    {
        $request->validate([
            'block_hash' => 'required|string|max:255',
            'block_number' => 'required|integer|min:1',
            'transaction_count' => 'required|integer|min:0',
            'gas_used' => 'required|integer|min:0',
            'block_reward' => 'required|numeric|min:0',
            'miner_address' => 'required|string|max:255',
            'timestamp' => 'required|date',
            'parent_hash' => 'nullable|string|max:255',
            'nonce' => 'required|string|max:255',
            'difficulty' => 'required|numeric|min:0',
            'total_difficulty' => 'required|numeric|min:0',
            'size' => 'required|integer|min:0',
            'data' => 'nullable|array'
        ]);

        $record = BlockchainRecord::create([
            'block_hash' => $request->block_hash,
            'block_number' => $request->block_number,
            'transaction_count' => $request->transaction_count,
            'gas_used' => $request->gas_used,
            'block_reward' => $request->block_reward,
            'miner_address' => $request->miner_address,
            'timestamp' => $request->timestamp,
            'parent_hash' => $request->parent_hash,
            'nonce' => $request->nonce,
            'difficulty' => $request->difficulty,
            'total_difficulty' => $request->total_difficulty,
            'size' => $request->size,
            'data' => $request->data ?? [],
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
        $blockHash = $request->block_hash;
        
        $block = BlockchainRecord::where('block_hash', $blockHash)->first();
        
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
            'current_block' => BlockchainRecord::latest()->value('block_number') ?? 0,
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
            'total_gas_used' => BlockchainRecord::sum('gas_used'),
            'total_block_reward' => BlockchainRecord::sum('block_reward'),
            'average_block_size' => BlockchainRecord::avg('size'),
            'average_gas_used' => BlockchainRecord::avg('gas_used'),
            'total_difficulty' => BlockchainRecord::latest()->value('total_difficulty') ?? 0,
            'hash_rate' => $this->calculateHashRate()
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
            'block_hash_valid' => strlen($block->block_hash) === 66,
            'nonce_valid' => is_numeric($block->nonce),
            'difficulty_valid' => $block->difficulty > 0,
            'timestamp_valid' => $block->timestamp instanceof \Carbon\Carbon,
            'parent_hash_valid' => $block->block_number === 1 || $block->parent_hash !== null
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
        $previousBlock = BlockchainRecord::where('block_number', $latestBlock->block_number - 1)->first();
        
        if (!$latestBlock || !$previousBlock) {
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
                'Block Hash', 'Block Number', 'Transaction Count', 'Gas Used', 'Block Reward', 
                'Miner Address', 'Timestamp', 'Difficulty', 'Size'
            ]);
            
            foreach ($records as $record) {
                fputcsv($file, [
                    $record->block_hash,
                    $record->block_number,
                    $record->transaction_count,
                    $record->gas_used,
                    $record->block_reward,
                    $record->miner_address,
                    $record->timestamp->toDateTimeString(),
                    $record->difficulty,
                    $record->size
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
        $block = BlockchainRecord::where('block_hash', $blockHash)->first();
        
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
