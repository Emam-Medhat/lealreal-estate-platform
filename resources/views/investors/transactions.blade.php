@extends('layouts.app')

@section('title', 'Investor Transactions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Transaction History</h1>
                    <p class="text-gray-600">{{ $investor->user->name ?? 'Investor' }}'s transaction records</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Add Transaction Button -->
                    <a href="{{ route('investor.transactions.create', $investor) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Transaction
                    </a>
                    
                    <!-- Export Button -->
                    <button onclick="exportTransactions()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Transaction Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-arrow-down text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Deposits</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalDeposits, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $depositCount }} transactions</p>
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
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalWithdrawals, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $withdrawalCount }} transactions</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exchange-alt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Net Balance</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($netBalance, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Current balance</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($monthlyTotal, 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +15.3%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                    <select id="typeFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Types</option>
                        <option value="deposit">Deposit</option>
                        <option value="withdrawal">Withdrawal</option>
                        <option value="investment">Investment</option>
                        <option value="return">Return</option>
                        <option value="fee">Fee</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select id="dateRangeFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Time</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Range</label>
                    <select id="amountRangeFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Amounts</option>
                        <option value="0-1000">$0 - $1,000</option>
                        <option value="1000-5000">$1,000 - $5,000</option>
                        <option value="5000-10000">$5,000 - $10,000</option>
                        <option value="10000+">$10,000+</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Transaction History</h2>
                    
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search transactions..." class="pl-10 pr-4 py-2 border rounded-lg text-sm w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($transactions as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm text-gray-900">{{ $transaction->created_at->format('M j, Y') }}</div>
                                        <div class="text-sm text-gray-500">{{ $transaction->created_at->format('g:i A') }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-2">
                                            @if($transaction->type === 'deposit')
                                                <i class="fas fa-arrow-down text-green-600"></i>
                                            @elseif($transaction->type === 'withdrawal')
                                                <i class="fas fa-arrow-up text-red-600"></i>
                                            @elseif($transaction->type === 'investment')
                                                <i class="fas fa-building text-blue-600"></i>
                                            @elseif($transaction->type === 'return')
                                                <i class="fas fa-chart-line text-purple-600"></i>
                                            @else
                                                <i class="fas fa-exchange-alt text-gray-600"></i>
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->type) }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $transaction->description }}</div>
                                    @if($transaction->reference)
                                        <div class="text-sm text-gray-500">Ref: {{ $transaction->reference }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $transaction->type === 'deposit' || $transaction->type === 'return' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $transaction->type === 'deposit' || $transaction->type === 'return' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($transaction->balance_after, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($transaction->status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($transaction->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($transaction->status === 'failed')
                                            bg-red-100 text-red-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($transaction->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('investor.transactions.show', [$investor, $transaction]) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($transaction->status === 'pending')
                                            <button onclick="cancelTransaction({{ $transaction->id }})" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <a href="{{ route('investor.transactions.receipt', [$investor, $transaction]) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-exchange-alt text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                                    <p class="text-gray-500 mb-6">This investor hasn't made any transactions yet.</p>
                                    <a href="{{ route('investor.transactions.create', $investor) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Transaction
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($transactions->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6 mt-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Action</h3>
        <p id="confirmMessage" class="text-gray-600 mb-6"></p>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let currentTransactionId = null;

// Cancel transaction
function cancelTransaction(transactionId) {
    currentTransactionId = transactionId;
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to cancel this transaction? This action cannot be undone.';
    document.getElementById('confirmButton').textContent = 'Cancel Transaction';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentTransactionId = null;
}

document.getElementById('confirmButton').addEventListener('click', function() {
    if (currentTransactionId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/investor/transactions/' + currentTransactionId + '/cancel';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Export transactions
function exportTransactions() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        const type = document.getElementById('typeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        const amountRange = document.getElementById('amountRangeFilter').value;
        
        const params = new URLSearchParams({
            format: format,
            type: type,
            status: status,
            date_range: dateRange,
            amount_range: amountRange
        });
        
        window.location.href = '/investor/transactions/{{ $investor->id }}/export?' + params.toString();
    }
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Apply filters
function applyFilters() {
    const type = document.getElementById('typeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const dateRange = document.getElementById('dateRangeFilter').value;
    const amountRange = document.getElementById('amountRangeFilter').value;
    
    const params = new URLSearchParams();
    if (type) params.append('type', type);
    if (status) params.append('status', status);
    if (dateRange) params.append('date_range', dateRange);
    if (amountRange) params.append('amount_range', amountRange);
    
    window.location.href = '/investor/transactions/{{ $investor->id }}?' + params.toString();
}

// Auto-apply filters on change
['typeFilter', 'statusFilter', 'dateRangeFilter', 'amountRangeFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyFilters);
});

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});
</script>
@endsection
