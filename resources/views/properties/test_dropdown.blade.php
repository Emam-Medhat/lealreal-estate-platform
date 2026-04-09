@extends('layouts.app')

@section('title', 'Test Property Types Dropdown')

@section('content')
<div class="container py-4">
    <h1>Test Property Types Dropdown</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Property Types from Database</h5>
                    
                    <!-- Debug Information -->
                    <div class="alert alert-info">
                        <strong>Debug Info:</strong><br>
                        Total Property Types: {{ $propertyTypes->count() }}<br>
                        @if($propertyTypes->count() > 0)
                            First Type: {{ $propertyTypes->first()->name }} ({{ $propertyTypes->first()->slug }})<br>
                            Last Type: {{ $propertyTypes->last()->name }} ({{ $propertyTypes->last()->slug }})
                        @endif
                    </div>
                    
                    <!-- Original Dropdown -->
                    <div class="mb-4">
                        <label for="property_type" class="form-label">Property Type *</label>
                        <select id="property_type" name="property_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Property Type</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->slug }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Alternative Dropdown -->
                    <div class="mb-4">
                        <label for="property_type_alt" class="form-label">Alternative Property Type</label>
                        <select id="property_type_alt" name="property_type_alt" 
                                class="form-select">
                            <option value="">Select Property Type</option>
                            @foreach($propertyTypes as $type)
                                <option value="{{ $type->slug }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Debug: Show All Property Types -->
                    <div class="mt-4">
                        <h6>All Property Types:</h6>
                        <ul>
                            @foreach($propertyTypes as $type)
                                <li>{{ $type->name }} ({{ $type->slug }}) - Active: {{ $type->is_active ? 'Yes' : 'No' }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
