<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    /**
     * Get wallet balance
     */
    public function getBalance(int $userId): array
    {
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return [
                'balance' => 0,
                'available_balance' => 0,
                'frozen_balance' => 0,
                'currency' => 'SAR'
            ];
        }
        
        return [
            'balance' => $wallet->balance,
            'available_balance' => $wallet->available_balance,
            'frozen_balance' => $wallet->frozen_balance,
            'currency' => $wallet->currency ?? 'SAR'
        ];
    }
    
    /**
     * Add funds to wallet
     */
    public function addFunds(int $userId, float $amount, string $type = 'deposit', array $meta = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            $wallet = UserWallet::create([
                'user_id' => $userId,
                'balance' => 0,
                'available_balance' => 0,
                'frozen_balance' => 0,
                'currency' => 'SAR'
            ]);
        }
        
        DB::beginTransaction();
        try {
            // Update wallet balance
            $wallet->increment('balance', $amount);
            $wallet->increment('available_balance', $amount);
            
            // Create transaction record
            $transaction = WalletTransaction::create([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $wallet->balance - $amount,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'description' => $this->getTransactionDescription($type, $meta),
                'meta' => $meta,
                'reference_id' => $meta['reference_id'] ?? null,
                'payment_method' => $meta['payment_method'] ?? null
            ]);
            
            DB::commit();
            
            // Fire event if needed
            if ($type === 'deposit') {
                event(new \App\Events\FundsAdded($wallet->user, $transaction));
            }
            
            return $transaction;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to add funds to wallet', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Deduct funds from wallet
     */
    public function deductFunds(int $userId, float $amount, string $type = 'payment', array $meta = []): WalletTransaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet || $wallet->available_balance < $amount) {
            throw new \Exception('Insufficient balance');
        }
        
        DB::beginTransaction();
        try {
            // Update wallet balance
            $wallet->decrement('balance', $amount);
            $wallet->decrement('available_balance', $amount);
            
            // Create transaction record
            $transaction = WalletTransaction::create([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => $type,
                'amount' => -$amount,
                'balance_before' => $wallet->balance + $amount,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'description' => $this->getTransactionDescription($type, $meta),
                'meta' => $meta,
                'reference_id' => $meta['reference_id'] ?? null
            ]);
            
            DB::commit();
            
            // Fire event if needed
            if ($type === 'payment') {
                event(new \App\Events\FundsDeducted($wallet->user, $transaction));
            }
            
            return $transaction;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to deduct funds from wallet', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Freeze funds
     */
    public function freezeFunds(int $userId, float $amount, string $reason, array $meta = []): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet || $wallet->available_balance < $amount) {
            throw new \Exception('Insufficient available balance');
        }
        
        DB::beginTransaction();
        try {
            // Update wallet balances
            $wallet->decrement('available_balance', $amount);
            $wallet->increment('frozen_balance', $amount);
            
            // Create freeze transaction
            WalletTransaction::create([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => 'freeze',
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'description' => "تجميد الأموال: {$reason}",
                'meta' => array_merge($meta, ['reason' => $reason])
            ]);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to freeze funds', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Unfreeze funds
     */
    public function unfreezeFunds(int $userId, float $amount, string $reason, array $meta = []): bool
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive');
        }
        
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet || $wallet->frozen_balance < $amount) {
            throw new \Exception('Insufficient frozen balance');
        }
        
        DB::beginTransaction();
        try {
            // Update wallet balances
            $wallet->increment('available_balance', $amount);
            $wallet->decrement('frozen_balance', $amount);
            
            // Create unfreeze transaction
            WalletTransaction::create([
                'user_id' => $userId,
                'wallet_id' => $wallet->id,
                'type' => 'unfreeze',
                'amount' => $amount,
                'balance_before' => $wallet->balance,
                'balance_after' => $wallet->balance,
                'status' => 'completed',
                'description' => "إلغاء تجميد الأموال: {$reason}",
                'meta' => array_merge($meta, ['reason' => $reason])
            ]);
            
            DB::commit();
            
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to unfreeze funds', [
                'user_id' => $userId,
                'amount' => $amount,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Get transaction history
     */
    public function getTransactionHistory(int $userId, array $filters = []): array
    {
        $query = WalletTransaction::where('user_id', $userId)
            ->with(['user', 'wallet'])
            ->orderBy('created_at', 'desc');
        
        // Apply filters
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }
        
        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }
        
        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }
        
        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }
        
        return $query->paginate($filters['per_page'] ?? 20);
    }
    
    /**
     * Get transaction description
     */
    private function getTransactionDescription(string $type, array $meta): string
    {
        $descriptions = [
            'deposit' => 'إيداع في المحفظة',
            'payment' => $meta['description'] ?? 'دفع من المحفظة',
            'withdrawal' => 'سحب من المحفظة',
            'refund' => 'استرداد',
            'commission' => 'عمولة',
            'bonus' => 'مكافأة',
            'freeze' => 'تجميد أموال',
            'unfreeze' => 'إلغاء تجميد',
            'transfer_in' => 'تحويل وارد',
            'transfer_out' => 'تحويل صادر'
        ];
        
        return $descriptions[$type] ?? 'معاملة محفظة';
    }
    
    /**
     * Get wallet statistics
     */
    public function getWalletStatistics(int $userId, string $period = 'month'): array
    {
        $wallet = UserWallet::where('user_id', $userId)->first();
        
        if (!$wallet) {
            return [
                'total_transactions' => 0,
                'total_deposits' => 0,
                'total_withdrawals' => 0,
                'net_change' => 0,
                'average_transaction' => 0
            ];
        }
        
        $query = WalletTransaction::where('user_id', $userId);
        
        // Apply period filter
        switch ($period) {
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case 'year':
                $query->where('created_at', '>=', now()->subYear());
                break;
        }
        
        $transactions = $query->get();
        
        $totalDeposits = $transactions->where('amount', '>', 0)->sum('amount');
        $totalWithdrawals = abs($transactions->where('amount', '<', 0)->sum('amount'));
        $netChange = $totalDeposits - $totalWithdrawals;
        
        return [
            'total_transactions' => $transactions->count(),
            'total_deposits' => $totalDeposits,
            'total_withdrawals' => $totalWithdrawals,
            'net_change' => $netChange,
            'average_transaction' => $transactions->count() > 0 ? $netChange / $transactions->count() : 0,
            'period' => $period
        ];
    }
}
