@extends('admin.layouts.admin')

@section('title', 'Edit Maintenance Schedule')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Maintenance Schedule</h1>
        <div>
            <a href="{{ route('maintenance.schedule.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to List
            </a>
            <a href="{{ route('maintenance.schedule.show', $schedule->id) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm ml-2">
                <i class="fas fa-eye fa-sm text-white-50"></i> View
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Edit Maintenance Schedule</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('maintenance.schedule.update', $schedule->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="property_id" class="form-label">Property *</label>
                                    <select class="form-select @error('property_id') is-invalid @enderror" id="property_id" name="property_id" required>
                                        <option value="">Select Property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}" {{ old('property_id', $schedule->property_id) == $property->id ? 'selected' : '' }}>
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
                                <div class="form-group mb-3">
                                    <label for="maintenance_team_id" class="form-label">Maintenance Team</label>
                                    <select class="form-select @error('maintenance_team_id') is-invalid @enderror" id="maintenance_team_id" name="maintenance_team_id">
                                        <option value="">Select Team</option>
                                        @foreach($teams as $team)
                                            <option value="{{ $team->id }}" {{ old('maintenance_team_id', $schedule->maintenance_team_id) == $team->id ? 'selected' : '' }}>
                                                {{ $team->name_ar }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('maintenance_team_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Schedule Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $schedule->title) }}" placeholder="e.g., HVAC Maintenance Check" required>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="maintenance_type" class="form-label">Maintenance Type *</label>
                                    <select class="form-select @error('maintenance_type') is-invalid @enderror" id="maintenance_type" name="maintenance_type" required>
                                        <option value="">Select Type</option>
                                        <option value="plumbing" {{ old('maintenance_type', $schedule->maintenance_type) == 'plumbing' ? 'selected' : '' }}>Plumbing</option>
                                        <option value="electrical" {{ old('maintenance_type', $schedule->maintenance_type) == 'electrical' ? 'selected' : '' }}>Electrical</option>
                                        <option value="hvac" {{ old('maintenance_type', $schedule->maintenance_type) == 'hvac' ? 'selected' : '' }}>HVAC</option>
                                        <option value="structural" {{ old('maintenance_type', $schedule->maintenance_type) == 'structural' ? 'selected' : '' }}>Structural</option>
                                        <option value="general" {{ old('maintenance_type', $schedule->maintenance_type) == 'general' ? 'selected' : '' }}>General</option>
                                        <option value="repair" {{ old('maintenance_type', $schedule->maintenance_type) == 'repair' ? 'selected' : '' }}>Repair</option>
                                        <option value="maintenance" {{ old('maintenance_type', $schedule->maintenance_type) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        <option value="inspection" {{ old('maintenance_type', $schedule->maintenance_type) == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                        <option value="cleaning" {{ old('maintenance_type', $schedule->maintenance_type) == 'cleaning' ? 'selected' : '' }}>Cleaning</option>
                                        <option value="service" {{ old('maintenance_type', $schedule->maintenance_type) == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="replacement" {{ old('maintenance_type', $schedule->maintenance_type) == 'replacement' ? 'selected' : '' }}>Replacement</option>
                                        <option value="testing" {{ old('maintenance_type', $schedule->maintenance_type) == 'testing' ? 'selected' : '' }}>Testing</option>
                                        <option value="other" {{ old('maintenance_type', $schedule->maintenance_type) == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('maintenance_type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="priority" class="form-label">Priority *</label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority', $schedule->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $schedule->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $schedule->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="emergency" {{ old('priority', $schedule->priority) == 'emergency' ? 'selected' : '' }}>Emergency</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="scheduled_date" class="form-label">Scheduled Date *</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date', $schedule->scheduled_date->format('Y-m-d')) }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="scheduled_time" class="form-label">Scheduled Time *</label>
                                    <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror" id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time', $schedule->scheduled_date->format('H:i')) }}" required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="estimated_duration" class="form-label">Estimated Duration (hours)</label>
                                    <input type="number" step="0.5" class="form-control @error('estimated_duration') is-invalid @enderror" id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', $schedule->estimated_duration ?? 2) }}" min="0.5" placeholder="e.g., 2.5">
                                    @error('estimated_duration')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="estimated_cost" class="form-label">Estimated Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost', $schedule->estimated_cost) }}" min="0" placeholder="0.00">
                                    </div>
                                    @error('estimated_cost')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Describe the maintenance work to be performed">{{ old('description', $schedule->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="requirements" class="form-label">Special Requirements</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="3" placeholder="Any special tools, materials, or access requirements">{{ old('requirements', $schedule->requirements) }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Any additional notes or instructions">{{ old('notes', $schedule->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Schedule Preview -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Schedule Preview</h6>
                            <div id="schedulePreview">
                                <p class="mb-1"><strong>Date:</strong> <span id="previewDate">Not selected</span></p>
                                <p class="mb-1"><strong>Time:</strong> <span id="previewTime">09:00</span></p>
                                <p class="mb-1"><strong>Duration:</strong> <span id="previewDuration">2 hours</span></p>
                                <p class="mb-0"><strong>End Time:</strong> <span id="previewEndTime">11:00</span></p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('maintenance.schedule.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Schedule
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update schedule preview
function updateSchedulePreview() {
    const date = document.getElementById('scheduled_date').value;
    const time = document.getElementById('scheduled_time').value;
    const duration = parseFloat(document.getElementById('estimated_duration').value) || 2;
    
    // Update date
    if (date) {
        const dateObj = new Date(date);
        document.getElementById('previewDate').textContent = dateObj.toLocaleDateString('en-US', { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    } else {
        document.getElementById('previewDate').textContent = 'Not selected';
    }
    
    // Update time
    document.getElementById('previewTime').textContent = time || '09:00';
    
    // Update duration
    document.getElementById('previewDuration').textContent = duration + ' hours';
    
    // Calculate end time
    if (time) {
        const [hours, minutes] = time.split(':').map(Number);
        const endHours = Math.floor(hours + duration);
        const endMinutes = minutes + ((duration % 1) * 60);
        const finalEndHours = Math.floor(endHours + (endMinutes / 60));
        const finalEndMinutes = endMinutes % 60;
        
        const endTime = `${String(finalEndHours).padStart(2, '0')}:${String(finalEndMinutes).padStart(2, '0')}`;
        document.getElementById('previewEndTime').textContent = endTime;
    } else {
        document.getElementById('previewEndTime').textContent = '11:00';
    }
}

// Add event listeners
document.getElementById('scheduled_date').addEventListener('change', updateSchedulePreview);
document.getElementById('scheduled_time').addEventListener('change', updateSchedulePreview);
document.getElementById('estimated_duration').addEventListener('input', updateSchedulePreview);

// Initialize preview on page load
updateSchedulePreview();

// Set minimum date to today
document.getElementById('scheduled_date').min = new Date().toISOString().split('T')[0];
</script>
@endsection
