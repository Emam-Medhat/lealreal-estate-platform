@extends('layouts.app')

@section('title', 'Agent Performance')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Performance Dashboard</h1>
                    <p class="text-gray-600">Track your performance metrics and achievements</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select onchange="changePeriod(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year">This Year</option>
                    </select>
                    <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Report
                    </button>
                    <a href="{{ route('agent.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Score -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">Overall Performance Score</h2>
                    <p class="text-gray-600">Based on sales, client satisfaction, and activity</p>
                </div>
                <div class="text-center">
                    <div class="text-5xl font-bold text-blue-600">{{ $performance['overall_score'] }}</div>
                    <div class="text-sm text-gray-600 mt-1">out of 100</div>
                    <div class="text-sm text-green-600 mt-2">
                        <i class="fas fa-arrow-up mr-1"></i>{{ $performance['score_change'] }}% from last period
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Performance Level</span>
                    <span>{{ $performance['level'] }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full" style="width: {{ $performance['overall_score'] }}%"></div>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties Sold</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $metrics['properties_sold'] }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $metrics['sales_growth'] }}% growth
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($metrics['total_sales'], 0) }}</p>
                        <p class="text-xs text-blue-600 mt-1">
                            {{ $metrics['avg_sale_price'] }} avg price
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">New Clients</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $metrics['new_clients'] }}</p>
                        <p class="text-xs text-purple-600 mt-1">
                            {{ $metrics['conversion_rate'] }}% conversion
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Client Rating</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $metrics['avg_rating'] }}/5</p>
                        <p class="text-xs text-yellow-600 mt-1">
                            {{ $metrics['total_reviews'] }} reviews
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Sales Trend -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sales Trend</h3>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Sales performance over time</p>
                    </div>
                </div>
                <div class="flex justify-between mt-4 text-sm text-gray-600">
                    @foreach ($salesTrend as $period => $value)
                        <span>{{ $period }}: {{ $value }}</span>
                    @endforeach
                </div>
            </div>
            
            <!-- Lead Conversion -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Lead Conversion Funnel</h3>
                <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-filter text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">Lead conversion rates</p>
                    </div>
                </div>
                <div class="space-y-2 mt-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">New Leads</span>
                        <span class="font-medium text-gray-800">{{ $conversion['new_leads'] }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Qualified</span>
                        <span class="font-medium text-gray-800">{{ $conversion['qualified'] }} ({{ $conversion['qualified_rate'] }}%)</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Converted</span>
                        <span class="font-medium text-green-600">{{ $conversion['converted'] }} ({{ $conversion['conversion_rate'] }}%)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Activity Breakdown</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Communication</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Phone Calls</span>
                            <span class="font-medium text-gray-800">{{ $activity['calls'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Emails Sent</span>
                            <span class="font-medium text-gray-800">{{ $activity['emails'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Text Messages</span>
                            <span class="font-medium text-gray-800">{{ $activity['texts'] }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Property Activities</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Showings</span>
                            <span class="font-medium text-gray-800">{{ $activity['showings'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Open Houses</span>
                            <span class="font-medium text-gray-800">{{ $activity['open_houses'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Virtual Tours</span>
                            <span class="font-medium text-gray-800">{{ $activity['virtual_tours'] }}</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-3">Marketing</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Social Posts</span>
                            <span class="font-medium text-gray-800">{{ $activity['social_posts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Blog Articles</span>
                            <span class="font-medium text-gray-800">{{ $activity['blog_posts'] }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Property Ads</span>
                            <span class="font-medium text-gray-800">{{ $activity['property_ads'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Achievements & Goals -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Achievements -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Recent Achievements</h2>
                <div class="space-y-3">
                    @forelse ($achievements as $achievement)
                        <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <i class="fas fa-trophy text-yellow-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $achievement['title'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $achievement['description'] }}</p>
                                <p class="text-xs text-gray-500">{{ $achievement['date'] }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No achievements yet</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Goals Progress -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Goals Progress</h2>
                <div class="space-y-4">
                    @foreach ($goals as $goal)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-800">{{ $goal['name'] }}</span>
                                <span class="text-sm text-gray-600">{{ $goal['current'] }} / {{ $goal['target'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $goal['percentage'] }}%"></div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">{{ $goal['percentage'] }}% complete</p>
                        </div>
                    @endforeach
                </div>
                <button onclick="setNewGoal()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Set New Goal
                </button>
            </div>
        </div>

        <!-- Rankings -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Team Rankings</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($rankings as $rank)
                            <tr class="{{ $rank['is_current'] ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($rank['position'] <= 3)
                                            <div class="bg-{{ $rank['position'] === 1 ? 'yellow' : ($rank['position'] === 2 ? 'gray' : 'orange') }}-100 rounded-full p-1 mr-2">
                                                <i class="fas fa-trophy text-{{ $rank['position'] === 1 ? 'yellow' : ($rank['position'] === 2 ? 'gray' : 'orange') }}-600 text-xs"></i>
                                            </div>
                                        @endif
                                        <span class="font-medium text-gray-900">#{{ $rank['position'] }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full w-8 h-8 mr-3 flex items-center justify-center">
                                            @if($rank['avatar'])
                                                <img src="{{ $rank['avatar'] }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-gray-400 text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $rank['name'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $rank['sales'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($rank['revenue'], 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $rank['score'] }}</span>
                                    @if($rank['is_current'])
                                        <span class="ml-2 bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">You</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function exportReport() {
    window.location.href = '/agent/performance/export';
}

function setNewGoal() {
    window.location.href = '/agent/performance/goals/create';
}
</script>
@endsection
