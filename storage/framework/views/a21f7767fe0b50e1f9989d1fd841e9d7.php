<?php $__env->startSection('title', 'العقارات | ' . config('app.name', 'منصة العقارات')); ?>

<?php $__env->startSection('content'); ?>
<div style="background: #f8f9fa; min-height: 100vh; padding: 20px 0;">
    <div class="container">
        <!-- Header -->
        <div class="text-center mb-5">
            <h1 style="color: #2c3e50; font-size: 2.5rem; font-weight: 300; margin-bottom: 10px;">العقارات</h1>
            <p style="color: #7f8c8d; font-size: 1.1rem;">اكتشف أفضل العقارات المتاحة</p>
        </div>

        <!-- Search Form -->
        <div class="card mb-4" style="border: none; border-radius: 15px; box-shadow: 0 2px 10px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <form method="GET" action="<?php echo e(route('properties.index')); ?>">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="q" value="<?php echo e(request('q')); ?>" 
                                   class="form-control" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;"
                                   placeholder="ابحث عن عقار...">
                        </div>
                        <div class="col-md-2">
                            <select name="property_type" class="form-select" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                <option value="">كل الأنواع</option>
                                <?php $__currentLoopData = $propertyTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($type->slug); ?>" <?php echo e(request('property_type') == $type->slug ? 'selected' : ''); ?>>
                                        <?php echo e($type->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="listing_type" class="form-select" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;">
                                <option value="">الكل</option>
                                <option value="sale" <?php echo e(request('listing_type') == 'sale' ? 'selected' : ''); ?>>للبيع</option>
                                <option value="rent" <?php echo e(request('listing_type') == 'rent' ? 'selected' : ''); ?>>للإيجار</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="max_price" value="<?php echo e(request('max_price')); ?>" 
                                   class="form-control" style="border-radius: 10px; border: 1px solid #e0e0e0; padding: 12px;"
                                   placeholder="السعر الأقصى">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn w-100" 
                                    style="background: #3498db; color: white; border: none; border-radius: 10px; padding: 12px; font-weight: 500;">
                                <i class="fas fa-search"></i> بحث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Count -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span style="color: #2c3e50; font-size: 1.1rem;">تم العثور على <strong><?php echo e($properties->total()); ?></strong> عقار</span>
            </div>
            <div class="btn-group">
                <button class="btn btn-outline-secondary active" style="border-radius: 8px;">
                    <i class="fas fa-th"></i>
                </button>
                <button class="btn btn-outline-secondary" style="border-radius: 8px;">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- Properties Grid -->
        <div class="row">
            <?php $__empty_1 = true; $__currentLoopData = $properties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100" style="border: none; border-radius: 15px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); transition: all 0.3s ease; overflow: hidden;">
                        <!-- Property Image -->
                        <div style="position: relative; overflow-hidden; height: 200px;">
                            <?php if($property->media && $property->media->first()): ?>
                                <img src="<?php echo e(asset('storage/' . $property->media->first()->file_path)); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                                     alt="<?php echo e($property->title); ?>"
                                     onerror="this.src='https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease;"
                                     alt="<?php echo e($property->title); ?>">
                            <?php endif; ?>
                            
                            <!-- Status Badges -->
                            <?php if($property->featured): ?>
                                <span style="position: absolute; top: 10px; right: 10px; background: #f39c12; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                                    <i class="fas fa-star"></i> مميز
                                </span>
                            <?php endif; ?>
                            
                            <span style="position: absolute; top: 10px; left: 10px; background: #27ae60; color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem;">
                                <?php echo e($property->listing_type == 'sale' ? 'للبيع' : 'للإيجار'); ?>

                            </span>
                        </div>

                        <!-- Property Details -->
                        <div class="card-body" style="padding: 20px;">
                            <h5 style="color: #2c3e50; font-weight: 600; margin-bottom: 10px;"><?php echo e($property->title); ?></h5>
                            
                            <div style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 15px;">
                                <i class="fas fa-map-marker-alt"></i> <?php echo e($property->city ?? 'غير محدد'); ?>, <?php echo e($property->country ?? ''); ?>

                            </div>
                            
                            <!-- Features -->
                            <div class="row text-center mb-3">
                                <?php if($property->bedrooms > 0): ?>
                                    <div class="col-4">
                                        <div style="color: #3498db; font-size: 1.2rem;"><i class="fas fa-bed"></i></div>
                                        <div style="font-weight: 600;"><?php echo e($property->bedrooms); ?></div>
                                        <div style="color: #7f8c8d; font-size: 0.8rem;">غرف</div>
                                    </div>
                                <?php endif; ?>
                                <?php if($property->bathrooms > 0): ?>
                                    <div class="col-4">
                                        <div style="color: #3498db; font-size: 1.2rem;"><i class="fas fa-bath"></i></div>
                                        <div style="font-weight: 600;"><?php echo e($property->bathrooms); ?></div>
                                        <div style="color: #7f8c8d; font-size: 0.8rem;">حمام</div>
                                    </div>
                                <?php endif; ?>
                                <?php if($property->area > 0): ?>
                                    <div class="col-4">
                                        <div style="color: #3498db; font-size: 1.2rem;"><i class="fas fa-expand"></i></div>
                                        <div style="font-weight: 600;"><?php echo e($property->area); ?></div>
                                        <div style="color: #7f8c8d; font-size: 0.8rem;">متر</div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Price -->
                            <?php if($property->price): ?>
                                <div class="text-center mb-3">
                                    <h4 style="color: #2c3e50; font-weight: 700; margin: 0;">
                                        <?php echo e(number_format($property->price)); ?>

                                        <small style="color: #7f8c8d; font-size: 0.9rem;"><?php echo e($property->currency ?? 'ريال'); ?></small>
                                    </h4>
                                </div>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('properties.show', $property->id)); ?>" 
                                   class="btn" style="background: #3498db; color: white; border: none; border-radius: 8px; padding: 10px;">
                                    <i class="fas fa-eye"></i> عرض التفاصيل
                                </a>
                                <div class="btn-group">
                                    <button class="btn btn-outline-primary" style="border-radius: 8px;">
                                        <i class="fas fa-phone"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" style="border-radius: 8px;">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-home" style="font-size: 4rem; color: #bdc3c7; margin-bottom: 20px;"></i>
                        <h3 style="color: #2c3e50; margin-bottom: 15px;">لا توجد عقارات</h3>
                        <p style="color: #7f8c8d; margin-bottom: 20px;">لم يتم العثور على عقارات تطابق معايير البحث</p>
                        <a href="<?php echo e(route('properties.index')); ?>" class="btn" style="background: #3498db; color: white; border: none; border-radius: 8px; padding: 10px 20px;">
                            مسح البحث
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($properties->hasPages()): ?>
            <div class="text-center mt-5">
                <?php echo e($properties->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

.card:hover img {
    transform: scale(1.05);
}

.btn:hover {
    transform: translateY(-1px);
}

/* RTL Support */
@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 15px;
        text-align: center;
    }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/properties/index.blade.php ENDPATH**/ ?>