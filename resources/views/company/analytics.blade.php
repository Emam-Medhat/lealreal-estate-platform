@extends('layouts.app')

@section('title', 'Company Analytics')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Analytics</h1>
                    <p class="text-gray-600">Track your company's performance and metrics</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select onchange="changePeriod(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="7">Last 7 Days</option>
                        <option value="30" selected>Last 30 Days</option>
                        <option value="90">Last 90 Days</option>
                        <option value="365">Last Year</option>
                    </select>
                    <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Report
                    </button>
                    <a href="{{ route('company.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($analytics['total_revenue'], 0) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $analytics['revenue_growth'] }}% from last period
                        </p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Properties Sold</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['properties_sold'] }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $analytics['sales_growth'] }}% from last period
                        </p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">New Leads</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['new_leads'] }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $analytics['leads_growth'] }}% from last period
                        </p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <i class="fas fa-user-plus text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Conversion Rate</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $analytics['conversion_rate'] }}%</p>
                        <p class="text-xs text-red-600 mt-1">
                            <i class="fas fa-arrow-down mr-1"></i>{{ $analytics['conversion_change'] }}% from last period
                        </p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue Trend</h3>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-area text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Revenue chart visualization</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4 text-sm text-gray-600">
                    <span>Jan: ${{ number_format($analytics['monthly_revenue']['jan'], 0) }}</span>
                    <span>Feb: ${{ number_format($analytics['monthly_revenue']['feb'], 0) }}</span>
                    <span>Mar: ${{ number_format($analytics['monthly_revenue']['mar'], 0) }}</span>
                    <span>Apr: ${{ number_format($analytics['monthly_revenue']['apr'], 0) }}</span>
                </div>
            </div>
            
            <!-- Properties Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Properties by Status</h3>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-pie text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Property status distribution</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-4 text-sm">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                        <span>For Sale: {{ $analytics['property_status']['for_sale'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                        <span>For Rent: {{ $analytics['property_status']['for_rent'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-gray-500 rounded-full mr-2"></div>
                        <span>Sold: {{ $analytics['property_status']['sold'] }}</span>
                    </div>
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                        <span>Rented: {{ $analytics['property_status']['rented'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Performance -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Team Performance</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Member</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Properties</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($analytics['team_performance'] as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full w-8 h-8 mr-3 flex items-center justify-center">
                                            @if($member['avatar'])
                                                <img src="{{ $member['avatar'] }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-gray-400 text-xs"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $member['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $member['role'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $member['properties'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $member['sales'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($member['revenue'], 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-1 mr-2">
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $member['performance'] }}%"></div>
                                            </div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $member['performance'] }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No team performance data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Lead Sources -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Lead Sources</h3>
                <div class="space-y-3">
                    @foreach ($analytics['lead_sources'] as $source)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-{{ $source['icon'] }} text-blue-600 text-sm"></i>
                                </div>
                                <span class="text-sm font-medium text-gray-800">{{ $source['name'] }}</span>
                            </div>
                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 mr-3">{{ $source['count'] }} leads</span>
                                <span class="text-sm font-medium text-gray-800">{{ $source['percentage'] }}%</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Top Properties -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Performing Properties</h3>
                <div class="space-y-3">
                    @foreach ($analytics['top_properties'] as $property)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $property['title'] }}</p>
                                <p class="text-xs text-gray-600">{{ $property['views'] }} views</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">${{ number_format($property['price'], 0) }}</p>
                                <span class="text-xs text-green-600">{{ $property['status'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
            <div class="space-y-3">
                @forelse ($analytics['recent_activity'] as $activity)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                            <i class="fas fa-{{ $activity['icon'] }} text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No recent activity</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function exportReport() {
    window.location.href = '/company/analytics/export';
}
</script>
@endsection
