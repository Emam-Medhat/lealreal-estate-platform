@extends('layouts.agent')

@section('title', 'Create Offer')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create New Offer</h1>
            <p class="text-muted">Create a new offer for a property</p>
        </div>
        <a href="{{ route('agent.offers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Offers
        </a>
    </div>

    <!-- Create Offer Form -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('agent.offers.store') }}">
                @csrf
                
                <div class="row g-3">
                    <!-- Property Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Property *</label>
                        <select name="property_id" class="form-select @error('property_id')" required>
                            <option value="">Select Property</option>
                            @foreach($properties as $id => $title)
                                <option value="{{ $id }}">{{ $title }}</option>
                            @endforeach
                        </select>
                        @error('property_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Buyer Selection -->
                    <div class="col-md-6">
                        <label class="form-label">Buyer *</label>
                        <select name="buyer_id" class="form-select @error('buyer_id')" required>
                            <option value="">Select Buyer</option>
                            @foreach($leads as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('buyer_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Offer Price -->
                    <div class="col-md-6">
                        <label class="form-label">Offer Price *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="offer_price" class="form-control @error('offer_price')" 
                                   step="0.01" min="0" required>
                        </div>
                        @error('offer_price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Offer Type -->
                    <div class="col-md-6">
                        <label class="form-label">Offer Type *</label>
                        <select name="offer_type" class="form-select @error('offer_type')" required>
                            <option value="fixed">Fixed Price</option>
                            <option value="percentage">Percentage</option>
                        </select>
                        @error('offer_type')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Expiry Date -->
                    <div class="col-md-6">
                        <label class="form-label">Expiry Date *</label>
                        <input type="date" name="expiry_date" class="form-control @error('expiry_date')" 
                               min="{{ now()->addDay()->format('Y-m-d') }}" required>
                        @error('expiry_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Contingencies -->
                    <div class="col-md-6">
                        <label class="form-label">Contingencies</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="contingencies[]" value="financing" id="financing">
                            <label class="form-check-label" for="financing">
                                Financing Approval
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="contingencies[]" value="inspection" id="inspection">
                            <label class="form-check-label" for="inspection">
                                Property Inspection
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="contingencies[]" value="appraisal" id="appraisal">
                            <label class="form-check-label" for="appraisal">
                                Property Appraisal
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="contingencies[]" value="title" id="title">
                            <label class="form-check-label" for="title">
                                Clear Title
                            </label>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes')" rows="4" 
                                  placeholder="Additional notes about this offer..."></textarea>
                        @error('notes')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('agent.offers.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Create Offer
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
    // Update property details when property is selected
    const propertySelect = document.querySelector('select[name="property_id"]');
    const buyerSelect = document.querySelector('select[name="buyer_id"]');
    
    propertySelect.addEventListener('change', function() {
        if (this.value) {
            // You could load property details here via AJAX if needed
            console.log('Property selected:', this.value);
        }
    });
    
    buyerSelect.addEventListener('change', function() {
        if (this.value) {
            // You could load buyer details here via AJAX if needed
            console.log('Buyer selected:', this.value);
        }
    });
});
</script>
@endpush
