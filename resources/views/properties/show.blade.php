@extends('layouts.app')

@section('title', $property->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Custom styles for this page -->
    <style>
        :root {
            --brand: #0d6efd;
            --brand-600: #0b5ed7;
            --muted: #6c757d;
            --card-hover-shadow: 0 0.75rem 1.5rem rgba(13, 110, 253, 0.08);
        }
        /* cards */
        .card-hover {
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .card-hover:hover { transform: translateY(-6px); box-shadow: var(--card-hover-shadow); }
        /* thumbnails */
        .thumbnail-image {
            width: 78px;
            height: 60px;
            object-fit: cover;
            cursor: pointer;
            border-radius: .5rem;
            border: 2px solid transparent;
            transition: transform .15s ease, border-color .15s ease;
        }
        .thumbnail-image.active {
            border-color: var(--brand);
            transform: translateY(-3px);
        }
        /* sticky sidebar */
        .sticky-sidebar {
            position: sticky;
            top: 135px;
        }
        /* stats */
        .stat-item .stat-value { font-weight: 700; font-size: 1.1rem; }
        .stat-item .stat-label { color: var(--muted); font-size: .85rem; }
        /* toast container positioning */
        #toastContainer { position: fixed; top: 1rem; right: 1rem; z-index: 1200; }
        /* map placeholder */
        .map-placeholder { background: linear-gradient(90deg,#f8f9fa,#eef2ff); display:flex; align-items:center; justify-content:center; color:var(--muted); height:100%; min-height:160px; border-radius:.5rem; }
        
        /* responsive adjustments */
        @media (max-width: 991px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
            }
        }
    </style>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-2">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('optimized.properties.index') }}">Properties</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $property->title }}</li>
                </ol>
            </nav>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start gap-3">
                <div>
                    <h1 class="h3 mb-1">{{ $property->title }}</h1>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $property->location?->full_address ?? $property->address }}
                    </p>
                    <div class="d-flex flex-wrap gap-2 mb-2">
                        <span class="badge bg-light text-dark">{{ $property->propertyType?->name ?? 'N/A' }}</span>
                        <span class="badge bg-primary text-white">{{ ucfirst($property->listing_type) }}</span>
                        @if($property->featured)
                            <span class="badge bg-warning text-dark"><i class="fas fa-star me-1"></i>Featured</span>
                        @endif
                        @if($property->premium)
                            <span class="badge" style="background:#6f42c1;color:#fff;"><i class="fas fa-crown me-1"></i>Premium</span>
                        @endif
                    </div>
                </div>

                <div class="text-md-end">
                    <h2 class="text-primary mb-0">
                        {{ $property->pricing?->formatted_price ?? number_format($property->price ?? 0, 2) . ' ' . ($property->currency ?? 'USD') }}
                    </h2>
                    @if($property->pricing && $property->pricing->is_negotiable)
                        <small class="text-muted">Negotiable</small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="row gx-4">
        <!-- Left: Gallery + Details -->
        <div class="col-lg-8">
            <div class="card mb-3 border-0 card-hover">
                <div class="card-body p-0">
                    <!-- Carousel -->
                    @php
                        $images = $property->media->where('media_type', 'image')->pluck('url')->toArray();
                    @endphp

                    @if(count($images) > 0)
                        <div id="propertyCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner" style="height:500px;">
                                @foreach($images as $idx => $img)
                                    <div class="carousel-item {{ $idx === 0 ? 'active' : '' }}" style="height:100%;">
                                        <img src="{{ $img }}" class="d-block w-100 h-100" style="object-fit:cover;" alt="{{ $property->title }} image {{ $idx+1 }}" loading="lazy">
                                    </div>
                                @endforeach
                            </div>

                            @if(count($images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnails -->
                        @if(count($images) > 1)
                            <div class="p-3">
                                <div class="d-flex gap-2 overflow-auto">
                                    @foreach($images as $idx => $img)
                                        <img src="{{ $img }}" class="thumbnail-image {{ $idx === 0 ? 'active' : '' }}" data-bs-target="#propertyCarousel" data-bs-slide-to="{{ $idx }}" alt="Thumbnail {{ $idx+1 }}" loading="lazy">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="d-flex align-items-center justify-content-center" style="height:500px; background:#f8f9fa;">
                            <i class="fas fa-home fa-4x text-muted"></i>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div class="card mb-3 shadow-sm card-hover">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-file-alt text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Property Description</h5>
                            <small class="text-muted">Detailed information about this property</small>
                        </div>
                    </div>

                    <div class="property-description text-secondary" style="line-height:1.8;">
                        {!! nl2br(e($property->description ?? 'No description available')) !!}
                    </div>
                </div>
            </div>

            <!-- Two-column details -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="card shadow-sm card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-info-circle text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Basic Information</h6>
                                    <small class="text-muted">Property details & specifications</small>
                                </div>
                            </div>

                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="text-muted"><i class="fas fa-hashtag me-2"></i>Property Code</div>
                                    <div class="fw-bold">{{ $property->property_code ?? 'N/A' }}</div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="text-muted"><i class="fas fa-building me-2"></i>Type</div>
                                    <div><span class="badge bg-primary">{{ ucfirst($property->property_type ?? 'N/A') }}</span></div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="text-muted"><i class="fas fa-list me-2"></i>Listing Type</div>
                                    <div><span class="badge bg-success">{{ ucfirst($property->listing_type) }}</span></div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="text-muted"><i class="fas fa-toggle-on me-2"></i>Status</div>
                                    <div><span class="badge bg-info">{{ ucfirst($property->status ?? 'draft') }}</span></div>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                                    <div class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Price</div>
                                    <div class="fw-bold text-primary">{{ number_format($property->price ?? 0, 2) }} {{ $property->currency ?? 'USD' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="col-md-6">
                    <div class="card shadow-sm card-hover h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-home text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Property Features</h6>
                                    <small class="text-muted">Specifications & amenities</small>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-bed text-primary fa-2x mb-2"></i>
                                        <div class="fw-bold">{{ $property->bedrooms ?? 0 }}</div>
                                        <div class="text-muted small">Bedrooms</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-bath text-info fa-2x mb-2"></i>
                                        <div class="fw-bold">{{ $property->bathrooms ?? 0 }}</div>
                                        <div class="text-muted small">Bathrooms</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-ruler-combined text-success fa-2x mb-2"></i>
                                        <div class="fw-bold">{{ number_format($property->area ?? 0, 2) }}</div>
                                        <div class="text-muted small">{{ $property->area_unit ?? 'm²' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-light rounded">
                                        <i class="fas fa-th text-warning fa-2x mb-2"></i>
                                        <div class="fw-bold">{{ number_format($property->land_area ?? 0, 2) }}</div>
                                        <div class="text-muted small">{{ $property->land_area_unit ?? 'm²' }}</div>
                                    </div>
                                </div>
                            </div>

                            @if($property->featured || $property->premium)
                                <div class="mt-3">
                                    @if($property->featured)
                                        <span class="badge bg-warning text-dark me-2"><i class="fas fa-star me-1"></i>Featured</span>
                                    @endif
                                    @if($property->premium)
                                        <span class="badge" style="background:#6f42c1;color:#fff;"><i class="fas fa-crown me-1"></i>Premium</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location + Map -->
            <div class="card mt-3 shadow-sm card-hover">
                <div class="card-body">
                    <h5 class="card-title mb-3">Location</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Address</h6>
                            <p class="mb-2">{{ $property->location?->full_address ?? $property->address }}</p>
                            @if($property->location?->neighborhood)
                                <p><strong>Neighborhood:</strong> {{ $property->location->neighborhood }}</p>
                            @endif
                            @if($property->location?->district)
                                <p><strong>District:</strong> {{ $property->location->district }}</p>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($property->location?->nearby_landmarks)
                                <h6><i class="fas fa-compass me-2 text-success"></i>Nearby Landmarks</h6>
                                <ul class="list-unstyled">
                                    @foreach($property->location->nearby_landmarks as $landmark)
                                        <li class="mb-1"><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $landmark }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    @if($property->location?->latitude && $property->location?->longitude)
                        <div class="mt-4">
                            <h6><i class="fas fa-map me-2 text-primary"></i>Property Location</h6>
                            
                            <!-- Location Details Card -->
                            <div class="alert alert-info bg-light border-0 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-2 text-primary">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            {{ $property->location?->full_address ?? $property->address ?? 'Address not available' }}
                                        </h6>
                                        <div class="small text-muted">
                                            <i class="fas fa-compass me-1"></i>
                                            Coordinates: {{ $property->location->latitude }}, {{ $property->location->longitude }}
                                        </div>
                                        @if($property->location?->city || $property->location?->country)
                                            <div class="small text-muted mt-1">
                                                @if($property->location?->city)
                                                    <i class="fas fa-city me-1"></i>{{ $property->location->city }}
                                                @endif
                                                @if($property->location?->country)
                                                    <span class="ms-2"><i class="fas fa-globe me-1"></i>{{ $property->location->country }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ $property->location->latitude }},{{ $property->location->longitude }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>View in Google Maps
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Interactive Map -->
                            <div id="propertyMap" class="rounded shadow-sm" style="height: 400px; position: relative; border: 2px solid #e9ecef;" 
                                 data-lat="{{ $property->location->latitude }}" 
                                 data-lng="{{ $property->location->longitude }}"
                                 data-title="{{ $property->title }}"
                                 data-address="{{ $property->location?->full_address ?? $property->address }}"
                                 data-city="{{ $property->location?->city ?? '' }}"
                                 data-country="{{ $property->location?->country ?? '' }}">
                                <div class="text-center" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading map...</span>
                                    </div>
                                    <div class="text-muted small mt-2">Loading interactive map...</div>
                                </div>
                            </div>
                            
                            <!-- Map Actions -->
                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                <button onclick="centerMapOnProperty()" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-crosshairs me-1"></i>Center on Property
                                </button>
                                <button onclick="zoomIn()" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-search-plus me-1"></i>Zoom In
                                </button>
                                <button onclick="zoomOut()" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-search-minus me-1"></i>Zoom Out
                                </button>
                                <button onclick="changeMapStyle('street')" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-road me-1"></i>Street Map
                                </button>
                                <button onclick="changeMapStyle('satellite')" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-satellite me-1"></i>Satellite
                                </button>
                                <a href="https://maps.google.com/maps?daddr={{ $property->location->latitude }},{{ $property->location->longitude }}" 
                                   target="_blank" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-directions me-1"></i>Get Directions
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- No Location Available -->
                        <div class="mt-4">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Location Not Available</strong>
                                <p class="mb-0 mt-2">The exact coordinates for this property are not available. Please contact the agent for more location details.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Amenities & Features Lists -->
            <div class="card mt-3 shadow-sm card-hover">
                <div class="card-body">
                    <h5 class="card-title mb-3">Features & Amenities</h5>
                    <div class="row">
                        <div class="col-md-6">
                            @if($property->details)
                                <h6 class="mb-3">Property Details</h6>
                                <ul class="list-unstyled">
                                    @if($property->details->bedrooms)<li class="mb-2"><i class="fas fa-bed text-primary me-2"></i>{{ $property->details->bedrooms }} Bedrooms</li>@endif
                                    @if($property->details->bathrooms)<li class="mb-2"><i class="fas fa-bath text-primary me-2"></i>{{ $property->details->bathrooms }} Bathrooms</li>@endif
                                    @if($property->details->area)<li class="mb-2"><i class="fas fa-ruler-combined text-primary me-2"></i>{{ $property->details?->formatted_area ?? number_format($property->area ?? 0, 2) }} {{ $property->area_unit ?? 'm²' }}</li>@endif
                                    @if($property->details->land_area)<li class="mb-2"><i class="fas fa-tree text-success me-2"></i>Land: {{ number_format($property->details->land_area ?? 0, 2) }} {{ $property->land_area_unit ?? 'm²' }}</li>@endif
                                    @if($property->details->parking_spaces)<li class="mb-2"><i class="fas fa-car text-info me-2"></i>{{ $property->details->parking_spaces }} Parking Spaces</li>@endif
                                    @if($property->details->year_built)<li class="mb-2"><i class="fas fa-calendar text-warning me-2"></i>Built in {{ $property->details->year_built }}</li>@endif
                                </ul>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($property->propertyAmenities->count() > 0)
                                <h6 class="mb-3">Amenities</h6>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @foreach($property->propertyAmenities as $amenity)
                                        <span class="badge bg-light text-dark">
                                            @if($amenity->icon)<i class="{{ $amenity->icon }} me-1"></i>@endif
                                            {{ $amenity?->name ?? 'Amenity' }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            @if($property->features->count() > 0)
                                <h6 class="mb-3">Special Features</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($property->features as $feature)
                                        <span class="badge {{ $feature->is_premium ? 'bg-warning text-dark' : 'bg-secondary text-white' }}">
                                            @if($feature->icon)<i class="{{ $feature->icon }} me-1"></i>@endif
                                            {{ $feature?->name ?? 'Feature' }}
                                            @if($feature->is_premium)<i class="fas fa-star ms-1"></i>@endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Agent Information -->
            @if($property->agent)
                <div class="card mt-3 shadow-sm card-hover">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="fas fa-user me-2 text-warning"></i>Agent Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ $property->agent->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $property->agent?->name }}" class="rounded-circle me-3" style="width:50px;height:50px;object-fit:cover;">
                                    <div>
                                        <div class="fw-bold">{{ $property->agent?->name ?? 'Agent' }}</div>
                                        <small class="text-muted">{{ $property->agent->role ?? 'Real Estate Agent' }}</small>
                                    </div>
                                </div>
                                <p class="mb-1"><strong>Email:</strong> <a href="mailto:{{ $property->agent->email }}">{{ $property->agent->email ?? 'N/A' }}</a></p>
                                <p class="mb-0"><strong>Phone:</strong> <a href="tel:{{ $property->agent->phone }}">{{ $property->agent->phone ?? 'N/A' }}</a></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Listed:</strong> {{ $property->created_at?->format('M d, Y') ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Updated:</strong> {{ $property->updated_at?->format('M d, Y') ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Media Gallery -->
            @if($property->media && $property->media->count() > 1)
                <div class="card mt-3 shadow-sm card-hover">
                    <div class="card-body">
                        <h5 class="card-title mb-3"><i class="fas fa-images me-2 text-info"></i>Media Files</h5>
                        <div class="row">
                            @foreach($property->media as $media)
                                @if($media->media_type === 'image')
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border">
                                            <img src="{{ $media->url }}" class="card-img-top" style="height:150px; object-fit:cover;" alt="{{ $media->file_name }}" loading="lazy">
                                            <div class="card-body p-2">
                                                <small class="text-muted d-block">{{ $media->file_name }}</small>
                                                @if($media->is_featured)
                                                    <span class="badge bg-warning text-dark mt-1">Featured</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Similar Properties -->
            @if($similarProperties->count() > 0)
                <div class="mt-4">
                    <h5 class="mb-3">Similar Properties</h5>
                    <div class="row">
                        @foreach($similarProperties as $similar)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="card h-100 card-hover border-0">
                                    @if($similar->media->first())
                                        <img src="{{ $similar->media->first()->url }}" class="card-img-top" style="height:160px;object-fit:cover;" alt="{{ $similar->title }}" loading="lazy">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" style="height:160px;">
                                            <i class="fas fa-home fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title mb-1">{{ Str::limit($similar->title, 40) }}</h6>
                                        <p class="card-text text-muted small mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i> {{ $similar->location?->city ?? $similar->city }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="text-primary fw-bold">
                                                {{ $similar->pricing?->formatted_price ?? number_format($similar->price ?? 0, 2) . ' ' . ($similar->currency ?? 'USD') }}
                                            </div>
                                            <a href="{{ route('properties.show', $similar) }}" class="btn btn-sm btn-primary">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right: Sidebar -->
        <div class="col-lg-4">
            <div class="sticky-sidebar">
                <!-- Contact Agent -->
                <div class="card mb-3 card-hover shadow">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Contact Agent</h5>
                        @if($property->agent)
                            <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                <img src="{{ $property->agent->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $property->agent?->name }}" class="rounded-circle me-3" style="width:60px;height:60px;object-fit:cover;">
                                <div>
                                    <div class="fw-bold">{{ $property->agent?->name ?? 'Agent' }}</div>
                                    <small class="text-muted">{{ $property->agent->role ?? 'Real Estate Agent' }}</small>
                                </div>
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-primary" onclick="contactAgent('phone')">
                                <i class="fas fa-phone me-2"></i>Call Agent
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="contactAgent('email')">
                                <i class="fas fa-envelope me-2"></i>Email Agent
                            </button>
                            <button type="button" class="btn btn-outline-success" onclick="contactAgent('whatsapp')">
                                <i class="fab fa-whatsapp me-2"></i>WhatsApp
                            </button>
                        </div>

                        @auth
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary w-100" onclick="scheduleViewing()">
                                    <i class="fas fa-calendar me-2"></i>Schedule Viewing
                                </button>
                            </div>
                        @endauth
                    </div>
                </div>

                <!-- Actions -->
                <div class="card mb-3 card-hover shadow">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-danger" onclick="toggleFavorite(this)">
                                <i class="far fa-heart me-2"></i><span id="favBtnText">Add to Favorites</span>
                            </button>
                            <button type="button" class="btn btn-outline-info" onclick="toggleComparison()">
                                <i class="fas fa-balance-scale me-2"></i>Add to Comparison
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="shareProperty()">
                                <i class="fas fa-share-alt me-2"></i>Share Property
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Stats -->
                <div class="card mb-3 card-hover shadow">
                    <div class="card-body">
                        <h6 class="card-title mb-3"><i class="fas fa-chart-bar me-2 text-info"></i>Property Statistics</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-eye text-primary fa-2x"></i>
                                    <div class="stat-value">{{ number_format($property->views_count ?? 0) }}</div>
                                    <div class="stat-label">Views</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-heart text-danger fa-2x"></i>
                                    <div class="stat-value">{{ number_format($property->favorites_count ?? 0) }}</div>
                                    <div class="stat-label">Favorites</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-phone text-success fa-2x"></i>
                                    <div class="stat-value">{{ number_format($property->inquiries_count ?? 0) }}</div>
                                    <div class="stat-label">Inquiries</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Virtual tours -->
                @if($property->virtualTours && $property->virtualTours->count() > 0)
                    <div class="card mb-3 card-hover shadow">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-vr-cardboard me-2 text-info"></i>Virtual Tour</h6>
                            @foreach($property->virtualTours as $tour)
                                <a href="{{ $tour->url }}" class="btn btn-outline-primary w-100 mb-2" target="_blank" rel="noopener noreferrer">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ $tour->title ?? 'View Tour' }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Floor plans -->
                @if($property->floorPlans && $property->floorPlans->count() > 0)
                    <div class="card card-hover shadow">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-th-large me-2 text-warning"></i>Floor Plans</h6>
                            @foreach($property->floorPlans as $plan)
                                <div class="mb-2">
                                    <small class="text-muted d-block mb-2">{{ $plan->title ?? 'Floor Plan' }}</small>
                                    <img src="{{ $plan->url ?? $plan->getUrlAttribute() }}" class="img-fluid rounded" style="max-height:180px; object-fit:contain;" alt="{{ $plan->title }}" loading="lazy">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="contactModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="contactModalTitle" class="modal-title">Contact Agent</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="contactModalBody" class="modal-body">
                <!-- content injected by JS -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="shareModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share Property</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div id="shareModalBody" class="modal-body">
                <!-- share content injected -->
            </div>
        </div>
    </div>
</div>

<!-- Toast container -->
<div id="toastContainer" aria-live="polite" aria-atomic="true"></div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<!-- Leaflet JavaScript -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
    // thumbnails sync
    document.addEventListener('DOMContentLoaded', function () {
        const thumbnails = document.querySelectorAll('.thumbnail-image');
        thumbnails.forEach((thumb, idx) => {
            thumb.addEventListener('click', () => {
                thumbnails.forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });
        });

        const carouselEl = document.querySelector('#propertyCarousel');
        if (carouselEl) {
            carouselEl.addEventListener('slid.bs.carousel', function (e) {
                const index = e.to;
                thumbnails.forEach((t, i) => t.classList.toggle('active', i === index));
            });
        }

        // record property view
        fetch(`/properties/{{ $property->id }}/view`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        }).catch(err => console.log('View recorded'));

        // Initialize Leaflet Map with delay to ensure all libraries are loaded
        setTimeout(() => {
            initializePropertyMap();
        }, 500);
    });

    // Initialize Property Map
    function initializePropertyMap() {
        const mapContainer = document.getElementById('propertyMap');
        
        if (!mapContainer) {
            console.log('Map container not found');
            return;
        }
        
        const lat = parseFloat(mapContainer.dataset.lat);
        const lng = parseFloat(mapContainer.dataset.lng);
        const title = mapContainer.dataset.title;
        const address = mapContainer.dataset.address;
        
        console.log('Map data:', { lat, lng, title, address });
        
        // Check if Leaflet is loaded
        if (typeof L === 'undefined') {
            console.error('Leaflet is not loaded');
            mapContainer.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Map library is loading... Please refresh the page.
                </div>
            `;
            return;
        }
        
        // Check if coordinates are valid
        if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
            console.log('Invalid coordinates:', lat, lng);
            mapContainer.innerHTML = `
                <div class="alert alert-info text-center">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    <strong>Location coordinates not available</strong><br>
                    <small>This property doesn't have exact location coordinates set.</small>
                </div>
            `;
            return;
        }
        
        // Clear loading spinner
        mapContainer.innerHTML = '';
        
        try {
            // Initialize the map
            const map = L.map('propertyMap').setView([lat, lng], 15);
            
            console.log('Map initialized successfully');
            
            // Add OpenStreetMap tiles with fallback options
            const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: 'OpenStreetMap contributors',
                maxZoom: 19,
                errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+TWFwIFRpbGUgRXJyb3I8L3RleHQ+PC9zdmc+',
                tileSize: 256,
                zoomOffset: 0
            }).addTo(map);
            
            // Add error handling for tile loading
            tileLayer.on('tileerror', function(e) {
                console.warn('Tile loading error:', e);
                // Try alternative tile provider if OpenStreetMap fails
                if (!window.fallbackTileUsed) {
                    window.fallbackTileUsed = true;
                    map.removeLayer(tileLayer);
                    
                    // Use CartoDB as fallback
                    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                        attribution: 'OpenStreetMap contributors CARTO',
                        maxZoom: 19,
                        subdomains: 'abcd',
                        errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+RmFsbGJhY2sgTWFwPC90ZXh0Pjwvc3ZnPg=='
                    }).addTo(map);
                    
                    console.log('Switched to fallback tile provider');
                }
            });
            
            // Create custom icon for property marker
            const propertyIcon = L.divIcon({
                html: '<div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3);"><i class="fas fa-home"></i></div>',
                iconSize: [40, 40],
                iconAnchor: [20, 40],
                popupAnchor: [0, -40],
                className: 'custom-property-marker'
            });
            
            // Add marker for property
            const marker = L.marker([lat, lng], { icon: propertyIcon }).addTo(map);
            
            // Create popup content
            const popupContent = `
                <div style="min-width: 200px;">
                    <h6 style="margin: 0 0 8px 0; color: #333; font-weight: bold;">${title}</h6>
                    <p style="margin: 0 0 8px 0; color: #666; font-size: 14px;">${address}</p>
                    <div style="display: flex; gap: 8px;">
                        <a href="https://www.google.com/maps/search/?api=1&query=${lat},${lng}" 
                           target="_blank" 
                           style="background: #4285f4; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                            <i class="fas fa-external-link-alt"></i> Google Maps
                        </a>
                        <a href="https://maps.google.com/maps?daddr=${lat},${lng}" 
                           target="_blank" 
                           style="background: #34a853; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px;">
                            <i class="fas fa-directions"></i> Directions
                        </a>
                    </div>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                maxWidth: 250,
                className: 'property-popup'
            });
            
            // Open popup by default
            marker.openPopup();
            
            // Add zoom controls styling
            map.zoomControl.setPosition('topright');
            
            // Add scale
            L.control.scale({
                position: 'bottomleft',
                metric: true,
                imperial: false
            }).addTo(map);
            
            // Store map instance globally for control functions
            window.propertyMap = map;
            window.propertyMarker = marker;
            
            console.log('Map setup completed successfully');
            
        } catch (error) {
            console.error('Error initializing map:', error);
            mapContainer.innerHTML = `
                <div class="alert alert-danger text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error loading map</strong><br>
                    <small>Please refresh the page or check your internet connection.</small>
                </div>
            `;
        }
    }

    // Map control functions
    function centerMapOnProperty() {
        if (window.propertyMap && window.propertyMarker) {
            window.propertyMap.setView(window.propertyMarker.getLatLng(), 16);
            window.propertyMarker.openPopup();
            showToast('Map centered on property', 'success');
        }
    }

    function zoomIn() {
        if (window.propertyMap) {
            window.propertyMap.zoomIn();
        }
    }

    function zoomOut() {
        if (window.propertyMap) {
            window.propertyMap.zoomOut();
        }
    }

    // Change map style
    function changeMapStyle(style) {
        if (!window.propertyMap) return;
        
        // Remove existing tile layers
        window.propertyMap.eachLayer(function(layer) {
            if (layer instanceof L.TileLayer) {
                window.propertyMap.removeLayer(layer);
            }
        });
        
        let tileUrl;
        let attribution;
        
        switch(style) {
            case 'satellite':
                tileUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}';
                attribution = 'Tiles &copy; Esri';
                break;
            case 'street':
            default:
                tileUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                attribution = 'OpenStreetMap contributors';
                break;
        }
        
        // Add new tile layer
        L.tileLayer(tileUrl, {
            attribution: attribution,
            maxZoom: 19,
            errorTileUrl: 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjU2IiBoZWlnaHQ9IjI1NiIgZmlsbD0iI2VlZSIvPjx0ZXh0IHg9IjUwJSIgeT0iNTAlIiBmb250LXNpemU9IjE0IiBmaWxsPSIjOTk5IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSI+TWFwIFRpbGUgRXJyb3I8L3RleHQ+PC9zdmc+',
            tileSize: 256,
            zoomOffset: 0
        }).addTo(window.propertyMap);
        
        showToast(`Switched to ${style} view`, 'success');
    }

    // show toast
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer');
        const bgClass = type === 'success' ? 'success' : type === 'error' ? 'danger' : 'primary';
        const wrapper = document.createElement('div');
        wrapper.innerHTML = `
            <div class="toast align-items-center text-bg-${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
              </div>
            </div>
        `;
        toastContainer.appendChild(wrapper.firstElementChild);
        const toastEl = toastContainer.lastElementChild;
        const bsToast = new bootstrap.Toast(toastEl, { delay: 3000 });
        bsToast.show();
        toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
    }

    // contact agent
    function contactAgent(method) {
        const modal = new bootstrap.Modal(document.getElementById('contactModal'));
        const body = document.getElementById('contactModalBody');
        const agentPhone = '{{ $property->agent->phone ?? "" }}';
        const agentEmail = '{{ $property->agent->email ?? "" }}';
        const propTitle = '{{ $property->title }}';

        if (method === 'phone') {
            body.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                    <h5>Call Agent</h5>
                    <p class="mb-3 text-muted">${agentPhone || 'Contact number not available'}</p>
                    ${agentPhone ? `<a href="tel:${agentPhone}" class="btn btn-primary btn-lg">Call Now</a>` : ''}
                </div>
            `;
        } else if (method === 'email') {
            body.innerHTML = `
                <form id="contactAgentForm">
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" value="Inquiry about: ${propTitle}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Your Message</label>
                        <textarea class="form-control" id="agentMessage" rows="4" required placeholder="Type your message here...">I'm interested in this property. Please provide more information.</textarea>
                    </div>
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary btn-lg" onclick="sendEmail()">Send Message</button>
                    </div>
                </form>
            `;
        } else if (method === 'whatsapp') {
            const message = encodeURIComponent(`Hi, I'm interested in this property: ${propTitle}\n${window.location.href}`);
            body.innerHTML = `
                <div class="text-center">
                    <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                    <h5>WhatsApp</h5>
                    <p class="mb-3 text-muted">Click below to open WhatsApp and send a message about this property.</p>
                    <a href="https://wa.me/?text=${message}" class="btn btn-success btn-lg" target="_blank">
                        <i class="fab fa-whatsapp me-2"></i>Open WhatsApp
                    </a>
                </div>
            `;
        }

        modal.show();
    }

    function sendEmail() {
        const message = document.getElementById('agentMessage').value;
        const agentEmail = '{{ $property->agent->email ?? "" }}';
        
        if (!agentEmail) {
            showToast('Agent email not available', 'error');
            return;
        }

        // Create mailto link
        const mailtoLink = `mailto:${agentEmail}?subject=Inquiry about: {{ $property->title }}&body=${encodeURIComponent(message)}`;
        window.location.href = mailtoLink;
        
        showToast('Opening your email client...', 'success');
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
        modal.hide();
    }

    // toggle favorite
    function toggleFavorite(btn) {
        fetch(`/properties/{{ $property->id }}/favorite/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const isFav = btn.classList.contains('btn-danger');
                    if (isFav) {
                        btn.classList.remove('btn-danger');
                        btn.classList.add('btn-outline-danger');
                        document.getElementById('favBtnText').textContent = 'Add to Favorites';
                    } else {
                        btn.classList.remove('btn-outline-danger');
                        btn.classList.add('btn-danger');
                        document.getElementById('favBtnText').textContent = 'Remove from Favorites';
                    }
                    showToast(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Please log in to add favorites', 'error');
            });
    }

    // toggle comparison
    function toggleComparison() {
        fetch(`/properties/compare/add`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                property_id: {{ $property->id }}
            })
        })
            .then(response => response.json())
            .then(data => {
                showToast(data.message, data.success ? 'success' : 'warning');
            })
            .catch(error => {
                showToast('Feature coming soon!', 'info');
            });
    }

    // share property
    function shareProperty() {
        const modal = new bootstrap.Modal(document.getElementById('shareModal'));
        const shareBody = document.getElementById('shareModalBody');
        const propUrl = window.location.href;
        const propTitle = '{{ $property->title }}';

        shareBody.innerHTML = `
            <div class="d-grid gap-2">
                <a href="mailto:?subject=${encodeURIComponent('Check out this property: ' + propTitle)}&body=${encodeURIComponent('I found this amazing property: ' + propUrl)}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-2"></i>Email
                </a>
                <a href="https://wa.me/?text=${encodeURIComponent('Check out this property: ' + propTitle + ' ' + propUrl)}" 
                   class="btn btn-success" target="_blank">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(propUrl)}" 
                   class="btn btn-primary" target="_blank">
                    <i class="fab fa-facebook me-2"></i>Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?text=${encodeURIComponent('Check out this property: ' + propTitle)}&url=${encodeURIComponent(propUrl)}" 
                   class="btn btn-info text-white" target="_blank">
                    <i class="fab fa-twitter me-2"></i>Twitter
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="copyLink()">
                    <i class="fas fa-link me-2"></i>Copy Link
                </button>
            </div>
        `;

        modal.show();
    }

    function copyLink() {
        navigator.clipboard.writeText(window.location.href).then(() => {
            showToast('Link copied to clipboard!', 'success');
        }).catch(() => {
            showToast('Failed to copy link', 'error');
        });
    }

    // schedule viewing
    function scheduleViewing() {
        showToast('Viewing scheduling feature coming soon!', 'info');
    }
</script>

@endsection