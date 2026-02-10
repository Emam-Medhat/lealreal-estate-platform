

<?php $__env->startSection('title', 'Inventory Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Inventory Management</h1>
                <div class="d-flex gap-2">
                    <a href="<?php echo e(route('inventory.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Item
                    </a>
                    <a href="<?php echo e(route('maintenance.index')); ?>" class="btn btn-secondary">
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
                            <h4 class="card-title"><?php echo e($items->total()); ?></h4>
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
                            <h4 class="card-title"><?php echo e(\App\Models\Inventory::available()->count()); ?></h4>
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
                            <h4 class="card-title"><?php echo e(\App\Models\Inventory::lowStock()->count()); ?></h4>
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
                            <h4 class="card-title"><?php echo e(\App\Models\Inventory::outOfStock()->count()); ?></h4>
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
            <form method="GET" action="<?php echo e(route('inventory.index')); ?>">
                <div class="row">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="<?php echo e(request('search')); ?>" placeholder="Search items...">
                    </div>
                    <div class="col-md-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All Categories</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($category->id); ?>" <?php echo e(request('category') == $category->id ? 'selected' : ''); ?>>
                                    <?php echo e($category->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="stock_level" class="form-label">Stock Level</label>
                        <select class="form-select" id="stock_level" name="stock_level">
                            <option value="">All Levels</option>
                            <option value="available" <?php echo e(request('stock_level') == 'available' ? 'selected' : ''); ?>>Available</option>
                            <option value="low" <?php echo e(request('stock_level') == 'low' ? 'selected' : ''); ?>>Low Stock</option>
                            <option value="out" <?php echo e(request('stock_level') == 'out' ? 'selected' : ''); ?>>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>Active</option>
                            <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>Inactive</option>
                            <option value="discontinued" <?php echo e(request('status') == 'discontinued' ? 'selected' : ''); ?>>Discontinued</option>
                        </select>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <a href="<?php echo e(route('inventory.index')); ?>" class="btn btn-secondary">
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
            <?php if($items->count() > 0): ?>
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
                            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($item->sku); ?></td>
                                    <td>
                                        <strong><?php echo e($item->name); ?></strong>
                                        <?php if($item->description): ?>
                                            <br><small class="text-muted"><?php echo e(Str::limit($item->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($item->category): ?>
                                            <?php echo e($item->getCategoryName()); ?>

                                        <?php else: ?>
                                            <span class="text-muted">No Category</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo e($item->getStockLevelColorAttribute()); ?>">
                                            <?php echo e($item->quantity); ?>

                                        </span>
                                        <?php if($item->reorder_level): ?>
                                            <br><small class="text-muted">Reorder: <?php echo e($item->reorder_level); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>$<?php echo e(number_format($item->unit_price, 2)); ?></td>
                                    <td>$<?php echo e(number_format($item->quantity * $item->unit_price, 2)); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($item->getStatusColorAttribute()); ?>">
                                            <?php echo e($item->getStatusLabelAttribute()); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('inventory.show', $item->id)); ?>" class="btn btn-outline-primary" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('inventory.edit', $item->id)); ?>" class="btn btn-outline-secondary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php if($item->isLowStock()): ?>
                                                <button class="btn btn-outline-warning" title="Reorder" onclick="reorderItem(<?php echo e($item->id); ?>)">
                                                    <i class="fas fa-shopping-cart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Showing <?php echo e($items->firstItem()); ?> to <?php echo e($items->lastItem()); ?> of <?php echo e($items->total()); ?> items
                    </div>
                    <?php echo e($items->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No inventory items found</h4>
                    <p class="text-muted">Get started by adding your first inventory item.</p>
                    <a href="<?php echo e(route('inventory.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add First Item
                    </a>
                </div>
            <?php endif; ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/maintenance/inventory.blade.php ENDPATH**/ ?>