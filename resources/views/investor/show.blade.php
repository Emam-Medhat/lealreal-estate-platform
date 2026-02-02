@extends('layouts.app')

@section('title', $investor->first_name . ' ' . $investor->last_name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('investor.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                        <i class="fas fa-arrow-left"></i> Back to Investors
                    </a>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('investor.edit', $investor) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                </div>
            </div>
        </div>

        <!-- Investor Profile -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-center">
                        @if($investor->profile_picture)
                            <img class="h-24 w-24 rounded-full mx-auto object-cover" src="{{ asset('storage/' . $investor->profile_picture) }}" alt="{{ $investor->first_name }}">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-300 mx-auto flex items-center justify-center">
                                <span class="text-gray-600 font-bold text-2xl">{{ strtoupper(substr($investor->first_name, 0, 1)) }}</span>
                            </div>
                        @endif
                        
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $investor->first_name }} {{ $investor->last_name }}</h2>
                        @if($investor->company_name)
                            <p class="text-gray-600">{{ $investor->company_name }}</p>
                        @endif
                        
                        <div class="mt-4 space-y-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($investor->investor_type) }}
                            </span>
                            <br>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($investor->status == 'active') bg-green-100 text-green-800
                                @elseif($investor->status == 'inactive') bg-gray-100 text-gray-800
                                @elseif($investor->status == 'suspended') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($investor->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope w-5"></i>
                            <span class="ml-2">{{ $investor->email }}</span>
                        </div>
                        @if($investor->phone)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-phone w-5"></i>
                                <span class="ml-2">{{ $investor->phone }}</span>
                            </div>
                        @endif
                        @if($investor->experience_years)
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-clock w-5"></i>
                                <span class="ml-2">{{ $investor->experience_years }} years experience</span>
                            </div>
                        @endif
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-shield-alt w-5"></i>
                            <span class="ml-2">{{ ucfirst($investor->risk_tolerance) }} risk tolerance</span>
                        </div>
                        @if($investor->accredited_investor)
                            <div class="flex items-center text-sm text-green-600">
                                <i class="fas fa-check-circle w-5"></i>
                                <span class="ml-2">Accredited Investor</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Investment Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Investment Summary</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600">Total Invested</p>
                            <p class="text-xl font-bold text-gray-900">${{ number_format($investor->total_invested, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Returns</p>
                            <p class="text-xl font-bold text-green-600">${{ number_format($investor->total_returns, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Net Invested</p>
                            <p class="text-xl font-bold text-blue-600">${{ number_format($investor->total_invested - $investor->total_returns, 2) }}</p>
                        </div>
                        @if($investor->total_invested > 0)
                            <div>
                                <p class="text-sm text-gray-600">ROI</p>
                                <p class="text-xl font-bold text-purple-600">{{ number_format(($investor->total_returns / $investor->total_invested) * 100, 2) }}%</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Bio -->
                @if($investor->bio)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">About</h3>
                        <p class="text-gray-600">{{ $investor->bio }}</p>
                    </div>
                @endif
                
                <!-- Verification Status -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Verification Status</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($investor->verification_status == 'verified') bg-green-100 text-green-800
                                @elseif($investor->verification_status == 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($investor->verification_status) }}
                            </span>
                            @if($investor->verified_at)
                                <p class="text-sm text-gray-600 mt-2">Verified on {{ $investor->verified_at->format('M d, Y') }}</p>
                            @endif
                        </div>
                        @if($investor->verification_status != 'verified')
                            <button onclick="showVerificationModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Update Verification
                            </button>
                        @endif
                    </div>
                </div>
                
                <!-- Status Management -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Status Management</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600">Current Status</p>
                            <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full 
                                @if($investor->status == 'active') bg-green-100 text-green-800
                                @elseif($investor->status == 'inactive') bg-gray-100 text-gray-800
                                @elseif($investor->status == 'suspended') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($investor->status) }}
                            </span>
                        </div>
                        <button onclick="showStatusModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Update Status
                        </button>
                    </div>
                </div>
                
                <!-- Portfolio Overview -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Portfolio Overview</h3>
                    @if($investor->portfolios && $investor->portfolios->count() > 0)
                        <div class="space-y-4">
                            @foreach($investor->portfolios as $portfolio)
                                <div class="border-l-4 border-blue-500 pl-4">
                                    <h4 class="font-medium text-gray-900">{{ $portfolio->name }}</h4>
                                    <p class="text-sm text-gray-600">{{ $portfolio->description }}</p>
                                    <div class="flex justify-between items-center mt-2">
                                        <p class="text-sm font-medium text-blue-600">Value: ${{ number_format($portfolio->total_value, 2) }}</p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($portfolio->status == 'active') bg-green-100 text-green-800
                                            @elseif($portfolio->status == 'inactive') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($portfolio->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500">No portfolio items found.</p>
                    @endif
                </div>
                
                <!-- Investment Goals -->
                @if($investor->investment_goals && count($investor->investment_goals) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Investment Goals</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($investor->investment_goals as $goal)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                    {{ ucfirst($goal) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Preferred Sectors -->
                @if($investor->preferred_sectors && count($investor->preferred_sectors) > 0)
                    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Preferred Sectors</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($investor->preferred_sectors as $sector)
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">
                                    {{ ucfirst($sector) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Recent Transactions -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Transactions</h3>
                    @if($investor->transactions && $investor->transactions->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($investor->transactions->take(5) as $transaction)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($transaction->type == 'investment') bg-blue-100 text-blue-800
                                                    @elseif($transaction->type == 'return') bg-green-100 text-green-800
                                                    @elseif($transaction->type == 'withdrawal') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ${{ number_format($transaction->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($transaction->status == 'completed') bg-green-100 text-green-800
                                                    @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ ucfirst($transaction->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $transaction->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if($investor->transactions->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View all transactions →
                                </a>
                            </div>
                        @endif
                    @else
                        <p class="text-gray-500">No transactions found.</p>
                    @endif
                </div>
                
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-medium">{{ $investor->email }}</p>
                        </div>
                        @if($investor->phone)
                            <div>
                                <p class="text-sm text-gray-600">Phone</p>
                                <p class="font-medium">{{ $investor->phone }}</p>
                            </div>
                        @endif
                        @if($investor->company_name)
                            <div>
                                <p class="text-sm text-gray-600">Company</p>
                                <p class="font-medium">{{ $investor->company_name }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Member Since</p>
                            <p class="font-medium">{{ $investor->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div id="statusModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-user-edit mr-3 text-blue-600"></i>
                    Update Investor Status
                </h3>
                <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="status" value="active" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-3 font-medium text-gray-900">Active</span>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Active</span>
                </label>
                
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="status" value="inactive" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-3 font-medium text-gray-900">Inactive</span>
                    </div>
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Inactive</span>
                </label>
                
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="status" value="suspended" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-3 font-medium text-gray-900">Suspended</span>
                    </div>
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Suspended</span>
                </label>
                
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="status" value="verified" class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                        <span class="ml-3 font-medium text-gray-900">Verified</span>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Verified</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <button onclick="closeStatusModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="updateStatus()" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all font-medium shadow-lg">
                    <i class="fas fa-save mr-2"></i>
                    Update Status
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Verification Update Modal -->
<div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 transform transition-all">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-shield-alt mr-3 text-green-600"></i>
                    Update Verification Status
                </h3>
                <button onclick="closeVerificationModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="space-y-6">
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="verification_status" value="pending" class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-3 font-medium text-gray-900">Pending</span>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Pending</span>
                </label>
                
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="verification_status" value="verified" class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-3 font-medium text-gray-900">Verified</span>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Verified</span>
                </label>
                
                <label class="flex items-center justify-between p-4 border-2 border-gray-200 rounded-xl hover:border-green-300 hover:bg-green-50 transition-all cursor-pointer group">
                    <div class="flex items-center">
                        <input type="radio" name="verification_status" value="rejected" class="w-4 h-4 text-green-600 focus:ring-green-500">
                        <span class="ml-3 font-medium text-gray-900">Rejected</span>
                    </div>
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Rejected</span>
                </label>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8">
                <button onclick="closeVerificationModal()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors font-medium">
                    Cancel
                </button>
                <button onclick="updateVerification()" class="px-6 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl hover:from-green-700 hover:to-green-800 transition-all font-medium shadow-lg">
                    <i class="fas fa-check-circle mr-2"></i>
                    Update Verification
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Set current status as selected
    const currentStatus = '{{ $investor->status }}';
    const radio = document.querySelector(`input[name="status"][value="${currentStatus}"]`);
    if (radio) radio.checked = true;
}

function closeStatusModal() {
    const modal = document.getElementById('statusModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function showVerificationModal() {
    const modal = document.getElementById('verificationModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    // Set current verification status as selected
    const currentStatus = '{{ $investor->verification_status }}';
    const radio = document.querySelector(`input[name="verification_status"][value="${currentStatus}"]`);
    if (radio) radio.checked = true;
}

function closeVerificationModal() {
    const modal = document.getElementById('verificationModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function updateStatus() {
    const selectedStatus = document.querySelector('input[name="status"]:checked');
    if (selectedStatus) {
        const status = selectedStatus.value;
        fetch(`{{ route('investor.update.status', $investor) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeStatusModal();
                location.reload();
            } else {
                alert('Error updating status: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating status');
        });
    } else {
        alert('Please select a status');
    }
}

function updateVerification() {
    const selectedStatus = document.querySelector('input[name="verification_status"]:checked');
    if (selectedStatus) {
        const status = selectedStatus.value;
        fetch(`{{ route('investor.update.verification', $investor) }}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ verification_status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                closeVerificationModal();
                location.reload();
            } else {
                alert('Error updating verification: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating verification');
        });
    } else {
        alert('Please select a verification status');
    }
}
</script>
@endsection
