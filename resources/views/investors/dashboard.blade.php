@extends('layouts.app')

@section('title', 'Investor Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <img class="h-16 w-16 rounded-full mr-4" src="{{ $investor->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($investor->user->name ?? 'User') . '&color=7F9CF5&background=EBF4FF' }}" alt="{{ $investor->user->name ?? 'Investor' }}">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, {{ $investor->user->first_name ?? $investor->user->name ?? 'Investor' }}!</h1>
                        <p class="text-gray-600">Here's your investment overview</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Quick Actions -->
                    <button onclick="showQuickActions()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Quick Actions
                    </button>
                    
                    <!-- Notifications -->
                    <div class="relative">
                        <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-bell"></i>
                            @if($notifications > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ $notifications }}
                                </span>
                            @endif
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-wallet text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Portfolio Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($portfolioValue, 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +12.5% this month
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Returns</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalReturns, 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +8.3% this month
                        </p>
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
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($averageROI, 1) }}%</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +2.1% this month
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $activeProperties }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $pendingProperties }} pending
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Portfolio Performance Chart -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Portfolio Performance</h2>
                    <select id="chartPeriod" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>

            <!-- Investment Distribution -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Investment Distribution</h2>
                <div class="space-y-4">
                    @foreach($investmentDistribution as $type => $data)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                                <span class="text-sm text-gray-600">{{ $data['count'] }} properties</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $data['percentage'] }}%;"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-500">${{ number_format($data['amount'], 2) }}</span>
                                <span class="text-xs text-gray-500">{{ number_format($data['percentage'], 1) }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
            <!-- Recent Investments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Investments</h2>
                    <a href="{{ route('investor.portfolio.index', $investor) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        View All
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($recentInvestments as $investment)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                @if($investment->property->featured_image)
                                    <img src="{{ $investment->property->featured_image }}" alt="{{ $investment->property->title }}" class="h-10 w-10 rounded-full mr-3">
                                @endif
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $investment->property->title ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">{{ $investment->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">${{ number_format($investment->amount, 2) }}</p>
                                <p class="text-xs {{ $investment->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($investment->roi, 1) }}%
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-briefcase text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No recent investments</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                    <a href="{{ route('investor.transactions.index', $investor) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        View All
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($recentTransactions as $transaction)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="mr-3">
                                    @if($transaction->type === 'deposit')
                                        <i class="fas fa-arrow-down text-green-600"></i>
                                    @elseif($transaction->type === 'withdrawal')
                                        <i class="fas fa-arrow-up text-red-600"></i>
                                    @else
                                        <i class="fas fa-exchange-alt text-blue-600"></i>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ ucfirst($transaction->type) }}</p>
                                    <p class="text-xs text-gray-500">{{ $transaction->created_at->format('M j, Y') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $transaction->type === 'deposit' ? '+' : '-' }}${{ number_format($transaction->amount, 2) }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $transaction->status }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No recent transactions</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Recommended Opportunities</h2>
                <a href="{{ route('investment.opportunities.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($recommendedOpportunities as $opportunity)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                        @if($opportunity->featured_image)
                            <img src="{{ $opportunity->featured_image }}" alt="{{ $opportunity->title }}" class="w-full h-32 object-cover rounded-lg mb-4">
                        @endif
                        
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $opportunity->title }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($opportunity->description, 100) }}</p>
                        
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Expected Return:</span>
                                <span class="font-medium text-green-600">{{ $opportunity->expected_return }}%</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Min Investment:</span>
                                <span class="font-medium">${{ number_format($opportunity->min_investment, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Risk Level:</span>
                                <span class="font-medium">{{ ucfirst($opportunity->risk_level) }}</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('investment.opportunities.show', $opportunity) }}" class="block w-full text-center bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Learn More
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-lightbulb text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recommended opportunities at this time</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Modal -->
<div id="quickActionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-6">Quick Actions</h3>
        
        <div class="space-y-3">
            <a href="{{ route('investor.portfolio.create', $investor) }}" class="block w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                <i class="fas fa-plus mr-3"></i>
                Add New Investment
            </a>
            
            <a href="{{ route('investor.transactions.create', $investor) }}" class="block w-full text-left px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors">
                <i class="fas fa-exchange-alt mr-3"></i>
                Make Transaction
            </a>
            
            <a href="{{ route('investor.roi.index', $investor) }}" class="block w-full text-left px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                <i class="fas fa-chart-line mr-3"></i>
                View ROI Analysis
            </a>
            
            <a href="{{ route('investor.risk.index', $investor) }}" class="block w-full text-left px-4 py-3 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                <i class="fas fa-shield-alt mr-3"></i>
                Risk Assessment
            </a>
            
            <a href="{{ route('investors.edit', $investor) }}" class="block w-full text-left px-4 py-3 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-user-edit mr-3"></i>
                Update Profile
            </a>
        </div>
        
        <div class="flex justify-end mt-6">
            <button onclick="closeQuickActions()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Quick actions modal
function showQuickActions() {
    document.getElementById('quickActionsModal').classList.remove('hidden');
}

function closeQuickActions() {
    document.getElementById('quickActionsModal').classList.add('hidden');
}

// Chart period change
document.getElementById('chartPeriod').addEventListener('change', function(e) {
    const period = e.target.value;
    // Update chart based on period
    updateChart(period);
});

function updateChart(period) {
    const ctx = document.getElementById('performanceChart');
    if (ctx) {
        // Simple chart update - integrate with Chart.js
        ctx.innerHTML = '<div class="text-gray-500">Performance chart for ' + period + ' days</div>';
    }
}

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    updateChart(30);
});

// Close modal when clicking outside
document.getElementById('quickActionsModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickActions();
    }
});
</script>
@endsection
