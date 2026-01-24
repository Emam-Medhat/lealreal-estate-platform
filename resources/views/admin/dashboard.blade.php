@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Admin Dashboard</h1>
                    <p class="text-gray-600">System overview and management</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500">Last updated: {{ now()->format('M j, Y H:i') }}</span>
                    <button onclick="refreshData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_users_today'] }} today</p>
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
                        <p class="text-sm text-green-600">+{{ $stats['new_properties_today'] }} today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-building text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Companies</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['total_companies'] }}</p>
                        <p class="text-sm text-green-600">+{{ $stats['new_companies_today'] }} today</p>
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
                        <p class="text-sm text-green-600">+${{ number_format($stats['revenue_today'], 2) }} today</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- User Growth Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">User Growth</h3>
                <canvas id="userGrowthChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Revenue Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue Trend</h3>
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Users</h3>
                <div class="space-y-3">
                    @forelse ($stats['recent_users'] as $user)
                        <div class="flex items-center">
                            <div class="bg-gray-100 rounded-full p-2 mr-3">
                                <i class="fas fa-user text-gray-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent users</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Users →
                    </a>
                </div>
            </div>
            
            <!-- Recent Properties -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Properties</h3>
                <div class="space-y-3">
                    @forelse ($stats['recent_properties'] as $property)
                        <div class="flex items-center">
                            <div class="bg-gray-100 rounded-full p-2 mr-3">
                                <i class="fas fa-home text-gray-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">{{ $property->title }}</p>
                                <p class="text-xs text-gray-500">${{ number_format($property->price, 0) }}</p>
                            </div>
                            <span class="text-xs text-gray-500">{{ $property->created_at->diffForHumans() }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500 text-sm">No recent properties</p>
                    @endforelse
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.properties.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Properties →
                    </a>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Database</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Healthy
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Storage</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            65% Used
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">API</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Online
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Queue</span>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            12 Jobs
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.users.create') }}" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors text-center">
                    <i class="fas fa-user-plus text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Add User</p>
                </a>
                
                <a href="{{ route('admin.companies.create') }}" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors text-center">
                    <i class="fas fa-building-plus text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Add Company</p>
                </a>
                
                <a href="{{ route('properties.create') }}" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition-colors text-center">
                    <i class="fas fa-home-plus text-purple-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Add Property</p>
                </a>
                
                <a href="#" onclick="showReportsModal()" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:bg-yellow-100 transition-colors text-center">
                    <i class="fas fa-chart-bar text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">View Reports</p>
                </a>
            </div>
        </div>

        <!-- Activity Log -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                <button onclick="refreshActivity()" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
            
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
    </div>
</div>

<!-- Reports Modal -->
<div id="reportsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">System Reports</h3>
        
        <div class="grid grid-cols-2 gap-4">
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">User Report</h4>
                <p class="text-sm text-gray-600 mb-3">Detailed user statistics and analytics</p>
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">Financial Report</h4>
                <p class="text-sm text-gray-600 mb-3">Revenue, payments, and transaction data</p>
                <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">Property Report</h4>
                <p class="text-sm text-gray-600 mb-3">Property listings and performance metrics</p>
                <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                    Generate Report
                </button>
            </div>
            
            <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                <h4 class="font-medium text-gray-800 mb-2">System Report</h4>
                <p class="text-sm text-gray-600 mb-3">System performance and health metrics</p>
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
// Initialize charts
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'New Users',
            data: [12, 19, 3, 5, 2, 3],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.1
        }]
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

const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Revenue',
            data: [12000, 19000, 30000, 50000, 42000, 38000],
            backgroundColor: 'rgba(34, 197, 94, 0.8)'
        }]
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

function refreshData() {
    location.reload();
}

function refreshActivity() {
    fetch('/admin/activity')
        .then(response => response.json())
        .then(data => {
            // Update activity log
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function showReportsModal() {
    document.getElementById('reportsModal').classList.remove('hidden');
}

function closeReportsModal() {
    document.getElementById('reportsModal').classList.add('hidden');
}

// Auto-refresh every 30 seconds
setInterval(refreshData, 30000);
</script>
@endsection
