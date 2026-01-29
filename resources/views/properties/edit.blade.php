@extends('layouts.app')

@section('title', 'Edit Property: ' . $property->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">Edit Property</h1>
                    <p class="text-muted mb-0">Update property information for: {{ $property->title }}</p>
                </div>
                <div>
                    <a href="{{ route('optimized.properties.show', $property) }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eye me-1"></i>View Property
                    </a>
                    <a href="{{ route('optimized.properties.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-list me-1"></i>Back to List
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('optimized.properties.update', $property) }}" enctype="multipart/form-data" id="propertyForm">
        @csrf
        @method('PUT')

        <!-- Progress Steps -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="progress-steps">
                    <div class="d-flex justify-content-between">
                        <div class="step active" data-step="1">
                            <div class="step-number">1</div>
                            <div class="step-title">Basic Info</div>
                        </div>
                        <div class="step" data-step="2">
                            <div class="step-number">2</div>
                            <div class="step-title">Location</div>
                        </div>
                        <div class="step" data-step="3">
                            <div class="step-number">3</div>
                            <div class="step-title">Details</div>
                        </div>
                        <div class="step" data-step="4">
                            <div class="step-number">4</div>
                            <div class="step-title">Media</div>
                        </div>
                        <div class="step" data-step="5">
                            <div class="step-number">5</div>
                            <div class="step-title">Pricing</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Basic Information -->
        <div class="step-content" data-step="1">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Basic Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="title" class="form-label">Property Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $property->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="property_type_id" class="form-label">Property Type *</label>
                            <select class="form-select @error('property_type_id') is-invalid @enderror" 
                                    id="property_type_id" name="property_type_id" required>
                                <option value="">Select Property Type</option>
                                @foreach($propertyTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('property_type_id', $property->property_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('property_type_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="listing_type" class="form-label">Listing Type *</label>
                            <select class="form-select @error('listing_type') is-invalid @enderror" 
                                    id="listing_type" name="listing_type" required>
                                <option value="">Select Listing Type</option>
                                <option value="sale" {{ old('listing_type', $property->listing_type) == 'sale' ? 'selected' : '' }}>For Sale</option>
                                <option value="rent" {{ old('listing_type', $property->listing_type) == 'rent' ? 'selected' : '' }}>For Rent</option>
                                <option value="lease" {{ old('listing_type', $property->listing_type) == 'lease' ? 'selected' : '' }}>For Lease</option>
                            </select>
                            @error('listing_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status *</label>
                            <select class="form-select @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="draft" {{ old('status', $property->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="active" {{ old('status', $property->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $property->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="sold" {{ old('status', $property->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                                <option value="rented" {{ old('status', $property->status) == 'rented' ? 'selected' : '' }}>Rented</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description', $property->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Provide a detailed description of your property (minimum 50 characters)</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                       value="1" {{ old('featured', $property->featured) ? 'checked' : '' }}>
                                <label class="form-check-label" for="featured">
                                    Featured Property
                                </label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="premium" name="premium" 
                                       value="1" {{ old('premium', $property->premium) ? 'checked' : '' }}>
                                <label class="form-check-label" for="premium">
                                    Premium Listing
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Location Information -->
        <div class="step-content" data-step="2" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Location Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="address" class="form-label">Street Address *</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address', $property->location?->address ?? $property->address) }}" required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="city" class="form-label">City *</label>
                            <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                   id="city" name="city" value="{{ old('city', $property->location?->city ?? $property->city) }}" required>
                            @error('city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="state" class="form-label">State/Province</label>
                            <input type="text" class="form-control @error('state') is-invalid @enderror" 
                                   id="state" name="state" value="{{ old('state', $property->location?->state ?? $property->state) }}">
                            @error('state')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="country" class="form-label">Country *</label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country', $property->location?->country ?? $property->country) }}" required>
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="postal_code" class="form-label">Postal Code</label>
                            <input type="text" class="form-control @error('postal_code') is-invalid @enderror" 
                                   id="postal_code" name="postal_code" value="{{ old('postal_code', $property->location?->postal_code ?? $property->postal_code) }}">
                            @error('postal_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="neighborhood" class="form-label">Neighborhood</label>
                            <input type="text" class="form-control @error('neighborhood') is-invalid @enderror" 
                                   id="neighborhood" name="neighborhood" value="{{ old('neighborhood', $property->location?->neighborhood) }}">
                            @error('neighborhood')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="district" class="form-label">District</label>
                            <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                   id="district" name="district" value="{{ old('district', $property->location?->district) }}">
                            @error('district')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                   id="latitude" name="latitude" value="{{ old('latitude', $property->location?->latitude ?? $property->latitude) }}">
                            @error('latitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                   id="longitude" name="longitude" value="{{ old('longitude', $property->location?->longitude ?? $property->longitude) }}">
                            @error('longitude')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-outline-secondary" onclick="getCurrentLocation()">
                                <i class="fas fa-map-marker-alt me-2"></i>Get Current Location
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Property Details -->
        <div class="step-content" data-step="3" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Property Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="bedrooms" class="form-label">Bedrooms</label>
                            <input type="number" class="form-control @error('bedrooms') is-invalid @enderror" 
                                   id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $property->details->bedrooms) }}" min="0">
                            @error('bedrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="bathrooms" class="form-label">Bathrooms</label>
                            <input type="number" class="form-control @error('bathrooms') is-invalid @enderror" 
                                   id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $property->details->bathrooms) }}" min="0">
                            @error('bathrooms')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="floors" class="form-label">Floors</label>
                            <input type="number" class="form-control @error('floors') is-invalid @enderror" 
                                   id="floors" name="floors" value="{{ old('floors', $property->details->floors) }}" min="0">
                            @error('floors')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="parking_spaces" class="form-label">Parking Spaces</label>
                            <input type="number" class="form-control @error('parking_spaces') is-invalid @enderror" 
                                   id="parking_spaces" name="parking_spaces" value="{{ old('parking_spaces', $property->details->parking_spaces) }}" min="0">
                            @error('parking_spaces')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="year_built" class="form-label">Year Built</label>
                            <input type="number" class="form-control @error('year_built') is-invalid @enderror" 
                                   id="year_built" name="year_built" value="{{ old('year_built', $property->details->year_built) }}" 
                                   min="1900" max="{{ date('Y') }}">
                            @error('year_built')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="area" class="form-label">Living Area *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('area') is-invalid @enderror" 
                                       id="area" name="area" value="{{ old('area', $property->details->area) }}" required min="1">
                                <select class="form-select @error('area_unit') is-invalid @enderror" 
                                        id="area_unit" name="area_unit" style="max-width: 100px;" required>
                                    <option value="sq_m" {{ old('area_unit', $property->details->area_unit) == 'sq_m' ? 'selected' : '' }}>m²</option>
                                    <option value="sq_ft" {{ old('area_unit', $property->details->area_unit) == 'sq_ft' ? 'selected' : '' }}>ft²</option>
                                </select>
                            </div>
                            @error('area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('area_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="land_area" class="form-label">Land Area</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('land_area') is-invalid @enderror" 
                                       id="land_area" name="land_area" value="{{ old('land_area', $property->details->land_area) }}" min="1">
                                <select class="form-select @error('land_area_unit') is-invalid @enderror" 
                                        id="land_area_unit" name="land_area_unit" style="max-width: 100px;">
                                    <option value="sq_m" {{ old('land_area_unit', $property->details->land_area_unit) == 'sq_m' ? 'selected' : '' }}>m²</option>
                                    <option value="sq_ft" {{ old('land_area_unit', $property->details->land_area_unit) == 'sq_ft' ? 'selected' : '' }}>ft²</option>
                                    <option value="acre" {{ old('land_area_unit', $property->details->land_area_unit) == 'acre' ? 'selected' : '' }}>acre</option>
                                    <option value="hectare" {{ old('land_area_unit', $property->details->land_area_unit) == 'hectare' ? 'selected' : '' }}>hectare</option>
                                </select>
                            </div>
                            @error('land_area')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @error('land_area_unit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities and Features -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Amenities & Features</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Amenities</h6>
                            <div class="amenities-grid">
                                @foreach($amenities as $amenity)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="amenity_{{ $amenity->id }}" 
                                               name="amenities[]" value="{{ $amenity->id }}" 
                                               {{ in_array($amenity->id, old('amenities', $property->amenities->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="amenity_{{ $amenity->id }}">
                                            @if($amenity->icon)
                                                <i class="{{ $amenity->icon }} me-1"></i>
                                            @endif
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Special Features</h6>
                            <div class="features-grid">
                                @foreach($features as $feature)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_{{ $feature->id }}" 
                                               name="features[]" value="{{ $feature->id }}" 
                                               {{ in_array($feature->id, old('features', $property->features->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_{{ $feature->id }}">
                                            @if($feature->icon)
                                                <i class="{{ $feature->icon }} me-1"></i>
                                            @endif
                                            {{ $feature->name }}
                                            @if($feature->is_premium)
                                                <span class="badge bg-warning text-dark ms-1">Premium</span>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4: Media Upload -->
        <div class="step-content" data-step="4" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Property Images</h5>
                </div>
                <div class="card-body">
                    <!-- Existing Images -->
                    @if($property->media->where('media_type', 'image')->count() > 0)
                        <h6 class="mb-3">Current Images</h6>
                        <div class="row g-2 mb-4">
                            @foreach($property->media->where('media_type', 'image') as $media)
                                <div class="col-md-3">
                                    <div class="position-relative">
                                        <img src="{{ $media->getUrlAttribute() }}" 
                                             class="w-100 rounded" 
                                             style="height: 120px; object-fit: cover;"
                                             alt="Property image">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                onclick="removeExistingImage({{ $media->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($media->is_primary)
                                            <span class="badge bg-primary position-absolute top-0 start-0 m-1">Primary</span>
                                        @else
                                            <button type="button" class="btn btn-primary btn-sm position-absolute top-0 start-0 m-1" 
                                                    onclick="setPrimaryImage({{ $media->id }})">
                                                Set Primary
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Upload New Images -->
                    <h6 class="mb-3">Upload New Images</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="images" class="form-label">Add Images</label>
                                <input type="file" class="form-control @error('images') is-invalid @enderror" 
                                       id="images" name="images[]" multiple accept="image/*">
                                @error('images')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Upload high-quality images (JPEG, PNG, GIF). Maximum file size: 10MB each.
                                </small>
                            </div>
                            <div id="imagePreview" class="row g-2">
                                <!-- Image previews will be displayed here -->
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Photo Guidelines</h6>
                                <ul class="mb-0 small">
                                    <li>Upload at least 5 high-quality photos</li>
                                    <li>Include exterior and interior shots</li>
                                    <li>Show all main rooms and features</li>
                                    <li>Ensure good lighting and clear focus</li>
                                    <li>Avoid heavy filters or excessive editing</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Documents</h5>
                </div>
                <div class="card-body">
                    <!-- Existing Documents -->
                    @if($property->media->where('media_type', 'document')->count() > 0)
                        <h6 class="mb-3">Current Documents</h6>
                        <div class="list-group mb-4">
                            @foreach($property->media->where('media_type', 'document') as $document)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-file-alt me-2"></i>{{ $document->file_name }}
                                        <small class="text-muted ms-2">{{ $document->formatted_file_size }}</small>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm" 
                                            onclick="removeExistingDocument({{ $document->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Upload New Documents -->
                    <h6 class="mb-3">Upload New Documents</h6>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="documents" class="form-label">Add Documents</label>
                                <input type="file" class="form-control @error('documents') is-invalid @enderror" 
                                       id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx">
                                @error('documents')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Upload property documents (PDF, DOC, DOCX, XLS, XLSX). Maximum file size: 10MB each.
                                </small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-file-alt me-2"></i>Document Types</h6>
                                <ul class="mb-0 small">
                                    <li>Property deed</li>
                                    <li>Building permits</li>
                                    <li>Floor plans</li>
                                    <li>Energy certificates</li>
                                    <li>Inspection reports</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt<|code_suffix|>                <ustin>
                    <div class="card-header">
                        <h5 class="card-title mb-0">Virtual Tour</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="virtual_tour_url" class="form-label">Virtual Tour URL</label>
                            <input type="url" class="form-control @error('virtual_tour_url') is-invalid @enderror" 
                                   id="virtual_tour_url" name="virtual_tour_url" value="{{ old('virtual_tour_url') }}"
                                   placeholder="https://example.com/virtual-tour">
                            @error('virtual_tour_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                Add a link to your virtual tour (YouTube, Matterport, etc.)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 5: Pricing -->
        <div class="step-content" data-step="5" style="display: none;">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pricing Information</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="price" class="form-label">Price *</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ old('currency', $property->price?->currency ?? $property->currency) }}</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                       id="price" name="price" value="{{ old('price', $property->price?->price ?? $property->price) }}" required min="0" step="0.01">
                            </div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="currency" class="form-label">Currency *</label>
                            <select class="form-select @error('currency') is-invalid @enderror" 
                                    id="currency" name="currency" required>
                                <option value="SAR" {{ old('currency', $property->price?->currency ?? $property->currency) == 'SAR' ? 'selected' : '' }}>SAR</option>
                                <option value="USD" {{ old('currency', $property->price?->currency ?? $property->currency) == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ old('currency', $property->price?->currency ?? $property->currency) == 'EUR' ? 'selected' : '' }}>EUR</option>
                                <option value="GBP" {{ old('currency', $property->price?->currency ?? $property->currency) == 'GBP' ? 'selected' : '' }}>GBP</option>
                                <option value="AED" {{ old('currency', $property->price?->currency ?? $property->currency) == 'AED' ? 'selected' : '' }}>AED</option>
                            </select>
                            @error('currency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="payment_frequency" class="form-label">Payment Frequency</label>
                            <select class="form-select @error('payment_frequency') is-invalid @enderror" 
                                    id="payment_frequency" name="payment_frequency">
                                <option value="">Select Frequency</option>
                                <option value="monthly" {{ old('payment_frequency', $property->price?->payment_frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                <option value="quarterly" {{ old('payment_frequency', $property->price?->payment_frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                <option value="annually" {{ old('payment_frequency', $property->price?->payment_frequency) == 'annually' ? 'selected' : '' }}>Annually</option>
                            </select>
                            @error('payment_frequency')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_negotiable" name="is_negotiable" 
                                       value="1" {{ old('is_negotiable', $property->price?->is_negotiable) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_negotiable">
                                    Price is negotiable
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includes_vat" name="includes_vat" 
                                       value="1" {{ old('includes_vat', $property->price?->includes_vat) ? 'checked' : '' }}>
                                <label class="form-check-label" for="includes_vat">
                                    Price includes VAT
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="service_charges" class="form-label">Service Charges</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ old('currency', $property->price?->currency ?? $property->currency) }}</span>
                                <input type="number" class="form-control @error('service_charges') is-invalid @enderror" 
                                       id="service_charges" name="service_charges" value="{{ old('service_charges', $property->price?->service_charges) }}" 
                                       min="0" step="0.01">
                            </div>
                            @error('service_charges')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="maintenance_fees" class="form-label">Maintenance Fees</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ old('currency', $property->price?->currency ?? $property->currency) }}</span>
                                <input type="number" class="form-control @error('maintenance_fees') is-invalid @enderror" 
                                       id="maintenance_fees" name="maintenance_fees" value="{{ old('maintenance_fees', $property->price?->maintenance_fees) }}" 
                                       min="0" step="0.01">
                            </div>
                            @error('maintenance_fees')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Price Change Reason -->
                        @if($property->price?->price != old('price', $property->price?->price ?? $property->price))
                            <div class="col-12">
                                <label for="price_change_reason" class="form-label">Price Change Reason</label>
                                <textarea class="form-control @error('price_change_reason') is-invalid @enderror" 
                                          id="price_change_reason" name="price_change_reason" rows="3" 
                                          placeholder="Please explain why you're changing the price">{{ old('price_change_reason') }}</textarea>
                                @error('price_change_reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" id="prevBtn" onclick="changeStep(-1)" style="display: none;">
                        <i class="fas fa-arrow-left me-2"></i>Previous
                    </button>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-outline-primary me-2" onclick="saveDraft()">
                            <i class="fas fa-save me-2"></i>Save Draft
                        </button>
                        <button type="button" class="btn btn-primary" id="nextBtn" onclick="changeStep(1)">
                            Next<i class="fas fa-arrow-right ms-2"></i>
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                            <i class="fas fa-check me-2"></i>Update Property
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.progress-steps {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
}

.step {
    text-align: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 20px;
    right: -50%;
    width: 100%;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}

.step.active:not(:last-child)::after {
    background: #007bff;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 8px;
    font-weight: bold;
    position: relative;
    z-index: 2;
}

.step.active .step-number {
    background: #007bff;
    color: white;
}

.step.completed .step-number {
    background: #28a745;
    color: white;
}

.step-title {
    font-size: 0.875rem;
    color: #6c757d;
}

.step.active .step-title {
    color: #007bff;
    font-weight: 600;
}

.step-content {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.amenities-grid, .features-grid {
    max-height: 300px;
    overflow-y: auto;
    columns: 2;
    column-gap: 20px;
}

.amenities-grid .form-check, .features-grid .form-check {
    break-inside: avoid;
    margin-bottom: 8px;
}

.image-preview-item {
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}

.image-preview-item .remove-btn {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
let currentStep = 1;
const totalSteps = 5;

function changeStep(direction) {
    // Validate current step before moving forward
    if (direction > 0 && !validateStep(currentStep)) {
        return;
    }

    // Hide current step
    document.querySelector(`.step-content[data-step="${currentStep}"]`).style.display = 'none';
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.remove('active');

    // Update current step
    currentStep += direction;

    // Show new step
    document.querySelector(`.step-content[data-step="${currentStep}"]`).style.display = 'block';
    document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');

    // Mark previous steps as completed
    for (let i = 1; i < currentStep; i++) {
        document.querySelector(`.step[data-step="${i}"]`).classList.add('completed');
    }

    // Update navigation buttons
    updateNavigationButtons();
}

function validateStep(step) {
    const stepContent = document.querySelector(`.step-content[data-step="${step}"]`);
    const requiredFields = stepContent.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        showToast('Please fill in all required fields', 'warning');
    }

    return isValid;
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');

    // Show/hide previous button
    prevBtn.style.display = currentStep === 1 ? 'none' : 'block';

    // Show/hide next and submit buttons
    if (currentStep === totalSteps) {
        nextBtn.style.display = 'none';
        submitBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'block';
        submitBtn.style.display = 'none';
    }
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude;
                document.getElementById('longitude').value = position.coords.longitude;
                showToast('Location obtained successfully!', 'success');
            },
            function(error) {
                showToast('Unable to get your location. Please enter manually.', 'error');
            }
        );
    } else {
        showToast('Geolocation is not supported by your browser.', 'error');
    }
}

function saveDraft() {
    // Implement draft saving logic
    showToast('Draft saved successfully!', 'success');
}

function removeExistingImage(mediaId) {
    if (confirm('Are you sure you want to remove this image?')) {
        fetch(`/properties/{{ $property->id }}/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Failed to remove image', 'error');
            }
        });
    }
}

function setPrimaryImage(mediaId) {
    fetch(`/properties/{{ $property->id }}/media/${mediaId}/set-primary`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast('Failed to set primary image', 'error');
        }
    });
}

function removeExistingDocument(mediaId) {
    if (confirm('Are you sure you want to remove this document?')) {
        fetch(`/properties/{{ $property->id }}/media/${mediaId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Failed to remove document', 'error');
            }
        });
    }
}

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

// Image preview
document.getElementById('images').addEventListener('change', function(e) {
    const files = Array.from(e.target.files);
    const previewContainer = document.getElementById('imagePreview');
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewItem = document.createElement('div');
                previewItem.className = 'col-md-3';
                previewItem.innerHTML = `
                    <div class="image-preview-item">
                        <img src="${e.target.result}" alt="Preview ${index + 1}">
                        <button type="button" class="remove-btn" onclick="removeImage(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewContainer.appendChild(previewItem);
            };
            reader.readAsDataURL(file);
        }
    });
});

function removeImage(button) {
    button.closest('.col-md-3').remove();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateNavigationButtons();
});
</script>
@endpush
