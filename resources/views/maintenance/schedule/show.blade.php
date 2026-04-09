@extends('admin.layouts.admin')

@section('title', 'Maintenance Schedule Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Schedule Details</h1>
        <div>
            <a href="{{ route('maintenance.schedule.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
            <a href="{{ route('maintenance.calendar') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm ml-2">
                <i class="fas fa-calendar fa-sm text-white-50"></i> Calendar
            </a>
            @if($schedule->status != 'completed' && $schedule->status != 'cancelled')
                <a href="{{ route('maintenance.schedule.edit', $schedule->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm ml-2">
                    <i class="fas fa-edit fa-sm text-white-50"></i> Edit
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Schedule Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Schedule Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Schedule Number:</strong> {{ $schedule->schedule_number ?? 'N/A' }}</p>
                            <p><strong>Title:</strong> {{ $schedule->title }}</p>
                            <p><strong>Property:</strong> {{ $schedule->property->title ?? 'N/A' }}</p>
                            <p><strong>Type:</strong> 
                                <span class="badge badge-info">{{ $schedule->maintenance_type_label ?? $schedule->maintenance_type }}</span>
                            </p>
                            <p><strong>Priority:</strong> 
                                <span class="badge badge-{{ $schedule->priority_color }}">{{ $schedule->priority_label ?? $schedule->priority }}</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status:</strong> 
                                <span class="badge badge-{{ $schedule->status_color }}">{{ $schedule->status_label ?? $schedule->status }}</span>
                            </p>
                            <p><strong>Scheduled Date:</strong> {{ $schedule->scheduled_date->format('Y-m-d H:i') }}</p>
                            <p><strong>Duration:</strong> {{ $schedule->estimated_duration }} hours</p>
                            <p><strong>Estimated Cost:</strong> ${{ number_format($schedule->estimated_cost ?? 0, 2) }}</p>
                            @if($schedule->actual_cost)
                                <p><strong>Actual Cost:</strong> ${{ number_format($schedule->actual_cost, 2) }}</p>
                            @endif
                        </div>
                    </div>

                    @if($schedule->description)
                        <div class="mt-3">
                            <h6>Description</h6>
                            <p>{{ $schedule->description }}</p>
                        </div>
                    @endif

                    @if($schedule->requirements)
                        <div class="mt-3">
                            <h6>Special Requirements</h6>
                            <p>{{ $schedule->requirements }}</p>
                        </div>
                    @endif

                    @if($schedule->notes)
                        <div class="mt-3">
                            <h6>Additional Notes</h6>
                            <p>{{ $schedule->notes }}</p>
                        </div>
                    @endif

                    @if($schedule->completion_notes)
                        <div class="mt-3">
                            <h6>Completion Notes</h6>
                            <p>{{ $schedule->completion_notes }}</p>
                        </div>
                    @endif

                    @if($schedule->cancellation_reason)
                        <div class="mt-3">
                            <h6 class="text-danger">Cancellation Reason</h6>
                            <p class="text-danger">{{ $schedule->cancellation_reason }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Assigned Team -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Assigned Team</h6>
                </div>
                <div class="card-body">
                    @if($schedule->maintenanceTeam)
                        <p><strong>Team Name:</strong> {{ $schedule->maintenanceTeam->name_ar ?? $schedule->maintenanceTeam->name }}</p>
                        <p><strong>Status:</strong> {{ $schedule->maintenanceTeam->status }}</p>
                        @if($schedule->maintenanceTeam->leader)
                            <p><strong>Team Leader:</strong> {{ $schedule->maintenanceTeam->leader->name }}</p>
                        @endif
                    @else
                        <p class="text-muted">No team assigned</p>
                    @endif
                </div>
            </div>

            <!-- Service Provider -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Service Provider</h6>
                </div>
                <div class="card-body">
                    @if($schedule->serviceProvider)
                        <p><strong>Name:</strong> {{ $schedule->serviceProvider->name }}</p>
                        <p><strong>Contact:</strong> {{ $schedule->serviceProvider->contact_person ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $schedule->serviceProvider->phone ?? 'N/A' }}</p>
                    @else
                        <p class="text-muted">No service provider assigned</p>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <small class="text-muted">Created</small>
                            <p>{{ $schedule->created_at->format('Y-m-d H:i') }}</p>
                            @if($schedule->createdBy)
                                <small>by {{ $schedule->createdBy->name }}</small>
                            @endif
                        </div>

                        @if($schedule->assigned_at)
                            <div class="timeline-item">
                                <small class="text-muted">Assigned</small>
                                <p>{{ $schedule->assigned_at->format('Y-m-d H:i') }}</p>
                                @if($schedule->assignedBy)
                                    <small>by {{ $schedule->assignedBy->name }}</small>
                                @endif
                            </div>
                        @endif

                        @if($schedule->started_at)
                            <div class="timeline-item">
                                <small class="text-muted">Started</small>
                                <p>{{ $schedule->started_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif

                        @if($schedule->completed_at)
                            <div class="timeline-item">
                                <small class="text-muted">Completed</small>
                                <p>{{ $schedule->completed_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif

                        @if($schedule->cancelled_at)
                            <div class="timeline-item">
                                <small class="text-muted">Cancelled</small>
                                <p>{{ $schedule->cancelled_at->format('Y-m-d H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    @if($schedule->status != 'completed' && $schedule->status != 'cancelled')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @if($schedule->status == 'scheduled' && $schedule->scheduled_date <= now())
                        <div class="col-md-3 mb-2">
                            <form action="{{ route('maintenance.schedule.start', $schedule->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-play"></i> Start Work
                                </button>
                            </form>
                        </div>
                    @endif

                    @if($schedule->status == 'in_progress')
                        <div class="col-md-3 mb-2">
                            <form action="{{ route('maintenance.schedule.complete', $schedule->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm w-100">
                                    <i class="fas fa-check"></i> Complete
                                </button>
                            </form>
                        </div>
                    @endif

                    <div class="col-md-3 mb-2">
                        <form action="{{ route('maintenance.schedule.cancel', $schedule->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to cancel this schedule?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </form>
                    </div>

                    <div class="col-md-3 mb-2">
                        <a href="{{ route('maintenance.schedule.edit', $schedule->id) }}" class="btn btn-warning btn-sm w-100">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.timeline-item {
    border-left: 3px solid #4e73df;
    padding-left: 15px;
    margin-bottom: 20px;
    position: relative;
}

.timeline-item:before {
    content: '';
    width: 10px;
    height: 10px;
    background: #4e73df;
    border-radius: 50%;
    position: absolute;
    left: -6.5px;
    top: 0;
}
</style>
@endsection
