<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BlockchainService;
use App\Repositories\Contracts\BlockchainRecordRepositoryInterface;
use App\Repositories\Contracts\CryptoTransactionRepositoryInterface;

class BlockchainController extends Controller
{
    protected $blockchainService;
    protected $blockchainRecordRepository;
    protected $cryptoTransactionRepository;

    public function __construct(
        BlockchainService $blockchainService,
        BlockchainRecordRepositoryInterface $blockchainRecordRepository,
        CryptoTransactionRepositoryInterface $cryptoTransactionRepository
    ) {
        $this->middleware('auth');
        $this->blockchainService = $blockchainService;
        $this->blockchainRecordRepository = $blockchainRecordRepository;
        $this->cryptoTransactionRepository = $cryptoTransactionRepository;
    }

    public function index()
    {
        $records = $this->blockchainRecordRepository->getPaginatedRecords(50);
        $recentBlocks = $this->blockchainService->getRecentBlocks(10);
        $recentTransactions = $this->blockchainService->getRecentCryptoTransactions(10);
        $stats = $this->blockchainService->getBlockchainStats();
        
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

        $record = $this->blockchainRecordRepository->create([
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
        
        $records = $this->blockchainRecordRepository->getPaginatedRecords($limit, $offset);

        return response()->json([
            'records' => $records,
            'total' => $this->blockchainRecordRepository->count(),
            'limit' => $limit,
            'offset' => $offset
        ]);
    }

    public function getBlock(Request $request)
    {
        $blockHash = $request->hash;
        
        $block = $this->blockchainRecordRepository->findByHash($blockHash);
        
        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }

        return response()->json($block);
    }

    public function getLatestBlock()
    {
        $block = $this->blockchainRecordRepository->getLatestBlock();
        
        return response()->json($block);
    }

    public function getBlockchainStats()
    {
        return response()->json($this->blockchainService->getBlockchainStats());
    }

    public function validateChain(Request $request)
    {
        $latestBlock = $this->blockchainRecordRepository->getLatestBlock();
        
        if (!$latestBlock) {
            return response()->json(['error' => 'No blocks found'], 404);
        }

        $isValid = $this->blockchainService->validateChainIntegrity($latestBlock);
        
        return response()->json([
            'is_valid' => $isValid,
            'latest_block' => $latestBlock,
            'validation_details' => $this->blockchainService->getValidationDetails($latestBlock)
        ]);
    }

    public function syncChain(Request $request)
    {
        $syncResult = $this->blockchainService->performChainSync();
        
        return response()->json([
            'status' => $syncResult['status'],
            'blocks_synced' => $syncResult['blocks_synced'],
            'errors' => $syncResult['errors'] ?? []
        ]);
    }

    public function getNetworkInfo()
    {
        return response()->json($this->blockchainService->getNetworkInfo());
    }

    public function exportRecords(Request $request)
    {
        $format = $request->format ?? 'json';
        $limit = $request->limit ?? 1000;
        
        $records = $this->blockchainRecordRepository->getPaginatedRecords($limit, 0);

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
        $transaction = $this->cryptoTransactionRepository->findByHash($hash);
        
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        return response()->json($transaction);
    }

    public function getBlockTransactions($blockHash)
    {
        $block = $this->blockchainRecordRepository->findByHash($blockHash);
        
        if (!$block) {
            return response()->json(['error' => 'Block not found'], 404);
        }

        $transactions = $this->cryptoTransactionRepository->getByBlockHash($blockHash);
        
        return response()->json($transactions);
    }

    public function searchTransactions(Request $request)
    {
        // Redirect to CryptoTransactionController logic or return empty for now
        // Assuming search logic is complex and best handled by dedicated controller
        return response()->json(['message' => 'Please use /api/crypto/transactions/search endpoint'], 302);
    }
}
