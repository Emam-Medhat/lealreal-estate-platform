

<?php $__env->startSection('title', 'Favorite Properties'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">My Favorite Properties</h1>
                    <p class="text-gray-600">Properties you've saved for later</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="compareSelected()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-balance-scale mr-2"></i>
                        Compare Selected
                    </button>
                    <button onclick="shareFavorites()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-share-alt mr-2"></i>
                        Share List
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <?php if($favorites->isNotEmpty()): ?>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fas fa-heart text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Favorites</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo e($favorites->count()); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Price</p>
                            <p class="text-2xl font-bold text-gray-800">$<?php echo e(number_format($favorites->avg(fn($f) => $f->favoritable->price ?? 0), 0)); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <i class="fas fa-bed text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Beds</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo e(number_format($favorites->avg(fn($f) => $f->favoritable->bedrooms ?? 0), 1)); ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-3 mr-4">
                            <i class="fas fa-ruler-combined text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Avg. Area</p>
                            <p class="text-2xl font-bold text-gray-800"><?php echo e(number_format($favorites->avg(fn($f) => $f->favoritable->area ?? 0), 0)); ?> <?php echo e($favorites->first()->favoritable->area_unit ?? 'sqft'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Filter and Sort -->
        <?php if($favorites->isNotEmpty()): ?>
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <label class="text-sm text-gray-600">Filter by:</label>
                        <select onchange="filterFavorites(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Properties</option>
                            <option value="for_sale">For Sale</option>
                            <option value="for_rent">For Rent</option>
                            <option value="price_low">Price (Low to High)</option>
                            <option value="price_high">Price (High to Low)</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                        </select>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <button onclick="selectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-check-square mr-1"></i>
                            Select All
                        </button>
                        <button onclick="deselectAll()" class="text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-square mr-1"></i>
                            Deselect All
                        </button>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Favorites Grid -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <?php $__empty_1 = true; $__currentLoopData = $favorites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $favorite): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded-lg overflow-hidden mb-6 hover:shadow-lg transition-shadow">
                        <div class="md:flex">
                            <!-- Checkbox -->
                            <div class="md:w-1/12 flex items-center justify-center p-4">
                                <input type="checkbox" class="property-checkbox" value="<?php echo e($favorite->favoritable->id); ?>" 
                                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                            </div>
                            
                            <!-- Property Image -->
                            <div class="md:w-2/6">
                                <div class="h-48 md:h-full bg-gray-200 relative">
                                    <?php if($favorite->favoritable->images->isNotEmpty()): ?>
                                        <img src="<?php echo e($favorite->favoritable->images->first()->url); ?>" alt="<?php echo e($favorite->favoritable->title); ?>" class="w-full h-full object-cover">
                                    <?php else: ?>
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-home text-gray-400 text-4xl"></i>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Status Badge -->
                                    <?php if($favorite->favoritable->status): ?>
                                        <div class="absolute top-2 left-2">
                                            <span class="bg-<?php echo e($favorite->favoritable->status === 'for_sale' ? 'green' : ($favorite->favoritable->status === 'for_rent' ? 'blue' : 'gray')); ?>-500 text-white px-2 py-1 rounded text-xs">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $favorite->favoritable->status))); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Remove Favorite Button -->
                                    <button onclick="removeFavorite(<?php echo e($favorite->favoritable->id); ?>)" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-red-50">
                                        <i class="fas fa-heart text-red-500"></i>
                                    </button>
                                    
                                    <!-- Price Change Badge -->
                                    <?php if($favorite->favoritable->price_changed): ?>
                                        <div class="absolute bottom-2 left-2">
                                            <span class="bg-<?php echo e($favorite->favoritable->price_change > 0 ? 'red' : 'green'); ?>-500 text-white px-2 py-1 rounded text-xs">
                                                <?php echo e($favorite->favoritable->price_change > 0 ? '+' : ''); ?><?php echo e(number_format($favorite->favoritable->price_change, 0)); ?>%
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Property Details -->
                            <div class="md:w-3/6 p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                                            <a href="<?php echo e(route('properties.show', $favorite->favoritable)); ?>" class="hover:text-blue-600">
                                                <?php echo e($favorite->favoritable->title); ?>

                                            </a>
                                        </h3>
                                        <p class="text-gray-600 mb-2"><?php echo e($favorite->favoritable->address); ?>, <?php echo e($favorite->favoritable->city); ?></p>
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-bed mr-1"></i><?php echo e($favorite->favoritable->bedrooms); ?> beds</span>
                                            <span><i class="fas fa-bath mr-1"></i><?php echo e($favorite->favoritable->bathrooms); ?> baths</span>
                                            <span><i class="fas fa-ruler-combined mr-1"></i><?php echo e(number_format($favorite->favoritable->area)); ?> <?php echo e($favorite->favoritable->area_unit); ?></span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-800">
                                            <?php echo e($favorite->favoritable->currency); ?> <?php echo e(number_format($favorite->favoritable->price, 0)); ?>

                                        </div>
                                        <?php if($favorite->favoritable->status === 'for_rent'): ?>
                                            <div class="text-sm text-gray-600">/month</div>
                                        <?php endif; ?>
                                        <div class="text-xs text-gray-500 mt-1">
                                            Favorited <?php echo e($favorite->created_at->diffForHumans()); ?>

                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Property Description -->
                                <p class="text-gray-600 mb-4 line-clamp-2">
                                    <?php echo e(Str::limit($favorite->favoritable->description, 150)); ?>

                                </p>
                                
                                <!-- Features -->
                                <?php if($favorite->favoritable->features->isNotEmpty()): ?>
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <?php $__currentLoopData = $favorite->favoritable->features->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">
                                                <?php echo e($feature->name); ?>

                                            </span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php if($favorite->favoritable->features->count() > 3): ?>
                                            <span class="text-gray-500 text-xs">+<?php echo e($favorite->favoritable->features->count() - 3); ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Actions -->
                                <div class="flex justify-between items-center">
                                    <div class="flex space-x-3">
                                        <a href="<?php echo e(route('properties.show', $favorite->favoritable)); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                            View Details
                                        </a>
                                        <button onclick="scheduleTour(<?php echo e($favorite->favoritable->id); ?>)" class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                            Schedule Tour
                                        </button>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="shareProperty(<?php echo e($favorite->favoritable->id); ?>)" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                        <button onclick="addToComparison(<?php echo e($favorite->favoritable->id); ?>)" class="text-gray-600 hover:text-gray-800">
                                            <i class="fas fa-balance-scale"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-heart text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No favorite properties yet</h3>
                        <p class="text-gray-500 mb-6">Start browsing and save properties you're interested in.</p>
                        <a href="<?php echo e(route('optimized.properties.index')); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            Browse Properties
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function filterFavorites(filter) {
    const url = new URL(window.location);
    if (filter) {
        url.searchParams.set('filter', filter);
    } else {
        url.searchParams.delete('filter');
    }
    window.location.href = url.toString();
}

function selectAll() {
    document.querySelectorAll('.property-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('.property-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function compareSelected() {
    const selected = document.querySelectorAll('.property-checkbox:checked');
    if (selected.length < 2) {
        alert('Please select at least 2 properties to compare');
        return;
    }
    
    const ids = Array.from(selected).map(cb => cb.value);
    window.location.href = '/properties/compare?properties=' + ids.join(',');
}

function shareFavorites() {
    window.location.href = '/favorites/share';
}

function removeFavorite(propertyId) {
    if (!confirm('Are you sure you want to remove this property from favorites?')) {
        return;
    }
    
    fetch('/favorites/' + propertyId, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

function scheduleTour(propertyId) {
    window.location.href = '/properties/' + propertyId + '/tour';
}

function shareProperty(propertyId) {
    window.location.href = '/properties/' + propertyId + '/share';
}

function addToComparison(propertyId) {
    window.location.href = '/properties/compare?add=' + propertyId;
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/properties/favorites.blade.php ENDPATH**/ ?>