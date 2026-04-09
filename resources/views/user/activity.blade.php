@extends('layouts.app')

@section('title', 'User Activity')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-clock me-2"></i>
                        My Activity
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h4 class="text-primary">{{ $activities->count() ?? 0 }}</h4>
                                    <p class="mb-0">Total Activities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h4 class="text-success">{{ $activities->where('created_at', '>=', now()->startOfDay())->count() ?? 0 }}</h4>
                                    <p class="mb-0">Today</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h4 class="text-info">{{ $activities->where('created_at', '>=', now()->startOfWeek())->count() ?? 0 }}</h4>
                                    <p class="mb-0">This Week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center bg-light">
                                <div class="card-body">
                                    <h4 class="text-warning">{{ $activities->where('created_at', '>=', now()->startOfMonth())->count() ?? 0 }}</h4>
                                    <p class="mb-0">This Month</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activities as $activity)
                                    <tr>
                                        <td>{{ $activity->created_at?->format('M j, Y') ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="badge bg-{{ getActionBadgeColor($activity->action ?? '') }}">
                                                {{ getActivityIcon($activity->action ?? '') }}
                                                {{ ucfirst(str_replace('_', ' ', $activity->action ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td>{{ $activity->details ?? 'No details available' }}</td>
                                        <td>
                                            <small class="text-muted">{{ $activity->created_at?->format('H:i:s') ?? 'Unknown' }}</small>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
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
    </div>
</div>

@php
function getActionBadgeColor($action) {
    $colors = [
        'viewed_property' => 'primary',
        'saved_property' => 'success',
        'searched_properties' => 'info',
        'updated_profile' => 'warning',
        'changed_password' => 'danger',
        'created_user' => 'success',
        'updated_user' => 'warning',
        'deleted_user' => 'danger',
        'logged_in' => 'success',
        'logged_out' => 'secondary',
    ];
    
    return $colors[$action] ?? 'secondary';
}

function getActivityIcon($action) {
    $icons = [
        'viewed_property' => '<i class="fas fa-home me-1"></i>',
        'saved_property' => '<i class="fas fa-heart me-1"></i>',
        'searched_properties' => '<i class="fas fa-search me-1"></i>',
        'updated_profile' => '<i class="fas fa-user me-1"></i>',
        'changed_password' => '<i class="fas fa-lock me-1"></i>',
        'created_user' => '<i class="fas fa-user-plus me-1"></i>',
        'updated_user' => '<i class="fas fa-user-edit me-1"></i>',
        'deleted_user' => '<i class="fas fa-user-times me-1"></i>',
        'logged_in' => '<i class="fas fa-sign-in-alt me-1"></i>',
        'logged_out' => '<i class="fas fa-sign-out-alt me-1"></i>',
    ];
    
    return $icons[$action] ?? '<i class="fas fa-circle me-1"></i>';
}
@endphp
@endsection
