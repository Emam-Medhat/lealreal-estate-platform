@extends('layouts.app')

@section('title', 'Investment Portfolio')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Investment Portfolio</h1>
                    <p class="text-gray-600">Track and manage your real estate investments</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exportPortfolio()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    <a href="{{ route('investor.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Portfolio Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($portfolio['total_value'], 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $portfolio['value_change'] }}%
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Returns</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($portfolio['total_returns'], 2) }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            {{ $portfolio['total_roi'] }}% ROI
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $portfolio['property_count'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $portfolio['active_count'] }} active
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-coins text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Monthly Income</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($portfolio['monthly_income'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $portfolio['income_sources'] }} sources
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Portfolio Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Portfolio Performance</h2>
                <select onchange="changePeriod(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                    <option value="1m">1 Month</option>
                    <option value="3m">3 Months</option>
                    <option value="6m">6 Months</option>
                    <option value="1y" selected>1 Year</option>
                    <option value="all">All Time</option>
                </select>
            </div>
            
            <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Portfolio performance over time</p>
                </div>
            </div>
        </div>

        <!-- Investment Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Asset Distribution</h2>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-pie-chart text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Investment distribution by property type</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach ($distribution as $type => $percentage)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($type) }}</span>
                            <span class="text-sm font-medium text-gray-800">{{ $percentage }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Income Sources</h2>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-bar text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Monthly income by source</p>
                    </div>
                </div>
                <div class="mt-4 space-y-2">
                    @foreach ($incomeSources as $source => $amount)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ ucfirst($source) }}</span>
                            <span class="text-sm font-medium text-gray-800">${{ number_format($amount, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Investments List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Your Investments</h2>
                    <div class="flex items-center space-x-3">
                        <input type="text" placeholder="Search investments..." class="px-3 py-2 border rounded-lg text-sm">
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option>All Status</option>
                            <option>Active</option>
                            <option>Completed</option>
                            <option>Pending</option>
                        </select>
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option>All Types</option>
                            <option>Equity</option>
                            <option>Debt</option>
                            <option>Mixed</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returns</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($investments as $investment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full w-10 h-10 mr-3 flex items-center justify-center">
                                            @if($investment->property->image)
                                                <img src="{{ $investment->property->image }}" alt="" class="w-10 h-10 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-building text-gray-400 text-xs"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $investment->property->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $investment->property->location }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($investment->amount, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($investment->current_value, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-{{ $investment->returns >= 0 ? 'green' : 'red' }}-600">
                                        ${{ number_format($investment->returns, 0) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-{{ $investment->returns_percentage >= 0 ? 'green' : 'red' }}-600">
                                        {{ $investment->returns_percentage >= 0 ? '+' : '' }}{{ number_format($investment->returns_percentage, 2) }}%
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($investment->status === 'active')
                                            bg-green-100 text-green-800
                                        @elseif($investment->status === 'completed')
                                            bg-blue-100 text-blue-800
                                        @elseif($investment->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($investment->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="viewInvestment({{ $investment->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="downloadStatement({{ $investment->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Investments Yet</h3>
                                    <p class="text-gray-500 mb-4">Start investing in real estate opportunities.</p>
                                    <button onclick="exploreOpportunities()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-search mr-2"></i>
                                        Explore Opportunities
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Investment Goals -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Investment Goals Progress</h2>
            
            <div class="space-y-4">
                @foreach ($goals as $goal)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $goal['name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $goal['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-800">${{ number_format($goal['current'], 0) }}</div>
                                <div class="text-sm text-gray-600">of ${{ number_format($goal['target'], 0) }}</div>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal['percentage'] }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-1">
                            <span class="text-xs text-gray-500">{{ $goal['percentage'] }}% complete</span>
                            <span class="text-xs text-gray-500">{{ $goal['time_remaining'] }} remaining</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function exportPortfolio() {
    window.location.href = '/investor/portfolio/export';
}

function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function viewInvestment(investmentId) {
    window.location.href = '/investor/portfolio/' + investmentId;
}

function downloadStatement(investmentId) {
    window.location.href = '/investor/portfolio/' + investmentId + '/statement';
}

function exploreOpportunities() {
    window.location.href = '/investor/opportunities';
}
</script>
@endsection
