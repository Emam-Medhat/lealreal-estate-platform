@extends('layouts.app')

@section('title', 'Create Maintenance Request')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Create Maintenance Request</h1>
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Maintenance
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">New Maintenance Request</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('maintenance.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="property_id" class="form-label">Property *</label>
                                    <select class="form-select @error('property_id') is-invalid @enderror" id="property_id" name="property_id" required>
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                                {{ $property->title }} - {{ $property->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('property_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority *</label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category *</label>
                                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="plumbing" {{ old('category') == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                        <option value="electrical" {{ old('category') == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="hvac" {{ old('category') == 'hvac' ? 'selected' : '' }}>HVAC</option>
                                        <option value="structural" {{ old('category') == 'structural' ? 'selected' : '' }}>Structural</option>
                                        <option value="painting" {{ old('category') == 'painting' ? 'selected' : '' }}>Painting</option>
                                        <option value="landscaping" {{ old('category') == 'landscaping' ? 'selected' : '' }}>Landscaping</option>
                                        <option value="other" {{ old('category') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="service_provider_id" class="form-label">Service Provider</label>
                                    <select class="form-select @error('service_provider_id') is-invalid @enderror" id="service_provider_id" name="service_provider_id">
                                        <option value="">Select Service Provider (Optional)</option>
                                        @foreach($serviceProviders as $provider)
                                            <option value="{{ $provider->id }}" {{ old('service_provider_id') == $provider->id ? 'selected' : '' }}>
                                                {{ $provider->name }} - {{ $provider->specialization }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('service_provider_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maintenance_team_id" class="form-label">Maintenance Team</label>
                                    <select class="form-select @error('maintenance_team_id') is-invalid @enderror" id="maintenance_team_id" name="maintenance_team_id">
                                        <option value="">Select Maintenance Team (Optional)</option>
                                        @foreach($maintenanceTeams as $team)
                                            <option value="{{ $team->id }}" {{ old('maintenance_team_id') == $team->id ? 'selected' : '' }}>
                                                {{ $team->name }} - {{ $team->specialization }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('maintenance_team_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_cost" class="form-label">Estimated Cost</label>
                                    <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}" placeholder="0.00">
                                    @error('estimated_cost')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="Brief description of the maintenance issue" required>
                            @error('title')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Detailed description of the maintenance issue" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Any additional information or special instructions">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
