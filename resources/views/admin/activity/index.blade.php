@extends('layouts.app')

@section('title', 'Admin Activity Log')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>
                    <i class="fas fa-history me-2"></i>
                    Admin Activity Log
                </h4>
                <button class="btn btn-outline-primary" onclick="location.reload()">
                    <i class="fas fa-sync-alt me-1"></i>
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>{{ $stats['total_activities'] ?? 0 }}</h5>
                    <p class="mb-0">Total Activities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>{{ $stats['today_activities'] ?? 0 }}</h5>
                    <p class="mb-0">Today's Activities</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>{{ $stats['unique_users'] ?? 0 }}</h5>
                    <p class="mb-0">Unique Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>{{ $stats['top_actions']->count() ?? 0 }}</h5>
                    <p class="mb-0">Action Types</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Activity Log -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Recent Activities</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>
                                            <small>{{ $activity->created_at?->format('M j, H:i') ?? 'Unknown' }}</small>
                                        </td>
                                        <td>
                                            @if($activity->user)
                                                <div>
                                                    <strong>{{ $activity->user->name ?? 'Unknown' }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $activity->user->email ?? 'N/A' }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ getActionBadgeColor($activity->action ?? '') }}">
                                                {{ ucfirst(str_replace('_', ' ', $activity->action ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $activity->details ?? 'No details' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $activity->ip_address ?? 'N/A' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No activities found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($activities, 'links') && $activities->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $activities->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Actions -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Actions</h5>
                </div>
                <div class="card-body">
                    @forelse(($stats['top_actions'] ?? [])->take(10) as $action)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ ucfirst(str_replace('_', ' ', $action->action ?? '')) }}</span>
                            <span class="badge bg-primary">{{ $action->count ?? 0 }}</span>
                        </div>
                    @empty
                        <p class="text-muted">No action data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Quick Filters -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Quick Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.activity') }}">
                        <div class="mb-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">All Actions</option>
                                <option value="login">Login</option>
                                <option value="logout">Logout</option>
                                <option value="user_created">User Created</option>
                                <option value="property_approved">Property Approved</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date From</label>
                            <input type="date" name="date_from" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date To</label>
                            <input type="date" name="date_to" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@php
function getActionBadgeColor($action) {
    $colors = [
        'login' => 'success',
        'logout' => 'secondary',
        'user_created' => 'primary',
        'user_updated' => 'warning',
        'user_deleted' => 'danger',
        'property_approved' => 'success',
        'property_rejected' => 'danger',
        'property_created' => 'info',
        'viewed_property' => 'primary',
        'searched_properties' => 'info',
        'admin_action' => 'dark',
    ];
    
    return $colors[$action] ?? 'secondary';
}
@endphp
@endsection
