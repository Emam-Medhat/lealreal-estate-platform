@extends('layouts.app')

@section('title', 'Edit Enterprise Subscription')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-warning me-2"></i>
            Edit Subscription
        </h1>
        <a href="{{ route('enterprise.subscriptions') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Subscriptions
        </a>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Subscription Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enterprise.subscriptions.update', $subscription->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- User Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">User *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $subscription->user_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('user_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="plan_id" class="form-label">Subscription Plan *</label>
                                <select class="form-select" id="plan_id" name="plan_id" required onchange="updateAmount()">
                                    <option value="">Select Plan</option>
                                    @if(isset($plans))
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" {{ $subscription->plan_id == $plan->id ? 'selected' : '' }}>
                                                {{ $plan->name }} - ${{ number_format($plan->price, 2) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('plan_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status and Dates -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" {{ $subscription->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="active" {{ $subscription->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="expired" {{ $subscription->status == 'expired' ? 'selected' : '' }}>Expired</option>
                                    <option value="cancelled" {{ $subscription->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="suspended" {{ $subscription->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="starts_at" class="form-label">Start Date *</label>
                                <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" 
                                       value="{{ $subscription->starts_at ? \Carbon\Carbon::parse($subscription->starts_at)->format('Y-m-d\TH:i') : '' }}" required>
                                @error('starts_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="ends_at" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control" id="ends_at" name="ends_at"
                                       value="{{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('Y-m-d\TH:i') : '' }}">
                                <small class="text-muted">Leave empty for no expiry</small>
                                @error('ends_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Information -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="amount" class="form-label">Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" 
                                           value="{{ $subscription->amount }}" required>
                                </div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" {{ $subscription->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $subscription->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ $subscription->currency == 'GBP' ? 'selected' : '' }}>GBP</option>
                                    <option value="SAR" {{ $subscription->currency == 'SAR' ? 'selected' : '' }}>SAR</option>
                                </select>
                                @error('currency')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                    <option value="monthly" {{ $subscription->billing_cycle == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ $subscription->billing_cycle == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="yearly" {{ $subscription->billing_cycle == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="stripe" {{ $subscription->payment_method == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                    <option value="paypal" {{ $subscription->payment_method == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bank_transfer" {{ $subscription->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ $subscription->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_method')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Status and Auto Renew -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="payment_status" class="form-label">Payment Status</label>
                                <select class="form-select" id="payment_status" name="payment_status">
                                    <option value="pending" {{ $subscription->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ $subscription->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ $subscription->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ $subscription->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="auto_renew" class="form-label">Auto Renew</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1" {{ $subscription->auto_renew ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auto_renew">
                                        Enable automatic renewal
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="activated_at" class="form-label">Activated At</label>
                                <input type="datetime-local" class="form-control" id="activated_at" name="activated_at"
                                       value="{{ $subscription->activated_at ? \Carbon\Carbon::parse($subscription->activated_at)->format('Y-m-d\TH:i') : '' }}">
                                <small class="text-muted">When subscription was activated</small>
                                @error('activated_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes...">{{ $subscription->notes ?? '' }}</textarea>
                            @error('notes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('enterprise.subscriptions') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Update Subscription
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Info -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Current Subscription Info</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subscription ID:</span>
                        <strong>#{{ $subscription->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created:</span>
                        <strong>{{ $subscription->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last Updated:</span>
                        <strong>{{ $subscription->updated_at->format('M d, Y') }}</strong>
                    </div>
                    @if($subscription->cancelled_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cancelled:</span>
                            <strong>{{ \Carbon\Carbon::parse($subscription->cancelled_at)->format('M d, Y') }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Help</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>User:</strong> Change the subscription owner.</p>
                        <p><strong>Plan:</strong> Update the subscription plan.</p>
                        <p><strong>Status:</strong> Change subscription status.</p>
                        <p><strong>Auto Renew:</strong> Enable/disable automatic renewal.</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateAmount() {
    const planSelect = document.getElementById('plan_id');
    const amountInput = document.getElementById('amount');
    const selectedOption = planSelect.options[planSelect.selectedIndex];
    
    if (selectedOption && selectedOption.dataset.price) {
        amountInput.value = selectedOption.dataset.price;
    }
}
</script>
@endsection
