@extends('layouts.app')

@section('title', 'Activity Analytics')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Activity Analytics
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-primary">{{ $analytics['stats']['total_activities'] ?? 0 }}</h3>
                                    <p class="mb-0">Total Activities</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-success">{{ $analytics['stats']['activities_this_month'] ?? 0 }}</h3>
                                    <p class="mb-0">This Month</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-info">{{ $analytics['stats']['activities_today'] ?? 0 }}</h3>
                                    <p class="mb-0">Today</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center">
                                <div class="card-body">
                                    <h3 class="text-warning">{{ $analytics['stats']['most_active_day']->day ?? 'N/A' }}</h3>
                                    <p class="mb-0">Most Active Day</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Types Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Activity Types</h6>
                                </div>
                                <div class="card-body">
                                    @forelse($analytics['activity_types'] ?? [] as $type)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span>{{ ucfirst(str_replace('_', ' ', $type->action)) }}</span>
                                            <span class="badge bg-primary">{{ $type->count ?? 0 }}</span>
                                        </div>
                                    @empty
                                        <p class="text-muted">No activity data available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Recent Activities</h6>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        @forelse(($analytics['recent_activities'] ?? [])->take(10) as $activity)
                                            <div class="list-group-item px-0">
                                                <div class="d-flex justify-content-between">
                                                    <div>
                                                        <small class="text-muted">{{ $activity->created_at?->format('M j, Y H:i') ?? 'Unknown' }}</small>
                                                        <p class="mb-0">{{ $activity->details ?? 'No details' }}</p>
                                                    </div>
                                                    <small class="badge bg-secondary">{{ $activity->action ?? 'Unknown' }}</small>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-muted">No recent activities</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Daily Activity Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Daily Activity (Last 30 Days)</h6>
                                </div>
                                <div class="card-body">
                                    @forelse($analytics['daily_activity'] ?? [] as $activity)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span>{{ $activity->date ?? 'Unknown' }}</span>
                                            <div class="progress" style="width: 60%; height: 20px;">
                                                <div class="progress-bar" style="width: {{ min(100, ($activity->count ?? 0) * 10) }}%">
                                                    {{ $activity->count ?? 0 }}
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-muted">No daily activity data available</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
