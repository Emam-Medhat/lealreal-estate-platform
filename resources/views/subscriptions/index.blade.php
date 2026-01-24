@extends('layouts.app')

@section('title', 'My Subscriptions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">My Subscriptions</h1>
                    <p class="text-gray-600">Manage your active subscriptions and billing</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('subscriptions.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        New Subscription
                    </a>
                    <a href="{{ route('subscriptions.plans.index') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-list mr-2"></i>
                        View Plans
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Subscriptions</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $subscriptions->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Monthly Spending</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($subscriptions->where('status', 'active')->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $subscriptions->where('payment_status', 'pending')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Saved</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($subscriptions->sum('amount'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscriptions List -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Your Subscriptions</h2>
                    
                    <!-- Filters -->
                    <div class="flex items-center space-x-3">
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="expired">Expired</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse ($subscriptions as $subscription)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-3 mr-4">
                                    <i class="fas fa-box text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $subscription->plan->name }}</h3>
                                    <p class="text-sm text-gray-600">{{ $subscription->plan->description }}</p>
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-calendar mr-1"></i>
                                            {{ $subscription->starts_at->format('M j, Y') }} - {{ $subscription->ends_at->format('M j, Y') }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            <i class="fas fa-dollar-sign mr-1"></i>
                                            ${{ number_format($subscription->amount, 2) }}/{{ $subscription->billing_cycle_unit }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full
                                    @if($subscription->status === 'active')
                                        bg-green-100 text-green-800
                                    @elseif($subscription->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @elseif($subscription->status === 'cancelled')
                                        bg-red-100 text-red-800
                                    @else
                                        bg-gray-100 text-gray-800
                                    @endif
                                ">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                                
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('subscriptions.show', $subscription) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($subscription->status === 'active')
                                        <a href="{{ route('subscriptions.upgrades.create', $subscription) }}" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                        <a href="{{ route('subscriptions.cancellations.create', $subscription) }}" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Progress Bar -->
                        @if($subscription->status === 'active')
                            <div class="mt-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Subscription Progress</span>
                                    <span>{{ round(($subscription->starts_at->diffInDays(now()) / $subscription->starts_at->diffInDays($subscription->ends_at)) * 100) }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, round(($subscription->starts_at->diffInDays(now()) / $subscription->starts_at->diffInDays($subscription->ends_at)) * 100)) }}%;"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-credit-card text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No subscriptions yet</h3>
                        <p class="text-gray-500 mb-6">Start by choosing a subscription plan that fits your needs.</p>
                        <a href="{{ route('subscriptions.plans.index') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                            <i class="fas fa-search mr-2"></i>
                            Browse Plans
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="bg-white px-4 py-3 border-t sm:px-6 mt-6">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
