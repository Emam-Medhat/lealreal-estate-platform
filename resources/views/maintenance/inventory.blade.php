@extends('layouts.app')

@section('title', 'Inventory Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Inventory Management</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Item
                    </a>
                    <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Maintenance
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $items->total() }}</h4>
                            <p class="card-text">Total Items</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Inventory::available()->count() }}</h4>
                            <p class="card-text">In Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Inventory::lowStock()->count() }}</h4>
                            <p class="card-text">Low Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ \App\Models\Inventory::outOfStock()->count() }}</h4>
                            <p class="card-text">Out of Stock</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('inventory.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search items...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stock_level" class="form-label">Stock Level</label>
                        <select class="form-select" id="stock_level" name="stock_level">
                            <option value="">All Levels</option>
                            <option value="available" {{ request('stock_level') == 'available' ? 'selected' : '' }}>Available</option>
                            <option value="low" {{ request('stock_level') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_level') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Inventory Items</h5>
        </div>
        <div class="card-body">
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total Value</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->sku }}</td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->category)
                                            {{ $item->getCategoryName() }}
                                        @else
                                            <span class="text-muted">No Category</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $item->getStockLevelColorAttribute() }}">
                                            {{ $item->quantity }}
                                        </span>
                                        @if($item->reorder_level)
                                            <br><small class="text-muted">Reorder: {{ $item->reorder_level }}</small>
                                        @endif
                                    </td>
                                    <td>${{ number_format($item->unit_price, 2) }}</td>
                                    <td>${{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $item->getStatusColorAttribute() }}">
                                            {{ $item->getStatusLabelAttribute() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($item->isLowStock())
                                                <button class="btn btn-outline-warning" title="Reorder" onclick="reorderItem({{ $item->id }})">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing {{ $items->firstItem() }} to {{ $items->lastItem() }} of {{ $items->total() }} items
                    </div>
                    {{ $items->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-box fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No inventory items found</h4>
                    <p class="text-muted">Get started by adding your first inventory item.</p>
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function reorderItem(itemId) {
    if (confirm('Are you sure you want to create a reorder for this item?')) {
        // Implementation for reorder functionality
        window.location.href = `/inventory/${itemId}/reorder`;
    }
}
</script>
@endsection
