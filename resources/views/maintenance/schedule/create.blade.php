@extends('layouts.app')

@section('title', 'Schedule Maintenance')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Schedule Maintenance</h1>
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
                    <h5 class="card-title mb-0">Create Maintenance Schedule</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('maintenance.schedule.store') }}">
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
                                    <label for="maintenance_team_id" class="form-label">Maintenance Team</label>
                                    <select class="form-select @error('maintenance_team_id') is-invalid @enderror" id="maintenance_team_id" name="maintenance_team_id">
                                        <option value="">Select Team</option>
                                        @foreach($teams as $team)
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
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Schedule Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="e.g., HVAC Maintenance Check" required>
                                    @error('title')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Maintenance Type *</label>
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="routine" {{ old('type') == 'routine' ? 'selected' : '' }}>Routine Maintenance</option>
                                        <option value="preventive" {{ old('type') == 'preventive' ? 'selected' : '' }}>Preventive Maintenance</option>
                                        <option value="corrective" {{ old('type') == 'corrective' ? 'selected' : '' }}>Corrective Maintenance</option>
                                        <option value="emergency" {{ old('type') == 'emergency' ? 'selected' : '' }}>Emergency Maintenance</option>
                                        <option value="inspection" {{ old('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">Priority *</label>
                                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                                        <option value="">Select Priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="scheduled_date" class="form-label">Scheduled Date *</label>
                                    <input type="date" class="form-control @error('scheduled_date') is-invalid @enderror" id="scheduled_date" name="scheduled_date" value="{{ old('scheduled_date') }}" required>
                                    @error('scheduled_date')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="scheduled_time" class="form-label">Scheduled Time *</label>
                                    <input type="time" class="form-control @error('scheduled_time') is-invalid @enderror" id="scheduled_time" name="scheduled_time" value="{{ old('scheduled_time', '09:00') }}" required>
                                    @error('scheduled_time')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_duration" class="form-label">Estimated Duration (hours)</label>
                                    <input type="number" step="0.5" class="form-control @error('estimated_duration') is-invalid @enderror" id="estimated_duration" name="estimated_duration" value="{{ old('estimated_duration', 2) }}" min="0.5" placeholder="e.g., 2.5">
                                    @error('estimated_duration')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="estimated_cost" class="form-label">Estimated Cost</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('estimated_cost') is-invalid @enderror" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost') }}" min="0" placeholder="0.00">
                                    </div>
                                    @error('estimated_cost')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" placeholder="Describe the maintenance work to be performed">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Special Requirements</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="3" placeholder="Any special tools, materials, or access requirements">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Any additional notes or instructions">{{ old('notes') }}</textarea>
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
                            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> Create Schedule
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
