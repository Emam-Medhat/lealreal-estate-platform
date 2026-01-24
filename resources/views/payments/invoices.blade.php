@extends('layouts.app')

@section('title', 'Invoices')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Invoices</h1>
                    <p class="text-gray-600">Manage and track your invoices</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Create Invoice Button -->
                    <a href="{{ route('payments.invoices.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Invoice
                    </a>
                    
                    <!-- Export Button -->
                    <button onclick="exportInvoices()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
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
                        <i class="fas fa-file-invoice text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Invoices</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $invoices->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Paid</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $invoices->where('status', 'paid')->count() }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ $invoices->where('status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Outstanding</p>
                        <p class="text-2xl font-bold text-gray-800">$8,234.50</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="filterStatus" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="sent">Sent</option>
                        <option value="paid">Paid</option>
                        <option value="overdue">Overdue</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select id="filterDateRange" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Time</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                    <input type="text" id="filterClient" placeholder="Search by client..." class="w-full px-3 py-2 border rounded-lg">
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
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Invoice List</h2>
                    
                    <!-- Search -->
                    <div class="relative">
                        <input type="text" id="searchInput" placeholder="Search invoices..." class="pl-10 pr-4 py-2 border rounded-lg text-sm w-64">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issue Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($invoices as $invoice)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="mr-3">
                                            @if($invoice->status === 'paid')
                                                <i class="fas fa-file-invoice text-green-600"></i>
                                            @elseif($invoice->status === 'overdue')
                                                <i class="fas fa-file-invoice text-red-600"></i>
                                            @else
                                                <i class="fas fa-file-invoice text-blue-600"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ str_pad($invoice->id, 6, '0', STR_PAD_LEFT) }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $invoice->reference }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $invoice->client_name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $invoice->client_email ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $invoice->issue_date->format('M j, Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $invoice->due_date->format('M j, Y') }}</div>
                                    @if($invoice->isOverdue())
                                        <div class="text-xs text-red-600 font-medium">Overdue</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($invoice->amount, 2) }}</div>
                                    <div class="text-sm text-gray-500">{{ $invoice->currency }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($invoice->status === 'paid')
                                            bg-green-100 text-green-800
                                        @elseif($invoice->status === 'overdue')
                                            bg-red-100 text-red-800
                                        @elseif($invoice->status === 'sent')
                                            bg-blue-100 text-blue-800
                                        @elseif($invoice->status === 'draft')
                                            bg-gray-100 text-gray-800
                                        @else
                                            bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('payments.invoices.show', $invoice) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('payments.invoices.edit', $invoice) }}" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <a href="{{ route('payments.invoices.download', $invoice) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        
                                        @if($invoice->status === 'sent')
                                            <button onclick="markAsPaid({{ $invoice->id }})" class="text-orange-600 hover:text-orange-900">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        @if(in_array($invoice->status, ['draft', 'sent']))
                                            <button onclick="cancelInvoice({{ $invoice->id }})" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-file-invoice text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No invoices found</h3>
                                    <p class="text-gray-500 mb-6">You haven't created any invoices yet.</p>
                                    <a href="{{ route('payments.invoices.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Your First Invoice
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if($invoices->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6">
                {{ $invoices->links() }}
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
            <button id="confirmButton" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Confirm
            </button>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentInvoiceId = null;

function markAsPaid(invoiceId) {
    currentAction = 'markPaid';
    currentInvoiceId = invoiceId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to mark this invoice as paid?';
    document.getElementById('confirmButton').textContent = 'Mark as Paid';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function cancelInvoice(invoiceId) {
    currentAction = 'cancel';
    currentInvoiceId = invoiceId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to cancel this invoice?';
    document.getElementById('confirmButton').textContent = 'Cancel Invoice';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentAction = null;
    currentInvoiceId = null;
}

document.getElementById('confirmButton').addEventListener('click', function() {
    if (currentAction && currentInvoiceId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/invoices/${currentInvoiceId}/${currentAction}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Export invoices
function exportInvoices() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        window.location.href = `{{ route('payments.invoices.export') }}?format=${format}`;
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
    const status = document.getElementById('filterStatus').value;
    const dateRange = document.getElementById('filterDateRange').value;
    const client = document.getElementById('filterClient').value;
    const amount = document.getElementById('filterAmount').value;
    
    const params = new URLSearchParams();
    
    if (status) params.append('status', status);
    if (dateRange) params.append('date_range', dateRange);
    if (client) params.append('client', client);
    if (amount) params.append('amount_range', amount);
    
    window.location.href = `{{ route('payments.invoices.index') }}?${params.toString()}`;
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});

// Auto-apply filters on change
['filterStatus', 'filterDateRange', 'filterClient', 'filterAmount'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyFilters);
});
</script>
@endsection
