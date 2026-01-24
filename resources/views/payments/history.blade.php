@extends('layouts.app')

@section('title', 'Payment History')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment History</h1>
                    <p class="text-gray-600">View and manage your payment transactions</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Export Button -->
                    <button onclick="exportPayments()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    
                    <!-- Filter Button -->
                    <button onclick="toggleFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>
                        Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters Panel -->
        <div id="filtersPanel" class="bg-white rounded-lg shadow-sm p-6 mb-6 hidden">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select id="filterMethod" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Methods</option>
                        <option value="card">Credit Card</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="crypto">Cryptocurrency</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" id="filterDateFrom" class="w-full px-3 py-2 border rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" id="filterDateTo" class="w-full px-3 py-2 border rounded-lg">
                </div>
            </div>
            
            <div class="flex justify-end mt-4 space-x-3">
                <button onclick="clearFilters()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Clear
                </button>
                <button onclick="applyFilters()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Spent</p>
                        <p class="text-2xl font-bold text-gray-800">$12,345.67</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Completed</p>
                        <p class="text-2xl font-bold text-gray-800">23</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">3</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Failed</p>
                        <p class="text-2xl font-bold text-gray-800">1</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                    
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($payments as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-3">
                                            @if($payment->paymentMethod->type === 'card')
                                                <i class="fas fa-credit-card text-blue-600"></i>
                                            @elseif($payment->paymentMethod->type === 'bank')
                                                <i class="fas fa-university text-green-600"></i>
                                            @elseif($payment->paymentMethod->type === 'crypto')
                                                <i class="fab fa-bitcoin text-orange-600"></i>
                                            @else
                                                <i class="fas fa-wallet text-gray-600"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $payment->reference }}</div>
                                            <div class="text-sm text-gray-500">{{ $payment->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payment->created_at->format('M j, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $payment->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payment->paymentMethod->getDisplayName() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($payment->amount, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($payment->status === 'completed')
                                            bg-green-100 text-green-800
                                        @elseif($payment->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'failed')
                                            bg-red-100 text-red-800
                                        @elseif($payment->status === 'refunded')
                                            bg-gray-100 text-gray-800
                                        @else
                                            bg-blue-100 text-blue-800
                                        @endif
                                    ">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($payment->status === 'completed')
                                            <a href="{{ route('payments.receipts.generate') }}?payment_id={{ $payment->id }}" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-receipt"></i>
                                            </a>
                                        @endif
                                        
                                        @if($payment->status === 'pending' || $payment->status === 'failed')
                                            <button onclick="retryPayment({{ $payment->id }})" class="text-orange-600 hover:text-orange-900">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No payment history found</h3>
                                    <p class="text-gray-500">You haven't made any payments yet. Start by adding a payment method and making your first payment.</p>
                                    <div class="mt-6">
                                        <a href="{{ route('payments.methods.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add Payment Method
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($payments->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Toggle filters panel
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('hidden');
}

// Apply filters
function applyFilters() {
    const status = document.getElementById('filterStatus').value;
    const method = document.getElementById('filterMethod').value;
    const dateFrom = document.getElementById('filterDateFrom').value;
    const dateTo = document.getElementById('filterDateTo').value;
    
    const params = new URLSearchParams();
    
    if (status) params.append('status', status);
    if (method) params.append('payment_method', method);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);
    
    window.location.href = '{{ route("payments.index") }}?' + params.toString();
}

// Clear filters
function clearFilters() {
    document.getElementById('filterStatus').value = '';
    document.getElementById('filterMethod').value = '';
    document.getElementById('filterDateFrom').value = '';
    document.getElementById('filterDateTo').value = '';
    
    window.location.href = '{{ route("payments.index") }}';
}

// Export payments
function exportPayments() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        window.location.href = '{{ route("payments.export") }}?format=' + format;
    }
}

// Retry payment
function retryPayment(paymentId) {
    if (confirm('Are you sure you want to retry this payment?')) {
        window.location.href = `/payments/${paymentId}/retry`;
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

// Auto-apply filters from URL parameters
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.has('status')) {
        document.getElementById('filterStatus').value = urlParams.get('status');
    }
    if (urlParams.has('payment_method')) {
        document.getElementById('filterMethod').value = urlParams.get('payment_method');
    }
    if (urlParams.has('date_from')) {
        document.getElementById('filterDateFrom').value = urlParams.get('date_from');
    }
    if (urlParams.has('date_to')) {
        document.getElementById('filterDateTo').value = urlParams.get('date_to');
    }
});
</script>
@endsection
