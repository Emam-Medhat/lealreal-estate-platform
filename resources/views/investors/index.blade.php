@extends('layouts.app')

@section('title', 'Investors Management')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Investors Management</h1>
                    <p class="text-gray-600">Manage investor profiles and investment activities</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Export Button -->
                    <button onclick="exportInvestors()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    
                    <!-- Add Investor Button -->
                    <a href="{{ route('investors.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Investor
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investors</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $investors->total() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Investors</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $investors->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-shield-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Verified Investors</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $investors->where('verification_status', 'verified')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investments</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($investors->sum('initial_investment'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" id="searchInput" placeholder="Search investors..." class="w-full px-3 py-2 border rounded-lg">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                        <option value="suspended">Suspended</option>
                        <option value="verified">Verified</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Investment Level</label>
                    <select id="levelFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Risk Profile</label>
                    <select id="riskFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Risk Profiles</option>
                        <option value="conservative">Conservative</option>
                        <option value="moderate">Moderate</option>
                        <option value="aggressive">Aggressive</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Investors Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Investors List</h2>
                    
                    <div class="flex items-center space-x-4">
                        <!-- View Toggle -->
                        <div class="flex items-center space-x-2">
                            <button onclick="setView('table')" id="tableView" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">
                                <i class="fas fa-table"></i>
                            </button>
                            <button onclick="setView('grid')" id="gridView" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg text-sm">
                                <i class="fas fa-th"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <div id="tableViewContent" class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Profile</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Initial Investment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($investors as $investor)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full" src="{{ $investor->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->user->name ?? 'User') . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $investor->user->name ?? 'Investor' }}">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $investor->user->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $investor->user->email ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($investor->investor_type === 'individual')
                                            bg-blue-100 text-blue-800
                                        @elseif($investor->investor_type === 'institutional')
                                            bg-purple-100 text-purple-800
                                        @elseif($investor->investor_type === 'corporate')
                                            bg-green-100 text-green-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($investor->investor_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($investor->investment_level === 'beginner')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($investor->investment_level === 'intermediate')
                                            bg-blue-100 text-blue-800
                                        @elseif($investor->investment_level === 'advanced')
                                            bg-purple-100 text-purple-800
                                        @else
                                            bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($investor->investment_level) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($investor->risk_profile === 'conservative')
                                            bg-green-100 text-green-800
                                        @elseif($investor->risk_profile === 'moderate')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-red-100 text-red-800
                                        @endif
                                    ">
                                        {{ ucfirst($investor->risk_profile) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($investor->initial_investment, 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($investor->status === 'active')
                                                bg-green-100 text-green-800
                                            @elseif($investor->status === 'inactive')
                                                bg-gray-100 text-gray-800
                                            @elseif($investor->status === 'suspended')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif
                                        ">
                                            {{ ucfirst($investor->status) }}
                                        </span>
                                        
                                        @if($investor->verification_status === 'verified')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Verified
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('investors.show', $investor) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('investors.edit', $investor) }}" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button onclick="updateStatus({{ $investor->id }}, '{{ $investor->status }}')" class="text-orange-600 hover:text-orange-900">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        
                                        @if($investor->verification_status !== 'verified')
                                            <button onclick="verifyInvestor({{ $investor->id }})" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-shield-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No investors found</h3>
                                    <p class="text-gray-500 mb-6">Start by adding your first investor to the system.</p>
                                    <a href="{{ route('investors.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                        <i class="fas fa-plus mr-2"></i>
                                        Add First Investor
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Grid View -->
            <div id="gridViewContent" class="hidden p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($investors as $investor)
                        <div class="bg-white border rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center mb-4">
                                <img class="h-12 w-12 rounded-full mr-3" src="{{ $investor->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->user->name ?? 'User') . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $investor->user->name ?? 'Investor' }}">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $investor->user->name ?? 'N/A' }}</h3>
                                    <p class="text-sm text-gray-500">{{ $investor->user->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Type:</span>
                                    <span class="font-medium">{{ ucfirst($investor->investor_type) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Level:</span>
                                    <span class="font-medium">{{ ucfirst($investor->investment_level) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Risk:</span>
                                    <span class="font-medium">{{ ucfirst($investor->risk_profile) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Investment:</span>
                                    <span class="font-medium">${{ number_format($investor->initial_investment, 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($investor->status === 'active')
                                            bg-green-100 text-green-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($investor->status) }}
                                    </span>
                                    
                                    @if($investor->verification_status === 'verified')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Verified
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('investors.show', $investor) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('investors.edit', $investor) }}" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No investors found</h3>
                            <p class="text-gray-500 mb-6">Start by adding your first investor to the system.</p>
                            <a href="{{ route('investors.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Investor
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($investors->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6 mt-6">
                {{ $investors->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Investor Status</h3>
        
        <form id="statusForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusSelect" class="w-full px-3 py-2 border rounded-lg">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                    <option value="verified">Verified</option>
                    <option value="restricted">Restricted</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea id="statusNotes" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter notes for status change..."></textarea>
            </div>
        </form>
        
        <div class="flex justify-end space-x-3 mt-6">
            <button onclick="closeStatusModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="submitStatusUpdate()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Update Status
            </button>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Verify Investor</h3>
        
        <form id="verificationForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Verification Status</label>
                <select id="verificationStatus" class="w-full px-3 py-2 border rounded-lg">
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                    <option value="pending">Pending</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Verification Notes</label>
                <textarea id="verificationNotes" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Enter verification notes..."></textarea>
            </div>
        </form>
        
        <div class="flex justify-end space-x-3 mt-6">
            <button onclick="closeVerificationModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="submitVerification()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Verify Investor
            </button>
        </div>
    </div>
</div>

<script>
let currentInvestorId = null;

// View toggle
function setView(view) {
    const tableView = document.getElementById('tableView');
    const gridView = document.getElementById('gridView');
    const tableViewContent = document.getElementById('tableViewContent');
    const gridViewContent = document.getElementById('gridViewContent');
    
    if (view === 'table') {
        tableView.classList.add('bg-blue-600', 'text-white');
        tableView.classList.remove('bg-gray-200', 'text-gray-700');
        gridView.classList.add('bg-gray-200', 'text-gray-700');
        gridView.classList.remove('bg-blue-600', 'text-white');
        
        tableViewContent.classList.remove('hidden');
        gridViewContent.classList.add('hidden');
    } else {
        gridView.classList.add('bg-blue-600', 'text-white');
        gridView.classList.remove('bg-gray-200', 'text-gray-700');
        tableView.classList.add('bg-gray-200', 'text-gray-700');
        tableView.classList.remove('bg-blue-600', 'text-white');
        
        gridViewContent.classList.remove('hidden');
        tableViewContent.classList.add('hidden');
    }
}

// Status update
function updateStatus(investorId, currentStatus) {
    currentInvestorId = investorId;
    document.getElementById('statusSelect').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    currentInvestorId = null;
    document.getElementById('statusForm').reset();
}

function submitStatusUpdate() {
    const status = document.getElementById('statusSelect').value;
    const notes = document.getElementById('statusNotes').value;
    
    fetch(`/investors/${currentInvestorId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            status: status,
            notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeStatusModal();
            location.reload();
        } else {
            alert(data.message || 'Error updating status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating status');
    });
}

// Verification
function verifyInvestor(investorId) {
    currentInvestorId = investorId;
    document.getElementById('verificationModal').classList.remove('hidden');
}

function closeVerificationModal() {
    document.getElementById('verificationModal').classList.add('hidden');
    currentInvestorId = null;
    document.getElementById('verificationForm').reset();
}

function submitVerification() {
    const verificationStatus = document.getElementById('verificationStatus').value;
    const notes = document.getElementById('verificationNotes').value;
    
    fetch(`/investors/${currentInvestorId}/verify`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
        body: JSON.stringify({
            verification_status: verificationStatus,
            verification_notes: notes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeVerificationModal();
            location.reload();
        } else {
            alert(data.message || 'Error verifying investor');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error verifying investor');
    });
}

// Export
function exportInvestors() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        const status = document.getElementById('statusFilter').value;
        const level = document.getElementById('levelFilter').value;
        const risk = document.getElementById('riskFilter').value;
        
        const params = new URLSearchParams({
            format: format,
            status: status,
            investment_level: level,
            risk_profile: risk
        });
        
        window.location.href = '/investors/export?' + params.toString();
    }
}

// Search and filter
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// Apply filters
['statusFilter', 'levelFilter', 'riskFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', function() {
        const status = document.getElementById('statusFilter').value;
        const level = document.getElementById('levelFilter').value;
        const risk = document.getElementById('riskFilter').value;
        
        const params = new URLSearchParams();
        if (status) params.append('status', status);
        if (level) params.append('investment_level', level);
        if (risk) params.append('risk_profile', risk);
        
        window.location.href = '/investors?' + params.toString();
    });
});

// Close modals when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});

document.getElementById('verificationModal').addEventListener('click', function(e) {
    if (e.target === this) closeVerificationModal();
});
</script>
@endsection
