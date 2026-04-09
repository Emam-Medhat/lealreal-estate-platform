@extends('layouts.app')

@section('title', 'Properties')

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Properties</h1>
                    <p class="text-muted mb-0">Browse our comprehensive property listings</p>
                </div>
                @auth
                    <a href="{{ route('optimized.properties.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Property
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('optimized.properties.index') }}" id="searchForm">
                        <div class="row g-3">
                            <!-- Basic Search -->
                            <div class="col-md-3">
                                <label for="q" class="form-label">Search</label>
                                <input type="text" class="form-control" id="q" name="q" 
                                       value="{{ request('q') }}" placeholder="Keywords, location...">
                            </div>

                            <!-- Property Type -->
                            <div class="col-md-2">
                                <label for="property_type" class="form-label">Type</label>
                                <select class="form-select" id="property_type" name="property_type">
                                    <option value="">All Types</option>
                                    @if(isset($propertyTypes))
                                        @foreach($propertyTypes as $type)
                                            <option value="{{ $type->slug }}" {{ request('property_type') == $type->slug ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Listing Type -->
                            <div class="col-md-2">
                                <label for="listing_type" class="form-label">For</label>
                                <select class="form-select" id="listing_type" name="listing_type">
                                    <option value="">All</option>
                                    <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                                    <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i>Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted">
                        @if(isset($properties))
                            Showing {{ $properties->firstItem() }} - {{ $properties->lastItem() }} 
                            of {{ $properties->total() }} properties
                        @else
                            Loading properties...
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Grid -->
    <div id="propertiesContainer" class="row">
        @if(isset($properties) && $properties->count() > 0)
            @foreach($properties as $property)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <!-- Property Image -->
                        <div class="position-relative">
                            <div class="property-image-container" style="height: 200px; overflow: hidden;">
                                @if($property->media->first())
                                    <img src="{{ $property->media->first()->getUrlAttribute() }}" 
                                         class="card-img-top" 
                                         alt="{{ $property->title }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                         style="height: 200px;">
                                        <i class="fas fa-home fa-3x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 m-2">
                                @if($property->featured)
                                    <span class="badge bg-warning">Featured</span>
                                @endif
                                @if($property->premium)
                                    <span class="badge bg-danger">Premium</span>
                                @endif
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($property->title, 50) }}</h5>
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $property->location?->city ?? 'N/A' }}
                            </p>
                            
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-1">{{ $property->propertyType?->name ?? 'N/A' }}</span>
                                <span class="badge bg-primary">{{ ucfirst($property->listing_type) }}</span>
                            </div>

                            @if($property->price)
                                <div class="mb-2">
                                    <span class="h5 text-primary">
                                        {{ number_format($property->price->price, 0) }} {{ $property->price->currency }}
                                    </span>
                                </div>
                            @endif

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-eye me-1"></i>{{ $property->views_count ?? 0 }} views
                                    </small>
                                    <a href="{{ route('optimized.properties.show', $property) }}" class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="text-center py-8">
                    <i class="fas fa-home fa-4x text-muted mb-4"></i>
                    <h3>No properties found</h3>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <a href="{{ route('optimized.properties.index') }}" class="btn btn-primary mt-3">
                        Clear Filters
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if(isset($properties) && $properties->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $properties->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
