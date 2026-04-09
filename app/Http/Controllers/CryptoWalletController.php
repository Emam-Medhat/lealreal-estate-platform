<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\CryptoWalletRepositoryInterface;
use App\Repositories\Contracts\CryptoTransactionRepositoryInterface;
use App\Services\BlockchainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CryptoWalletController extends Controller
{
    protected $cryptoWalletRepository;
    protected $cryptoTransactionRepository;
    protected $blockchainService;

    public function __construct(
        CryptoWalletRepositoryInterface $cryptoWalletRepository,
        CryptoTransactionRepositoryInterface $cryptoTransactionRepository,
        BlockchainService $blockchainService
    ) {
        $this->middleware('auth');
        $this->cryptoWalletRepository = $cryptoWalletRepository;
        $this->cryptoTransactionRepository = $cryptoTransactionRepository;
        $this->blockchainService = $blockchainService;
    }

    public function index()
    {
        $wallets = $this->cryptoWalletRepository->getWallets([], 20);
        
        return view('blockchain.wallets.index', compact('wallets'));
    }

    public function create()
    {
        return view('blockchain.wallets.create');
    }

    public function createWallet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255|unique:crypto_wallets',
            'private_key' => 'required|string',
            'public_key' => 'required|string',
            'wallet_type' => 'required|string|in:ethereum,btc,custom',
            'network' => 'required|string|in:mainnet,testnet,polygon,bsc',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,BTC,USDC,USDT',
            'is_active' => 'required|boolean',
            'is_default' => 'required|boolean',
            'metadata' => 'nullable|array',
            'created_at' => 'now()',
            'updated_at' => 'now()'
        ]);

        // Encrypt private key
        $encryptedPrivateKey = Crypt::encrypt($request->private_key);

        $wallet = $this->cryptoWalletRepository->create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'address' => $request->address,
            'private_key' => $encryptedPrivateKey,
            'public_key' => $request->public_key,
            'wallet_type' => $request->wallet_type,
            'network' => $request->network,
            'balance' => $request->balance,
            'currency' => $request->currency,
            'is_active' => $request->is_active,
            'is_default' => $request->is_default,
            'metadata' => $request->metadata ?? [],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $wallet
        ]);
    }

    public function getWallets(Request $request)
    {
        $filters = array_filter($request->only([
            'user_id', 'wallet_type', 'network', 'is_active'
        ]), function($value) {
            return $value !== null;
        });

        $wallets = $this->cryptoWalletRepository->getWallets($filters, 20);
        
        return response()->json($wallets);
    }

    public function getWallet(Request $request)
    {
        $wallet = $this->cryptoWalletRepository->getWallet($request->id, $request->address);
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        return response()->json($wallet);
    }

    public function updateWallet(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crypto_wallets,id',
            'name' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
            'is_default' => 'nullable|boolean',
            'metadata' => 'nullable|array'
        ]);

        $wallet = $this->cryptoWalletRepository->findById($request->id);
        
        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->cryptoWalletRepository->update($wallet->id, [
            'name' => $request->name ?? $wallet->name,
            'is_active' => $request->is_active ?? $wallet->is_active,
            'is_default' => $request->is_default ?? $wallet->is_default,
            'metadata' => $request->metadata ?? $wallet->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $this->cryptoWalletRepository->findById($wallet->id)
        ]);
    }

    public function deleteWallet(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crypto_wallets,id'
        ]);

        $wallet = $this->cryptoWalletRepository->findById($request->id);
        
        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $this->cryptoWalletRepository->deleteById($wallet->id);

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet deleted successfully'
        ]);
    }

    public function getBalance(Request $request)
    {
        $address = $request->address;
        
        $wallet = $this->cryptoWalletRepository->getByAddress($address);
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $balance = $this->getWalletBalance($wallet);
        
        return response()->json([
            'address' => $address,
            'balance' => $balance['balance'],
            'currency' => $balance['currency'],
            'usd_value' => $balance['usd_value'],
            'last_updated' => $balance['last_updated']
        ]);
    }

    public function updateBalance(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'balance' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,BTC,USDC,USDT'
        ]);

        $wallet = $this->cryptoWalletRepository->getByAddress($request->address);
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $this->cryptoWalletRepository->update($wallet->id, [
            'balance' => $request->balance,
            'currency' => $request->currency,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $this->cryptoWalletRepository->findById($wallet->id)
        ]);
    }

    public function sendTransaction(Request $request)
    {
        $request->validate([
            'from_address' => 'required|string|max:255',
            'to_address' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|in:ETH,BTC,USDC,USDT',
            'gas_price' => 'nullable|numeric|min:0',
            'gas_limit' => 'nullable|integer|min:0',
            'private_key' => 'required|string',
            'memo' => 'nullable|string|max:255'
        ]);

        $wallet = $this->cryptoWalletRepository->getByAddress($request->from_address);
        
        if (!$wallet) {
            return response()->json(['error' => 'Source wallet not found'], 404);
        }

        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify private key
        if (Crypt::decrypt($wallet->private_key) !== $request->private_key) {
            return response()->json(['error' => 'Invalid private key'], 401);
        }

        // Check balance
        if ($wallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        $result = $this->blockchainService->performCryptoTransaction($wallet, $request->all());

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash'] ?? null,
            'gas_used' => $result['gas_used'] ?? 0,
            'amount' => $result['amount'] ?? 0,
            'error' => $result['error'] ?? null
        ]);
    }

    public function getTransactions(Request $request)
    {
        $address = $request->address;
        
        $wallet = $this->cryptoWalletRepository->getByAddress($address);
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $transactions = $this->cryptoTransactionRepository->getFiltered([
            'address' => $address
        ], 50);

        return response()->json($transactions);
    }

    public function getWalletStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_wallets' => $this->cryptoWalletRepository->count(),
            'active_wallets' => $this->cryptoWalletRepository->countActive(),
            'total_balance' => $this->cryptoWalletRepository->sumBalance(),
            'total_transactions' => $this->cryptoTransactionRepository->count(),
            'total_volume' => $this->cryptoTransactionRepository->totalVolume(),
            'wallet_types' => $this->cryptoWalletRepository->getWalletTypes($startDate),
            'networks' => $this->cryptoWalletRepository->getNetworks($startDate),
            'currencies' => $this->cryptoWalletRepository->getCurrencies($startDate),
            'new_wallets' => $this->cryptoWalletRepository->countNewWallets($startDate),
            'average_balance' => $this->cryptoWalletRepository->averageBalance(),
            'top_wallets' => $this->cryptoWalletRepository->getTopWallets($startDate)
        ];

        return response()->json($stats);
    }

    public function importWallet(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'private_key' => 'required|string',
            'wallet_type' => 'required|string|in:ethereum,btc,custom',
            'network' => 'required|string|in:mainnet,testnet,polygon,bsc'
        ]);

        // Derive address and public key from private key
        $walletData = $this->deriveWalletFromPrivateKey($request->private_key);

        // Check if wallet already exists
        $existingWallet = $this->cryptoWalletRepository->getByAddress($walletData['address']);
        if ($existingWallet) {
            return response()->json(['error' => 'Wallet already exists'], 400);
        }

        $wallet = $this->cryptoWalletRepository->create([
            'user_id' => auth()->id(),
            'name' => $request->name,
            'address' => $walletData['address'],
            'private_key' => Crypt::encrypt($request->private_key),
            'public_key' => $walletData['public_key'],
            'wallet_type' => $request->wallet_type,
            'network' => $request->network,
            'balance' => 0,
            'currency' => 'ETH',
            'is_active' => true,
            'is_default' => false,
            'metadata' => ['imported' => true],
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $wallet
        ]);
    }

    public function exportWallet(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crypto_wallets,id',
            'format' => 'required|string|in:json,keystore'
        ]);

        $wallet = $this->cryptoWalletRepository->findById($request->id);
        
        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($request->format === 'keystore') {
            return $this->exportKeystore($wallet);
        }

        return response()->json([
            'address' => $wallet->address,
            'public_key' => $wallet->public_key,
            'wallet_type' => $wallet->wallet_type,
            'network' => $wallet->network,
            'balance' => $wallet->balance,
            'currency' => $wallet->currency,
            'metadata' => $wallet->metadata
        ]);
    }

    private function getWalletBalance($wallet)
    {
        // Simplified balance fetching
        return [
            'balance' => $wallet->balance,
            'currency' => $wallet->currency,
            'usd_value' => $this->convertToUSD($wallet->balance, $wallet->currency),
            'last_updated' => $wallet->updated_at
        ];
    }

    private function deriveWalletFromPrivateKey($privateKey)
    {
        // Simplified wallet derivation
        return [
            'address' => '0x' . substr(hash('sha256', $privateKey), 0, 40),
            'public_key' => '0x' . substr(hash('sha256', $privateKey . 'public'), 0, 64)
        ];
    }

    private function convertToUSD($amount, $currency)
    {
        // Simplified USD conversion
        $rates = [
            'ETH' => 2000,
            'BTC' => 50000,
            'USDC' => 1,
            'USDT' => 1
        ];

        return $amount * ($rates[$currency] ?? 1);
    }

    private function exportKeystore($wallet)
    {
        $keystore = [
            'address' => $wallet->address,
            'crypto' => [
                'ciphertext' => Crypt::encrypt($wallet->private_key),
                'cipherparams' => ['iv' => bin2hex(random_bytes(16))],
                'cipher' => 'aes-128-ctr',
                'kdf' => 'scrypt',
                'kdfparams' => [
                    'dklen' => 32,
                    'salt' => bin2hex(random_bytes(32)),
                    'n' => 8192,
                    'r' => 8,
                    'p' => 1
                ],
                'mac' => bin2hex(random_bytes(32))
            ],
            'id' => bin2hex(random_bytes(8)),
            'version' => 3
        ];

        return response()->json($keystore);
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
}
