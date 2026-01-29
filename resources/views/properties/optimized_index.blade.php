@extends('layouts.app')

@section('title', 'Properties - Optimized')

@section('head')
    @parent
    <style>
        /* Modern Tailwind-inspired styling */
        .property-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
        }
        .property-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
        .filter-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .property-stats {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1rem;
        }
    </style>
@endsection

@section('content')
<div class="container py-6">
    <!-- Modern Page Header -->
    <div class="page-header mb-6">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold mb-3">Discover Your Dream Property</h1>
                <p class="lead mb-0 opacity-90">Browse our comprehensive collection of premium properties</p>
            </div>
            <div class="col-md-4 text-md-end">
                @auth
                    <a href="{{ route('optimized.properties.create') }}" class="btn btn-light btn-lg">
                        <i class="fas fa-plus me-2"></i>Add Property
                    </a>
                @endauth
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row mt-4">
            <div class="col-md-3 col-6">
                <div class="property-stats text-center">
                    <h3 class="h2 mb-1">{{ $properties->total() }}</h3>
                    <small class="opacity-75">Total Properties</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="property-stats text-center">
                    <h3 class="h2 mb-1">{{ $propertyTypes->count() }}</h3>
                    <small class="opacity-75">Property Types</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="property-stats text-center">
                    <h3 class="h2 mb-1">24/7</h3>
                    <small class="opacity-75">Support</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="property-stats text-center">
                    <h3 class="h2 mb-1">5★</h3>
                    <small class="opacity-75">Service</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card filter-card">
                <div class="card-body p-4">
                    <form method="GET" action="{{ route('optimized.properties.index') }}" id="searchForm">
                        <div class="row g-3">
                            <!-- Basic Search -->
                            <div class="col-lg-4 col-md-6">
                                <label for="q" class="form-label fw-semibold">Search Properties</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">
                                        <i class="fas fa-search text-muted"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0" id="q" name="q" 
                                           value="{{ request('q') }}" placeholder="Keywords, location...">
                                </div>
                            </div>

                            <!-- Property Type -->
                            <div class="col-lg-2 col-md-6">
                                <label for="property_type" class="form-label fw-semibold">Property Type</label>
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
                            <div class="col-lg-2 col-md-6">
                                <label for="listing_type" class="form-label fw-semibold">Listing Type</label>
                                <select class="form-select" id="listing_type" name="listing_type">
                                    <option value="">All</option>
                                    <option value="sale" {{ request('listing_type') == 'sale' ? 'selected' : '' }}>Sale</option>
                                    <option value="rent" {{ request('listing_type') == 'rent' ? 'selected' : '' }}>Rent</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div class="col-lg-2 col-md-6">
                                <label for="max_price" class="form-label fw-semibold">Max Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0">$</span>
                                    <input type="number" class="form-control border-start-0" id="max_price" name="max_price" 
                                           value="{{ request('max_price') }}" placeholder="Max">
                                </div>
                            </div>

                            <!-- Bedrooms -->
                            <div class="col-lg-2 col-md-6">
                                <label for="bedrooms" class="form-label fw-semibold">Bedrooms</label>
                                <select class="form-select" id="bedrooms" name="bedrooms">
                                    <option value="">Any</option>
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ request('bedrooms') == $i ? 'selected' : '' }}>{{ $i }}+</option>
                                    @endfor
                                </select>
                            </div>

                            <!-- Search Buttons -->
                            <div class="col-lg-2 col-md-6">
                                <label class="form-label fw-semibold">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="toggleAdvancedFilters()">
                                        <i class="fas fa-sliders-h me-2"></i>Advanced
                                    </button>
                                    <button type="submit" class="btn btn-primary flex-fill" id="searchBtn">
                                        <i class="fas fa-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Filters (Hidden by default) -->
                        <div id="advancedFilters" class="mt-4 pt-4 border-top" style="display: none;">
                            <div class="row g-3">
                                <div class="col-lg-3 col-md-6">
                                    <label for="min_price" class="form-label fw-semibold">Min Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0">$</span>
                                        <input type="number" class="form-control border-start-0" id="min_price" name="min_price" 
                                               value="{{ request('min_price') }}" placeholder="Min">
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="city" class="form-label fw-semibold">City</label>
                                    <input type="text" class="form-control" id="city" name="city" 
                                           value="{{ request('city') }}" placeholder="Enter city">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="min_area" class="form-label fw-semibold">Min Area (m²)</label>
                                    <input type="number" class="form-control" id="min_area" name="min_area" 
                                           value="{{ request('min_area') }}" placeholder="Min area">
                                </div>
                                <div class="col-lg-3 col-md-6">
                                    <label for="max_area" class="form-label fw-semibold">Max Area (m²)</label>
                                    <input type="number" class="form-control" id="max_area" name="max_area" 
                                           value="{{ request('max_area') }}" placeholder="Max area">
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
