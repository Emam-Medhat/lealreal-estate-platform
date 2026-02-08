@extends('layouts.app')

@section('title', 'Energy Monitoring Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 mb-2">
                <i class="fas fa-bolt text-warning me-2"></i>
                Energy Monitoring Dashboard
            </h1>
            <p class="text-muted mb-0">Monitor and analyze energy consumption across your properties</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" onclick="addMonitoring()">
                <i class="fas fa-plus me-1"></i>
                Add Monitoring
            </button>
            <button class="btn btn-outline-success" onclick="generateReport()">
                <i class="fas fa-file-alt me-1"></i>
                Generate Report
            </button>
            <button class="btn btn-outline-info" onclick="viewTrends()">
                <i class="fas fa-chart-line me-1"></i>
                View Trends
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-primary text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ $stats['total_properties'] }}</h3>
                            <p class="card-text small opacity-75">Total Properties</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-building fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-success text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ $stats['active_monitoring'] }}</h3>
                            <p class="card-text small opacity-75">Active Monitoring</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-chart-line fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-warning text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">{{ number_format($stats['total_consumption'], 1) }}</h3>
                            <p class="card-text small opacity-75">Total kWh</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-bolt fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-gradient-info text-white border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">${{ number_format($stats['energy_savings'], 0) }}</h3>
                            <p class="card-text small opacity-75">Energy Savings</p>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-piggy-bank fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Data Section -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-area text-primary me-2"></i>
                            Recent Energy Data
                        </h5>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" onclick="refreshData()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="border-0">Property</th>
                                    <th class="border-0">Consumption</th>
                                    <th class="border-0">Savings</th>
                                    <th class="border-0">Efficiency</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Last Reading</th>
                                    <th class="border-0 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($recentData->count() > 0)
                                    @foreach($recentData as $data)
                                        <tr class="border-bottom">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        <i class="fas fa-home text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-medium">{{ $data->property->property ? $data->property->property->title : 'Property ' . $data->property_id }}</div>
                                                        <small class="text-muted">ID: {{ $data->id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-bolt text-warning me-1"></i>
                                                    <span class="fw-bold">{{ number_format($data->consumption_kwh, 2) }}</span>
                                                    <small class="text-muted ms-1">kWh</small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-dollar-sign text-success me-1"></i>
                                                    <span class="fw-bold text-success">{{ number_format($data->savings_amount, 2) }}</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                        <div class="progress-bar bg-{{ $data->efficiency_score >= 80 ? 'success' : ($data->efficiency_score >= 60 ? 'warning' : 'danger') }}" 
                                                             style="width: {{ $data->efficiency_score }}%">
                                                        </div>
                                                    </div>
                                                    <span class="small fw-medium">{{ number_format($data->efficiency_score, 1) }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $data->status == 'active' ? 'success' : ($data->status == 'inactive' ? 'secondary' : 'warning') }} rounded-pill">
                                                    <i class="fas fa-circle fa-xs me-1"></i>
                                                    {{ ucfirst($data->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <i class="far fa-clock me-1"></i>
                                                    {{ $data->last_reading_at ? \Carbon\Carbon::parse($data->last_reading_at)->format('M j, H:i') : 'Never' }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button class="btn btn-outline-primary btn-sm" onclick="viewDetails({{ $data->id }})" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning btn-sm" onclick="editData({{ $data->id }})" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteData({{ $data->id }})" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                                <p class="mb-0">No energy monitoring data available</p>
                                                <small>Start by adding your first energy monitoring setup</small>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tachometer-alt text-info me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="addMonitoring()">
                            <i class="fas fa-plus me-2"></i>
                            Add New Monitoring
                        </button>
                        <button class="btn btn-success" onclick="generateReport()">
                            <i class="fas fa-file-alt me-2"></i>
                            Generate Report
                        </button>
                        <button class="btn btn-info" onclick="viewTrends()">
                            <i class="fas fa-chart-line me-2"></i>
                            View Trends
                        </button>
                        <button class="btn btn-outline-secondary" onclick="refreshData()">
                            <i class="fas fa-sync-alt me-2"></i>
                            Refresh Data
                        </button>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        System Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Average Daily:</span>
                            <span class="fw-medium">{{ number_format($stats['average_daily'], 2) }} kWh</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Efficiency Score:</span>
                            <span class="fw-medium">{{ number_format($stats['efficiency_score'], 1) }}%</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Last Updated:</span>
                            <span class="fw-medium">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal" tabindex="-1" aria-labelledby="viewDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewDetailsModalLabel">
                    <i class="fas fa-bolt text-warning me-2"></i>
                    Energy Monitoring Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <!-- Content will be loaded via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editFromModalBtn">Edit</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function refreshData() {
    location.reload();
}

function exportData() {
    window.open('/iot/energy/export', '_blank');
}

function addMonitoring() {
    window.location.href = '{{ route('iot.energy.create') }}';
}

function generateReport() {
    window.open('/iot/energy/report', '_blank');
}

function viewTrends() {
    window.location.href = '/iot/energy/trends';
}

function viewDetails(id) {
    // Load energy monitoring data via AJAX
    fetch(`/iot/energy/${id}`)
        .then(response => response.text())
        .then(html => {
            // Create a temporary div to parse the HTML
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            
            // Extract the main content from the show view
            const mainContent = tempDiv.querySelector('.container-fluid');
            if (mainContent) {
                document.getElementById('modalContent').innerHTML = mainContent.innerHTML;
                
                // Set edit button to redirect to edit page
                document.getElementById('editFromModalBtn').onclick = function() {
                    window.location.href = `/iot/energy/${id}/edit`;
                };
                
                // Show the modal
                const modal = new bootstrap.Modal(document.getElementById('viewDetailsModal'));
                modal.show();
            } else {
                // Fallback: redirect to show page
                window.location.href = `/iot/energy/${id}`;
            }
        })
        .catch(error => {
            console.error('Error loading details:', error);
            // Fallback: redirect to show page
            window.location.href = `/iot/energy/${id}`;
        });
}

function editData(id) {
    window.location.href = `/iot/energy/${id}/edit`;
}

function deleteData(id) {
    if (confirm('Are you sure you want to delete this energy monitoring data?')) {
        fetch(`/iot/energy/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting data');
        });
    }
}
</script>
@endsection
