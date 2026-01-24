@extends('layouts.app')

@section('title', 'Upgrade Subscription')

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
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Upgrade Your Subscription</h1>
                    <p class="text-gray-600">Choose a better plan that fits your growing needs</p>
                </div>
            </div>
        </div>

        <!-- Current Plan -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Plan</h2>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-gray-100 rounded-full p-3 mr-4">
                        <i class="fas fa-box text-gray-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">{{ $currentPlan->name }}</h3>
                        <p class="text-gray-600">{{ $currentPlan->description }}</p>
                        <div class="flex items-center space-x-4 mt-2">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-dollar-sign mr-1"></i>
                                ${{ number_format($currentPlan->price, 2) }}/{{ $currentPlan->billing_cycle_unit }}
                            </span>
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-calendar mr-1"></i>
                                Next billing: {{ $subscription->ends_at->format('M j, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        Current Plan
                    </span>
                </div>
            </div>
        </div>

        <!-- Available Plans -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Available Upgrade Options</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse ($availablePlans as $plan)
                    <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $plan->name }}</h3>
                                <p class="text-gray-600 text-sm">{{ $plan->description }}</p>
                            </div>
                            @if($plan->is_popular)
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Popular</span>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex items-baseline">
                                <span class="text-2xl font-bold text-gray-800">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-gray-600 text-sm">/{{ $plan->billing_cycle_unit }}</span>
                            </div>
                            @if($plan->price > $currentPlan->price)
                                <div class="text-sm text-green-600 mt-1">
                                    +${{ number_format($plan->price - $currentPlan->price, 2) }} more
                                </div>
                            @endif
                        </div>

                        <!-- Proration Info -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                            <div class="text-sm">
                                <p class="text-blue-800 font-medium mb-1">Proration Details:</p>
                                <div class="text-blue-600">
                                    <div>Current period remaining: {{ $subscription->ends_at->diffInDays(now()) }} days</div>
                                    <div>Prorated amount: ${{ number_format($plan->proration_amount ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Features -->
                        <div class="space-y-2 mb-6">
                            @foreach ($plan->features->take(5) as $feature)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-700">{{ $feature->name }}</span>
                                </div>
                            @endforeach
                            @if($plan->features->count() > 5)
                                <div class="text-sm text-gray-500">
                                    +{{ $plan->features->count() - 5 }} more features
                                </div>
                            @endif
                        </div>

                        <!-- Upgrade Button -->
                        <button onclick="initiateUpgrade({{ $plan->id }})" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Upgrade to {{ $plan->name }}
                        </button>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-arrow-up text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No upgrade options available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Upgrade Benefits -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Why Upgrade?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-rocket text-blue-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">More Features</h3>
                    <p class="text-sm text-gray-600">Access to advanced features and tools</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-green-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Higher Limits</h3>
                    <p class="text-sm text-gray-600">Increased storage, users, and API calls</p>
                </div>
                
                <div class="text-center">
                    <div class="bg-purple-100 rounded-full p-3 w-12 h-12 mx-auto mb-3 flex items-center justify-center">
                        <i class="fas fa-headset text-purple-600"></i>
                    </div>
                    <h3 class="font-semibold text-gray-800 mb-2">Priority Support</h3>
                    <p class="text-sm text-gray-600">Get help faster with priority support</p>
                </div>
            </div>
        </div>

        <!-- Upgrade History -->
        @if($upgradeHistory->isNotEmpty())
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Upgrade History</h2>
                
                <div class="space-y-4">
                    @foreach ($upgradeHistory as $upgrade)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="bg-blue-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-arrow-up text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800">
                                        {{ $upgrade->oldPlan->name }} → {{ $upgrade->newPlan->name }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $upgrade->created_at->format('M j, Y') }}
                                    </p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($upgrade->status === 'completed')
                                        bg-green-100 text-green-800
                                    @elseif($upgrade->status === 'pending')
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    {{ ucfirst($upgrade->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Upgrade Confirmation Modal -->
<div id="upgradeModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4 w-full">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Upgrade</h3>
        <div id="upgradeDetails" class="mb-6">
            <!-- Upgrade details will be inserted here -->
        </div>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeUpgradeModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                Cancel
            </button>
            <button onclick="confirmUpgrade()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Confirm Upgrade
            </button>
        </div>
    </div>
</div>

<script>
let selectedPlanId = null;

function initiateUpgrade(planId) {
    selectedPlanId = planId;
    
    // Fetch upgrade details
    fetch(`/subscriptions/upgrades/options?plan_id=${planId}`)
        .then(response => response.json())
        .then(data => {
            showUpgradeModal(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function showUpgradeModal(upgradeData) {
    const modal = document.getElementById('upgradeModal');
    const details = document.getElementById('upgradeDetails');
    
    details.innerHTML = `
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Current Plan:</span>
                <span class="font-medium">${upgradeData.old_plan_name}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">New Plan:</span>
                <span class="font-medium">${upgradeData.new_plan_name}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Price Difference:</span>
                <span class="font-medium text-green-600">+$${upgradeData.proration_amount}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Effective Date:</span>
                <span class="font-medium">Immediately</span>
            </div>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function closeUpgradeModal() {
    document.getElementById('upgradeModal').classList.add('hidden');
    selectedPlanId = null;
}

function confirmUpgrade() {
    if (!selectedPlanId) return;
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/subscriptions/upgrades/${selectedPlanId}`;
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = '{{ csrf_token() }}';
    form.appendChild(csrfToken);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection
