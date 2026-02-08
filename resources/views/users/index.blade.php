@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Users Management</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Add User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Search and Filters -->
                    <form method="GET" action="{{ route('users.index') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="user_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="agent" {{ request('user_type') == 'agent' ? 'selected' : '' }}>Agent</option>
                                    <option value="investor" {{ request('user_type') == 'investor' ? 'selected' : '' }}>Investor</option>
                                    <option value="buyer" {{ request('user_type') == 'buyer' ? 'selected' : '' }}>Buyer</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="account_status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('account_status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('account_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ request('account_status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info">Filter</button>
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">Clear</a>
                            </div>
                        </div>
                    </form>

                    <!-- User Stats -->
                    @if(isset($userStats))
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Users</span>
                                    <span class="info-box-number">{{ $userStats['total_users'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-user-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Active Users</span>
                                    <span class="info-box-number">{{ $userStats['active_users'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-user-clock"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">New This Month</span>
                                    <span class="info-box-number">{{ $userStats['new_this_month'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-user-times"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Inactive Users</span>
                                    <span class="info-box-number">{{ $userStats['inactive_users'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>KYC Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}" class="img-circle mr-2" style="width: 30px; height: 30px;">
                                            @else
                                                <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $user->full_name }}" class="img-circle mr-2" style="width: 30px; height: 30px;">
                                            @endif
                                            {{ $user->full_name }}
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge badge-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'agent' ? 'info' : 'primary') }}">
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->account_status == 'active' ? 'success' : ($user->account_status == 'inactive' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($user->account_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $user->kyc_status == 'verified' ? 'success' : ($user->kyc_status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($user->kyc_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">No users found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
