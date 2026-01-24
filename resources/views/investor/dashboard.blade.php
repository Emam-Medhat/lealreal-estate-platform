@extends('layouts.app')

@section('title', 'Investor Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Investor Dashboard</h1>
                    <p class="text-gray-600">Manage your real estate investment portfolio</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="exploreOpportunities()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>
                        Explore Opportunities
                    </button>
                </div>
            </div>
        </div>

        <!-- Portfolio Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investment</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($portfolio['total_investment'], 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $portfolio['growth'] }}% this month
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
                            {{ $portfolio['roi'] }}% ROI
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
                        <p class="text-sm text-gray-600">Active Investments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $portfolio['active_investments'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $portfolio['pending_investments'] }} pending
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
                            From {{ $portfolio['income_sources'] }} sources
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Investment Performance</h2>
            <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Portfolio performance over time</p>
                </div>
            </div>
            <div class="flex justify-between mt-4 text-sm text-gray-600">
                @foreach ($monthlyPerformance as $month => $value)
                    <span>{{ $month }}: ${{ number_format($value, 0) }}</span>
                @endforeach
            </div>
        </div>

        <!-- Recent Investments -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Recent Investments</h2>
                <a href="{{ route('investor.portfolio') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="space-y-3">
                @forelse ($recentInvestments as $investment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 cursor-pointer" onclick="viewInvestment({{ $investment->id }})">
                        <div class="flex items-center">
                            <div class="bg-gray-200 rounded-full w-12 h-12 mr-3 flex items-center justify-center">
                                @if($investment->property->image)
                                    <img src="{{ $investment->property->image }}" alt="" class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <i class="fas fa-building text-gray-400 text-sm"></i>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $investment->property->name }}</h4>
                                <div class="flex items-center space-x-3 text-sm text-gray-600">
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $investment->created_at->format('M j, Y') }}</span>
                                    <span><i class="fas fa-dollar-sign mr-1"></i>${{ number_format($investment->amount, 0) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800">${{ number_format($investment->current_value, 0) }}</div>
                            <div class="text-sm text-{{ $investment->returns >= 0 ? 'green' : 'red' }}-600">
                                {{ $investment->returns >= 0 ? '+' : '' }}{{ number_format($investment->returns_percentage, 2) }}%
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No investments yet</p>
                @endforelse
            </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">New Opportunities</h2>
                <a href="{{ route('investor.opportunities') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($opportunities as $opportunity)
                    <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-medium text-gray-800">{{ $opportunity->property->name }}</h4>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $opportunity->investment_type }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($opportunity->description, 80) }}</p>
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-lg font-bold text-gray-800">${{ number_format($opportunity->minimum_investment, 0) }}</span>
                            <span class="text-sm text-gray-600">{{ $opportunity->expected_roi }}% ROI</span>
                        </div>
                        <div class="mb-3">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Funded</span>
                                <span class="font-medium text-gray-800">{{ $opportunity->funded_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $opportunity->funded_percentage }}%"></div>
                            </div>
                        </div>
                        <button onclick="viewOpportunity({{ $opportunity->id }})" class="w-full bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors text-sm">
                            View Details
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-full p-3 mr-3">
                        <i class="fas fa-calculator text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">ROI Calculator</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Calculate potential returns on investments</p>
                <a href="{{ route('investor.roi-calculator') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Calculate ROI →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 rounded-full p-3 mr-3">
                        <i class="fas fa-shield-alt text-yellow-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">Risk Assessment</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Analyze investment risks and mitigation strategies</p>
                <a href="{{ route('investor.risk-assessment') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Assess Risk →
                </a>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 rounded-full p-3 mr-3">
                        <i class="fas fa-coins text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800">DeFi Loans</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">Access decentralized finance lending options</p>
                <a href="{{ route('investor.defi-loans') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View Loans →
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function exploreOpportunities() {
    window.location.href = '/investor/opportunities';
}

function viewInvestment(investmentId) {
    window.location.href = '/investor/portfolio/' + investmentId;
}

function viewOpportunity(opportunityId) {
    window.location.href = '/investor/opportunities/' + opportunityId;
}
</script>
@endsection
