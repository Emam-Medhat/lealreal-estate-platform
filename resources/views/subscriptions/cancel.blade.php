@extends('layouts.app')

@section('title', 'Cancel Subscription')

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
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Cancel Subscription</h1>
                    <p class="text-gray-600">We're sorry to see you go. Let us know why you're leaving.</p>
                </div>
            </div>
        </div>

        <!-- Current Subscription Info -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Current Subscription</h2>
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
                                Current period ends: {{ $subscription->ends_at->format('M j, Y') }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        Active
                    </span>
                </div>
            </div>
        </div>

        <!-- Cancellation Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Cancellation Details</h2>
            
            <form action="{{ route('subscriptions.cancellations.store', $subscription) }}" method="POST">
                @csrf
                
                <!-- Cancellation Type -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">When would you like to cancel?</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="effective_date" value="immediate" class="mr-3" checked>
                            <div>
                                <div class="font-medium text-gray-800">Cancel Immediately</div>
                                <div class="text-sm text-gray-600">Access ends immediately with partial refund</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="effective_date" value="end_of_period" class="mr-3">
                            <div>
                                <div class="font-medium text-gray-800">Cancel at End of Period</div>
                                <div class="text-sm text-gray-600">Access continues until {{ $subscription->ends_at->format('M j, Y') }}</div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Cancellation Reason -->
                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Why are you canceling? <span class="text-red-500">*</span>
                    </label>
                    <select name="reason" id="reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Please select a reason</option>
                        <option value="too_expensive">Too expensive</option>
                        <option value="missing_features">Missing features I need</option>
                        <option value="found_alternative">Found a better alternative</option>
                        <option value="technical_issues">Technical issues</option>
                        <option value="no_longer_needed">No longer need the service</option>
                        <option value="business_closed">Business closed</option>
                        <option value="temporary_pause">Temporary pause</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Additional Feedback -->
                <div class="mb-6">
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                        Additional feedback (optional)
                    </label>
                    <textarea name="feedback" id="feedback" rows="4" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Tell us more about your experience..."></textarea>
                </div>

                <!-- Would you recommend? -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Would you recommend our service to others?
                    </label>
                    <div class="flex space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="would_recommend" value="1" class="mr-2">
                            <span class="text-gray-700">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="would_recommend" value="0" class="mr-2">
                            <span class="text-gray-700">No</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="would_recommend" value="" class="mr-2" checked>
                            <span class="text-gray-700">Prefer not to say</span>
                        </label>
                    </div>
                </div>

                <!-- Alternative Solution -->
                <div class="mb-6">
                    <label for="alternative_solution" class="block text-sm font-medium text-gray-700 mb-2">
                        What could we do to keep you as a customer?
                    </label>
                    <textarea name="alternative_solution" id="alternative_solution" rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Any suggestions for improvement..."></textarea>
                </div>

                <!-- Refund Information -->
                @if($refundInfo['refund_eligible'])
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                        <h3 class="text-green-800 font-medium mb-2">Refund Information</h3>
                        <div class="text-green-700 text-sm space-y-1">
                            <div>• You are eligible for a partial refund</div>
                            <div>• Estimated refund amount: ${{ number_format($refundInfo['refund_amount'], 2) }}</div>
                            <div>• Refund will be processed within 5-7 business days</div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <h3 class="text-yellow-800 font-medium mb-2">No Refund Available</h3>
                        <div class="text-yellow-700 text-sm">
                            Based on your usage period, no refund is available. You will continue to have access until the end of your current billing period.
                        </div>
                    </div>
                @endif

                <!-- Important Notice -->
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h3 class="text-red-800 font-medium mb-2">Important Notice</h3>
                    <div class="text-red-700 text-sm space-y-1">
                        <div>• All data will be preserved for 30 days after cancellation</div>
                        <div>• You can reactivate your subscription within this period</div>
                        <div>• After 30 days, all data will be permanently deleted</div>
                        <div>• You can export your data before cancellation</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('subscriptions.show', $subscription) }}" class="text-gray-600 hover:text-gray-800">
                        Keep My Subscription
                    </a>
                    <div class="flex space-x-3">
                        <button type="button" onclick="exportData()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                            <i class="fas fa-download mr-2"></i>
                            Export Data
                        </button>
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Cancel Subscription
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Alternative Plans -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Consider These Alternatives</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Downgrade Option -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Downgrade to Basic</h3>
                    <p class="text-gray-600 text-sm mb-3">Keep essential features at a lower cost</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">$9.99/month</span>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View Plan
                        </button>
                    </div>
                </div>
                
                <!-- Pause Subscription -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Pause Subscription</h3>
                    <p class="text-gray-600 text-sm mb-3">Take a break without losing your data</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-800">Free</span>
                        <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Learn More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportData() {
    // Implement data export functionality
    window.location.href = '/subscriptions/export-data';
}

// Handle form submission with confirmation
document.querySelector('form').addEventListener('submit', function(e) {
    const reason = document.getElementById('reason').value;
    
    if (!reason) {
        e.preventDefault();
        alert('Please select a reason for cancellation.');
        return;
    }
    
    if (!confirm('Are you sure you want to cancel your subscription? This action cannot be undone.')) {
        e.preventDefault();
        return;
    }
});
</script>
@endsection
