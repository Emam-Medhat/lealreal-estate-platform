@extends('layouts.agent')

@section('title', 'Schedule Appointment')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Schedule Appointment</h1>
            <p class="text-muted">Create a new appointment with a client</p>
        </div>
        <a href="{{ route('agent.appointments.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Appointments
        </a>
    </div>

    <!-- Create Appointment Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('agent.appointments.store') }}">
                @csrf
                
                <div class="row g-3">
                    <!-- Client Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Client *</label>
                        <select name="client_id" class="form-select @error('client_id')" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->full_name }}</option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Property Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Property</label>
                        <select name="property_id" class="form-select @error('property_id')">
                            <option value="">Select Property (Optional)</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}">{{ $property->title }}</option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Appointment Details -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Appointment Details</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control @error('title')" 
                               placeholder="e.g., Property Viewing" required>
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Type *</label>
                        <select name="appointment_type" class="form-select @error('appointment_type')" required>
                            <option value="">Select Type</option>
                            <option value="viewing">Property Viewing</option>
                            <option value="consultation">Consultation</option>
                            <option value="meeting">Meeting</option>
                            <option value="follow_up">Follow-up</option>
                            <option value="closing">Closing</option>
                            <option value="inspection">Inspection</option>
                            <option value="other">Other</option>
                        </select>
                        @error('appointment_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Date and Time -->
                    <div class="col-md-6">
                        <label class="form-label">Date *</label>
                        <input type="date" name="appointment_date" class="form-control @error('appointment_date')" 
                               min="{{ now()->format('Y-m-d') }}" required>
                        @error('appointment_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Start Time *</label>
                        <input type="time" name="start_time" class="form-control @error('start_time')" required>
                        @error('start_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">End Time *</label>
                        <input type="time" name="end_time" class="form-control @error('end_time')" required>
                        @error('end_time')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Location -->
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" name="location" class="form-control @error('location')" 
                               placeholder="e.g., Office, Property Address">
                        @error('location')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Meeting Type</label>
                        <select name="meeting_type" class="form-select @error('meeting_type')">
                            <option value="in_person">In Person</option>
                            <option value="video_call">Video Call</option>
                            <option value="phone_call">Phone Call</option>
                        </select>
                        @error('meeting_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select @error('priority')">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                        @error('priority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select @error('status')">
                            <option value="scheduled" selected>Scheduled</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="pending">Pending</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Reminder</label>
                        <select name="reminder" class="form-select @error('reminder')">
                            <option value="none">No Reminder</option>
                            <option value="15_minutes">15 minutes before</option>
                            <option value="30_minutes">30 minutes before</option>
                            <option value="1_hour">1 hour before</option>
                            <option value="1_day">1 day before</option>
                        </select>
                        @error('reminder')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control @error('description')" rows="4" 
                                  placeholder="Add details about this appointment..."></textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label">Internal Notes</label>
                        <textarea name="notes" class="form-control @error('notes')" rows="3" 
                                  placeholder="Internal notes for your reference..."></textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('agent.appointments.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Schedule Appointment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate end time based on duration
    const startTimeInput = document.querySelector('input[name="start_time"]');
    const endTimeInput = document.querySelector('input[name="end_time"]');
    const appointmentTypeSelect = document.querySelector('select[name="appointment_type"]');
    
    function updateEndTime() {
        if (startTimeInput.value && appointmentTypeSelect.value) {
            const startTime = new Date(`2000-01-01T${startTimeInput.value}`);
            const durations = {
                'viewing': 60,      // 1 hour
                'consultation': 45,  // 45 minutes
                'meeting': 60,       // 1 hour
                'follow_up': 30,     // 30 minutes
                'closing': 90,       // 1.5 hours
                'inspection': 120,   // 2 hours
                'other': 30          // 30 minutes
            };
            
            const duration = durations[appointmentTypeSelect.value] || 30;
            const endTime = new Date(startTime.getTime() + duration * 60000);
            endTimeInput.value = endTime.toTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
    }
    
    startTimeInput.addEventListener('change', updateEndTime);
    appointmentTypeSelect.addEventListener('change', updateEndTime);
    
    // Validate end time is after start time
    endTimeInput.addEventListener('change', function() {
        if (startTimeInput.value && this.value) {
            const start = new Date(`2000-01-01T${startTimeInput.value}`);
            const end = new Date(`2000-01-01T${this.value}`);
            
            if (end <= start) {
                this.setCustomValidity('End time must be after start time');
                this.reportValidity();
            } else {
                this.setCustomValidity('');
                this.reportValidity();
            }
        }
    });
    
    // Set minimum date to today
    const dateInput = document.querySelector('input[name="appointment_date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
});
</script>
@endpush
