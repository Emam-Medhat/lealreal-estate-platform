@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>
                        Activity Log
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>{{ $log->created_at?->format('M j, Y H:i:s') ?? 'Unknown' }}</td>
                                        <td>
                                            <span class="badge bg-{{ getActionBadgeColor($log->action ?? '') }}">
                                                {{ ucfirst(str_replace('_', ' ', $log->action ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td>{{ $log->details ?? 'No details available' }}</td>
                                        <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No activity logs found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if(method_exists($logs, 'links') && $logs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $logs->links() }}
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
@endphp
@endsection
