@extends('layouts.app')

@section('title', 'Enterprise Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Enterprise Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3>{{ \App\Models\EnterpriseAccount::count() }}</h3>
                            <small>Total Enterprises</small>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ \App\Models\EnterpriseAccount::where('status', 'active')->count() }}</h3>
                            <small>Active Accounts</small>
                        </div>
                        <div class="col-md-3">
                            <h3>{{ \App\Models\Subscription::where('status', 'active')->count() }}</h3>
                            <small>Active Subscriptions</small>
                        </div>
                        <div class="col-md-3">
                            <h3>${{ number_format(\App\Models\Subscription::where('status', 'active')->sum('price'), 0) }}</h3>
                            <small>Monthly Revenue</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Enterprise Management</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <a href="{{ route('enterprise.accounts') }}" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-users"></i> Accounts
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('enterprise.subscriptions') }}" class="btn btn-outline-info btn-block mb-2">
                                <i class="fas fa-credit-card"></i> Subscriptions
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('enterprise.accounts') }}" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-building"></i> Accounts
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('enterprise.reports') }}" class="btn btn-outline-warning btn-block mb-2">
                                <i class="fas fa-chart-bar"></i> Reports
                            </a>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('enterprise.billing') }}" class="btn btn-outline-secondary btn-block mb-2">
                                <i class="fas fa-file-invoice-dollar"></i> Billing
                            </a>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-outline-danger btn-block mb-2" data-bs-toggle="modal" data-bs-target="#createEnterpriseModal">
                                <i class="fas fa-plus"></i> New Enterprise
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Enterprises -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Enterprise Accounts</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Company</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Users</th>
                                    <th>Properties</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $recentEnterprises = \App\Models\EnterpriseAccount::orderBy('created_at', 'desc')
                                        ->limit(10)
                                        ->get();
                                @endphp
                                
                                @if($recentEnterprises->count() > 0)
                                    @foreach($recentEnterprises as $enterprise)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="company-logo mr-2">
                                                        <i class="fas fa-building fa-2x text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <strong>{{ $enterprise->company_name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $enterprise->industry }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getPlanBadgeClass($enterprise->subscription_plan) }}">
                                                    {{ ucfirst($enterprise->subscription_plan) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ getStatusBadgeClass($enterprise->status) }}">
                                                    {{ ucfirst($enterprise->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $enterprise->contact_person ?? 'N/A' }}</td>
                                            <td>{{ $enterprise->contact_email ?? 'N/A' }}</td>
                                            <td>{{ $enterprise->created_at->format('M j, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary view-enterprise" data-enterprise-id="{{ $enterprise->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success manage-enterprise" data-enterprise-id="{{ $enterprise->id }}">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="fas fa-building fa-3x mb-3"></i>
                                            <p>No enterprise accounts yet</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createEnterpriseModal">
                                                Create First Enterprise Account
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Subscription Overview</h5>
                </div>
                <div class="card-body">
                    @php
                        $subscriptions = \App\Models\Subscription::with('enterprise')
                            ->where('status', 'active')
                            ->get();
                    @endphp
                    
                    <div class="mb-3">
                        <h6>Active Subscriptions by Plan</h6>
                        @foreach(['starter', 'professional', 'enterprise'] as $plan)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ ucfirst($plan) }}</span>
                                <span class="badge badge-primary">{{ $subscriptions->where('plan', $plan)->count() }}</span>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mb-3">
                        <h6>Monthly Revenue</h6>
                        <h4 class="text-success">${{ number_format($subscriptions->sum('price'), 0) }}</h4>
                    </div>
                    
                    <div class="mb-3">
                        <h6>Annual Projection</h6>
                        <h4 class="text-info">${{ number_format($subscriptions->sum('price') * 12, 0) }}</h4>
                    </div>
                </div>
            </div>

            <!-- Enterprise Health -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title">System Health</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Server Load</span>
                            <span class="badge badge-success">Normal</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Database</span>
                            <span class="badge badge-success">Healthy</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>API Response</span>
                            <span class="badge badge-success">Fast</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Storage</span>
                            <span class="badge badge-warning">75%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Enterprise Modal -->
<div class="modal fade" id="createEnterpriseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Enterprise Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createEnterpriseForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="companyName" class="form-label">Company Name</label>
                                <input type="text" class="form-control" id="companyName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="companyType" class="form-label">Company Type</label>
                                <select class="form-select" id="companyType" required>
                                    <option value="">Select Type</option>
                                    <option value="corporation">Corporation</option>
                                    <option value="llc">LLC</option>
                                    <option value="partnership">Partnership</option>
                                    <option value="sole_proprietorship">Sole Proprietorship</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="industry" class="form-label">Industry</label>
                                <select class="form-select" id="industry" required>
                                    <option value="">Select Industry</option>
                                    <option value="real_estate">Real Estate</option>
                                    <option value="property_management">Property Management</option>
                                    <option value="construction">Construction</option>
                                    <option value="investment">Investment</option>
                                    <option value="technology">Technology</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="size" class="form-label">Company Size</label>
                                <select class="form-select" id="size" required>
                                    <option value="">Select Size</option>
                                    <option value="small">Small (1-50)</option>
                                    <option value="medium">Medium (51-500)</option>
                                    <option value="large">Large (501-5000)</option>
                                    <option value="enterprise">Enterprise (5000+)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contactPerson" class="form-label">Contact Person</label>
                                <input type="text" class="form-control" id="contactPerson" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="contactEmail" class="form-label">Contact Email</label>
                                <input type="email" class="form-control" id="contactEmail" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subscriptionPlan" class="form-label">Subscription Plan</label>
                                <select class="form-select" id="subscriptionPlan" required>
                                    <option value="starter">Starter - $99/month</option>
                                    <option value="professional">Professional - $299/month</option>
                                    <option value="enterprise">Enterprise - $999/month</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod" required>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="crypto">Cryptocurrency</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createEnterprise()">Create Enterprise</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    $('.view-enterprise').on('click', function() {
        window.location.href = `/enterprise/accounts`;
    });
    
    $('.manage-enterprise').on('click', function() {
        const enterpriseId = $(this).data('enterprise-id');
        window.location.href = `/enterprise/accounts/${enterpriseId}/edit`;
    });
});

function createEnterprise() {
    const formData = {
        company_name: $('#companyName').val(),
        company_type: $('#companyType').val(),
        industry: $('#industry').val(),
        size: $('#size').val(),
        contact_person: $('#contactPerson').val(),
        contact_email: $('#contactEmail').val(),
        subscription_plan: $('#subscriptionPlan').val(),
        payment_method: $('#paymentMethod').val()
    };

    $.ajax({
        url: '/api/enterprise/create-account',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('Enterprise account created successfully!');
                $('#createEnterpriseModal').modal('hide');
                location.reload();
            } else {
                alert('Creation failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error creating enterprise account');
        }
    });
}

@php
function getPlanBadgeClass($plan) {
    switch($plan) {
        case 'starter': return 'secondary';
        case 'professional': return 'info';
        case 'enterprise': return 'warning';
        default: return 'secondary';
    }
}

function getStatusBadgeClass($status) {
    switch($status) {
        case 'active': return 'success';
        case 'pending': return 'warning';
        case 'suspended': return 'danger';
        default: return 'secondary';
    }
}
@endphp
</script>
@endsection
