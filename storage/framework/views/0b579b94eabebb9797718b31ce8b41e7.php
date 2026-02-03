<?php $__env->startSection('title', 'إنشاء تقرير أداء جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">إنشاء تقرير أداء جديد</h2>
                    <p class="text-muted">إنشاء تقرير أداء جديد للوكلاء والعقارات</p>
                </div>
                <div>
                    <a href="<?php echo e(route('reports.performance.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ نجاح:</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ خطأ:</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات التقرير</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('reports.performance.store')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agent_id" class="form-label">الوكيل</label>
                                <select class="form-select <?php $__errorArgs = ['agent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="agent_id" name="agent_id">
                                    <option value="">اختر الوكيل</option>
                                    <?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($agent->id); ?>" <?php echo e(old('agent_id') == $agent->id ? 'selected' : ''); ?>><?php echo e($agent->user->name ?? $agent->name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['agent_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="period_start" class="form-label">تاريخ البدء *</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="period_start" name="period_start" value="<?php echo e(old('period_start')); ?>" required>
                                <?php $__errorArgs = ['period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="period_end" class="form-label">تاريخ الانتهاء *</label>
                                <input type="date" class="form-control <?php $__errorArgs = ['period_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="period_end" name="period_end" value="<?php echo e(old('period_end')); ?>" required>
                                <?php $__errorArgs = ['period_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="report_type" class="form-label">نوع التقرير</label>
                                <select class="form-select" id="report_type" name="report_type">
                                    <option value="monthly">شهري</option>
                                    <option value="quarterly">ربع سنوي</option>
                                    <option value="yearly">سنوي</option>
                                    <option value="custom">مخصص</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان التقرير *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="title" name="title" value="<?php echo e(old('title')); ?>" placeholder="أدخل عنوان التقرير" required>
                            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="format" class="form-label">تنسيق التقرير *</label>
                            <select class="form-select <?php $__errorArgs = ['format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="format" name="format" required>
                                <option value="">اختر التنسيق</option>
                                <option value="pdf" <?php echo e(old('format') == 'pdf' ? 'selected' : ''); ?>>PDF</option>
                                <option value="excel" <?php echo e(old('format') == 'excel' ? 'selected' : ''); ?>>Excel</option>
                                <option value="csv" <?php echo e(old('format') == 'csv' ? 'selected' : ''); ?>>CSV</option>
                            </select>
                            <?php $__errorArgs = ['format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف التقرير</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="أدخل وصف التقرير"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" checked>
                                <label class="form-check-label" for="include_charts">
                                    تضمين الرسوم البيانية
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_details" name="include_details" checked>
                                <label class="form-check-label" for="include_details">
                                    تضمين التفاصيل الكاملة
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التقرير
                            </button>
                            <a href="<?php echo e(route('reports.performance.index')); ?>" class="btn btn-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> ملاحظات</h6>
                        <p class="mb-0">سيتم إنشاء التقرير تلقائياً بناءً على البيانات المتاحة في الفترة المحددة.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>الإحصائيات المتاحة:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> إجمالي المبيعات</li>
                            <li><i class="fas fa-check text-success"></i> عدد العقارات المباعة</li>
                            <li><i class="fas fa-check text-success"></i> معدل التحويل</li>
                            <li><i class="fas fa-check text-success"></i> متوسط سعر البيع</li>
                            <li><i class="fas fa-check text-success"></i> أداء الوكيل</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/performance/create.blade.php ENDPATH**/ ?>