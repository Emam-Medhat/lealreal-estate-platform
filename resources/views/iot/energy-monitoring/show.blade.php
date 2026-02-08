@extends('layouts.app')

@section('title', 'Energy Monitoring Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-bolt text-warning me-2"></i>
            Energy Monitoring Details
        </h1>
        <div>
            <a href="{{ route('iot.energy.dashboard') }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Dashboard
            </a>
            <a href="{{ route('iot.energy.edit', $data->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monitoring Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Property</h6>
                            <p class="text-muted">
                                @if($data->property)
                                    {{ $data->property->property ? $data->property->property->title : 'Property ' . $data->property_id }}
                                @else
                                    Property {{ $data->property_id }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Device</h6>
                            <p class="text-muted">
                                @if($data->device)
                                    {{ $data->device->brand }} {{ $data->device->model }}
                                @else
                                    No device assigned
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-4">
                            <h6>Consumption</h6>
                            <p class="fw-bold">{{ number_format($data->consumption_kwh, 2) }} kWh</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Savings</h6>
                            <p class="fw-bold text-success">${{ number_format($data->savings_amount, 2) }}</p>
                        </div>
                        <div class="col-md-4">
                            <h6>Efficiency Score</h6>
                            <p class="fw-bold">{{ $data->efficiency_score ?? 'N/A' }}%</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Status</h6>
                            <span class="badge bg-{{ $data->status == 'active' ? 'success' : ($data->status == 'inactive' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($data->status) }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <h6>Monitoring Type</h6>
                            <p class="text-muted">{{ ucfirst($data->monitoring_type) }}</p>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6>Last Reading</h6>
                            <p class="text-muted">{{ $data->last_reading_at ? $data->last_reading_at->format('M d, Y H:i') : 'No reading' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Created</h6>
                            <p class="text-muted">{{ $data->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>

                    @if($data->notes)
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <h6>Notes</h6>
                            <p class="text-muted">{{ $data->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('iot.energy.edit', $data->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>
                            Edit Monitoring
                        </a>
                        <form method="POST" action="{{ route('iot.energy.destroy', $data->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this energy monitoring data?')">
                                <i class="fas fa-trash me-1"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Statistics</h6>
                </div>
                <div class="card-body">
                    <div class="small">
                        <p><strong>Total Properties:</strong> {{ \App\Models\EnergyMonitoring::count() }}</p>
                        <p><strong>Active Monitoring:</strong> {{ \App\Models\EnergyMonitoring::where('status', 'active')->count() }}</p>
                        <p><strong>Total Consumption:</strong> {{ number_format(\App\Models\EnergyMonitoring::sum('consumption_kwh'), 2) }} kWh</p>
                        <p><strong>Total Savings:</strong> ${{ number_format(\App\Models\EnergyMonitoring::sum('savings_amount'), 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
