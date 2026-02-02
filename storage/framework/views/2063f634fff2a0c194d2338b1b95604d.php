

<?php $__env->startSection('title', 'Page Not Found'); ?>

<?php $__env->startSection('content'); ?>
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="error-page">
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="mb-4">Page Not Found</h2>
                    <p class="lead text-muted mb-4">
                        Sorry, the page you are looking for doesn't exist or has been moved.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="<?php echo e(route('home')); ?>" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                        <a href="<?php echo e(route('properties.index')); ?>" class="btn btn-outline-primary">
                            <i class="fas fa-building me-2"></i>Browse Properties
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/errors/404.blade.php ENDPATH**/ ?>