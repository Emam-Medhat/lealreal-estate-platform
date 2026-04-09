@extends('layouts.app')

@section('title', 'Create Enterprise Subscription')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-plus text-success me-2"></i>
            Create New Subscription
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
                    <h5 class="mb-0">Subscription Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enterprise.subscriptions.store') }}">
                        @csrf
                        
                        <!-- User Selection -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">User *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
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
                                            <option value="{{ $plan->id }}" data-price="{{ $plan->price }}">{{ $plan->name }} - ${{ number_format($plan->price, 2) }}</option>
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
                                    <option value="pending">Pending</option>
                                    <option value="active" selected>Active</option>
                                    <option value="expired">Expired</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="suspended">Suspended</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="starts_at" class="form-label">Start Date *</label>
                                <input type="datetime-local" class="form-control" id="starts_at" name="starts_at" required>
                                @error('starts_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="ends_at" class="form-label">End Date</label>
                                <input type="datetime-local" class="form-control" id="ends_at" name="ends_at">
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
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="0" required>
                                </div>
                                @error('amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="currency" class="form-label">Currency</label>
                                <select class="form-select" id="currency" name="currency">
                                    <option value="USD" selected>USD</option>
                                    <option value="EUR">EUR</option>
                                    <option value="GBP">GBP</option>
                                    <option value="SAR">SAR</option>
                                </select>
                                @error('currency')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="billing_cycle" class="form-label">Billing Cycle *</label>
                                <select class="form-select" id="billing_cycle" name="billing_cycle" required>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="yearly">Yearly</option>
                                </select>
                                @error('billing_cycle')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method">
                                    <option value="">Select Method</option>
                                    <option value="stripe">Stripe</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="cash">Cash</option>
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
                                    <option value="pending">Pending</option>
                                    <option value="paid" selected>Paid</option>
                                    <option value="failed">Failed</option>
                                    <option value="refunded">Refunded</option>
                                </select>
                                @error('payment_status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="auto_renew" class="form-label">Auto Renew</label>
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="auto_renew" name="auto_renew" value="1" checked>
                                    <label class="form-check-label" for="auto_renew">
                                        Enable automatic renewal
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="activated_at" class="form-label">Activated At</label>
                                <input type="datetime-local" class="form-control" id="activated_at" name="activated_at">
                                <small class="text-muted">When subscription was activated</small>
                                @error('activated_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes..."></textarea>
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
                                    Create Subscription
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Stats</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Subscriptions:</span>
                        <strong>{{ App\Models\Subscription::count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Active Today:</span>
                        <strong>{{ App\Models\Subscription::where('status', 'active')->whereDate('created_at', today())->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Pending:</span>
                        <strong>{{ App\Models\Subscription::where('status', 'pending')->count() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Revenue This Month:</span>
                        <strong>${{ number_format(App\Models\Subscription::where('payment_status', 'paid')->whereMonth('created_at', now()->month)->sum('amount'), 2) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Help</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>User:</strong> Select the user who will own this subscription.</p>
                        <p><strong>Plan:</strong> Choose the subscription plan. Amount will be auto-filled.</p>
                        <p><strong>Status:</strong> Set initial status (usually 'active' for paid subscriptions).</p>
                        <p><strong>Auto Renew:</strong> Enable if subscription should renew automatically.</p>
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

// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const now = new Date();
    const localDateTime = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.getElementById('starts_at').value = localDateTime;
    document.getElementById('activated_at').value = localDateTime;
});
</script>
@endsection
