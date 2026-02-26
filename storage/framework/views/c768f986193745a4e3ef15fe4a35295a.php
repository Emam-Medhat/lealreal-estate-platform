<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-all duration-300 group">
    <div class="relative">
        <!-- Property Image -->
        <div class="aspect-w-16 aspect-h-9 overflow-hidden">
            <?php if($property->media && $property->media->where('media_type', 'image')->count() > 0): ?>
                <img src="<?php echo e($property->media->where('media_type', 'image')->first()->url); ?>" 
                     alt="<?php echo e($property->title); ?>" 
                     class="w-full h-56 object-cover transform group-hover:scale-105 transition-transform duration-500">
            <?php else: ?>
                <div class="w-full h-56 bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-building text-gray-300 text-5xl"></i>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Status Badges -->
        <div class="absolute top-4 left-4 flex flex-col space-y-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-white/90 text-blue-600 shadow-sm">
                For <?php echo e(ucfirst($property->listing_type)); ?>

            </span>
            <?php if($property->featured): ?>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-400 text-white shadow-sm">
                    <i class="fas fa-star mr-1"></i>Featured
                </span>
            <?php endif; ?>
        </div>

        <!-- Price Badge -->
        <div class="absolute bottom-4 right-4">
            <div class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold shadow-lg">
                <?php echo e(number_format($property->price ?? 0)); ?> <?php echo e($property->currency ?? 'USD'); ?>

            </div>
        </div>
    </div>

    <div class="p-6">
        <div class="flex items-center text-sm text-blue-600 font-semibold mb-2">
            <i class="fas fa-tag mr-2"></i>
            <?php echo e(optional($property->propertyType)->name ?? 'Property'); ?>

        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-1 group-hover:text-blue-600 transition-colors" style="font-size:20px">
            <a href="<?php echo e(route('optimized.properties.show', $property)); ?>">
                <?php echo e($property->title); ?>

            </a>
        </h3>
        <p class="text-gray-500 text-sm mb-4 flex items-start line-clamp-1">
            <i class="fas fa-map-marker-alt mr-2 mt-1 flex-shrink-0"></i>
            <?php echo e($property->city ?? 'Unknown City'); ?>, <?php echo e($property->country ?? 'Unknown Country'); ?>

        </p>

        <!-- Features -->
        <div class="flex items-center justify-between py-4 border-t border-gray-100">
            <div class="flex items-center text-gray-600 text-sm">
                <i class="fas fa-bed mr-2 text-blue-500"></i>
                <span class="font-medium"><?php echo e($property->bedrooms ?? 0); ?> Beds</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm">
                <i class="fas fa-bath mr-2 text-blue-500"></i>
                <span class="font-medium"><?php echo e($property->bathrooms ?? 0); ?> Baths</span>
            </div>
            <div class="flex items-center text-gray-600 text-sm">
                <i class="fas fa-vector-square mr-2 text-blue-500"></i>
                <span class="font-medium"><?php echo e(number_format($property->area)); ?> <?php echo e($property->area_unit ?? 'sqft'); ?></span>
            </div>
        </div>

        <div class="mt-4 flex items-center justify-between">
            <div class="flex items-center">
                <img src="<?php echo e($property->agent?->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($property->agent?->name ?? 'Agent')); ?>" 
                     alt="Agent" class="w-8 h-8 rounded-full border border-gray-200">
                <span class="ml-2 text-sm text-gray-600 font-medium"><?php echo e($property->agent?->name ?? 'Professional Agent'); ?></span>
            </div>
            <a href="<?php echo e(route('optimized.properties.show', $property)); ?>" class="text-blue-600 font-bold text-sm hover:text-blue-800 transition-colors flex items-center">
                View Details <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>
    </div>
</div>
<?php /**PATH F:\larvel state big\resources\views/properties/partials/card.blade.php ENDPATH**/ ?>