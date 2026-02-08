@extends('layouts.app')

@section('title', 'Enterprise Accounts')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-building text-primary me-2"></i>
            Enterprise Accounts
        </h1>
        <div class="btn-group">
            <button class="btn btn-primary" onclick="createAccount()">
                <i class="fas fa-plus me-1"></i>
                New Account
            </button>
            <button class="btn btn-success" onclick="refreshAccounts()">
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
                            <h4 class="mb-0">{{ $accounts->count() }}</h4>
                            <p class="mb-0">Total Accounts</p>
                        </div>
                        <i class="fas fa-building fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $accounts->where('status', 'active')->count() }}</h4>
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
                            <h4 class="mb-0">{{ $accounts->where('status', 'pending')->count() }}</h4>
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
                            <h4 class="mb-0">{{ $accounts->whereIn('status', ['suspended', 'banned'])->count() }}</h4>
                            <p class="mb-0">Suspended/Banned</p>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x opacity-75"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form id="account-filters">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" id="search-accounts" placeholder="Search accounts...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select class="form-control" id="filter-status" onchange="filterAccounts()">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                            <option value="banned">Banned</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-control" id="filter-type" onchange="filterAccounts()">
                            <option value="">All Types</option>
                            <option value="individual">Individual</option>
                            <option value="company">Company</option>
                            <option value="developer">Developer</option>
                            <option value="investor">Investor</option>
                        </select>
                    </div>
                    <div class="col-md-3">
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

    <!-- Accounts Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Account</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Active</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($accounts->count() > 0)
                            @foreach($accounts as $account)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ strtoupper(substr($account->company_name ?? 'Unknown', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $account->company_name ?? 'Unknown Account' }}</div>
                                                <small class="text-muted">{{ $account->contact_email ?? 'unknown@example.com' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getAccountTypeColor($account->company_type ?? 'enterprise') }}">
                                            {{ ucfirst($account->company_type ?? 'enterprise') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ getAccountStatusColor($account->status ?? 'pending') }}">
                                            {{ ucfirst($account->status ?? 'pending') }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $account->created_at ? \Carbon\Carbon::parse($account->created_at)->format('M d, Y') : 'Unknown' }}
                                    </td>
                                    <td>
                                        {{ $account->updated_at ? \Carbon\Carbon::parse($account->updated_at)->format('M d, Y') : 'Never' }}
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewAccount({{ $account->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-warning" onclick="editAccount({{ $account->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-success" onclick="toggleAccountStatus({{ $account->id }})">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                    <h4 class="text-muted">No accounts found</h4>
                                    <p class="text-muted">There are no enterprise accounts to display.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Account Details Modal -->
    <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accountModalLabel">Account Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="account-details">
                        <!-- Account details will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function createAccount() {
    window.location.href = '/enterprise/accounts/create';
}

function refreshAccounts() {
    location.reload();
}

function filterAccounts() {
    const status = document.getElementById('filter-status').value;
    const type = document.getElementById('filter-type').value;
    const search = document.getElementById('search-accounts').value;
    
    // Build URL with filters
    let url = '/enterprise/accounts';
    const params = new URLSearchParams();
    
    if (status) params.append('status', status);
    if (type) params.append('type', type);
    if (search) params.append('search', search);
    
    if (params.toString()) {
        url += '?' + params.toString();
    }
    
    window.location.href = url;
}

function clearFilters() {
    document.getElementById('search-accounts').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('filter-type').value = '';
    filterAccounts();
}

function viewAccount(id) {
    fetch(`/enterprise/accounts/${id}/view`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show details in modal
                document.getElementById('account-details').innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Account Information</h6>
                            <p><strong>Company Name:</strong> ${data.account.company_name}</p>
                            <p><strong>Contact Email:</strong> ${data.account.contact_email}</p>
                            <p><strong>Contact Person:</strong> ${data.account.contact_person}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Account Details</h6>
                            <p><strong>Status:</strong> ${data.account.status}</p>
                            <p><strong>Company Type:</strong> ${data.account.company_type}</p>
                            <p><strong>Created:</strong> ${data.account.created_at}</p>
                        </div>
                    </div>
                `;
                
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('accountModal'));
                modal.show();
            } else {
                alert('Failed to load account details');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading account details');
        });
}

function editAccount(id) {
    window.location.href = `/enterprise/accounts/${id}/edit`;
}

function toggleAccountStatus(id) {
    if (confirm('Are you sure you want to change this account status?')) {
        fetch(`/enterprise/accounts/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Account status updated successfully!');
                location.reload();
            } else {
                alert('Failed to update account status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating account status');
        });
    }
}
</script>
@endsection

@php
function getAccountTypeColor($type) {
    $colors = [
        'individual' => 'primary',
        'company' => 'success',
        'developer' => 'info',
        'investor' => 'warning',
        'admin' => 'danger',
        'super_admin' => 'dark'
    ];
    return $colors[$type] ?? 'secondary';
}

function getAccountStatusColor($status) {
    $colors = [
        'pending' => 'warning',
        'active' => 'success',
        'suspended' => 'danger',
        'banned' => 'dark'
    ];
    return $colors[$status] ?? 'secondary';
}
@endphp
