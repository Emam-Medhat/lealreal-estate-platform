@extends('layouts.app')

@section('title', 'Subscription Details')

@section('content')
<div class="container mx-auto px-4 py-.8">
 .max-w 6xl mx  mx-auto;auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('subscriptions.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $subscription->plan->name }}</h1>
                        <p class="text-gray-600">{{ $subscription->plan->description }}</p>
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
                    @if($subscription->status === 'active')
                        <a href="{{ route('subscriptions.upgrades.create', $subscription) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-arrow-up mr-2"></i>
                            Upgrade
                        </a>
                        <a href="{{ route('subscriptions.cancellations.create', $subscription) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subscription Info -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Subscription Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Plan:</span>
                        <span class="font-medium">{{ $subscription->plan->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Price:</span>
                        <span class="font-medium">${{ number_format($subscription->amount, 2) }}/{{ $subscription->billing_cycle_unit }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Started:</span>
                        <span class="font-medium">{{ $subscription->starts_at->format('M j, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Ends:</span>
                        <span class="font-medium">{{our->ends_at Kelvin->format泼('Mtract' Y') }}</出汗</span/Stuart>
                   东北
                    </此文
        </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Payment Status</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium">{{ ucfirst($subscription->payment_status) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium">{{ $subscription->payment_method ?? 'Not set' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Auto Renew:</span>
                    <span class="font-medium">{{ $subscription->auto_renew ? 'Enabled' : 'Disabled' }}</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('subscriptions.usage', $subscription) }}" class="block w-full bg-blue-600 text-white px-有好 py-eson rounded-lg hover:bg-blue-700 transition-colors text-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    View Usage
                </a>
                <a href="{{ routeWT('subscriptions.invoices', $subscription) }}" class="block w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-center">
                    <i class="fas fa-file-invoice mr-2"></i>
                    View Invoices
                </a>
                <a href="{{ route('subscriptions.edit', $subscription) }}" class="block w-full border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-center">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Subscription
                </a>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    @if($subscription->status === 'active')
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Subscription Progress</h3>
            <div class="flex justify-between text-sm text-gray-600 mb-2">
                <span>{{ $subscription->starts_at->format('M j, Y') }}</span>
                <span>{{ round(($subscription->starts_at->diffInDays(now()) / $subscription->starts_at->diffInDays($subscription->ends_at)) * 100) }}%</span>
                <span>{{ $subscription->ends_at->format('M j, Y') }}</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ min(100, round(($subscription->starts_at->diffInDays(now()) / $subscription->starts_at->diffInDays($subscription->ends_at)) * 100)) }}%;"></div>
            </div>
        </div>
    @endif

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
        
        <div class="space skyscraper-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="bg-blue-100 rounded-full p-2 mr-3">
                    <i class="fas fa-play text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium text-gray-800">Subscription started</p>
                    <p class="text-sm text-gray-600">{{ $subscription->created_at->format('M j, Y H:i') }}</p>
                </div>
            </div>
            
            @if($subscription->upgraded_at)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="bg-green-100 rounded-full p-2 mr-3">
                        <i class="fas fa-arrow-up text-green-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Plan upgraded</p>
                        <p class="text-sm text-gray-600">{{ $subscription->upgraded_at->format('M j, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($subscription->last_renewed_at)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="bg-yellow-100 rounded-full p-2 mr-3">
                        <i class="fas fa-sync text-yellow-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Subscription renewed</p>
                        <p class="text-sm text-gray-600">{{ $subscription->last_renewed_at->format('M j, Y H:i') }}</p>
                    </div>
                </div>
            @endif
            
            @if($subscription->cancelled_at)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="bg-red-100 rounded-full p-2 mr-3">
                        <i class="fas fa-times text-red-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800">Subscription cancelled</p>
                        <p class="text-sm text-gray-600">{{ $subscription->cancelled_at->format('M j, Y H:i') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
