@extends('layouts.app')

@section('title', 'Inventory Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">{{ $item->name }}</h1>
                    <p class="text-muted mb-0">SKU: {{ $item->sku }}</p>
                </div>
                <div class="d-flex gap-2">
                    @if(Route::has('inventory.edit'))
                    <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Item
                    </a>
                    @endif
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Item Details -->
        <div class="col-md-8">
            <div class="card card-shell">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">Item Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Basic Information</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">SKU</span>
                                        <span class="info-value">{{ $item->sku }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Category</span>
                                        <span class="info-value">
                                            @if($item->category)
                                                <span class="badge bg-info">{{ $item->getCategoryName() }}</span>
                                            @else
                                                <span class="text-muted">No Category</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Supplier</span>
                                        <span class="info-value">
                                            @if($item->supplier)
                                                <span class="badge bg-primary">{{ $item->getSupplierName() }}</span>
                                            @else
                                                <span class="text-muted">No Supplier</span>
                                            @endif
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Unit Price</span>
                                        <span class="info-value fw-bold">${{ number_format($item->unit_price, 2) }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Unit of Measure</span>
                                        <span class="info-value">{{ ucfirst($item->unit_of_measure) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-4">
                                <h6 class="text-muted mb-3">Stock Information</h6>
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label">Current Quantity</span>
                                        <span class="info-value">
                                            <span class="badge bg-{{ $item->quantity > 0 ? 'success' : 'danger' }} fs-6">
                                                {{ $item->quantity }}
                                            </span>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Reorder Level</span>
                                        <span class="info-value">{{ $item->reorder_level ?? 'Not Set' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Max Stock</span>
                                        <span class="info-value">{{ $item->max_stock ?? 'Not Set' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Location</span>
                                        <span class="info-value">{{ $item->location ?? 'Not Specified' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">Total Value</span>
                                        <span class="info-value fw-bold">${{ number_format($item->quantity * $item->unit_price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($item->description)
                        <div class="mt-4">
                            <h6 class="text-muted mb-2">Description</h6>
                            <div class="p-3 bg-light rounded">{{ $item->description }}</div>
                        </div>
                    @endif

                    @if($item->notes)
                        <div class="mt-4">
                            <h6 class="text-muted mb-2">Notes</h6>
                            <div class="p-3 bg-light rounded">{{ $item->notes }}</div>
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-top">
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <i class="fas fa-calendar-plus me-1"></i> Created: {{ $item->created_at->format('M d, Y H:i') }}
                            </div>
                            <div class="col-md-6">
                                <i class="fas fa-calendar-check me-1"></i> Updated: {{ $item->updated_at->format('M d, Y H:i') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status Card -->
        <div class="col-md-4">
            <div class="card card-shell">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">Stock Status</h5>
                </div>
                <div class="card-body text-center">
                    @if($item->quantity == 0)
                        <div class="status-indicator status-danger mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x"></i>
                        </div>
                        <h5 class="text-danger">Out of Stock</h5>
                        <p class="text-muted">This item is currently out of stock</p>
                    @elseif($item->reorder_level && $item->quantity <= $item->reorder_level)
                        <div class="status-indicator status-warning mb-3">
                            <i class="fas fa-exclamation-circle fa-3x"></i>
                        </div>
                        <h5 class="text-warning">Low Stock</h5>
                        <p class="text-muted">Reorder soon. Current: {{ $item->quantity }}, Reorder at: {{ $item->reorder_level }}</p>
                    @else
                        <div class="status-indicator status-success mb-3">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                        <h5 class="text-success">In Stock</h5>
                        <p class="text-muted">Good stock level: {{ $item->quantity }} units</p>
                    @endif

                    @if($item->reorder_level)
                        <?php
                        $percentage = $item->max_stock ? 
                            min(($item->quantity / $item->max_stock) * 100, 100) : 
                            min(($item->quantity / ($item->reorder_level * 2)) * 100, 100);
                        ?>
                        <div class="mt-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="small text-muted">Stock Level</span>
                                <span class="small fw-bold">{{ round($percentage) }}%</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-{{ $item->quantity <= $item->reorder_level ? 'warning' : 'success' }}" 
                                     style="width: {{ $percentage }}%; border-radius: 4px;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card card-shell mt-3">
                <div class="card-header bg-gradient text-white">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($item->reorder_level && $item->quantity <= $item->reorder_level)
                            <button class="btn btn-warning" onclick="reorderItem({{ $item->id }})">
                                <i class="fas fa-shopping-cart"></i> Reorder Item
                            </button>
                        @endif
                        @if(Route::has('inventory.edit'))
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
                        @endif
                        <button class="btn btn-outline-secondary" onclick="printItem({{ $item->id }})">
                            <i class="fas fa-print"></i> Print Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function reorderItem(itemId) {
    if (confirm('Are you sure you want to create a reorder for this item?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = `/inventory/items/${itemId}/reorder`;
        
        let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        let csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function printItem(itemId) {
    window.print();
}
</script>

<style>
.card-shell {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
    transition: box-shadow 0.15s ease-in-out;
}

.card-shell:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.card-header.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border-bottom: none;
    border-radius: 0.5rem 0.5rem 0 0 !important;
}

.info-grid {
    display: grid;
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    border-left: 4px solid #667eea;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
}

.info-value {
    font-weight: 500;
    color: #212529;
}

.status-indicator {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.status-indicator::before {
    content: '';
    position: absolute;
    inset: -5px;
    border-radius: 50%;
    background: inherit;
    opacity: 0.2;
    z-index: -1;
}

.status-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
}

.status-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    color: white;
}

.status-danger {
    background: linear-gradient(135deg, #dc3545, #e83e8c);
    color: white;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}

.progress-bar {
    transition: width 0.6s ease;
}

@media print {
    .d-flex.gap-2, .btn, .card-header .btn-group {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
    }
}
</style>
@endsection
