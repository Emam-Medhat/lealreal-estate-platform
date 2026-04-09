@extends('admin.layouts.admin')

@section('title', 'View Enterprise Account')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-building text-blue-600 mr-3"></i>
                    {{ $account->company_name }}
                </h1>
                <p class="text-gray-600">Enterprise account details and information</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('enterprise.accounts.edit', $account->id) }}" 
                   class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit
                </a>
                <a href="{{ route('enterprise.accounts') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Account Details -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Account Details
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Status</span>
                        <span class="px-3 py-1 text-sm font-medium rounded-full 
                            @if($account->status == 'active') 
                                bg-green-100 text-green-800
                            @elseif($account->status == 'pending') 
                                bg-yellow-100 text-yellow-800
                            @else 
                                bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($account->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Company Type</span>
                        <span class="text-gray-900">{{ ucfirst($account->company_type) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Industry</span>
                        <span class="text-gray-900">{{ $account->industry }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Size</span>
                        <span class="text-gray-900">{{ $account->size }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600 font-medium">Created At</span>
                        <span class="text-gray-900">{{ $account->created_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-address-card text-green-500 mr-2"></i>
                    Contact Information
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Contact Person</span>
                        <span class="text-gray-900">{{ $account->contact_person }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Email</span>
                        <a href="mailto:{{ $account->contact_email }}" 
                           class="text-blue-600 hover:text-blue-800 transition-colors">
                            {{ $account->contact_email }}
                        </a>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">Phone</span>
                        <span class="text-gray-900">{{ $account->contact_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600 font-medium">Website</span>
                        @if($account->website)
                            <a href="{{ $account->website }}" target="_blank" 
                               class="text-blue-600 hover:text-blue-800 transition-colors">
                                {{ $account->website }}
                            </a>
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription Details -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mt-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-credit-card text-purple-500 mr-2"></i>
                Subscription Details
            </h2>
            @if($account->subscription)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Current Plan</span>
                            <span class="text-gray-900 font-semibold">{{ $account->subscription_plan }}</span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 font-medium">Amount</span>
                            <span class="text-gray-900 font-semibold">${{ number_format($account->subscription->amount ?? 0, 2) }}</span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-100">
                            <span class="text-gray-600 font-medium">Next Billing</span>
                            <span class="text-gray-900">
                                {{ $account->subscription->next_billing_at ? $account->subscription->next_billing_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 font-medium">Payment Method</span>
                            <span class="text-gray-900">{{ ucfirst($account->payment_method) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-credit-card text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No active subscription found.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
