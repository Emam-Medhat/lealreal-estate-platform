@extends('layouts.agent')

@section('title', 'Add Client')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Add New Client</h1>
            <p class="text-muted">Add a new client to your CRM</p>
        </div>
        <a href="{{ route('agent.crm.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to CRM
        </a>
    </div>

    <!-- Create Client Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('agent.crm.store') }}">
                @csrf
                
                <div class="row g-3">
                    <!-- Personal Information -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">Personal Information</h5>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">First Name *</label>
                        <input type="text" name="first_name" class="form-control @error('first_name')" required>
                        @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Last Name *</label>
                        <input type="text" name="last_name" class="form-control @error('last_name')" required>
                        @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control @error('email')" required>
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="tel" name="phone" class="form-control @error('phone')" required>
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" class="form-control @error('company')">
                        @error('company')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" class="form-control @error('position')">
                        @error('position')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Address Information -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Address Information</h5>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control @error('address')">
                        @error('address')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control @error('city')">
                        @error('city')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">State</label>
                        <input type="text" name="state" class="form-control @error('state')">
                        @error('state')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Postal Code</label>
                        <input type="text" name="postal_code" class="form-control @error('postal_code')">
                        @error('postal_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Country</label>
                        <select name="country" class="form-select @error('country')">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="CA">Canada</option>
                            <option value="UK">United Kingdom</option>
                            <option value="AU">Australia</option>
                            <option value="Other">Other</option>
                        </select>
                        @error('country')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Preferences -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Property Preferences</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Property Type</label>
                        <select name="property_type" class="form-select @error('property_type')">
                            <option value="">Select Type</option>
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="industrial">Industrial</option>
                            <option value="land">Land</option>
                            <option value="mixed">Mixed Use</option>
                        </select>
                        @error('property_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Budget Range</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="number" name="budget_min" class="form-control @error('budget_min')" 
                                       placeholder="Min" step="10000">
                            </div>
                            <div class="col-6">
                                <input type="number" name="budget_max" class="form-control @error('budget_max')" 
                                       placeholder="Max" step="10000">
                            </div>
                        </div>
                        @error('budget_min')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('budget_max')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bedrooms</label>
                        <select name="bedrooms" class="form-select @error('bedrooms')">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                            <option value="5">5+</option>
                        </select>
                        @error('bedrooms')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Bathrooms</label>
                        <select name="bathrooms" class="form-select @error('bathrooms')">
                            <option value="">Any</option>
                            <option value="1">1+</option>
                            <option value="2">2+</option>
                            <option value="3">3+</option>
                            <option value="4">4+</option>
                        </select>
                        @error('bathrooms')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Preferred Areas</label>
                        <input type="text" name="preferred_areas" class="form-control @error('preferred_areas')" 
                               placeholder="e.g., Downtown, Suburbs">
                        @error('preferred_areas')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Additional Information -->
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3 mt-4">Additional Information</h5>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Lead Source</label>
                        <select name="lead_source" class="form-select @error('lead_source')">
                            <option value="">Select Source</option>
                            <option value="website">Website</option>
                            <option value="referral">Referral</option>
                            <option value="social_media">Social Media</option>
                            <option value="advertisement">Advertisement</option>
                            <option value="event">Event</option>
                            <option value="cold_call">Cold Call</option>
                            <option value="other">Other</option>
                        </select>
                        @error('lead_source')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select @error('priority')">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                        @error('priority')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes')" rows="4" 
                                  placeholder="Additional notes about this client..."></textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('agent.crm.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Add Client
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
