@extends('admin.layouts.admin')

@section('title', 'Enterprise Accounts')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-building text-blue-600 mr-3"></i>
                    Enterprise Accounts
                </h1>
                <p class="text-gray-600">Manage enterprise accounts and their status</p>
            </div>
            <div class="flex gap-3">
                <button onclick="createAccount()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    New Account
                </button>
                <button onclick="refreshAccounts()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $accounts->count() }}</p>
                        <p class="text-gray-600 text-sm">Total Accounts</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-building text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $accounts->where('status', 'active')->count() }}</p>
                        <p class="text-gray-600 text-sm">Active</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $accounts->where('status', 'pending')->count() }}</p>
                        <p class="text-gray-600 text-sm">Pending</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $accounts->whereIn('status', ['suspended', 'banned'])->count() }}</p>
                        <p class="text-gray-600 text-sm">Suspended/Banned</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <form id="account-filters" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" id="search-accounts" placeholder="Search accounts..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select id="filter-status" onchange="filterAccounts()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                        <select id="filter-type" onchange="filterAccounts()" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="individual">Individual</option>
                            <option value="company">Company</option>
                            <option value="developer">Developer</option>
                            <option value="investor">Investor</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="clearFilters()" 
                                class="w-full px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Accounts Table -->
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if($accounts->count() > 0)
                            @foreach($accounts as $account)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold mr-3">
                                                {{ strtoupper(substr($account->company_name ?? 'Unknown', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $account->company_name ?? 'Unknown Account' }}</div>
                                                <div class="text-sm text-gray-500">{{ $account->contact_email ?? 'unknown@example.com' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ getAccountTypeColor($account->company_type ?? 'enterprise') }}-100 text-{{ getAccountTypeColor($account->company_type ?? 'enterprise') }}-800">
                                            {{ ucfirst($account->company_type ?? 'enterprise') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-{{ getAccountStatusColor($account->status ?? 'pending') }}-100 text-{{ getAccountStatusColor($account->status ?? 'pending') }}-800">
                                            {{ ucfirst($account->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('M d, Y') : 'Unknown' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $account->updated_at ? \Carbon\Carbon::parse($account->updated_at)->format('M d, Y') : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('enterprise.accounts.view', $account->id) }}" 
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('enterprise.accounts.edit', $account->id) }}" 
                                                    class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('enterprise.accounts.toggle-status', ['id' => $account->id]) }}" method="POST" class="inline-block" style="display:inline;" onsubmit="return confirm('Are you sure you want to change this account status?');">
                                                @csrf
                                                <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Toggle Status">
                                                    <i class="fas fa-power-off"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-building text-4xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No accounts found</h3>
                                        <p class="text-gray-500">There are no enterprise accounts to display.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Account Details Modal -->
    <div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="accountModal">
        <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Account Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="p-6">
                <div id="account-details">
                    <!-- Account details will be loaded here -->
                </div>
            </div>
            <div class="flex justify-end p-6 border-t border-gray-200">
                <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function createAccount() {
    window.location.href = '/enterprise/accounts/create';
}

function refreshAccounts() {
    location.reload();
}

function filterAccounts() {
    const status = document.getElementById('filter-status').value;
    const type = document.getElementById('filter-type').value;
    const search = document.getElementById('search-accounts').value;
    
    // Build URL with filters
    let url = '/enterprise/accounts';
    const params = new URLSearchParams();
    
    if (status) params.append('status', status);
    if (type) params.append('type', type);
    if (search) params.append('search', search);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

function clearFilters() {
    document.getElementById('search-accounts').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-type').value = '';
    filterAccounts();
}

function closeModal() {
    document.getElementById('accountModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('accountModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>
@endpush

@php
function getAccountTypeColor($type) {
    $colors = [
        'individual' => 'primary',
        'company' => 'success',
        'developer' => 'info',
        'investor' => 'warning',
        'admin' => 'danger',
        'super_admin' => 'dark'
    ];
    return $colors[$type] ?? 'secondary';
}

function getAccountStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'active' => 'success',
        'suspended' => 'danger',
        'banned' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}
@endphp
