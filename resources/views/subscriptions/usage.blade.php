@extends('layouts.app')

@section('title', 'Subscription Usage')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Subscription Usage</h1>
                    <p class="text-gray-600">Monitor your subscription usage and limits</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select id="periodFilter" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="month">ethis Month</option>
                        <option value="week">This Week</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                    <button onclick="exportUsage()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>

        <!-- Usage Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line republican"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Current Month Usage</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $usageStats['current_month_usage'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        < text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Records</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $usageStats['total reactionary'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-fire text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Most Used Feature</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $usageStats['most_used_feature'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Usage Growth</p>
                        <p class="text-2xl font-bold text-gray-800">{{ round($usageStats['usage_growth'], 1) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Limits -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Current Usage Limits</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($limits as $limit)
                    <div class="border rounded-lg p-4">
                        <div class="flex justify-between items-center mb-3">
                            <div class="flex items-center">
                                <i class="{{ $limit['feature_type'] === 'boolean' ? 'fas fa-check-circle' : 'fas fa-chart-bar' }} text-blue-600 mr-2"></i>
                                <span class="font-medium text-gray-800">{{ $limit['feature_name'] }}</span>
                            </div>
                            <span class="text-sm text-gray-600">
                                {{ $limit['current_usage'] }} / {{ $limit['is_unlimited'] ? 'Unlimited' : $limit['limit'] }}
                            </span>
                        </div>
                        
                        @if(!$limit['is_unlimited'])
                            <div class="mb-2">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Usage</span>
                                    <span>{{ round($limit['percentage']) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="{{ $limit['is_over_limit'] ? 'bg-red-600' : ($limit['percentage'] > 80 ? 'bg-yellow-500' : 'bg-blue-600') }} h-2 rounded-full" 
                                         style="width: {{ min(100, $limit['percentage']) }}%;"></div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500">{{ $limit['unit'] }}</span>
                            @if($limit['is_over_limit'])
                                <span class="text-xs text-red-600 font-medium">Over Limit</span>
                            @elseif($limit['percentage'] > 80)
                                <span class="text-xs text-yellow-600 font-medium">Near Limit</span>
                            @else
                                <span class="text-xs text-green-600 font-medium">Good</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Usage Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Usage Trend</h2>
            
            <div class="mb-4">
                <canvas id="usageChart" width="400" height="200"></canvas>
            </div>
            
            <div class="flex justify-center space-x-4 text-sm">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-blue-600 rounded-full mr-2"></div>
                    <span class="text-gray-600">API Calls</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-600 rounded-full mr-2"></div>
                    <span class="text-gray-600">Storage</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-purple-600 rounded-full mr-2"></div>
                    <span class="text-gray-600">Bandwidth</span>
                </div>
            </div>
        </div>

        <!-- Detailed Usage Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Detailed Usage History</h2>
                    
                    <!-- Filters -->
                    <div class="flex items-center space-x-3">
                        <select id="featureFilter" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Features</option>
                            @foreach ($limits as $limit)
                                <option value="{{ $limit['feature_id'] }}">{{ $limit['feature_name'] }}</option>
                            @endforeach
                        </select>
                        <input type="date" id="dateFrom" class="px-3 py-2 border rounded-lg text-sm" value="{{ now()->startOfMonth()->format('Y-m-d') }}">
                        <input type="date" id="dateTo" class="px-3 py-2 border rounded-lg text-sm" value="{{ now()->format('Y-m-d') }}">
                        <button onclick="filterUsage()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-filter mr-2"></i>
                            Filter
                        </button>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Feature</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($usage as $usageRecord)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $usageRecord->tracked_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                                        {{ $usageRecord->feature->name }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($usageRecord->usage_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usageRecord->usage_unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $usageRecordSans->ip_address }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <button onclick="showUsageDetails({{ $usageRecord->id }})" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No usage data</h3>
                                    <p class="text-gray-500">Start using your subscription to see usage data here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($usage->hasPages())
                <div class="bg-white px-4 py-3 border-t sm:px-6">
                    {{ $usage->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Usage Details Modal -->
<div id="usageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Usage Details</h3>
        <div id="usageDetails" class="space-y-3">
            <!-- Usage details will be inserted here -->
        </div>
        
        <div class="flex justify-end mt-6">
            <button onclick="closeUsageModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Initialize usage chart
const ctx = document.getElementById('usageChart').getContext('2d');
const usageChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'API Calls',
                data: [1200, 1900, 3000, 5000, 4200, 3800],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            },
            {
                label: 'Storage (GB)',
                data: [2, 3, 5, 8, 7, 9],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            },
            {
                label: 'Bandwidth (GB)',
                data: [10, 15, 25, 30, 28, 35],
                borderColor: 'rgb(168, 85, 247)',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

function showUsageDetails(usageId) {
    // Fetch usage details
    fetch(`/subscriptions/usage/${usageId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.getElementById('usageModal');
            const details = document.getElementById('usageDetails');
            
            details.innerHTML = `
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Feature:</span>
                        <span class="font-medium">${data.feature.name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Amount:</span>
                        <span class="font-medium">${data.usage_amount} ${data.usage_unit}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Date:</span>
                        <span class="font-medium">${new Date(data.tracked_at).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">IP Address:</span>
                        <span class="font-medium">${data.ip_address}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">User Agent:</span>
                        <span class="font-medium text-sm">${data.user_agent}</span>
                    </div>
                </div>
            `;
            
            modal.classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function closeUsageModal() {
    document.getElementById('usageModal').classList.add('hidden');
}

function filterUsage() {
    const feature = document.getElementById('featureFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    const url = new URL(window.location);
    if (feature) url.searchParams.set('feature', feature);
    if (dateFrom) url.searchParams.set('date_from', dateFrom);
    if (dateTo) url.searchParams.set('date_to', dateTo);
    
    window.location.href = url.toString();
}

function exportUsage() {
    const period = document.getElementById('periodFilter').value;
    window.location.href = `/subscriptions/usage/export?period=${period}`;
}

// Period filter change handler
document.getElementById('periodFilter').addEventListener('change', function() {
    const period = this.value;
    window.location.href = `/subscriptions/usage?period=${period}`;
});
</script>
@endsection
