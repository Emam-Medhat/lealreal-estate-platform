@extends('layouts.app')

@section('title', 'Inventory Item Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Inventory Item Details</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Item
                    </a>
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
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $item->name }}</h5>
                    <span class="badge bg-{{ $item->status == 'active' ? 'success' : ($item->status == 'inactive' ? 'warning' : 'secondary') }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">SKU:</td>
                                    <td>{{ $item->sku }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Category:</td>
                                    <td>
                                        @if($item->category)
                                            <span class="badge bg-info">{{ $item->getCategoryName() }}</span>
                                        @else
                                            <span class="text-muted">No Category</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Supplier:</td>
                                    <td>
                                        @if($item->supplier)
                                            <span class="badge bg-primary">{{ $item->getSupplierName() }}</span>
                                        @else
                                            <span class="text-muted">No Supplier</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Unit Price:</td>
                                    <td><strong>${{ number_format($item->unit_price, 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Unit of Measure:</td>
                                    <td>{{ ucfirst($item->unit_of_measure) }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold">Current Quantity:</td>
                                    <td>
                                        <span class="badge bg-{{ $item->quantity > 0 ? 'success' : 'danger' }} fs-6">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Reorder Level:</td>
                                    <td>{{ $item->reorder_level ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Max Stock:</td>
                                    <td>{{ $item->max_stock ?? 'Not Set' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Location:</td>
                                    <td>{{ $item->location ?? 'Not Specified' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Total Value:</td>
                                    <td><strong>${{ number_format($item->quantity * $item->unit_price, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($item->description)
                        <div class="mt-4">
                            <h6>Description</h6>
                            <p class="text-muted">{{ $item->description }}</p>
                        </div>
                    @endif

                    @if($item->notes)
                        <div class="mt-4">
                            <h6>Notes</h6>
                            <p class="text-muted">{{ $item->notes }}</p>
                        </div>
                    @endif

                    <div class="mt-4">
                        <small class="text-muted">
                            Created: {{ $item->created_at->format('M d, Y H:i') }} | 
                            Updated: {{ $item->updated_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Status Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Stock Status</h5>
                </div>
                <div class="card-body text-center">
                    @if($item->quantity == 0)
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">Out of Stock</h5>
                        <p class="text-muted">This item is currently out of stock</p>
                    @elseif($item->reorder_level && $item->quantity <= $item->reorder_level)
                        <i class="fas fa-exclamation-circle fa-3x text-warning mb-3"></i>
                        <h5 class="text-warning">Low Stock</h5>
                        <p class="text-muted">Reorder soon. Current: {{ $item->quantity }}, Reorder at: {{ $item->reorder_level }}</p>
                    @else
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-success">In Stock</h5>
                        <p class="text-muted">Good stock level: {{ $item->quantity }} units</p>
                    @endif

                    @if($item->reorder_level)
                        <div class="mt-3">
                            <div class="progress" style="height: 20px;">
                                <?php
                                $percentage = $item->max_stock ? 
                                    min(($item->quantity / $item->max_stock) * 100, 100) : 
                                    min(($item->quantity / ($item->reorder_level * 2)) * 100, 100);
                                ?>
                                <div class="progress-bar bg-{{ $item->quantity <= $item->reorder_level ? 'warning' : 'success' }}" 
                                     style="width: {{ $percentage }}%">
                                    {{ round($percentage) }}%
                                </div>
                            </div>
                            <small class="text-muted">Stock Level</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($item->reorder_level && $item->quantity <= $item->reorder_level)
                            <button class="btn btn-warning" onclick="reorderItem({{ $item->id }})">
                                <i class="fas fa-shopping-cart"></i> Reorder Item
                            </button>
                        @endif
                        <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Item
                        </a>
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
