

<?php $__env->startSection('title', 'إنشاء تقرير سوق جديد'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center">
                <a href="<?php echo e(route('reports.market.index')); ?>" class="text-white hover:text-purple-200 mr-4">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold mb-2">إنشاء تقرير سوق جديد</h1>
                    <p class="text-purple-100">تحليل شامل لاتجاهات السوق والمنافسة</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="<?php echo e(route('reports.market.store')); ?>">
                <?php echo csrf_field(); ?>
                
                <!-- Title -->
                <div class="mb-6">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان التقرير <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="مثال: تقرير سوق الرياض للربع الأول 2024"
                           value="<?php echo e(old('title')); ?>"
                           required>
                    <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Market Area -->
                <div class="mb-6">
                    <label for="market_area" class="block text-sm font-medium text-gray-700 mb-2">
                        منطقة السوق <span class="text-red-500">*</span>
                    </label>
                    <select id="market_area" 
                            name="market_area" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                        <option value="">اختر منطقة السوق</option>
                        <option value="الرياض" <?php echo e(old('market_area') == 'الرياض' ? 'selected' : ''); ?>>الرياض</option>
                        <option value="جدة" <?php echo e(old('market_area') == 'جدة' ? 'selected' : ''); ?>>جدة</option>
                        <option value="مكة المكرمة" <?php echo e(old('market_area') == 'مكة المكرمة' ? 'selected' : ''); ?>>مكة المكرمة</option>
                        <option value="المدينة المنورة" <?php echo e(old('market_area') == 'المدينة المنورة' ? 'selected' : ''); ?>>المدينة المنورة</option>
                        <option value="الدمام" <?php echo e(old('market_area') == 'الدمام' ? 'selected' : ''); ?>>الدمام</option>
                        <option value="الخبر" <?php echo e(old('market_area') == 'الخبر' ? 'selected' : ''); ?>>الخبر</option>
                        <option value="الطائف" <?php echo e(old('market_area') == 'الطائف' ? 'selected' : ''); ?>>الطائف</option>
                        <option value="تبوك" <?php echo e(old('market_area') == 'تبوك' ? 'selected' : ''); ?>>تبوك</option>
                        <option value="أبها" <?php echo e(old('market_area') == 'أبها' ? 'selected' : ''); ?>>أبها</option>
                        <option value="نجران" <?php echo e(old('market_area') == 'نجران' ? 'selected' : ''); ?>>نجران</option>
                    </select>
                    <?php $__errorArgs = ['market_area'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Period -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label for="period_start" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ البداية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="period_start" 
                               name="period_start" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               value="<?php echo e(old('period_start')); ?>"
                               required>
                        <?php $__errorArgs = ['period_start'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    
                    <div>
                        <label for="period_end" class="block text-sm font-medium text-gray-700 mb-2">
                            تاريخ النهاية <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               id="period_end" 
                               name="period_end" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               value="<?php echo e(old('period_end')); ?>"
                               required>
                        <?php $__errorArgs = ['period_end'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                <!-- Format -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تنسيق التقرير <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="format" value="pdf" class="ml-3" <?php echo e(old('format', 'pdf') == 'pdf' ? 'checked' : ''); ?> required>
                            <div>
                                <div class="font-medium">PDF</div>
                                <div class="text-sm text-gray-500">ملف PDF قابل للطباعة</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="format" value="excel" class="ml-3" <?php echo e(old('format') == 'excel' ? 'checked' : ''); ?>>
                            <div>
                                <div class="font-medium">Excel</div>
                                <div class="text-sm text-gray-500">ملف Excel مع جداول</div>
                            </div>
                        </label>
                        
                        <label class="flex items-center p-4 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="radio" name="format" value="csv" class="ml-3" <?php echo e(old('format') == 'csv' ? 'checked' : ''); ?>>
                            <div>
                                <div class="font-medium">CSV</div>
                                <div class="text-sm text-gray-500">ملف CSV للتحليل</div>
                            </div>
                        </label>
                    </div>
                    <?php $__errorArgs = ['format'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <!-- Filters -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        فلاتر إضافية (اختياري)
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="filters[property_type]" value="1" class="ml-3" <?php echo e(old('filters.property_type') ? 'checked' : ''); ?>>
                            <span>تحليل حسب نوع العقار</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="filters[price_range]" value="1" class="ml-3" <?php echo e(old('filters.price_range') ? 'checked' : ''); ?>>
                            <span>تحليل حسب نطاق السعر</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="filters[neighborhood]" value="1" class="ml-3" <?php echo e(old('filters.neighborhood') ? 'checked' : ''); ?>>
                            <span>تحليل حسب الأحياء</span>
                        </label>
                        
                        <label class="flex items-center">
                            <input type="checkbox" name="filters[competitor]" value="1" class="ml-3" <?php echo e(old('filters.competitor') ? 'checked' : ''); ?>>
                            <span>تحليل المنافسين</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-between">
                    <a href="<?php echo e(route('reports.market.index')); ?>" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        إلغاء
                    </a>
                    <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-chart-line ml-2"></i>
                        إنشاء التقرير
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Set minimum date to today
document.getElementById('period_start').min = new Date().toISOString().split('T')[0];
document.getElementById('period_end').min = new Date().toISOString().split('T')[0];

// Ensure end date is after start date
document.getElementById('period_start').addEventListener('change', function() {
    document.getElementById('period_end').min = this.value;
});

document.getElementById('period_end').addEventListener('change', function() {
    const startDate = document.getElementById('period_start').value;
    if (this.value < startDate) {
        this.value = startDate;
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/market/create.blade.php ENDPATH**/ ?>