@extends('layouts.app')

@section('title', 'Edit Inventory Item')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Edit Inventory Item</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-info">
                        <i class="fas fa-eye"></i> View Item
                    </a>
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Inventory
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit: {{ $item->name }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('inventory.update', $item->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Item Name *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $item->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sku" class="form-label">SKU *</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $item->sku) }}" required>
                                    @error('sku')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $item->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">Category</label>
                                    <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                        <option value="">Select Category</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="supplier_id" class="form-label">Supplier</label>
                                    <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id">
                                        <option value="">Select Supplier</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $item->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit_price" class="form-label">Unit Price *</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" step="0.01" class="form-control @error('unit_price') is-invalid @enderror" id="unit_price" name="unit_price" value="{{ old('unit_price', $item->unit_price) }}" required>
                                    </div>
                                    @error('unit_price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="quantity" class="form-label">Quantity *</label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', $item->quantity) }}" min="0" required>
                                    @error('quantity')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="reorder_level" class="form-label">Reorder Level</label>
                                    <input type="number" class="form-control @error('reorder_level') is-invalid @enderror" id="reorder_level" name="reorder_level" value="{{ old('reorder_level', $item->reorder_level) }}" min="0">
                                    @error('reorder_level')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_stock" class="form-label">Max Stock Level</label>
                                    <input type="number" class="form-control @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock', $item->max_stock) }}" min="0">
                                    @error('max_stock')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="unit_of_measure" class="form-label">Unit of Measure</label>
                                    <select class="form-select @error('unit_of_measure') is-invalid @enderror" id="unit_of_measure" name="unit_of_measure">
                                        <option value="pcs" {{ old('unit_of_measure', $item->unit_of_measure) == 'pcs' ? 'selected' : '' }}>Pieces</option>
                                        <option value="kg" {{ old('unit_of_measure') == 'kg' ? 'selected' : '' }}>Kilograms</option>
                                        <option value="liters" {{ old('unit_of_measure') == 'liters' ? 'selected' : '' }}>Liters</option>
                                        <option value="meters" {{ old('unit_of_measure') == 'meters' ? 'selected' : '' }}>Meters</option>
                                        <option value="boxes" {{ old('unit_of_measure') == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                        <option value="sets" {{ old('unit_of_measure') == 'sets' ? 'selected' : '' }}>Sets</option>
                                    </select>
                                    @error('unit_of_measure')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="discontinued" {{ old('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="location" class="form-label">Storage Location</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $item->location) }}" placeholder="e.g., Warehouse A, Shelf 3">
                            @error('location')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Additional notes about this item">{{ old('notes', $item->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Stock Status -->
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Current Stock Status</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Current Quantity:</strong> {{ $item->quantity }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Current Value:</strong> ${{ number_format($item->quantity * $item->unit_price, 2) }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Reorder Level:</strong> {{ $item->reorder_level ?? 'Not Set' }}
                                </div>
                                <div class="col-md-3">
                                    <strong>Status:</strong> 
                                    <span class="badge bg-{{ $item->status == 'active' ? 'success' : ($item->status == 'inactive' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Item
                                </button>
                                <a href="{{ route('inventory.show', $item->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                            <div>
                                @if($item->quantity > 0)
                                    <button type="button" class="btn btn-warning" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete Item
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                                        <i class="fas fa-trash"></i> Delete Item
                                    </button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="deleteForm" method="POST" action="{{ route('inventory.destroy', $item->id) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
function confirmDelete() {
    if (confirm('Are you sure you want to delete this inventory item? This action cannot be undone.')) {
        document.getElementById('deleteForm').submit();
    }
}

// Calculate total value
function calculateTotal() {
    const price = parseFloat(document.getElementById('unit_price').value) || 0;
    const quantity = parseInt(document.getElementById('quantity').value) || 0;
    const total = price * quantity;
    
    // You could display this somewhere if needed
    console.log('Total Value: $' + total.toFixed(2));
}

document.getElementById('unit_price').addEventListener('input', calculateTotal);
document.getElementById('quantity').addEventListener('input', calculateTotal);

// Show warning if quantity is below reorder level
document.getElementById('quantity').addEventListener('input', function() {
    const quantity = parseInt(this.value) || 0;
    const reorderLevel = parseInt(document.getElementById('reorder_level').value) || 0;
    
    if (quantity > 0 && quantity <= reorderLevel) {
        this.classList.add('is-warning');
        console.log('Warning: Quantity is at or below reorder level');
    } else {
        this.classList.remove('is-warning');
    }
});
</script>
@endsection
