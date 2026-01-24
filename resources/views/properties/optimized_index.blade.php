@extends('layouts.app')

@section('title', 'Properties - Optimized')

@section('head')
    @parent
    <style>
        /* Optimized CSS for better performance */
        .property-card {
            transition: transform 0.2s ease;
        }
        .property-card:hover {
            transform: translateY(-2px);
        }
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }
        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
        .lazy-image {
            opacity: 0;
            transition: opacity 0.3s;
        }
        .lazy-image.loaded {
            opacity: 1;
        }
    </style>
@endsection

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
                                    <button type="submit" class="btn btn-primary flex-fill" id="searchBtn">
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

    <!-- Loading Indicator -->
    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading properties...</p>
    </div>

    <!-- Properties Grid/List -->
    <div id="propertiesContainer" class="row">
        @if($properties->count() > 0)
            @foreach($properties as $property)
                <div class="col-lg-4 col-md-6 mb-4 property-item" data-view-mode="grid">
                    <div class="card h-100 property-card">
                        <!-- Property Image -->
                        <div class="position-relative">
                            <div class="property-image-container" style="height: 200px; overflow: hidden;">
                                @if($property->media->first())
                                    <img data-src="{{ $property->media->first()->getUrlAttribute() }}" 
                                         class="card-img-top property-image lazy-image" 
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
                            
                            <!-- Actions -->
                            <div class="position-absolute top-0 end-0 m-2">
                                <button type="button" class="btn btn-sm btn-light rounded-circle" 
                                        onclick="toggleFavorite({{ $property->id }})" data-property-id="{{ $property->id }}">
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
                                {{ $property->location?->city ?? 'N/A' }}, {{ $property->location?->country ?? 'N/A' }}
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
    @if($properties->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-center">
                    {{ $properties->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
    @parent
    <script>
        // Lazy Loading for images
        document.addEventListener('DOMContentLoaded', function() {
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src;
                            img.classList.add('loaded');
                            observer.unobserve(img);
                        }
                    });
                });

                document.querySelectorAll('.lazy-image').forEach(img => {
                    imageObserver.observe(img);
                });
            } else {
                // Fallback for browsers that don't support IntersectionObserver
                document.querySelectorAll('.lazy-image').forEach(img => {
                    img.src = img.dataset.src;
                    img.classList.add('loaded');
                });
            }
        });

        // Toggle advanced filters
        function toggleAdvancedFilters() {
            const filters = document.getElementById('advancedFilters');
            filters.style.display = filters.style.display === 'none' ? 'block' : 'none';
        }

        // Set view mode
        function setViewMode(mode) {
            const container = document.getElementById('propertiesContainer');
            const items = container.querySelectorAll('.property-item');
            
            items.forEach(item => {
                if (mode === 'list') {
                    item.classList.remove('col-lg-4', 'col-md-6');
                    item.classList.add('col-12');
                } else {
                    item.classList.remove('col-12');
                    item.classList.add('col-lg-4', 'col-md-6');
                }
            });

            // Update button states
            document.getElementById('gridViewBtn').classList.toggle('active', mode === 'grid');
            document.getElementById('listViewBtn').classList.toggle('active', mode === 'list');
        }

        // Toggle favorite with AJAX
        function toggleFavorite(propertyId) {
            const btn = document.querySelector(`[data-property-id="${propertyId}"]`);
            const icon = btn.querySelector('i');
            
            // Show loading state
            btn.disabled = true;
            icon.classList.add('fa-spin');
            
            fetch(`/api/properties/${propertyId}/favorite/toggle`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                icon.classList.remove('fa-spin');
                if (data.is_favorited) {
                    icon.classList.remove('far');
                    icon.classList.add('fas', 'text-danger');
                } else {
                    icon.classList.remove('fas', 'text-danger');
                    icon.classList.add('far');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                icon.classList.remove('fa-spin');
            })
            .finally(() => {
                btn.disabled = false;
            });
        }

        // Toggle comparison
        function toggleComparison(propertyId) {
            // Implement comparison logic
            console.log('Toggle comparison for property:', propertyId);
        }

        // Search form with AJAX
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const loadingIndicator = document.getElementById('loadingIndicator');
            const container = document.getElementById('propertiesContainer');
            
            // Show loading
            loadingIndicator.style.display = 'block';
            container.style.display = 'none';
            
            // Get form data
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            
            // Fetch results
            fetch(`${window.location.pathname}?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(html => {
                // Update container with new content
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById('propertiesContainer');
                
                if (newContainer) {
                    container.innerHTML = newContainer.innerHTML;
                    
                    // Reinitialize lazy loading for new images
                    const images = container.querySelectorAll('.lazy-image');
                    images.forEach(img => {
                        if ('IntersectionObserver' in window) {
                            imageObserver.observe(img);
                        } else {
                            img.src = img.dataset.src;
                            img.classList.add('loaded');
                        }
                    });
                }
                
                // Hide loading
                loadingIndicator.style.display = 'none';
                container.style.display = 'flex';
                
                // Update URL
                history.pushState({}, '', `${window.location.pathname}?${params}`);
            })
            .catch(error => {
                console.error('Error:', error);
                loadingIndicator.style.display = 'none';
                container.style.display = 'flex';
            });
        });

        // Debounce search input
        let searchTimeout;
        document.getElementById('q').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('searchForm').dispatchEvent(new Event('submit'));
            }, 500);
        });
    </script>
@endsection
