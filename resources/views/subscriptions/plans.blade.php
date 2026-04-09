@extends('layouts.app')

@section('title', 'Subscription Plans')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">Choose Your Perfect Plan</h1>
            <p class="text-xl text-gray-600 mb-8">Select the subscription plan that best fits your needs</p>
            
            <!-- Billing Toggle -->
            <div class="inline-flex items-center bg-gray-100 rounded-lg p-1">
                <button onclick="setBillingCycle('monthly')" id="monthlyBtn" class="px-4 py-2 rounded-md bg-white text-gray-900 font-medium transition-colors">
                    Monthly
                </button>
                <button onclick="setBillingCycle('yearly')" id="yearlyBtn" class="px-4 py-2 rounded-md text-gray-600 font-medium transition-colors">
                    Yearly <span class="bg-green-500 text-white text-xs px-2 py-1 rounded-full ml-1">Save 20%</span>
                </button>
            </div>
        </div>

        <!-- Plans Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
            @forelse ($plans as $plan)
                <div class="bg-white rounded-lg shadow-lg overflow-hidden {{ $plan->is_popular ? 'ring-2 ring-blue-500 transform scale-105' : '' }}">
                    @if($plan->is_popular)
                        <div class="bg-blue-500 text-white text-center py-2 text-sm font-semibold">
                            MOST POPULAR
                        </div>
                    @endif
                    
                    <div class="p-8">
                        <div class="text-center mb-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                            <div class="mb-6">
                                <span class="text-4xl font-bold text-gray-800">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-gray-600">/{{ $plan->billing_cycle_unit }}</span>
                            </div>
                            @if($plan->trial_days > 0)
                                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm inline-block mb-4">
                                    {{ $plan->trial_days }} days free trial
                                </div>
                            @endif
                        </div>

                        <!-- Features -->
                        <div class="space-y-4 mb-8">
                            @foreach ($plan->features as $feature)
                                <div class="flex items-center">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <span class="text-gray-700">{{ $feature->name }}</span>
                                    @if(isset($feature->pivot->limit) && $feature->pivot->limit > 0)
                                        <span class="text-gray-500 text-sm ml-2">({{ $feature->pivot->limit }})</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button onclick="selectPlan({{ $plan->id }})" class="w-full {{ $plan->is_popular ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-600 hover:bg-gray-700' }} text-white px-6 py-3 rounded-lg transition-colors font-medium">
                                @if($plan->is_popular)
                                    Get Started Now
                                @else
                                    Choose Plan
                                @endif
                            </button>
                            <a href="{{ route('subscriptions.plans.show', $plan) }}" class="block w-full text-center text-gray-600 hover:text-gray-800 py-2">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No plans available</h3>
                    <p class="text-gray-500">Check back later for available subscription plans.</p>
                </div>
            @endforelse
        </div>

        <!-- Features Comparison -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Compare All Features</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Features</th>
                            @foreach ($plans as $plan)
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ $plan->name }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Get all unique features -->
                        @php
                            $allFeatures = [];
                            foreach ($plans as $plan) {
                                foreach ($plan->features as $feature) {
                                    $allFeatures[$feature->id] = $feature;
                                }
                            }
                        @endphp
                        
                        @foreach ($allFeatures as $feature)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $feature->name }}
                                </td>
                                @foreach ($plans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if($plan->features->contains($feature))
                                            <i class="fas fa-check text-green-500"></i>
                                            @if(isset($feature->pivot->limit) && $feature->pivot->limit > 0)
                                                <span class="text-gray-500 ml-2">{{ $feature->pivot->limit }}</span>
                                            @endif
                                        @else
                                            <i class="fas fa-times text-red-500"></i>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Frequently Asked Questions</h2>
            
            <div class="space-y-6">
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I change my plan later?</h3>
                    <p class="text-gray-600">Yes, you can upgrade or downgrade your plan at any time. Changes will be prorated.</p>
                </div>
                
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">What happens if I exceed my plan limits?</h3>
                    <p class="text-gray-600">You'll be notified when you approach your limits. You can upgrade your plan or purchase add-ons.</p>
                </div>
                
                <div class="border-b pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Do you offer refunds?</h3>
                    <p class="text-gray-600">Yes, we offer a 30-day money-back guarantee for all new subscriptions.</p>
                </div>
                
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Can I cancel anytime?</h3>
                    <p class="text-gray-600">Yes, you can cancel your subscription at any time. Your access will continue until the end of your billing period.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentBillingCycle = 'monthly';

function setBillingCycle(cycle) {
    currentBillingCycle = cycle;
    
    // Update button styles
    const monthlyBtn = document.getElementById('monthlyBtn');
    const yearlyBtn = document.getElementById('yearlyBtn');
    
    if (cycle === 'monthly') {
        monthlyBtn.className = 'px-4 py-2 rounded-md bg-white text-gray-900 font-medium transition-colors';
        yearlyBtn.className = 'px-4 py-2 rounded-md text-gray-600 font-medium transition-colors';
    } else {
        monthlyBtn.className = 'px-4 py-2 rounded-md text-gray-600 font-medium transition-colors';
        yearlyBtn.className = 'px-4 py-2 rounded-md bg-white text-gray-900 font-medium transition-colors';
    }
    
    // Update prices (this would typically make an API call)
    updatePrices(cycle);
}

function updatePrices(cycle) {
    // Update prices based on billing cycle
    // This is a placeholder - implement actual price updates
    console.log('Updating prices for', cycle);
}

function selectPlan(planId) {
    // Redirect to subscription creation page
    window.location.href = `/subscriptions/create?plan_id=${planId}&billing_cycle=${currentBillingCycle}`;
}
</script>
@endsection
