@extends('layouts.agent')

@section('title', 'Featured Properties')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Featured Properties</h1>
            <p class="text-muted">Your featured property listings</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agent.properties.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Property
            </a>
            <a href="{{ route('agent.properties.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>All Properties
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $properties->count() }}</h4>
                            <p class="card-text">Featured Properties</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Properties Grid -->
    @if($properties->count() > 0)
        <div class="row">
            @foreach($properties as $property)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card property-card h-100">
                        <!-- Property Image -->
                        <div class="position-relative">
                            <img src="{{ $property->getFirstImageUrl() ?? '/images/placeholder-property.jpg' }}" 
                                 class="card-img-top property-image" alt="{{ $property->title }}">
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge bg-warning">
                                    <i class="fas fa-star me-1"></i>Featured
                                </span>
                            </div>
                            <div class="position-absolute top-0 start-0 p-2">
                                <span class="badge bg-{{ $property->getStatusColor() }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <!-- Property Details -->
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="{{ route('agent.properties.show', $property) }}" class="text-decoration-none text-dark">
                                    {{ $property->title }}
                                </a>
                            </h5>
                            
                            <p class="text-muted small mb-2">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $property->location->address ?? 'Address not available' }}
                            </p>
                            
                            <div class="property-specs mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <small class="text-muted">Type:</small>
                                        <div>{{ $property->propertyType->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Bedrooms:</small>
                                        <div>{{ $property->bedrooms ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Bathrooms:</small>
                                        <div>{{ $property->bathrooms ?? 'N/A' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Area:</small>
                                        <div>{{ $property->area ?? 'N/A' }} m²</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 class="text-primary mb-0">
                                    ${{ number_format($property->price->price, 2) }}
                                </h4>
                                <button type="button" class="btn btn-sm btn-outline-secondary" 
                                        onclick="toggleFeatured({{ $property->id }})">
                                    <i class="fas fa-star"></i>
                                </button>
                            </div>
                            
                            <!-- Property Stats -->
                            <div class="row text-center mb-3">
                                <div class="col-4">
                                    <small class="text-muted">Views</small>
                                    <div class="fw-bold">{{ $property->views_count ?? 0 }}</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Inquiries</small>
                                    <div class="fw-bold">{{ $property->inquiries_count ?? 0 }}</div>
                                </div>
                                <div class="col-4">
                                    <small class="text-muted">Days Listed</small>
                                    <div class="fw-bold">{{ $property->created_at->diffInDays() }}</div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="d-flex gap-2">
                                <a href="{{ route('agent.properties.show', $property) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                <a href="{{ route('agent.properties.edit', $property) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </a>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('agent.properties.show', $property) }}">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('agent.properties.edit', $property) }}">
                                                <i class="fas fa-edit me-2"></i>Edit Property
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="duplicateProperty({{ $property->id }})">
                                                <i class="fas fa-copy me-2"></i>Duplicate
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item text-danger" href="#" onclick="deleteProperty({{ $property->id }})">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $properties->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-star fa-3x text-muted mb-3"></i>
            <h4>No featured properties</h4>
            <p class="text-muted">You haven't featured any properties yet.</p>
            <a href="{{ route('agent.properties.index') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Featured Property
            </a>
        </div>
    @endif
</div>

<style>
.property-card {
    transition: transform 0.2s ease;
}

.property-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.property-image {
    height: 200px;
    object-fit: cover;
}

.property-specs .row > div {
    border-right: 1px solid #dee2e6;
}

.property-specs .row > div:last-child {
    border-right: none;
}
</style>

@push('scripts')
<script>
function toggleFeatured(propertyId) {
    fetch(`/agent/properties/${propertyId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

function duplicateProperty(propertyId) {
    if (confirm('Are you sure you want to duplicate this property?')) {
        fetch(`/agent/properties/${propertyId}/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.href = `/agent/properties/${data.property_id}/edit`;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function deleteProperty(propertyId) {
    if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
        fetch(`/agent/properties/${propertyId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}
</script>
@endpush
