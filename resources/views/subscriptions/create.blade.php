@extends('layouts.app')

@section('title', 'Create Subscription')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('subscriptions.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Subscription</h1>
                    <p class="text-gray-600">Choose a plan and start your subscription</p>
                </div>
            </div>
        </div>

        <!-- Plan Selection -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Available Plans</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse ($plans as $plan)
                    <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow cursor-pointer" onclick="selectPlan({{ $plan->id }})">
                        @if($plan->is_popular)
                            <div class="bg-blue-500 text-white text-center py-2 text-sm font-semibold rounded-t-lg -mt-6 -mx-6 mb-4">
                                MOST POPULAR
                            </div>
                        @endif
                        
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $plan->name }}</h3>
                            <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                            <div class="mb-4">
                                <span class="text-3xl font-bold text-gray-800">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-gray-600">/{{ $plan->billing_cycle_unit }}</span>
                            </div>
                            @if($plan->trial_days > 0)
                                <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm inline-block">
                                    {{ $plan->trial_days }} days free trial
                                </div>
                            @endif
                        </div>

                        <!-- Features -->
                        <div class="space-y-2 mb-6">
                            @foreach ($plan->features->take(4) as $feature)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-check text-green-500 mr-2"></i>
                                    <span class="text-gray-700">{{ $feature->name }}</span>
                                </div>
                            @endforeach
                        </div>

                        <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            Select Plan
                        </button>
                    </div>
                @empty
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-box text-4xl text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No plans available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Subscription Form -->
        <form id="subscriptionForm" action="{{ route('subscriptions.store') }}" method="POST" class="bg-white rounded-lg shadow-sm p-6">
            @csrf
            
            <input type="hidden" name="plan_id" id="selectedPlanId" required>
            
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Subscription Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Method -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Payment Method</option>
                        <option value="stripe">Credit Card (Stripe)</option>
                        <option value="paypal">PayPal</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
                
                <!-- Auto Renew -->
                <div class="flex items-center">
                    <input type="checkbox" name="auto_renew" id="auto_renew" class="mr-2" checked>
                    <label for="auto_renew" class="text-sm text-gray-700">
                        Auto-renew subscription
                    </label>
                </div>
            </div>

            <!-- Selected Plan Summary -->
            <div id="planSummary" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <h3 class="text-blue-800 font-medium mb-2">Selected Plan Summary</h3>
                <div id="planDetails" class="text-blue-700 text-sm">
                    <!-- Plan details will be inserted here -->
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="terms" class="mr-2" required>
                    <span class="text-sm text-gray-700">
                        I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms and Conditions</a> 
                        and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                    </span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <a href="{{ route('subscriptions.index') }}" class="text-gray-600 hover:text-gray-800">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors" disabled id="submitBtn">
                    Create Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectPlan(planId) {
    // Fetch plan details
    fetch('/subscriptions/plans/' + planId)
        .then(response => response.json())
        .then(data => {
            updatePlanSelection(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updatePlanSelection(plan) {
    const selectedPlanId = document.getElementById('selectedPlanId');
    const planSummary = document.getElementById('planSummary');
    const planDetails = document.getElementById('planDetails');
    const submitBtn = document.getElementById('submitBtn');
    
    selectedPlanId.value = plan.id;
    
    // Update plan summary
    planDetails.innerHTML = `
        <div class="space-y-1">
            <div><strong>Plan:</strong> ${plan.name}</div>
            <div><strong>Price:</strong> $${plan.price}/${plan.billing_cycle_unit}</div>
            <div><strong>Billing Cycle:</strong> ${plan.billing_cycle} ${plan.billing_cycle_unit}</div>
            ${plan.trial_days > 0 ? `<div><strong>Free Trial:</strong> ${plan.trial_days} days</div>` : ''}
        </div>
    `;
    
    planSummary.classList.remove('hidden');
    submitBtn.disabled = false;
    
    // Highlight selected plan
    document.querySelectorAll('.border').forEach(el => {
        el.classList.remove('border-blue-500', 'bg-blue-50');
    });
    event.currentTarget.classList.add('border-blue-500', 'bg-blue-50');
}

// Form validation
document.getElementById('subscriptionForm').addEventListener('submit', function(e) {
    const planId = document.getElementById('selectedPlanId').value;
    
    if (!planId) {
        e.preventDefault();
        alert('Please select a plan');
        return;
    }
    
    const terms = document.querySelector('input[name="terms"]').checked;
    if (!terms) {
        e.preventDefault();
        alert('Please agree to the terms and conditions');
        return;
    }
});
</script>
@endsection
