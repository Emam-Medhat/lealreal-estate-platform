@extends('layouts.app')

@section('title', 'Property Recommendations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Recommended for You</h1>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    @if($recommendedProperties->count() > 0)
        <div class="row">
            @foreach($recommendedProperties as $property)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card property-card h-100">
                        @if($property->media->count() > 0)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $property->media->first()->file_path) }}" 
                                     class="card-img-top property-image" 
                                     alt="{{ $property->title }}"
                                     style="height: 200px; object-fit: cover;">
                                @if($property->featured)
                                    <span class="badge bg-warning position-absolute top-0 start-0 m-2">Featured</span>
                                @endif
                                @if($property->listing_type === 'rent')
                                    <span class="badge bg-info position-absolute top-0 end-0 m-2">For Rent</span>
                                @else
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">For Sale</span>
                                @endif
                            </div>
                        @else
                            <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height: 200px;">
                                <i class="fas fa-home fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($property->title, 50) }}</h5>
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-map-marker-alt"></i> {{ $property->location->city }}, {{ $property->location->state }}
                            </p>
                            
                            <div class="mb-2">
                                @if($property->price)
                                    <h4 class="text-primary mb-0">
                                        @if($property->listing_type === 'rent')
                                            ${{ number_format($property->price) }}/month
                                        @else
                                            ${{ number_format($property->price) }}
                                        @endif
                                    </h4>
                                @endif
                            </div>
                            
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <small class="text-muted">Beds</small>
                                    <div class="fw-bold">{{ $property->details->bedrooms ?? 'N/A' }}</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Baths</small>
                                    <div class="fw-bold">{{ $property->details->bathrooms ?? 'N/A' }}</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Area</small>
                                    <div class="fw-bold">{{ $property->details->area ?? 'N/A' }}m²</div>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <i class="fas fa-user"></i> {{ $property->agent->name }}
                                    </div>
                                    <div>
                                        <a href="{{ route('properties.show', $property->id) }}" class="btn btn-primary btn-sm">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-home fa-4x text-muted mb-3"></i>
            <h3 class="text-muted">No recommendations available</h3>
            <p class="text-muted">Start browsing properties to get personalized recommendations.</p>
            <a href="{{ route('properties.index') }}" class="btn btn-primary">
                <i class="fas fa-search"></i> Browse Properties
            </a>
        </div>
    @endif
</div>
@endsection
