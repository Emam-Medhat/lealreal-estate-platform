@extends('layouts.app')

@section('title', 'Receipts')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Receipts</h1>
                    <p class="text-gray-600">View and manage your payment receipts</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Generate Receipt Button -->
                    <button onclick="generateReceipt()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Generate Receipt
                    </button>
                    
                    <!-- Export Button -->
                    <button onclick="exportReceipts()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-receipt text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Receipts</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $receipts->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">This Month</p>
                        <p class="text-2xl font-bold text-gray-800">12</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Amount</p>
                        <p class="text-2xl font-bold text-gray-800">$15,678.90</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Amount</p>
                        <p class="text-2xl font-bold text-gray-800">$1,306.58</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select id="filterDateRange" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Time</option>
                        <option value="today">Today</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Range</label>
                    <select id="filterAmount" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Amounts</option>
                        <option value="0-100">$0 - $100</option>
                        <option value="100-500">$100 - $500</option>
                        <option value="500-1000">$500 - $1,000</option>
                        <option value="1000+">$1,000+</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="voided">Voided</option>
                        <option value="refunded">Refunded</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Receipts Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Receipt List</h2>
                    
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search receipts..." class="pl-10 pr-4 py-2 border rounded-lg text-sm w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Receipt #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($receipts as $receipt)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-3">
                                            @if($receipt->status === 'active')
                                                <i class="fas fa-receipt text-green-600"></i>
                                            @elseif($receipt->status === 'voided')
                                                <i class="fas fa-receipt text-red-600"></i>
                                            @else
                                                <i class="fas fa-receipt text-gray-600"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ str_pad($receipt->id, 8, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $receipt->receipt_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $receipt->created_at->format('M j, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $receipt->created_at->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $receipt->payment->reference ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $receipt->payment->description ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $receipt->payment_method }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($receipt->amount, 2) }}</div>
                                    <div class="text-sm text-gray-500">{{ $receipt->currency }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($receipt->status === 'active')
                                            bg-green-100 text-green-800
                                        @elseif($receipt->status === 'voided')
                                            bg-red-100 text-red-800
                                        @elseif($receipt->status === 'refunded')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($receipt->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('payments.receipts.show', $receipt) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('payments.receipts.download', $receipt) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        
                                        <button onclick="sendEmail({{ $receipt->id }})" class="text-purple-600 hover:text-purple-900">
                                            <i class="fas fa-envelope"></i>
                                        </button>
                                        
                                        @if($receipt->status === 'active')
                                            <button onclick="voidReceipt({{ $receipt->id }})" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <button onclick="duplicateReceipt({{ $receipt->id }})" class="text-orange-600 hover:text-orange-900">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No receipts found</h3>
                                    <p class="text-gray-500 mb-6">You haven't generated any receipts yet.</p>
                                    <div class="mt-6">
                                        <button onclick="generateReceipt()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                            <i class="fas fa-plus mr-2"></i>
                                            Generate Your First Receipt
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($receipts->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6">
                {{ $receipts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Generate Receipt Modal -->
<div id="generateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Generate Receipt</h3>
        
        <form id="generateForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Reference</label>
                <input type="text" id="paymentReference" placeholder="Enter payment reference..." class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                <input type="number" id="receiptAmount" placeholder="0.00" step="0.01" class="w-full px-3 py-2 border rounded-lg" required>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea id="receiptDescription" rows="3" placeholder="Enter description..." class="w-full px-3 py-2 border rounded-lg"></textarea>
            </div>
        </form>
        
        <div class="flex justify-end space-x-3 mt-6">
            <button onclick="closeGenerateModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="submitGenerateReceipt()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Generate Receipt
            </button>
        </div>
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
let currentAction = null;
let currentReceiptId = null;

function generateReceipt() {
    document.getElementById('generateModal').classList.remove('hidden');
}

function closeGenerateModal() {
    document.getElementById('generateModal').classList.add('hidden');
    document.getElementById('generateForm').reset();
}

function submitGenerateReceipt() {
    const form = document.getElementById('generateForm');
    const formData = new FormData(form);
    
    fetch('{{ route("payments.receipts.generate") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            payment_reference: document.getElementById('paymentReference').value,
            amount: document.getElementById('receiptAmount').value,
            description: document.getElementById('receiptDescription').value,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeGenerateModal();
            location.reload();
        } else {
            alert(data.message || 'Error generating receipt');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error generating receipt');
    });
}

function sendEmail(receiptId) {
    if (confirm('Send this receipt via email?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/receipts/${receiptId}/send`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function voidReceipt(receiptId) {
    currentAction = 'void';
    currentReceiptId = receiptId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to void this receipt? This action cannot be undone.';
    document.getElementById('confirmButton').textContent = 'Void Receipt';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function duplicateReceipt(receiptId) {
    currentAction = 'duplicate';
    currentReceiptId = receiptId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to duplicate this receipt?';
    document.getElementById('confirmButton').textContent = 'Duplicate';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentAction = null;
    currentReceiptId = null;
}

document.getElementById('confirmButton').addEventListener('click', function() {
    if (currentAction && currentReceiptId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/receipts/${currentReceiptId}/${currentAction}`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Export receipts
function exportReceipts() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        window.location.href = `{{ route('payments.receipts.export') }}?format=${format}`;
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
    const dateRange = document.getElementById('filterDateRange').value;
    const method = document.getElementById('filterMethod').value;
    const amount = document.getElementById('filterAmount').value;
    const status = document.getElementById('filterStatus').value;
    
    const params = new URLSearchParams();
    
    if (dateRange) params.append('date_range', dateRange);
    if (method) params.append('payment_method', method);
    if (amount) params.append('amount_range', amount);
    if (status) params.append('status', status);
    
    window.location.href = `{{ route('payments.receipts.index') }}?${params.toString()}`;
}

// Auto-apply filters on change
['filterDateRange', 'filterMethod', 'filterAmount', 'filterStatus'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyFilters);
});

// Close modals when clicking outside
document.getElementById('generateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeGenerateModal();
    }
});

document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});
</script>
@endsection
