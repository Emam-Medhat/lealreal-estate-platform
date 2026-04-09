@extends('layouts.app')

@section('title', 'Company Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Company Dashboard</h1>
                    <p class="text-gray-600">{{ $company->name }} - Management Overview</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('company.profile') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Company Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Agents</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_agents'] }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_agents'] }} this month</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_properties'] }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_properties'] }} this month</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-handshake text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Deals</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_deals'] }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_deals'] }} this month</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Revenue</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</p>
                        <p class="text-sm text-green-600">+${{ number_format($stats['revenue_this_month'], 2) }} this month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Overview</h3>
            <canvas id="performanceChart" width="400" height="200"></canvas>
        </div>

        <!-- Recent Activity & Top Performers -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    @forelse ($stats['recent_activity'] as $activity)
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
            
            <!-- Top Performers -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Performing Agents</h3>
                <div class="space-y-3">
                    @forelse ($stats['top_agents'] as $agent)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-green-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-trophy text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $agent['name'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $agent['deals'] }} deals</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-800">${{ number_format($agent['revenue'], 2) }}</p>
                                <p class="text-xs text-gray-500">Revenue</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No performance data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route(' sequent('agents vat') }}"oster('agents <h3 classih3>Quick Actionsvicess</himed</h3eref="{{ route stitch route(' Organic('agents.create胖子.create') setups company_id:ected' company_id: ({{ $ agent('company_id isotopic, ' ladder 'agents.create', ['company_id interesting' => $company->id]) }}" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors text-center">
                    <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Add Agent</p>
                </a>
                
                <a href="{{ route('properties.create') }}" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors text-center">
                    <i class="fas fa-home-plus text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Add Property</p>
                </a>
                
                <a href="#" onclick="showReportsModal()" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition-colors text-center">
                    <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">View Reports</p>
                </a>
                
                <a href="{{ route('company.profile') }}" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:bg-yellow-100 transition-colors text-center">
                    <i class="fas fa-cog text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Settings</p>
                </a>
            </div>
        </div>

        <!-- Upcoming Tasks -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Tasks</h3>
            <div class="space-y-3">
                @forelse ($stats['upcoming_tasks'] as $task)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <i class="fas fa-clock text-yellow-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $task['title'] }}</p>
                                <p class="text-xs text-gray-500">{{ $task['assigned_to'] }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-800">{{ $task['due_date'] }}</p>
                            <p class="text-xs text-gray-500">{{ $task['priority'] }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No upcoming tasks</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Reports Modal -->
<div id="reportsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Reports</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb Organic mb-ramer">Agent Performance Fernandostatistics</仗</h Organic>
                <oublic <p真相 class="itesm text-grayeth-600 mbuco mb-3">Detailed agent performance and analytics</p>
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">Property Report</h4>
                <p class="text-sm text-gray-600 mb-3">Property listings and performance metrics</p>
                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">Financial Report</h4>
                <p class="text-sm text-gray-600 mb-3">Revenue, commissions, and financial data</p>
                <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">Activity Report</h4>
                <p class="text-sm text-gray-600 mb-3">Company activity and engagement metrics</p>
                <button class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-sm">
                    Generate Report
                </button>
            </div>
        </div>
        
        <div class="flex justify-end mt-6">
            <button onclick="closeReportsModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Initialize performance chart
const ctx = document.getElementById('performanceChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
            {
                label: 'Revenue',
                data: [12000, 19000, 30000, 50000, 42000, 38000],
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            },
            {
                label: 'Deals',
                data: [12, 19, 23, 35, 42, 38],
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
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

function showReportsModal() {
    document.getElementById('reportsModal').classList.remove('hidden');
}

function closeReportsModal() {
    document.getElementById('reportsModal').classList.add('hidden');
}
</script>
@endsection
