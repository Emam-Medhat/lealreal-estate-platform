@extends('layouts.app')

@section('title', 'Edit Subscription')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('subscriptions.show', $subscription) }}" class="text-gray-600 hover:text-gray-800 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Subscription</h1>
                    <p class="text-gray-600">Update your subscription settings</p>
                </div>
            </div>
        </div>

        <!-- Current Plan Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Plan</h2>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $subscription->plan->name }}</h3>
                        <p class="text-gray-600">{{ $subscription->plan->description }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                ${{ number_format($subscription->amount, 2) }}/{{ $subscription->billing_cycle_unit }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $subscription->starts_at->format('M j, Y') }} - {{ $subscription->ends_at->format('M j, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <form action="{{ route('subscriptions.update', $subscription) }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            @method('PUT')
            
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Subscription Settings</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Auto Renew -->
                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="auto_renew" {{ $subscription->auto_renew ? 'checked' : '' }} class="mr-3">
                        <div>
                            <span class="font-medium text-gray-800">Auto-renew subscription</span>
                            <p class="text-sm text-gray-600">Automatically renew your subscription before it expires</p>
                        </div>
                    </label>
                </div>
                
                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="stripe" {{ $subscription->payment_method === 'stripe' ? 'selected' : '' }}>Credit Card (Stripe)</option>
                        <option value="paypal" {{ $subscription->payment_method === 'paypal' ? 'selected' : '' }}>PayPal</option>
                        <option value="bank_transfer" {{ $subscription->payment_method === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
                
                <!-- Status (for admin only) -->
                @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="active" {{ $subscription->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="pending" {{ $subscription->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ $subscription->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="expired" {{ $subscription->status === 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                @endif
            </div>

            <!-- Billing Information -->
            <div class="mt-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Billing Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Billing Address</label>
                        <textarea name="billing_address" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter billing address">{{ $subscription->billing_address ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tax Information</label>
                        <input type="text" name="tax_id" value="{{ $subscription->tax_id ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Tax ID (optional)">
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="mt-8">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Add any notes about this subscription">{{ $subscription->notes ?? '' }}</textarea>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center mt-8">
                <a href="{{ route('subscriptions.show', $subscription) }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <div class="flex space-x-3">
                    @if($subscription->status === 'active')
                        <a href="{{ route('subscriptions.upgrades.create', $subscription) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-arrow-up mr-2"></i>
                            Upgrade Plan
                        </a>
                    @endif
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('subscriptions.usage', $subscription) }}" class="block text-center bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors">
                    <i class="fas fa-chart-line text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">View Usage</p>
                </a>
                
                <a href="{{ route('subscriptions.invoices', $subscription) }}" class="block text-center bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors">
                    <i class="fas fa-file-invoice text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">View Invoices</p>
                </a>
                
                @if($subscription->status === 'active')
                    <a href="{{ route('subscriptions.cancellations.create', $subscription) }}" class="block text-center bg-red-50 border border-red-200 rounded-lg p-4 hover:bg-red-100 transition-colors">
                        <i class="fas fa-times text-red-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Cancel</p>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
