@extends('layouts.app')

@section('title', 'Investor Portfolio')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Investment Portfolio</h1>
                    <p class="text-gray-600">{{ $investor->user->name ?? 'Investor' }}'s investment portfolio</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Export Button -->
                    <button onclick="exportPortfolio()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    
                    <!-- Add Investment Button -->
                    <a href="{{ route('investor.portfolio.create', $investor) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Add Investment
                    </a>
                </div>
            </div>
        </div>

        <!-- Portfolio Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-briefcase text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Investments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $investments->count() }}</p>
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
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($investments->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Current Value</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($investments->sum('current_value'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($investments->avg('roi') ?? 0, 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Portfolio Performance</h2>
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <canvas id="portfolioChart"></canvas>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type</label>
                    <select id="propertyTypeFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Types</option>
                        <option value="residential">Residential</option>
                        <option value="commercial">Commercial</option>
                        <option value="industrial">Industrial</option>
                        <option value="retail">Retail</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range</label>
                    <select id="dateRangeFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="">All Time</option>
                        <option value="7">Last 7 Days</option>
                        <option value="30">Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select id="sortByFilter" class="w-full px-3 py-2 border rounded-lg">
                        <option value="created_at">Date Added</option>
                        <option value="amount">Investment Amount</option>
                        <option value="roi">ROI</option>
                        <option value="current_value">Current Value</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Investments Grid -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Investments</h2>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center space-x-2">
                        <button onclick="setView('grid')" id="gridView" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">
                            <i class="fas fa-th"></i>
                        </button>
                        <button onclick="setView('list')" id="listView" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg text-sm">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Grid View -->
            <div id="gridViewContent" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($investments as $investment)
                        <div class="bg-white border rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $investment->property->title ?? 'N/A' }}</h3>
                                    <p class="text-sm text-gray-600">{{ $investment->property->location ?? 'N/A' }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($investment->status === 'active')
                                        bg-green-100 text-green-800
                                    @elseif($investment->status === 'completed')
                                        bg-blue-100 text-blue-800
                                    @else
                                        bg-yellow-100 text-yellow-800
                                    @endif
                                ">
                                    {{ ucfirst($investment->status) }}
                                </span>
                            </div>
                            
                            @if($investment->property->featured_image)
                                <img src="{{ $investment->property->featured_image }}" alt="{{ $investment->property->title }}" class="w-full h-32 object-cover rounded-lg mb-4">
                            @endif
                            
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Invested:</span>
                                    <span class="font-medium">${{ number_format($investment->amount, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Current Value:</span>
                                    <span class="font-medium">${{ number_format($investment->current_value, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">ROI:</span>
                                    <span class="font-medium {{ $investment->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($investment->roi, 1) }}%
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">{{ $investment->created_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ ucfirst($investment->property->type ?? 'N/A') }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('investor.portfolio.show', [$investor, $investment]) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('investor.portfolio.edit', [$investor, $investment]) }}" class="text-gray-600 hover:text-gray-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-12">
                            <i class="fas fa-briefcase text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No investments found</h3>
                            <p class="text-gray-500 mb-6">This investor hasn't made any investments yet.</p>
                            <a href="{{ route('investor.portfolio.create', $investor) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                <i class="fas fa-plus mr-2"></i>
                                Add First Investment
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- List View -->
            <div id="listViewContent" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Value</th>
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
                                            @if($investment->property->featured_image)
                                                <img src="{{ $investment->property->featured_image }}" alt="{{ $investment->property->title }}" class="h-10 w-10 rounded-full mr-3">
                                            @endif
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">{{ $investment->property->title ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-500">{{ $investment->property->location ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ ucfirst($investment->property->type ?? 'N/A') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($investment->amount, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">${{ number_format($investment->current_value, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium {{ $investment->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($investment->roi, 1) }}%
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($investment->status === 'active')
                                                bg-green-100 text-green-800
                                            @elseif($investment->status === 'completed')
                                                bg-blue-100 text-blue-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif
                                        ">
                                            {{ ucfirst($investment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('investor.portfolio.show', [$investor, $investment]) }}" class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('investor.portfolio.edit', [$investor, $investment]) }}" class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-briefcase text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No investments found</h3>
                                        <p class="text-gray-500 mb-6">This investor hasn't made any investments yet.</p>
                                        <a href="{{ route('investor.portfolio.create', $investor) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                                            <i class="fas fa-plus mr-2"></i>
                                            Add First Investment
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if($investments->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6 mt-6">
                {{ $investments->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// View toggle
function setView(view) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridViewContent = document.getElementById('gridViewContent');
    const listViewContent = document.getElementById('listViewContent');
    
    if (view === 'grid') {
        gridView.classList.add('bg-blue-600', 'text-white');
        gridView.classList.remove('bg-gray-200', 'text-gray-700');
        listView.classList.add('bg-gray-200', 'text-gray-700');
        listView.classList.remove('bg-blue-600', 'text-white');
        
        gridViewContent.classList.remove('hidden');
        listViewContent.classList.add('hidden');
    } else {
        listView.classList.add('bg-blue-600', 'text-white');
        listView.classList.remove('bg-gray-200', 'text-gray-700');
        gridView.classList.add('bg-gray-200', 'text-gray-700');
        gridView.classList.remove('bg-blue-600', 'text-white');
        
        listViewContent.classList.remove('hidden');
        gridViewContent.classList.add('hidden');
    }
}

// Export portfolio
function exportPortfolio() {
    const format = prompt('Choose export format:', 'csv');
    if (format && ['csv', 'xlsx', 'json'].includes(format)) {
        const propertyType = document.getElementById('propertyTypeFilter').value;
        const status = document.getElementById('statusFilter').value;
        const dateRange = document.getElementById('dateRangeFilter').value;
        
        const params = new URLSearchParams({
            format: format,
            property_type: propertyType,
            status: status,
            date_range: dateRange
        });
        
        window.location.href = '/investor/portfolio/{{ $investor->id }}/export?' + params.toString();
    }
}

// Apply filters
function applyFilters() {
    const propertyType = document.getElementById('propertyTypeFilter').value;
    const status = document.getElementById('statusFilter').value;
    const dateRange = document.getElementById('dateRangeFilter').value;
    const sortBy = document.getElementById('sortByFilter').value;
    
    const params = new URLSearchParams();
    if (propertyType) params.append('property_type', propertyType);
    if (status) params.append('status', status);
    if (dateRange) params.append('date_range', dateRange);
    if (sortBy) params.append('sort_by', sortBy);
    
    window.location.href = '/investor/portfolio/{{ $investor->id }}?' + params.toString();
}

// Auto-apply filters on change
['propertyTypeFilter', 'statusFilter', 'dateRangeFilter', 'sortByFilter'].forEach(id => {
    document.getElementById(id).addEventListener('change', applyFilters);
});

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('portfolioChart');
    if (ctx) {
        // Simple chart initialization - you can integrate Chart.js here
        ctx.innerHTML = '<div class="text-gray-500">Portfolio performance chart will be displayed here</div>';
    }
});
</script>
@endsection
