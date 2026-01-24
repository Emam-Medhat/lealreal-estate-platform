 @extends('layouts.app')

@section('title', $property->title)

@section('content')
    <div class="container-fluid py-4">
        <!-- Property Header -->
        <div class="row mb-4">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('optimized.properties.index') }}">Properties</a></li>
                        <li class="breadcrumb-item active">{{ $property->title }}</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="h2 mb-2">{{ $property->title }}</h1>
                        <p class="text-muted mb-2">
                            <i class="fas fa-map-marker-alt me-1"></i>
                            {{ $property->location?->full_address ?? $property->address }}
                        </p>
                        <div class="d-flex gap-2 mb-2">
                            <span class="badge bg-light text-dark">{{ $property->propertyType?->name ?? 'N/A' }}</span>
                            <span class="badge bg-primary">{{ ucfirst($property->listing_type) }}</span>
                            @if($property->featured)
                                <span class="badge bg-warning">Featured</span>
                            @endif
                            @if($property->premium)
                                <span class="badge bg-danger">Premium</span>
                            @endif
                        </div>
                    </div>
                    <div class="text-end">
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

        <!-- Property Gallery and Details -->
        <div class="row mb-4">
            <!-- Image Gallery -->
            <div class="col-lg-8" style="width:100% !important">
                <div class="card">
                    <div class="card-body p-0">
                        <!-- Main Image -->
                        <div id="mainImageContainer" class="position-relative" style="height: 500px;">
                            @if($property->media->where('media_type', 'image')->first())
                                <img id="mainImage"
                                    src="{{ $property->media->where('media_type', 'image')->first()->getUrlAttribute() }}"
                                    class="w-100 h-100" style="object-fit: cover;" alt="{{ $property->title }}">
                            @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-home fa-4x text-muted"></i>
                                </div>
                            @endif

                            <!-- Image Navigation -->
                            @if($property->media->where('media_type', 'image')->count() > 1)
                                <button type="button"
                                    class="btn btn-dark btn-sm position-absolute top-50 start-0 translate-middle-y ms-2"
                                    onclick="changeImage(-1)">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button type="button"
                                    class="btn btn-dark btn-sm position-absolute top-50 end-0 translate-middle-y me-2"
                                    onclick="changeImage(1)">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            @endif
                        </div>

                        <!-- Thumbnail Gallery -->
                        @if($property->media->where('media_type', 'image')->count() > 1)
                            <div class="p-3">
                                <div class="d-flex gap-2 overflow-auto">
                                    @foreach($property->media->where('media_type', 'image') as $index => $image)
                                        <img src="{{ $image->getUrlAttribute() }}"
                                            class="thumbnail-image {{ $index == 0 ? 'border-primary' : '' }}"
                                            style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;"
                                            onclick="setMainImage('{{ $image->getUrlAttribute() }}', {{ $index }})"
                                            alt="Thumbnail {{ $index + 1 }}">
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Property Description -->
                <div class="card shadow-sm border-0 mt-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                <i class="fas fa-file-alt text-primary"></i>
                            </div>
                            <div>
                                <h5 class="card-title mb-0">Property Description</h5>
                                <p class="text-muted mb-0">Detailed information about this property</p>
                            </div>
                        </div>
                        <div class="property-description fs-6 text-secondary">
                            {!! nl2br(e($property->description ?? 'No description available')) !!}
                        </div>
                    </div>
                </div>

                <!-- Property Details Grid -->
                <div class="row mt-4">
                    <!-- Basic Information -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-info-circle text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Basic Information</h5>
                                        <p class="text-muted mb-0">Property details & specifications</p>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <span class="text-muted"><i class="fas fa-hashtag me-2"></i>Property Code</span>
                                            <span class="fw-bold">{{ $property->property_code ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <span class="text-muted"><i class="fas fa-tag me-2"></i>Title</span>
                                            <span class="fw-bold">{{ $property->title }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <span class="text-muted"><i class="fas fa-building me-2"></i>Type</span>
                                            <span class="badge bg-primary">{{ ucfirst($property->property_type ?? 'N/A') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <span class="text-muted"><i class="fas fa-list me-2"></i>Listing Type</span>
                                            <span class="badge bg-success">{{ ucfirst($property->listing_type) }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                            <span class="text-muted"><i class="fas fa-toggle-on me-2"></i>Status</span>
                                            <span class="badge bg-info">{{ ucfirst($property->status ?? 'draft') }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded">
                                            <span class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Price</span>
                                            <span class="fw-bold text-primary fs-5">{{ number_format($property->price ?? 0, 2) }} {{ $property->currency ?? 'USD' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Features -->
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm border-0 h-100">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-home text-success"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Property Features</h5>
                                        <p class="text-muted mb-0">Specifications & amenities</p>
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
                                        <span class="badge bg-purple text-white"><i class="fas fa-crown me-1"></i>Premium</span>
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Location Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-map-marker-alt text-info"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Location Information</h5>
                                    <p class="text-muted mb-0">Property location & coordinates</p>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-home text-primary me-2"></i>
                                            <span class="text-muted">Address</span>
                                        </div>
                                        <div class="fw-bold">{{ $property->address ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-city text-success me-2"></i>
                                            <span class="text-muted">City</span>
                                        </div>
                                        <div class="fw-bold">{{ $property->city ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-globe text-info me-2"></i>
                                            <span class="text-muted">Country</span>
                                        </div>
                                        <div class="fw-bold">{{ $property->country ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-map-pin text-warning me-2"></i>
                                            <span class="text-muted">Latitude</span>
                                        </div>
                                        <div class="fw-bold">{{ $property->latitude ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 bg-light rounded">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="fas fa-compass text-danger me-2"></i>
                                            <span class="text-muted">Longitude</span>
                                        </div>
                                        <div class="fw-bold">{{ $property->longitude ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Map -->
                            @if($property->location?->latitude && $property->location?->longitude)
                            <div class="mt-3">
                                <div id="propertyMap" style="height: 200px;" class="rounded overflow-hidden"></div>
                            </div>
                            @endif

                            <!-- Property Status -->
                            <div class="mt-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="fas fa-star text-warning"></i>
                                    </div>
                                    <div>
                                        <h5 class="card-title mb-0">Property Status</h5>
                                        <p class="text-muted mb-0">Listing information & features</p>
                                    </div>
                                </div>
                                
                                <div class="row g-3">
                                    <div class="col-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-eye text-primary fa-2x mb-2"></i>
                                            <div class="fw-bold">{{ number_format($property->views_count ?? 0) }}</div>
                                            <div class="text-muted small">Views</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-heart text-danger fa-2x mb-2"></i>
                                            <div class="fw-bold">{{ number_format($property->favorites_count ?? 0) }}</div>
                                            <div class="text-muted small">Favorites</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-phone text-success fa-2x mb-2"></i>
                                            <div class="fw-bold">{{ number_format($property->inquiries_count ?? 0) }}</div>
                                            <div class="text-muted small">Inquiries</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center p-3 bg-light rounded">
                                            <i class="fas fa-calendar text-info fa-2x mb-2"></i>
                                            <div class="fw-bold">{{ $property->created_at?->format('M d, Y') ?? 'N/A' }}</div>
                                            <div class="text-muted small">Listed</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    @if($property->featured)
                                        <div class="alert alert-warning d-flex align-items-center">
                                            <i class="fas fa-star me-2"></i>
                                            <div>
                                                <strong>Featured Property</strong><br>
                                                <small>This property is featured on our homepage</small>
                                            </div>
                                        </div>
                                    @endif
                                    @if($property->premium)
                                        <div class="alert alert-purple d-flex align-items-center">
                                            <i class="fas fa-crown me-2"></i>
                                            <div>
                                                <strong>Premium Listing</strong><br>
                                                <small>This property has premium features</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Second Row - Features and Amenities -->
            <div class="row mt-4">
                <!-- Property Features -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-home text-success"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Property Features</h5>
                                    <p class="text-muted mb-0">Specifications & amenities</p>
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
                                    <span class="badge bg-purple text-white"><i class="fas fa-crown me-1"></i>Premium</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-info-circle text-primary"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0">Additional Information</h5>
                                    <p class="text-muted mb-0">Extra property details</p>
                                </div>
                            </div>
                            
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <span class="text-muted"><i class="fas fa-hashtag me-2"></i>Property Code</span>
                                        <span class="fw-bold">{{ $property->property_code ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <span class="text-muted"><i class="fas fa-building me-2"></i>Type</span>
                                        <span class="badge bg-primary">{{ ucfirst($property->property_type ?? 'N/A') }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                                        <span class="text-muted"><i class="fas fa-list me-2"></i>Listing Type</span>
                                        <span class="badge bg-success">{{ ucfirst($property->listing_type) }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center p-3 bg-primary bg-opacity-10 rounded">
                                        <span class="text-muted"><i class="fas fa-dollar-sign me-2"></i>Price</span>
                                        <span class="fw-bold text-primary fs-5">{{ number_format($property->price ?? 0, 2) }} {{ $property->currency ?? 'USD' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                                    <tr>
                                        <td><strong>Premium:</strong></td>
                                        <td>
                                            @if($property->premium ?? false)
                                                <span class="badge bg-danger">Yes</span>
                                            @else
                                                <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Views:</strong></td>
                                        <td>{{ $property->views_count ?? 0 }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Media Information -->
                        <h6 class="mt-4"><i class="fas fa-images me-2 text-info"></i>Media Files</h6>
                        @if($property->media && $property->media->count() > 0)
                            <div class="row">
                                @foreach($property->media as $media)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <i class="fas fa-file me-2"></i>
                                                    {{ ucfirst($media->media_type ?? 'file') }}
                                                </h6>
                                                <p class="mb-1"><strong>File Name:</strong> {{ $media->file_name }}</p>
                                                <p class="mb-1"><strong>File Size:</strong> {{ $media->formatted_file_size ?? 'N/A' }}</p>
                                                <p class="mb-1"><strong>File Type:</strong> {{ $media->file_type ?? 'N/A' }}</p>
                                                <p class="mb-1"><strong>Sort Order:</strong> {{ $media->sort_order ?? 0 }}</p>
                                                @if($media->is_featured)
                                                    <span class="badge bg-warning">Featured</span>
                                                @endif
                                                @if($media->is_primary)
                                                    <span class="badge bg-primary">Primary</span>
                                                @endif
                                                @if($media->file_path && $media->media_type === 'image')
                                                    <div class="mt-2">
                                                        <img src="{{ $media->url }}" class="img-fluid rounded" style="max-height: 150px;" alt="{{ $media->file_name }}">
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No media files uploaded yet.</p>
                        @endif

                        <!-- Agent Information -->
                        <h6 class="mt-4"><i class="fas fa-user me-2 text-warning"></i>Agent Information</h6>
                        @if($property->agent)
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Name:</strong> {{ $property->agent->name ?? 'N/A' }}</p>
                                    <p><strong>Email:</strong> {{ $property->agent->email ?? 'N/A' }}</p>
                                    <p><strong>Role:</strong> {{ $property->agent->role ?? 'Agent' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Created At:</strong> {{ $property->created_at->format('Y-m-d H:i') }}</p>
                                    <p><strong>Updated At:</strong> {{ $property->updated_at->format('Y-m-d H:i') }}</p>
                                </div>
                            </div>
                        @else
                            <p class="text-muted">No agent information available.</p>
                        @endif
                    </div>
                </div>

                <!-- Property Features -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Features & Amenities</h5>

                        <!-- Key Features -->
                        @if($property->details)
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h6>Property Details</h6>
                                    <ul class="list-unstyled">
                                        @if($property->details->bedrooms)
                                            <li><i class="fas fa-bed me-2 text-primary"></i>{{ $property->details->bedrooms }}
                                                Bedrooms</li>
                                        @endif
                                        @if($property->details->bathrooms)
                                            <li><i class="fas fa-bath me-2 text-primary"></i>{{ $property->details->bathrooms }}
                                                Bathrooms</li>
                                        @endif
                                        @if($property->details->area)
                                            <li><i
                                                    class="fas fa-ruler-combined me-2 text-primary"></i>{{ $property->details?->formatted_area ?? number_format($property->area, 2) . ' ' . $property->area_unit }}
                                            </li>
                                        @endif
                                        @if($property->details->land_area)
                                            <li><i
                                                    class="fas fa-tree me-2 text-primary"></i>{{ $property->details?->formatted_land_area ?? number_format($property->details?->land_area ?? 0, 2) . ' ' . ($property->details?->land_area_unit ?? $property->area_unit) }}
                                            </li>
                                        @endif
                                        @if($property->details->floors)
                                            <li><i class="fas fa-building me-2 text-primary"></i>{{ $property->details->floors }}
                                                Floors</li>
                                        @endif
                                        @if($property->details->parking_spaces)
                                            <li><i class="fas fa-car me-2 text-primary"></i>{{ $property->details->parking_spaces }}
                                                Parking Spaces</li>
                                        @endif
                                        @if($property->details->year_built)
                                            <li><i class="fas fa-calendar me-2 text-primary"></i>Built in
                                                {{ $property->details->year_built }}</li>
                                        @endif
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6>Additional Features</h6>
                                    @if($property->details->specifications)
                                        <h6 class="mt-3">Specifications</h6>
                                        <ul class="list-unstyled">
                                            @foreach($property->details->specifications as $spec)
                                                <li><i class="fas fa-check me-2 text-success"></i>{{ $spec }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    @if($property->details->materials)
                                        <h6 class="mt-3">Materials</h6>
                                        <ul class="list-unstyled">
                                            @foreach($property->details->materials as $material)
                                                <li><i class="fas fa-cube me-2 text-info"></i>{{ $material }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Amenities -->
                        @if($property->propertyAmenities->count() > 0)
                            <h6 class="mt-3">Amenities</h6>
                            <div class="row">
                                @foreach($property->propertyAmenities as $amenity)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <span class="badge bg-light text-dark">
                                            @if($amenity->icon)
                                                <i class="{{ $amenity->icon }} me-1"></i>
                                            @endif
                                            {{ $amenity?->name ?? 'Amenity' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Features -->
                        @if($property->features->count() > 0)
                            <h6 class="mt-3">Special Features</h6>
                            <div class="row">
                                @foreach($property->features as $feature)
                                    <div class="col-md-4 col-sm-6 mb-2">
                                        <span class="badge {{ $feature->is_premium ? 'bg-warning' : 'bg-secondary' }}">
                                            @if($feature->icon)
                                                <i class="{{ $feature->icon }} me-1"></i>
                                            @endif
                                            {{ $feature?->name ?? 'Feature' }}
                                            @if($feature->is_premium)
                                                <i class="fas fa-star ms-1"></i>
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Location -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Location</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Address</h6>
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
                                    <h6>Nearby Landmarks</h6>
                                    <ul class="list-unstyled">
                                        @foreach($property->location->nearby_landmarks as $landmark)
                                            <li><i class="fas fa-map-marker-alt me-2 text-primary"></i>{{ $landmark }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        <!-- Map -->
                        @if($property->location?->latitude && $property->location?->longitude)
                            <div class="mt-3">
                                <div id="propertyMap" style="height: 300px;"></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Agent -->
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5 class="card-title">Contact Agent</h5>
                        @if($property->agent)
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <img src="{{ $property->agent->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                        class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover;"
                                        alt="{{ $property->agent?->name ?? 'Agent' }}">
                                </div>
                                <div>
                                    <h6 class="mb-0">{{ $property->agent?->name ?? 'Agent' }}</h6>
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
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-outline-danger" onclick="toggleFavorite()">
                                <i class="far fa-heart me-2"></i>Add to Favorites
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

                <!-- Property Stats -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Property Statistics</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-eye text-primary fa-2x"></i>
                                    <div class="stat-value">{{ $property->views_count }}</div>
                                    <div class="stat-label">Views</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-heart text-danger fa-2x"></i>
                                    <div class="stat-value">{{ $property->favorites_count }}</div>
                                    <div class="stat-label">Favorites</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item">
                                    <i class="fas fa-phone text-success fa-2x"></i>
                                    <div class="stat-value">{{ $property->inquiries_count }}</div>
                                    <div class="stat-label">Inquiries</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Virtual Tour -->
                @if($property->virtualTours->count() > 0)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Virtual Tour</h6>
                            @foreach($property->virtualTours as $tour)
                                <a href="{{ $tour->url }}" class="btn btn-outline-primary w-100 mb-2" target="_blank">
                                    <i class="fas fa-vr-cardboard me-2"></i>{{ $tour->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Floor Plans -->
                @if($property->floorPlans->count() > 0)
                    <div class="card mt-3">
                        <div class="card-body">
                            <h6 class="card-title">Floor Plans</h6>
                            @foreach($property->floorPlans as $floorPlan)
                                <div class="mb-2">
                                    <small class="text-muted">{{ $floorPlan->title }}</small>
                                    <img src="{{ $floorPlan->getUrlAttribute() }}" class="w-100"
                                        style="max-height: 200px; object-fit: contain;" alt="{{ $floorPlan->title }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Similar Properties -->
        @if($similarProperties->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <h3 class="mb-3">Similar Properties</h3>
                    <div class="row">
                        @foreach($similarProperties as $similarProperty)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100">
                                    @if($similarProperty->media->first())
                                        <img src="{{ $similarProperty->media->first()->getUrlAttribute() }}" class="card-img-top"
                                            style="height: 200px; object-fit: cover;" alt="{{ $similarProperty->title }}">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light"
                                            style="height: 200px;">
                                            <i class="fas fa-home fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title">{{ Str::limit($similarProperty->title, 30) }}</h6>
                                        <p class="card-text text-muted small">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $similarProperty->location?->city ?? $similarProperty->city }}
                                        </p>
                                        <h5 class="text-primary">
                                            {{ $similarProperty->pricing?->formatted_price ?? number_format($similarProperty->price ?? 0, 2) . ' ' . ($similarProperty->currency ?? 'USD') }}
                                        </h5>
                                        <a href="{{ route('properties.show', $similarProperty) }}"
                                            class="btn btn-primary btn-sm w-100">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Contact Agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="contactContent">
                        <!-- Contact form will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Share Modal -->
    <div class="modal fade" id="shareModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share Property</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="shareContent">
                        <!-- Share options will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom CSS -->
    <style>
        .alert-purple {
            background-color: #6f42c1;
            color: white;
            border: none;
        }
        
        .alert-purple .alert-link {
            color: #fff;
            text-decoration: underline;
        }
        
        .property-description {
            line-height: 1.8;
        }
        
        .card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .bg-opacity-10 {
            background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
        }
        
        .badge.bg-purple {
            background-color: #6f42c1 !important;
        }
        
        .rounded-circle {
            border-radius: 50% !important;
        }
        
        .text-primary {
            color: #0d6efd !important;
        }
        
        .text-success {
            color: #198754 !important;
        }
        
        .text-info {
            color: #0dcaf0 !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .text-danger {
            color: #dc3545 !important;
        }
        
        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
        
        .border-0 {
            border: none !important;
        }
        
        .h-100 {
            height: 100% !important;
        }
        
        .p-3 {
            padding: 1rem !important;
        }
        
        .p-4 {
            padding: 1.5rem !important;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem !important;
        }
        
        .mb-3 {
            margin-bottom: 1rem !important;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
        
        .mt-3 {
            margin-top: 1rem !important;
        }
        
        .mt-4 {
            margin-top: 1.5rem !important;
        }
        
        .me-2 {
            margin-right: 0.5rem !important;
        }
        
        .me-3 {
            margin-right: 1rem !important;
        }
        
        .g-3 > * {
            padding: 0.5rem;
        }
        
        .fa-2x {
            font-size: 2rem;
        }
        
        .fs-5 {
            font-size: 1.25rem !important;
        }
        
        .fs-6 {
            font-size: 1rem !important;
        }
        
        .small {
            font-size: 0.875em;
        }
    </style>
    
    <!-- Custom JavaScript -->
    <script>
        // Image gallery functionality
        let currentImageIndex = 0;
        const images = @json($property->media->where('media_type', 'image')->pluck('url')->toArray());

        function changeImage(direction) {
            currentImageIndex += direction;
            if (currentImageIndex < 0) currentImageIndex = images.length - 1;
            if (currentImageIndex >= images.length) currentImageIndex = 0;

            document.getElementById('mainImage').src = images[currentImageIndex];
            updateThumbnails();
        }

        function setMainImage(src, index) {
            document.getElementById('mainImage').src = src;
            currentImageIndex = index;
            updateThumbnails();
        }

        function updateThumbnails() {
            const thumbnails = document.querySelectorAll('.thumbnail-image');
            thumbnails.forEach((thumb, index) => {
                if (index === currentImageIndex) {
                    thumb.classList.add('border-primary');
                } else {
                    thumb.classList.remove('border-primary');
                }
            });
        }

        // Contact agent
        function contactAgent(method) {
            let contentHtml = '';

            switch (method) {
                case 'phone':
                    contentHtml = `
                    <div class="text-center">
                        <i class="fas fa-phone fa-3x text-primary mb-3"></i>
                        <h5>Call Agent</h5>
                        <p>{{ $property->agent->phone ?? 'Contact number not available' }}</p>
                        <button type="button" class="btn btn-primary" onclick="window.location.href='tel:{{ $property->agent->phone ?? '' }}'">
                            <i class="fas fa-phone me-2"></i>Call Now
                        </button>
                    </div>
                `;
                    break;
                case 'email':
                    contentHtml = `
                    <form onsubmit="sendEmail(event)">
                        <div class="mb-3">
                            <label for="emailSubject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="emailSubject" value="Inquiry about Property: {{ $property->title }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="emailMessage" class="form-label">Message</label>
                            <textarea class="form-control" id="emailMessage" rows="5" required>I'm interested in this property. Please provide more information.</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-envelope me-2"></i>Send Email
                        </button>
                    </form>
                `;
                    break;
                case 'whatsapp':
                    const message = encodeURIComponent('Hi, I\'m interested in this property: ' + window.location.href);
                    contentHtml = `
                    <div class="text-center">
                        <i class="fab fa-whatsapp fa-3x text-success mb-3"></i>
                        <h5>WhatsApp</h5>
                        <p>Click below to open WhatsApp and send a message about this property.</p>
                        <a href="https://wa.me/?text=${message}" class="btn btn-success" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>Open WhatsApp
                        </a>
                    </div>
                `;
                    break;
            }

            document.getElementById('contactContent').innerHTML = contentHtml;
            
            // Show modal (using Bootstrap 5)
            const modal = new bootstrap.Modal(document.getElementById('contactModal'));
            modal.show();
        }

        function sendEmail(event) {
            event.preventDefault();
            showToast('Email sent successfully!', 'success');
            const modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
            modal.hide();
        }

        // Toggle favorite
        function toggleFavorite() {
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
                        showToast(data.message, 'success');
                    }
                })
                .catch(error => {
                    showToast('Feature coming soon!', 'info');
                });
        }

        // Toggle comparison
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
                    if (data.success) {
                        showToast(data.message, 'success');
                    } else {
                        showToast(data.message, 'warning');
                    }
                })
                .catch(error => {
                    showToast('Feature coming soon!', 'info');
                });
        }

        // Share property
        function shareProperty() {
            const content = document.getElementById('shareContent');

            content.innerHTML = `
            <div class="d-grid gap-2">
                <a href="mailto:?subject={{ urlencode('Check out this property: ' . $property->title) }}&body={{ urlencode('I found this amazing property: ' . route('properties.show', $property)) }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-envelope me-2"></i>Email
                </a>
                <a href="https://wa.me/?text={{ urlencode('Check out this property: ' . route('properties.show', $property)) }}" 
                   class="btn btn-success" target="_blank">
                    <i class="fab fa-whatsapp me-2"></i>WhatsApp
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('properties.show', $property)) }}" 
                   class="btn btn-primary" target="_blank">
                    <i class="fab fa-facebook me-2"></i>Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode('Check out this property: ' . $property->title) }}&url={{ urlencode(route('properties.show', $property)) }}" 
                   class="btn btn-info" target="_blank">
                    <i class="fab fa-twitter me-2"></i>Twitter
                </a>
                <button type="button" class="btn btn-outline-secondary" onclick="copyLink()">
                    <i class="fas fa-link me-2"></i>Copy Link
                </button>
            </div>
        `;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('shareModal'));
            modal.show();
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                showToast('Link copied to clipboard!', 'success');
            });
        }

        // Schedule viewing
        function scheduleViewing() {
            showToast('Viewing scheduling feature coming soon!', 'info');
        }

        // Show toast notification
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
            toast.style.zIndex = '9999';
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Initialize map if coordinates are available
        @if($property->location?->latitude && $property->location?->longitude)
            document.addEventListener('DOMContentLoaded', function () {
                const mapContainer = document.getElementById('propertyMap');
                if (mapContainer) {
                    mapContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 bg-light"><div class="text-center"><i class="fas fa-map fa-3x text-muted mb-2"></div><p class="text-muted">Map will be displayed here</p></div>';
                }
            });
        @endif

        // Record property view
        document.addEventListener('DOMContentLoaded', function () {
            fetch(`/properties/{{ $property->id }}/view`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
            });
        });
    </script>
@endsection
