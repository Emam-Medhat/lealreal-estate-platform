@extends('layouts.app')

@section('title', 'Wallet Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Wallet Details</h1>
                    <p class="text-gray-600">Complete wallet information and transaction history</p>
                </div>
                <a href="{{ route('user.wallet') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Wallet
                </a>
            </div>
        </div>

        <!-- Wallet Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-blue-100 text-sm mb-2">Current Balance</p>
                        <p class="text-3xl font-bold">${{ number_format($wallet->balance, 2) }}</p>
                        <p class="text-blue-100 text-sm">{{ $wallet->currency }}</p>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-full p-3">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                </div>
            </div>
            
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
        </div>

        <!-- Wallet Information -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Wallet Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Wallet ID:</span>
                        <span class="font-medium text-gray-800">#{{ str_pad($wallet->id, 8, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Currency:</span>
                        <span class="font-medium text-gray-800">{{ $wallet->currency }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Status:</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                            @if($wallet->status === 'active')
                                bg-green-100 text-green-800
                            @else
                                bg-red-100 text-red-800
                            @endif
                        ">
                            {{ ucfirst($wallet->status) }}
                        </span>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium text-gray-800">{{ $wallet->created_at->format('M j, Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Last Transaction:</span>
                        <span class="font-medium text-gray-800">
                            {{ $wallet->transactions()->latest()->first()?->created_at->format('M j, Y H:i') ?: 'Never' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Total Transactions:</span>
                        <span class="font-medium text-gray-800">{{ $wallet->transactions()->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transaction Methods -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Methods</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-credit-card text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Credit/Debit Card</h3>
                                <p class="text-sm text-gray-600">Visa, Mastercard, AMEX</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">Active</span>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <i class="fab fa-paypal text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">PayPal</h3>
                                <p class="text-sm text-gray-600">Fast and secure</p>
                            </div>
                        </div>
                        <span class="text-green-600 text-sm">Active</span>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-center	-center justify.
                    justify-between .between">
                       .
                        <.div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <i class="fas fa-university text-purple-    -600;600"></ .</i .div>
;>
                           
                            <;div>
  ;>
                                 ;                              < (h3
                                < 3 class=".
                                    "font》；font-medium
                                    -  "gray-
                                    - .800">Bank}Bank
                                    Transfer</h3>
                                    <p class="text-sm text-gray-600">Direct deposit</p .div
                               ;>
                        </
                        < .span class  . .text-green-600 text-sm">Active</span>
                    </div>
                </div>
                
                <div class="border rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <i class="fab fa-bitcoin text-yellow-600"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">Cryptocurrency</h3>
                                <p class="text-sm text-gray-600">Bitcoin, Ethereum</p>
                            </div>
                        </div>
                        <span class="text-gray-400 text-sm">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
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
                        @forelse ($wallet->transactions()->latest()->paginate(20) as $transaction)
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
        </div>
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
</script>
@endsection
