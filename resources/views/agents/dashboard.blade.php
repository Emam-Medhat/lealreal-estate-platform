@extends('layouts.app')

@section('title', 'Agent Dashboard')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Agent Dashboard</h1>
        <p class="text-gray-600">Welcome back, {{ $agent->name ?? 'Agent' }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Properties</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['total_properties'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 rounded-full p-3">
                    <i class="fas fa-home text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Listings</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['active_listings'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <i class="fas fa-list text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Pending Deals</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $dashboardData['pending_deals'] ?? 0 }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Commission This Month</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($dashboardData['monthly_commission'] ?? 0, 2) }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <i class="fas fa-dollar-sign text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Recent Activities</h2>
            <div class="space-y-4">
                @forelse ($dashboardData['recent_activities'] ?? [] as $activity)
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-{{ $activity['icon'] ?? 'clipboard-list' }} text-blue-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] ?? 'Activity' }}</p>
                            <p class="text-xs text-gray-600">{{ $activity['time'] ?? 'Just now' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-inbox text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">No recent activities</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Upcoming Appointments</h2>
            <div class="space-y-4">
                @forelse ($dashboardData['upcoming_appointments'] ?? [] as $appointment)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ $appointment['client_name'] ?? 'Client' }}</p>
                                <p class="text-sm text-gray-600">{{ $appointment['property_title'] ?? 'Property Viewing' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ $appointment['time'] ?? 'Time' }}</p>
                                <p class="text-xs text-gray-600">{{ $appointment['date'] ?? 'Date' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar text-4xl text-gray-400 mb-2"></i>
                        <p class="text-gray-600">No upcoming appointments</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Performance Overview</h2>
        <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
            <div class="text-center">
                <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                <p class="text-gray-600">Performance chart will be displayed here</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Agent dashboard loaded');
    
    // Initialize dashboard components
    initializeDashboard();
});

function initializeDashboard() {
    // Placeholder for dashboard initialization
    console.log('Dashboard initialized');
}
</script>
@endpush
