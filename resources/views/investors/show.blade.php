@extends('layouts.app')

@section('title', 'Investor Details')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('investors.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="flex items-center">
                        <img class="h-16 w-16 rounded-full mr-4" src="{{ $investor->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->user->name ?? 'User') . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $investor->user->name ?? 'Investor' }}">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ $investor->user->name ?? 'N/A' }}</h1>
                            <p class="text-gray-600">{{ $investor->user->email ?? 'N/A' }}</p>
                            <div class="flex items-center space-x-2 mt-2">
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
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Verified
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('investors.edit', $investor) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit
                    </a>
                    
                    <button onclick="updateStatus({{ $investor->id }}, '{{ $investor->status }}')" class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-sync mr-2"></i>
                        Update Status
                    </button>
                    
                    @if($investor->verification_status !== 'verified')
                        <button onclick="verifyInvestor({{ $investor->id }})" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Verify
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $investor->investments->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Invested</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($investor->investments->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Average ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($investor->investments->avg('roi') ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Member Since</p>
                        <p class="text-lg font-bold text-gray-800">{{ $investor->created_at->format('M j, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Investor Profile -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Investor Profile</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Investor Type</p>
                                <p class="font-medium text-gray-800">{{ ucfirst($investor->investor_type) }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Investment Level</p>
                                <p class="font-medium text-gray-800">{{ ucfirst($investor->investment_level) }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Risk Profile</p>
                                <p class="font-medium text-gray-800">{{ ucfirst($investor->risk_profile) }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Investment Horizon</p>
                                <p class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $investor->investment_horizon)) }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm text-gray-600">Experience</p>
                                <p class="font-medium text-gray-800">{{ $investor->experience_years }} years</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Investment Knowledge</p>
                                <p class="font-medium text-gray-800">{{ ucfirst($investor->investment_knowledge) }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Accredited Investor</p>
                                <p class="font-medium text-gray-800">{{ $investor->accredited_investor ? 'Yes' : 'No' }}</p>
                            </div>
                            
                            <div>
                                <p class="text-sm text-gray-600">Employment Status</p>
                                <p class="font-medium text-gray-800">{{ ucfirst(str_replace('_', ' ', $investor->employment_status)) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Investment Goals -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Investment Goals</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($investor->investment_goals ?? [] as $goal)
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="text-gray-800">{{ ucfirst(str_replace('_', ' ', $goal)) }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm text-gray-600">Expected Return</p>
                            <p class="font-medium text-gray-800">{{ $investor->expected_return }}%</p>
                        </div>
                        
                        <div>
                            <p class="text-sm text-gray-600">Liquidity Needs</p>
                            <p class="font-medium text-gray-800">{{ ucfirst($investor->liquidity_needs) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Preferred Sectors -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-6">Preferred Sectors</h2>
                    
                    <div class="flex flex-wrap gap-2">
                        @foreach($investor->preferred_sectors ?? [] as $sector)
                            <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($sector) }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Investments -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-lg font-semibold text-gray-800">Recent Investments</h2>
                        <a href="{{ route('investor.portfolio.index', $investor) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View All
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($investor->investments->take(5) as $investment)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $investment->property->title ?? 'N/A' }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">${{ number_format($investment->amount, 2) }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ number_format($investment->roi ?? 0, 1) }}%</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $investment->created_at->format('M j, Y') }}</div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            No investments found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Financial Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Financial Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Initial Investment</span>
                            <span class="font-medium text-gray-800">${{ number_format($investor->initial_investment, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Invested</span>
                            <span class="font-medium text-gray-800">${{ number_format($investor->investments->sum('amount'), 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Net Worth</span>
                            <span class="font-medium text-gray-800">${{ number_format($investor->net_worth ?? 0, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Annual Income</span>
                            <span class="font-medium text-gray-800">${{ number_format($investor->annual_income ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Communication Preferences -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Communication</h3>
                    
                    <div class="space-y-2">
                        @if($investor->communication_preferences['email'] ?? false)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-envelope text-green-500 mr-2"></i>
                                <span>Email Notifications</span>
                            </div>
                        @endif
                        
                        @if($investor->communication_preferences['phone'] ?? false)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-phone text-green-500 mr-2"></i>
                                <span>Phone Calls</span>
                            </div>
                        @endif
                        
                        @if($investor->communication_preferences['sms'] ?? false)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-sms text-green-500 mr-2"></i>
                                <span>SMS Messages</span>
                            </div>
                        @endif
                        
                        @if($investor->communication_preferences['newsletter'] ?? false)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-newspaper text-green-500 mr-2"></i>
                                <span>Newsletter</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('investor.portfolio.index', $investor) }}" class="block w-full text-left px-4 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-briefcase mr-2"></i>
                            View Portfolio
                        </a>
                        
                        <a href="{{ route('investor.transactions.index', $investor) }}" class="block w-full text-left px-4 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                            <i class="fas fa-exchange-alt mr-2"></i>
                            View Transactions
                        </a>
                        
                        <a href="{{ route('investor.roi.index', $investor) }}" class="block w-full text-left px-4 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                            <i class="fas fa-chart-line mr-2"></i>
                            ROI Analysis
                        </a>
                        
                        <a href="{{ route('investor.risk.index', $investor) }}" class="block w-full text-left px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-shield-alt mr-2"></i>
                            Risk Assessment
                        </a>
                        
                        <button onclick="getRecommendations({{ $investor->id }})" class="block w-full text-left px-4 py-2 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors">
                            <i class="fas fa-lightbulb mr-2"></i>
                            Get Recommendations
                        </button>
                    </div>
                </div>

                <!-- Notes -->
                @if($investor->notes)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Notes</h3>
                        <p class="text-sm text-gray-600">{{ $investor->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
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
let currentInvestorId = {{ $investor->id }};

// Status update
function updateStatus(investorId, currentStatus) {
    document.getElementById('statusSelect').value = currentStatus;
    document.getElementById('statusModal').classList.remove('hidden');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
    document.getElementById('statusForm').reset();
}

function submitStatusUpdate() {
    const status = document.getElementById('statusSelect').value;
    const notes = document.getElementById('statusNotes').value;
    
    fetch('/investors/' + currentInvestorId + '/status', {
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
    document.getElementById('verificationModal').classList.remove('hidden');
}

function closeVerificationModal() {
    document.getElementById('verificationModal').classList.add('hidden');
    document.getElementById('verificationForm').reset();
}

function submitVerification() {
    const verificationStatus = document.getElementById('verificationStatus').value;
    const notes = document.getElementById('verificationNotes').value;
    
    fetch('/investors/' + currentInvestorId + '/verify', {
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

// Get recommendations
function getRecommendations(investorId) {
    fetch('/investors/' + investorId + '/recommendations', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let recommendations = 'Investment Recommendations:\n\n';
            data.recommendations.forEach((rec, index) => {
                recommendations += (index + 1) + '. ' + rec.type + '\n';
                recommendations += '   ' + rec.description + '\n';
                recommendations += '   Expected Return: ' + rec.expected_return + '\n';
                recommendations += '   Risk Level: ' + rec.risk_level + '\n\n';
            });
            alert(recommendations);
        } else {
            alert('Error fetching recommendations');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error fetching recommendations');
    });
}

// Close modals when clicking outside
document.getElementById('statusModal').addEventListener('click', function(e) {
    if (e.target === this) closeStatusModal();
});

document.getElementById('verificationModal').addEventListener('click', function(e) {
    if (e.target === this) closeVerificationModal();
});
</script>
@endsection
