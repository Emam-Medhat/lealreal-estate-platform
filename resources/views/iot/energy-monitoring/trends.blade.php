@extends('layouts.app')

@section('title', 'Energy Monitoring Trends')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-chart-line text-warning me-2"></i>
            Energy Monitoring Trends
        </h1>
        <div>
            <button onclick="window.print()" class="btn btn-secondary me-2">
                <i class="fas fa-print me-1"></i>
                Print Trends
            </button>
            <a href="{{ route('iot.energy.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Consumption</h5>
                    <h2>{{ number_format($costAnalysis['total_consumption'] ?? 0, 2) }} kWh</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Cost</h5>
                    <h2>${{ number_format($costAnalysis['total_cost'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Avg Efficiency</h5>
                    <h2>{{ number_format($costAnalysis['avg_efficiency'] ?? 0, 1) }}%</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Savings</h5>
                    <h2>${{ number_format($costAnalysis['potential_savings'] ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Consumption Trends (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="consumptionTrendsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Device Efficiency Comparison</h5>
                </div>
                <div class="card-body">
                    <canvas id="deviceEfficiencyChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cost Analysis</h5>
                </div>
                <div class="card-body">
                    <canvas id="costAnalysisChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Monthly Trends Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Avg Consumption (kWh)</th>
                                    <th>Total Consumption (kWh)</th>
                                    <th>Avg Efficiency (%)</th>
                                    <th>Records Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($monthlyData as $data)
                                    <tr>
                                        <td>{{ $data->month }}</td>
                                        <td>{{ number_format($data->avg_consumption, 2) }}</td>
                                        <td>{{ number_format($data->total_consumption, 2) }}</td>
                                        <td>{{ number_format($data->avg_efficiency, 1) }}</td>
                                        <td>{{ $data->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No monthly data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Device Efficiency Data</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Device</th>
                                    <th>Avg Consumption (kWh)</th>
                                    <th>Avg Efficiency (%)</th>
                                    <th>Records Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deviceEfficiency as $data)
                                    <tr>
                                        <td>
                                            @if($data->device)
                                                {{ $data->device->brand }} {{ $data->device->model }}
                                            @else
                                                Device {{ $data->device_id }}
                                            @endif
                                        </td>
                                        <td>{{ number_format($data->avg_consumption, 2) }}</td>
                                        <td>{{ number_format($data->avg_efficiency, 1) }}</td>
                                        <td>{{ $data->count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No device efficiency data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Footer -->
    <div class="text-center mt-4 text-muted">
        <small>
            Trends report generated on {{ now()->format('F d, Y H:i:s') }} | 
            Data period: Last 30 days and monthly breakdown
        </small>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Consumption Trends Chart
    const consumptionCtx = document.getElementById('consumptionTrendsChart').getContext('2d');
    const consumptionTrendsChart = new Chart(consumptionCtx, {
        type: 'line',
        data: {
            labels: @json($consumptionTrends->pluck('date')->values()),
            datasets: [{
                label: 'Daily Consumption (kWh)',
                data: @json($consumptionTrends->pluck('consumption')->values()),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4
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

    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: @json($monthlyData->pluck('month')->values()),
            datasets: [{
                label: 'Total Consumption (kWh)',
                data: @json($monthlyData->pluck('total_consumption')->values()),
                backgroundColor: '#28a745'
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

    // Device Efficiency Chart
    const deviceCtx = document.getElementById('deviceEfficiencyChart').getContext('2d');
    const deviceChart = new Chart(deviceCtx, {
        type: 'radar',
        data: {
            labels: @json($deviceEfficiency->map(function($item) { 
                return $item->device ? $item->device->brand . ' ' . $item->device->model : 'Device ' . $item->device_id; 
            })->values()),
            datasets: [{
                label: 'Efficiency (%)',
                data: @json($deviceEfficiency->pluck('avg_efficiency')->values()),
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.2)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                r: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // Cost Analysis Chart
    const costCtx = document.getElementById('costAnalysisChart').getContext('2d');
    const costChart = new Chart(costCtx, {
        type: 'doughnut',
        data: {
            labels: ['Total Cost', 'Potential Savings'],
            datasets: [{
                data: [
                    {{ $costAnalysis['total_cost'] ?? 0 }},
                    {{ $costAnalysis['potential_savings'] ?? 0 }}
                ],
                backgroundColor: ['#dc3545', '#28a745']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
@endsection
