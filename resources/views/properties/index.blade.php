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
                    <a href="{{ route('properties.create') }}" class="btn btn-primary">
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
                                    @foreach($propertyTypes as $type)
                                        <option value="{{ $type->slug }}" {{ request('property_type') == $type->slug ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
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

                            <!-- Price Range -->
                            <div class="col-md-2">
                                <label for="max_price" class="form-label">Max Price</label>
                                <input type="number" class="form-control" id="max_price" name="max_price" 
                                       value="{{ request('max_price') }}" placeholder="Max price">
                            </div>

                            <!-- Bedrooms -->
                            <div class="col-md-1">
                                <label for="bedrooms" class="form-label">Beds</label>
                                <select class="form-select" id="bedrooms" name="bedrooms">
                                    <option value="">Any</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Search Button -->
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="fas fa-search me-1"></i>Search
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleAdvancedFilters()">
                                        <i class="fas fa-sliders-h"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Filters (Hidden by default) -->
                        <div id="advancedFilters" class="row g-3 mt-3" style="display: none;">
                            <div class="col-md-3">
                                <label for="min_price" class="form-label">Min Price</label>
                                <input type="number" class="form-control" id="min_price" name="min_price" 
                                       value="{{ request('min_price') }}" placeholder="Min price">
                            </div>
                            <div class="col-md-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="{{ request('city') }}" placeholder="City">
                            </div>
                            <div class="col-md-3">
                                <label for="min_area" class="form-label">Min Area (m²)</label>
                                <input type="number" class="form-control" id="min_area" name="min_area" 
                                       value="{{ request('min_area') }}" placeholder="Min area">
                            </div>
                            <div class="col-md-3">
                                <label for="max_area" class="form-label">Max Area (m²)</label>
                                <input type="number" class="form-control" id="max_area" name="max_area" 
                                       value="{{ request('max_area') }}" placeholder="Max area">
                            </div>
                            <div class="col-md-3">
                                <label for="bathrooms" class="form-label">Bathrooms</label>
                                <select class="form-select" id="bathrooms" name="bathrooms">
                                    <option value="">Any</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ request('bathrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Features</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="featured" name="featured" 
                                           value="1" {{ request('featured') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featured">
                                        Featured
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="premium" name="premium" 
                                           value="1" {{ request('premium') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="premium">
                                        Premium
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="sort" class="form-label">Sort By</label>
                                <select class="form-select" id="sort" name="sort">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Latest</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="area" {{ request('sort') == 'area' ? 'selected' : '' }}>Area</option>
                                    <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>Most Viewed</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <a href="{{ route('optimized.properties.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i>Clear
                                    </a>
                                </div>
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
                        Showing {{ $properties->firstItem() }} - {{ $properties->lastItem() }} 
                        of {{ $properties->total() }} properties
                    </span>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setViewMode('grid')" id="gridViewBtn">
                        <i class="fas fa-th"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="setViewMode('list')" id="listViewBtn">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Properties Grid/List -->
    @if($properties->count() > 0)
        <div id="propertiesContainer" class="row">
            @foreach($properties as $property)
                <div class="col-lg-4 col-md-6 mb-4 property-item" data-view-mode="grid">
                    <div class="card h-100 property-card">
                        <!-- Property Image -->
                        <div class="position-relative">
                            @if($property->media->first())
                                <img src="{{ $property->media->first()->getUrlAttribute() }}" 
                                     class="card-img-top property-image" 
                                     alt="{{ $property->title }}"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                     style="height: 200px;">
                                    <i class="fas fa-home fa-3x text-muted"></i>
                                </div>
                            @endif
                            
                            <!-- Badges -->
                            <div class="position-absolute top-0 start-0 m-2">
                                @if($property->featured)
                                    <span class="badge bg-warning">Featured</span>
                                @endif
                                @if($property->premium)
                                    <span class="badge bg-danger">Premium</span>
                                @endif
                            </div>
                            
                            <!-- Actions -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <button type="button" class="btn btn-sm btn-light rounded-circle" 
                                        onclick="toggleFavorite({{ $property->id }})">
                                    <i class="far fa-heart" id="favorite-{{ $property->id }}"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light rounded-circle" 
                                        onclick="toggleComparison({{ $property->id }})">
                                    <i class="fas fa-balance-scale"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ Str::limit($property->title, 50) }}</h5>
                            <p class="card-text text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $property->location?->city ?? $property->city }}, {{ $property->location?->country ?? $property->country }}
                            </p>
                            
                            <div class="mb-2">
                                <span class="badge bg-light text-dark me-1">{{ $property->propertyType?->name ?? 'N/A' }}</span>
                                <span class="badge bg-primary">{{ ucfirst($property->listing_type) }}</span>
                            </div>

                            <!-- Property Features -->
                            @if($property->details)
                                <div class="row g-2 mb-3">
                                    @if($property->details->bedrooms)
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fas fa-bed me-1"></i>{{ $property->details->bedrooms }}
                                            </small>
                                        </div>
                                    @endif
                                    @if($property->details->bathrooms)
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fas fa-bath me-1"></i>{{ $property->details->bathrooms }}
                                            </small>
                                        </div>
                                    @endif
                                    @if($property->details->area)
                                        <div class="col-auto">
                                            <small class="text-muted">
                                                <i class="fas fa-ruler-combined me-1"></i>{{ $property->details->area }} m²
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Price -->
                            <div class="mt-auto">
                                <h4 class="text-primary mb-0">
                                    {{ number_format($property->price ?? 0, 2) }} {{ $property->currency ?? 'USD' }}
                                </h4>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-eye me-1"></i>{{ $property->views_count }} views
                                </small>
                                <a href="{{ route('properties.show', $property) }}" class="btn btn-primary btn-sm">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-12">
                {{ $properties->links() }}
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-home fa-3x text-muted mb-3"></i>
                    <h4>No properties found</h4>
                    <p class="text-muted">Try adjusting your search criteria or browse all properties.</p>
                    <a href="{{ route('optimized.properties.index') }}" class="btn btn-outline-primary">
                        Clear Filters
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Comparison Modal -->
<div class="modal fade" id="comparisonModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Property Comparison</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="comparisonContent">
                    <!-- Comparison content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.property-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.property-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.property-image {
    transition: transform 0.3s;
}
.property-card:hover .property-image {
    transform: scale(1.05);
}
.property-item[data-view-mode="list"] {
    display: none;
}
.property-item[data-view-mode="list"] .property-card {
    display: flex;
    flex-direction: row;
    height: auto;
}
.property-item[data-view-mode="list"] .card-img-top {
    width: 300px;
    height: 200px;
}
.property-item[data-view-mode="list"] .card-body {
    flex: 1;
}
</style>
@endpush

@push('scripts')
<script>
// Toggle advanced filters
function toggleAdvancedFilters() {
    const filters = document.getElementById('advancedFilters');
    filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
}

// View mode toggle
function setViewMode(mode) {
    const items = document.querySelectorAll('.property-item');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    items.forEach(item => {
        item.setAttribute('data-view-mode', mode);
    });
    
    if (mode === 'grid') {
        items.forEach(item => {
            item.className = 'col-lg-4 col-md-6 mb-4 property-item';
        });
        gridBtn.classList.add('btn-primary');
        gridBtn.classList.remove('btn-outline-secondary');
        listBtn.classList.remove('btn-primary');
        listBtn.classList.add('btn-outline-secondary');
    } else {
        items.forEach(item => {
            item.className = 'col-12 mb-3 property-item';
        });
        listBtn.classList.add('btn-primary');
        listBtn.classList.remove('btn-outline-secondary');
        gridBtn.classList.remove('btn-primary');
        gridBtn.classList.add('btn-outline-secondary');
    }
}

// Toggle favorite
function toggleFavorite(propertyId) {
    fetch(`/properties/${propertyId}/favorite/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const icon = document.getElementById(`favorite-${propertyId}`);
            if (data.is_favorited) {
                icon.classList.remove('far');
                icon.classList.add('fas', 'text-danger');
            } else {
                icon.classList.remove('fas', 'text-danger');
                icon.classList.add('far');
            }
        }
    });
}

// Toggle comparison
function toggleComparison(propertyId) {
    fetch(`/properties/compare/add`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            property_id: propertyId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadComparison();
            showToast('Property added to comparison', 'success');
        } else {
            showToast(data.message, 'warning');
        }
    });
}

// Load comparison
function loadComparison() {
    fetch('/properties/compare')
    .then(response => response.text())
    .then(html => {
        document.getElementById('comparisonContent').innerHTML = html;
    });
}

// Show toast notification
function showToast(message, type = 'info') {
    // Simple toast implementation - you might want to use a proper toast library
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} position-fixed top-0 end-0 m-3`;
    toast.style.zIndex = '9999';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Set default view mode
    setViewMode('grid');
    
    // Load comparison if any items exist
    loadComparison();
});
</script>
@endpush
