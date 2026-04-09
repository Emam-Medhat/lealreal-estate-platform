@extends('layouts.app')

@section('title', 'Energy Monitoring Report')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-bolt text-warning me-2"></i>
            Energy Monitoring Report
        </h1>
        <div>
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print me-1"></i>
                Print Report
            </button>
            <a href="{{ route('iot.energy.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Properties</h5>
                    <h2>{{ $data->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Consumption</h5>
                    <h2>{{ number_format($data->sum('consumption_kwh'), 2) }} kWh</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Savings</h5>
                    <h2>${{ number_format($data->sum('savings_amount'), 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg Efficiency</h5>
                    <h2>{{ number_format($data->avg('efficiency_score'), 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Detailed Energy Monitoring Data</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Property</th>
                            <th>Device</th>
                            <th>Consumption (kWh)</th>
                            <th>Savings ($)</th>
                            <th>Efficiency (%)</th>
                            <th>Status</th>
                            <th>Type</th>
                            <th>Last Reading</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $item)
                            <tr>
                                <td>{{ $item->id }}</td>
                                <td>
                                    @if($item->property)
                                        {{ $item->property->property ? $item->property->property->title : 'Property ' . $item->property_id }}
                                    @else
                                        Property {{ $item->property_id }}
                                    @endif
                                </td>
                                <td>
                                    @if($item->device)
                                        {{ $item->device->brand }} {{ $item->device->model }}
                                    @else
                                        No device
                                    @endif
                                </td>
                                <td>{{ number_format($item->consumption_kwh, 2) }}</td>
                                <td>${{ number_format($item->savings_amount, 2) }}</td>
                                <td>{{ $item->efficiency_score ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status == 'active' ? 'success' : ($item->status == 'inactive' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ ucfirst($item->monitoring_type) }}</td>
                                <td>{{ $item->last_reading_at ? $item->last_reading_at->format('M d, Y H:i') : 'N/A' }}</td>
                                <td>{{ $item->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No energy monitoring data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Consumption by Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="consumptionChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Efficiency Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="efficiencyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Footer -->
    <div class="text-center mt-4 text-muted">
        <small>
            Report generated on {{ now()->format('F d, Y H:i:s') }} | 
            Total records: {{ $data->count() }} | 
            Period: {{ $data->min('created_at')->format('M d, Y') }} to {{ $data->max('created_at')->format('M d, Y') }}
        </small>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Consumption by Status Chart
    const consumptionCtx = document.getElementById('consumptionChart').getContext('2d');
    const consumptionChart = new Chart(consumptionCtx, {
        type: 'bar',
        data: {
            labels: ['Active', 'Inactive', 'Maintenance'],
            datasets: [{
                label: 'Consumption (kWh)',
                data: [
                    @php
                        $activeConsumption = $data->where('status', 'active')->sum('consumption_kwh');
                        $inactiveConsumption = $data->where('status', 'inactive')->sum('consumption_kwh');
                        $maintenanceConsumption = $data->where('status', 'maintenance')->sum('consumption_kwh');
                    @endphp
                    {{ $activeConsumption }},
                    {{ $inactiveConsumption }},
                    {{ $maintenanceConsumption }}
                ],
                backgroundColor: ['#28a745', '#6c757d', '#ffc107']
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Efficiency Distribution Chart
    const efficiencyCtx = document.getElementById('efficiencyChart').getContext('2d');
    const efficiencyChart = new Chart(efficiencyCtx, {
        type: 'pie',
        data: {
            labels: ['High (>80%)', 'Medium (50-80%)', 'Low (<50%)'],
            datasets: [{
                data: [
                    @php
                        $highEfficiency = $data->where('efficiency_score', '>', 80)->count();
                        $mediumEfficiency = $data->whereBetween('efficiency_score', [50, 80])->count();
                        $lowEfficiency = $data->where('efficiency_score', '<', 50)->count();
                    @endphp
                    {{ $highEfficiency }},
                    {{ $mediumEfficiency }},
                    {{ $lowEfficiency }}
                ],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection
