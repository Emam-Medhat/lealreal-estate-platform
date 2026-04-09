@extends('layouts.app')

@section('title', 'Inventory Management')

@push('styles')
<style>
    .inventory-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 50%, #a855f7 100%);
        color: #fff;
        padding: 2rem;
        border-radius: 16px;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.10);
        position: relative;
        overflow: hidden;
    }

    .inventory-header:before {
        content: '';
        position: absolute;
        inset: 0;
        opacity: 0.18;
        background-image:
            radial-gradient(circle at 20% 20%, rgba(255,255,255,0.35), transparent 40%),
            radial-gradient(circle at 80% 30%, rgba(255,255,255,0.25), transparent 45%),
            radial-gradient(circle at 50% 90%, rgba(255,255,255,0.20), transparent 50%);
        pointer-events: none;
    }

    .card-shell {
        border: none;
        border-radius: 16px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        overflow: hidden;
        background: #fff;
    }

    .card-shell .card-header {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border: none;
        padding: 1rem 1.25rem;
        font-weight: 800;
    }

    .metric {
        border-radius: 16px;
        color: #fff;
        padding: 1.5rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .metric:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .metric.blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .metric.green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .metric.yellow { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .metric.red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    .action-btn {
        border-radius: 999px;
        padding: 0.7rem 1.1rem;
        font-weight: 700;
        transition: all 0.25s ease;
        border: none;
    }

    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.10);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        border: none;
        padding: 0.9rem 1rem;
        font-weight: 800;
        color: #374151;
        white-space: nowrap;
    }

    .table-modern tbody tr {
        transition: background-color 0.2s ease;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9ff;
    }

    .fade-in {
        animation: fadeIn 0.45s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <div class="inventory-header">
        <div class="row align-items-center" style="position: relative; z-index: 1;">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-box-open ml-2"></i>
                    Inventory Management
                </h1>
                <p class="mb-0 opacity-90">Manage parts, supplies, and equipment stock</p>
            </div>
            <div class="col-md-4 text-md-left mt-3 mt-md-0">
                <div class="d-flex justify-content-md-end gap-2 flex-wrap">
                    @if(\Illuminate\Support\Facades\Route::has('inventory.create'))
                        <a href="{{ route('inventory.create') }}" class="btn btn-light action-btn">
                            <i class="fas fa-plus ml-2"></i>
                            Add Item
                        </a>
                    @endif
                    @if(\Illuminate\Support\Facades\Route::has('maintenance.index'))
                        <a href="{{ route('maintenance.index') }}" class="btn btn-light action-btn">
                            <i class="fas fa-arrow-left ml-2"></i>
                            Maintenance
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric blue">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">Total Items</div>
                        <div class="h2 mb-0 font-weight-bold">{{ $items->total() }}</div>
                        <div class="text-white-50 small mt-2">All inventory items</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric green">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">In Stock</div>
                        <div class="h2 mb-0 font-weight-bold">{{ \App\Models\Inventory::available()->count() }}</div>
                        <div class="text-white-50 small mt-2">Available quantity</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 mb-md-0">
            <div class="metric yellow">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">Low Stock</div>
                        <div class="h2 mb-0 font-weight-bold">{{ \App\Models\Inventory::lowStock()->count() }}</div>
                        <div class="text-white-50 small mt-2">Needs reorder soon</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="metric red">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small mb-1">Out of Stock</div>
                        <div class="h2 mb-0 font-weight-bold">{{ \App\Models\Inventory::outOfStock()->count() }}</div>
                        <div class="text-white-50 small mt-2">No quantity available</div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card-shell mb-4">
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
                        <button type="submit" class="btn btn-primary action-btn">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary action-btn">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card-shell">
        <div class="card-header">
            <h5 class="card-title mb-0">Inventory Items</h5>
        </div>
        <div class="card-body">
            @if($items->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
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
                                            @if(\Illuminate\Support\Facades\Route::has('inventory.show'))
                                                <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if(\Illuminate\Support\Facades\Route::has('inventory.edit'))
                                                <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
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
                    @if(\Illuminate\Support\Facades\Route::has('inventory.create'))
                        <a href="{{ route('inventory.create') }}" class="btn btn-primary action-btn">
                            <i class="fas fa-plus"></i> Add First Item
                        </a>
                    @endif
                </div>
            @endif
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
</script>
@endsection
