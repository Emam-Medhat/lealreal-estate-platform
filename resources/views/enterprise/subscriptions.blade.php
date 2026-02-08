@extends('layouts.app')

@section('title', 'Enterprise Subscriptions')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-crown text-warning me-2"></i>
            Enterprise Subscriptions
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="createSubscription()">
                <i class="fas fa-plus me-1"></i>
                New Subscription
            </button>
            <button class="btn btn-success" onclick="refreshSubscriptions()">
                <i class="fas fa-sync-alt me-1"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subscriptions->count() }}</h4>
                            <p class="mb-0">Total Subscriptions</p>
                        </div>
                        <i class="fas fa-users fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subscriptions->where('status', 'active')->count() }}</h4>
                            <p class="mb-0">Active</p>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subscriptions->where('status', 'pending')->count() }}</h4>
                            <p class="mb-0">Pending</p>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $subscriptions->whereIn('status', ['expired', 'cancelled'])->count() }}</h4>
                            <p class="mb-0">Expired/Cancelled</p>
                        </div>
                        <i class="fas fa-times-circle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="subscription-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="search-subscriptions" placeholder="Search subscriptions...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="filter-status" onchange="filterSubscriptions()">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="expired">Expired</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Billing Cycle</label>
                        <select class="form-control" id="filter-cycle" onchange="filterSubscriptions()">
                            <option value="">All Cycles</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                            <option value="quarterly">Quarterly</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date Range</label>
                        <input type="date" class="form-control" id="filter-date" onchange="filterSubscriptions()">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-secondary w-100" onclick="clearFilters()">
                            <i class="fas fa-times me-1"></i>
                            Clear
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th>Billing Cycle</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($subscriptions->count() > 0)
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($subscription->user->name ?? 'Unknown', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $subscription->user->name ?? 'Unknown User' }}</div>
                                                <small class="text-muted">{{ $subscription->user->email ?? 'unknown@example.com' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-bold">{{ $subscription->plan->name ?? 'Unknown Plan' }}</div>
                                            <small class="text-muted">{{ $subscription->plan->description ?? '' }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getSubscriptionStatusColor($subscription->status ?? 'pending') }}">
                                            {{ ucfirst($subscription->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">
                                            {{ number_format($subscription->amount ?? 0, 2) }}
                                            {{ $subscription->currency ?? 'USD' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucfirst($subscription->billing_cycle ?? 'monthly') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $subscription->starts_at ? \Carbon\Carbon::parse($subscription->starts_at)->format('M d, Y') : 'Not set' }}
                                    </td>
                                    <td>
                                        {{ $subscription->ends_at ? \Carbon\Carbon::parse($subscription->ends_at)->format('M d, Y') : 'No expiry' }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewSubscription({{ $subscription->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editSubscription({{ $subscription->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="toggleSubscriptionStatus({{ $subscription->id }})">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-crown fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No subscriptions found</h4>
                                    <p class="text-muted">There are no enterprise subscriptions to display.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Details Modal -->
<div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionModalLabel">Subscription Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="subscription-details">
                    <!-- Subscription details will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Helper function to get status color
function getSubscriptionStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'active': 'success',
        'expired': 'danger',
        'cancelled': 'secondary',
        'suspended': 'dark'
    };
    return colors[status] || 'secondary';
}

function createSubscription() {
    window.location.href = '/enterprise/subscriptions/create';
}

function refreshSubscriptions() {
    alert('Refresh clicked!');
    location.reload();
}

function filterSubscriptions() {
    const status = document.getElementById('filter-status').value;
    const cycle = document.getElementById('filter-cycle').value;
    const date = document.getElementById('filter-date').value;
    
    // Implementation for filtering
    console.log('Filter subscriptions:', { status, cycle, date });
}

function clearFilters() {
    document.getElementById('search-subscriptions').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-cycle').value = '';
    document.getElementById('filter-date').value = '';
    filterSubscriptions();
}

function viewSubscription(id) {
    // Fetch subscription details via AJAX
    fetch(`/enterprise/subscriptions/${id}/view`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show details in modal
                document.getElementById('subscription-details').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>User Information</h6>
                            <p><strong>Name:</strong> ${data.subscription.user_name}</p>
                            <p><strong>Email:</strong> ${data.subscription.user_email}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Subscription Details</h6>
                            <p><strong>Plan:</strong> ${data.subscription.plan_name}</p>
                            <p><strong>Amount:</strong> $${data.subscription.amount}</p>
                            <p><strong>Status:</strong> ${data.subscription.status}</p>
                        </div>
                    </div>
                `;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('subscriptionModal'));
                modal.show();
            } else {
                alert('Failed to load subscription details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading subscription details');
        });
}

function editSubscription(id) {
    // Redirect to edit page
    window.location.href = `/enterprise/subscriptions/${id}/edit`;
}

function toggleSubscriptionStatus(id) {
    if (confirm('Are you sure you want to change this subscription status?')) {
        fetch(`/enterprise/subscriptions/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Subscription status updated successfully!');
                location.reload();
            } else {
                alert('Failed to update subscription status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating subscription status');
        });
    }
}
</script>
@endsection

@php
function getSubscriptionStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'active' => 'success',
        'expired' => 'danger',
        'cancelled' => 'secondary',
        'suspended' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}
@endphp
