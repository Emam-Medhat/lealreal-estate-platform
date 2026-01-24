<?php

namespace App\Http\Controllers;

use App\Models\CryptoWallet;
use App\Models\CryptoTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class CryptoWalletController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wallets = CryptoWallet::with(['user', 'transactions'])->latest()->paginate(20);
        
        return view('blockchain.wallets.index', compact('wallets'));
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

        $wallet = CryptoWallet::create([
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
        $query = CryptoWallet::with(['user', 'transactions']);
        
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->wallet_type) {
            $query->where('wallet_type', $request->wallet_type);
        }
        
        if ($request->network) {
            $query->where('network', $request->network);
        }
        
        if ($request->is_active !== null) {
            $query->where('is_active', $request->is_active);
        }

        $wallets = $query->latest()->paginate(20);
        
        return response()->json($wallets);
    }

    public function getWallet(Request $request)
    {
        $wallet = CryptoWallet::with(['user', 'transactions'])
            ->where('id', $request->id)
            ->orWhere('address', $request->address)
            ->first();
        
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

        $wallet = CryptoWallet::findOrFail($request->id);
        
        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $wallet->update([
            'name' => $request->name ?? $wallet->name,
            'is_active' => $request->is_active ?? $wallet->is_active,
            'is_default' => $request->is_default ?? $wallet->is_default,
            'metadata' => $request->metadata ?? $wallet->metadata,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $wallet
        ]);
    }

    public function deleteWallet(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:crypto_wallets,id'
        ]);

        $wallet = CryptoWallet::findOrFail($request->id);
        
        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $wallet->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Wallet deleted successfully'
        ]);
    }

    public function getBalance(Request $request)
    {
        $address = $request->address;
        
        $wallet = CryptoWallet::where('address', $address)->first();
        
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

        $wallet = CryptoWallet::where('address', $request->address)->first();
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $wallet->update([
            'balance' => $request->balance,
            'currency' => $request->currency,
            'updated_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'wallet' => $wallet
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

        $wallet = CryptoWallet::where('address', $request->from_address)->first();
        
        if (!$wallet) {
            return response()->json(['error' => 'Source wallet not found'], 404);
        }

        // Check if user owns this wallet
        if ($wallet->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Verify private key
        if (!Crypt::decrypt($wallet->private_key) === $request->private_key) {
            return response()->json(['error' => 'Invalid private key'], 401);
        }

        // Check balance
        if ($wallet->balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 400);
        }

        $result = $this->performTransaction($wallet, $request->all());

        return response()->json([
            'status' => $result['status'],
            'transaction_hash' => $result['transaction_hash'],
            'gas_used' => $result['gas_used'],
            'amount' => $result['amount']
        ]);
    }

    public function getTransactions(Request $request)
    {
        $address = $request->address;
        
        $wallet = CryptoWallet::where('address', $address)->first();
        
        if (!$wallet) {
            return response()->json(['error' => 'Wallet not found'], 404);
        }

        $transactions = CryptoTransaction::where('from_address', $address)
            ->orWhere('to_address', $address)
            ->with(['user'])
            ->latest()
            ->paginate(50);

        return response()->json($transactions);
    }

    public function getWalletStats(Request $request)
    {
        $period = $request->period ?? '30d';
        $startDate = $this->getStartDate($period);

        $stats = [
            'total_wallets' => CryptoWallet::count(),
            'active_wallets' => CryptoWallet::where('is_active', true)->count(),
            'total_balance' => CryptoWallet::sum('balance'),
            'total_transactions' => CryptoTransaction::count(),
            'total_volume' => CryptoTransaction::sum('amount'),
            'wallet_types' => $this->getWalletTypes($startDate),
            'networks' => $this->getNetworks($startDate),
            'currencies' => $this->getCurrencies($startDate),
            'new_wallets' => CryptoWallet::where('created_at', '>=', $startDate)->count(),
            'average_balance' => CryptoWallet::avg('balance'),
            'top_wallets' => $this->getTopWallets($startDate)
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
        $existingWallet = CryptoWallet::where('address', $walletData['address'])->first();
        if ($existingWallet) {
            return response()->json(['error' => 'Wallet already exists'], 400);
        }

        $wallet = CryptoWallet::create([
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

        $wallet = CryptoWallet::findOrFail($request->id);
        
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

    private function performTransaction($wallet, $params)
    {
        try {
            $gasUsed = $params['gas_limit'] ?? 21000;
            $transactionHash = '0x' . bin2hex(random_bytes(32));
            
            // Update wallet balance
            $newBalance = $wallet->balance - $params['amount'];
            $wallet->update(['balance' => $newBalance]);

            // Create transaction record
            CryptoTransaction::create([
                'from_address' => $params['from_address'],
                'to_address' => $params['to_address'],
                'amount' => $params['amount'],
                'currency' => $params['currency'],
                'gas_price' => $params['gas_price'] ?? 0,
                'gas_used' => $gasUsed,
                'hash' => $transactionHash,
                'status' => 'confirmed',
                'user_id' => $wallet->user_id,
                'created_at' => now()
            ]);

            return [
                'status' => 'success',
                'transaction_hash' => $transactionHash,
                'gas_used' => $gasUsed,
                'amount' => $params['amount']
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'error' => $e->getMessage(),
                'gas_used' => 0,
                'amount' => 0
            ];
        }
    }

    private function getWalletTypes($startDate)
    {
        return CryptoWallet::where('created_at', '>=', $startDate)
            ->selectRaw('wallet_type, COUNT(*) as count')
            ->groupBy('wallet_type')
            ->orderByDesc('count')
            ->get();
    }

    private function getNetworks($startDate)
    {
        return CryptoWallet::where('created_at', '>=', $startDate)
            ->selectRaw('network, COUNT(*) as count')
            ->groupBy('network')
            ->orderByDesc('count')
            ->get();
    }

    private function getCurrencies($startDate)
    {
        return CryptoWallet::where('created_at', '>=', $startDate)
            ->selectRaw('currency, COUNT(*) as count, SUM(balance) as total_balance')
            ->groupBy('currency')
            ->orderByDesc('total_balance')
            ->get();
    }

    private function getTopWallets($startDate)
    {
        return CryptoWallet::where('created_at', '>=', $startDate)
            ->with('user')
            ->orderByDesc('balance')
            ->limit(10)
            ->get();
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
