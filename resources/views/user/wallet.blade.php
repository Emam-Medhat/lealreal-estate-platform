@extends('layouts.app')

@section('title', 'My Wallet')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">My Wallet</h1>
                    <p class="text-gray-600">Manage your wallet balance and transactions</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="showAddFundsModal()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Funds
                    </button>
                    <button onclick="showWithdrawModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-minus mr-2"></i>
                        Withdraw
                    </button>
                </div>
            </div>
        </div>

        <!-- Wallet Balance Card -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-blue-100 text-sm mb-2">Current Balance</p>
                    <p class="text-4xl font-bold mb-1">${{ number_format($wallet->balance, 2) }}</p>
                    <p class="text-blue-100 text-sm">{{ $wallet->currency }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-white bg-opacity-20 rounded-lg p-3 mb-2">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <p class="text-sm text-blue-100">Status: {{ ucfirst($wallet->status) }}</p>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-arrow-down text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Deposits</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($wallet->transactions()->where('transaction_type', 'deposit')->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <i class="fas fa-arrow-up text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Withdrawals</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($wallet->transactions()->where('transaction_type', 'withdrawal')->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Transactions</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $wallet->transactions()->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Transaction</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $wallet->transactions()->latest()->first()?->created_at->format('M j') ?: 'Never' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Transaction History</h2>
                    <div class="flex items-center space-x-3">
                        <select class="px-3 py-2 border rounded-lg text-sm" onchange="filterTransactions(this.value)">
                            <option value="">All Types</option>
                            <option value="deposit">Deposits</option>
                            <option value="withdrawal">Withdrawals</option>
                            <option value="payment">Payments</option>
                            <option value="refund">Refunds</option>
                        </select>
                        <button onclick="exportTransactions()" class="text-gray-600 hover:text-gray-800">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->created_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($transaction->type === 'deposit')
                                            bg-green-100 text-green-800
                                        @elseif($transaction->type === 'withdrawal')
                                            bg-red-100 text-red-800
                                        @elseif($transaction->type === 'payment')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $transaction->description }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium
                                    @if($transaction->type === 'deposit')
                                        text-green-600
                                    @elseif($transaction->type === 'withdrawal')
                                        text-red-600
                                    @else
                                        text-gray-900
                                    @endif
                                ">
                                    @if($transaction->type === 'deposit')
                                        +
                                    @elseif($transaction->type === 'withdrawal')
                                        -
                                    @endif
                                    ${{ number_format($transaction->amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($transaction->status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($transaction->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button onclick="showTransactionDetails({{ $transaction->id }})" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-wallet text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions yet</h3>
                                    <p class="text-gray-500">Start by adding funds to your wallet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transactions->hasPages())
                <div class="bg-white px-4 py-3 border-t sm:px-6">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Funds Modal -->
<div id="addFundsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Funds</h3>
        
        <form id="addFundsForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                <input type="number" name="amount" step="0.01" min="1" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required autocomplete="off">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Payment Method</option>
                    <option value="credit_card">Credit Card</option>
                    <option value="paypal">PayPal</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="crypto">Cryptocurrency</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description (optional)</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" autocomplete="off"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeAddFundsModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Add Funds
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Withdraw Modal -->
<div id="withdrawModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Withdraw Funds</h3>
        
        <form id="withdrawForm">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                <input type="number" name="amount" step="0.01" min="1" max="{{ $wallet->balance }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                <p class="text-sm text-gray-500 mt-1">Available balance: ${{ number_format($wallet->balance, 2) }}</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Withdrawal Method</label>
                <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="">Select Withdrawal Method</option>
                    <option value="bank_transfer">Bank Transfer</option>
                    <option value="paypal">PayPal</option>
                    <option value="check">Check</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason (optional)</label>
                <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeWithdrawModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Withdraw
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaction Details</h3>
        <div id="transactionDetails" class="space-y-3">
            <!-- Transaction details will be inserted here -->
        </div>
        
        <div class="flex justify-end mt-6">
            <button onclick="closeTransactionModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function showAddFundsModal() {
    const modal = document.getElementById('addFundsModal');
    modal.classList.remove('hidden');
    
    // Enable all input fields
    const inputs = modal.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.disabled = false;
        input.readOnly = false;
    });
    
    // Reset form
    document.getElementById('addFundsForm').reset();
}

function closeAddFundsModal() {
    document.getElementById('addFundsModal').classList.add('hidden');
    document.getElementById('addFundsForm').reset();
}

function showWithdrawModal() {
    const modal = document.getElementById('withdrawModal');
    modal.classList.remove('hidden');
    
    // Enable all input fields
    const inputs = modal.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
        input.disabled = false;
        input.readOnly = false;
    });
    
    // Reset form
    document.getElementById('withdrawForm').reset();
}

function closeWithdrawModal() {
    document.getElementById('withdrawModal').classList.add('hidden');
    document.getElementById('withdrawForm').reset();
}

function showTransactionDetails(transactionId) {
    fetch('/wallet/transactions/' + transactionId)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('transactionModal');
            const details = document.getElementById('transactionDetails');
            
            details.innerHTML = '<div class="space-y-3"><div class="flex justify-between"><span class="text-gray-600">Transaction ID:</span><span class="font-medium">' + data.id + '</span></div><div class="flex justify-between"><span class="text-gray-600">Type:</span><span class="font-medium">' + data.type + '</span></div><div class="flex justify-between"><span class="text-gray-600">Amount:</span><span class="font-medium">$' + data.amount + '</span></div><div class="flex justify-between"><span class="text-gray-600">Status:</span><span class="font-medium">' + data.status + '</span></div><div class="flex justify-between"><span class="text-gray-600">Date:</span><span class="font-medium">' + new Date(data.created_at).toLocaleString() + '</span></div><div class="flex justify-between"><span class="text-gray-600">Description:</span><span class="font-medium">' + data.description + '</span></div></div>';
            
            modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}

function filterTransactions(type) {
    const url = new URL(window.location);
    if (type) {
        url.searchParams.set('type', type);
    } else {
        url.searchParams.delete('type');
    }
    window.location.href = url.toString();
}

function exportTransactions() {
    window.location.href = '/wallet/export';
}

// Form submissions
document.getElementById('addFundsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/wallet/deposit', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error adding funds');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});

document.getElementById('withdrawForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('/wallet/withdraw', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error withdrawing funds');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});
</script>
@endsection
