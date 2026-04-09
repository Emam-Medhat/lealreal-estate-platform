@extends('layouts.app')

@section('title', 'Edit Energy Monitoring')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-bolt text-warning me-2"></i>
            Edit Energy Monitoring
        </h1>
        <a href="{{ route('iot.energy.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Dashboard
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Energy Monitoring Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('iot.energy.update', $data->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="property_id" class="form-label">Property *</label>
                                <select class="form-select" id="property_id" name="property_id" required>
                                    <option value="">Select Property</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id', $data->property_id) == $property->id ? 'selected' : '' }}>
                                            {{ $property->property ? $property->property->title : 'Property ' . $property->id }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="device_id" class="form-label">IoT Device</label>
                                <select class="form-select" id="device_id" name="device_id">
                                    <option value="">Select Device (Optional)</option>
                                    @foreach($devices as $device)
                                        <option value="{{ $device->id }}" {{ old('device_id', $data->device_id) == $device->id ? 'selected' : '' }}>
                                            {{ $device->brand }} {{ $device->model }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('device_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="consumption_kwh" class="form-label">Consumption (kWh) *</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="consumption_kwh" name="consumption_kwh" 
                                       value="{{ old('consumption_kwh', $data->consumption_kwh) }}" required>
                                @error('consumption_kwh')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="savings_amount" class="form-label">Savings Amount ($)</label>
                                <input type="number" step="0.01" min="0" class="form-control" id="savings_amount" name="savings_amount" 
                                       value="{{ old('savings_amount', $data->savings_amount) }}">
                                @error('savings_amount')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="efficiency_score" class="form-label">Efficiency Score (%)</label>
                                <input type="number" step="0.1" min="0" max="100" class="form-control" id="efficiency_score" name="efficiency_score" 
                                       value="{{ old('efficiency_score', $data->efficiency_score) }}">
                                @error('efficiency_score')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="active" {{ old('status', $data->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $data->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="maintenance" {{ old('status', $data->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="monitoring_type" class="form-label">Monitoring Type *</label>
                                <input type="text" class="form-control" id="monitoring_type" name="monitoring_type" 
                                       value="{{ old('monitoring_type', $data->monitoring_type) }}" required>
                                @error('monitoring_type')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="last_reading_at" class="form-label">Last Reading</label>
                                <input type="datetime-local" class="form-control" id="last_reading_at" name="last_reading_at" 
                                       value="{{ old('last_reading_at', $data->last_reading_at ? $data->last_reading_at->format('Y-m-d\TH:i') : '') }}">
                                @error('last_reading_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $data->notes) }}</textarea>
                                @error('notes')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('iot.energy.dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Monitoring
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Current Data</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>ID:</strong> {{ $data->id }}</p>
                        <p><strong>Created:</strong> {{ $data->created_at->format('M d, Y H:i') }}</p>
                        <p><strong>Updated:</strong> {{ $data->updated_at->format('M d, Y H:i') }}</p>
                        @if($data->property)
                            <p><strong>Property:</strong> {{ $data->property->property ? $data->property->property->title : 'Property ' . $data->property_id }}</p>
                        @endif
                        @if($data->device)
                            <p><strong>Device:</strong> {{ $data->device->brand }} {{ $data->device->model }}</p>
                        @endif
                    </small>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Help</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>Property:</strong> Select the property to monitor.</p>
                        <p><strong>IoT Device:</strong> Choose to device that will collect energy data.</p>
                        <p><strong>Consumption:</strong> Enter the current energy consumption in kWh.</p>
                        <p><strong>Savings:</strong> Amount saved through energy efficiency measures.</p>
                        <p><strong>Efficiency Score:</strong> Overall efficiency rating (0-100).</p>
                        <p><strong>Status:</strong> Current monitoring status.</p>
                        <p><strong>Monitoring Type:</strong> Type of energy being monitored.</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
